<?php
/**
 * PHPUnit Bootstrap File for OltreBlocksy Theme Testing
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

// Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Setup WordPress test environment
if (!defined('WP_TESTS_CONFIG_FILE_PATH')) {
    define('WP_TESTS_CONFIG_FILE_PATH', dirname(__DIR__) . '/wp-tests-config.php');
}

// WordPress constants for testing
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}

if (!defined('WP_DEBUG_LOG')) {
    define('WP_DEBUG_LOG', true);
}

// Theme constants
if (!defined('OLTREBLOCKSY_VERSION')) {
    define('OLTREBLOCKSY_VERSION', '1.0.0');
}

if (!defined('OLTREBLOCKSY_THEME_DIR')) {
    define('OLTREBLOCKSY_THEME_DIR', dirname(__DIR__) . '/oltreblocksy-theme');
}

if (!defined('OLTREBLOCKSY_THEME_URI')) {
    define('OLTREBLOCKSY_THEME_URI', 'http://localhost/oltreblocksy-theme');
}

if (!defined('OLTREBLOCKSY_INC_DIR')) {
    define('OLTREBLOCKSY_INC_DIR', OLTREBLOCKSY_THEME_DIR . '/inc');
}

if (!defined('OLTREBLOCKSY_ASSETS_URI')) {
    define('OLTREBLOCKSY_ASSETS_URI', OLTREBLOCKSY_THEME_URI . '/assets');
}

// Initialize Brain Monkey
\Brain\Monkey\setUp();

// WordPress function mocks for testing
if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data, $options = 0, $depth = 512) {
        return json_encode($data, $options, $depth);
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('sanitize_hex_color')) {
    function sanitize_hex_color($color) {
        if ('' === $color) {
            return '';
        }
        
        if (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color)) {
            return $color;
        }
        
        return null;
    }
}

if (!function_exists('sanitize_key')) {
    function sanitize_key($key) {
        return preg_replace('/[^a-z0-9_\-]/', '', strtolower($key));
    }
}

if (!function_exists('wp_validate_boolean')) {
    function wp_validate_boolean($var) {
        if (is_bool($var)) {
            return $var;
        }
        
        if (is_string($var) && 'false' === strtolower($var)) {
            return false;
        }
        
        return (bool) $var;
    }
}

if (!function_exists('get_theme_mod')) {
    function get_theme_mod($name, $default = false) {
        return $default;
    }
}

if (!function_exists('set_theme_mod')) {
    function set_theme_mod($name, $value) {
        return true;
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters($hook_name, $value, ...$args) {
        return $value;
    }
}

if (!function_exists('add_action')) {
    function add_action($hook_name, $callback, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hook_name, $callback, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return esc_html($text);
    }
}

if (!function_exists('sprintf')) {
    // sprintf already exists in PHP
}

if (!function_exists('error_log')) {
    // error_log already exists in PHP
}

// Cleanup function
register_shutdown_function(function() {
    \Brain\Monkey\tearDown();
});