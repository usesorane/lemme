<?php

namespace Sorane\Lemme;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Sorane\Lemme\Commands\LemmeClearCommand;
use Sorane\Lemme\Commands\LemmeInstallCommand;
use Sorane\Lemme\Commands\LemmePublishCommand;
use Sorane\Lemme\Commands\LemmeReindexCommand;
use Sorane\Lemme\Support\ContentRenderer;
use Sorane\Lemme\Support\NavigationBuilder;
use Sorane\Lemme\Support\PageRepository;
use Sorane\Lemme\Support\SearchIndexBuilder;

class LemmeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge package config
        $this->mergeConfigFrom(__DIR__.'/../config/lemme.php', 'lemme');

        // Core support singletons for easier customization / swapping
        $this->app->singleton(PageRepository::class, function ($app) {
            return new PageRepository($app->make(SearchIndexBuilder::class));
        });
        $this->app->singleton(ContentRenderer::class, fn () => new ContentRenderer);
        $this->app->singleton(NavigationBuilder::class, fn () => new NavigationBuilder);
        $this->app->singleton(SearchIndexBuilder::class, fn () => new SearchIndexBuilder);

        // Bind the Lemme facade/root object
        $this->app->singleton(\Sorane\Lemme\Lemme::class, function ($app) {
            return new Lemme(
                $app->make(PageRepository::class),
                $app->make(NavigationBuilder::class),
                $app->make(SearchIndexBuilder::class),
                $app->make(ContentRenderer::class),
            );
        });

        // Backwards compatible alias so resolve('lemme') still works
        $this->app->alias(\Sorane\Lemme\Lemme::class, 'lemme');
    }

    public function boot(): void
    {
        if (config('lemme.subdomain') && config('lemme.route_prefix')) {
            Log::notice('Lemme: both subdomain and route_prefix configured; route_prefix will take precedence.');
        }
        // Load routes (only when not cached)
        $router = $this->app['router'];
        $routesCached = method_exists($router, 'routesAreCached') ? $router->routesAreCached() : false;
        if (! $routesCached) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lemme');

        // Register Blade Components namespace
        Blade::componentNamespace('Sorane\\Lemme\\Views\\Components', 'lemme');

        // Register Livewire component
        if (class_exists(Livewire::class)) {
            Livewire::component('lemme.search-component', \Sorane\Lemme\Livewire\SearchComponent::class);
        }

        // Console-specific booting
        if ($this->app->runningInConsole()) {
            // Publishing config
            $this->publishes([
                __DIR__.'/../config/lemme.php' => config_path('lemme.php'),
            ], 'lemme-config');

            // Publishing views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/lemme'),
            ], 'lemme-views');

            // Publishing compiled assets (CSS/JS) only if build directory exists
            if (is_dir(__DIR__.'/../resources/dist')) {
                $this->publishes([
                    __DIR__.'/../resources/dist' => public_path('vendor/lemme'),
                ], 'lemme-assets');
            }

            // Register artisan commands
            $this->commands([
                LemmeInstallCommand::class,
                LemmeClearCommand::class,
                LemmePublishCommand::class,
                LemmeReindexCommand::class,
            ]);
        }
    }
}
