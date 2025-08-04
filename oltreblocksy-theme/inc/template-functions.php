<?php
/**
 * Template Functions
 *
 * Functions that alter core WordPress functionality for the theme
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Add custom classes to the body tag
 *
 * @param array $classes Existing body classes
 * @return array Modified body classes
 */
function oltreblocksy_body_classes($classes) {
    // Add class for JavaScript detection
    $classes[] = 'no-js';
    
    // Add class for layout type
    if (is_singular()) {
        $classes[] = 'layout-single';
    } elseif (is_archive() || is_home()) {
        $classes[] = 'layout-archive';
    }
    
    // Add class for sidebar
    if (is_active_sidebar('sidebar-1')) {
        $classes[] = 'has-sidebar';
    } else {
        $classes[] = 'no-sidebar';
    }
    
    // Add class for header style
    $header_style = oltreblocksy_get_theme_mod('header_style', 'default');
    $classes[] = 'header-' . $header_style;
    
    // Add class for dark mode
    if (oltreblocksy_is_dark_mode()) {
        $classes[] = 'dark-mode';
    }
    
    // Add class for reduced motion preference
    if (oltreblocksy_prefers_reduced_motion()) {
        $classes[] = 'prefers-reduced-motion';
    }
    
    return array_unique($classes);
}
add_filter('body_class', 'oltreblocksy_body_classes');

/**
 * Add custom classes to post containers
 *
 * @param array $classes Existing post classes
 * @param array $class Additional classes
 * @param int   $post_id Post ID
 * @return array Modified post classes
 */
function oltreblocksy_post_classes($classes, $class, $post_id) {
    // Add reading time class
    $reading_time = oltreblocksy_get_reading_time(get_post_field('post_content', $post_id));
    $classes[] = 'reading-time-' . $reading_time;
    
    // Add featured image class
    if (has_post_thumbnail($post_id)) {
        $classes[] = 'has-featured-image';
    }
    
    // Add comment status class
    if (comments_open($post_id)) {
        $classes[] = 'comments-open';
    }
    
    return $classes;
}
add_filter('post_class', 'oltreblocksy_post_classes', 10, 3);

/**
 * Customize excerpt length
 *
 * @param int $length Current excerpt length
 * @return int New excerpt length
 */
function oltreblocksy_excerpt_length($length) {
    if (is_admin()) {
        return $length;
    }
    
    $custom_length = oltreblocksy_get_theme_mod('excerpt_length', 25);
    return absint($custom_length);
}
add_filter('excerpt_length', 'oltreblocksy_excerpt_length');

/**
 * Customize excerpt more link
 *
 * @param string $more Current more link
 * @return string New more link
 */
function oltreblocksy_excerpt_more($more) {
    if (is_admin()) {
        return $more;
    }
    
    $more_text = oltreblocksy_get_theme_mod('excerpt_more_text', __('Read more', 'oltreblocksy'));
    
    return '&hellip; <a href="' . esc_url(get_permalink()) . '" class="more-link" aria-label="' . 
           esc_attr(sprintf(__('Read more about %s', 'oltreblocksy'), get_the_title())) . '">' . 
           esc_html($more_text) . ' ' . oltreblocksy_get_svg_icon('arrow-right', 16) . '</a>';
}
add_filter('excerpt_more', 'oltreblocksy_excerpt_more');

/**
 * Add custom image sizes to media library
 *
 * @param array $sizes Existing image sizes
 * @return array Modified image sizes
 */
function oltreblocksy_custom_image_sizes($sizes) {
    return array_merge($sizes, array(
        'hero' => __('Hero (1920x1080)', 'oltreblocksy'),
        'card' => __('Card (600x400)', 'oltreblocksy'),
        'thumbnail-large' => __('Large Thumbnail (300x300)', 'oltreblocksy'),
    ));
}
add_filter('image_size_names_choose', 'oltreblocksy_custom_image_sizes');

/**
 * Customize navigation menu markup
 *
 * @param string $nav_menu Navigation menu HTML
 * @param object $args Navigation menu arguments
 * @return string Modified navigation menu HTML
 */
function oltreblocksy_nav_menu_css_class($classes, $item, $args, $depth) {
    // Add depth class for styling
    $classes[] = 'menu-depth-' . $depth;
    
    // Add class for menu items with children
    if (in_array('menu-item-has-children', $classes)) {
        $classes[] = 'has-dropdown';
    }
    
    // Add class for current page ancestors
    if (in_array('current-page-ancestor', $classes)) {
        $classes[] = 'is-ancestor';
    }
    
    return $classes;
}
add_filter('nav_menu_css_class', 'oltreblocksy_nav_menu_css_class', 10, 4);

/**
 * Add dropdown toggle button to menu items with children
 *
 * @param string $item_output Menu item HTML
 * @param object $item Menu item object
 * @param int    $depth Menu depth
 * @param object $args Menu arguments
 * @return string Modified menu item HTML
 */
function oltreblocksy_nav_menu_item_output($item_output, $item, $depth, $args) {
    // Only add toggle for primary menu with children
    if (isset($args->theme_location) && $args->theme_location === 'primary') {
        if (in_array('menu-item-has-children', $item->classes)) {
            $toggle_button = '<button class="dropdown-toggle" aria-expanded="false" aria-label="' . 
                           esc_attr__('Toggle submenu', 'oltreblocksy') . '">' . 
                           oltreblocksy_get_svg_icon('chevron-down', 12) . '</button>';
            $item_output .= $toggle_button;
        }
    }
    
    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'oltreblocksy_nav_menu_item_output', 10, 4);

/**
 * Customize comment form fields
 *
 * @param array $fields Comment form fields
 * @return array Modified comment form fields
 */
function oltreblocksy_comment_form_fields($fields) {
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? ' aria-required="true"' : '');
    
    $fields['author'] = '<div class="comment-form-author"><label for="author">' . 
                       esc_html__('Name', 'oltreblocksy') . 
                       ($req ? ' <span class="required">*</span>' : '') . '</label>' .
                       '<input id="author" name="author" type="text" value="' . 
                       esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . 
                       ' placeholder="' . esc_attr__('Your name', 'oltreblocksy') . '"></div>';
    
    $fields['email'] = '<div class="comment-form-email"><label for="email">' . 
                      esc_html__('Email', 'oltreblocksy') . 
                      ($req ? ' <span class="required">*</span>' : '') . '</label>' .
                      '<input id="email" name="email" type="email" value="' . 
                      esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . 
                      ' placeholder="' . esc_attr__('your.email@example.com', 'oltreblocksy') . '"></div>';
    
    $fields['url'] = '<div class="comment-form-url"><label for="url">' . 
                    esc_html__('Website', 'oltreblocksy') . '</label>' .
                    '<input id="url" name="url" type="url" value="' . 
                    esc_attr($commenter['comment_author_url']) . '" size="30" placeholder="' . 
                    esc_attr__('https://yourwebsite.com', 'oltreblocksy') . '"></div>';
    
    return $fields;
}
add_filter('comment_form_default_fields', 'oltreblocksy_comment_form_fields');

/**
 * Customize comment form defaults
 *
 * @param array $defaults Comment form defaults
 * @return array Modified comment form defaults
 */
function oltreblocksy_comment_form_defaults($defaults) {
    $defaults['comment_field'] = '<div class="comment-form-comment"><label for="comment">' . 
                                esc_html__('Comment', 'oltreblocksy') . ' <span class="required">*</span></label>' .
                                '<textarea id="comment" name="comment" cols="45" rows="8" required placeholder="' . 
                                esc_attr__('Share your thoughts...', 'oltreblocksy') . '"></textarea></div>';
    
    $defaults['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s">';
    $defaults['submit_field'] = '<div class="form-submit">%1$s %2$s</div>';
    
    return $defaults;
}
add_filter('comment_form_defaults', 'oltreblocksy_comment_form_defaults');

/**
 * Add schema markup to articles
 *
 * @param array $attributes HTML attributes
 * @param string $context Context
 * @return array Modified attributes
 */
function oltreblocksy_add_schema_markup($attributes, $context = '') {
    if (is_singular('post')) {
        $attributes['itemtype'] = 'https://schema.org/BlogPosting';
        $attributes['itemscope'] = true;
    } elseif (is_page()) {
        $attributes['itemtype'] = 'https://schema.org/WebPage';
        $attributes['itemscope'] = true;
    }
    
    return $attributes;
}

/**
 * Optimize images for performance
 *
 * @param array $attr Image attributes
 * @param object $attachment Attachment object
 * @param string $size Image size
 * @return array Modified image attributes
 */
function oltreblocksy_optimize_images($attr, $attachment, $size) {
    // Add loading="lazy" for non-critical images
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    
    // Add decoding="async" for better performance
    if (!isset($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }
    
    // Improve alt text if empty
    if (empty($attr['alt'])) {
        $attr['alt'] = get_the_title($attachment->ID) ?: '';
    }
    
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'oltreblocksy_optimize_images', 10, 3);

/**
 * Remove unnecessary WordPress features for performance
 */
function oltreblocksy_cleanup_wp_head() {
    // Remove unnecessary meta tags
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    
    // Remove emoji support if not needed
    if (!oltreblocksy_get_theme_mod('enable_emojis', false)) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
    }
}
add_action('init', 'oltreblocksy_cleanup_wp_head');

/**
 * Add custom meta tags for SEO and social sharing
 */
function oltreblocksy_add_meta_tags() {
    if (is_singular()) {
        $post_id = get_queried_object_id();
        $title = get_the_title($post_id);
        $description = get_the_excerpt($post_id) ?: wp_trim_words(get_post_field('post_content', $post_id), 20);
        $image = get_the_post_thumbnail_url($post_id, 'large');
        
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink($post_id)) . '">' . "\n";
        
        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        }
        
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'oltreblocksy_add_meta_tags', 5);