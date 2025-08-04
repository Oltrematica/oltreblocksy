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
    <div class="container">
        <?php if (have_posts()) : ?>
            
            <?php if (is_home() && !is_front_page()) : ?>
                <header class="page-header">
                    <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                </header>
            <?php endif; ?>

            <div class="posts-container">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                        <header class="entry-header">
                            <?php
                            if (is_singular()) :
                                the_title('<h1 class="entry-title">', '</h1>');
                            else :
                                the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                            endif;

                            if ('post' === get_post_type()) :
                                ?>
                                <div class="entry-meta">
                                    <?php
                                    oltreblocksy_posted_on();
                                    oltreblocksy_posted_by();
                                    ?>
                                </div>
                                <?php
                            endif;
                            ?>
                        </header>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <?php
                                if (is_singular()) :
                                    the_post_thumbnail('large', array(
                                        'alt' => the_title_attribute(array('echo' => false)),
                                        'loading' => 'lazy'
                                    ));
                                else :
                                    ?>
                                    <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                        <?php
                                        the_post_thumbnail('medium', array(
                                            'alt' => the_title_attribute(array('echo' => false)),
                                            'loading' => 'lazy'
                                        ));
                                        ?>
                                    </a>
                                    <?php
                                endif;
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="entry-content">
                            <?php
                            if (is_singular()) :
                                the_content(sprintf(
                                    wp_kses(
                                        __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'oltreblocksy'),
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
                                the_excerpt();
                            endif;
                            ?>
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
            the_posts_navigation(array(
                'prev_text' => __('Older posts', 'oltreblocksy'),
                'next_text' => __('Newer posts', 'oltreblocksy'),
            ));

        else :
            ?>
            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php esc_html_e('Nothing here', 'oltreblocksy'); ?></h1>
                </header>

                <div class="page-content">
                    <?php if (is_home() && current_user_can('publish_posts')) : ?>
                        <p>
                            <?php
                            printf(
                                wp_kses(
                                    __('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'oltreblocksy'),
                                    array(
                                        'a' => array(
                                            'href' => array(),
                                        ),
                                    )
                                ),
                                esc_url(admin_url('post-new.php'))
                            );
                            ?>
                        </p>
                    <?php elseif (is_search()) : ?>
                        <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'oltreblocksy'); ?></p>
                        <?php get_search_form(); ?>
                    <?php else : ?>
                        <p><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'oltreblocksy'); ?></p>
                        <?php get_search_form(); ?>
                    <?php endif; ?>
                </div>
            </section>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_sidebar();
get_footer();