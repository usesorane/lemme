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

class LemmeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge package config
        $this->mergeConfigFrom(__DIR__.'/../config/lemme.php', 'lemme');

        // Bind the Lemme class to the container and provide an alias 'lemme'
        $this->app->singleton(\Sorane\Lemme\Lemme::class, function () {
            return new Lemme;
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
