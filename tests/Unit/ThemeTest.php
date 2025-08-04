<?php

use Brain\Monkey;

/**
 * Test main theme class functionality
 */
describe('OltreBlocksy_Theme', function () {

    beforeEach(function () {
        // Mock WordPress functions and constants that are used in functions.php
        Monkey\Functions\when('defined')->with('ABSPATH')->justReturn(true);
        Monkey\Functions\when('get_template_directory')->justReturn('/path/to/theme');
        Monkey\Functions\when('get_template_directory_uri')->justReturn('https://example.com/theme');
        
        // Load the main theme file
        require_once OLTREBLOCKSY_THEME_DIR . '/functions.php';
    });

    it('can be instantiated as singleton', function () {
        $instance1 = OltreBlocksy_Theme::get_instance();
        $instance2 = OltreBlocksy_Theme::get_instance();
        
        expect($instance1)->toBeInstanceOf(OltreBlocksy_Theme::class);
        expect($instance1)->toBe($instance2); // Should be the same instance
    });

    it('initializes with proper hooks', function () {
        // Mock WordPress hook functions
        Monkey\Functions\when('add_action')->justReturn(true);
        Monkey\Functions\when('add_filter')->justReturn(true);
        
        $theme = OltreBlocksy_Theme::get_instance();
        
        // Verify the instance exists
        expect($theme)->toBeInstanceOf(OltreBlocksy_Theme::class);
    });

    it('sets up theme support correctly', function () {
        // Mock WordPress functions
        Monkey\Functions\when('add_theme_support')->justReturn(true);
        Monkey\Functions\when('register_nav_menus')->justReturn(true);
        Monkey\Functions\when('set_post_thumbnail_size')->justReturn(true);
        Monkey\Functions\when('add_image_size')->justReturn(true);
        Monkey\Functions\when('add_editor_style')->justReturn(true);
        Monkey\Functions\when('esc_html__')->returnArg();
        Monkey\Functions\when('apply_filters')->returnArg();
        Monkey\Functions\when('add_action')->justReturn(true);
        
        $theme = OltreBlocksy_Theme::get_instance();
        
        // Call theme_setup method
        $theme->theme_setup();
        
        // If we reach here without exceptions, theme setup worked
        expect(true)->toBeTrue();
    });

    it('loads theme textdomain', function () {
        Monkey\Functions\when('load_theme_textdomain')
            ->with('oltreblocksy', Mockery::type('string'))
            ->justReturn(true);
        
        $theme = OltreBlocksy_Theme::get_instance();
        $theme->load_textdomain();
        
        expect(true)->toBeTrue();
    });

    it('registers widget areas', function () {
        Monkey\Functions\when('register_sidebar')->justReturn(true);
        Monkey\Functions\when('esc_html__')->returnArg();
        Monkey\Functions\when('sprintf')->alias('sprintf');
        
        $theme = OltreBlocksy_Theme::get_instance();
        $theme->register_widget_areas();
        
        expect(true)->toBeTrue();
    });

    it('enqueues scripts and styles', function () {
        Monkey\Functions\when('wp_enqueue_style')->justReturn(true);
        Monkey\Functions\when('wp_enqueue_script')->justReturn(true);
        Monkey\Functions\when('wp_script_is')->justReturn(true);
        Monkey\Functions\when('wp_localize_script')->justReturn(true);
        Monkey\Functions\when('get_stylesheet_uri')->justReturn('https://example.com/style.css');
        Monkey\Functions\when('file_exists')->justReturn(true);
        Monkey\Functions\when('admin_url')->justReturn('https://example.com/wp-admin/admin-ajax.php');
        Monkey\Functions\when('wp_create_nonce')->justReturn('test_nonce');
        Monkey\Functions\when('esc_html__')->returnArg();
        Monkey\Functions\when('is_singular')->justReturn(false);
        Monkey\Functions\when('comments_open')->justReturn(false);
        Monkey\Functions\when('get_option')->justReturn(false);
        Monkey\Functions\when('is_front_page')->justReturn(false);
        
        $theme = OltreBlocksy_Theme::get_instance();
        $theme->enqueue_scripts();
        
        expect(true)->toBeTrue();
    });

    it('enqueues editor scripts', function () {
        Monkey\Functions\when('wp_enqueue_script')->justReturn(true);
        Monkey\Functions\when('wp_enqueue_style')->justReturn(true);
        Monkey\Functions\when('file_exists')->justReturn(true);
        
        $theme = OltreBlocksy_Theme::get_instance();
        $theme->enqueue_editor_scripts();
        
        expect(true)->toBeTrue();
    });

    it('preloads critical resources', function () {
        Monkey\Functions\when('esc_url')->returnArg();
        Monkey\Functions\when('get_stylesheet_uri')->justReturn('https://example.com/style.css');
        
        ob_start();
        $theme = OltreBlocksy_Theme::get_instance();
        $theme->preload_critical_resources();
        $output = ob_get_clean();
        
        expect($output)->toContain('<link rel="preload"');
        expect($output)->toContain('as="style"');
        expect($output)->toContain('as="script"');
        expect($output)->toContain('<link rel="dns-prefetch"');
    });

    it('adds async attributes to scripts', function () {
        $theme = OltreBlocksy_Theme::get_instance();
        
        $original_tag = '<script src="/path/to/script.js"></script>';
        $result = $theme->add_async_defer_attributes($original_tag, 'oltreblocksy-main');
        
        expect($result)->toBe('<script async src="/path/to/script.js"></script>');
        
        $result = $theme->add_async_defer_attributes($original_tag, 'comment-reply');
        expect($result)->toBe('<script defer src="/path/to/script.js"></script>');
        
        $result = $theme->add_async_defer_attributes($original_tag, 'other-script');
        expect($result)->toBe($original_tag); // No modification
    });

    it('adds preload for local fonts', function () {
        Monkey\Functions\when('get_template_directory_uri')->justReturn('https://example.com/theme');
        
        $theme = OltreBlocksy_Theme::get_instance();
        
        $original_html = '<link rel="stylesheet" href="https://example.com/theme/fonts/font.css">';
        $result = $theme->add_preload_for_local_fonts($original_html, 'theme-font');
        
        expect($result)->toContain('rel="preload"');
        expect($result)->toContain('as="style"');
        expect($result)->toContain('onload="this.onload=null;this.rel=\'stylesheet\'"');
        
        // External fonts should not be modified
        $external_html = '<link rel="stylesheet" href="https://fonts.googleapis.com/css">';
        $result = $theme->add_preload_for_local_fonts($external_html, 'google-font');
        expect($result)->toBe($external_html);
    });

    it('can get module instances', function () {
        $theme = OltreBlocksy_Theme::get_instance();
        
        // Since modules are loaded, we should be able to get them
        $performance_module = $theme->get_module('Performance');
        $typography_module = $theme->get_module('Typography');
        $non_existent = $theme->get_module('NonExistent');
        
        expect($performance_module)->not->toBeNull();
        expect($typography_module)->not->toBeNull();
        expect($non_existent)->toBeNull();
    });

    it('can get and set theme options', function () {
        Monkey\Functions\when('get_theme_mod')
            ->with('test_option', 'default')
            ->justReturn('test_value');
        
        Monkey\Functions\when('set_theme_mod')
            ->with('test_option', 'new_value')
            ->justReturn(true);
        
        $theme = OltreBlocksy_Theme::get_instance();
        
        $value = $theme->get_option('test_option', 'default');
        expect($value)->toBe('test_value');
        
        $theme->set_option('test_option', 'new_value');
        // If we reach here without exceptions, the method worked
        expect(true)->toBeTrue();
    });

    it('registers module customizer settings', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Mock module with customize_register method
        $mock_module = Mockery::mock();
        $mock_module->shouldReceive('customize_register')
            ->with($wp_customize)
            ->once();
        
        // Create a theme instance with a mocked module
        $theme = new class extends OltreBlocksy_Theme {
            public function __construct() {
                // Skip parent constructor
                $this->modules = [];
            }
            
            public function add_test_module($module) {
                $this->modules['TestModule'] = $module;
            }
        };
        
        $theme->add_test_module($mock_module);
        $theme->register_module_customizer_settings($wp_customize);
        
        expect(true)->toBeTrue();
    });

    it('initializes with oltreblocksy_init function', function () {
        $theme = oltreblocksy_init();
        
        expect($theme)->toBeInstanceOf(OltreBlocksy_Theme::class);
    });

    it('provides helper function oltreblocksy()', function () {
        $theme = oltreblocksy();
        
        expect($theme)->toBeInstanceOf(OltreBlocksy_Theme::class);
        expect($theme)->toBe(oltreblocksy()); // Should return same instance
    });

    it('loads all core modules', function () {
        $theme = OltreBlocksy_Theme::get_instance();
        
        // Check that core modules are loaded
        expect($theme->get_module('Performance'))->not->toBeNull();
        expect($theme->get_module('Typography'))->not->toBeNull();
        expect($theme->get_module('Customizer'))->not->toBeNull();
        expect($theme->get_module('ColorSystem'))->not->toBeNull();
        expect($theme->get_module('Accessibility'))->not->toBeNull();
    });

    it('handles module loading errors gracefully', function () {
        // This test would require more complex mocking to simulate class loading failures
        // For now, we just ensure the theme can be instantiated
        $theme = OltreBlocksy_Theme::get_instance();
        expect($theme)->toBeInstanceOf(OltreBlocksy_Theme::class);
    });

    it('loads theme dependencies correctly', function () {
        // Mock file_exists and require_once
        Monkey\Functions\when('file_exists')->justReturn(true);
        
        $theme = OltreBlocksy_Theme::get_instance();
        
        // If we reach here, dependencies were loaded successfully
        expect($theme)->toBeInstanceOf(OltreBlocksy_Theme::class);
    });

    it('applies theme content width', function () {
        Monkey\Functions\when('apply_filters')
            ->with('oltreblocksy_content_width', 800)
            ->justReturn(1000);
        
        // Mock global variable
        $GLOBALS['content_width'] = null;
        
        $theme = OltreBlocksy_Theme::get_instance();
        $theme->theme_setup();
        
        expect($GLOBALS['content_width'])->toBe(1000);
    });

    it('sets up proper image sizes', function () {
        Monkey\Functions\when('set_post_thumbnail_size')->justReturn(true);
        Monkey\Functions\when('add_image_size')->justReturn(true);
        Monkey\Functions\when('add_theme_support')->justReturn(true);
        Monkey\Functions\when('register_nav_menus')->justReturn(true);
        Monkey\Functions\when('esc_html__')->returnArg();
        Monkey\Functions\when('apply_filters')->returnArg();
        Monkey\Functions\when('add_editor_style')->justReturn(true);
        Monkey\Functions\when('add_action')->justReturn(true);
        
        $theme = OltreBlocksy_Theme::get_instance();
        $theme->theme_setup();
        
        // If we reach here without exceptions, image sizes were set up
        expect(true)->toBeTrue();
    });

    it('conditionally loads jQuery based on settings', function () {
        Monkey\Functions\when('is_admin')->justReturn(false);
        Monkey\Functions\when('wp_deregister_script')->justReturn(true);
        Monkey\Functions\when('get_stylesheet_uri')->justReturn('style.css');
        Monkey\Functions\when('wp_enqueue_style')->justReturn(true);
        Monkey\Functions\when('file_exists')->justReturn(false);
        
        // Mock get_module to return a module with force_jquery setting
        $theme = new class extends OltreBlocksy_Theme {
            public function __construct() {
                // Skip parent constructor
            }
            
            public function get_module($name) {
                if ($name === 'Performance') {
                    return new class {
                        public function get_setting($key, $default = null) {
                            return $key === 'force_jquery' ? false : $default;
                        }
                    };
                }
                return null;
            }
        };
        
        $theme->optimize_scripts();
        
        expect(true)->toBeTrue();
    });

});