<?php

use OltreBlocksy\Modules\Performance;
use Brain\Monkey;

/**
 * Test performance metrics and optimizations
 */
describe('Performance Metrics', function () {

    beforeEach(function () {
        // Mock WordPress functions
        Monkey\Functions\when('add_action')->justReturn(true);
        Monkey\Functions\when('add_filter')->justReturn(true);
        Monkey\Functions\when('get_theme_mod')->justReturn(null);
        Monkey\Functions\when('apply_filters')->returnArg();
        Monkey\Functions\when('__')->returnArg();
    });

    it('measures CSS minification performance', function () {
        $performance = new Performance();
        
        // Generate a large CSS string for testing
        $large_css = '';
        for ($i = 0; $i < 1000; $i++) {
            $large_css .= "
                .test-class-{$i} {
                    color: #ff0000;
                    background: #ffffff;
                    margin: 10px;
                    padding: 5px;
                }
                /* Comment for class {$i} */
            ";
        }
        
        $start_time = microtime(true);
        $minified = oltreblocksy_minify_css($large_css);
        $end_time = microtime(true);
        
        $execution_time = ($end_time - $start_time) * 1000; // Convert to milliseconds
        
        // Minification should complete in reasonable time (less than 100ms for this test)
        expect($execution_time)->toBeLessThan(100);
        
        // Result should be significantly smaller
        expect(strlen($minified))->toBeLessThan(strlen($large_css) * 0.7);
        
        // Should remove comments and whitespace
        expect($minified)->not->toContain('/*');
        expect($minified)->not->toContain("\n");
    });

    it('measures script optimization performance', function () {
        $performance = new Performance();
        
        // Test multiple script optimization calls
        $script_tags = [];
        for ($i = 0; $i < 100; $i++) {
            $script_tags[] = "<script src='/path/to/script-{$i}.js'></script>";
        }
        
        $start_time = microtime(true);
        
        foreach ($script_tags as $index => $tag) {
            $handle = $index % 3 === 0 ? 'oltreblocksy-main' : 'other-script';
            $performance->optimize_script_loading($tag, $handle, '/path/to/script.js');
        }
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000;
        
        // Should process 100 scripts quickly (less than 50ms)
        expect($execution_time)->toBeLessThan(50);
    });

    it('measures color contrast calculation performance', function () {
        $colorSystem = new OltreBlocksy\Modules\ColorSystem();
        
        // Test multiple contrast calculations
        $colors = [
            '#ff0000', '#00ff00', '#0000ff', '#ffffff', '#000000',
            '#ffff00', '#ff00ff', '#00ffff', '#808080', '#c0c0c0'
        ];
        
        $start_time = microtime(true);
        
        // Calculate contrast for all color combinations
        foreach ($colors as $color1) {
            foreach ($colors as $color2) {
                $colorSystem->check_contrast_ratio($color1, $color2);
            }
        }
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000;
        
        // Should calculate 100 contrast ratios quickly (less than 100ms)
        expect($execution_time)->toBeLessThan(100);
    });

    it('measures typography CSS generation performance', function () {
        $typography = new OltreBlocksy\Modules\Typography();
        
        // Mock minify function
        Monkey\Functions\when('oltreblocksy_minify_css')->alias(function($css) {
            return preg_replace('/\s+/', '', $css);
        });
        
        $preset = [
            'heading_font' => [
                'family' => 'Inter',
                'category' => 'sans-serif'
            ],
            'body_font' => [
                'family' => 'Inter',
                'category' => 'sans-serif'
            ],
            'font_scale' => 1.25,
            'line_height_scale' => 1.6,
            'letter_spacing' => [
                'headings' => '-0.02em',
                'body' => '0'
            ]
        ];
        
        $start_time = microtime(true);
        
        // Generate CSS multiple times
        for ($i = 0; $i < 50; $i++) {
            // Use reflection to access protected method
            $reflection = new ReflectionClass($typography);
            $method = $reflection->getMethod('generate_typography_css');
            $method->setAccessible(true);
            $method->invoke($typography, $preset);
        }
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000;
        
        // Should generate CSS quickly (less than 200ms for 50 generations)
        expect($execution_time)->toBeLessThan(200);
    });

    it('measures memory usage during module initialization', function () {
        $initial_memory = memory_get_usage();
        
        // Initialize all modules
        $modules = [
            new Performance(),
            new OltreBlocksy\Modules\Typography(),
            new OltreBlocksy\Modules\ColorSystem(),
            new OltreBlocksy\Modules\Accessibility(),
            new OltreBlocksy\Modules\Customizer()
        ];
        
        $final_memory = memory_get_usage();
        $memory_used = $final_memory - $initial_memory;
        
        // Memory usage should be reasonable (less than 5MB for all modules)
        expect($memory_used)->toBeLessThan(5 * 1024 * 1024);
        
        // Cleanup
        unset($modules);
    });

    it('measures palette generation performance', function () {
        $colorSystem = new OltreBlocksy\Modules\ColorSystem();
        
        $base_colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff'];
        $harmony_types = ['complementary', 'triadic', 'analogous', 'split_complementary'];
        
        $start_time = microtime(true);
        
        // Generate multiple palettes
        foreach ($base_colors as $color) {
            foreach ($harmony_types as $harmony) {
                $colorSystem->generate_palette($color, $harmony);
            }
        }
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000;
        
        // Should generate 20 palettes quickly (less than 50ms)  
        expect($execution_time)->toBeLessThan(50);
    });

    it('measures helper function performance', function () {
        // Test reading time calculation with large content
        $large_content = str_repeat('word ', 10000); // 10,000 words
        
        $start_time = microtime(true);
        $reading_time = oltreblocksy_get_reading_time($large_content);
        $end_time = microtime(true);
        
        $execution_time = ($end_time - $start_time) * 1000;
        
        // Should calculate reading time quickly (less than 10ms)
        expect($execution_time)->toBeLessThan(10);
        expect($reading_time)->toBe(50); // 10,000 words / 200 WPM = 50 minutes
    });

    it('measures SVG icon generation performance', function () {
        $icons = ['arrow-right', 'arrow-left', 'search', 'menu', 'close', 'chevron-down', 'external-link'];
        $sizes = [16, 24, 32, 48, 64];
        
        $start_time = microtime(true);
        
        // Generate all icon variations
        foreach ($icons as $icon) {
            foreach ($sizes as $size) {
                oltreblocksy_get_svg_icon($icon, $size);
            }
        }
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000;
        
        // Should generate 35 icon variations quickly (less than 20ms)
        expect($execution_time)->toBeLessThan(20);
    });

    it('measures critical CSS generation performance', function () {
        // Mock apply_filters to return unmodified CSS
        Monkey\Functions\when('apply_filters')
            ->with('oltreblocksy_critical_css', Mockery::type('string'))
            ->returnArg();
        
        $start_time = microtime(true);
        
        // Generate critical CSS multiple times
        for ($i = 0; $i < 100; $i++) {
            oltreblocksy_get_critical_css();
        }
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000;
        
        // Should generate critical CSS quickly (less than 100ms for 100 generations)
        expect($execution_time)->toBeLessThan(100);
    });

    it('tests overall theme performance metrics', function () {
        // Mock WordPress functions for performance metrics
        $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true) - 0.5; // 0.5 seconds ago
        Monkey\Functions\when('get_num_queries')->justReturn(15);
        Monkey\Functions\when('apply_filters')->returnArg();
        
        $start_time = microtime(true);
        
        $metrics = oltreblocksy_get_performance_metrics();
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000;
        
        // Performance metrics collection should be fast (less than 5ms)
        expect($execution_time)->toBeLessThan(5);
        
        // Metrics should contain expected data
        expect($metrics)->toHaveKey('memory_usage');
        expect($metrics)->toHaveKey('peak_memory');
        expect($metrics)->toHaveKey('execution_time');
        expect($metrics)->toHaveKey('queries_count');
        
        expect($metrics['queries_count'])->toBe(15);
        expect($metrics['execution_time'])->toBeGreaterThan(0.4); // At least 0.4 seconds
    });

    it('benchmarks different CSS minification approaches', function () {
        $test_css = str_repeat("
            .test-class {
                color: red;
                background: blue;
                margin: 10px;
            }
            /* This is a comment */
        ", 500);
        
        // Method 1: Current minification
        $start1 = microtime(true);
        $result1 = oltreblocksy_minify_css($test_css);
        $end1 = microtime(true);
        $time1 = ($end1 - $start1) * 1000;
        
        // Method 2: Simple regex approach
        $start2 = microtime(true);
        $result2 = preg_replace('/\s+/', ' ', preg_replace('/\/\*.*?\*\//', '', $test_css));
        $end2 = microtime(true);
        $time2 = ($end2 - $start2) * 1000;
        
        // Both should complete quickly
        expect($time1)->toBeLessThan(100);
        expect($time2)->toBeLessThan(100);
        
        // Current method should produce smaller output
        expect(strlen($result1))->toBeLessThanOrEqualTo(strlen($result2));
    });

});