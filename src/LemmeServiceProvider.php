<?php

namespace Sorane\Lemme;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sorane\Lemme\Commands\LemmeCommand;
use Sorane\Lemme\Commands\LemmeClearCommand;

class LemmeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('lemme')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_lemme_table')
            ->hasCommand(LemmeCommand::class)
            ->hasCommand(LemmeClearCommand::class);
    }

    public function packageBooted()
    {
        // Bind the Lemme class to the container
        $this->app->singleton('lemme', function () {
            return new Lemme();
        });
    }
}
