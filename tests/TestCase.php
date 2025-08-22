<?php

namespace Sorane\Lemme\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('cache.default', 'array');
        // Provide encryption key for tests (Livewire, encryption services)
        $app['config']->set('app.key', 'base64:'.base64_encode('a'.str_repeat('b', 31)));
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            \Sorane\Lemme\LemmeServiceProvider::class,
        ];
    }
}
