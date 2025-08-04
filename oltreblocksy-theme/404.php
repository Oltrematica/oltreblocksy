<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header(); ?>

<main id="primary" class="site-main error-404" role="main">
    <div class="container">
        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('404', 'oltreblocksy'); ?></h1>
                <p class="page-subtitle"><?php esc_html_e('Oops! That page can't be found.', 'oltreblocksy'); ?></p>
            </header>

            <div class="page-content">
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'oltreblocksy'); ?></p>

                <?php get_search_form(); ?>

                <div class="widget widget_recent_entries">
                    <h2 class="widget-title"><?php esc_html_e('Recent Posts', 'oltreblocksy'); ?></h2>
                    <ul>
                        <?php
                        wp_get_archives(array(
                            'type' => 'postbypost',
                            'limit' => 5,
                            'format' => 'html',
                        ));
                        ?>
                    </ul>
                </div>

                <div class="widget widget_categories">
                    <h2 class="widget-title"><?php esc_html_e('Categories', 'oltreblocksy'); ?></h2>
                    <ul>
                        <?php
                        wp_list_categories(array(
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'show_count' => 1,
                            'title_li' => '',
                            'number' => 10,
                        ));
                        ?>
                    </ul>
                </div>

                <div class="widget">
                    <h2 class="widget-title"><?php esc_html_e('Archives', 'oltreblocksy'); ?></h2>
                    <ul>
                        <?php
                        wp_get_archives(array(
                            'type' => 'monthly',
                            'limit' => 12,
                        ));
                        ?>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer();