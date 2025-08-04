<?php

use OltreBlocksy\Modules\Performance;
use OltreBlocksy\Modules\Typography;
use OltreBlocksy\Modules\ColorSystem;
use OltreBlocksy\Modules\Accessibility;
use OltreBlocksy\Modules\Customizer;
use Brain\Monkey;

/**
 * Test module integration and interaction
 */
describe('Module Integration', function () {

    beforeEach(function () {
        // Mock WordPress functions commonly used across modules
        Monkey\Functions\when('add_action')->justReturn(true);
        Monkey\Functions\when('add_filter')->justReturn(true);
        Monkey\Functions\when('get_theme_mod')->justReturn(null);
        Monkey\Functions\when('set_theme_mod')->justReturn(true);
        Monkey\Functions\when('apply_filters')->returnArg();
        Monkey\Functions\when('__')->returnArg();
        Monkey\Functions\when('esc_html__')->returnArg();
        Monkey\Functions\when('esc_attr')->returnArg();
        Monkey\Functions\when('esc_url')->returnArg();
    });

    it('can load all modules together', function () {
        $performance = new Performance();
        $typography = new Typography();
        $colorSystem = new ColorSystem();
        $accessibility = new Accessibility();
        $customizer = new Customizer();
        
        expect($performance)->toBeInstanceOf(Performance::class);
        expect($typography)->toBeInstanceOf(Typography::class);
        expect($colorSystem)->toBeInstanceOf(ColorSystem::class);
        expect($accessibility)->toBeInstanceOf(Accessibility::class);
        expect($customizer)->toBeInstanceOf(Customizer::class);
    });

    it('modules can interact through WordPress hooks', function () {
        // Test that modules can register hooks without conflicts
        $modules = [
            new Performance(),
            new Typography(),
            new ColorSystem(),
            new Accessibility(),
            new Customizer()
        ];
        
        foreach ($modules as $module) {
            expect($module->is_enabled())->toBeTrue();
        }
    });

    it('color system and accessibility modules work together', function () {
        $colorSystem = new ColorSystem();
        $accessibility = new Accessibility();
        
        // Test contrast checking functionality
        $contrast_ratio = $colorSystem->check_contrast_ratio('#000000', '#ffffff');
        expect($contrast_ratio)->toBeGreaterThan(7); // WCAG AAA compliance
        
        // Colors should be accessible
        $contrast_ratio_fail = $colorSystem->check_contrast_ratio('#ffff00', '#ffffff');
        expect($contrast_ratio_fail)->toBeLessThan(4.5); // Should fail WCAG AA
    });

    it('performance and typography modules integrate CSS generation', function () {
        // Mock functions needed for typography
        Monkey\Functions\when('oltreblocksy_minify_css')->alias(function($css) {
            return preg_replace('/\s+/', '', $css);
        });
        
        $performance = new Performance();
        $typography = new Typography();
        
        // Both modules should be able to generate and minify CSS
        expect($performance)->toHaveMethod('optimize_style_loading');
        expect($typography)->toHaveMethod('output_typography_css');
    });

    it('customizer integrates with all other modules', function () {
        $customizer = new Customizer();
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Mock customizer registration calls
        $wp_customize->shouldReceive('remove_section')->atLeast(1);
        $wp_customize->shouldReceive('add_panel')->atLeast(1);
        $wp_customize->shouldReceive('add_section')->atLeast(1);
        $wp_customize->shouldReceive('add_setting')->atLeast(1);
        $wp_customize->shouldReceive('add_control')->atLeast(1);
        
        // Should not throw exceptions when registering
        expect(fn() => $customizer->register_customizer($wp_customize))->not->toThrow(Exception::class);
    });

    it('modules share common base functionality', function () {
        $modules = [
            new Performance(),
            new Typography(),
            new ColorSystem(),
            new Accessibility(),
            new Customizer()
        ];
        
        foreach ($modules as $module) {
            // All modules should inherit from Base_Module
            expect($module)->toHaveMethod('is_enabled');
            expect($module)->toHaveMethod('get_setting');
            expect($module)->toHaveMethod('set_setting');
            expect($module)->toHaveMethod('get_info');
        }
    });

    it('modules can be enabled and disabled independently', function () {
        $performance = new Performance();
        $typography = new Typography();
        
        // Both should start enabled
        expect($performance->is_enabled())->toBeTrue();
        expect($typography->is_enabled())->toBeTrue();
        
        // Can disable one without affecting the other
        $performance->disable();
        expect($performance->is_enabled())->toBeFalse();
        expect($typography->is_enabled())->toBeTrue();
        
        // Can re-enable
        $performance->enable();
        expect($performance->is_enabled())->toBeTrue();
    });

    it('modules handle WordPress hooks correctly', function () {
        // Mock WordPress hook system
        $hooks_called = [];
        
        Monkey\Functions\when('add_action')->alias(function($hook, $callback) use (&$hooks_called) {
            $hooks_called[] = $hook;
            return true;
        });
        
        // Initialize modules
        $performance = new Performance();
        $typography = new Typography();
        
        // Check that hooks were registered
        expect($hooks_called)->toContain('wp_enqueue_scripts');
        expect($hooks_called)->toContain('wp_head');
    });

    it('modules provide consistent customizer integration', function () {
        $modules = [
            new Performance(),
            new Typography(),
            new ColorSystem(),
            new Accessibility(),
            new Customizer()
        ];
        
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        $wp_customize->shouldReceive('add_section')->atLeast(0);
        $wp_customize->shouldReceive('add_setting')->atLeast(0);
        $wp_customize->shouldReceive('add_control')->atLeast(0);
        $wp_customize->shouldReceive('remove_section')->atLeast(0);
        $wp_customize->shouldReceive('add_panel')->atLeast(0);
        $wp_customize->shouldReceive('get_section')->andReturn(null);
        
        foreach ($modules as $module) {
            // All modules should handle customize_register without errors
            expect(fn() => $module->customize_register($wp_customize))->not->toThrow(Exception::class);
        }
    });

    it('modules work with theme options system', function () {
        $performance = new Performance();
        $typography = new Typography();
        
        // Modules should be able to save and retrieve settings
        $performance->set_setting('test_performance_setting', 'performance_value');
        $typography->set_setting('test_typography_setting', 'typography_value');
        
        expect($performance->get_setting('test_performance_setting'))->toBe('performance_value');
        expect($typography->get_setting('test_typography_setting'))->toBe('typography_value');
        
        // Settings should be independent
        expect($performance->get_setting('test_typography_setting'))->toBeNull();
        expect($typography->get_setting('test_performance_setting'))->toBeNull();
    });

    it('handles WordPress environment requirements', function () {
        // Test that modules can work in different WordPress contexts
        Monkey\Functions\when('is_admin')->justReturn(false);
        Monkey\Functions\when('is_customizer_preview')->justReturn(false);
        
        $modules = [
            new Performance(),
            new Typography(),
            new ColorSystem(),
            new Accessibility()
        ];
        
        foreach ($modules as $module) {
            expect($module->get_info())->toBeArray();
            expect($module->get_info())->toHaveKey('name');
            expect($module->get_info())->toHaveKey('enabled');
        }
    });

    it('modules handle AJAX requests appropriately', function () {
        // Mock AJAX environment
        Monkey\Functions\when('check_ajax_referer')->justReturn(true);
        Monkey\Functions\when('wp_send_json_success')->justReturn(true);
        Monkey\Functions\when('wp_send_json_error')->justReturn(true);
        
        $colorSystem = new ColorSystem();
        
        // Mock $_POST data for color system AJAX
        $_POST['base_color'] = '#ff0000';
        $_POST['harmony_type'] = 'complementary';
        $_POST['nonce'] = 'test_nonce';
        
        expect(fn() => $colorSystem->ajax_generate_palette())->not->toThrow(Exception::class);
    });

    it('provides consistent error handling across modules', function () {
        $modules = [
            new Performance(),
            new Typography(),
            new ColorSystem(),
            new Accessibility(),
            new Customizer()
        ];
        
        foreach ($modules as $module) {
            // Modules should handle activation/deactivation gracefully
            expect(fn() => $module->activate())->not->toThrow(Exception::class);
            expect(fn() => $module->deactivate())->not->toThrow(Exception::class);
        }
    });

    it('supports theme filters and actions', function () {
        $applied_filters = [];
        
        Monkey\Functions\when('apply_filters')->alias(function($filter, $value) use (&$applied_filters) {
            $applied_filters[] = $filter;
            return $value;
        });
        
        // Initialize modules that should apply filters
        $typography = new Typography();
        $colorSystem = new ColorSystem();
        
        // Should apply theme-specific filters
        expect($applied_filters)->toContain('oltreblocksy_typography_presets');
        expect($applied_filters)->toContain('oltreblocksy_color_palettes');
    });

});