<?php

use Sorane\Lemme\Facades\Lemme;
use Sorane\Lemme\Tests\Support\DocsFactory;
use Illuminate\Support\Facades\Cache;

it('rotates html cache key when file modified', function () {
    $docs = DocsFactory::make();
    config()->set('lemme.docs_directory', $docs->relativePath());
    config()->set('lemme.cache.enabled', true);

    $docs->file('rotate.md', "# First\n");
    $firstHtml = Lemme::getPageHtml('rotate');
    expect($firstHtml)->toContain('First');

    $pointerKey = 'lemme.html.current.rotate';
    $initialCacheKey = Cache::get($pointerKey);
    expect($initialCacheKey)->not->toBeNull();

    sleep(1);
    $docs->file('rotate.md', "# Second\n");
    Lemme::clearCache();
    $secondHtml = Lemme::getPageHtml('rotate');
    expect($secondHtml)->toContain('Second');
    $newCacheKey = Cache::get($pointerKey);
    expect($newCacheKey)->not->toBe($initialCacheKey);
    expect(Cache::has($initialCacheKey))->toBeFalse();
});
