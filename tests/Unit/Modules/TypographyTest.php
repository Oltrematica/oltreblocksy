<?php

use OltreBlocksy\Modules\Typography;
use Brain\Monkey;

/**
 * Test Typography module functionality
 */
describe('Typography Module', function () {

    beforeEach(function () {
        // Create a testable version of Typography module
        $this->typography = new class extends Typography {
            public function __construct() {
                // Skip parent constructor to avoid WordPress dependencies
                $this->name = 'Typography';
                $this->version = '1.0.0';
                $this->dependencies = [];
                $this->enabled = true;
                $this->settings = [];
                
                // Load typography presets and combinations
                $this->load_typography_presets();
                $this->load_font_combinations();
            }
            
            // Expose protected methods for testing
            public function test_load_typography_presets() {
                $this->load_typography_presets();
                return $this->presets;
            }
            
            public function test_load_font_combinations() {
                $this->load_font_combinations();
                return $this->font_combinations;
            }
            
            public function test_get_current_preset() {
                return $this->get_current_preset();
            }
            
            public function test_build_google_font_url($font) {
                return $this->build_google_font_url($font);
            }
            
            public function test_generate_typography_css($preset) {
                return $this->generate_typography_css($preset);
            }
            
            public function test_fluid_typography($size) {
                return $this->fluid_typography($size);
            }
            
            public function test_generate_editor_typography_css($preset) {
                return $this->generate_editor_typography_css($preset);
            }
            
            public function test_minify_css($css) {
                return $this->minify_css($css);
            }
            
            // Override get_setting for testing
            public function get_setting($key, $default = null) {
                $test_settings = [
                    'preset' => 'modern',
                    'custom_heading_font' => '',
                    'custom_body_font' => '',
                    'font_scale' => 1.25
                ];
                
                return $test_settings[$key] ?? $default;
            }
        };
    });

    it('can be instantiated', function () {
        expect($this->typography)->toBeInstanceOf(Typography::class);
    });

    it('returns correct module name', function () {
        expect($this->typography->get_name())->toBe('Typography');
    });

    it('loads typography presets correctly', function () {
        $presets = $this->typography->test_load_typography_presets();
        
        expect($presets)->toBeArray();
        expect($presets)->toHaveKey('elegant');
        expect($presets)->toHaveKey('modern');
        expect($presets)->toHaveKey('editorial');
        expect($presets)->toHaveKey('minimalist');
        expect($presets)->toHaveKey('creative');
        
        // Check structure of a preset
        $modern = $presets['modern'];
        expect($modern)->toHaveKey('name');
        expect($modern)->toHaveKey('description');
        expect($modern)->toHaveKey('heading_font');
        expect($modern)->toHaveKey('body_font');
        expect($modern)->toHaveKey('font_scale');
        expect($modern)->toHaveKey('line_height_scale');
        expect($modern)->toHaveKey('letter_spacing');
    });

    it('loads font combinations correctly', function () {
        $combinations = $this->typography->test_load_font_combinations();
        
        expect($combinations)->toBeArray();
        expect(count($combinations))->toBeGreaterThan(0);
        
        // Check structure of a combination
        $first_combination = $combinations[0];
        expect($first_combination)->toHaveKey('name');
        expect($first_combination)->toHaveKey('heading');
        expect($first_combination)->toHaveKey('body');
        expect($first_combination)->toHaveKey('mood');
    });

    it('gets current preset correctly', function () {
        $preset = $this->typography->test_get_current_preset();
        
        expect($preset)->toBeArray();
        expect($preset)->toHaveKey('name');
        expect($preset)->toHaveKey('heading_font');
        expect($preset)->toHaveKey('body_font');
        expect($preset['name'])->toBe('Modern'); // Default preset
    });

    it('builds Google Font URL correctly', function () {
        $font = [
            'family' => 'Inter',
            'weights' => [300, 400, 500, 600, 700]
        ];
        
        $url = $this->typography->test_build_google_font_url($font);
        
        expect($url)->toBeString();
        expect($url)->toContain('family=Inter');
        expect($url)->toContain('wght@300;400;500;600;700');
    });

    it('handles font families with spaces correctly', function () {
        $font = [
            'family' => 'Source Sans Pro',
            'weights' => [400, 600]
        ];
        
        $url = $this->typography->test_build_google_font_url($font);
        
        expect($url)->toContain('family=Source+Sans+Pro');
        expect($url)->toContain('wght@400;600');
    });

    it('generates typography CSS with custom properties', function () {
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
        
        $css = $this->typography->test_generate_typography_css($preset);
        
        expect($css)->toBeString();
        expect($css)->toContain('--font-heading: Inter, sans-serif');
        expect($css)->toContain('--font-body: Inter, sans-serif');
        expect($css)->toContain('--font-size-base:');
        expect($css)->toContain('--line-height-base: 1.6');
        expect($css)->toContain('--letter-spacing-heading: -0.02em');
    });

    it('generates fluid typography values', function () {
        $result = $this->typography->test_fluid_typography(1.5);
        
        expect($result)->toBeString();
        expect($result)->toContain('clamp(');
        expect($result)->toContain('rem');
        expect($result)->toContain('vw');
        
        // Should contain min, preferred, and max values
        $parts = explode(',', $result);
        expect(count($parts))->toBe(3);
    });

    it('generates editor typography CSS', function () {
        $preset = [
            'heading_font' => [
                'family' => 'Playfair Display',
                'category' => 'serif'
            ],
            'body_font' => [
                'family' => 'Source Sans Pro',
                'category' => 'sans-serif'
            ],
            'letter_spacing' => [
                'headings' => '-0.025em',
                'body' => '0'
            ]
        ];
        
        $css = $this->typography->test_generate_editor_typography_css($preset);
        
        expect($css)->toBeString();
        expect($css)->toContain('.editor-styles-wrapper');
        expect($css)->toContain('--font-heading: Playfair Display, serif');
        expect($css)->toContain('--font-body: Source Sans Pro, sans-serif');
        expect($css)->toContain('letter-spacing: -0.025em');
    });

    it('minifies CSS correctly', function () {
        $original_css = "
            .typography {
                font-family: 'Inter', sans-serif;
                font-size: 1rem;
            }
            /* Typography comment */
            .heading {
                font-weight: 700;
            }
        ";
        
        $minified = $this->typography->test_minify_css($original_css);
        
        expect($minified)->not->toContain('/*');
        expect($minified)->not->toContain("\n");
        expect($minified)->not->toContain('  ');
        expect($minified)->toContain('.typography{');
    });

    it('gets font recommendations', function () {
        $recommendations = $this->typography->get_font_recommendations('Playfair Display');
        
        expect($recommendations)->toBeArray();
        
        // Should find the Classic Contrast combination
        $found = false;
        foreach ($recommendations as $rec) {
            if ($rec['heading'] === 'Playfair Display' || $rec['body'] === 'Playfair Display') {
                $found = true;
                break;
            }
        }
        expect($found)->toBeTrue();
    });

    it('handles customizer registration', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Mock the get_section method to return null (section doesn't exist)
        $wp_customize->shouldReceive('get_section')
            ->with('oltreblocksy_typography')
            ->andReturn(null);
        
        // Expect section and settings to be added
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->atLeast(4);
        $wp_customize->shouldReceive('add_control')->atLeast(4);
        
        $this->typography->customize_register($wp_customize);
        
        // If we reach here without exceptions, the test passes
        expect(true)->toBeTrue();
    });

    it('sanitizes font scale correctly', function () {
        // Valid values
        expect($this->typography->sanitize_font_scale(1.25))->toBe(1.25);
        expect($this->typography->sanitize_font_scale(1.1))->toBe(1.1);
        expect($this->typography->sanitize_font_scale(1.6))->toBe(1.6);
        
        // Invalid values should return default
        expect($this->typography->sanitize_font_scale(0.5))->toBe(1.25);
        expect($this->typography->sanitize_font_scale(2.0))->toBe(1.25);
        expect($this->typography->sanitize_font_scale('invalid'))->toBe(1.25);
    });

    it('handles font variations correctly', function () {
        $presets = $this->typography->test_load_typography_presets();
        
        // Check that different presets have different characteristics
        expect($presets['elegant']['heading_font']['category'])->toBe('serif');
        expect($presets['modern']['heading_font']['category'])->toBe('sans-serif');
        expect($presets['minimalist']['heading_font']['google_font'])->toBeFalse();
        expect($presets['creative']['heading_font']['google_font'])->toBeTrue();
    });

    it('provides proper font weights for different presets', function () {
        $presets = $this->typography->test_load_typography_presets();
        
        foreach ($presets as $preset) {
            expect($preset['heading_font'])->toHaveKey('weights');
            expect($preset['body_font'])->toHaveKey('weights');
            expect($preset['heading_font']['weights'])->toBeArray();
            expect($preset['body_font']['weights'])->toBeArray();
            expect(count($preset['heading_font']['weights']))->toBeGreaterThan(0);
            expect(count($preset['body_font']['weights']))->toBeGreaterThan(0);
        }
    });

});