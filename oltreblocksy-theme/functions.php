<?php
/**
 * OltreBlocksy Theme Functions
 *
 * Sets up the theme and provides a modular architecture that surpasses
 * Blocksy in terms of configurability, performance, and code quality.
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Theme constants
define('OLTREBLOCKSY_VERSION', '1.0.0');
define('OLTREBLOCKSY_THEME_DIR', get_template_directory());
define('OLTREBLOCKSY_THEME_URI', get_template_directory_uri());
define('OLTREBLOCKSY_INC_DIR', OLTREBLOCKSY_THEME_DIR . '/inc');
define('OLTREBLOCKSY_ASSETS_URI', OLTREBLOCKSY_THEME_URI . '/assets');

/**
 * PSR-4 Autoloader for theme classes
 *
 * @param string $class_name The fully-qualified class name.
 */
spl_autoload_register(function ($class_name) {
    // Project namespace prefix
    $prefix = 'OltreBlocksy\\';
    
    // Base directory for the namespace prefix
    $base_dir = OLTREBLOCKSY_INC_DIR . '/';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class_name, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class_name, $len);
    
    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Load the file if it exists
    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Main Theme Class - Singleton Pattern
 */
final class OltreBlocksy_Theme {
    
    /**
     * Theme instance
     * 
     * @var OltreBlocksy_Theme
     */
    private static $instance = null;
    
    /**
     * Loaded modules
     * 
     * @var array
     */
    private $modules = array();
    
    /**
     * Theme options
     * 
     * @var array
     */
    private $options = array();
    
    /**
     * Get theme instance
     * 
     * @return OltreBlocksy_Theme
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
        $this->load_modules();
    }
    
    /**
     * Load core dependencies
     */
    private function load_dependencies() {
        // Core helper functions
        require_once OLTREBLOCKSY_INC_DIR . '/helpers.php';
        require_once OLTREBLOCKSY_INC_DIR . '/template-functions.php';
        require_once OLTREBLOCKSY_INC_DIR . '/template-tags.php';
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('after_setup_theme', array($this, 'theme_setup'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_scripts'));
        add_action('wp_head', array($this, 'preload_critical_resources'), 1);
        add_action('init', array($this, 'load_textdomain'));
        add_action('customize_register', array($this, 'register_module_customizer_settings'), 15);
        
        // Performance optimizations
        add_filter('script_loader_tag', array($this, 'add_async_defer_attributes'), 10, 2);
        add_filter('style_loader_tag', array($this, 'add_preload_for_local_fonts'), 10, 2);
    }
    
    /**
     * Load theme modules
     */
    private function load_modules() {
        $core_modules = array(
            'Performance' => 'OltreBlocksy\\Modules\\Performance',
            'Typography' => 'OltreBlocksy\\Modules\\Typography',
            'Customizer' => 'OltreBlocksy\\Modules\\Customizer',
            'ColorSystem' => 'OltreBlocksy\\Modules\\ColorSystem',
            'Accessibility' => 'OltreBlocksy\\Modules\\Accessibility',
        );
        
        foreach ($core_modules as $name => $class) {
            try {
                if (class_exists($class)) {
                    $this->modules[$name] = new $class();
                } else {
                    error_log("OltreBlocksy: Module class $class not found");
                }
            } catch (\Exception $e) {
                error_log("OltreBlocksy: Failed to initialize module $name: " . $e->getMessage());
            }
        }
        
        // Allow modules to be filtered
        $this->modules = apply_filters('oltreblocksy_loaded_modules', $this->modules);
    }
    
    /**
     * Theme setup
     */
    public function theme_setup() {
        // Add theme support
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('custom-logo');
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ));
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        add_theme_support('editor-styles');
        add_theme_support('wp-block-styles');
        
        // Register navigation menus
        register_nav_menus(array(
            'primary' => esc_html__('Primary Menu', 'oltreblocksy'),
            'footer' => esc_html__('Footer Menu', 'oltreblocksy'),
            'social' => esc_html__('Social Menu', 'oltreblocksy'),
        ));
        
        // Set content width
        $GLOBALS['content_width'] = apply_filters('oltreblocksy_content_width', 800);
        
        // Add editor styles
        add_editor_style('assets/css/editor-style.css');
        
        // Set up image sizes
        set_post_thumbnail_size(1200, 675, true);
        add_image_size('hero', 1920, 1080, true);
        add_image_size('card', 600, 400, true);
        add_image_size('thumbnail-large', 300, 300, true);
        
        // Register widget areas
        add_action('widgets_init', array($this, 'register_widget_areas'));
    }
    
    /**
     * Load theme textdomain
     */
    public function load_textdomain() {
        load_theme_textdomain('oltreblocksy', OLTREBLOCKSY_THEME_DIR . '/languages');
    }
    
    /**
     * Register widget areas
     */
    public function register_widget_areas() {
        register_sidebar(array(
            'name'          => esc_html__('Sidebar', 'oltreblocksy'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', 'oltreblocksy'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ));
        
        // Footer widget areas
        for ($i = 1; $i <= 4; $i++) {
            register_sidebar(array(
                'name'          => sprintf(esc_html__('Footer %d', 'oltreblocksy'), $i),
                'id'            => 'footer-' . $i,
                'description'   => sprintf(esc_html__('Add widgets to footer column %d.', 'oltreblocksy'), $i),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h3 class="widget-title">',
                'after_title'   => '</h3>',
            ));
        }
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Main stylesheet
        wp_enqueue_style(
            'oltreblocksy-style',
            get_stylesheet_uri(),
            array(),
            OLTREBLOCKSY_VERSION
        );
        
        // Main JavaScript (with modern ES6+ features)
        if (file_exists(OLTREBLOCKSY_THEME_DIR . '/assets/js/main.js')) {
            wp_enqueue_script(
                'oltreblocksy-main',
                OLTREBLOCKSY_ASSETS_URI . '/js/main.js',
                array(),
                OLTREBLOCKSY_VERSION,
                true
            );
        }
        
        // Localize script for AJAX
        if (wp_script_is('oltreblocksy-main', 'enqueued')) {
            wp_localize_script('oltreblocksy-main', 'oltreBlocksyAjax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('oltreblocksy_nonce'),
                'strings' => array(
                    'loading' => esc_html__('Loading...', 'oltreblocksy'),
                    'error' => esc_html__('An error occurred', 'oltreblocksy'),
                ),
            ));
        }
        
        // Comments script
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
        
        // Conditional scripts
        if (is_front_page() && file_exists(OLTREBLOCKSY_THEME_DIR . '/assets/js/homepage.js')) {
            wp_enqueue_script(
                'oltreblocksy-homepage',
                OLTREBLOCKSY_ASSETS_URI . '/js/homepage.js',
                array('oltreblocksy-main'),
                OLTREBLOCKSY_VERSION,
                true
            );
        }
    }
    
    /**
     * Enqueue editor scripts
     */
    public function enqueue_editor_scripts() {
        if (file_exists(OLTREBLOCKSY_THEME_DIR . '/assets/js/editor.js')) {
            wp_enqueue_script(
                'oltreblocksy-editor',
                OLTREBLOCKSY_ASSETS_URI . '/js/editor.js',
                array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'),
                OLTREBLOCKSY_VERSION,
                true
            );
        }
        
        if (file_exists(OLTREBLOCKSY_THEME_DIR . '/assets/css/editor.css')) {
            wp_enqueue_style(
                'oltreblocksy-editor-style',
                OLTREBLOCKSY_ASSETS_URI . '/css/editor.css',
                array(),
                OLTREBLOCKSY_VERSION
            );
        }
    }
    
    /**
     * Preload critical resources for performance
     */
    public function preload_critical_resources() {
        // Preload critical CSS
        echo '<link rel="preload" href="' . esc_url(get_stylesheet_uri()) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
        
        // Preload main JavaScript
        echo '<link rel="preload" href="' . esc_url(OLTREBLOCKSY_ASSETS_URI . '/js/main.js') . '" as="script">' . "\n";
        
        // DNS prefetch for external resources
        echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
        echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
    }
    
    /**
     * Add async/defer attributes to scripts
     */
    public function add_async_defer_attributes($tag, $handle) {
        $async_scripts = array('oltreblocksy-main', 'oltreblocksy-homepage');
        $defer_scripts = array('comment-reply');
        
        if (in_array($handle, $async_scripts)) {
            return str_replace('<script ', '<script async ', $tag);
        }
        
        if (in_array($handle, $defer_scripts)) {
            return str_replace('<script ', '<script defer ', $tag);
        }
        
        return $tag;
    }
    
    /**
     * Add preload for local fonts
     */
    public function add_preload_for_local_fonts($html, $handle) {
        if (strpos($handle, 'font') !== false && strpos($html, get_template_directory_uri()) !== false) {
            $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
        }
        return $html;
    }
    
    /**
     * Get module instance
     * 
     * @param string $module_name Module name
     * @return object|null Module instance or null if not found
     */
    public function get_module($module_name) {
        return isset($this->modules[$module_name]) ? $this->modules[$module_name] : null;
    }
    
    /**
     * Get theme option
     * 
     * @param string $option_name Option name
     * @param mixed $default Default value
     * @return mixed Option value
     */
    public function get_option($option_name, $default = null) {
        return get_theme_mod($option_name, $default);
    }
    
    /**
     * Set theme option
     * 
     * @param string $option_name Option name
     * @param mixed $value Option value
     */
    public function set_option($option_name, $value) {
        set_theme_mod($option_name, $value);
    }
    
    /**
     * Register module customizer settings
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    public function register_module_customizer_settings($wp_customize) {
        foreach ($this->modules as $module) {
            if (method_exists($module, 'customize_register')) {
                $module->customize_register($wp_customize);
            }
        }
    }
}

/**
 * Initialize the theme
 */
function oltreblocksy_init() {
    return OltreBlocksy_Theme::get_instance();
}

// Start the theme
oltreblocksy_init();

/**
 * Helper function to get theme instance
 * 
 * @return OltreBlocksy_Theme
 */
function oltreblocksy() {
    return OltreBlocksy_Theme::get_instance();
}