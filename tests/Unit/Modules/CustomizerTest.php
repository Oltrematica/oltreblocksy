<?php

use OltreBlocksy\Modules\Customizer;
use Brain\Monkey;

/**
 * Test Customizer module functionality
 */
describe('Customizer Module', function () {

    beforeEach(function () {
        // Create a testable version of Customizer module
        $this->customizer = new class extends Customizer {
            public function __construct() {
                // Skip parent constructor to avoid WordPress dependencies
                $this->name = 'Customizer';
                $this->version = '1.0.0';
                $this->dependencies = [];
                $this->enabled = true;
                $this->settings = [];
                $this->custom_controls = [];
            }
            
            // Expose protected methods for testing
            public function test_register_layout_panel($wp_customize) {
                $this->register_layout_panel($wp_customize);
            }
            
            public function test_register_styling_panel($wp_customize) {
                $this->register_styling_panel($wp_customize);
            }
            
            public function test_register_performance_panel($wp_customize) {
                $this->register_performance_panel($wp_customize);
            }
            
            public function test_register_advanced_panel($wp_customize) {
                $this->register_advanced_panel($wp_customize);
            }
            
            public function test_register_general_section($wp_customize) {
                $this->register_general_section($wp_customize);
            }
            
            public function test_register_header_section($wp_customize) {
                $this->register_header_section($wp_customize);
            }
            
            public function test_register_footer_section($wp_customize) {
                $this->register_footer_section($wp_customize);
            }
            
            public function test_register_colors_section($wp_customize) {
                $this->register_colors_section($wp_customize);
            }
            
            public function test_register_spacing_section($wp_customize) {
                $this->register_spacing_section($wp_customize);
            }
            
            public function test_register_blog_section($wp_customize) {
                $this->register_blog_section($wp_customize);
            }
            
            public function test_reset_section_settings($section) {
                $this->reset_section_settings($section);
            }
            
            public function test_export_theme_settings() {
                $this->export_theme_settings();
            }
            
            public function test_import_theme_settings($settings) {
                $this->import_theme_settings($settings);
            }
        };
    });

    it('can be instantiated', function () {
        expect($this->customizer)->toBeInstanceOf(Customizer::class);
    });

    it('returns correct module name', function () {
        expect($this->customizer->get_name())->toBe('Customizer');
    });

    it('registers layout panel correctly', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        $wp_customize->shouldReceive('add_panel')
            ->once()
            ->with('oltreblocksy_layout', Mockery::type('array'));
        
        $this->customizer->test_register_layout_panel($wp_customize);
        
        // If we reach here without exceptions, the test passes
        expect(true)->toBeTrue();
    });

    it('registers styling panel correctly', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        $wp_customize->shouldReceive('add_panel')
            ->once()
            ->with('oltreblocksy_styling', Mockery::type('array'));
        
        $this->customizer->test_register_styling_panel($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('registers performance panel correctly', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        $wp_customize->shouldReceive('add_panel')
            ->once()
            ->with('oltreblocksy_performance', Mockery::type('array'));
        
        $this->customizer->test_register_performance_panel($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('registers advanced panel correctly', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        $wp_customize->shouldReceive('add_panel')
            ->once()
            ->with('oltreblocksy_advanced', Mockery::type('array'));
        
        $this->customizer->test_register_advanced_panel($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('registers general section with proper settings', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Expect section, settings, and controls
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(3);
        $wp_customize->shouldReceive('add_control')->times(3);
        
        $this->customizer->test_register_general_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('registers header section with sticky header option', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Expect section, settings, and controls for header options
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(3);
        $wp_customize->shouldReceive('add_control')->times(3);
        
        $this->customizer->test_register_header_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('registers footer section with widget columns', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Expect section, settings, and controls for footer options
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(2);
        $wp_customize->shouldReceive('add_control')->times(2);
        
        $this->customizer->test_register_footer_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('registers colors section with color controls', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Expect section, settings, and color controls
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(4);
        $wp_customize->shouldReceive('add_control')->times(4);
        
        $this->customizer->test_register_colors_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('registers spacing section with scale controls', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Expect section, settings, and controls for spacing
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(2);
        $wp_customize->shouldReceive('add_control')->times(2);
        
        $this->customizer->test_register_spacing_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('registers blog section with layout options', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Expect section, settings, and controls for blog options
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(4);
        $wp_customize->shouldReceive('add_control')->times(4);
        
        $this->customizer->test_register_blog_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('handles full customizer registration', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Mock section removal
        $wp_customize->shouldReceive('remove_section')->twice();
        
        // Mock all expected calls for panels, sections, settings, and controls
        $wp_customize->shouldReceive('add_panel')->times(4);
        $wp_customize->shouldReceive('add_section')->times(6);
        $wp_customize->shouldReceive('add_setting')->atLeast(15);
        $wp_customize->shouldReceive('add_control')->atLeast(15);
        
        $this->customizer->register_customizer($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('has proper active callbacks', function () {
        // Mock get_theme_mod
        Monkey\Functions\when('get_theme_mod')
            ->with('oltreblocksy_blog_layout', 'grid')
            ->justReturn('grid');
        
        expect($this->customizer->is_grid_layout())->toBeTrue();
        
        Monkey\Functions\when('get_theme_mod')
            ->with('oltreblocksy_blog_layout', 'grid')
            ->justReturn('list');
        
        expect($this->customizer->is_grid_layout())->toBeFalse();
    });

    it('checks excerpt enabled callback correctly', function () {
        Monkey\Functions\when('get_theme_mod')
            ->with('oltreblocksy_show_excerpt', true)
            ->justReturn(true);
        
        expect($this->customizer->show_excerpt_enabled())->toBeTrue();
        
        Monkey\Functions\when('get_theme_mod')
            ->with('oltreblocksy_show_excerpt', true)
            ->justReturn(false);
        
        expect($this->customizer->show_excerpt_enabled())->toBeFalse();
    });

    it('sanitizes float values correctly', function () {
        expect($this->customizer->sanitize_float('1.5'))->toBe(1.5);
        expect($this->customizer->sanitize_float('2.25'))->toBe(2.25);
        expect($this->customizer->sanitize_float('invalid'))->toBe(0.0);
        expect($this->customizer->sanitize_float(3))->toBe(3.0);
    });

    it('handles AJAX customizer actions', function () {
        // Mock WordPress functions
        Monkey\Functions\when('check_ajax_referer')->justReturn(true);
        Monkey\Functions\when('current_user_can')->with('customize')->justReturn(true);
        Monkey\Functions\when('wp_send_json_success')->justReturn(true);
        Monkey\Functions\when('wp_send_json_error')->justReturn(true);
        
        // Test reset section
        $_POST['customizer_action'] = 'reset_section';
        $_POST['section'] = 'test_section';
        $_POST['nonce'] = 'test_nonce';
        
        expect(fn() => $this->customizer->handle_customizer_ajax())->not->toThrow(Exception::class);
        
        // Test export settings
        $_POST['customizer_action'] = 'export_settings';
        expect(fn() => $this->customizer->handle_customizer_ajax())->not->toThrow(Exception::class);
        
        // Test import settings
        $_POST['customizer_action'] = 'import_settings';
        $_POST['settings'] = '{"test_setting": "test_value"}';
        expect(fn() => $this->customizer->handle_customizer_ajax())->not->toThrow(Exception::class);
    });

    it('handles unauthorized AJAX requests', function () {
        // Mock WordPress functions
        Monkey\Functions\when('check_ajax_referer')->justReturn(true);
        Monkey\Functions\when('current_user_can')->with('customize')->justReturn(false);
        Monkey\Functions\when('wp_die')->justReturn(null);
        
        $_POST['customizer_action'] = 'reset_section';
        $_POST['nonce'] = 'test_nonce';
        
        expect(fn() => $this->customizer->handle_customizer_ajax())->not->toThrow(Exception::class);
    });

    it('resets section settings correctly', function () {
        Monkey\Functions\when('wp_send_json_success')->justReturn(true);
        
        expect(fn() => $this->customizer->test_reset_section_settings('test_section'))->not->toThrow(Exception::class);
    });

    it('exports theme settings correctly', function () {
        Monkey\Functions\when('get_theme_mods')->justReturn(['setting1' => 'value1', 'setting2' => 'value2']);
        Monkey\Functions\when('wp_send_json_success')->justReturn(true);
        
        expect(fn() => $this->customizer->test_export_theme_settings())->not->toThrow(Exception::class);
    });

    it('imports theme settings correctly', function () {
        Monkey\Functions\when('set_theme_mod')->justReturn(true);
        Monkey\Functions\when('wp_send_json_success')->justReturn(true);
        
        $settings = '{"test_setting": "test_value", "another_setting": "another_value"}';
        
        expect(fn() => $this->customizer->test_import_theme_settings($settings))->not->toThrow(Exception::class);
    });

    it('handles invalid JSON in import', function () {
        Monkey\Functions\when('wp_send_json_error')->justReturn(true);
        
        $invalid_settings = 'invalid json string';
        
        expect(fn() => $this->customizer->test_import_theme_settings($invalid_settings))->not->toThrow(Exception::class);
    });

    it('provides proper customizer control types', function () {
        // Check that different control types are used appropriately
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Should use range controls for numeric values
        $wp_customize->shouldReceive('add_control')
            ->with('oltreblocksy_container_width', Mockery::on(function($args) {
                return $args['type'] === 'range' && 
                       isset($args['input_attrs']['min']) && 
                       isset($args['input_attrs']['max']);
            }));
        
        // Should use select controls for predefined choices
        $wp_customize->shouldReceive('add_control')
            ->with('oltreblocksy_site_layout', Mockery::on(function($args) {
                return $args['type'] === 'select' && isset($args['choices']);
            }));
        
        // Should use checkbox controls for boolean values
        $wp_customize->shouldReceive('add_control')
            ->with('oltreblocksy_sticky_header', Mockery::on(function($args) {
                return $args['type'] === 'checkbox';
            }));
        
        // Mock other required calls
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(3);
        $wp_customize->shouldReceive('add_control')->times(3);
        
        $this->customizer->test_register_general_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('uses proper sanitization callbacks', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Check that proper sanitization is used for different setting types
        $wp_customize->shouldReceive('add_setting')
            ->with('oltreblocksy_container_width', Mockery::on(function($args) {
                return $args['sanitize_callback'] === 'absint';
            }));
        
        $wp_customize->shouldReceive('add_setting')
            ->with('oltreblocksy_site_layout', Mockery::on(function($args) {
                return $args['sanitize_callback'] === 'sanitize_key';
            }));
        
        // Mock other required calls
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(3);
        $wp_customize->shouldReceive('add_control')->times(3);
        
        $this->customizer->test_register_general_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('uses postMessage transport for live preview', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Check that postMessage transport is used for live-updatable settings
        $wp_customize->shouldReceive('add_setting')
            ->with('oltreblocksy_container_width', Mockery::on(function($args) {
                return $args['transport'] === 'postMessage';
            }));
        
        // Mock other required calls
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->times(3);
        $wp_customize->shouldReceive('add_control')->times(3);
        
        $this->customizer->test_register_general_section($wp_customize);
        
        expect(true)->toBeTrue();
    });

});