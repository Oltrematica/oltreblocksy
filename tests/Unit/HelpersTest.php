<?php

use Brain\Monkey;

/**
 * Test helper functions
 */
describe('Helper Functions', function () {

    beforeEach(function () {
        // Load helper functions
        require_once OLTREBLOCKSY_INC_DIR . '/helpers.php';
    });

    it('checks if plugin is active correctly', function () {
        // Mock WordPress functions
        Monkey\Functions\when('get_option')
            ->with('active_plugins', [])
            ->justReturn(['test-plugin/test-plugin.php', 'another-plugin/another-plugin.php']);
        
        Monkey\Functions\when('is_plugin_active_for_network')
            ->justReturn(false);
        
        expect(oltreblocksy_is_plugin_active('test-plugin/test-plugin.php'))->toBeTrue();
        expect(oltreblocksy_is_plugin_active('non-existent-plugin/plugin.php'))->toBeFalse();
    });

    it('checks network active plugins', function () {
        Monkey\Functions\when('get_option')
            ->with('active_plugins', [])
            ->justReturn([]);
        
        Monkey\Functions\when('is_plugin_active_for_network')
            ->with('network-plugin/network-plugin.php')
            ->justReturn(true);
        
        expect(oltreblocksy_is_plugin_active('network-plugin/network-plugin.php'))->toBeTrue();
    });

    it('returns SVG icons correctly', function () {
        $arrow_right = oltreblocksy_get_svg_icon('arrow-right', 24);
        
        expect($arrow_right)->toBeString();
        expect($arrow_right)->toContain('<svg');
        expect($arrow_right)->toContain('width="24"');
        expect($arrow_right)->toContain('height="24"');
        expect($arrow_right)->toContain('stroke="currentColor"');
    });

    it('returns empty string for non-existent icons', function () {
        $non_existent = oltreblocksy_get_svg_icon('non-existent-icon');
        expect($non_existent)->toBe('');
    });

    it('handles different icon sizes', function () {
        $small_icon = oltreblocksy_get_svg_icon('search', 16);
        $large_icon = oltreblocksy_get_svg_icon('search', 32);
        
        expect($small_icon)->toContain('width="16"');
        expect($small_icon)->toContain('height="16"');
        expect($large_icon)->toContain('width="32"');
        expect($large_icon)->toContain('height="32"');
    });

    it('provides all expected icons', function () {
        $expected_icons = [
            'arrow-right', 'arrow-left', 'search', 'menu', 
            'close', 'chevron-down', 'external-link'
        ];
        
        foreach ($expected_icons as $icon) {
            $svg = oltreblocksy_get_svg_icon($icon);
            expect($svg)->toBeString();
            expect($svg)->not->toBe('');
            expect($svg)->toContain('<svg');
        }
    });

    it('sanitizes HTML classes correctly', function () {
        expect(oltreblocksy_sanitize_html_classes('test-class'))->toBe('test-class');
        expect(oltreblocksy_sanitize_html_classes(['class1', 'class2']))->toBe('class1 class2');
        
        // Test with invalid characters (function relies on sanitize_html_class)
        Monkey\Functions\when('sanitize_html_class')->returnArg();
        expect(oltreblocksy_sanitize_html_classes('valid-class'))->toBe('valid-class');
    });

    it('calculates reading time correctly', function () {
        $short_content = str_repeat('word ', 50); // ~50 words
        $medium_content = str_repeat('word ', 400); // ~400 words
        $long_content = str_repeat('word ', 1000); // ~1000 words
        
        expect(oltreblocksy_get_reading_time($short_content))->toBe(1); // Minimum 1 minute
        expect(oltreblocksy_get_reading_time($medium_content))->toBe(2); // ~2 minutes
        expect(oltreblocksy_get_reading_time($long_content))->toBe(5); // ~5 minutes
    });

    it('handles HTML in reading time calculation', function () {
        $html_content = '<p>' . str_repeat('word ', 200) . '</p><h2>Heading</h2><p>' . str_repeat('word ', 200) . '</p>';
        
        // Should strip HTML tags and count only words
        $reading_time = oltreblocksy_get_reading_time($html_content);
        expect($reading_time)->toBe(2); // ~400 words / 200 WPM = 2 minutes
    });

    it('gets optimized image attributes', function () {
        // Mock WordPress functions
        Monkey\Functions\when('wp_get_attachment_image_src')
            ->with(123, 'large')
            ->justReturn(['https://example.com/image.jpg', 800, 600]);
        
        Monkey\Functions\when('get_post_meta')
            ->with(123, '_wp_attachment_image_alt', true)
            ->justReturn('Test image');
        
        Monkey\Functions\when('wp_get_attachment_image_srcset')
            ->with(123, 'large')
            ->justReturn('https://example.com/image-400.jpg 400w, https://example.com/image-800.jpg 800w');
        
        Monkey\Functions\when('wp_get_attachment_image_sizes')
            ->with(123, 'large')
            ->justReturn('(max-width: 800px) 100vw, 800px');
        
        $attrs = oltreblocksy_get_optimized_image_attrs(123, 'large');
        
        expect($attrs)->toHaveKey('src');
        expect($attrs['src'])->toBe('https://example.com/image.jpg');
        expect($attrs)->toHaveKey('width');
        expect($attrs['width'])->toBe(800);
        expect($attrs)->toHaveKey('height');
        expect($attrs['height'])->toBe(600);
        expect($attrs)->toHaveKey('alt');
        expect($attrs['alt'])->toBe('Test image');
        expect($attrs)->toHaveKey('loading');
        expect($attrs['loading'])->toBe('lazy');
        expect($attrs)->toHaveKey('decoding');
        expect($attrs['decoding'])->toBe('async');
        expect($attrs)->toHaveKey('srcset');
        expect($attrs)->toHaveKey('sizes');
    });

    it('returns empty array for non-existent image', function () {
        Monkey\Functions\when('wp_get_attachment_image_src')
            ->with(999, 'large')
            ->justReturn(false);
        
        $attrs = oltreblocksy_get_optimized_image_attrs(999, 'large');
        expect($attrs)->toBe([]);
    });

    it('merges custom attributes correctly', function () {
        Monkey\Functions\when('wp_get_attachment_image_src')
            ->with(123, 'large')
            ->justReturn(['https://example.com/image.jpg', 800, 600]);
        
        Monkey\Functions\when('get_post_meta')
            ->with(123, '_wp_attachment_image_alt', true)
            ->justReturn('Test image');
        
        Monkey\Functions\when('wp_get_attachment_image_srcset')->justReturn(false);
        
        $custom_attrs = ['class' => 'custom-class', 'loading' => 'eager'];
        $attrs = oltreblocksy_get_optimized_image_attrs(123, 'large', $custom_attrs);
        
        expect($attrs)->toHaveKey('class');
        expect($attrs['class'])->toBe('custom-class');
        expect($attrs['loading'])->toBe('eager'); // Should override default 'lazy'
    });

    it('generates critical CSS', function () {
        $critical_css = oltreblocksy_get_critical_css();
        
        expect($critical_css)->toBeString();
        expect($critical_css)->toContain(':root');
        expect($critical_css)->toContain('--color-primary');
        expect($critical_css)->toContain('--font-system');
        expect($critical_css)->toContain('box-sizing:border-box');
        expect($critical_css)->toContain('.container');
        expect($critical_css)->toContain('.sr-only');
    });

    it('can be filtered', function () {
        // Mock apply_filters
        Monkey\Functions\when('apply_filters')
            ->with('oltreblocksy_critical_css', Mockery::type('string'))
            ->justReturn('filtered-css');
        
        $critical_css = oltreblocksy_get_critical_css();
        expect($critical_css)->toBe('filtered-css');
    });

    it('detects asset loading conditions correctly', function () {
        // Mock WordPress conditional functions
        Monkey\Functions\when('is_front_page')->justReturn(true);
        Monkey\Functions\when('is_home')->justReturn(false);
        Monkey\Functions\when('is_archive')->justReturn(false);
        Monkey\Functions\when('is_search')->justReturn(false);
        Monkey\Functions\when('is_singular')->justReturn(false);
        
        expect(oltreblocksy_should_load_asset('homepage'))->toBeTrue();
        expect(oltreblocksy_should_load_asset('blog'))->toBeFalse();
        expect(oltreblocksy_should_load_asset('single'))->toBeFalse();
    });

    it('detects blog asset loading', function () {
        Monkey\Functions\when('is_front_page')->justReturn(false);
        Monkey\Functions\when('is_home')->justReturn(true);
        Monkey\Functions\when('is_archive')->justReturn(false);
        Monkey\Functions\when('is_search')->justReturn(false);
        
        expect(oltreblocksy_should_load_asset('blog'))->toBeTrue();
        expect(oltreblocksy_should_load_asset('homepage'))->toBeFalse();
    });

    it('detects comments asset loading', function () {
        Monkey\Functions\when('is_singular')->justReturn(true);
        Monkey\Functions\when('comments_open')->justReturn(true);
        Monkey\Functions\when('get_option')->with('thread_comments')->justReturn(1);
        
        expect(oltreblocksy_should_load_asset('comments'))->toBeTrue();
        
        Monkey\Functions\when('comments_open')->justReturn(false);
        expect(oltreblocksy_should_load_asset('comments'))->toBeFalse();
    });

    it('gets performance metrics', function () {
        // Mock server variables
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true) - 0.1; // 0.1 seconds ago
        
        // Mock get_num_queries
        Monkey\Functions\when('get_num_queries')->justReturn(25);
        
        $metrics = oltreblocksy_get_performance_metrics();
        
        expect($metrics)->toHaveKey('memory_usage');
        expect($metrics)->toHaveKey('peak_memory');
        expect($metrics)->toHaveKey('execution_time');
        expect($metrics)->toHaveKey('queries_count');
        
        expect($metrics['memory_usage'])->toBeInt();
        expect($metrics['peak_memory'])->toBeInt();
        expect($metrics['execution_time'])->toBeFloat();
        expect($metrics['queries_count'])->toBe(25);
    });

    it('can filter performance metrics', function () {
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
        
        Monkey\Functions\when('get_num_queries')->justReturn(10);
        Monkey\Functions\when('apply_filters')
            ->with('oltreblocksy_performance_metrics', Mockery::type('array'))
            ->justReturn(['custom_metric' => 'custom_value']);
        
        $metrics = oltreblocksy_get_performance_metrics();
        expect($metrics)->toBe(['custom_metric' => 'custom_value']);
    });

    it('logs performance data in debug mode', function () {
        // Mock constants and functions
        if (!defined('WP_DEBUG')) {
            define('WP_DEBUG', true);
        }
        
        Monkey\Functions\when('wp_json_encode')->alias('json_encode');
        
        $test_data = ['test' => 'data'];
        
        // Should not throw an exception
        expect(fn() => oltreblocksy_log_performance($test_data))->not->toThrow(Exception::class);
    });

    it('provides correct breakpoint values', function () {
        expect(oltreblocksy_get_breakpoint('sm'))->toBe('640px');
        expect(oltreblocksy_get_breakpoint('md'))->toBe('768px');
        expect(oltreblocksy_get_breakpoint('lg'))->toBe('1024px');
        expect(oltreblocksy_get_breakpoint('xl'))->toBe('1280px');
        expect(oltreblocksy_get_breakpoint('2xl'))->toBe('1536px');
        expect(oltreblocksy_get_breakpoint('invalid'))->toBe('');
    });

    it('detects reduced motion preference', function () {
        // Test with cookie set
        $_COOKIE['prefers-reduced-motion'] = 'reduce';
        expect(oltreblocksy_prefers_reduced_motion())->toBeTrue();
        
        // Test with cookie not set
        unset($_COOKIE['prefers-reduced-motion']);
        expect(oltreblocksy_prefers_reduced_motion())->toBeFalse();
        
        // Test with different cookie value
        $_COOKIE['prefers-reduced-motion'] = 'no-preference';
        expect(oltreblocksy_prefers_reduced_motion())->toBeFalse();
    });

    it('generates unique IDs', function () {
        // Mock wp_rand
        Monkey\Functions\when('wp_rand')->justReturn(1234);
        
        $id1 = oltreblocksy_generate_id();
        $id2 = oltreblocksy_generate_id('custom');
        
        expect($id1)->toBeString();
        expect($id2)->toBeString();
        expect($id1)->toStartWith('oltreblocksy-');
        expect($id2)->toStartWith('custom-');
        expect($id1)->not->toBe($id2);
    });

    it('minifies CSS correctly', function () {
        $css = "
            /* This is a comment */
            .test-class {
                color: red;
                background: blue;
                margin: 10px;
            }
            
            .another-class {
                padding: 5px;;
            }
        ";
        
        $minified = oltreblocksy_minify_css($css);
        
        expect($minified)->not->toContain('/*');
        expect($minified)->not->toContain("\n");
        expect($minified)->not->toContain("\t");
        expect($minified)->not->toContain('  ');
        expect($minified)->not->toContain(';}');
        expect($minified)->toContain('.test-class{');
    });

    it('gets theme mod with fallback', function () {
        Monkey\Functions\when('get_theme_mod')
            ->with('test_setting', 'default_value')
            ->justReturn('actual_value');
        
        $result = oltreblocksy_get_theme_mod('test_setting', 'default_value');
        expect($result)->toBe('actual_value');
    });

    it('detects dark mode correctly', function () {
        // Test enabled mode
        Monkey\Functions\when('get_theme_mod')
            ->with('dark_mode_setting', 'auto')
            ->justReturn('enabled');
        
        expect(oltreblocksy_is_dark_mode())->toBeTrue();
        
        // Test disabled mode
        Monkey\Functions\when('get_theme_mod')
            ->with('dark_mode_setting', 'auto')
            ->justReturn('disabled');
        
        expect(oltreblocksy_is_dark_mode())->toBeFalse();
        
        // Test auto mode with cookie
        Monkey\Functions\when('get_theme_mod')
            ->with('dark_mode_setting', 'auto')
            ->justReturn('auto');
        
        $_COOKIE['dark-mode'] = 'true';
        expect(oltreblocksy_is_dark_mode())->toBeTrue();
        
        $_COOKIE['dark-mode'] = 'false';
        expect(oltreblocksy_is_dark_mode())->toBeFalse();
        
        unset($_COOKIE['dark-mode']);
        expect(oltreblocksy_is_dark_mode())->toBeFalse();
    });

});