<?php
/**
 * Typography Module
 *
 * Revolutionary typography system with fluid scaling, font pairing,
 * and advanced typography controls that surpass existing solutions
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

namespace OltreBlocksy\Modules;

defined('ABSPATH') || exit;

class Typography extends Base_Module {
    
    /**
     * Typography presets
     * 
     * @var array
     */
    private $presets = array();
    
    /**
     * Font combinations
     * 
     * @var array
     */
    private $font_combinations = array();
    
    /**
     * Get module name
     * 
     * @return string
     */
    protected function get_name() {
        return 'Typography';
    }
    
    /**
     * Initialize module
     */
    protected function init() {
        $this->load_typography_presets();
        $this->load_font_combinations();
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_fonts'));
        add_action('wp_head', array($this, 'output_typography_css'), 20);
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_fonts'));
        
        add_filter('wp_theme_json_data_theme', array($this, 'modify_theme_json_typography'));
        
        $this->log('Typography module initialized');
    }
    
    /**
     * Load typography presets
     */
    private function load_typography_presets() {
        $this->presets = array(
            'elegant' => array(
                'name' => __('Elegant', 'oltreblocksy'),
                'description' => __('Classic serif elegance with modern sensibility', 'oltreblocksy'),
                'heading_font' => array(
                    'family' => 'Playfair Display',
                    'category' => 'serif',
                    'weights' => array(400, 500, 700, 900),
                    'google_font' => true,
                ),
                'body_font' => array(
                    'family' => 'Source Sans Pro',
                    'category' => 'sans-serif',
                    'weights' => array(300, 400, 600, 700),
                    'google_font' => true,
                ),
                'font_scale' => 1.333, // Perfect fourth
                'line_height_scale' => 1.6,
                'letter_spacing' => array(
                    'headings' => '-0.025em',
                    'body' => '0',
                ),
            ),
            'modern' => array(
                'name' => __('Modern', 'oltreblocksy'),
                'description' => __('Clean, contemporary sans-serif for digital-first design', 'oltreblocksy'),
                'heading_font' => array(
                    'family' => 'Inter',
                    'category' => 'sans-serif',
                    'weights' => array(300, 400, 500, 600, 700, 800),
                    'google_font' => true,
                ),
                'body_font' => array(
                    'family' => 'Inter',
                    'category' => 'sans-serif',
                    'weights' => array(300, 400, 500, 600),
                    'google_font' => true,
                ),
                'font_scale' => 1.25, // Major third
                'line_height_scale' => 1.6,
                'letter_spacing' => array(
                    'headings' => '-0.02em',
                    'body' => '0',
                ),
            ),
            'editorial' => array(
                'name' => __('Editorial', 'oltreblocksy'),
                'description' => __('Traditional newspaper-inspired typography for readability', 'oltreblocksy'),
                'heading_font' => array(
                    'family' => 'Crimson Text',
                    'category' => 'serif',
                    'weights' => array(400, 600, 700),
                    'google_font' => true,
                ),
                'body_font' => array(
                    'family' => 'Crimson Text',
                    'category' => 'serif',
                    'weights' => array(400, 600),
                    'google_font' => true,
                ),
                'font_scale' => 1.2, // Minor third
                'line_height_scale' => 1.7,
                'letter_spacing' => array(
                    'headings' => '-0.01em',
                    'body' => '0.01em',
                ),
            ),
            'minimalist' => array(
                'name' => __('Minimalist', 'oltreblocksy'),
                'description' => __('Ultra-clean system fonts for maximum performance', 'oltreblocksy'),
                'heading_font' => array(
                    'family' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    'category' => 'sans-serif',
                    'weights' => array(300, 400, 500, 600, 700),
                    'google_font' => false,
                ),
                'body_font' => array(
                    'family' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    'category' => 'sans-serif',
                    'weights' => array(300, 400, 500, 600),
                    'google_font' => false,
                ),
                'font_scale' => 1.25,
                'line_height_scale' => 1.6,
                'letter_spacing' => array(
                    'headings' => '-0.015em',
                    'body' => '0',
                ),
            ),
            'creative' => array(
                'name' => __('Creative', 'oltreblocksy'),
                'description' => __('Bold, expressive typography for creative portfolios', 'oltreblocksy'),
                'heading_font' => array(
                    'family' => 'Montserrat',
                    'category' => 'sans-serif',
                    'weights' => array(300, 400, 500, 600, 700, 800, 900),
                    'google_font' => true,
                ),
                'body_font' => array(
                    'family' => 'Open Sans',
                    'category' => 'sans-serif',
                    'weights' => array(300, 400, 600, 700),
                    'google_font' => true,
                ),
                'font_scale' => 1.414, // Augmented fourth
                'line_height_scale' => 1.5,
                'letter_spacing' => array(
                    'headings' => '-0.03em',
                    'body' => '0',
                ),
            ),
        );
        
        $this->presets = apply_filters('oltreblocksy_typography_presets', $this->presets);
    }
    
    /**
     * Load font combinations
     */
    private function load_font_combinations() {
        $this->font_combinations = array(
            array(
                'name' => __('Classic Contrast', 'oltreblocksy'),
                'heading' => 'Playfair Display',
                'body' => 'Lato',
                'mood' => 'elegant',
            ),
            array(
                'name' => __('Modern Harmony', 'oltreblocksy'),
                'heading' => 'Poppins',
                'body' => 'Inter',
                'mood' => 'contemporary',
            ),
            array(
                'name' => __('Editorial Authority', 'oltreblocksy'),
                'heading' => 'Merriweather',
                'body' => 'Source Sans Pro',
                'mood' => 'professional',
            ),
            array(
                'name' => __('Creative Expression', 'oltreblocksy'),
                'heading' => 'Oswald',
                'body' => 'Nunito Sans',
                'mood' => 'dynamic',
            ),
        );
        
        $this->font_combinations = apply_filters('oltreblocksy_font_combinations', $this->font_combinations);
    }
    
    /**
     * Enqueue fonts
     */
    public function enqueue_fonts() {
        $current_preset = $this->get_current_preset();
        
        if (!$current_preset) {
            return;
        }
        
        $google_fonts = array();
        
        // Collect Google Fonts
        if ($current_preset['heading_font']['google_font']) {
            $google_fonts[] = $this->build_google_font_url($current_preset['heading_font']);
        }
        
        if ($current_preset['body_font']['google_font'] && 
            $current_preset['body_font']['family'] !== $current_preset['heading_font']['family']) {
            $google_fonts[] = $this->build_google_font_url($current_preset['body_font']);
        }
        
        // Enqueue Google Fonts
        if (!empty($google_fonts)) {
            $font_url = 'https://fonts.googleapis.com/css2?' . implode('&', $google_fonts) . '&display=swap';
            
            wp_enqueue_style(
                'oltreblocksy-google-fonts',
                $font_url,
                array(),
                null
            );
            
            // Preconnect to Google Fonts
            add_action('wp_head', function() {
                echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
                echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
            }, 1);
        }
    }
    
    /**
     * Build Google Font URL
     * 
     * @param array $font Font configuration
     * @return string Google Font URL parameter
     */
    private function build_google_font_url($font) {
        $family = str_replace(' ', '+', $font['family']);
        $weights = implode(';', $font['weights']);
        
        return "family={$family}:wght@{$weights}";
    }
    
    /**
     * Get current typography preset
     * 
     * @return array|null Current preset
     */
    private function get_current_preset() {
        $preset_name = $this->get_setting('preset', 'modern');
        
        return isset($this->presets[$preset_name]) ? $this->presets[$preset_name] : $this->presets['modern'];
    }
    
    /**
     * Output typography CSS
     */
    public function output_typography_css() {
        $current_preset = $this->get_current_preset();
        
        if (!$current_preset) {
            return;
        }
        
        $css = $this->generate_typography_css($current_preset);
        
        echo '<style id="oltreblocksy-typography-css">' . $css . '</style>' . "\n";
    }
    
    /**
     * Generate typography CSS
     * 
     * @param array $preset Typography preset
     * @return string Generated CSS
     */
    private function generate_typography_css($preset) {
        $css = ':root {';
        
        // Font families
        $css .= '--font-heading: ' . $preset['heading_font']['family'] . ', ' . $preset['heading_font']['category'] . ';';
        $css .= '--font-body: ' . $preset['body_font']['family'] . ', ' . $preset['body_font']['category'] . ';';
        
        // Font scale
        $scale = $preset['font_scale'];
        $base_size = 1; // 1rem
        
        $css .= '--font-size-xs: ' . $this->fluid_typography($base_size / ($scale * $scale)) . ';';
        $css .= '--font-size-sm: ' . $this->fluid_typography($base_size / $scale) . ';';
        $css .= '--font-size-base: ' . $this->fluid_typography($base_size) . ';';
        $css .= '--font-size-lg: ' . $this->fluid_typography($base_size * $scale) . ';';
        $css .= '--font-size-xl: ' . $this->fluid_typography($base_size * ($scale * $scale)) . ';';
        $css .= '--font-size-2xl: ' . $this->fluid_typography($base_size * ($scale * $scale * $scale)) . ';';
        $css .= '--font-size-3xl: ' . $this->fluid_typography($base_size * ($scale * $scale * $scale * $scale)) . ';';
        $css .= '--font-size-4xl: ' . $this->fluid_typography($base_size * ($scale * $scale * $scale * $scale * $scale)) . ';';
        
        // Line height
        $css .= '--line-height-base: ' . $preset['line_height_scale'] . ';';
        $css .= '--line-height-heading: 1.2;';
        
        // Letter spacing
        $css .= '--letter-spacing-heading: ' . $preset['letter_spacing']['headings'] . ';';
        $css .= '--letter-spacing-body: ' . $preset['letter_spacing']['body'] . ';';
        
        $css .= '}';
        
        // Apply typography
        $css .= '
            body {
                font-family: var(--font-body);
                font-size: var(--font-size-base);
                line-height: var(--line-height-base);
                letter-spacing: var(--letter-spacing-body);
            }
            
            h1, h2, h3, h4, h5, h6,
            .h1, .h2, .h3, .h4, .h5, .h6 {
                font-family: var(--font-heading);
                line-height: var(--line-height-heading);
                letter-spacing: var(--letter-spacing-heading);
                font-weight: 700;
            }
            
            h1, .h1 { font-size: var(--font-size-4xl); }
            h2, .h2 { font-size: var(--font-size-3xl); }
            h3, .h3 { font-size: var(--font-size-2xl); }
            h4, .h4 { font-size: var(--font-size-xl); }
            h5, .h5 { font-size: var(--font-size-lg); }
            h6, .h6 { font-size: var(--font-size-base); }
            
            .text-xs { font-size: var(--font-size-xs); }
            .text-sm { font-size: var(--font-size-sm); }
            .text-base { font-size: var(--font-size-base); }
            .text-lg { font-size: var(--font-size-lg); }
            .text-xl { font-size: var(--font-size-xl); }
            .text-2xl { font-size: var(--font-size-2xl); }
            .text-3xl { font-size: var(--font-size-3xl); }
            .text-4xl { font-size: var(--font-size-4xl); }
            
            .font-heading { font-family: var(--font-heading); }
            .font-body { font-family: var(--font-body); }
            
            /* Enhanced readability */
            p, li, dd, blockquote {
                max-width: 65ch;
            }
            
            /* Optimize for reading */
            @media (max-width: 768px) {
                :root {
                    --line-height-base: 1.7;
                }
            }
            
            /* Dark mode typography adjustments */
            @media (prefers-color-scheme: dark) {
                :root {
                    --letter-spacing-body: 0.015em;
                }
            }
        ';
        
        return $this->minify_css($css);
    }
    
    /**
     * Generate fluid typography
     * 
     * @param float $size Font size in rem
     * @return string Clamp CSS function
     */
    private function fluid_typography($size) {
        $min_size = $size * 0.875; // 87.5% of target size
        $max_size = $size * 1.125; // 112.5% of target size
        $viewport_factor = $size * 1.25; // Viewport scaling factor
        
        return "clamp({$min_size}rem, {$viewport_factor}vw, {$max_size}rem)";
    }
    
    /**
     * Enqueue editor fonts
     */
    public function enqueue_editor_fonts() {
        $current_preset = $this->get_current_preset();
        
        if (!$current_preset) {
            return;
        }
        
        // Enqueue same fonts for editor
        $google_fonts = array();
        
        if ($current_preset['heading_font']['google_font']) {
            $google_fonts[] = $this->build_google_font_url($current_preset['heading_font']);
        }
        
        if ($current_preset['body_font']['google_font'] && 
            $current_preset['body_font']['family'] !== $current_preset['heading_font']['family']) {
            $google_fonts[] = $this->build_google_font_url($current_preset['body_font']);
        }
        
        if (!empty($google_fonts)) {
            $font_url = 'https://fonts.googleapis.com/css2?' . implode('&', $google_fonts) . '&display=swap';
            
            wp_enqueue_style(
                'oltreblocksy-editor-fonts',
                $font_url,
                array(),
                null
            );
        }
        
        // Add editor typography styles
        wp_add_inline_style('oltreblocksy-editor-fonts', $this->generate_editor_typography_css($current_preset));
    }
    
    /**
     * Generate editor typography CSS
     * 
     * @param array $preset Typography preset
     * @return string Editor CSS
     */
    private function generate_editor_typography_css($preset) {
        return "
            .editor-styles-wrapper {
                --font-heading: {$preset['heading_font']['family']}, {$preset['heading_font']['category']};
                --font-body: {$preset['body_font']['family']}, {$preset['body_font']['category']};
            }
            
            .editor-styles-wrapper .wp-block {
                font-family: var(--font-body);
            }
            
            .editor-styles-wrapper h1,
            .editor-styles-wrapper h2,
            .editor-styles-wrapper h3,
            .editor-styles-wrapper h4,
            .editor-styles-wrapper h5,
            .editor-styles-wrapper h6 {
                font-family: var(--font-heading);
                letter-spacing: {$preset['letter_spacing']['headings']};
            }
        ";
    }
    
    /**
     * Modify theme.json typography data
     * 
     * @param WP_Theme_JSON_Data $theme_json Theme JSON data
     * @return WP_Theme_JSON_Data Modified theme JSON data
     */
    public function modify_theme_json_typography($theme_json) {
        $current_preset = $this->get_current_preset();
        
        if (!$current_preset) {
            return $theme_json;
        }
        
        $data = $theme_json->get_data();
        
        // Update font families
        $data['settings']['typography']['fontFamilies'][] = array(
            'fontFamily' => $current_preset['heading_font']['family'],
            'name' => __('Heading Font', 'oltreblocksy'),
            'slug' => 'heading',
        );
        
        $data['settings']['typography']['fontFamilies'][] = array(
            'fontFamily' => $current_preset['body_font']['family'],
            'name' => __('Body Font', 'oltreblocksy'),
            'slug' => 'body',
        );
        
        return new \WP_Theme_JSON_Data($data);
    }
    
    /**
     * Get font recommendations
     * 
     * @param string $primary_font Primary font family
     * @return array Recommended font pairings
     */
    public function get_font_recommendations($primary_font) {
        $recommendations = array();
        
        foreach ($this->font_combinations as $combination) {
            if ($combination['heading'] === $primary_font || $combination['body'] === $primary_font) {
                $recommendations[] = $combination;
            }
        }
        
        return $recommendations;
    }
    
    /**
     * Minify CSS
     * 
     * @param string $css CSS content
     * @return string Minified CSS
     */
    private function minify_css($css) {
        return oltreblocksy_minify_css($css);
    }
    
    /**
     * Customize register for typography settings
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    public function customize_register($wp_customize) {
        // Typography section
        if (!$wp_customize->get_section('oltreblocksy_typography')) {
            $wp_customize->add_section('oltreblocksy_typography', array(
                'title' => __('Typography', 'oltreblocksy'),
                'priority' => 40,
                'description' => __('Choose from carefully crafted typography presets or create your own combinations.', 'oltreblocksy'),
            ));
        }
        
        // Typography preset
        $preset_choices = array();
        foreach ($this->presets as $key => $preset) {
            $preset_choices[$key] = $preset['name'];
        }
        
        $wp_customize->add_setting('oltreblocksy_typography_preset', array(
            'default' => 'modern',
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('oltreblocksy_typography_preset', array(
            'label' => __('Typography Preset', 'oltreblocksy'),
            'section' => 'oltreblocksy_typography',
            'type' => 'select',
            'choices' => $preset_choices,
        ));
        
        // Custom heading font
        $wp_customize->add_setting('oltreblocksy_custom_heading_font', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('oltreblocksy_custom_heading_font', array(
            'label' => __('Custom Heading Font', 'oltreblocksy'),
            'description' => __('Override the preset heading font with a custom Google Font.', 'oltreblocksy'),
            'section' => 'oltreblocksy_typography',
            'type' => 'text',
        ));
        
        // Custom body font
        $wp_customize->add_setting('oltreblocksy_custom_body_font', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        
        $wp_customize->add_control('oltreblocksy_custom_body_font', array(
            'label' => __('Custom Body Font', 'oltreblocksy'),
            'description' => __('Override the preset body font with a custom Google Font.', 'oltreblocksy'),
            'section' => 'oltreblocksy_typography',
            'type' => 'text',
        ));
        
        // Font scale
        $wp_customize->add_setting('oltreblocksy_font_scale', array(
            'default' => 1.25,
            'sanitize_callback' => array($this, 'sanitize_font_scale'),
        ));
        
        $wp_customize->add_control('oltreblocksy_font_scale', array(
            'label' => __('Font Scale Ratio', 'oltreblocksy'),
            'description' => __('Controls the size relationship between different heading levels.', 'oltreblocksy'),
            'section' => 'oltreblocksy_typography',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 1.1,
                'max' => 1.6,
                'step' => 0.05,
            ),
        ));
    }
    
    /**
     * Sanitize font scale
     * 
     * @param float $value Font scale value
     * @return float Sanitized value
     */
    public function sanitize_font_scale($value) {
        $value = floatval($value);
        return ($value >= 1.1 && $value <= 1.6) ? $value : 1.25;
    }
}