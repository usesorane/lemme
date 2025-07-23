<?php

// config for Sorane/Lemme
return [
    /*
    |--------------------------------------------------------------------------
    | Documentation Directory
    |--------------------------------------------------------------------------
    |
    | This is the directory where your markdown documentation files are stored.
    | By default, it's 'docs' but you can change it to any directory you prefer.
    |
    */
    'docs_directory' => env('LEMME_DOCS_DIRECTORY', 'docs'),

    /*
    |--------------------------------------------------------------------------
    | Subdomain
    |--------------------------------------------------------------------------
    |
    | The subdomain where your documentation will be served.
    | By default, it's 'docs' (e.g., docs.yoursite.com).
    |
    */
    'subdomain' => env('LEMME_SUBDOMAIN', 'docs'),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | If you don't want to use a subdomain, you can use a route prefix instead.
    | Leave as null to use subdomain, or set to a string like 'docs' to use
    | yoursite.com/docs instead.
    |
    */
    'route_prefix' => env('LEMME_ROUTE_PREFIX', null),

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | The theme to use for your documentation site.
    | Available themes: 'default', 'dark', 'minimal'
    |
    */
    'theme' => env('LEMME_THEME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Site Title
    |--------------------------------------------------------------------------
    |
    | The title of your documentation site.
    |
    */
    'site_title' => env('LEMME_SITE_TITLE', 'Documentation'),

    /*
    |--------------------------------------------------------------------------
    | Site Description
    |--------------------------------------------------------------------------
    |
    | A brief description of your documentation site.
    |
    */
    'site_description' => env('LEMME_SITE_DESCRIPTION', 'Project Documentation'),

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Configure how navigation is generated from your markdown files.
    |
    */
    'navigation' => [
        'auto_generate' => true,
        'sort_by' => 'filename', // 'filename', 'title', 'created_at', 'modified_at'
        'sort_direction' => 'asc',

        // Directory-based grouping
        'grouping' => [
            'enabled' => true,
            'sort_groups_by' => 'directory_name', // 'directory_name', 'title'
            'sort_groups_direction' => 'asc',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Enable caching for better performance in production.
    |
    */
    'cache' => [
        'enabled' => env('LEMME_CACHE_ENABLED', true),
        'ttl' => env('LEMME_CACHE_TTL', 3600), // 1 hour
    ],
];
