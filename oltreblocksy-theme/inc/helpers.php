<?php
/**
 * Helper Functions
 *
 * Collection of utility functions used throughout the theme
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Check if a plugin is active
 *
 * @param string $plugin Plugin path
 * @return bool
 */
function oltreblocksy_is_plugin_active($plugin) {
    return in_array($plugin, (array) get_option('active_plugins', array())) ||
           is_plugin_active_for_network($plugin);
}

/**
 * Get SVG icon
 *
 * @param string $icon Icon name
 * @param int    $size Icon size
 * @return string SVG markup
 */
function oltreblocksy_get_svg_icon($icon, $size = 24) {
    $icons = array(
        'arrow-right' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>',
        'arrow-left' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>',
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
        'menu' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>',
        'close' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>',
        'chevron-down' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>',
        'chevron-right' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
        'external-link' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>',
        'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>',
        'user' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'folder' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>',
        'tag' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><circle cx="7" cy="7" r="1.5"/></svg>',
        'message-circle' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
        'clock' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>',
        'edit' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="m18.5 2.5 3 3L12 15l-4 1 1-4Z"/></svg>',
    );
    
    return isset($icons[$icon]) ? $icons[$icon] : '';
}

/**
 * Sanitize HTML classes
 *
 * @param string|array $classes Classes to sanitize
 * @return string Sanitized classes
 */
function oltreblocksy_sanitize_html_classes($classes) {
    if (is_array($classes)) {
        $classes = implode(' ', $classes);
    }
    
    return sanitize_html_class($classes);
}

/**
 * Get reading time estimate
 *
 * @param string $content Post content
 * @return int Reading time in minutes
 */
function oltreblocksy_get_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words per minute
    
    return max(1, $reading_time);
}

/**
 * Get optimized image attributes
 *
 * @param int    $attachment_id Image attachment ID
 * @param string $size Image size
 * @param array  $attr Additional attributes
 * @return array Image attributes
 */
function oltreblocksy_get_optimized_image_attrs($attachment_id, $size = 'large', $attr = array()) {
    $image = wp_get_attachment_image_src($attachment_id, $size);
    
    if (!$image) {
        return array();
    }
    
    $default_attrs = array(
        'src' => $image[0],
        'width' => $image[1],
        'height' => $image[2],
        'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
        'loading' => 'lazy',
        'decoding' => 'async',
    );
    
    // Generate srcset for responsive images
    $srcset = wp_get_attachment_image_srcset($attachment_id, $size);
    if ($srcset) {
        $default_attrs['srcset'] = $srcset;
        $default_attrs['sizes'] = wp_get_attachment_image_sizes($attachment_id, $size);
    }
    
    return array_merge($default_attrs, $attr);
}

/**
 * Generate critical CSS for above-the-fold content
 *
 * @return string Critical CSS
 */
function oltreblocksy_get_critical_css() {
    $critical_css = "
        :root{
            --color-primary: #1e40af;
            --color-text: #1e293b;
            --color-background: #f8fafc;
            --font-system: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box}
        body{margin:0;font-family:var(--font-system);color:var(--color-text);background-color:var(--color-background)}
        .container{max-width:min(100% - 2rem, 1200px);margin:0 auto}
        .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
    ";
    
    return apply_filters('oltreblocksy_critical_css', $critical_css);
}

/**
 * Check if current page should load specific assets
 *
 * @param string $asset_type Asset type to check
 * @return bool
 */
function oltreblocksy_should_load_asset($asset_type) {
    switch ($asset_type) {
        case 'homepage':
            return is_front_page();
        case 'blog':
            return is_home() || is_archive() || is_search();
        case 'single':
            return is_singular();
        case 'comments':
            return is_singular() && comments_open() && get_option('thread_comments');
        case 'contact':
            return is_page_template('page-contact.php') || is_page('contact');
        default:
            return false;
    }
}

/**
 * Get theme performance metrics
 *
 * @return array Performance metrics
 */
function oltreblocksy_get_performance_metrics() {
    $metrics = array(
        'memory_usage' => memory_get_usage(true),
        'peak_memory' => memory_get_peak_usage(true),
        'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
        'queries_count' => get_num_queries(),
    );
    
    return apply_filters('oltreblocksy_performance_metrics', $metrics);
}

/**
 * Log performance data for debugging
 *
 * @param array $data Performance data
 */
function oltreblocksy_log_performance($data) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('OltreBlocksy Performance: ' . wp_json_encode($data));
    }
}

/**
 * Get breakpoint value
 *
 * @param string $breakpoint Breakpoint name
 * @return string Breakpoint value
 */
function oltreblocksy_get_breakpoint($breakpoint) {
    $breakpoints = array(
        'sm' => '640px',
        'md' => '768px',
        'lg' => '1024px',
        'xl' => '1280px',
        '2xl' => '1536px',
    );
    
    return isset($breakpoints[$breakpoint]) ? $breakpoints[$breakpoint] : '';
}

/**
 * Check if user prefers reduced motion
 *
 * @return bool
 */
function oltreblocksy_prefers_reduced_motion() {
    return isset($_COOKIE['prefers-reduced-motion']) && $_COOKIE['prefers-reduced-motion'] === 'reduce';
}

/**
 * Generate unique ID for elements
 *
 * @param string $prefix ID prefix
 * @return string Unique ID
 */
function oltreblocksy_generate_id($prefix = 'oltreblocksy') {
    static $id_counter = 0;
    $id_counter++;
    
    return $prefix . '-' . $id_counter . '-' . wp_rand(1000, 9999);
}

/**
 * Compress and minify CSS
 *
 * @param string $css CSS content
 * @return string Minified CSS
 */
function oltreblocksy_minify_css($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // Remove extra whitespace
    $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
    // Remove unnecessary semicolons
    $css = str_replace(';}', '}', $css);
    
    return trim($css);
}

/**
 * Get theme customizer value with fallback
 *
 * @param string $setting Setting name
 * @param mixed  $default Default value
 * @return mixed Setting value
 */
function oltreblocksy_get_theme_mod($setting, $default = null) {
    return get_theme_mod($setting, $default);
}

/**
 * Check if dark mode is enabled
 *
 * @return bool
 */
function oltreblocksy_is_dark_mode() {
    $dark_mode_setting = oltreblocksy_get_theme_mod('dark_mode_setting', 'auto');
    
    if ($dark_mode_setting === 'enabled') {
        return true;
    } elseif ($dark_mode_setting === 'disabled') {
        return false;
    }
    
    // Auto mode - check user preference via JavaScript (stored in cookie/localStorage)
    return isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] === 'true';
}

/**
 * Display custom logo
 */
function oltreblocksy_custom_logo() {
    if (has_custom_logo()) {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo_attrs = oltreblocksy_get_optimized_image_attrs($custom_logo_id, 'full', array(
            'class' => 'custom-logo',
            'loading' => 'eager' // Logo should load immediately
        ));
        
        echo '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link" rel="home" itemscope itemtype="https://schema.org/Organization">';
        echo '<img';
        foreach ($logo_attrs as $attr => $value) {
            echo ' ' . esc_attr($attr) . '="' . esc_attr($value) . '"';
        }
        echo ' itemprop="logo">';
        echo '</a>';
    }
}

/**
 * Display breadcrumbs
 */
function oltreblocksy_breadcrumbs() {
    if (is_front_page()) {
        return;
    }
    
    $breadcrumbs = array();
    $breadcrumbs[] = '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'oltreblocksy') . '</a>';
    
    if (is_category() || is_single()) {
        $category = get_the_category();
        if (!empty($category)) {
            $breadcrumbs[] = '<a href="' . esc_url(get_category_link($category[0]->term_id)) . '">' . esc_html($category[0]->name) . '</a>';
        }
        
        if (is_single()) {
            $breadcrumbs[] = '<span class="current">' . get_the_title() . '</span>';
        }
    } elseif (is_page()) {
        if ($post = get_post(get_the_ID())) {
            if ($post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs_temp = array();
                while ($parent_id) {
                    $page = get_post($parent_id);
                    $breadcrumbs_temp[] = '<a href="' . esc_url(get_permalink($page->ID)) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_merge($breadcrumbs, array_reverse($breadcrumbs_temp));
            }
            $breadcrumbs[] = '<span class="current">' . get_the_title() . '</span>';
        }
    } elseif (is_tag()) {
        $breadcrumbs[] = '<span class="current">' . single_tag_title('', false) . '</span>';
    } elseif (is_author()) {
        $breadcrumbs[] = '<span class="current">' . get_the_author() . '</span>';
    } elseif (is_404()) {
        $breadcrumbs[] = '<span class="current">404</span>';
    }
    
    if (!empty($breadcrumbs)) {
        echo '<nav class="breadcrumbs container" aria-label="' . esc_attr__('Breadcrumb navigation', 'oltreblocksy') . '">';
        echo '<ol class="breadcrumb-list">';
        foreach ($breadcrumbs as $breadcrumb) {
            echo '<li class="breadcrumb-item">' . $breadcrumb . '</li>';
        }
        echo '</ol>';
        echo '</nav>';
    }
}