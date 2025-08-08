<?php

namespace Sorane\Lemme;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Lemme
{
    /**
     * Get all documentation pages
     */
    public function getPages(): Collection
    {
        $cacheKey = 'lemme.pages';

        if (config('lemme.cache.enabled') && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $docsPath = base_path(config('lemme.docs_directory', 'docs'));

        if (! File::exists($docsPath)) {
            return collect();
        }

        $pages = collect(File::allFiles($docsPath))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $this->parseMarkdownFile($file->getPathname()))
            ->filter()
            ->sortBy($this->getSortCallback())
            ->values();

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
    public function getPage(string $slug): ?array
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

        $cacheKey = "lemme.html.{$slug}.".md5($page['modified_at']);

        if (config('lemme.cache.enabled') && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $html = app(\Spatie\LaravelMarkdown\MarkdownRenderer::class)
            ->highlightTheme(['light' => 'github-light', 'dark' => 'github-dark'])
            ->toHtml($page['raw_content']);

        if (config('lemme.cache.enabled')) {
            Cache::put($cacheKey, $html, config('lemme.cache.ttl', 3600));
        }

        return $html;
    }

    /**
     * Parse a markdown file and extract frontmatter and content
     */
    protected function parseMarkdownFile(string $filepath): ?array
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
            $markdownWithIds = $this->injectHeadingIds($markdownContent, $headings);

            return [
                'title' => $document->matter('title') ?? $this->generateTitleFromPath($relativePath),
                'slug' => $slug,
                'raw_content' => $markdownWithIds,
                'headings' => $headings,
                'frontmatter' => $document->matter(),
                'filepath' => $filepath,
                'relative_path' => $relativePath,
                'modified_at' => File::lastModified($filepath),
                'created_at' => File::lastModified($filepath), // Simplified for now
            ];
        } catch (\Exception $e) {
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
        $direction = config('lemme.navigation.sort_direction', 'asc');

        return match ($sortBy) {
            'title' => fn ($page) => $direction === 'asc' ? $page['title'] : $page['title'] * -1,
            'created_at' => fn ($page) => $direction === 'asc' ? $page['created_at'] : $page['created_at'] * -1,
            'modified_at' => fn ($page) => $direction === 'asc' ? $page['modified_at'] : $page['modified_at'] * -1,
            default => fn ($page) => $this->getSortableFilename($page['relative_path'], $direction),
        };
    }

    /**
     * Get sortable filename considering number prefixes
     */
    protected function getSortableFilename(string $path, string $direction): string
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

        $sortableString = implode('/', $sortableParts);

        return $direction === 'asc' ? $sortableString : str_replace('/', chr(255), $sortableString);
    }

    /**
     * Get navigation structure
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
        Cache::forget('lemme.pages');

        // Clear all HTML cache entries
        $pages = $this->getPages();
        foreach ($pages as $page) {
            $cacheKey = "lemme.html.{$page['slug']}.".md5($page['modified_at']);
            Cache::forget($cacheKey);
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
    protected function injectHeadingIds(string $markdownContent, array $headings): string
    {
        $lines = explode("\n", $markdownContent);
        $headingIndex = 0;

        foreach ($lines as $index => $line) {
            // Match markdown headers (# ## ### etc.)
            if (preg_match('/^(#{1,6})\s+(.+)$/', trim($line), $matches)) {
                if ($headingIndex < count($headings)) {
                    $heading = $headings[$headingIndex];
                    // Add the ID to the heading line by converting it to HTML with an id attribute
                    $level = strlen($matches[1]);
                    $text = trim($matches[2]);
                    $lines[$index] = "<h{$level} id=\"{$heading['id']}\">{$text}</h{$level}>";
                    $headingIndex++;
                }
            }
        }

        return implode("\n", $lines);
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
        // Make content length configurable - default to no limit for comprehensive search
        $maxLength = config('lemme.search.max_content_length', 0);

        // Remove HTML tags that were injected for headings
        $content = preg_replace('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/', '$1', $content);

        // Remove markdown formatting
        $patterns = [
            '/^#{1,6}\s+/m',           // Headers
            '/\*\*(.*?)\*\*/',         // Bold
            '/\*(.*?)\*/',             // Italic
            '/`(.*?)`/',               // Inline code
            '/```[\s\S]*?```/',        // Code blocks
            '/\[(.*?)\]\([^)]*\)/',    // Links
            '/!\[.*?\]\([^)]*\)/',     // Images
        ];

        $content = preg_replace($patterns, ['$1', '$1', '$1', '$1', '', '$1', ''], $content);

        // Clean up whitespace and truncate
        $content = preg_replace('/\s+/', ' ', trim($content));

        return $maxLength > 0 && strlen($content) > $maxLength
            ? substr($content, 0, $maxLength).'...'
            : $content;
    }
}
