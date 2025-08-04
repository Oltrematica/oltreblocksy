<?php
/**
 * Performance Module
 *
 * Handles all performance optimizations including lazy loading,
 * critical CSS, code splitting, and caching strategies
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

namespace OltreBlocksy\Modules;

defined('ABSPATH') || exit;

class Performance extends Base_Module {
    
    /**
     * Performance metrics
     * 
     * @var array
     */
    private $metrics = array();
    
    /**
     * Get module name
     * 
     * @return string
     */
    protected function get_name() {
        return 'Performance';
    }
    
    /**
     * Initialize module
     */
    protected function init() {
        add_action('wp_enqueue_scripts', array($this, 'optimize_scripts'), 5);
        add_action('wp_head', array($this, 'add_critical_css'), 1);
        add_action('wp_head', array($this, 'add_resource_hints'), 2);
        add_action('wp_footer', array($this, 'add_performance_monitoring'), 999);
        
        add_filter('script_loader_tag', array($this, 'optimize_script_loading'), 10, 3);
        add_filter('style_loader_tag', array($this, 'optimize_style_loading'), 10, 4);
        add_filter('wp_get_attachment_image_attributes', array($this, 'optimize_images'), 10, 3);
        add_filter('the_content', array($this, 'lazy_load_content'), 20);
        
        // Database optimization - simplified
        add_action('init', array($this, 'optimize_database_queries'));
        
        // Performance optimizations
        add_action('wp_default_scripts', array($this, 'remove_jquery_migrate'));
        
        $this->log('Performance module initialized');
    }
    
    /**
     * Optimize script loading
     */
    public function optimize_scripts() {
        // Remove unnecessary WordPress scripts
        if (!is_admin()) {
            wp_deregister_script('wp-embed');
            
            // Conditionally load jQuery
            if (!$this->get_setting('force_jquery', false)) {
                wp_deregister_script('jquery');
            }
        }
        
        // Preload critical resources
        $this->preload_critical_resources();
    }
    
    /**
     * Add critical CSS inline
     */
    public function add_critical_css() {
        $critical_css = $this->get_critical_css();
        
        if (!empty($critical_css)) {
            echo '<style id="oltreblocksy-critical-css">' . $critical_css . '</style>' . "\n";
            
            // Preload main stylesheet
            $stylesheet_uri = get_stylesheet_uri();
            echo '<link rel="preload" href="' . esc_url($stylesheet_uri) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
            echo '<noscript><link rel="stylesheet" href="' . esc_url($stylesheet_uri) . '"></noscript>' . "\n";
        }
    }
    
    /**
     * Get critical CSS
     * 
     * @return string Critical CSS
     */
    private function get_critical_css() {
        $cache_key = 'oltreblocksy_critical_css_' . get_queried_object_id();
        $critical_css = wp_cache_get($cache_key, 'oltreblocksy_performance');
        
        if (false === $critical_css) {
            $critical_css = $this->generate_critical_css();
            wp_cache_set($cache_key, $critical_css, 'oltreblocksy_performance', HOUR_IN_SECONDS);
        }
        
        return $critical_css;
    }
    
    /**
     * Generate critical CSS
     * 
     * @return string Generated critical CSS
     */
    private function generate_critical_css() {
        $critical_css = "
            :root {
                --color-primary: #1e40af;
                --color-text: #1e293b;
                --color-background: #f8fafc;
                --font-system: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                --spacing-sm: clamp(0.5rem, 1vw, 1rem);
                --spacing-md: clamp(1rem, 2vw, 2rem);
                --font-size-base: clamp(1rem, 1.25vw, 1.125rem);
                --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            *, *::before, *::after {
                box-sizing: border-box;
            }
            
            body {
                margin: 0;
                font-family: var(--font-system);
                font-size: var(--font-size-base);
                line-height: 1.6;
                color: var(--color-text);
                background-color: var(--color-background);
                overflow-x: hidden;
            }
            
            .container {
                max-width: min(100% - 2rem, 1200px);
                margin: 0 auto;
                padding-inline: var(--spacing-sm);
            }
            
            .skip-link {
                position: absolute;
                top: -40px;
                left: 6px;
                background: var(--color-primary);
                color: white;
                padding: 8px 12px;
                text-decoration: none;
                border-radius: 4px;
                z-index: 1000;
                transition: var(--transition-base);
            }
            
            .skip-link:focus {
                top: 6px;
            }
            
            .site-header {
                position: relative;
                background: var(--color-background);
                border-bottom: 1px solid #e2e8f0;
            }
            
            .loading {
                opacity: 0;
                animation: fadeIn 250ms forwards;
            }
            
            @keyframes fadeIn {
                to { opacity: 1; }
            }
            
            @media (prefers-reduced-motion: reduce) {
                *, *::before, *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
            }
        ";
        
        // Add page-specific critical CSS
        if (is_front_page()) {
            $critical_css .= $this->get_homepage_critical_css();
        } elseif (is_singular()) {
            $critical_css .= $this->get_single_critical_css();
        }
        
        return $this->minify_css($critical_css);
    }
    
    /**
     * Get homepage critical CSS
     * 
     * @return string Homepage critical CSS
     */
    private function get_homepage_critical_css() {
        return "
            .hero-section {
                min-height: 50vh;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
            }
            
            .hero-title {
                font-size: clamp(2rem, 4vw, 3rem);
                font-weight: 700;
                line-height: 1.2;
                margin-bottom: var(--spacing-sm);
            }
        ";
    }
    
    /**
     * Get single page critical CSS
     * 
     * @return string Single page critical CSS
     */
    private function get_single_critical_css() {
        return "
            .entry-header {
                margin-bottom: var(--spacing-md);
            }
            
            .entry-title {
                font-size: clamp(1.5rem, 3vw, 2.5rem);
                font-weight: 700;
                line-height: 1.2;
                margin-bottom: var(--spacing-sm);
            }
            
            .entry-meta {
                color: #64748b;
                font-size: 0.875rem;
            }
        ";
    }
    
    /**
     * Add resource hints
     */
    public function add_resource_hints() {
        // DNS prefetch
        $dns_prefetch = array(
            '//fonts.googleapis.com',
            '//fonts.gstatic.com',
            '//www.google-analytics.com',
            '//www.googletagmanager.com',
        );
        
        $dns_prefetch = apply_filters('oltreblocksy_dns_prefetch', $dns_prefetch);
        
        foreach ($dns_prefetch as $domain) {
            echo '<link rel="dns-prefetch" href="' . esc_url($domain) . '">' . "\n";
        }
        
        // Preconnect to critical domains
        $preconnect = array(
            '//fonts.googleapis.com' => 'crossorigin',
            '//fonts.gstatic.com' => 'crossorigin',
        );
        
        $preconnect = apply_filters('oltreblocksy_preconnect', $preconnect);
        
        foreach ($preconnect as $domain => $attributes) {
            echo '<link rel="preconnect" href="' . esc_url($domain) . '"' . 
                 ($attributes ? ' ' . $attributes : '') . '>' . "\n";
        }
    }
    
    /**
     * Preload critical resources
     */
    private function preload_critical_resources() {
        // Preload critical fonts
        $critical_fonts = $this->get_setting('critical_fonts', array());
        
        foreach ($critical_fonts as $font) {
            echo '<link rel="preload" href="' . esc_url($font['url']) . '" as="font" type="font/' . 
                 esc_attr($font['format']) . '" crossorigin>' . "\n";
        }
        
        // Preload above-the-fold images
        if (is_front_page() && has_custom_logo()) {
            $logo_id = get_theme_mod('custom_logo');
            $logo_url = wp_get_attachment_image_url($logo_id, 'medium');
            if ($logo_url) {
                echo '<link rel="preload" href="' . esc_url($logo_url) . '" as="image">' . "\n";
            }
        }
        
        // Preload hero image on homepage
        if (is_front_page()) {
            $hero_image = $this->get_setting('hero_image_url');
            if ($hero_image) {
                echo '<link rel="preload" href="' . esc_url($hero_image) . '" as="image">' . "\n";
            }
        }
    }
    
    /**
     * Optimize script loading
     * 
     * @param string $tag Script tag
     * @param string $handle Script handle
     * @param string $src Script source
     * @return string Modified script tag
     */
    public function optimize_script_loading($tag, $handle, $src) {
        // Add async to non-critical scripts
        $async_scripts = array(
            'oltreblocksy-main',
            'comment-reply',
            'wp-embed',
        );
        
        if (in_array($handle, $async_scripts)) {
            return str_replace('<script ', '<script async ', $tag);
        }
        
        // Add defer to analytics scripts
        $defer_scripts = array(
            'google-analytics',
            'gtag',
            'facebook-pixel',
        );
        
        if (in_array($handle, $defer_scripts)) {
            return str_replace('<script ', '<script defer ', $tag);
        }
        
        // Add module attribute for modern browsers
        if (strpos($handle, 'oltreblocksy-') === 0) {
            return str_replace('<script ', '<script type="module" ', $tag);
        }
        
        return $tag;
    }
    
    /**
     * Optimize style loading
     * 
     * @param string $html Style tag HTML
     * @param string $handle Style handle
     * @param string $href Style URL
     * @param string $media Media attribute
     * @return string Modified style tag
     */
    public function optimize_style_loading($html, $handle, $href, $media) {
        // Load non-critical stylesheets asynchronously
        $async_styles = array(
            'wp-block-library',
            'wp-block-library-theme',
        );
        
        if (in_array($handle, $async_styles)) {
            $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
            $html .= '<noscript>' . str_replace("rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", "rel='stylesheet'", $html) . '</noscript>';
        }
        
        return $html;
    }
    
    /**
     * Optimize images
     * 
     * @param array $attr Image attributes
     * @param object $attachment Attachment object
     * @param string $size Image size
     * @return array Modified attributes
     */
    public function optimize_images($attr, $attachment, $size) {
        // Add loading="lazy" except for above-the-fold images
        if (!isset($attr['loading'])) {
            $attr['loading'] = $this->should_eager_load() ? 'eager' : 'lazy';
        }
        
        // Add decoding="async"
        if (!isset($attr['decoding'])) {
            $attr['decoding'] = 'async';
        }
        
        // Add fetchpriority for critical images
        if ($this->should_eager_load() && !isset($attr['fetchpriority'])) {
            $attr['fetchpriority'] = 'high';
        }
        
        return $attr;
    }
    
    /**
     * Check if image should be eager loaded
     * 
     * @return bool
     */
    private function should_eager_load() {
        global $wp_query;
        
        // Eager load first few images
        static $image_count = 0;
        $image_count++;
        
        return $image_count <= 2;
    }
    
    /**
     * Lazy load content elements
     * 
     * @param string $content Post content
     * @return string Modified content
     */
    public function lazy_load_content($content) {
        // Lazy load iframes
        $content = preg_replace_callback(
            '/<iframe([^>]+)>/i',
            array($this, 'lazy_load_iframe'),
            $content
        );
        
        // Lazy load videos
        $content = preg_replace_callback(
            '/<video([^>]+)>/i',
            array($this, 'lazy_load_video'),
            $content
        );
        
        return $content;
    }
    
    /**
     * Lazy load iframe callback
     * 
     * @param array $matches Regex matches
     * @return string Modified iframe
     */
    private function lazy_load_iframe($matches) {
        $iframe_attrs = $matches[1];
        
        // Skip if already has loading attribute
        if (strpos($iframe_attrs, 'loading=') !== false) {
            return $matches[0];
        }
        
        return '<iframe' . $iframe_attrs . ' loading="lazy">';
    }
    
    /**
     * Lazy load video callback
     * 
     * @param array $matches Regex matches
     * @return string Modified video
     */
    private function lazy_load_video($matches) {
        $video_attrs = $matches[1];
        
        // Skip if already has preload attribute
        if (strpos($video_attrs, 'preload=') !== false) {
            return $matches[0];
        }
        
        return '<video' . $video_attrs . ' preload="metadata">';
    }
    
    /**
     * Optimize database queries
     */
    public function optimize_database_queries() {
        // Remove unnecessary queries
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    }
    
    /**
     * Remove jQuery Migrate for performance
     */
    public function remove_jquery_migrate($scripts) {
        if (!is_admin() && isset($scripts->registered['jquery'])) {
            $script = $scripts->registered['jquery'];
            if ($script->deps) {
                $script->deps = array_diff($script->deps, array('jquery-migrate'));
            }
        }
    }
    
    /**
     * Setup object cache
     */
    public function setup_object_cache() {
        // Enable persistent object cache if available
        if (function_exists('wp_cache_add_global_groups')) {
            wp_cache_add_global_groups(array('oltreblocksy_performance'));
        }
    }
    
    /**
     * Add performance monitoring
     */
    public function add_performance_monitoring() {
        if (!$this->get_setting('enable_monitoring', false)) {
            return;
        }
        
        $metrics = oltreblocksy_get_performance_metrics();
        
        echo '<script id="oltreblocksy-performance-monitor">';
        echo 'window.oltreBlocksyPerformance = ' . wp_json_encode($metrics) . ';';
        echo 'if (window.console && console.log) {';
        echo 'console.log("OltreBlocksy Performance Metrics:", window.oltreBlocksyPerformance);';
        echo '}';
        echo '</script>';
        
        // Log to server if debug mode
        if (defined('WP_DEBUG') && WP_DEBUG) {
            oltreblocksy_log_performance($metrics);
        }
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
     * Customize register for performance settings
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    public function customize_register($wp_customize) {
        $wp_customize->add_section('oltreblocksy_performance', array(
            'title' => __('Performance', 'oltreblocksy'),
            'priority' => 130,
            'description' => __('Configure performance optimization settings.', 'oltreblocksy'),
        ));
        
        // Enable monitoring
        $wp_customize->add_setting('oltreblocksy_performance_monitoring', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('oltreblocksy_performance_monitoring', array(
            'label' => __('Enable Performance Monitoring', 'oltreblocksy'),
            'section' => 'oltreblocksy_performance',
            'type' => 'checkbox',
        ));
        
        // Force jQuery
        $wp_customize->add_setting('oltreblocksy_force_jquery', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('oltreblocksy_force_jquery', array(
            'label' => __('Force Load jQuery', 'oltreblocksy'),
            'description' => __('Enable if you have plugins that require jQuery.', 'oltreblocksy'),
            'section' => 'oltreblocksy_performance',
            'type' => 'checkbox',
        ));
    }
}