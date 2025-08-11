<?php

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
    | Leave as null to use route prefix instead (e.g., yoursite.com/docs).
    |
    */
    'subdomain' => env('LEMME_SUBDOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The route prefix where your documentation will be served.
    | By default, it's 'docs' (e.g., yoursite.com/docs).
    | Set to null to use subdomain routing instead.
    |
    */
    'route_prefix' => env('LEMME_ROUTE_PREFIX', 'docs'),

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

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    |
    | Configure search functionality settings.
    |
    */
    'search' => [
        'max_content_length' => env('LEMME_SEARCH_MAX_CONTENT_LENGTH', 0), // 0 = no limit (index full content)
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Configure how the logo in the documentation layout is rendered.
    | Supported types:
    | - view  : renders a Blade view (default existing partial)
    | - image : renders an <img> tag (provide image path relative to public/ or full URL)
    | - text  : renders plain text inside a <span>
    |
    | You can override via env vars, e.g.:
    |   LEMME_LOGO_TYPE=image
    |   LEMME_LOGO_IMAGE="images/logo.svg"
    |   LEMME_LOGO_ALT="My Project"
    |
    */
    'logo' => [
        'type' => env('LEMME_LOGO_TYPE', 'view'),
        'view' => env('LEMME_LOGO_VIEW', 'lemme::partials.logo'),
        'image' => env('LEMME_LOGO_IMAGE', null),
        'text' => env('LEMME_LOGO_TEXT', null),
        'alt' => env('LEMME_LOGO_ALT', 'Logo'),
        // Additional CSS classes applied to the root element of image/text variants
        'classes' => env('LEMME_LOGO_CLASSES', 'h-6 text-black dark:text-white'),
    ],
];
