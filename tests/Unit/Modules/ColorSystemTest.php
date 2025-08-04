<?php

use OltreBlocksy\Modules\ColorSystem;
use Brain\Monkey;

/**
 * Test ColorSystem module functionality
 */
describe('ColorSystem Module', function () {

    beforeEach(function () {
        // Create a testable version of ColorSystem module
        $this->colorSystem = new class extends ColorSystem {
            public function __construct() {
                // Skip parent constructor to avoid WordPress dependencies
                $this->name = 'ColorSystem';
                $this->version = '1.0.0';
                $this->dependencies = [];
                $this->enabled = true;
                $this->settings = [];
                
                // Load color palettes and harmony rules
                $this->load_color_palettes();
                $this->load_harmony_rules();
            }
            
            // Expose protected methods for testing
            public function test_load_color_palettes() {
                $this->load_color_palettes();
                return $this->palettes;
            }
            
            public function test_load_harmony_rules() {
                $this->load_harmony_rules();
                return $this->harmony_rules;
            }
            
            public function test_get_current_palette() {
                return $this->get_current_palette();
            }
            
            public function test_hex_to_hsl($hex) {
                return $this->hex_to_hsl($hex);
            }
            
            public function test_hsl_to_hex($h, $s, $l) {
                return $this->hsl_to_hex($h, $s, $l);
            }
            
            public function test_get_relative_luminance($hex) {
                return $this->get_relative_luminance($hex);
            }
            
            public function test_generate_dark_mode_css($palette) {
                return $this->generate_dark_mode_css($palette);
            }
            
            public function test_generate_color_utilities($palette) {
                return $this->generate_color_utilities($palette);
            }
            
            public function test_invert_neutral_color($color, $name) {
                return $this->invert_neutral_color($color, $name);
            }
            
            public function test_minify_css($css) {
                return $this->minify_css($css);
            }
            
            // Override get_setting for testing
            public function get_setting($key, $default = null) {
                $test_settings = [
                    'palette' => 'professional',
                    'custom_colors' => [],
                    'dark_mode_enabled' => true
                ];
                
                return $test_settings[$key] ?? $default;
            }
        };
    });

    it('can be instantiated', function () {
        expect($this->colorSystem)->toBeInstanceOf(ColorSystem::class);
    });

    it('returns correct module name', function () {
        expect($this->colorSystem->get_name())->toBe('ColorSystem');
    });

    it('loads color palettes correctly', function () {
        $palettes = $this->colorSystem->test_load_color_palettes();
        
        expect($palettes)->toBeArray();
        expect($palettes)->toHaveKey('professional');
        expect($palettes)->toHaveKey('creative');
        expect($palettes)->toHaveKey('minimalist');
        expect($palettes)->toHaveKey('nature');
        
        // Check structure of a palette
        $professional = $palettes['professional'];
        expect($professional)->toHaveKey('name');
        expect($professional)->toHaveKey('colors');
        expect($professional['colors'])->toHaveKey('primary');
        expect($professional['colors'])->toHaveKey('secondary');
        expect($professional['colors'])->toHaveKey('neutral-50');
        expect($professional['colors'])->toHaveKey('neutral-900');
    });

    it('loads harmony rules correctly', function () {
        $harmony_rules = $this->colorSystem->test_load_harmony_rules();
        
        expect($harmony_rules)->toBeArray();
        expect($harmony_rules)->toHaveKey('complementary');
        expect($harmony_rules)->toHaveKey('triadic');
        expect($harmony_rules)->toHaveKey('analogous');
        expect($harmony_rules)->toHaveKey('split_complementary');
        
        // Check structure of a harmony rule
        $complementary = $harmony_rules['complementary'];
        expect($complementary)->toHaveKey('name');
        expect($complementary)->toHaveKey('description');
        expect($complementary)->toHaveKey('angle');
    });

    it('gets current palette correctly', function () {
        $palette = $this->colorSystem->test_get_current_palette();
        
        expect($palette)->toBeArray();
        expect($palette)->toHaveKey('name');
        expect($palette)->toHaveKey('colors');
        expect($palette['name'])->toBe('Professional'); // Default palette
    });

    it('converts hex to HSL correctly', function () {
        // Test pure red
        $hsl = $this->colorSystem->test_hex_to_hsl('#ff0000');
        expect($hsl['h'])->toBe(0);
        expect($hsl['s'])->toBe(100);
        expect($hsl['l'])->toBe(50);
        
        // Test pure blue
        $hsl = $this->colorSystem->test_hex_to_hsl('#0000ff');
        expect($hsl['h'])->toBe(240);
        expect($hsl['s'])->toBe(100);
        expect($hsl['l'])->toBe(50);
        
        // Test white
        $hsl = $this->colorSystem->test_hex_to_hsl('#ffffff');
        expect($hsl['h'])->toBe(0);
        expect($hsl['s'])->toBe(0);
        expect($hsl['l'])->toBe(100);
        
        // Test black
        $hsl = $this->colorSystem->test_hex_to_hsl('#000000');
        expect($hsl['h'])->toBe(0);
        expect($hsl['s'])->toBe(0);
        expect($hsl['l'])->toBe(0);
    });

    it('handles short hex colors', function () {
        $hsl = $this->colorSystem->test_hex_to_hsl('#f00'); // Short red
        expect($hsl['h'])->toBe(0);
        expect($hsl['s'])->toBe(100);
        expect($hsl['l'])->toBe(50);
    });

    it('converts HSL to hex correctly', function () {
        // Test pure red
        $hex = $this->colorSystem->test_hsl_to_hex(0, 100, 50);
        expect(strtolower($hex))->toBe('#ff0000');
        
        // Test pure blue
        $hex = $this->colorSystem->test_hsl_to_hex(240, 100, 50);
        expect(strtolower($hex))->toBe('#0000ff');
        
        // Test white
        $hex = $this->colorSystem->test_hsl_to_hex(0, 0, 100);
        expect(strtolower($hex))->toBe('#ffffff');
        
        // Test black
        $hex = $this->colorSystem->test_hsl_to_hex(0, 0, 0);
        expect(strtolower($hex))->toBe('#000000');
    });

    it('calculates relative luminance correctly', function () {
        // White should have luminance close to 1
        $luminance = $this->colorSystem->test_get_relative_luminance('#ffffff');
        expect($luminance)->toBeGreaterThan(0.9);
        
        // Black should have luminance close to 0
        $luminance = $this->colorSystem->test_get_relative_luminance('#000000');
        expect($luminance)->toBeLessThan(0.1);
        
        // Pure red
        $luminance = $this->colorSystem->test_get_relative_luminance('#ff0000');
        expect($luminance)->toBeGreaterThan(0);
        expect($luminance)->toBeLessThan(1);
    });

    it('calculates contrast ratio correctly', function () {
        // Black on white should have high contrast (21:1)
        $ratio = $this->colorSystem->check_contrast_ratio('#000000', '#ffffff');
        expect($ratio)->toBeGreaterThan(20);
        
        // Same colors should have contrast ratio of 1
        $ratio = $this->colorSystem->check_contrast_ratio('#ff0000', '#ff0000');
        expect($ratio)->toBe(1.0);
        
        // Test WCAG AA compliance (4.5:1)
        $ratio = $this->colorSystem->check_contrast_ratio('#767676', '#ffffff');
        expect($ratio)->toBeGreaterThan(4.5);
    });

    it('generates complementary color palette', function () {
        $palette = $this->colorSystem->generate_palette('#ff0000', 'complementary');
        
        expect($palette)->toBeArray();
        expect(count($palette))->toBe(2);
        expect($palette[0])->toBe('#ff0000'); // Original color
        
        // Complementary of red should be cyan-ish
        $complement = $palette[1];
        $hsl = $this->colorSystem->test_hex_to_hsl($complement);
        expect($hsl['h'])->toBeBetween(170, 190); // Around 180 degrees from red
    });

    it('generates triadic color palette', function () {
        $palette = $this->colorSystem->generate_palette('#ff0000', 'triadic');
        
        expect($palette)->toBeArray();
        expect(count($palette))->toBe(3);
        expect($palette[0])->toBe('#ff0000'); // Original color
        
        // Check that colors are roughly 120 degrees apart
        $hsl1 = $this->colorSystem->test_hex_to_hsl($palette[1]);
        $hsl2 = $this->colorSystem->test_hex_to_hsl($palette[2]);
        
        expect($hsl1['h'])->toBeBetween(110, 130); // ~120 degrees from red
        expect($hsl2['h'])->toBeBetween(230, 250); // ~240 degrees from red
    });

    it('generates analogous color palette', function () {
        $palette = $this->colorSystem->generate_palette('#ff0000', 'analogous');
        
        expect($palette)->toBeArray();
        expect(count($palette))->toBe(3);
        expect($palette[0])->toBe('#ff0000'); // Original color
        
        // Analogous colors should be close to the original
        $hsl1 = $this->colorSystem->test_hex_to_hsl($palette[1]);
        $hsl2 = $this->colorSystem->test_hex_to_hsl($palette[2]);
        
        expect($hsl1['h'])->toBeBetween(20, 40); // ~30 degrees from red
        expect($hsl2['h'])->toBeBetween(320, 340); // ~-30 degrees from red
    });

    it('generates dark mode CSS', function () {
        $palette = [
            'colors' => [
                'neutral-50' => '#f8fafc',
                'neutral-100' => '#f1f5f9',
                'neutral-900' => '#0f172a',
                'primary' => '#1e40af'
            ]
        ];
        
        $css = $this->colorSystem->test_generate_dark_mode_css($palette);
        
        expect($css)->toBeString();
        expect($css)->toContain('@media (prefers-color-scheme: dark)');
        expect($css)->toContain('.dark-mode');
        expect($css)->toContain('--color-text: var(--color-neutral-100)');
        expect($css)->toContain('--color-background: var(--color-neutral-900)');
    });

    it('generates color utility classes', function () {
        $palette = [
            'colors' => [
                'primary' => '#1e40af',
                'secondary' => '#64748b'
            ]
        ];
        
        $css = $this->colorSystem->test_generate_color_utilities($palette);
        
        expect($css)->toBeString();
        expect($css)->toContain('.text-primary{color:var(--color-primary)}');
        expect($css)->toContain('.bg-primary{background-color:var(--color-primary)}');
        expect($css)->toContain('.border-primary{border-color:var(--color-primary)}');
        expect($css)->toContain('.fill-primary{fill:var(--color-primary)}');
        expect($css)->toContain('.stroke-primary{stroke:var(--color-primary)}');
    });

    it('inverts neutral colors for dark mode', function () {
        // This is a complex method, so we'll test basic functionality
        $inverted = $this->colorSystem->test_invert_neutral_color('#f8fafc', 'neutral-50');
        expect($inverted)->toBeString();
        expect($inverted)->toStartWith('#');
    });

    it('minifies CSS correctly', function () {
        $original_css = "
            .color-test {
                color: #ff0000;
                background: #ffffff;
            }
            /* Color comment */
            .another-color {
                border: 1px solid #000000;
            }
        ";
        
        $minified = $this->colorSystem->test_minify_css($original_css);
        
        expect($minified)->not->toContain('/*');
        expect($minified)->not->toContain("\n");
        expect($minified)->not->toContain('  ');
        expect($minified)->toContain('.color-test{color:#ff0000;background:#ffffff}');
    });

    it('handles AJAX palette generation', function () {
        // Mock WordPress AJAX functions
        Monkey\Functions\when('check_ajax_referer')->justReturn(true);
        Monkey\Functions\when('sanitize_hex_color')->returnArg();
        Monkey\Functions\when('sanitize_key')->returnArg();
        Monkey\Functions\when('wp_send_json_success')->justReturn(true);
        
        // Mock $_POST data
        $_POST['base_color'] = '#ff0000';
        $_POST['harmony_type'] = 'complementary';
        $_POST['nonce'] = 'test_nonce';
        
        // This should not throw an exception
        expect(fn() => $this->colorSystem->ajax_generate_palette())->not->toThrow(Exception::class);
    });

    it('handles AJAX contrast checking', function () {
        // Mock WordPress AJAX functions
        Monkey\Functions\when('check_ajax_referer')->justReturn(true);
        Monkey\Functions\when('sanitize_hex_color')->returnArg();
        Monkey\Functions\when('wp_send_json_success')->justReturn(true);
        
        // Mock $_POST data
        $_POST['color1'] = '#000000';
        $_POST['color2'] = '#ffffff';
        $_POST['nonce'] = 'test_nonce';
        
        // This should not throw an exception
        expect(fn() => $this->colorSystem->ajax_check_contrast())->not->toThrow(Exception::class);
    });

    it('handles customizer registration', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Mock the get_section method to return a section (section exists)
        $wp_customize->shouldReceive('get_section')
            ->with('oltreblocksy_colors')
            ->andReturn(new stdClass());
        
        // Expect settings and controls to be added
        $wp_customize->shouldReceive('add_setting')->atLeast(2);
        $wp_customize->shouldReceive('add_control')->atLeast(2);
        
        $this->colorSystem->customize_register($wp_customize);
        
        // If we reach here without exceptions, the test passes
        expect(true)->toBeTrue();
    });

    it('validates color palette structure', function () {
        $palettes = $this->colorSystem->test_load_color_palettes();
        
        foreach ($palettes as $palette_name => $palette) {
            expect($palette)->toHaveKey('name');
            expect($palette)->toHaveKey('colors');
            expect($palette['colors'])->toBeArray();
            
            // Check that essential colors exist
            expect($palette['colors'])->toHaveKey('primary');
            expect($palette['colors'])->toHaveKey('secondary');
            
            // Validate color format
            foreach ($palette['colors'] as $color_name => $color_value) {
                expect($color_value)->toStartWith('#');
                expect(strlen($color_value))->toBeOneOf([4, 7]); // #RGB or #RRGGBB
            }
        }
    });

    it('provides proper contrast for all palette combinations', function () {
        $palettes = $this->colorSystem->test_load_color_palettes();
        
        foreach ($palettes as $palette_name => $palette) {
            $colors = $palette['colors'];
            
            // Check contrast between text and background colors
            if (isset($colors['neutral-900']) && isset($colors['neutral-50'])) {
                $contrast = $this->colorSystem->check_contrast_ratio($colors['neutral-900'], $colors['neutral-50']);
                expect($contrast)->toBeGreaterThan(7); // Should meet WCAG AAA
            }
            
            if (isset($colors['primary']) && isset($colors['neutral-50'])) {
                $contrast = $this->colorSystem->check_contrast_ratio($colors['primary'], $colors['neutral-50']);
                expect($contrast)->toBeGreaterThan(3); // Should at least meet WCAG AA Large
            }
        }
    });

});