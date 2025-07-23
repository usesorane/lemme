<?php

namespace Sorane\Lemme\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;
use Sorane\Lemme\Facades\Lemme;
use Sorane\Lemme\LemmeServiceProvider;

class LemmeTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LemmeServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Lemme' => Lemme::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test docs directory
        $this->app['config']->set('lemme.docs_directory', 'tests/docs');
        $this->app['config']->set('lemme.cache.enabled', false);
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (File::exists(base_path('tests/docs'))) {
            File::deleteDirectory(base_path('tests/docs'));
        }

        parent::tearDown();
    }

    public function test_it_can_get_empty_pages_collection()
    {
        $pages = Lemme::getPages();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $pages);
        $this->assertEmpty($pages);
    }

    public function test_it_can_parse_markdown_files()
    {
        // Create test docs directory and file
        $docsPath = base_path('tests/docs');
        File::ensureDirectoryExists($docsPath);

        File::put($docsPath.'/test.md', <<<'MD'
---
title: Test Page
description: A test page
---

# Test Content

This is a test page with some content.
MD);

        $pages = Lemme::getPages();

        $this->assertCount(1, $pages);
        $this->assertEquals('Test Page', $pages->first()['title']);
        $this->assertEquals('test', $pages->first()['slug']);
        $this->assertStringContainsString('<h1 id="test-content">Test Content</h1>', $pages->first()['raw_content']);
    }

    public function test_it_can_get_specific_page()
    {
        // Create test docs directory and file
        $docsPath = base_path('tests/docs');
        File::ensureDirectoryExists($docsPath);

        File::put($docsPath.'/getting-started.md', <<<'MD'
---
title: Getting Started
---

# Getting Started Guide
MD);

        $page = Lemme::getPage('getting-started');

        $this->assertNotNull($page);
        $this->assertEquals('Getting Started', $page['title']);
        $this->assertEquals('getting-started', $page['slug']);
    }

    public function test_it_generates_navigation()
    {
        // Create test docs directory and files
        $docsPath = base_path('tests/docs');
        File::ensureDirectoryExists($docsPath);

        File::put($docsPath.'/index.md', <<<'MD'
---
title: Home
---

# Home Page
MD);

        File::put($docsPath.'/about.md', <<<'MD'
---
title: About
---

# About Page
MD);

        $navigation = Lemme::getNavigation();

        $this->assertCount(2, $navigation);
        $this->assertEquals('About', $navigation->first()['title']);
        $this->assertEquals('Home', $navigation->last()['title']);
    }

    public function test_it_generates_kebab_case_slugs_from_filenames()
    {
        // Create test docs directory and files
        $docsPath = base_path('tests/docs');
        File::ensureDirectoryExists($docsPath);

        // Test various filename formats
        File::put($docsPath.'/1_getting_started.md', <<<'MD'
---
title: Getting Started
---

# Getting Started
MD);

        File::put($docsPath.'/2-advanced-features.md', <<<'MD'
---
title: Advanced Features
---

# Advanced Features
MD);

        File::put($docsPath.'/10_camelCaseFile.md', <<<'MD'
---
title: Camel Case File
---

# Camel Case File
MD);

        $pages = Lemme::getPages();

        $this->assertCount(3, $pages);

        $slugs = $pages->pluck('slug')->toArray();

        $this->assertContains('getting-started', $slugs);
        $this->assertContains('advanced-features', $slugs);
        $this->assertContains('camel-case-file', $slugs);
    }

    public function test_it_uses_frontmatter_slug_when_provided()
    {
        // Create test docs directory and files
        $docsPath = base_path('tests/docs');
        File::ensureDirectoryExists($docsPath);

        File::put($docsPath.'/1_getting_started.md', <<<'MD'
---
title: Getting Started
slug: custom-setup-guide
---

# Getting Started
MD);

        $pages = Lemme::getPages();
        $page = $pages->first();

        $this->assertEquals('custom-setup-guide', $page['slug']);
        $this->assertEquals('Getting Started', $page['title']);
    }

    public function test_it_generates_slug_from_filename_only_not_directory()
    {
        // Create test docs directory and files with subdirectories
        $docsPath = base_path('tests/docs');
        File::ensureDirectoryExists($docsPath.'/1_getting-started');
        File::ensureDirectoryExists($docsPath.'/2_api');

        File::put($docsPath.'/1_getting-started/1_installation.md', <<<'MD'
---
title: Installation
---

# Installation Guide
MD);

        File::put($docsPath.'/2_api/1_authentication.md', <<<'MD'
---
title: Authentication
---

# API Authentication
MD);

        $pages = Lemme::getPages();
        $slugs = $pages->pluck('slug')->toArray();

        // Slugs should be based on filename only, not include directory
        $this->assertContains('installation', $slugs);
        $this->assertContains('authentication', $slugs);

        // Should not contain directory names
        $this->assertNotContains('getting-started-installation', $slugs);
        $this->assertNotContains('api-authentication', $slugs);
    }
}
