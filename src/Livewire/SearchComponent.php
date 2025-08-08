<?php

namespace Sorane\Lemme\Livewire;

use Livewire\Component;
use Sorane\Lemme\Facades\Lemme;

class SearchComponent extends Component
{
    public $search = '';

    public $results = [];

    protected $listeners = [
        'init-search-data' => 'initSearchData',
        'search-results' => 'handleSearchResults',
    ];

    public function mount()
    {
        $this->initSearchData();
    }

    public function initSearchData()
    {
        $searchData = Lemme::getSearchData();

        $this->dispatch('search-data-ready', data: $searchData);
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

    public function handleSearchResults($results)
    {
        $this->results = $results;
    }

    public function render()
    {
        return view('lemme::livewire.search-component');
    }
}
