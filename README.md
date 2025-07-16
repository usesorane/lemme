# Lemme - Laravel Documentation Generator

Lemme is a Laravel package that facilitates the creation of beautiful documentation websites from Markdown files. It provides a simple way to turn your project's documentation into a fully-featured website with a modern, responsive design.

## Features

- 📝 **Markdown-based**: Write your documentation in simple Markdown files
- 🎨 **Beautiful UI**: Modern design with Tailwind CSS 4
- 📱 **Responsive**: Works perfectly on all devices
- ⚡ **Fast**: Built-in caching for optimal performance
- 🎯 **Flexible routing**: Use subdomains or route prefixes
- 🔧 **Configurable**: Customize themes, directories, and more
- 🚀 **Laravel-native**: Seamlessly integrates with your Laravel application
- ✨ **Syntax highlighting**: Automatic code highlighting powered by Shiki

## Installation

Install the package via Composer:

```bash
composer require usesorane/lemme
```

Publish the config file (optional):

```bash
php artisan vendor:publish --tag="lemme-config"
```

Install the documentation system:

```bash
php artisan lemme:install
```

This will:
- Create a `docs` directory in your project root
- Generate sample documentation files
- Set up the necessary configuration

## Configuration

Use the environment variables or edit `config/lemme.php` to customize your documentation website:

```php
return [
    // Directory where your markdown files are stored
    'docs_directory' => env('LEMME_DOCS_DIRECTORY', 'docs'),

    // Subdomain for documentation (e.g., docs.yoursite.com)
    'subdomain' => env('LEMME_SUBDOMAIN', 'docs'),

    // Alternative: use route prefix instead of subdomain
    'route_prefix' => env('LEMME_ROUTE_PREFIX', null),

    // Theme configuration
    'theme' => env('LEMME_THEME', 'default'),

    // Site information
    'site_title' => env('LEMME_SITE_TITLE', 'Documentation'),
    'site_description' => env('LEMME_SITE_DESCRIPTION', 'Project Documentation'),

    // Navigation settings
    'navigation' => [
        'auto_generate' => true,
        'sort_by' => 'filename', // 'filename', 'title', 'created_at', 'modified_at'
        'sort_direction' => 'asc',
    ],

    // Cache settings
    'cache' => [
        'enabled' => env('LEMME_CACHE_ENABLED', true),
        'ttl' => env('LEMME_CACHE_TTL', 3600),
    ],
];
```

## Usage

### Creating Documentation

1. **Create Markdown files** in your configured docs directory (default: `docs/`)
2. **Add frontmatter** to set page metadata (optional):

```markdown
---
title: Getting Started
description: Learn how to get started with our project
---

# Getting Started

Your content here...
```

3. **Organize in folders** for better structure:

```
docs/
├── index.md
├── getting-started.md
├── guides/
│   ├── installation.md
│   └── configuration.md
└── api/
    ├── authentication.md
    └── endpoints.md
```

### Accessing Documentation

By default, your documentation will be available at:

- **Subdomain**: `https://docs.yoursite.com` (if using subdomain routing)
- **Route prefix**: `https://yoursite.com/docs` (if using route prefix)

### API Access

Lemme provides JSON API endpoints for headless usage:

- `GET /api` - Get all pages and navigation
- `GET /api/{slug}` - Get a specific page

**Example use cases:**
- Build custom documentation frontends
- Create mobile apps or SPAs
- Integrate with external tools or chatbots
- Generate documentation reports

### Commands

- `php artisan lemme:install` - Install and set up documentation
- `php artisan lemme:install --force` - Reinstall and overwrite existing files
- `php artisan lemme:clear` - Clear documentation cache

### Using the Facade

You can also interact with Lemme programmatically:

```php
use Sorane\Lemme\Facades\Lemme;

// Get all documentation pages
$pages = Lemme::getPages();

// Get a specific page
$page = Lemme::getPage('getting-started');

// Get navigation structure
$navigation = Lemme::getNavigation();

// Clear cache
Lemme::clearCache();
```

## Themes

Lemme comes with a beautiful default theme built with Tailwind CSS 4. The theme features:

- Clean, modern design
- Responsive layout
- Mobile-friendly navigation
- Syntax highlighting for code blocks
- Automatic page navigation
- Search-friendly structure

The syntax highlighting is powered by **Shiki** and supports:

- PHP, JavaScript, Python, Ruby, Go, Rust
- HTML, CSS, SCSS, JSON, YAML, XML  
- Bash, SQL, Docker, and many more!

Simply use triple backticks with the language identifier:

````markdown
```php
<?php echo "Hello, World!"; ?>
```
````

## Performance

Lemme includes built-in caching to ensure your documentation loads quickly:

- Pages are cached automatically
- Cache respects file modification times
- Easy cache clearing via command or facade
- Configurable cache TTL

## Subdomain Setup

To use subdomain routing (e.g., `docs.yoursite.com`):

1. **Configure DNS**: Add a CNAME record pointing `docs` to your main domain
2. **Set up web server**: Configure your web server to handle the subdomain
3. **Update config**: Set `LEMME_SUBDOMAIN=docs` in your `.env` file

For Apache, add to your virtual host:
```apache
ServerAlias docs.yoursite.com
```

For Nginx:
```nginx
server_name yoursite.com docs.yoursite.com;
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- **Author**: Rutger Broerze
- **Built with**: Laravel, Tailwind CSS, Alpine.js
- **Markdown parsing**: Spatie Laravel Markdown (with Shiki syntax highlighting)
- **Frontmatter parsing**: Spatie YAML Front Matter
