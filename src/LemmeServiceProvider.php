<?php

namespace Sorane\Lemme;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Sorane\Lemme\Commands\LemmeClearCommand;
use Sorane\Lemme\Commands\LemmeInstallCommand;
use Sorane\Lemme\Commands\LemmePublishCommand;

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
        // Load routes (only when not cached)
        if (! $this->app->routesAreCached()) {
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

            // Publishing compiled assets (CSS/JS)
            $this->publishes([
                __DIR__.'/../resources/dist' => public_path('vendor/lemme'),
            ], 'lemme-assets');

            // Register artisan commands
            $this->commands([
                LemmeInstallCommand::class,
                LemmeClearCommand::class,
                LemmePublishCommand::class,
            ]);
        }
    }
}
