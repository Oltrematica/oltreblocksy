<?php

namespace OltreBlocksy\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Base Test Case for OltreBlocksy Theme Tests
 *
 * @package OltreBlocksy\Tests
 * @since 1.0.0
 */
abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Set up the test environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        
        // Mock common WordPress functions
        $this->mockWordPressFunctions();
    }

    /**
     * Clean up the test environment after each test.
     */
    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Mock common WordPress functions used throughout the theme
     */
    protected function mockWordPressFunctions(): void
    {
        // WordPress core functions
        Monkey\Functions\when('wp_json_encode')->alias('json_encode');
        Monkey\Functions\when('esc_attr')->alias('htmlspecialchars');
        Monkey\Functions\when('esc_html')->alias('htmlspecialchars');
        Monkey\Functions\when('esc_url')->returnArg();
        
        // Theme modification functions
        Monkey\Functions\when('get_theme_mod')->justReturn(null);
        Monkey\Functions\when('set_theme_mod')->justReturn(true);
        
        // Hook functions
        Monkey\Functions\when('add_action')->justReturn(true);
        Monkey\Functions\when('add_filter')->justReturn(true);
        Monkey\Functions\when('apply_filters')->returnArg();
        
        // Translation functions
        Monkey\Functions\when('__')->returnArg();
        Monkey\Functions\when('esc_html__')->returnArg();
        
        // Sanitization functions
        Monkey\Functions\when('sanitize_key')->alias(function($key) {
            return preg_replace('/[^a-z0-9_\-]/', '', strtolower($key));
        });
        
        Monkey\Functions\when('sanitize_hex_color')->alias(function($color) {
            if ('' === $color) {
                return '';
            }
            
            if (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color)) {
                return $color;
            }
            
            return null;
        });
        
        Monkey\Functions\when('wp_validate_boolean')->alias(function($var) {
            if (is_bool($var)) {
                return $var;
            }
            
            if (is_string($var) && 'false' === strtolower($var)) {
                return false;
            }
            
            return (bool) $var;
        });
        
        // Plugin functions
        Monkey\Functions\when('is_plugin_active')->justReturn(false);
        Monkey\Functions\when('is_plugin_active_for_network')->justReturn(false);
        
        // WordPress constants
        if (!defined('HOUR_IN_SECONDS')) {
            define('HOUR_IN_SECONDS', 3600);
        }
    }

    /**
     * Create a mock WordPress theme modification value
     *
     * @param string $mod_name The theme mod name
     * @param mixed $value The value to return
     * @return void
     */
    protected function mockThemeMod(string $mod_name, $value = null): void
    {
        Monkey\Functions\when('get_theme_mod')
            ->with($mod_name, \Mockery::any())
            ->justReturn($value);
    }

    /**
     * Assert that a WordPress hook was added
     *
     * @param string $hook_name The hook name
     * @param callable|string $callback The callback
     * @param int $priority The priority
     * @param int $accepted_args Number of accepted arguments
     * @return void
     */
    protected function assertHookAdded(string $hook_name, $callback = null, int $priority = 10, int $accepted_args = 1): void
    {
        if ($callback === null) {
            Monkey\Actions\has($hook_name)->shouldHaveBeenCalled();
        } else {
            Monkey\Actions\has($hook_name, $callback, $priority)->shouldHaveBeenCalled();
        }
    }

    /**
     * Create a test instance of a module with mocked dependencies
     *
     * @param string $module_class The module class name
     * @param array $settings Optional settings to mock
     * @return object The module instance
     */
    protected function createModuleInstance(string $module_class, array $settings = []): object
    {
        // Mock get_setting method to return test values
        $mock = \Mockery::mock($module_class)->makePartial();
        
        foreach ($settings as $key => $value) {
            $mock->shouldReceive('get_setting')
                ->with($key, \Mockery::any())
                ->andReturn($value);
        }
        
        return $mock;
    }

    /**
     * Assert that CSS contains expected properties
     *
     * @param string $css The CSS to check
     * @param array $expected_properties Expected CSS properties
     * @return void
     */
    protected function assertCssContains(string $css, array $expected_properties): void
    {
        foreach ($expected_properties as $property => $value) {
            $pattern = '/' . preg_quote($property, '/') . '\s*:\s*' . preg_quote($value, '/') . '/';
            $this->assertMatchesRegularExpression(
                $pattern,
                $css,
                "CSS should contain property '{$property}' with value '{$value}'"
            );
        }
    }

    /**
     * Assert that a value is a valid hex color
     *
     * @param string $color The color to validate
     * @return void
     */
    protected function assertValidHexColor(string $color): void
    {
        $this->assertMatchesRegularExpression(
            '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            $color,
            "'{$color}' is not a valid hex color"
        );
    }

    /**
     * Assert that an array has the expected structure
     *
     * @param array $expected_keys The expected keys
     * @param array $actual_array The actual array
     * @return void
     */
    protected function assertArrayStructure(array $expected_keys, array $actual_array): void
    {
        foreach ($expected_keys as $key) {
            $this->assertArrayHasKey($key, $actual_array, "Array should have key '{$key}'");
        }
    }
}