<?php

namespace Sorane\Lemme;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Lemme
{
    /**
     * Return base scheme from app configuration, with sensible fallback.
     */
    public static function baseScheme(): string
    {
        $scheme = (string) parse_url((string) config('app.url'), PHP_URL_SCHEME);
        if (! $scheme) {
            $fallback = (string) url('/');
            $scheme = (string) parse_url($fallback, PHP_URL_SCHEME);
        }

        return $scheme ?: 'http';
    }

    /**
     * Return base host from app configuration, with sensible fallback.
     */
    public static function baseHost(): string
    {
        $host = (string) parse_url((string) config('app.url'), PHP_URL_HOST);
        if (! $host) {
            $fallback = (string) url('/');
            $host = (string) parse_url($fallback, PHP_URL_HOST);
        }

        return $host ?: 'localhost';
    }

    /**
     * Get all documentation pages
     */
    /**
     * Get all documentation pages.
     *
     * @return Collection<int, \Sorane\Lemme\Data\PageData>
     */
    public function getPages(): Collection
    {
        $cacheKey = 'lemme.pages';

        if (config('lemme.cache.enabled') && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $docsPath = base_path(config('lemme.docs_directory', 'docs'));
        $realDocsPath = realpath($docsPath);
        $basePath = realpath(base_path());

        if (! $realDocsPath || ! $basePath || ! str_starts_with($realDocsPath, $basePath)) {
            // Security: prevent traversing outside project root
            return collect();
        }

        if (! File::exists($docsPath)) {
            return collect();
        }

        $pages = collect(File::allFiles($docsPath))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $this->parseMarkdownFile($file->getPathname()))
            ->filter();

        // Sorting with proper descending handling for string fields
        $callback = $this->getSortCallback();
        $descending = strtolower((string) config('lemme.navigation.sort_direction', 'asc')) === 'desc';
        $pages = $pages->sortBy($callback, SORT_REGULAR, $descending)->values();

        // Detect duplicate flat slugs and fail fast with guidance
        $duplicates = $pages->groupBy('slug')->filter(fn ($g) => $g->count() > 1);
        if ($duplicates->isNotEmpty()) {
            $example = $duplicates->first();
            $sl = $example->first()['slug'] ?? '';
            $files = $example->pluck('relative_path')->all();
            Log::error('Lemme: duplicate slug detected', [
                'slug' => $sl,
                'files' => $files,
            ]);
            throw new \RuntimeException(
                'Duplicate documentation slug "'.$sl.'" generated for multiple files: '.implode(', ', $files).'. '
                .'Keep flat slugs unique. Provide `slug:` in frontmatter to disambiguate, or rename files.'
            );
        }

        if (config('lemme.cache.enabled')) {
            Cache::put($cacheKey, $pages, config('lemme.cache.ttl', 3600));

            // Build and cache search data at the same time
            $this->buildAndCacheSearchData($pages);
        }

        return $pages;
    }

    /**
     * Get a specific page by slug
     */
    public function getPage(string $slug): ?\Sorane\Lemme\Data\PageData
    {
        return $this->getPages()->first(fn ($page) => $page['slug'] === $slug);
    }

    /**
     * Get rendered HTML for a specific page
     */
    public function getPageHtml(string $slug): ?string
    {
        $page = $this->getPage($slug);

        if (! $page) {
            return null;
        }

        $cacheKey = "lemme.html.{$slug}.{$page['modified_at']}";
        $pointerKey = "lemme.html.current.{$slug}";

        if (config('lemme.cache.enabled') && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $html = app(\Spatie\LaravelMarkdown\MarkdownRenderer::class)
            ->highlightTheme(['light' => 'github-light', 'dark' => 'github-dark'])
            ->toHtml($page['raw_content']);

        // Inject heading IDs after render (safer than mutating markdown pre-render)
        $html = $this->injectHeadingIdsIntoHtml($html);

        if (config('lemme.cache.enabled')) {
            // Proactively clear the previously used key for this slug
            $previousKey = Cache::get($pointerKey);
            if ($previousKey && $previousKey !== $cacheKey) {
                Cache::forget($previousKey);
            }
            Cache::put($cacheKey, $html, config('lemme.cache.ttl', 3600));
            Cache::put($pointerKey, $cacheKey, config('lemme.cache.ttl', 3600));
        }

        return $html;
    }

    /**
     * Parse a markdown file and extract frontmatter and content
     */
    protected function parseMarkdownFile(string $filepath): ?\Sorane\Lemme\Data\PageData
    {
        try {
            $content = File::get($filepath);
            $document = YamlFrontMatter::parse($content);

            $relativePath = str_replace(base_path(config('lemme.docs_directory', 'docs')).'/', '', $filepath);

            // Check if slug is provided in frontmatter, otherwise generate it from filename
            $slug = $document->matter('slug') ?? $this->generateSlugFromFilename($relativePath);

            $markdownContent = $document->body();
            $headings = $this->extractHeadings($markdownContent);

            // Inject IDs into the markdown content for the headings
            return new \Sorane\Lemme\Data\PageData(
                title: $document->matter('title') ?? $this->generateTitleFromPath($relativePath),
                slug: $slug,
                raw_content: $markdownContent,
                headings: $headings,
                frontmatter: $document->matter(),
                filepath: $filepath,
                relative_path: $relativePath,
                modified_at: File::lastModified($filepath),
                created_at: File::lastModified($filepath),
            );
        } catch (\Exception $e) {
            Log::error('Lemme: failed to parse markdown file', [
                'file' => $filepath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Generate a URL-friendly slug from filename only (not full path)
     */
    protected function generateSlugFromFilename(string $path): string
    {
        // Get just the filename without extension
        $filename = pathinfo($path, PATHINFO_FILENAME);

        // Remove number prefix (e.g., "1_", "2-", "10_")
        $cleaned = $this->removeNumberPrefix($filename);

        // Pre-process camelCase to add spaces before capital letters
        $cleaned = preg_replace('/([a-z])([A-Z])/', '$1 $2', $cleaned);

        // Use Laravel's Str::slug() to generate a proper slug
        $slug = Str::slug($cleaned, '-');

        return $slug === 'index' ? '' : $slug;
    }

    /**
     * Generate a title from file path
     */
    protected function generateTitleFromPath(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        // Remove number prefix from filename
        $cleaned = $this->removeNumberPrefix($filename);

        return ucwords(str_replace(['-', '_'], ' ', $cleaned));
    }

    /**
     * Get sort callback based on configuration
     */
    protected function getSortCallback(): callable
    {
        $sortBy = config('lemme.navigation.sort_by', 'filename');

        return match ($sortBy) {
            'title' => fn ($page) => $page['title'],
            'created_at' => fn ($page) => $page['created_at'],
            'modified_at' => fn ($page) => $page['modified_at'],
            default => fn ($page) => $this->getSortableFilename($page['relative_path']),
        };
    }

    /**
     * Get sortable filename considering number prefixes
     */
    protected function getSortableFilename(string $path): string
    {
        $parts = explode('/', $path);
        $sortableParts = [];

        foreach ($parts as $part) {
            // Extract number prefix for proper numeric sorting
            if (preg_match('/^(\d+)[-_](.+)/', $part, $matches)) {
                $number = str_pad($matches[1], 5, '0', STR_PAD_LEFT); // Pad for proper sorting
                $name = $matches[2];
                $sortableParts[] = $number.'_'.$name;
            } else {
                // No number prefix, add high number to sort after numbered items
                $sortableParts[] = '99999_'.$part;
            }
        }

        return implode('/', $sortableParts);
    }

    /**
     * Get navigation structure
     */
    /**
     * @return Collection<int, mixed>
     */
    public function getNavigation(): Collection
    {
        $pages = $this->getPages();

        // Check if grouping is enabled
        if (config('lemme.navigation.grouping.enabled', true)) {
            $groupedPages = $this->groupPagesByDirectory($pages);

            return $this->buildNavigationTree($groupedPages);
        }

        // Fallback to flat navigation if grouping is disabled
        return $pages->map(function ($page) {
            return [
                'type' => 'page',
                'title' => $page['title'],
                'slug' => $page['slug'],
                'url' => $this->getPageUrl($page['slug']),
            ];
        });
    }

    /**
     * Group pages by their directory structure
     */
    /**
     * @param  Collection<int, \Sorane\Lemme\Data\PageData>  $pages
     * @return array<string, mixed>
     */
    protected function groupPagesByDirectory(Collection $pages): array
    {
        $grouped = [];

        foreach ($pages as $page) {
            $pathParts = explode('/', $page['relative_path']);
            $filename = array_pop($pathParts); // Remove filename

            // If no directory (root level), add to ungrouped
            if (empty($pathParts)) {
                $grouped['_root'][] = $page;
            } else {
                // Create nested array structure based on directory path
                $current = &$grouped;
                foreach ($pathParts as $dir) {
                    if (! isset($current[$dir])) {
                        $current[$dir] = [];
                    }
                    $current = &$current[$dir];
                }

                // Add page to the deepest directory level
                if (! isset($current['_pages'])) {
                    $current['_pages'] = [];
                }
                $current['_pages'][] = $page;
            }
        }

        return $grouped;
    }

    /**
     * Build navigation tree from grouped pages
     */
    protected function buildNavigationTree(array $grouped): Collection
    {
        $navigation = collect();

        // Add root level pages first (ungrouped)
        if (isset($grouped['_root'])) {
            foreach ($grouped['_root'] as $page) {
                $navigation->push([
                    'type' => 'page',
                    'title' => $page['title'],
                    'slug' => $page['slug'],
                    'url' => $this->getPageUrl($page['slug']),
                ]);
            }
            unset($grouped['_root']);
        }

        // Sort groups if needed
        $sortBy = config('lemme.navigation.grouping.sort_groups_by', 'directory_name');
        $sortDirection = config('lemme.navigation.grouping.sort_groups_direction', 'asc');

        $sortedGroups = collect($grouped);
        if ($sortBy === 'directory_name') {
            // Custom sorting that respects number prefixes
            $sortedGroups = $sortedGroups->sortBy(function ($value, $key) {
                return $this->getSortableDirectoryName($key);
            }, SORT_REGULAR, $sortDirection === 'desc');
        }

        // Add grouped pages
        foreach ($sortedGroups as $groupName => $groupData) {
            $navigation->push($this->buildNavigationGroup($groupName, $groupData));
        }

        return $navigation;
    }

    /**
     * Build a navigation group (with potential nesting)
     */
    protected function buildNavigationGroup(string $groupName, array $groupData): array
    {
        $group = [
            'type' => 'group',
            'title' => $this->formatGroupTitle($groupName),
            'slug' => $groupName,
            'children' => collect(),
        ];

        // Add pages in this group
        if (isset($groupData['_pages'])) {
            // Sort pages by their filename (respecting number prefixes)
            $sortedPages = collect($groupData['_pages'])->sortBy(function ($page) {
                $filename = basename($page['relative_path']);

                return $this->getSortableDirectoryName($filename);
            });

            foreach ($sortedPages as $page) {
                $group['children']->push([
                    'type' => 'page',
                    'title' => $page['title'],
                    'slug' => $page['slug'],
                    'url' => $this->getPageUrl($page['slug']),
                ]);
            }
            unset($groupData['_pages']);
        }

        // Sort nested groups and add them
        $sortedNestedGroups = collect($groupData)->sortBy(function ($value, $key) {
            return $this->getSortableDirectoryName($key);
        });

        foreach ($sortedNestedGroups as $nestedGroupName => $nestedGroupData) {
            $group['children']->push($this->buildNavigationGroup($nestedGroupName, $nestedGroupData));
        }

        return $group;
    }

    /**
     * Format group title from directory name
     */
    protected function formatGroupTitle(string $dirName): string
    {
        // Remove number prefix (e.g., "1_", "01-", "2-")
        $cleaned = $this->removeNumberPrefix($dirName);

        // Convert kebab-case or snake_case to Title Case
        $formatted = str_replace(['-', '_'], ' ', $cleaned);

        return ucwords(strtolower($formatted));
    }

    /**
     * Get sortable directory name considering number prefixes
     */
    protected function getSortableDirectoryName(string $dirName): string
    {
        // Extract number prefix for proper numeric sorting
        if (preg_match('/^(\d+)[-_](.+)/', $dirName, $matches)) {
            $number = str_pad($matches[1], 5, '0', STR_PAD_LEFT); // Pad for proper sorting
            $name = $matches[2];

            return $number.'_'.$name;
        }

        // No number prefix, add high number to sort after numbered items
        return '99999_'.$dirName;
    }

    /**
     * Remove number prefix from filename or directory name
     */
    protected function removeNumberPrefix(string $name): string
    {
        // Remove patterns like: "1_", "01-", "2-", "10_", etc.
        return preg_replace('/^\d+[-_]/', '', $name);
    }

    /**
     * Get URL for a page
     */
    public function getPageUrl(string $slug): string
    {
        $prefix = config('lemme.route_prefix');

        if ($prefix) {
            return url($prefix.'/'.$slug);
        }

        return url($slug);
    }

    /**
     * Clear the cache
     */
    public function clearCache(): void
    {
        $pages = Cache::get('lemme.pages');
        Cache::forget('lemme.pages');
        if ($pages instanceof Collection) {
            foreach ($pages as $page) {
                $cacheKey = "lemme.html.{$page['slug']}.{$page['modified_at']}";
                $pointerKey = "lemme.html.current.{$page['slug']}";
                Cache::forget($cacheKey);
                Cache::forget($pointerKey);
            }
        }

        // Clear search cache
        Cache::forget('lemme.search_data');
    }

    /**
     * Extract headings from markdown content and generate table of contents
     */
    protected function extractHeadings(string $markdownContent): array
    {
        $headings = [];
        $lines = explode("\n", $markdownContent);

        foreach ($lines as $line) {
            // Match markdown headers (# ## ### etc.)
            if (preg_match('/^(#{1,6})\s+(.+)$/', trim($line), $matches)) {
                $level = strlen($matches[1]); // Number of # characters
                $text = trim($matches[2]);

                // Generate a URL-friendly ID from the heading text
                $id = $this->generateHeadingId($text);

                // Determine CSS class based on heading level
                $class = $this->getHeadingClass($level);

                $headings[] = [
                    'id' => $id,
                    'text' => $text,
                    'level' => $level,
                    'class' => $class,
                ];
            }
        }

        return $headings;
    }

    /**
     * Generate a URL-friendly ID from heading text
     */
    protected function generateHeadingId(string $text): string
    {
        // Remove markdown formatting
        $text = preg_replace('/[*_`]/', '', $text);

        // Convert to lowercase and replace spaces/special chars with hyphens
        $id = strtolower($text);
        $id = preg_replace('/[^a-z0-9\s-]/', '', $id);
        $id = preg_replace('/[\s-]+/', '-', $id);
        $id = trim($id, '-');

        return $id ?: 'heading';
    }

    /**
     * Get CSS class for heading level
     */
    protected function getHeadingClass(int $level): string
    {
        return match ($level) {
            1, 2 => 'font-medium',
            3 => 'pl-2',
            4 => 'pl-4',
            5 => 'pl-6',
            6 => 'pl-8',
            default => '',
        };
    }

    /**
     * Inject heading IDs into markdown content
     */
    protected function injectHeadingIdsIntoHtml(string $html): string
    {
        return (string) preg_replace_callback('/<h([1-6])(?![^>]*id=)([^>]*)>(.*?)<\/h\\1>/i', function ($matches) {
            $level = $matches[1];
            $attrs = $matches[2];
            $inner = $matches[3];
            $plain = strip_tags($inner);
            $id = $this->generateHeadingId($plain);

            return "<h{$level}{$attrs} id=\"{$id}\">{$inner}</h{$level}>";
        }, $html);
    }

    /**
     * Build and cache search data from pages
     */
    protected function buildAndCacheSearchData(Collection $pages): void
    {
        $searchData = $this->buildSearchDataFromPages($pages);
        Cache::put('lemme.search_data', $searchData, config('lemme.cache.ttl', 3600));
    }

    /**
     * Get search data (cached or generate from pages)
     */
    public function getSearchData(): array
    {
        if (config('lemme.cache.enabled') && Cache::has('lemme.search_data')) {
            return Cache::get('lemme.search_data');
        }

        // If search data is not cached but pages are, build from cached pages
        $pages = $this->getPages();

        return $this->buildSearchDataFromPages($pages);
    }

    /**
     * Build search data array from pages collection
     */
    protected function buildSearchDataFromPages(Collection $pages): array
    {
        return $pages->map(function ($page) {
            return [
                'title' => $page['title'],
                'category' => $this->getCategoryFromPath($page['relative_path']),
                'url' => $this->getPageUrl($page['slug']),
                'content' => $this->getSearchableContent($page['raw_content']),
                'slug' => $page['slug'],
            ];
        })->toArray();
    }

    /**
     * Extract category from file path (directory name)
     */
    protected function getCategoryFromPath(string $path): string
    {
        $pathParts = explode('/', $path);

        if (count($pathParts) > 1) {
            // Get the first directory name
            $directory = $pathParts[0];

            // Remove number prefix and format as title
            $cleaned = $this->removeNumberPrefix($directory);
            $formatted = str_replace(['-', '_'], ' ', $cleaned);

            return ucwords(strtolower($formatted));
        }

        return 'General';
    }

    /**
     * Get searchable content from markdown, optimized for search
     */
    protected function getSearchableContent(string $content): string
    {
        $maxLength = (int) config('lemme.search.max_content_length', 0);
        $html = app(\Spatie\LaravelMarkdown\MarkdownRenderer::class)->toHtml($content);
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');
        if ($maxLength > 0 && strlen($text) > $maxLength) {
            return substr($text, 0, $maxLength).'...';
        }

        return $text;
    }
}
