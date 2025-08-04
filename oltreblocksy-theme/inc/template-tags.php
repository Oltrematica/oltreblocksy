<?php
/**
 * Template Tags
 *
 * Custom template tags used throughout the theme
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

if (!function_exists('oltreblocksy_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time
     */
    function oltreblocksy_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s" itemprop="datePublished">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s" itemprop="datePublished">%2$s</time><time class="updated" datetime="%3$s" itemprop="dateModified">%4$s</time>';
        }

        $time_string = sprintf($time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x('Posted on %s', 'post date', 'oltreblocksy'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . oltreblocksy_get_svg_icon('calendar', 16) . ' ' . $posted_on . '</span>';
    }
endif;

if (!function_exists('oltreblocksy_posted_by')) :
    /**
     * Prints HTML with meta information for the current author
     */
    function oltreblocksy_posted_by() {
        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x('by %s', 'post author', 'oltreblocksy'),
            '<span class="author vcard" itemprop="author" itemscope itemtype="https://schema.org/Person">' .
            '<a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '" itemprop="url">' .
            '<span itemprop="name">' . esc_html(get_the_author()) . '</span></a></span>'
        );

        echo '<span class="byline"> ' . oltreblocksy_get_svg_icon('user', 16) . ' ' . $byline . '</span>';
    }
endif;

if (!function_exists('oltreblocksy_entry_footer')) :
    /**
     * Prints HTML with meta information for categories, tags and comments
     */
    function oltreblocksy_entry_footer() {
        // Hide category and tag text for pages
        if ('post' === get_post_type()) {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'oltreblocksy'));
            if ($categories_list) {
                /* translators: 1: list of categories. */
                printf('<span class="cat-links">' . oltreblocksy_get_svg_icon('folder', 16) . ' ' . esc_html__('Posted in %1$s', 'oltreblocksy') . '</span>', $categories_list);
            }

            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'oltreblocksy'));
            if ($tags_list) {
                /* translators: 1: list of tags. */
                printf('<span class="tags-links">' . oltreblocksy_get_svg_icon('tag', 16) . ' ' . esc_html__('Tagged %1$s', 'oltreblocksy') . '</span>', $tags_list);
            }
        }

        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<span class="comments-link">';
            echo oltreblocksy_get_svg_icon('message-circle', 16) . ' ';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'oltreblocksy'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                )
            );
            echo '</span>';
        }

        // Add reading time
        $reading_time = oltreblocksy_get_reading_time(get_the_content());
        if ($reading_time > 0) {
            printf(
                '<span class="reading-time">' . oltreblocksy_get_svg_icon('clock', 16) . ' ' . 
                /* translators: %s: reading time in minutes */
                esc_html(_n('%s min read', '%s min read', $reading_time, 'oltreblocksy')) . '</span>',
                number_format_i18n($reading_time)
            );
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Edit <span class="screen-reader-text">%s</span>', 'oltreblocksy'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            ),
            '<span class="edit-link">' . oltreblocksy_get_svg_icon('edit', 16) . ' ',
            '</span>'
        );
    }
endif;

if (!function_exists('oltreblocksy_post_thumbnail')) :
    /**
     * Displays an optional post thumbnail with optimized loading
     */
    function oltreblocksy_post_thumbnail($size = 'large', $attr = array()) {
        if (!has_post_thumbnail()) {
            return;
        }

        $default_attr = array(
            'loading' => is_singular() ? 'eager' : 'lazy',
            'decoding' => 'async',
        );
        
        $attr = wp_parse_args($attr, $default_attr);

        if (is_singular()) :
            ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail($size, $attr); ?>
            </div>
            <?php
        else :
            ?>
            <div class="post-thumbnail">
                <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                    <?php the_post_thumbnail($size, $attr); ?>
                </a>
            </div>
            <?php
        endif;
    }
endif;

if (!function_exists('oltreblocksy_custom_logo')) :
    /**
     * Display custom logo with enhanced markup
     */
    function oltreblocksy_custom_logo() {
        if (!has_custom_logo()) {
            return;
        }

        $custom_logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
        $logo_alt = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true);
        
        if (empty($logo_alt)) {
            $logo_alt = get_bloginfo('name', 'display');
        }

        printf(
            '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">' .
            '<img src="%2$s" class="custom-logo" alt="%3$s" width="auto" height="40" loading="eager" decoding="async">' .
            '</a>',
            esc_url(home_url('/')),
            esc_url($logo_url),
            esc_attr($logo_alt)
        );
    }
endif;

if (!function_exists('oltreblocksy_breadcrumbs')) :
    /**
     * Display breadcrumb navigation
     */
    function oltreblocksy_breadcrumbs() {
        if (is_front_page()) {
            return;
        }

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'title' => __('Home', 'oltreblocksy'),
            'url' => home_url('/'),
        );

        if (is_category() || is_single()) {
            $categories = get_the_category();
            if (!empty($categories)) {
                $category = $categories[0];
                $breadcrumbs[] = array(
                    'title' => $category->name,
                    'url' => get_category_link($category->term_id),
                );
            }
        }

        if (is_single()) {
            $breadcrumbs[] = array(
                'title' => get_the_title(),
                'url' => '',
            );
        } elseif (is_page()) {
            $page_ancestors = get_post_ancestors(get_the_ID());
            if (!empty($page_ancestors)) {
                foreach (array_reverse($page_ancestors) as $ancestor) {
                    $breadcrumbs[] = array(
                        'title' => get_the_title($ancestor),
                        'url' => get_permalink($ancestor),
                    );
                }
            }
            $breadcrumbs[] = array(
                'title' => get_the_title(),
                'url' => '',
            );
        } elseif (is_category()) {
            $breadcrumbs[] = array(
                'title' => single_cat_title('', false),
                'url' => '',
            );
        } elseif (is_tag()) {
            $breadcrumbs[] = array(
                'title' => single_tag_title('', false),
                'url' => '',
            );
        } elseif (is_archive()) {
            $breadcrumbs[] = array(
                'title' => get_the_archive_title(),
                'url' => '',
            );
        } elseif (is_search()) {
            $breadcrumbs[] = array(
                'title' => sprintf(__('Search Results for "%s"', 'oltreblocksy'), get_search_query()),
                'url' => '',
            );
        } elseif (is_404()) {
            $breadcrumbs[] = array(
                'title' => __('404 Not Found', 'oltreblocksy'),
                'url' => '',
            );
        }

        if (empty($breadcrumbs)) {
            return;
        }

        echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'oltreblocksy') . '" itemscope itemtype="https://schema.org/BreadcrumbList">';
        echo '<ol class="breadcrumb-list">';

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $position = $index + 1;
            echo '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            
            if (!empty($breadcrumb['url'])) {
                printf(
                    '<a href="%s" itemprop="item"><span itemprop="name">%s</span></a>',
                    esc_url($breadcrumb['url']),
                    esc_html($breadcrumb['title'])
                );
            } else {
                printf('<span itemprop="name">%s</span>', esc_html($breadcrumb['title']));
            }
            
            echo '<meta itemprop="position" content="' . $position . '">';
            echo '</li>';
            
            if ($index < count($breadcrumbs) - 1) {
                echo '<li class="breadcrumb-separator" aria-hidden="true">' . oltreblocksy_get_svg_icon('chevron-right', 12) . '</li>';
            }
        }

        echo '</ol>';
        echo '</nav>';
    }
endif;

if (!function_exists('oltreblocksy_social_menu')) :
    /**
     * Output social menu with icons
     */
    function oltreblocksy_social_menu() {
        if (!has_nav_menu('social')) {
            return;
        }

        wp_nav_menu(array(
            'theme_location' => 'social',
            'menu_class' => 'social-menu',
            'container' => 'nav',
            'container_class' => 'social-navigation',
            'container_id' => 'social-navigation',
            'depth' => 1,
            'link_before' => '<span class="screen-reader-text">',
            'link_after' => '</span>',
            'fallback_cb' => false,
        ));
    }
endif;

if (!function_exists('oltreblocksy_pagination')) :
    /**
     * Custom pagination with improved accessibility
     */
    function oltreblocksy_pagination() {
        global $wp_query;

        if ($wp_query->max_num_pages <= 1) {
            return;
        }

        $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
        $max = intval($wp_query->max_num_pages);

        echo '<nav class="pagination-navigation" role="navigation" aria-label="' . esc_attr__('Posts navigation', 'oltreblocksy') . '">';
        echo '<div class="nav-links">';

        // Previous page link
        if ($paged > 1) {
            $prev_text = oltreblocksy_get_svg_icon('arrow-left', 16) . ' ' . __('Previous', 'oltreblocksy');
            echo '<a href="' . esc_url(get_pagenum_link($paged - 1)) . '" class="prev page-numbers" aria-label="' . esc_attr__('Previous page', 'oltreblocksy') . '">' . $prev_text . '</a>';
        }

        // Page numbers
        for ($i = 1; $i <= $max; $i++) {
            if ($i == $paged) {
                echo '<span class="page-numbers current" aria-current="page" aria-label="' . esc_attr(sprintf(__('Page %d', 'oltreblocksy'), $i)) . '">' . $i . '</span>';
            } else {
                echo '<a href="' . esc_url(get_pagenum_link($i)) . '" class="page-numbers" aria-label="' . esc_attr(sprintf(__('Go to page %d', 'oltreblocksy'), $i)) . '">' . $i . '</a>';
            }
        }

        // Next page link
        if ($paged < $max) {
            $next_text = __('Next', 'oltreblocksy') . ' ' . oltreblocksy_get_svg_icon('arrow-right', 16);
            echo '<a href="' . esc_url(get_pagenum_link($paged + 1)) . '" class="next page-numbers" aria-label="' . esc_attr__('Next page', 'oltreblocksy') . '">' . $next_text . '</a>';
        }

        echo '</div>';
        echo '</nav>';
    }
endif;

if (!function_exists('oltreblocksy_search_form')) :
    /**
     * Custom search form with improved accessibility
     */
    function oltreblocksy_search_form() {
        $unique_id = oltreblocksy_generate_id('search');
        $search_text = __('Search', 'oltreblocksy');
        $placeholder = __('Search...', 'oltreblocksy');
        
        echo '<form role="search" method="get" class="search-form" action="' . esc_url(home_url('/')) . '">';
        echo '<label for="' . $unique_id . '" class="screen-reader-text">' . $search_text . '</label>';
        echo '<input type="search" id="' . $unique_id . '" class="search-field" placeholder="' . esc_attr($placeholder) . '" value="' . get_search_query() . '" name="s" required>';
        echo '<button type="submit" class="search-submit" aria-label="' . esc_attr($search_text) . '">';
        echo oltreblocksy_get_svg_icon('search', 20);
        echo '<span class="screen-reader-text">' . $search_text . '</span>';
        echo '</button>';
        echo '</form>';
    }
endif;