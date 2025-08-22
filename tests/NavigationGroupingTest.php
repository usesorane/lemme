<?php

use Sorane\Lemme\Lemme;

// Keeping reflection-based tests for now; integration navigation test added separately.

beforeEach(function () {
    $this->lemme = new Lemme;
});

it('groups pages by directory', function () {
    $pages = collect([
        ['title' => 'Home', 'slug' => '', 'relative_path' => 'index.md'],
        ['title' => 'Installation', 'slug' => 'getting-started-installation', 'relative_path' => 'getting-started/installation.md'],
        ['title' => 'Configuration', 'slug' => 'getting-started-configuration', 'relative_path' => 'getting-started/configuration.md'],
        ['title' => 'Authentication', 'slug' => 'api-authentication', 'relative_path' => 'api/authentication.md'],
        ['title' => 'Webhooks', 'slug' => 'api-advanced-webhooks', 'relative_path' => 'api/advanced/webhooks.md'],
    ]);

    $reflection = new ReflectionClass($this->lemme);
    $method = $reflection->getMethod('groupPagesByDirectory');
    $method->setAccessible(true);
    $result = $method->invoke($this->lemme, $pages);

    expect($result)->toHaveKeys(['_root', 'getting-started', 'api'])
        ->and($result['_root'])->toHaveCount(1)
        ->and($result['_root'][0]['title'])->toBe('Home')
        ->and($result['getting-started'])->toHaveKey('_pages')
        ->and($result['getting-started']['_pages'])->toHaveCount(2)
        ->and($result['api'])->toHaveKey('advanced')
        ->and($result['api']['advanced'])->toHaveKey('_pages')
        ->and($result['api']['advanced']['_pages'])->toHaveCount(1)
        ->and($result['api']['advanced']['_pages'][0]['title'])->toBe('Webhooks');
});

it('formats group title', function () {
    $reflection = new ReflectionClass($this->lemme);
    $method = $reflection->getMethod('formatGroupTitle');
    $method->setAccessible(true);

    expect($method->invoke($this->lemme, 'getting-started'))->toBe('Getting Started')
        ->and($method->invoke($this->lemme, 'api_reference'))->toBe('Api Reference')
        ->and($method->invoke($this->lemme, 'user-management'))->toBe('User Management')
        ->and($method->invoke($this->lemme, '1_getting-started'))->toBe('Getting Started')
        ->and($method->invoke($this->lemme, '01-api_reference'))->toBe('Api Reference')
        ->and($method->invoke($this->lemme, '10_advanced-topics'))->toBe('Advanced Topics');
});

it('removes number prefix', function () {
    $reflection = new ReflectionClass($this->lemme);
    $method = $reflection->getMethod('removeNumberPrefix');
    $method->setAccessible(true);

    expect($method->invoke($this->lemme, '1_installation.md'))->toBe('installation.md')
        ->and($method->invoke($this->lemme, '01-configuration.md'))->toBe('configuration.md')
        ->and($method->invoke($this->lemme, '10_advanced-topics'))->toBe('advanced-topics')
        ->and($method->invoke($this->lemme, '100-webhooks'))->toBe('webhooks')
        ->and($method->invoke($this->lemme, 'installation.md'))->toBe('installation.md')
        ->and($method->invoke($this->lemme, 'getting-started'))->toBe('getting-started');
});

it('gets sortable directory name', function () {
    $reflection = new ReflectionClass($this->lemme);
    $method = $reflection->getMethod('getSortableDirectoryName');
    $method->setAccessible(true);

    expect($method->invoke($this->lemme, '1_getting-started'))->toBe('00001_getting-started')
        ->and($method->invoke($this->lemme, '10_advanced'))->toBe('00010_advanced')
        ->and($method->invoke($this->lemme, '1-api'))->toBe('00001_api')
        ->and($method->invoke($this->lemme, 'misc'))->toBe('99999_misc')
        ->and($method->invoke($this->lemme, 'guides'))->toBe('99999_guides');
});
