<?php

namespace Sorane\Lemme\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class SearchComponent extends Component
{
    public $search = '';

    public $results = [];

    public $searchInitialized = false;

    // Dummy data for testing - in a real implementation,
    // this would come from your docs/markdown files
    private $dummyData = [
        [
            'title' => 'Getting Started',
            'category' => 'Guides',
            'url' => '/docs/getting-started',
            'content' => 'Learn how to get started with our documentation system. This comprehensive guide will walk you through the initial setup process.',
        ],
        [
            'title' => 'Installation',
            'category' => 'Guides',
            'url' => '/docs/installation',
            'content' => 'Step-by-step installation guide for setting up the system. Covers requirements, dependencies, and configuration.',
        ],
        [
            'title' => 'Authentication',
            'category' => 'Security',
            'url' => '/docs/authentication',
            'content' => 'How to implement authentication in your application. Covers OAuth, JWT tokens, and session management.',
        ],
        [
            'title' => 'API Reference',
            'category' => 'Reference',
            'url' => '/docs/api',
            'content' => 'Complete API reference documentation. Includes endpoints, parameters, responses, and examples.',
        ],
        [
            'title' => 'Configuration',
            'category' => 'Guides',
            'url' => '/docs/configuration',
            'content' => 'Configure your application settings and options. Environment variables, config files, and advanced settings.',
        ],
        [
            'title' => 'Troubleshooting',
            'category' => 'Support',
            'url' => '/docs/troubleshooting',
            'content' => 'Common issues and how to resolve them. Error messages, debugging tips, and frequently asked questions.',
        ],
        [
            'title' => 'Advanced Features',
            'category' => 'Guides',
            'url' => '/docs/advanced',
            'content' => 'Advanced functionality and customization options. Custom hooks, plugins, and extending the system.',
        ],
        [
            'title' => 'Deployment',
            'category' => 'Guides',
            'url' => '/docs/deployment',
            'content' => 'Deploy your application to production. Server setup, environment configuration, and best practices.',
        ],
    ];

    #[On('init-search-data')]
    public function initSearchData()
    {
        $this->searchInitialized = true;
        $this->dispatch('search-data-ready', data: $this->dummyData);
    }

    public function updatedSearch()
    {
        if (empty(trim($this->search))) {
            $this->results = [];

            return;
        }

        // Dispatch search event to JavaScript
        $this->dispatch('perform-search', query: trim($this->search));
    }

    #[On('search-results')]
    public function handleSearchResults($results)
    {
        $this->results = $results;
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
