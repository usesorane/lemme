<?php

namespace Sorane\Lemme\Livewire;

use Livewire\Component;
use Sorane\Lemme\Facades\Lemme;

class SearchComponent extends Component
{
    public string $search = '';

    /** @var array<int, mixed> */
    public array $results = [];

    protected $listeners = [
        'init-search-data' => 'initSearchData',
        'search-results' => 'handleSearchResults',
    ];

    public function mount(): void
    {
        $this->initSearchData();
    }

    public function initSearchData(): void
    {
        $searchData = Lemme::getSearchData();

        $this->dispatch('search-data-ready', data: $searchData);
    }

    public function updatedSearch(): void
    {
        if (empty(trim($this->search))) {
            $this->results = [];

            return;
        }

        // Dispatch search event to JavaScript
        $this->dispatch('perform-search', query: trim($this->search));
    }

    public function handleSearchResults($results): void
    {
        $this->results = $results;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('lemme::livewire.search-component');
    }
}
