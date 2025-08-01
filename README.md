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
- 📁 **Directory-based grouping**: Organize navigation automatically using folder structure

## Installation

Install the package via Composer:

```bash
composer require usesorane/lemme
```

Publish the config and assets:

```bash
php artisan vendor:publish --tag="lemme-config"
php artisan vendor:publish --tag="lemme-assets"
```

Install the documentation system:

```bash
php artisan lemme:install
```

This will:
- Create a `docs` directory in your project root
- Generate sample documentation files with **numbered structure**
- Publish the compiled Tailwind CSS assets
- Set up the necessary configuration

**Example structure created:**
```
docs/
├── index.md
├── 1_getting-started/
│   ├── 1_installation.md
│   ├── 2_configuration.md
│   └── 3_first-steps.md
├── 2_api/
│   ├── 1_authentication.md
│   ├── 2_endpoints.md
│   └── 3_advanced/
│       ├── 1_webhooks.md
│       └── 2_rate-limiting.md
└── 3_guides/
    ├── 1_deployment.md
    └── 2_troubleshooting.md
```

## Configuration

Use the environment variables or edit `config/lemme.php` to customize your documentation website:

```php
return [
    // Directory where your markdown files are stored
    'docs_directory' => env('LEMME_DOCS_DIRECTORY', 'docs'),

    // Route prefix for documentation (e.g., yoursite.com/docs)
    'route_prefix' => env('LEMME_ROUTE_PREFIX', 'docs'),

    // Alternative: use subdomain instead of route prefix
    'subdomain' => env('LEMME_SUBDOMAIN', null),

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
        
        // Directory-based grouping
        'grouping' => [
            'enabled' => true,
            'sort_groups_by' => 'directory_name',
            'sort_groups_direction' => 'asc',
        ],
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
slug: custom-getting-started
---

# Getting Started

Your content here...
```

#### Slug Configuration

Slugs determine the URL path for your documentation pages:

- **Custom slugs**: Set a custom `slug` in the frontmatter to override the default
- **Auto-generated slugs**: When no slug is provided, Lemme automatically generates one from the filename:
  - Converts to `kebab-case` format
  - Removes number prefixes (e.g., `1_`, `2-`, `10_`)
  - Excludes directory names (only uses the filename)

**Examples:**
```
docs/1_getting-started/2_installation.md
├─ Default slug: "installation" 
├─ URL: /installation

docs/api/3-authentication.md  
├─ Default slug: "authentication"
├─ URL: /authentication

docs/guides/advanced-features.md
├─ Default slug: "advanced-features"  
├─ URL: /advanced-features
```

**Custom slug example:**
```markdown
---
title: Installation Guide
slug: setup
---
```
Result: `/setup` instead of `/installation`

3. **Organize in folders** for better structure:

```
docs/
├── index.md
├── getting-started/
│   ├── installation.md
│   └── configuration.md
├── guides/
│   ├── deployment.md
│   └── troubleshooting.md
└── api/
    ├── authentication.md
    └── endpoints.md
```

### Directory-based Navigation Grouping

Lemme automatically organizes your navigation based on your directory structure. Each folder becomes a group in the sidebar navigation:

- **Root files** (like `index.md`) appear ungrouped at the top
- **Folder-based files** are grouped under collapsible sections
- **Nested folders** create sub-groups for better organization

**Example Structure:**
```
docs/
├── index.md                    # Ungrouped: "Home"  
├── 1_getting-started/          # Group: "Getting Started" (sorted first)
│   ├── 1_installation.md       #   ├─ Installation
│   └── 2_configuration.md      #   └─ Configuration
└── 2_api/                      # Group: "API" (sorted second)
    ├── 1_authentication.md     #   ├─ Authentication
    ├── 2_endpoints.md          #   ├─ Endpoints  
    └── 3_advanced/             #   └─ Advanced (sub-group)
        ├── 1_webhooks.md       #       ├─ Webhooks
        └── 2_rate-limiting.md  #       └─ Rate Limiting
```

**Number Prefixes for Sorting:**
- Use `1_`, `2_`, `10_` or `1-`, `2-`, `10-` prefixes for precise ordering
- Number prefixes are automatically removed from navigation titles
- Both `snake_case` and `kebab-case` naming conventions are supported
- Files and directories without numbers sort alphabetically after numbered ones

You can disable grouping in the configuration if you prefer a flat navigation structure.

### Accessing Documentation

By default, your documentation will be available at:

- **Route prefix**: `https://yoursite.com/docs` (default)
- **Subdomain**: `https://docs.yoursite.com` (if using subdomain routing)

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

## Subdomain Setup (Optional)

By default, Lemme serves documentation at `/docs` on your main domain. To use subdomain routing instead (e.g., `docs.yoursite.com`):

1. **Configure DNS**: Add a CNAME record pointing `docs` to your main domain
2. **Set up web server**: Configure your web server to handle the subdomain
3. **Update config**: Set `LEMME_SUBDOMAIN=docs` and `LEMME_ROUTE_PREFIX=null` in your `.env` file

For Apache, add to your virtual host:
```apache
ServerAlias docs.yoursite.com
```

For Nginx:
```nginx
server_name yoursite.com docs.yoursite.com;
```

## Development

If you're contributing to Lemme or want to customize the styles:

### Building Assets

```bash
# Install dependencies
bun install

# Build for development (with watching)
bun run dev

# Build for production
bun run build
```

The compiled CSS will be generated in the `dist/` directory and can be published to Laravel projects.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- **Author**: Rutger Broerze
- **Built with**: Laravel, Tailwind CSS, Alpine.js
- **Markdown parsing**: Spatie Laravel Markdown (with Shiki syntax highlighting)
- **Frontmatter parsing**: Spatie YAML Front Matter
