<?php

namespace Sorane\Lemme;

use Sorane\Lemme\Commands\LemmeClearCommand;
use Sorane\Lemme\Commands\LemmeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LemmeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('lemme')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasCommands(
                LemmeCommand::class,
                LemmeClearCommand::class
            );
    }

    public function packageBooted()
    {
        // Bind the Lemme class to the container
        $this->app->singleton('lemme', function () {
            return new Lemme;
        });
    }
}
