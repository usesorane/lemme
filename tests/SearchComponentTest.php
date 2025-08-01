<?php

namespace Sorane\Lemme\Tests;

use Livewire\Livewire;
use Orchestra\Testbench\TestCase;
use Sorane\Lemme\Facades\Lemme;
use Sorane\Lemme\LemmeServiceProvider;
use Sorane\Lemme\Livewire\SearchComponent;

class SearchComponentTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            LemmeServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Set up encryption key for testing
        $this->app['config']->set('app.key', 'base64:'.base64_encode('a'.str_repeat('b', 31)));
    }

    protected function getPackageAliases($app)
    {
        return [
            'Lemme' => Lemme::class,
        ];
    }

    /** @test */
    public function it_can_render_search_component()
    {
        Livewire::test(SearchComponent::class)
            ->assertStatus(200)
            ->assertViewIs('lemme::livewire.search-component');
    }

    /** @test */
    public function it_initializes_with_empty_search_and_results()
    {
        Livewire::test(SearchComponent::class)
            ->assertSet('search', '')
            ->assertSet('results', [])
            ->assertSet('searchInitialized', false);
    }

    /** @test */
    public function it_can_initialize_search_data()
    {
        Livewire::test(SearchComponent::class)
            ->call('initSearchData')
            ->assertSet('searchInitialized', true)
            ->assertDispatched('search-data-ready');
    }

    /** @test */
    public function it_dispatches_search_event_when_search_updates()
    {
        Livewire::test(SearchComponent::class)
            ->set('search', 'installation')
            ->assertDispatched('perform-search', query: 'installation');
    }

    /** @test */
    public function it_handles_search_results()
    {
        $mockResults = [
            [
                'title' => 'Installation Guide',
                'category' => 'Guides',
                'url' => '/docs/installation',
                'content' => 'How to install the system',
                'score' => 0.1,
            ],
        ];

        Livewire::test(SearchComponent::class)
            ->call('handleSearchResults', $mockResults)
            ->assertSet('results', $mockResults);
    }

    /** @test */
    public function it_clears_results_when_search_is_empty()
    {
        Livewire::test(SearchComponent::class)
            ->set('search', 'test')
            ->set('search', '')
            ->assertSet('results', []);
    }

    /** @test */
    public function it_highlights_search_terms_correctly()
    {
        $component = new SearchComponent;

        $highlighted = $component->highlightSearchTerm('Installation Guide', 'install');

        $this->assertStringContainsString('<mark class="underline bg-transparent text-emerald-500">install</mark>', strtolower($highlighted));
    }

    /** @test */
    public function it_returns_original_text_when_no_search_term()
    {
        $component = new SearchComponent;

        $result = $component->highlightSearchTerm('Installation Guide', '');

        $this->assertEquals('Installation Guide', $result);
    }
}
