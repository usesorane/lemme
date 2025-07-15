<?php

namespace Sorane\Lemme;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sorane\Lemme\Commands\LemmeCommand;

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
            ->hasMigration('create_lemme_table')
            ->hasCommand(LemmeCommand::class);
    }
}
