<?php
/**
 * Base Module Class
 *
 * Abstract base class for all theme modules
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

namespace OltreBlocksy\Modules;

defined('ABSPATH') || exit;

abstract class Base_Module {
    
    /**
     * Module name
     * 
     * @var string
     */
    protected $name = '';
    
    /**
     * Module version
     * 
     * @var string
     */
    protected $version = '1.0.0';
    
    /**
     * Module dependencies
     * 
     * @var array
     */
    protected $dependencies = array();
    
    /**
     * Module enabled status
     * 
     * @var bool
     */
    protected $enabled = true;
    
    /**
     * Module settings
     * 
     * @var array
     */
    protected $settings = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        try {
            $this->name = $this->get_name();
            $this->load_settings();
            
            if ($this->is_enabled() && $this->check_dependencies()) {
                $this->init();
            }
        } catch (\Exception $e) {
            $this->log('Module initialization failed: ' . $e->getMessage(), 'error');
        }
    }
    
    /**
     * Get module name
     * 
     * @return string Module name
     */
    abstract protected function get_name();
    
    /**
     * Initialize module
     */
    abstract protected function init();
    
    /**
     * Check if module is enabled
     * 
     * @return bool
     */
    public function is_enabled() {
        $option_name = 'oltreblocksy_module_' . strtolower($this->name) . '_enabled';
        return apply_filters('oltreblocksy_module_enabled', 
            get_theme_mod($option_name, $this->enabled), 
            $this->name
        );
    }
    
    /**
     * Enable module
     */
    public function enable() {
        $option_name = 'oltreblocksy_module_' . strtolower($this->name) . '_enabled';
        set_theme_mod($option_name, true);
        $this->enabled = true;
    }
    
    /**
     * Disable module
     */
    public function disable() {
        $option_name = 'oltreblocksy_module_' . strtolower($this->name) . '_enabled';
        set_theme_mod($option_name, false);
        $this->enabled = false;
    }
    
    /**
     * Check module dependencies
     * 
     * @return bool
     */
    protected function check_dependencies() {
        foreach ($this->dependencies as $dependency) {
            if (!$this->is_dependency_available($dependency)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Check if dependency is available
     * 
     * @param string $dependency Dependency to check
     * @return bool
     */
    protected function is_dependency_available($dependency) {
        switch ($dependency['type']) {
            case 'plugin':
                return is_plugin_active($dependency['path']);
                
            case 'function':
                return function_exists($dependency['name']);
                
            case 'class':
                return class_exists($dependency['name']);
                
            case 'module':
                return oltreblocksy()->get_module($dependency['name']) !== null;
                
            default:
                return true;
        }
    }
    
    /**
     * Load module settings
     */
    protected function load_settings() {
        $settings_key = 'oltreblocksy_module_' . strtolower($this->name) . '_settings';
        $this->settings = get_theme_mod($settings_key, array());
    }
    
    /**
     * Save module settings
     * 
     * @param array $settings Settings to save
     */
    public function save_settings($settings) {
        $settings_key = 'oltreblocksy_module_' . strtolower($this->name) . '_settings';
        set_theme_mod($settings_key, $settings);
        $this->settings = $settings;
    }
    
    /**
     * Get module setting
     * 
     * @param string $key Setting key
     * @param mixed  $default Default value
     * @return mixed Setting value
     */
    public function get_setting($key, $default = null) {
        return isset($this->settings[$key]) ? $this->settings[$key] : $default;
    }
    
    /**
     * Set module setting
     * 
     * @param string $key Setting key
     * @param mixed  $value Setting value
     */
    public function set_setting($key, $value) {
        $this->settings[$key] = $value;
        $this->save_settings($this->settings);
    }
    
    /**
     * Get module info
     * 
     * @return array Module information
     */
    public function get_info() {
        return array(
            'name' => $this->name,
            'version' => $this->version,
            'enabled' => $this->is_enabled(),
            'dependencies' => $this->dependencies,
            'settings' => $this->settings,
        );
    }
    
    /**
     * Enqueue module assets
     */
    protected function enqueue_assets() {
        $module_slug = strtolower(str_replace('_', '-', $this->name));
        
        // Enqueue CSS if exists
        $css_file = OLTREBLOCKSY_ASSETS_URI . '/css/modules/' . $module_slug . '.css';
        if (file_exists(OLTREBLOCKSY_THEME_DIR . '/assets/css/modules/' . $module_slug . '.css')) {
            wp_enqueue_style(
                'oltreblocksy-module-' . $module_slug,
                $css_file,
                array('oltreblocksy-style'),
                $this->version
            );
        }
        
        // Enqueue JavaScript if exists
        $js_file = OLTREBLOCKSY_ASSETS_URI . '/js/modules/' . $module_slug . '.js';
        if (file_exists(OLTREBLOCKSY_THEME_DIR . '/assets/js/modules/' . $module_slug . '.js')) {
            wp_enqueue_script(
                'oltreblocksy-module-' . $module_slug,
                $js_file,
                array('oltreblocksy-main'),
                $this->version,
                true
            );
        }
    }
    
    /**
     * Add module to customizer
     * 
     * @param WP_Customize_Manager $wp_customize Customizer instance
     */
    public function customize_register($wp_customize) {
        // Override in child classes to add customizer controls
    }
    
    /**
     * Log module messages
     * 
     * @param string $message Log message
     * @param string $level Log level (info, warning, error)
     */
    protected function log($message, $level = 'info') {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf('[OltreBlocksy %s Module - %s] %s', $this->name, strtoupper($level), $message));
        }
    }
    
    /**
     * Handle AJAX requests for this module
     */
    public function handle_ajax() {
        // Override in child classes to handle AJAX
    }
    
    /**
     * Module activation hook
     */
    public function activate() {
        $this->log('Module activated');
    }
    
    /**
     * Module deactivation hook
     */
    public function deactivate() {
        $this->log('Module deactivated');
    }
}