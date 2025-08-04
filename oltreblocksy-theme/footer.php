<?php
/**
 * The template for displaying the footer
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>

    <footer id="colophon" class="site-footer footer-columns-<?php echo esc_attr(get_theme_mod('oltreblocksy_footer_columns', 4)); ?>" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
        <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3') || is_active_sidebar('footer-4')) : ?>
            <div class="footer-widgets">
                <div class="footer-widgets-container">
                        <?php if (is_active_sidebar('footer-1')) : ?>
                            <div class="footer-widget-area footer-widget-1">
                                <?php dynamic_sidebar('footer-1'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (is_active_sidebar('footer-2')) : ?>
                            <div class="footer-widget-area footer-widget-2">
                                <?php dynamic_sidebar('footer-2'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (is_active_sidebar('footer-3')) : ?>
                            <div class="footer-widget-area footer-widget-3">
                                <?php dynamic_sidebar('footer-3'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (is_active_sidebar('footer-4')) : ?>
                            <div class="footer-widget-area footer-widget-4">
                                <?php dynamic_sidebar('footer-4'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer-info">
            <div class="footer-info-container">
                <div class="copyright">
                    <?php
                    $copyright_text = get_theme_mod('oltreblocksy_copyright_text', '');
                    if ($copyright_text) {
                        echo wp_kses_post($copyright_text);
                    } else {
                        printf(
                            /* translators: 1: Current year, 2: Site name */
                            esc_html__('&copy; %1$s %2$s. All rights reserved.', 'oltreblocksy'),
                            date('Y'),
                            get_bloginfo('name')
                        );
                    }
                    ?>
                </div>

                <?php if (has_nav_menu('footer')) : ?>
                    <nav class="footer-navigation" aria-label="<?php esc_attr_e('Footer menu', 'oltreblocksy'); ?>">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ));
                        ?>
                    </nav>
                <?php endif; ?>

                <?php if (has_nav_menu('social')) : ?>
                    <div class="social-links">
                        <?php oltreblocksy_social_menu(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>