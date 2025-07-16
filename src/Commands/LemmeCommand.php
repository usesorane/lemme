<?php

namespace Sorane\Lemme\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Sorane\Lemme\Facades\Lemme;

class LemmeCommand extends Command
{
    public $signature = 'lemme:install {--force : Overwrite existing files}';

    public $description = 'Install Lemme documentation system';

    public function handle(): int
    {
        $this->info('Installing Lemme documentation system...');

        $docsDirectory = base_path(config('lemme.docs_directory', 'docs'));
        
        // Create docs directory if it doesn't exist
        if (!File::exists($docsDirectory)) {
            File::makeDirectory($docsDirectory, 0755, true);
            $this->info("Created directory: {$docsDirectory}");
        } else {
            $this->info("Directory already exists: {$docsDirectory}");
        }

        // Create sample documentation files
        $this->createSampleFiles($docsDirectory);

        // Clear cache if enabled
        if (config('lemme.cache.enabled')) {
            Lemme::clearCache();
            $this->info('Cleared documentation cache');
        }

        $this->newLine();
        $this->info('Lemme installation completed successfully!');
        $this->newLine();
        
        $this->line('Next steps:');
        $this->line('1. Add your markdown files to the ' . config('lemme.docs_directory', 'docs') . ' directory');
        $this->line('2. Configure your subdomain or route prefix in config/lemme.php');
        
        if (config('lemme.subdomain')) {
            $this->line('3. Your documentation will be available at: ' . config('lemme.subdomain') . '.' . parse_url(config('app.url'), PHP_URL_HOST));
        } else {
            $this->line('3. Your documentation will be available at: ' . url('docs'));
        }

        return self::SUCCESS;
    }

    protected function createSampleFiles(string $docsDirectory): void
    {
        $indexFile = $docsDirectory . '/index.md';
        $gettingStartedFile = $docsDirectory . '/getting-started.md';

        if (!File::exists($indexFile) || $this->option('force')) {
            File::put($indexFile, $this->getIndexContent());
            $this->info('Created: index.md');
        }

        if (!File::exists($gettingStartedFile) || $this->option('force')) {
            File::put($gettingStartedFile, $this->getGettingStartedContent());
            $this->info('Created: getting-started.md');
        }
    }

    protected function getIndexContent(): string
    {
        return <<<'MD'
---
title: Welcome to Documentation
---

# Welcome to Your Documentation

This is your project documentation homepage. Start editing this file to customize your documentation.

## Getting Started

- Edit markdown files in the `docs` directory
- Each markdown file becomes a page in your documentation
- Use frontmatter to set page titles and metadata
- Navigate between pages using the sidebar

## Features

- 📝 Write documentation in Markdown
- 🎨 Beautiful default theme with Tailwind CSS
- 📱 Responsive design that works on all devices
- 🔍 Fast search and navigation
- ⚡ Built for Laravel applications
- 🎨 **Automatic syntax highlighting** with Spatie Laravel Markdown

## Example Code

Here's some PHP code with automatic syntax highlighting:

```php
<?php

use Sorane\Lemme\Facades\Lemme;

// Get all documentation pages
$pages = Lemme::getPages();

// Get a specific page
$page = Lemme::getPage('getting-started');

// Clear the cache when needed
Lemme::clearCache();
```

JavaScript example:

```javascript
// Modern JavaScript with syntax highlighting
const fetchDocs = async () => {
    try {
        const response = await fetch('/docs/api');
        const data = await response.json();
        return data.pages;
    } catch (error) {
        console.error('Failed to fetch docs:', error);
    }
};
```

CSS styling:

```css
/* Custom documentation styles */
.docs-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.docs-nav {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
}
```

Happy documenting! 🚀
MD;
    }

    protected function getGettingStartedContent(): string
    {
        return <<<'MD'
---
title: Getting Started
---

# Getting Started

Welcome! This guide will help you get up and running with your documentation.

## Creating Pages

1. Create a new `.md` file in your docs directory
2. Add frontmatter at the top (optional but recommended):

```yaml
---
title: Your Page Title
description: A brief description
---
```

3. Write your content using Markdown

## Organizing Content

You can organize your documentation in folders:

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

## Markdown Features

### Headers

Use `#` for headers:

```markdown
# Main Header
## Sub Header
### Sub-sub Header
```

### Lists

- Unordered lists use `-` or `*`
- You can nest lists
  - Like this
  - And this

1. Ordered lists use numbers
2. They're automatically numbered
3. Even if you use the same number

### Code Blocks with Syntax Highlighting

Lemme uses **Spatie Laravel Markdown** which provides automatic syntax highlighting for many languages:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    protected $fillable = ['title', 'content', 'slug'];
    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
```

```javascript
// JavaScript with beautiful highlighting
class DocumentationManager {
    constructor(apiUrl) {
        this.apiUrl = apiUrl;
    }
    
    async fetchPage(slug) {
        const response = await fetch(`${this.apiUrl}/${slug}`);
        return await response.json();
    }
}
```

```bash
# Even bash commands are highlighted
composer require usesorane/lemme
php artisan lemme:install
php artisan lemme:clear
```

```json
{
    "name": "My Documentation",
    "version": "1.0.0",
    "features": [
        "Markdown parsing",
        "Syntax highlighting",
        "Responsive design"
    ]
}
```

### Links and Images

[Link to another page](getting-started)
![Alt text for image](https://via.placeholder.com/400x200)

## Configuration

Edit `config/lemme.php` to customize:

- Documentation directory location
- Subdomain or route prefix
- Theme settings
- Cache configuration

## Syntax Highlighting

The syntax highlighting is powered by **Spatie Laravel Markdown** and supports:

- PHP, JavaScript, Python, Ruby, Go, Rust
- HTML, CSS, SCSS, JSON, YAML, XML
- Bash, SQL, Docker, and many more!

Simply use triple backticks with the language identifier:

````markdown
```php
<?php echo "Hello, World!"; ?>
```
````

## Need Help?

- Check the configuration file for all available options
- All pages are cached automatically for better performance
- Use `php artisan lemme:install --force` to recreate sample files
- Syntax highlighting works out of the box - no configuration needed!
MD;
    }
}
