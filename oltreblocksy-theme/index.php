<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header(); ?>

<main id="primary" class="site-main" role="main" aria-label="<?php esc_attr_e('Main content', 'oltreblocksy'); ?>">
    <div class="content-area<?php echo is_active_sidebar('sidebar-1') ? ' has-sidebar' : ''; ?>">
        <div class="main-content">
            <?php if (have_posts()) : ?>
                
                <?php if (is_home() && !is_front_page()) : ?>
                    <header class="page-header mb-4">
                        <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                    </header>
                <?php endif; ?>

                <div class="posts-container blog-layout-<?php echo esc_attr(get_theme_mod('oltreblocksy_blog_layout', 'grid')); ?> posts-per-row-<?php echo esc_attr(get_theme_mod('oltreblocksy_posts_per_row', 2)); ?>"><?php 
                    $show_excerpt = get_theme_mod('oltreblocksy_show_excerpt', true);
                    $excerpt_length = get_theme_mod('oltreblocksy_excerpt_length', 25);
                ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-item card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <?php if (!is_singular()) : ?>
                                        <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                            <?php
                                            the_post_thumbnail('card', array(
                                                'alt' => the_title_attribute(array('echo' => false)),
                                                'loading' => 'lazy'
                                            ));
                                            ?>
                                        </a>
                                    <?php else : ?>
                                        <?php
                                        the_post_thumbnail('large', array(
                                            'alt' => the_title_attribute(array('echo' => false)),
                                            'loading' => 'lazy'
                                        ));
                                        ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <header class="entry-header">
                                    <?php
                                    if (is_singular()) :
                                        the_title('<h1 class="entry-title card-title">', '</h1>');
                                    else :
                                        the_title('<h2 class="entry-title card-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                                    endif;

                                    if ('post' === get_post_type()) :
                                        ?>
                                        <div class="entry-meta text-muted mb-2">
                                            <?php
                                            oltreblocksy_posted_on();
                                            oltreblocksy_posted_by();
                                            ?>
                                        </div>
                                        <?php
                                    endif;
                                    ?>
                                </header>

                                <div class="entry-content card-text">
                                    <?php
                                    if (is_singular()) :
                                        the_content(sprintf(
                                            wp_kses(
                                                __('Continue reading<span class="sr-only"> "%s"</span>', 'oltreblocksy'),
                                                array(
                                                    'span' => array(
                                                        'class' => array(),
                                                    ),
                                                )
                                            ),
                                            wp_kses_post(get_the_title())
                                        ));

                                        wp_link_pages(array(
                                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'oltreblocksy'),
                                            'after'  => '</div>',
                                        ));
                                    else :
                                        if ($show_excerpt) :
                                            // Custom excerpt with length control
                                            $excerpt = get_the_excerpt();
                                            if ($excerpt_length && $excerpt_length != 55) {
                                                $excerpt = wp_trim_words($excerpt, $excerpt_length, '...');
                                            }
                                            echo '<p class="post-excerpt" data-original-text="' . esc_attr($excerpt) . '">' . esc_html($excerpt) . '</p>';
                                        endif;
                                    endif;
                                    ?>
                                </div>
                                
                                <?php if (!is_singular()) : ?>
                                    <div class="card-footer">
                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                            <?php esc_html_e('Read More', 'oltreblocksy'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (is_singular()) : ?>
                                <footer class="entry-footer">
                                    <?php oltreblocksy_entry_footer(); ?>
                                </footer>
                            <?php endif; ?>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php
                $pagination_args = array(
                    'prev_text' => '<span class="nav-prev-text">' . __('Older posts', 'oltreblocksy') . '</span>',
                    'next_text' => '<span class="nav-next-text">' . __('Newer posts', 'oltreblocksy') . '</span>',
                    'class'     => 'posts-navigation d-flex justify-content-between mt-4'
                );
                the_posts_navigation($pagination_args);

            else :
                ?>
                <section class="no-results not-found card">
                    <div class="card-body text-center">
                        <header class="page-header">
                            <h1 class="page-title card-title"><?php esc_html_e('Nothing here', 'oltreblocksy'); ?></h1>
                        </header>

                        <div class="page-content card-text">
                            <?php if (is_home() && current_user_can('publish_posts')) : ?>
                                <p>
                                    <?php
                                    printf(
                                        wp_kses(
                                            __('Ready to publish your first post? <a href="%1$s" class="btn btn-primary">Get started here</a>.', 'oltreblocksy'),
                                            array(
                                                'a' => array(
                                                    'href' => array(),
                                                    'class' => array(),
                                                ),
                                            )
                                        ),
                                        esc_url(admin_url('post-new.php'))
                                    );
                                    ?>
                                </p>
                            <?php elseif (is_search()) : ?>
                                <p class="mb-3"><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'oltreblocksy'); ?></p>
                                <?php get_search_form(); ?>
                            <?php else : ?>
                                <p class="mb-3"><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'oltreblocksy'); ?></p>
                                <?php get_search_form(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                <?php
            endif;
            ?>
        </div>
        
        <?php if (is_active_sidebar('sidebar-1')) : ?>
            <aside class="sidebar">
                <?php dynamic_sidebar('sidebar-1'); ?>
            </aside>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();