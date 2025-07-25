<?php

namespace Sorane\Lemme\Livewire;

use Livewire\Component;

class SearchComponent extends Component
{
    public $search = '';

    public $results = [];

    // Dummy data for testing
    private $dummyData = [
        [
            'title' => 'Getting Started',
            'category' => 'Guides',
            'url' => '/docs/getting-started',
            'content' => 'Learn how to get started with our documentation system.',
        ],
        [
            'title' => 'Installation',
            'category' => 'Guides',
            'url' => '/docs/installation',
            'content' => 'Step-by-step installation guide for setting up the system.',
        ],
        [
            'title' => 'Authentication',
            'category' => 'Security',
            'url' => '/docs/authentication',
            'content' => 'How to implement authentication in your application.',
        ],
        [
            'title' => 'API Reference',
            'category' => 'Reference',
            'url' => '/docs/api',
            'content' => 'Complete API reference documentation.',
        ],
        [
            'title' => 'Configuration',
            'category' => 'Guides',
            'url' => '/docs/configuration',
            'content' => 'Configure your application settings and options.',
        ],
        [
            'title' => 'Troubleshooting',
            'category' => 'Support',
            'url' => '/docs/troubleshooting',
            'content' => 'Common issues and how to resolve them.',
        ],
    ];

    public function updatedSearch()
    {
        if (empty(trim($this->search))) {
            $this->results = [];

            return;
        }

        $searchTerm = strtolower(trim($this->search));

        $this->results = collect($this->dummyData)
            ->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item['title']), $searchTerm) ||
                       str_contains(strtolower($item['category']), $searchTerm) ||
                       str_contains(strtolower($item['content']), $searchTerm);
            })
            ->take(5)
            ->values()
            ->toArray();
    }

    public function highlightSearchTerm($text, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $text;
        }

        $highlighted = preg_replace(
            '/('.preg_quote($searchTerm, '/').')/i',
            '<mark class="underline bg-transparent text-emerald-500">$1</mark>',
            $text
        );

        return $highlighted;
    }

    public function render()
    {
        return view('lemme::livewire.search-component');
    }
}
