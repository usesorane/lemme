<?php

use PHPUnit\Framework\TestCase;
use Sorane\Lemme\Lemme;

class NavigationGroupingTest extends TestCase
{
    protected $lemme;

    protected function setUp(): void
    {
        $this->lemme = new Lemme;
    }

    public function test_group_pages_by_directory()
    {
        // Mock pages data
        $pages = collect([
            [
                'title' => 'Home',
                'slug' => '',
                'relative_path' => 'index.md',
            ],
            [
                'title' => 'Installation',
                'slug' => 'getting-started-installation',
                'relative_path' => 'getting-started/installation.md',
            ],
            [
                'title' => 'Configuration',
                'slug' => 'getting-started-configuration',
                'relative_path' => 'getting-started/configuration.md',
            ],
            [
                'title' => 'Authentication',
                'slug' => 'api-authentication',
                'relative_path' => 'api/authentication.md',
            ],
            [
                'title' => 'Webhooks',
                'slug' => 'api-advanced-webhooks',
                'relative_path' => 'api/advanced/webhooks.md',
            ],
        ]);

        // Use reflection to access protected method
        $reflection = new ReflectionClass($this->lemme);
        $method = $reflection->getMethod('groupPagesByDirectory');
        $method->setAccessible(true);

        $result = $method->invoke($this->lemme, $pages);

        // Assert structure
        $this->assertArrayHasKey('_root', $result);
        $this->assertArrayHasKey('getting-started', $result);
        $this->assertArrayHasKey('api', $result);

        // Assert root pages
        $this->assertCount(1, $result['_root']);
        $this->assertEquals('Home', $result['_root'][0]['title']);

        // Assert getting-started group
        $this->assertArrayHasKey('_pages', $result['getting-started']);
        $this->assertCount(2, $result['getting-started']['_pages']);

        // Assert nested api/advanced structure
        $this->assertArrayHasKey('advanced', $result['api']);
        $this->assertArrayHasKey('_pages', $result['api']['advanced']);
        $this->assertCount(1, $result['api']['advanced']['_pages']);
        $this->assertEquals('Webhooks', $result['api']['advanced']['_pages'][0]['title']);
    }

    public function test_format_group_title()
    {
        $reflection = new ReflectionClass($this->lemme);
        $method = $reflection->getMethod('formatGroupTitle');
        $method->setAccessible(true);

        // Test regular formatting
        $this->assertEquals('Getting Started', $method->invoke($this->lemme, 'getting-started'));
        $this->assertEquals('Api Reference', $method->invoke($this->lemme, 'api_reference'));
        $this->assertEquals('User Management', $method->invoke($this->lemme, 'user-management'));

        // Test number prefix removal
        $this->assertEquals('Getting Started', $method->invoke($this->lemme, '1_getting-started'));
        $this->assertEquals('Api Reference', $method->invoke($this->lemme, '01-api_reference'));
        $this->assertEquals('Advanced Topics', $method->invoke($this->lemme, '10_advanced-topics'));
    }

    public function test_remove_number_prefix()
    {
        $reflection = new ReflectionClass($this->lemme);
        $method = $reflection->getMethod('removeNumberPrefix');
        $method->setAccessible(true);

        // Test various number prefix patterns
        $this->assertEquals('installation.md', $method->invoke($this->lemme, '1_installation.md'));
        $this->assertEquals('configuration.md', $method->invoke($this->lemme, '01-configuration.md'));
        $this->assertEquals('advanced-topics', $method->invoke($this->lemme, '10_advanced-topics'));
        $this->assertEquals('webhooks', $method->invoke($this->lemme, '100-webhooks'));

        // Test without number prefix (should remain unchanged)
        $this->assertEquals('installation.md', $method->invoke($this->lemme, 'installation.md'));
        $this->assertEquals('getting-started', $method->invoke($this->lemme, 'getting-started'));
    }

    public function test_sortable_directory_name()
    {
        $reflection = new ReflectionClass($this->lemme);
        $method = $reflection->getMethod('getSortableDirectoryName');
        $method->setAccessible(true);

        // Test numeric sorting
        $this->assertEquals('00001_getting-started', $method->invoke($this->lemme, '1_getting-started'));
        $this->assertEquals('00010_advanced', $method->invoke($this->lemme, '10_advanced'));
        $this->assertEquals('00001_api', $method->invoke($this->lemme, '1-api'));

        // Test non-numbered items (should sort last)
        $this->assertEquals('99999_misc', $method->invoke($this->lemme, 'misc'));
        $this->assertEquals('99999_guides', $method->invoke($this->lemme, 'guides'));
    }
}
