<?php

namespace Sorane\Lemme\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Sorane\Lemme\Data\PageData;
use Sorane\Lemme\Events\MarkdownParseFailed;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class PageRepository
{
    public function __construct(
        protected SearchIndexBuilder $searchIndexBuilder = new SearchIndexBuilder,
    ) {}

    /**
     * @return Collection<int, PageData>
     */
    public function all(): Collection
    {
        $cacheKey = 'lemme.pages';

        if (config('lemme.cache.enabled') && Cache::has($cacheKey)) {
            /** @var Collection<int, PageData> */
            return Cache::get($cacheKey);
        }

        $docsPath = base_path(config('lemme.docs_directory', 'docs'));
        $realDocsPath = realpath($docsPath);
        $basePath = realpath(base_path());
        if (! $realDocsPath || ! $basePath || ! str_starts_with($realDocsPath, $basePath)) {
            return collect();
        }
        if (! File::exists($docsPath)) {
            return collect();
        }

        $pages = collect(File::allFiles($docsPath))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $this->parseMarkdownFile($file->getPathname()))
            ->filter();

        $callback = $this->getSortCallback();
        $descending = strtolower((string) config('lemme.navigation.sort_direction', 'asc')) === 'desc';
        $pages = $pages->sortBy($callback, SORT_REGULAR, $descending)->values();

        $duplicates = $pages->groupBy('slug')->filter(fn ($g) => $g->count() > 1);
        if ($duplicates->isNotEmpty()) {
            $example = $duplicates->first();
            $slug = $example->first()['slug'] ?? '';
            $files = $example->pluck('relative_path')->all();
            Log::error('Lemme: duplicate slug detected', ['slug' => $slug, 'files' => $files]);
            throw new \RuntimeException('Duplicate documentation slug "'.$slug.'" generated for multiple files: '.implode(', ', $files));
        }

        if (config('lemme.cache.enabled')) {
            Cache::put($cacheKey, $pages, config('lemme.cache.ttl', 3600));
            $this->searchIndexBuilder->buildAndCache($pages, fn ($slug) => app('lemme')->getPageUrl($slug));
        }

        return $pages;
    }

    public function findBySlug(string $slug): ?PageData
    {
        return $this->all()->first(fn ($page) => $page['slug'] === $slug);
    }

    public function clearCache(): void
    {
        Cache::forget('lemme.pages');
        Cache::forget('lemme.search_data');
    }

    protected function parseMarkdownFile(string $filepath): ?PageData
    {
        try {
            $content = File::get($filepath);
            $document = YamlFrontMatter::parse($content);
            $relativePath = str_replace(base_path(config('lemme.docs_directory', 'docs')).'/', '', $filepath);
            $slug = $document->matter('slug') ?? $this->generateSlugFromFilename($relativePath);
            $markdownContent = $document->body();
            $headings = $this->extractHeadings($markdownContent);

            return new PageData(
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
            Log::error('Lemme: failed to parse markdown file', ['file' => $filepath, 'error' => $e->getMessage()]);
            event(new MarkdownParseFailed($filepath, $e->getMessage()));

            return null;
        }
    }

    protected function generateSlugFromFilename(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $directory = trim(str_replace(basename($path), '', $path), '/');
        $cleaned = $this->removeNumberPrefix($filename);
        $cleaned = preg_replace('/([a-z])([A-Z])/', '$1 $2', $cleaned);
        $slug = Str::slug($cleaned, '-');
        if ($slug === 'index') {
            if ($directory === '') {
                return '';
            }
            $segments = explode('/', $directory);
            $last = end($segments);
            $last = $this->removeNumberPrefix($last);

            return Str::slug($last, '-');
        }

        return $slug;
    }

    protected function generateTitleFromPath(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $cleaned = $this->removeNumberPrefix($filename);

        return ucwords(str_replace(['-', '_'], ' ', $cleaned));
    }

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

    protected function getSortableFilename(string $path): string
    {
        $parts = explode('/', $path);
        $sortableParts = [];
        foreach ($parts as $part) {
            $sortableParts[] = $this->prefixSortableName($part);
        }

        return implode('/', $sortableParts);
    }

    protected function prefixSortableName(string $name): string
    {
        if (preg_match('/^(\d+)[-_](.+)/', $name, $matches)) {
            $number = str_pad($matches[1], 5, '0', STR_PAD_LEFT);
            $clean = $matches[2];

            return $number.'_'.$clean;
        }

        return '99999_'.$name;
    }

    protected function removeNumberPrefix(string $name): string
    {
        return (string) preg_replace('/^\d+[-_]/', '', $name);
    }

    protected function extractHeadings(string $markdownContent): array
    {
        $headings = [];
        $lines = explode("\n", $markdownContent);
        foreach ($lines as $line) {
            if (preg_match('/^(#{1,6})\s+(.+)$/', trim($line), $matches)) {
                $level = strlen($matches[1]);
                $text = trim($matches[2]);
                $id = $this->generateHeadingId($text);
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

    protected function generateHeadingId(string $text): string
    {
        $text = preg_replace('/[*_`]/', '', $text);
        $id = strtolower($text);
        $id = preg_replace('/[^a-z0-9\s-]/', '', $id);
        $id = preg_replace('/[\s-]+/', '-', $id);
        $id = trim($id, '-');

        return $id ?: 'heading';
    }

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
}
