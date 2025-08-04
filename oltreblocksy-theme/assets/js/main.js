/**
 * OltreBlocksy Main JavaScript
 * 
 * Modern ES6+ JavaScript with performance optimizations
 * 
 * @package OltreBlocksy
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Feature detection
    const supports = {
        intersectionObserver: 'IntersectionObserver' in window,
        css: {
            customProperties: CSS.supports('color', 'var(--primary)'),
            grid: CSS.supports('display', 'grid'),
            clamp: CSS.supports('font-size', 'clamp(1rem, 2vw, 2rem)')
        }
    };

    // Performance monitoring
    const performance = {
        startTime: Date.now(),
        marks: new Map(),
        
        mark(name) {
            this.marks.set(name, Date.now() - this.startTime);
        },
        
        measure(name, startMark, endMark) {
            const start = this.marks.get(startMark) || 0;
            const end = this.marks.get(endMark) || Date.now() - this.startTime;
            console.log(`${name}: ${end - start}ms`);
        }
    };

    // Theme utilities
    const OltreBlocksy = {
        
        // Initialize theme
        init() {
            performance.mark('init-start');
            
            this.setupMobileMenu();
            this.setupSmoothScrolling();
            this.setupLazyLoading();
            this.setupAccessibility();
            this.setupDarkMode();
            this.setupPerformanceOptimizations();
            
            performance.mark('init-end');
            performance.measure('Theme initialization', 'init-start', 'init-end');
            
            // Emit custom event
            document.dispatchEvent(new CustomEvent('oltreblocksy:ready', {
                detail: { supports, performance }
            }));
        },

        // Mobile menu functionality
        setupMobileMenu() {
            const menuToggle = document.querySelector('.menu-toggle');
            const mobileMenu = document.querySelector('#primary-menu');
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener('click', this.toggleMobileMenu.bind(this));
                
                // Close menu on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
                        this.closeMobileMenu();
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!menuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
                        this.closeMobileMenu();
                    }
                });
            }

            // Dropdown toggles
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', this.toggleDropdown.bind(this));
            });
        },

        toggleMobileMenu() {
            const menuToggle = document.querySelector('.menu-toggle');
            const mobileMenu = document.querySelector('#primary-menu');
            const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';

            menuToggle.setAttribute('aria-expanded', !isOpen);
            mobileMenu.classList.toggle('is-open', !isOpen);
            document.body.classList.toggle('menu-open', !isOpen);

            // Trap focus in menu when open
            if (!isOpen) {
                this.trapFocus(mobileMenu);
            } else {
                this.releaseFocus();
            }
        },

        closeMobileMenu() {
            const menuToggle = document.querySelector('.menu-toggle');
            const mobileMenu = document.querySelector('#primary-menu');

            if (menuToggle && mobileMenu) {
                menuToggle.setAttribute('aria-expanded', 'false');
                mobileMenu.classList.remove('is-open');
                document.body.classList.remove('menu-open');
                this.releaseFocus();
            }
        },

        toggleDropdown(e) {
            e.preventDefault();
            const toggle = e.currentTarget;
            const submenu = toggle.nextElementSibling;
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

            toggle.setAttribute('aria-expanded', !isExpanded);
            if (submenu) {
                submenu.hidden = isExpanded;
            }
        },

        // Smooth scrolling for anchor links
        setupSmoothScrolling() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href^="#"]');
                if (!link) return;

                const targetId = link.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);

                if (target) {
                    e.preventDefault();
                    
                    const headerOffset = document.querySelector('.site-header')?.offsetHeight || 0;
                    const targetPosition = target.offsetTop - headerOffset - 20;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    // Update focus for accessibility
                    target.tabIndex = -1;
                    target.focus();
                }
            });
        },

        // Lazy loading for images and content
        setupLazyLoading() {
            if (!supports.intersectionObserver) {
                // Fallback for browsers without IntersectionObserver
                this.loadAllImages();
                return;
            }

            // Lazy load images
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            lazyImages.forEach(img => imageObserver.observe(img));

            // Lazy load iframes
            const lazyIframes = document.querySelectorAll('iframe[loading="lazy"]');
            const iframeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const iframe = entry.target;
                        if (iframe.dataset.src) {
                            iframe.src = iframe.dataset.src;
                            iframe.removeAttribute('data-src');
                        }
                        iframeObserver.unobserve(iframe);
                    }
                });
            }, {
                rootMargin: '100px 0px'
            });

            lazyIframes.forEach(iframe => iframeObserver.observe(iframe));
        },

        loadAllImages() {
            const lazyImages = document.querySelectorAll('img[data-src]');
            lazyImages.forEach(img => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                img.classList.add('loaded');
            });
        },

        // Accessibility enhancements
        setupAccessibility() {
            // Skip link functionality
            const skipLink = document.querySelector('.skip-link');
            if (skipLink) {
                skipLink.addEventListener('click', (e) => {
                    const target = document.querySelector(skipLink.getAttribute('href'));
                    if (target) {
                        target.tabIndex = -1;
                        target.focus();
                    }
                });
            }

            // Focus visible polyfill
            this.setupFocusVisible();

            // Improve form accessibility
            this.enhanceFormAccessibility();

            // ARIA live regions for dynamic content
            this.setupLiveRegions();
        },

        setupFocusVisible() {
            let hadKeyboardEvent = true;
            const keyboardThrottleTimeout = 100;

            function onPointerDown() {
                hadKeyboardEvent = false;
            }

            function onKeyDown(e) {
                if (e.metaKey || e.altKey || e.ctrlKey) {
                    return;
                }
                hadKeyboardEvent = true;
            }

            function onFocus(e) {
                if (hadKeyboardEvent || e.target.matches(':focus-visible')) {
                    e.target.classList.add('focus-visible');
                }
            }

            function onBlur(e) {
                e.target.classList.remove('focus-visible');
            }

            document.addEventListener('keydown', onKeyDown, true);
            document.addEventListener('mousedown', onPointerDown, true);
            document.addEventListener('pointerdown', onPointerDown, true);
            document.addEventListener('touchstart', onPointerDown, true);
            document.addEventListener('focus', onFocus, true);
            document.addEventListener('blur', onBlur, true);
        },

        enhanceFormAccessibility() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                // Add required indicators
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    const label = form.querySelector(`label[for="${field.id}"]`);
                    if (label && !label.querySelector('.required-indicator')) {
                        const indicator = document.createElement('span');
                        indicator.className = 'required-indicator';
                        indicator.textContent = ' *';
                        indicator.setAttribute('aria-label', 'required');
                        label.appendChild(indicator);
                    }
                });

                // Enhanced error handling
                form.addEventListener('submit', this.handleFormValidation.bind(this));
            });
        },

        handleFormValidation(e) {
            const form = e.target;
            const invalidFields = form.querySelectorAll(':invalid');
            
            if (invalidFields.length > 0) {
                e.preventDefault();
                
                // Focus first invalid field
                invalidFields[0].focus();
                
                // Add error styling
                invalidFields.forEach(field => {
                    field.classList.add('error');
                    field.addEventListener('input', function() {
                        this.classList.remove('error');
                    }, { once: true });
                });
            }
        },

        setupLiveRegions() {
            // Create live region for announcements
            const liveRegion = document.createElement('div');
            liveRegion.id = 'live-region';
            liveRegion.setAttribute('aria-live', 'polite');
            liveRegion.setAttribute('aria-atomic', 'true');
            liveRegion.className = 'sr-only';
            document.body.appendChild(liveRegion);

            // Expose function to announce messages
            window.announceToScreenReader = (message) => {
                liveRegion.textContent = message;
                setTimeout(() => {
                    liveRegion.textContent = '';
                }, 1000);
            };
        },

        // Dark mode functionality
        setupDarkMode() {
            const darkModeToggle = document.querySelector('.dark-mode-toggle');
            if (!darkModeToggle) return;

            darkModeToggle.addEventListener('click', this.toggleDarkMode.bind(this));

            // Listen for system preference changes
            if (window.matchMedia) {
                const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
                darkModeQuery.addEventListener('change', this.handleSystemDarkModeChange.bind(this));
            }
        },

        toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark-mode');
            localStorage.setItem('dark-mode', isDark);
            
            // Update cookie for server-side detection
            document.cookie = `dark-mode=${isDark}; path=/; max-age=31536000; SameSite=Lax`;
            
            // Announce change to screen readers
            if (window.announceToScreenReader) {
                const message = isDark ? 'Dark mode enabled' : 'Light mode enabled';
                window.announceToScreenReader(message);
            }
        },

        handleSystemDarkModeChange(e) {
            if (localStorage.getItem('dark-mode') === null) {
                document.documentElement.classList.toggle('dark-mode', e.matches);
            }
        },

        // Performance optimizations
        setupPerformanceOptimizations() {
            // Preload critical resources on hover
            this.setupHoverPreloading();
            
            // Optimize scroll performance
            this.setupScrollOptimizations();
            
            // Monitor performance metrics
            this.monitorPerformance();
        },

        setupHoverPreloading() {
            const links = document.querySelectorAll('a[href^="/"], a[href^="' + window.location.origin + '"]');
            
            links.forEach(link => {
                link.addEventListener('mouseenter', () => {
                    if (!link.dataset.preloaded) {
                        const prefetchLink = document.createElement('link');
                        prefetchLink.rel = 'prefetch';
                        prefetchLink.href = link.href;
                        document.head.appendChild(prefetchLink);
                        link.dataset.preloaded = 'true';
                    }
                }, { once: true });
            });
        },

        setupScrollOptimizations() {
            let ticking = false;

            function updateScrollPosition() {
                const scrollY = window.scrollY;
                document.documentElement.style.setProperty('--scroll-y', scrollY + 'px');
                ticking = false;
            }

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(updateScrollPosition);
                    ticking = true;
                }
            }, { passive: true });
        },

        monitorPerformance() {
            if (!window.performance) return;

            window.addEventListener('load', () => {
                setTimeout(() => {
                    const navigation = performance.getEntriesByType('navigation')[0];
                    const paint = performance.getEntriesByType('paint');
                    
                    const metrics = {
                        loadTime: navigation.loadEventEnd - navigation.fetchStart,
                        domContentLoaded: navigation.domContentLoadedEventEnd - navigation.fetchStart,
                        firstPaint: paint.find(p => p.name === 'first-paint')?.startTime || 0,
                        firstContentfulPaint: paint.find(p => p.name === 'first-contentful-paint')?.startTime || 0
                    };
                    
                    // Send to analytics or log for development
                    if (window.console && typeof window.gtag !== 'function') {
                        console.log('Performance Metrics:', metrics);
                    }
                }, 0);
            });
        },

        // Focus management utilities
        trapFocus(element) {
            const focusableElements = element.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            
            if (focusableElements.length === 0) return;

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            function handleTabKey(e) {
                if (e.key !== 'Tab') return;

                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        lastElement.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        firstElement.focus();
                        e.preventDefault();
                    }
                }
            }

            element.addEventListener('keydown', handleTabKey);
            firstElement.focus();

            // Store cleanup function
            this._focusTrapCleanup = () => {
                element.removeEventListener('keydown', handleTabKey);
            };
        },

        releaseFocus() {
            if (this._focusTrapCleanup) {
                this._focusTrapCleanup();
                this._focusTrapCleanup = null;
            }
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => OltreBlocksy.init());
    } else {
        OltreBlocksy.init();
    }

    // Expose OltreBlocksy to global scope for extensibility
    window.OltreBlocksy = OltreBlocksy;

})();