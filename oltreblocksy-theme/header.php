<?php
/**
 * The header for the theme
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class('no-js'); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary">
    <?php esc_html_e('Skip to main content', 'oltreblocksy'); ?>
</a>

<div id="page" class="site layout-<?php echo esc_attr(get_theme_mod('oltreblocksy_site_layout', 'full-width')); ?>">
    <header id="masthead" class="site-header header-<?php echo esc_attr(get_theme_mod('oltreblocksy_header_layout', 'default')); ?><?php echo get_theme_mod('oltreblocksy_sticky_header', true) ? ' sticky-header' : ''; ?>" role="banner" itemscope itemtype="https://schema.org/WPHeader">
        <div class="header-container container">
            <div class="site-branding">
                <?php
                if (has_custom_logo()) {
                    oltreblocksy_custom_logo();
                } else {
                    ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" itemprop="url">
                            <span itemprop="name"><?php bloginfo('name'); ?></span>
                        </a>
                    </h1>
                    <?php
                    $description = get_bloginfo('description', 'display');
                    if ($description || is_customize_preview()) :
                        ?>
                        <p class="site-description" itemprop="description"><?php echo $description; ?></p>
                        <?php
                    endif;
                }
                ?>
            </div>

            <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary menu', 'oltreblocksy'); ?>" itemscope itemtype="https://schema.org/SiteNavigationElement">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'oltreblocksy'); ?>">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span class="menu-toggle-text sr-only"><?php esc_html_e('Menu', 'oltreblocksy'); ?></span>
                </button>

                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'menu_class'     => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>
        </div>
    </header>

    <?php
    // Breadcrumbs
    if (!is_front_page()) {
        oltreblocksy_breadcrumbs();
    }
    ?>