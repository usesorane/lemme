<?php

use Illuminate\Support\Facades\Route;
use Sorane\Lemme\Http\Controllers\DocsController;

// Routes for documentation
$subdomain = config('lemme.subdomain');
$routePrefix = config('lemme.route_prefix');

if ($subdomain && ! $routePrefix) {
    // Use subdomain routing
    Route::domain($subdomain.'.'.\Sorane\Lemme\Lemme::baseHost())
        ->middleware(['web'])
        ->group(function () {
            Route::get('/', [DocsController::class, 'show'])->name('lemme.home');
            Route::get('/{slug}', [DocsController::class, 'show'])->name('lemme.page')->where('slug', '.*');

            // API routes
            Route::prefix('api')->group(function () {
                Route::get('/', [DocsController::class, 'api'])->name('lemme.api');
                Route::get('/{slug}', [DocsController::class, 'apiPage'])->name('lemme.api.page')->where('slug', '.*');
            });
        });
} elseif ($routePrefix) {
    // Use route prefix
    Route::prefix($routePrefix)
        ->middleware(['web'])
        ->group(function () {
            Route::get('/', [DocsController::class, 'show'])->name('lemme.home');
            Route::get('/{slug}', [DocsController::class, 'show'])->name('lemme.page')->where('slug', '.*');

            // API routes
            Route::prefix('api')->group(function () {
                Route::get('/', [DocsController::class, 'api'])->name('lemme.api');
                Route::get('/{slug}', [DocsController::class, 'apiPage'])->name('lemme.api.page')->where('slug', '.*');
            });
        });
} else {
    // Default: use main domain with docs prefix
    Route::prefix('docs')
        ->middleware(['web'])
        ->group(function () {
            Route::get('/', [DocsController::class, 'show'])->name('lemme.home');
            Route::get('/{slug}', [DocsController::class, 'show'])->name('lemme.page')->where('slug', '.*');

            // API routes
            Route::prefix('api')->group(function () {
                Route::get('/', [DocsController::class, 'api'])->name('lemme.api');
                Route::get('/{slug}', [DocsController::class, 'apiPage'])->name('lemme.api.page')->where('slug', '.*');
            });
        });
}
