<?php
/**
 * Color System Module
 *
 * Advanced color system with design tokens, automatic palette generation,
 * accessibility checking, and intelligent dark mode support
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

namespace OltreBlocksy\Modules;

defined('ABSPATH') || exit;

class ColorSystem extends Base_Module {
    
    /**
     * Color palettes
     * 
     * @var array
     */
    private $palettes = array();
    
    /**
     * Color harmony rules
     * 
     * @var array
     */
    private $harmony_rules = array();
    
    /**
     * Get module name
     * 
     * @return string
     */
    protected function get_name() {
        return 'ColorSystem';
    }
    
    /**
     * Initialize module
     */
    protected function init() {
        $this->load_color_palettes();
        $this->load_harmony_rules();
        
        add_action('wp_head', array($this, 'output_color_css'), 15);
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_colors'));
        
        add_filter('wp_theme_json_data_theme', array($this, 'modify_theme_json_colors'));
        
        // AJAX endpoints for color tools
        add_action('wp_ajax_oltreblocksy_generate_palette', array($this, 'ajax_generate_palette'));
        add_action('wp_ajax_oltreblocksy_check_contrast', array($this, 'ajax_check_contrast'));
        
        $this->log('Color System module initialized');
    }
    
    /**
     * Load predefined color palettes
     */
    private function load_color_palettes() {
        $this->palettes = array(
            'professional' => array(
                'name' => __('Professional', 'oltreblocksy'),
                'colors' => array(
                    'primary' => '#1e40af',
                    'secondary' => '#64748b',
                    'accent' => '#f59e0b',
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'error' => '#ef4444',
                    'neutral-50' => '#f8fafc',
                    'neutral-100' => '#f1f5f9',
                    'neutral-200' => '#e2e8f0',
                    'neutral-300' => '#cbd5e1',
                    'neutral-400' => '#94a3b8',
                    'neutral-500' => '#64748b',
                    'neutral-600' => '#475569',
                    'neutral-700' => '#334155',
                    'neutral-800' => '#1e293b',
                    'neutral-900' => '#0f172a',
                ),
            ),
            'creative' => array(
                'name' => __('Creative', 'oltreblocksy'),
                'colors' => array(
                    'primary' => '#8b5cf6',
                    'secondary' => '#06b6d4',
                    'accent' => '#f59e0b',
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'error' => '#ef4444',
                    'neutral-50' => '#faf5ff',
                    'neutral-100' => '#f3e8ff',
                    'neutral-200' => '#e9d5ff',
                    'neutral-300' => '#d8b4fe',
                    'neutral-400' => '#c084fc',
                    'neutral-500' => '#a855f7',
                    'neutral-600' => '#9333ea',
                    'neutral-700' => '#7c3aed',
                    'neutral-800' => '#6b21a8',
                    'neutral-900' => '#581c87',
                ),
            ),
            'minimalist' => array(
                'name' => __('Minimalist', 'oltreblocksy'),
                'colors' => array(
                    'primary' => '#000000',
                    'secondary' => '#6b7280',
                    'accent' => '#dc2626',
                    'success' => '#059669',
                    'warning' => '#d97706',
                    'error' => '#dc2626',
                    'neutral-50' => '#ffffff',
                    'neutral-100' => '#f9fafb',
                    'neutral-200' => '#f3f4f6',
                    'neutral-300' => '#e5e7eb',
                    'neutral-400' => '#d1d5db',
                    'neutral-500' => '#9ca3af',
                    'neutral-600' => '#6b7280',
                    'neutral-700' => '#4b5563',
                    'neutral-800' => '#374151',
                    'neutral-900' => '#111827',
                ),
            ),
            'nature' => array(
                'name' => __('Nature', 'oltreblocksy'),
                'colors' => array(
                    'primary' => '#059669',
                    'secondary' => '#0d9488',
                    'accent' => '#ea580c',
                    'success' => '#059669',
                    'warning' => '#d97706',
                    'error' => '#dc2626',
                    'neutral-50' => '#f0fdf4',
                    'neutral-100' => '#dcfce7',
                    'neutral-200' => '#bbf7d0',
                    'neutral-300' => '#86efac',
                    'neutral-400' => '#4ade80',
                    'neutral-500' => '#22c55e',
                    'neutral-600' => '#16a34a',
                    'neutral-700' => '#15803d',
                    'neutral-800' => '#166534',
                    'neutral-900' => '#14532d',
                ),
            ),
        );
        
        $this->palettes = apply_filters('oltreblocksy_color_palettes', $this->palettes);
    }
    
    /**
     * Load color harmony rules
     */
    private function load_harmony_rules() {
        $this->harmony_rules = array(
            'complementary' => array(
                'name' => __('Complementary', 'oltreblocksy'),
                'description' => __('Colors opposite on the color wheel', 'oltreblocksy'),
                'angle' => 180,
            ),
            'triadic' => array(
                'name' => __('Triadic', 'oltreblocksy'),
                'description' => __('Three colors equally spaced on the color wheel', 'oltreblocksy'),
                'angles' => array(120, 240),
            ),
            'analogous' => array(
                'name' => __('Analogous', 'oltreblocksy'),
                'description' => __('Colors adjacent on the color wheel', 'oltreblocksy'),
                'angles' => array(30, -30),
            ),
            'split_complementary' => array(
                'name' => __('Split Complementary', 'oltreblocksy'),
                'description' => __('Base color plus two colors adjacent to its complement', 'oltreblocksy'),
                'angles' => array(150, 210),
            ),
        );
    }
    
    /**
     * Get current color palette
     * 
     * @return array Current palette
     */
    private function get_current_palette() {
        $palette_name = $this->get_setting('palette', 'professional');
        $custom_colors = $this->get_setting('custom_colors', array());
        
        $palette = isset($this->palettes[$palette_name]) ? $this->palettes[$palette_name] : $this->palettes['professional'];
        
        // Merge custom colors
        $palette['colors'] = array_merge($palette['colors'], $custom_colors);
        
        return $palette;
    }
    
    /**
     * Output color CSS variables
     */
    public function output_color_css() {
        $palette = $this->get_current_palette();
        $dark_mode_enabled = $this->get_setting('dark_mode_enabled', true);
        
        $css = ':root {';
        
        // Generate CSS custom properties
        foreach ($palette['colors'] as $name => $color) {
            $css .= "--color-{$name}: {$color};";
            
            // Generate HSL values for better manipulation
            $hsl = $this->hex_to_hsl($color);
            $css .= "--color-{$name}-h: {$hsl['h']};";
            $css .= "--color-{$name}-s: {$hsl['s']}%;";
            $css .= "--color-{$name}-l: {$hsl['l']}%;";
            $css .= "--color-{$name}-hsl: {$hsl['h']}, {$hsl['s']}%, {$hsl['l']}%;";
        }
        
        // Semantic color mappings
        $css .= '--color-text: var(--color-neutral-900);';
        $css .= '--color-text-muted: var(--color-neutral-600);';
        $css .= '--color-background: var(--color-neutral-50);';
        $css .= '--color-surface: var(--color-neutral-100);';
        $css .= '--color-border: var(--color-neutral-200);';
        $css .= '--color-border-subtle: var(--color-neutral-100);';
        
        $css .= '}';
        
        // Dark mode colors
        if ($dark_mode_enabled) {
            $css .= $this->generate_dark_mode_css($palette);
        }
        
        // Generate utility classes
        $css .= $this->generate_color_utilities($palette);
        
        echo '<style id="oltreblocksy-color-system">' . $this->minify_css($css) . '</style>' . "\n";
    }
    
    /**
     * Generate dark mode CSS
     * 
     * @param array $palette Color palette
     * @return string Dark mode CSS
     */
    private function generate_dark_mode_css($palette) {
        $css = '@media (prefers-color-scheme: dark) { :root {';
        
        // Automatically generate dark mode variants
        foreach ($palette['colors'] as $name => $color) {
            if (strpos($name, 'neutral-') === 0) {
                // Invert neutral colors for dark mode
                $inverted_color = $this->invert_neutral_color($color, $name);
                $css .= "--color-{$name}: {$inverted_color};";
            }
        }
        
        // Dark mode semantic mappings
        $css .= '--color-text: var(--color-neutral-100);';
        $css .= '--color-text-muted: var(--color-neutral-400);';
        $css .= '--color-background: var(--color-neutral-900);';
        $css .= '--color-surface: var(--color-neutral-800);';
        $css .= '--color-border: var(--color-neutral-700);';
        $css .= '--color-border-subtle: var(--color-neutral-800);';
        
        $css .= '} }';
        
        // Manual dark mode toggle
        $css .= '.dark-mode {';
        foreach ($palette['colors'] as $name => $color) {
            if (strpos($name, 'neutral-') === 0) {
                $inverted_color = $this->invert_neutral_color($color, $name);
                $css .= "--color-{$name}: {$inverted_color};";
            }
        }
        
        $css .= '--color-text: var(--color-neutral-100);';
        $css .= '--color-text-muted: var(--color-neutral-400);';
        $css .= '--color-background: var(--color-neutral-900);';
        $css .= '--color-surface: var(--color-neutral-800);';
        $css .= '--color-border: var(--color-neutral-700);';
        $css .= '--color-border-subtle: var(--color-neutral-800);';
        
        $css .= '}';
        
        return $css;
    }
    
    /**
     * Generate color utility classes
     * 
     * @param array $palette Color palette
     * @return string Utility CSS
     */
    private function generate_color_utilities($palette) {
        $css = '';
        
        foreach ($palette['colors'] as $name => $color) {
            // Text colors
            $css .= ".text-{$name} { color: var(--color-{$name}); }";
            
            // Background colors
            $css .= ".bg-{$name} { background-color: var(--color-{$name}); }";
            
            // Border colors
            $css .= ".border-{$name} { border-color: var(--color-{$name}); }";
            
            // Fill colors for SVGs
            $css .= ".fill-{$name} { fill: var(--color-{$name}); }";
            
            // Stroke colors for SVGs
            $css .= ".stroke-{$name} { stroke: var(--color-{$name}); }";
        }
        
        // Semantic utility classes
        $css .= '.text-primary { color: var(--color-primary); }';
        $css .= '.text-secondary { color: var(--color-secondary); }';
        $css .= '.text-accent { color: var(--color-accent); }';
        $css .= '.text-muted { color: var(--color-text-muted); }';
        
        $css .= '.bg-primary { background-color: var(--color-primary); }';
        $css .= '.bg-secondary { background-color: var(--color-secondary); }';
        $css .= '.bg-accent { background-color: var(--color-accent); }';
        $css .= '.bg-surface { background-color: var(--color-surface); }';
        
        return $css;
    }
    
    /**
     * Invert neutral color for dark mode
     * 
     * @param string $color Hex color
     * @param string $name Color name
     * @return string Inverted color
     */
    private function invert_neutral_color($color, $name) {
        // Extract number from neutral color name
        preg_match('/neutral-(\d+)/', $name, $matches);
        if (!$matches) {
            return $color;
        }
        
        $level = intval($matches[1]);
        $inverted_level = 1000 - $level;
        
        // Map to existing neutral colors
        $neutral_map = array(
            1000 => 'neutral-50',
            950 => 'neutral-100',
            900 => 'neutral-100',
            850 => 'neutral-200',
            800 => 'neutral-200',
            750 => 'neutral-300',
            700 => 'neutral-300',
            650 => 'neutral-400',
            600 => 'neutral-400',
            550 => 'neutral-500',
            500 => 'neutral-500',
            450 => 'neutral-600',
            400 => 'neutral-600',
            350 => 'neutral-700',
            300 => 'neutral-700',
            250 => 'neutral-800',
            200 => 'neutral-800',
            150 => 'neutral-900',
            100 => 'neutral-900',
            50 => 'neutral-950',
        );
        
        $mapped_name = isset($neutral_map[$inverted_level]) ? $neutral_map[$inverted_level] : $name;
        $palette = $this->get_current_palette();
        
        return isset($palette['colors'][$mapped_name]) ? $palette['colors'][$mapped_name] : $color;
    }
    
    /**
     * Convert hex color to HSL
     * 
     * @param string $hex Hex color
     * @return array HSL values
     */
    private function hex_to_hsl($hex) {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $diff = $max - $min;
        
        // Lightness
        $l = ($max + $min) / 2;
        
        if ($diff == 0) {
            $h = $s = 0; // achromatic
        } else {
            // Saturation
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);
            
            // Hue
            switch ($max) {
                case $r:
                    $h = (($g - $b) / $diff + ($g < $b ? 6 : 0)) / 6;
                    break;
                case $g:
                    $h = (($b - $r) / $diff + 2) / 6;
                    break;
                case $b:
                    $h = (($r - $g) / $diff + 4) / 6;
                    break;
            }
        }
        
        return array(
            'h' => round($h * 360),
            's' => round($s * 100),
            'l' => round($l * 100),
        );
    }
    
    /**
     * Check color contrast ratio
     * 
     * @param string $color1 First color (hex)
     * @param string $color2 Second color (hex)
     * @return float Contrast ratio
     */
    public function check_contrast_ratio($color1, $color2) {
        $luminance1 = $this->get_relative_luminance($color1);
        $luminance2 = $this->get_relative_luminance($color2);
        
        $lighter = max($luminance1, $luminance2);
        $darker = min($luminance1, $luminance2);
        
        return ($lighter + 0.05) / ($darker + 0.05);
    }
    
    /**
     * Get relative luminance of a color
     * 
     * @param string $hex Hex color
     * @return float Relative luminance
     */
    private function get_relative_luminance($hex) {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        
        // Apply gamma correction
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
    
    /**
     * Generate color palette from base color
     * 
     * @param string $base_color Base hex color
     * @param string $harmony_type Harmony rule type
     * @return array Generated palette
     */
    public function generate_palette($base_color, $harmony_type = 'complementary') {
        if (!isset($this->harmony_rules[$harmony_type])) {
            return array();
        }
        
        $rule = $this->harmony_rules[$harmony_type];
        $hsl = $this->hex_to_hsl($base_color);
        $palette = array($base_color);
        
        if (isset($rule['angle'])) {
            // Single angle (complementary)
            $new_hue = ($hsl['h'] + $rule['angle']) % 360;
            $palette[] = $this->hsl_to_hex($new_hue, $hsl['s'], $hsl['l']);
        } elseif (isset($rule['angles'])) {
            // Multiple angles (triadic, analogous, etc.)
            foreach ($rule['angles'] as $angle) {
                $new_hue = ($hsl['h'] + $angle) % 360;
                $palette[] = $this->hsl_to_hex($new_hue, $hsl['s'], $hsl['l']);
            }
        }
        
        return $palette;
    }
    
    /**
     * Convert HSL to hex color
     * 
     * @param int $h Hue (0-360)
     * @param int $s Saturation (0-100)
     * @param int $l Lightness (0-100)
     * @return string Hex color
     */
    private function hsl_to_hex($h, $s, $l) {
        $h /= 360;
        $s /= 100;
        $l /= 100;
        
        if ($s == 0) {
            $r = $g = $b = $l; // achromatic
        } else {
            $hue2rgb = function($p, $q, $t) {
                if ($t < 0) $t += 1;
                if ($t > 1) $t -= 1;
                if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
                if ($t < 1/2) return $q;
                if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
                return $p;
            };
            
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            
            $r = $hue2rgb($p, $q, $h + 1/3);
            $g = $hue2rgb($p, $q, $h);
            $b = $hue2rgb($p, $q, $h - 1/3);
        }
        
        return sprintf('#%02x%02x%02x', round($r * 255), round($g * 255), round($b * 255));
    }
    
    /**
     * Enqueue editor colors
     */
    public function enqueue_editor_colors() {
        $palette = $this->get_current_palette();
        $editor_colors = array();
        
        foreach ($palette['colors'] as $name => $color) {
            $editor_colors[] = array(
                'name' => ucfirst(str_replace(array('-', '_'), ' ', $name)),
                'slug' => $name,
                'color' => $color,
            );
        }
        
        wp_add_inline_script(
            'wp-block-editor',
            'wp.data.dispatch("core/block-editor").updateSettings({ colors: ' . wp_json_encode($editor_colors) . ' });'
        );
    }
    
    /**
     * Modify theme.json color data
     * 
     * @param WP_Theme_JSON_Data $theme_json
     * @return WP_Theme_JSON_Data
     */
    public function modify_theme_json_colors($theme_json) {
        $palette = $this->get_current_palette();
        $data = $theme_json->get_data();
        
        $wp_palette = array();
        foreach ($palette['colors'] as $name => $color) {
            $wp_palette[] = array(
                'color' => $color,
                'name' => ucfirst(str_replace(array('-', '_'), ' ', $name)),
                'slug' => $name,
            );
        }
        
        $data['settings']['color']['palette'] = $wp_palette;
        
        return new \WP_Theme_JSON_Data($data);
    }
    
    /**
     * AJAX: Generate color palette
     */
    public function ajax_generate_palette() {
        check_ajax_referer('oltreblocksy_nonce', 'nonce');
        
        $base_color = sanitize_hex_color($_POST['base_color'] ?? '#1e40af');
        $harmony_type = sanitize_key($_POST['harmony_type'] ?? 'complementary');
        
        $palette = $this->generate_palette($base_color, $harmony_type);
        
        wp_send_json_success(array('palette' => $palette));
    }
    
    /**
     * AJAX: Check color contrast
     */
    public function ajax_check_contrast() {
        check_ajax_referer('oltreblocksy_nonce', 'nonce');
        
        $color1 = sanitize_hex_color($_POST['color1'] ?? '#000000');
        $color2 = sanitize_hex_color($_POST['color2'] ?? '#ffffff');
        
        $ratio = $this->check_contrast_ratio($color1, $color2);
        
        $wcag_aa = $ratio >= 4.5;
        $wcag_aaa = $ratio >= 7;
        $wcag_aa_large = $ratio >= 3;
        
        wp_send_json_success(array(
            'ratio' => round($ratio, 2),
            'wcag_aa' => $wcag_aa,
            'wcag_aaa' => $wcag_aaa,
            'wcag_aa_large' => $wcag_aa_large,
        ));
    }
    
    /**
     * Minify CSS
     * 
     * @param string $css
     * @return string
     */
    private function minify_css($css) {
        return oltreblocksy_minify_css($css);
    }
    
    /**
     * Customize register for color settings
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    public function customize_register($wp_customize) {
        // Ensure colors section exists
        if (!$wp_customize->get_section('oltreblocksy_colors')) {
            return; // Section will be created by Customizer module
        }
        
        // Color palette selection
        $palette_choices = array();
        foreach ($this->palettes as $key => $palette) {
            $palette_choices[$key] = $palette['name'];
        }
        
        $wp_customize->add_setting('oltreblocksy_color_palette', array(
            'default' => 'professional',
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('oltreblocksy_color_palette', array(
            'label' => __('Color Palette', 'oltreblocksy'),
            'section' => 'oltreblocksy_colors',
            'type' => 'select',
            'choices' => $palette_choices,
        ));
        
        // Dark mode toggle
        $wp_customize->add_setting('oltreblocksy_dark_mode_enabled', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('oltreblocksy_dark_mode_enabled', array(
            'label' => __('Enable Dark Mode', 'oltreblocksy'),
            'description' => __('Automatically generate dark mode color variants.', 'oltreblocksy'),
            'section' => 'oltreblocksy_colors',
            'type' => 'checkbox',
        ));
    }
}