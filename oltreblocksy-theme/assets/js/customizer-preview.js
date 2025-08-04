/**
 * OltreBlocksy Customizer Preview
 * Live preview functionality for theme customization
 * 
 * @package OltreBlocksy
 * @since 1.0.0
 */

(function($) {
    'use strict';

    const OltreBlocksyPreview = {
        
        init() {
            this.setupColorPreviews();
            this.setupLayoutPreviews();
            this.setupTypographyPreviews();
            this.setupSpacingPreviews();
            this.setupHeaderPreviews();
            this.setupBlogPreviews();
            this.injectCustomStyles();
        },

        /**
         * Live preview for color changes
         */
        setupColorPreviews() {
            // Primary color
            wp.customize('oltreblocksy_primary_color', (value) => {
                value.bind((color) => {
                    this.updateCSSProperty('--wp--preset--color--blue-600', color);
                    this.updateCSSProperty('--wp--preset--color--blue-700', this.darkenColor(color, 10));
                    
                    // Update elements that use primary color
                    $('body').find('.btn-primary, .nav-link.active, .text-primary').each(function() {
                        $(this).css('color', color);
                    });
                });
            });

            // Secondary color
            wp.customize('oltreblocksy_secondary_color', (value) => {
                value.bind((color) => {
                    this.updateCSSProperty('--wp--preset--color--slate-500', color);
                    this.updateCSSProperty('--wp--preset--color--slate-600', this.darkenColor(color, 10));
                    
                    $('body').find('.text-muted, .nav-link').each(function() {
                        $(this).css('color', color);
                    });
                });
            });

            // Accent color
            wp.customize('oltreblocksy_accent_color', (value) => {
                value.bind((color) => {
                    this.updateCSSProperty('--wp--preset--color--accent', color);
                    
                    $('body').find('.badge-warning, .alert-warning').each(function() {
                        $(this).css('background-color', color);
                    });
                });
            });

            // Color scheme
            wp.customize('oltreblocksy_color_scheme', (value) => {
                value.bind((scheme) => {
                    $('html').removeClass('light-mode dark-mode auto-mode').addClass(scheme + '-mode');
                    
                    if (scheme === 'dark') {
                        this.applyDarkMode();
                    } else if (scheme === 'light') {
                        this.applyLightMode();
                    } else {
                        this.applyAutoMode();
                    }
                });
            });
        },

        /**
         * Live preview for layout changes
         */
        setupLayoutPreviews() {
            // Container width
            wp.customize('oltreblocksy_container_width', (value) => {
                value.bind((width) => {
                    this.updateCSSProperty('--container-max-width', `min(100% - 2rem, ${width}px)`);
                });
            });

            // Content width
            wp.customize('oltreblocksy_content_width', (value) => {
                value.bind((width) => {
                    this.updateCSSProperty('--content-max-width', `min(100% - 2rem, ${width}ch)`);
                });
            });

            // Site layout
            wp.customize('oltreblocksy_site_layout', (value) => {
                value.bind((layout) => {
                    $('body').removeClass('layout-full-width layout-boxed layout-framed')
                             .addClass('layout-' + layout);
                });
            });
        },

        /**
         * Live preview for header changes
         */
        setupHeaderPreviews() {
            // Header layout
            wp.customize('oltreblocksy_header_layout', (value) => {
                value.bind((layout) => {
                    $('.site-header').removeClass('header-default header-centered header-minimal header-split')
                                    .addClass('header-' + layout);
                });
            });

            // Header height
            wp.customize('oltreblocksy_header_height', (value) => {
                value.bind((height) => {
                    this.updateCSSProperty('--header-height', height + 'px');
                });
            });

            // Sticky header
            wp.customize('oltreblocksy_sticky_header', (value) => {
                value.bind((sticky) => {
                    $('.site-header').toggleClass('sticky-header', sticky);
                    
                    if (sticky) {
                        $('.site-header').css({
                            'position': 'sticky',
                            'top': '0',
                            'z-index': '100'
                        });
                    } else {
                        $('.site-header').css({
                            'position': 'static'
                        });
                    }
                });
            });
        },

        /**
         * Live preview for blog layout changes
         */
        setupBlogPreviews() {
            // Blog layout
            wp.customize('oltreblocksy_blog_layout', (value) => {
                value.bind((layout) => {
                    $('.posts-container').removeClass('blog-layout-list blog-layout-grid blog-layout-masonry blog-layout-cards')
                                        .addClass('blog-layout-' + layout);
                                        
                    $('body').removeClass('blog-layout-list blog-layout-grid blog-layout-masonry blog-layout-cards')
                             .addClass('blog-layout-' + layout);
                });
            });

            // Posts per row
            wp.customize('oltreblocksy_posts_per_row', (value) => {
                value.bind((columns) => {
                    $('body').removeClass('posts-per-row-1 posts-per-row-2 posts-per-row-3 posts-per-row-4')
                             .addClass('posts-per-row-' + columns);
                });
            });

            // Show excerpt
            wp.customize('oltreblocksy_show_excerpt', (value) => {
                value.bind((show) => {
                    $('.post-excerpt').toggle(show);
                });
            });

            // Excerpt length
            wp.customize('oltreblocksy_excerpt_length', (value) => {
                value.bind((length) => {
                    // This would need server-side processing for actual content
                    // For preview, we can simulate by truncating existing excerpts
                    $('.post-excerpt').each(function() {
                        const text = $(this).data('original-text') || $(this).text();
                        $(this).data('original-text', text);
                        
                        const words = text.split(' ');
                        const truncated = words.slice(0, length).join(' ');
                        $(this).text(truncated + (words.length > length ? '...' : ''));
                    });
                });
            });
        },

        /**
         * Live preview for spacing changes
         */
        setupSpacingPreviews() {
            // Global spacing scale
            wp.customize('oltreblocksy_spacing_scale', (value) => {
                value.bind((scale) => {
                    const baseSpacing = {
                        xs: 0.25,
                        sm: 0.5,
                        md: 1,
                        lg: 1.5,
                        xl: 2,
                        xxl: 3
                    };

                    Object.entries(baseSpacing).forEach(([size, base]) => {
                        const scaledValue = base * scale;
                        this.updateCSSProperty(`--spacing-${size}`, `clamp(${scaledValue * 0.5}rem, ${scaledValue}vw, ${scaledValue * 2}rem)`);
                    });
                });
            });

            // Section spacing
            wp.customize('oltreblocksy_section_spacing', (value) => {
                value.bind((spacing) => {
                    $('body').removeClass('section-spacing-small section-spacing-medium section-spacing-large section-spacing-custom')
                             .addClass('section-spacing-' + spacing);
                });
            });
        },

        /**
         * Live typography preview (future enhancement)
         */
        setupTypographyPreviews() {
            // Font family changes would go here
            // Font size scaling would go here
            // Line height adjustments would go here
        },

        /**
         * Footer preview
         */
        setupFooterPreviews() {
            // Footer columns
            wp.customize('oltreblocksy_footer_columns', (value) => {
                value.bind((columns) => {
                    $('.site-footer').removeClass('footer-columns-1 footer-columns-2 footer-columns-3 footer-columns-4')
                                    .addClass('footer-columns-' + columns);
                });
            });

            // Copyright text
            wp.customize('oltreblocksy_copyright_text', (value) => {
                value.bind((text) => {
                    $('.copyright').html(text || 'Copyright © ' + new Date().getFullYear());
                });
            });
        },

        /**
         * Utility functions
         */
        updateCSSProperty(property, value) {
            document.documentElement.style.setProperty(property, value);
        },

        darkenColor(hex, percent) {
            const num = parseInt(hex.replace('#', ''), 16);
            const amt = Math.round(2.55 * percent);
            const R = (num >> 16) - amt;
            const G = (num >> 8 & 0x00FF) - amt;
            const B = (num & 0x0000FF) - amt;
            
            return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
                (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
                (B < 255 ? B < 1 ? 0 : B : 255))
                .toString(16).slice(1);
        },

        lightenColor(hex, percent) {
            const num = parseInt(hex.replace('#', ''), 16);
            const amt = Math.round(2.55 * percent);
            const R = (num >> 16) + amt;
            const G = (num >> 8 & 0x00FF) + amt;
            const B = (num & 0x0000FF) + amt;
            
            return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
                (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
                (B < 255 ? B < 1 ? 0 : B : 255))
                .toString(16).slice(1);
        },

        applyDarkMode() {
            $('html').addClass('dark-mode');
            this.updateCSSProperty('--color-text', '#f8fafc');
            this.updateCSSProperty('--color-background', '#0f172a');
            this.updateCSSProperty('--color-surface', '#1e293b');
        },

        applyLightMode() {
            $('html').removeClass('dark-mode');
            this.updateCSSProperty('--color-text', '#0f172a');
            this.updateCSSProperty('--color-background', '#f8fafc');
            this.updateCSSProperty('--color-surface', '#ffffff');
        },

        applyAutoMode() {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            if (darkModeQuery.matches) {
                this.applyDarkMode();
            } else {
                this.applyLightMode();
            }
        },

        /**
         * Inject custom styles for preview enhancements
         */
        injectCustomStyles() {
            const customStyles = `
                <style id="oltreblocksy-preview-styles">
                /* Customizer Preview Enhancements */
                .customizer-loading {
                    opacity: 0.7;
                    transition: opacity 0.3s ease;
                }
                
                .preview-highlight {
                    outline: 2px dashed #007cba !important;
                    outline-offset: 2px !important;
                }
                
                .section-spacing-small .site-main { padding: 1rem 0; }
                .section-spacing-medium .site-main { padding: 2rem 0; }
                .section-spacing-large .site-main { padding: 3rem 0; }
                
                /* Blog layout transitions */
                .posts-container {
                    transition: all 0.3s ease;
                }
                
                .post-item {
                    transition: all 0.3s ease;
                }
                
                /* Header transitions */
                .site-header {
                    transition: all 0.3s ease;
                }
                
                /* Color transitions */
                * {
                    transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
                }
                </style>
            `;
            
            $('head').append(customStyles);
        },

        /**
         * Highlight elements on hover (development helper)
         */
        setupElementHighlighting() {
            if (typeof wp !== 'undefined' && wp.customize && wp.customize.settings.theme.is_theme_active) {
                return; // Only for preview mode
            }

            $(document).on('mouseenter', '[class*="oltreblocksy"], .site-header, .site-footer, .main-content', function() {
                $(this).addClass('preview-highlight');
            }).on('mouseleave', '[class*="oltreblocksy"], .site-header, .site-footer, .main-content', function() {
                $(this).removeClass('preview-highlight');
            });
        }
    };

    // Initialize preview functionality
    $(document).ready(() => {
        OltreBlocksyPreview.init();
        
        // Add loading class during customizer operations
        if (parent.wp && parent.wp.customize) {
            parent.wp.customize.bind('change', () => {
                $('body').addClass('customizer-loading');
                setTimeout(() => {
                    $('body').removeClass('customizer-loading');
                }, 300);
            });
        }
    });

    // Make preview object available globally for debugging
    window.OltreBlocksyPreview = OltreBlocksyPreview;

})(jQuery);