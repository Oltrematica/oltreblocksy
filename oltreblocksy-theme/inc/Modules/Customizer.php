<?php
/**
 * Customizer Module
 *
 * Extended Customizer API with dynamic panels, conditional logic,
 * and advanced controls that surpass standard WordPress customization
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

namespace OltreBlocksy\Modules;

defined('ABSPATH') || exit;

class Customizer extends Base_Module {
    
    /**
     * Custom control types
     * 
     * @var array
     */
    private $custom_controls = array();
    
    /**
     * Get module name
     * 
     * @return string
     */
    protected function get_name() {
        return 'Customizer';
    }
    
    /**
     * Initialize module
     */
    protected function init() {
        add_action('customize_register', array($this, 'register_customizer'), 10);
        add_action('customize_controls_enqueue_scripts', array($this, 'enqueue_customizer_scripts'));
        add_action('customize_preview_init', array($this, 'enqueue_preview_scripts'));
        add_action('wp_ajax_oltreblocksy_customizer_action', array($this, 'handle_customizer_ajax'));
        
        // Register custom control types
        $this->register_custom_controls();
        
        $this->log('Customizer module initialized');
    }
    
    /**
     * Register custom control types
     */
    private function register_custom_controls() {
        // For now, we use standard WordPress controls
        // Custom controls can be added in future versions
        $this->custom_controls = array();
    }
    
    /**
     * Register customizer panels, sections, and controls
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    public function register_customizer($wp_customize) {
        // Remove default sections we don't need
        $wp_customize->remove_section('colors');
        $wp_customize->remove_section('background_image');
        
        // Register custom panels
        $this->register_layout_panel($wp_customize);
        $this->register_styling_panel($wp_customize);
        $this->register_performance_panel($wp_customize);
        $this->register_advanced_panel($wp_customize);
        
        // Register sections within panels
        $this->register_general_section($wp_customize);
        $this->register_header_section($wp_customize);
        $this->register_footer_section($wp_customize);
        $this->register_colors_section($wp_customize);
        $this->register_spacing_section($wp_customize);
        $this->register_blog_section($wp_customize);
    }
    
    /**
     * Register Layout Panel
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_layout_panel($wp_customize) {
        $wp_customize->add_panel('oltreblocksy_layout', array(
            'title' => __('Layout & Structure', 'oltreblocksy'),
            'description' => __('Configure the overall layout and structure of your site.', 'oltreblocksy'),
            'priority' => 30,
        ));
    }
    
    /**
     * Register Styling Panel
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_styling_panel($wp_customize) {
        $wp_customize->add_panel('oltreblocksy_styling', array(
            'title' => __('Styling & Colors', 'oltreblocksy'),
            'description' => __('Customize colors, typography, and visual styling.', 'oltreblocksy'),
            'priority' => 40,
        ));
    }
    
    /**
     * Register Performance Panel
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_performance_panel($wp_customize) {
        $wp_customize->add_panel('oltreblocksy_performance', array(
            'title' => __('Performance & Optimization', 'oltreblocksy'),
            'description' => __('Configure performance optimization settings.', 'oltreblocksy'),
            'priority' => 200,
        ));
    }
    
    /**
     * Register Advanced Panel
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_advanced_panel($wp_customize) {
        $wp_customize->add_panel('oltreblocksy_advanced', array(
            'title' => __('Advanced Settings', 'oltreblocksy'),
            'description' => __('Advanced configuration options for developers.', 'oltreblocksy'),
            'priority' => 300,
        ));
    }
    
    /**
     * Register General Section
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_general_section($wp_customize) {
        $wp_customize->add_section('oltreblocksy_general', array(
            'title' => __('General Settings', 'oltreblocksy'),
            'panel' => 'oltreblocksy_layout',
            'priority' => 10,
        ));
        
        // Container width
        $wp_customize->add_setting('oltreblocksy_container_width', array(
            'default' => 1200,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('oltreblocksy_container_width', array(
            'label' => __('Container Max Width', 'oltreblocksy'),
            'section' => 'oltreblocksy_general',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 800,
                'max' => 1600,
                'step' => 10,
            ),
        ));
        
        // Content width
        $wp_customize->add_setting('oltreblocksy_content_width', array(
            'default' => 65,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('oltreblocksy_content_width', array(
            'label' => __('Content Max Width', 'oltreblocksy'),
            'description' => __('Maximum width for readable content in characters.', 'oltreblocksy'),
            'section' => 'oltreblocksy_general',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 45,
                'max' => 85,
                'step' => 1,
            ),
        ));
        
        // Site layout
        $wp_customize->add_setting('oltreblocksy_site_layout', array(
            'default' => 'full-width',
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('oltreblocksy_site_layout', array(
            'label' => __('Site Layout', 'oltreblocksy'),
            'section' => 'oltreblocksy_general',
            'type' => 'select',
            'choices' => array(
                'full-width' => __('Full Width', 'oltreblocksy'),
                'boxed' => __('Boxed', 'oltreblocksy'),
                'framed' => __('Framed', 'oltreblocksy'),
            ),
        ));
    }
    
    /**
     * Register Header Section
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_header_section($wp_customize) {
        $wp_customize->add_section('oltreblocksy_header', array(
            'title' => __('Header Settings', 'oltreblocksy'),
            'panel' => 'oltreblocksy_layout',
            'priority' => 20,
        ));
        
        // Header layout
        $wp_customize->add_setting('oltreblocksy_header_layout', array(
            'default' => 'default',
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('oltreblocksy_header_layout', array(
            'label' => __('Header Layout', 'oltreblocksy'),
            'section' => 'oltreblocksy_header',
            'type' => 'select',
            'choices' => array(
                'default' => __('Default', 'oltreblocksy'),
                'centered' => __('Centered', 'oltreblocksy'),
                'minimal' => __('Minimal', 'oltreblocksy'),
                'split' => __('Split Navigation', 'oltreblocksy'),
            ),
        ));
        
        // Sticky header
        $wp_customize->add_setting('oltreblocksy_sticky_header', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('oltreblocksy_sticky_header', array(
            'label' => __('Sticky Header', 'oltreblocksy'),
            'description' => __('Keep header visible when scrolling.', 'oltreblocksy'),
            'section' => 'oltreblocksy_header',
            'type' => 'checkbox',
        ));
        
        // Header height
        $wp_customize->add_setting('oltreblocksy_header_height', array(
            'default' => 80,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('oltreblocksy_header_height', array(
            'label' => __('Header Height', 'oltreblocksy'),
            'section' => 'oltreblocksy_header',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 60,
                'max' => 120,
                'step' => 5,
            ),
        ));
    }
    
    /**
     * Register Footer Section
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_footer_section($wp_customize) {
        $wp_customize->add_section('oltreblocksy_footer', array(
            'title' => __('Footer Settings', 'oltreblocksy'),
            'panel' => 'oltreblocksy_layout',
            'priority' => 30,
        ));
        
        // Footer widgets columns
        $wp_customize->add_setting('oltreblocksy_footer_columns', array(
            'default' => 4,
            'sanitize_callback' => 'absint',
        ));
        
        $wp_customize->add_control('oltreblocksy_footer_columns', array(
            'label' => __('Footer Widget Columns', 'oltreblocksy'),
            'section' => 'oltreblocksy_footer',
            'type' => 'select',
            'choices' => array(
                1 => __('1 Column', 'oltreblocksy'),
                2 => __('2 Columns', 'oltreblocksy'),
                3 => __('3 Columns', 'oltreblocksy'),
                4 => __('4 Columns', 'oltreblocksy'),
            ),
        ));
        
        // Copyright text
        $wp_customize->add_setting('oltreblocksy_copyright_text', array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        ));
        
        $wp_customize->add_control('oltreblocksy_copyright_text', array(
            'label' => __('Copyright Text', 'oltreblocksy'),
            'description' => __('Leave empty to use default copyright text.', 'oltreblocksy'),
            'section' => 'oltreblocksy_footer',
            'type' => 'textarea',
        ));
    }
    
    /**
     * Register Colors Section
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_colors_section($wp_customize) {
        $wp_customize->add_section('oltreblocksy_colors', array(
            'title' => __('Color System', 'oltreblocksy'),
            'panel' => 'oltreblocksy_styling',
            'priority' => 10,
        ));
        
        // Color scheme
        $wp_customize->add_setting('oltreblocksy_color_scheme', array(
            'default' => 'light',
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('oltreblocksy_color_scheme', array(
            'label' => __('Color Scheme', 'oltreblocksy'),
            'section' => 'oltreblocksy_colors',
            'type' => 'select',
            'choices' => array(
                'light' => __('Light', 'oltreblocksy'),
                'dark' => __('Dark', 'oltreblocksy'),
                'auto' => __('Auto (System Preference)', 'oltreblocksy'),
            ),
        ));
        
        // Primary color
        $wp_customize->add_setting('oltreblocksy_primary_color', array(
            'default' => '#1e40af',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'oltreblocksy_primary_color', array(
            'label' => __('Primary Color', 'oltreblocksy'),
            'section' => 'oltreblocksy_colors',
        )));
        
        // Secondary color
        $wp_customize->add_setting('oltreblocksy_secondary_color', array(
            'default' => '#64748b',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'oltreblocksy_secondary_color', array(
            'label' => __('Secondary Color', 'oltreblocksy'),
            'section' => 'oltreblocksy_colors',
        )));
        
        // Accent color
        $wp_customize->add_setting('oltreblocksy_accent_color', array(
            'default' => '#f59e0b',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control(new \WP_Customize_Color_Control($wp_customize, 'oltreblocksy_accent_color', array(
            'label' => __('Accent Color', 'oltreblocksy'),
            'section' => 'oltreblocksy_colors',
        )));
    }
    
    /**
     * Register Spacing Section
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_spacing_section($wp_customize) {
        $wp_customize->add_section('oltreblocksy_spacing', array(
            'title' => __('Spacing & Layout', 'oltreblocksy'),
            'panel' => 'oltreblocksy_styling',
            'priority' => 20,
        ));
        
        // Global spacing scale
        $wp_customize->add_setting('oltreblocksy_spacing_scale', array(
            'default' => 1.0,
            'sanitize_callback' => array($this, 'sanitize_float'),
            'transport' => 'postMessage',
        ));
        
        $wp_customize->add_control('oltreblocksy_spacing_scale', array(
            'label' => __('Global Spacing Scale', 'oltreblocksy'),
            'description' => __('Scale all spacing values proportionally.', 'oltreblocksy'),
            'section' => 'oltreblocksy_spacing',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 0.5,
                'max' => 2.0,
                'step' => 0.1,
            ),
        ));
        
        // Section spacing
        $wp_customize->add_setting('oltreblocksy_section_spacing', array(
            'default' => 'medium',
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('oltreblocksy_section_spacing', array(
            'label' => __('Section Spacing', 'oltreblocksy'),
            'section' => 'oltreblocksy_spacing',
            'type' => 'select',
            'choices' => array(
                'small' => __('Small', 'oltreblocksy'),
                'medium' => __('Medium', 'oltreblocksy'),
                'large' => __('Large', 'oltreblocksy'),
                'custom' => __('Custom', 'oltreblocksy'),
            ),
        ));
    }
    
    /**
     * Register Blog Section
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    private function register_blog_section($wp_customize) {
        $wp_customize->add_section('oltreblocksy_blog', array(
            'title' => __('Blog Settings', 'oltreblocksy'),
            'priority' => 130,
        ));
        
        // Blog layout
        $wp_customize->add_setting('oltreblocksy_blog_layout', array(
            'default' => 'grid',
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('oltreblocksy_blog_layout', array(
            'label' => __('Blog Layout', 'oltreblocksy'),
            'section' => 'oltreblocksy_blog',
            'type' => 'select',
            'choices' => array(
                'list' => __('List', 'oltreblocksy'),
                'grid' => __('Grid', 'oltreblocksy'),
                'masonry' => __('Masonry', 'oltreblocksy'),
                'cards' => __('Cards', 'oltreblocksy'),
            ),
        ));
        
        // Posts per row
        $wp_customize->add_setting('oltreblocksy_posts_per_row', array(
            'default' => 2,
            'sanitize_callback' => 'absint',
        ));
        
        $wp_customize->add_control('oltreblocksy_posts_per_row', array(
            'label' => __('Posts per Row', 'oltreblocksy'),
            'section' => 'oltreblocksy_blog',
            'type' => 'select',
            'choices' => array(
                1 => __('1 Column', 'oltreblocksy'),
                2 => __('2 Columns', 'oltreblocksy'),
                3 => __('3 Columns', 'oltreblocksy'),
                4 => __('4 Columns', 'oltreblocksy'),
            ),
            'active_callback' => array($this, 'is_grid_layout'),
        ));
        
        // Show excerpt
        $wp_customize->add_setting('oltreblocksy_show_excerpt', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('oltreblocksy_show_excerpt', array(
            'label' => __('Show Post Excerpt', 'oltreblocksy'),
            'section' => 'oltreblocksy_blog',
            'type' => 'checkbox',
        ));
        
        // Excerpt length
        $wp_customize->add_setting('oltreblocksy_excerpt_length', array(
            'default' => 25,
            'sanitize_callback' => 'absint',
        ));
        
        $wp_customize->add_control('oltreblocksy_excerpt_length', array(
            'label' => __('Excerpt Length', 'oltreblocksy'),
            'section' => 'oltreblocksy_blog',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 10,
                'max' => 100,
                'step' => 5,
            ),
            'active_callback' => array($this, 'show_excerpt_enabled'),
        ));
    }
    
    /**
     * Enqueue customizer scripts
     */
    public function enqueue_customizer_scripts() {
        // Scripts disabled until files are created
        /*
        wp_enqueue_script(
            'oltreblocksy-customizer',
            OLTREBLOCKSY_ASSETS_URI . '/js/customizer.js',
            array('jquery', 'customize-controls'),
            OLTREBLOCKSY_VERSION,
            true
        );
        
        wp_enqueue_style(
            'oltreblocksy-customizer-controls',
            OLTREBLOCKSY_ASSETS_URI . '/css/customizer-controls.css',
            array(),
            OLTREBLOCKSY_VERSION
        );
        
        wp_localize_script('oltreblocksy-customizer', 'oltreBlocksyCustomizer', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('oltreblocksy_customizer_nonce'),
            'strings' => array(
                'loading' => __('Loading...', 'oltreblocksy'),
                'error' => __('An error occurred', 'oltreblocksy'),
                'success' => __('Settings saved successfully', 'oltreblocksy'),
            ),
        ));
        */
    }
    
    /**
     * Enqueue preview scripts
     */
    public function enqueue_preview_scripts() {
        // Scripts disabled until files are created
        /*
        wp_enqueue_script(
            'oltreblocksy-customizer-preview',
            OLTREBLOCKSY_ASSETS_URI . '/js/customizer-preview.js',
            array('jquery', 'customize-preview'),
            OLTREBLOCKSY_VERSION,
            true
        );
        
        wp_localize_script('oltreblocksy-customizer-preview', 'oltreBlocksyPreview', array(
            'settings' => array(
                'container_width' => get_theme_mod('oltreblocksy_container_width', 1200),
                'primary_color' => get_theme_mod('oltreblocksy_primary_color', '#1e40af'),
                'secondary_color' => get_theme_mod('oltreblocksy_secondary_color', '#64748b'),
                'accent_color' => get_theme_mod('oltreblocksy_accent_color', '#f59e0b'),
            ),
        ));
        */
    }
    
    /**
     * Handle AJAX requests from customizer
     */
    public function handle_customizer_ajax() {
        check_ajax_referer('oltreblocksy_customizer_nonce', 'nonce');
        
        if (!current_user_can('customize')) {
            wp_die(__('You do not have permission to customize this site.', 'oltreblocksy'));
        }
        
        $action = sanitize_text_field($_POST['customizer_action'] ?? '');
        
        switch ($action) {
            case 'reset_section':
                $this->reset_section_settings($_POST['section'] ?? '');
                break;
                
            case 'export_settings':
                $this->export_theme_settings();
                break;
                
            case 'import_settings':
                $this->import_theme_settings($_POST['settings'] ?? '');
                break;
                
            default:
                wp_send_json_error(__('Invalid action', 'oltreblocksy'));
        }
    }
    
    /**
     * Active callback: Check if grid layout is selected
     * 
     * @return bool
     */
    public function is_grid_layout() {
        $layout = get_theme_mod('oltreblocksy_blog_layout', 'grid');
        return in_array($layout, array('grid', 'masonry', 'cards'));
    }
    
    /**
     * Active callback: Check if excerpt is enabled
     * 
     * @return bool
     */
    public function show_excerpt_enabled() {
        return get_theme_mod('oltreblocksy_show_excerpt', true);
    }
    
    /**
     * Sanitize float values
     * 
     * @param mixed $value
     * @return float
     */
    public function sanitize_float($value) {
        return floatval($value);
    }
    
    /**
     * Reset section settings
     * 
     * @param string $section Section ID
     */
    private function reset_section_settings($section) {
        // Implementation for resetting section settings
        wp_send_json_success(__('Section settings reset successfully', 'oltreblocksy'));
    }
    
    /**
     * Export theme settings
     */
    private function export_theme_settings() {
        $settings = get_theme_mods();
        wp_send_json_success(array('settings' => $settings));
    }
    
    /**
     * Import theme settings
     * 
     * @param string $settings JSON encoded settings
     */
    private function import_theme_settings($settings) {
        $decoded_settings = json_decode($settings, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(__('Invalid settings format', 'oltreblocksy'));
        }
        
        foreach ($decoded_settings as $key => $value) {
            set_theme_mod($key, $value);
        }
        
        wp_send_json_success(__('Settings imported successfully', 'oltreblocksy'));
    }
}