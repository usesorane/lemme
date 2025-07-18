<?php

namespace Sorane\Lemme;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
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
     * Parse a markdown file and extract frontmatter and content
     */
    protected function parseMarkdownFile(string $filepath): ?array
    {
        try {
            $content = File::get($filepath);
            $document = YamlFrontMatter::parse($content);

            $relativePath = str_replace(base_path(config('lemme.docs_directory', 'docs')).'/', '', $filepath);
            $slug = $this->generateSlug($relativePath);

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
     * Generate a URL-friendly slug from file path
     */
    protected function generateSlug(string $path): string
    {
        $slug = str_replace(['/', '.md'], ['-', ''], $path);
        $slug = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);
        $slug = strtolower(trim($slug, '-'));

        return $slug === 'index' ? '' : $slug;
    }

    /**
     * Generate a title from file path
     */
    protected function generateTitleFromPath(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return ucwords(str_replace(['-', '_'], ' ', $filename));
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
            default => fn ($page) => $direction === 'asc' ? $page['relative_path'] : $page['relative_path'] * -1,
        };
    }

    /**
     * Get navigation structure
     */
    public function getNavigation(): Collection
    {
        return $this->getPages()->map(function ($page) {
            return [
                'title' => $page['title'],
                'slug' => $page['slug'],
                'url' => $this->getPageUrl($page['slug']),
            ];
        });
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
}
