<?php
/**
 * The template for displaying the footer
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>

    <footer id="colophon" class="site-footer" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
        <div class="container">
            <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3') || is_active_sidebar('footer-4')) : ?>
                <div class="footer-widgets">
                    <div class="footer-widgets-grid">
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
            <?php endif; ?>

            <div class="site-info">
                <div class="site-info-inner">
                    <div class="copyright">
                        <p>
                            <?php
                            printf(
                                /* translators: 1: Site name, 2: Current year */
                                esc_html__('&copy; %1$s %2$s. All rights reserved.', 'oltreblocksy'),
                                date('Y'),
                                get_bloginfo('name')
                            );
                            ?>
                        </p>
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

                <div class="theme-info">
                    <p>
                        <?php
                        printf(
                            /* translators: %s: Theme name */
                            esc_html__('Powered by %s', 'oltreblocksy'),
                            '<a href="https://oltreblocksy.com" rel="nofollow">OltreBlocksy</a>'
                        );
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

<script>
// Replace no-js class with js
document.documentElement.classList.remove('no-js');
document.documentElement.classList.add('js');

// Mobile menu toggle
(function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const primaryMenu = document.querySelector('#primary-menu');
    
    if (menuToggle && primaryMenu) {
        menuToggle.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            primaryMenu.hidden = expanded;
        });
    }
    
    // Handle dropdown toggles
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            
            const submenu = this.nextElementSibling;
            if (submenu) {
                submenu.hidden = expanded;
            }
        });
    });
})();

// Dark mode toggle (if enabled)
if (window.matchMedia && window.localStorage) {
    const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const savedMode = localStorage.getItem('dark-mode');
    
    function setDarkMode(isDark) {
        document.documentElement.classList.toggle('dark-mode', isDark);
        localStorage.setItem('dark-mode', isDark);
        
        // Update cookie for server-side detection
        document.cookie = `dark-mode=${isDark}; path=/; max-age=31536000; SameSite=Lax`;
    }
    
    // Initialize dark mode
    if (savedMode !== null) {
        setDarkMode(savedMode === 'true');
    } else {
        setDarkMode(darkModeMediaQuery.matches);
    }
    
    // Listen for system preference changes
    darkModeMediaQuery.addEventListener('change', function(e) {
        if (localStorage.getItem('dark-mode') === null) {
            setDarkMode(e.matches);
        }
    });
    
    // Expose function for theme customizer
    window.oltreBlocksySetDarkMode = setDarkMode;
}

// Performance monitoring
if (window.console && performance && performance.mark) {
    performance.mark('oltreblocksy-footer-end');
    
    window.addEventListener('load', function() {
        performance.mark('oltreblocksy-load-complete');
        
        // Log performance metrics in development
        if (window.oltreBlocksyPerformance) {
            const navigationTiming = performance.getEntriesByType('navigation')[0];
            if (navigationTiming) {
                console.log('Page Load Time:', navigationTiming.loadEventEnd - navigationTiming.fetchStart, 'ms');
                console.log('DOM Content Loaded:', navigationTiming.domContentLoadedEventEnd - navigationTiming.fetchStart, 'ms');
                console.log('First Paint:', navigationTiming.responseEnd - navigationTiming.fetchStart, 'ms');
            }
        }
    });
}
</script>

</body>
</html>