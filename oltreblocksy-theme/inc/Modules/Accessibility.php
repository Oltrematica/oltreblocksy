<?php
/**
 * Accessibility Module
 *
 * Ensures WCAG 2.1 AA compliance and provides advanced accessibility features
 * that surpass standard WordPress themes
 *
 * @package OltreBlocksy
 * @since 1.0.0
 */

namespace OltreBlocksy\Modules;

defined('ABSPATH') || exit;

class Accessibility extends Base_Module {
    
    /**
     * Get module name
     * 
     * @return string
     */
    protected function get_name() {
        return 'Accessibility';
    }
    
    /**
     * Initialize module
     */
    protected function init() {
        add_action('wp_head', array($this, 'add_accessibility_features'), 5);
        add_action('wp_footer', array($this, 'add_accessibility_scripts'), 999);
        
        add_filter('nav_menu_link_attributes', array($this, 'enhance_menu_accessibility'), 10, 4);
        add_filter('wp_get_attachment_image_attributes', array($this, 'improve_image_accessibility'), 10, 3);
        add_filter('the_content', array($this, 'enhance_content_accessibility'), 25);
        add_filter('comment_form_default_fields', array($this, 'improve_form_accessibility'));
        
        // Admin accessibility enhancements
        if (is_admin()) {
            add_action('admin_head', array($this, 'add_admin_accessibility_features'));
        }
        
        $this->log('Accessibility module initialized');
    }
    
    /**
     * Add accessibility features to head
     */
    public function add_accessibility_features() {
        // Reduced motion support
        echo '<style id="oltreblocksy-reduced-motion">';
        echo '@media (prefers-reduced-motion: reduce) {';
        echo '*, *::before, *::after {';
        echo 'animation-duration: 0.01ms !important;';
        echo 'animation-iteration-count: 1 !important;';
        echo 'transition-duration: 0.01ms !important;';
        echo 'scroll-behavior: auto !important;';
        echo '}';
        echo '}';
        echo '</style>' . "\n";
        
        // High contrast mode detection
        echo '<style id="oltreblocksy-high-contrast">';
        echo '@media (prefers-contrast: high) {';
        echo ':root {';
        echo '--color-text: #000000;';
        echo '--color-background: #ffffff;';
        echo '--color-border: #000000;';
        echo '}';
        echo 'a { text-decoration: underline; }';
        echo 'button, input, select, textarea { border: 2px solid #000000; }';
        echo '}';
        echo '</style>' . "\n";
        
        // Focus management
        echo '<style id="oltreblocksy-focus-management">';
        echo '.focus-visible {';
        echo 'outline: 2px solid var(--color-primary, #1e40af);';
        echo 'outline-offset: 2px;';
        echo '}';
        echo '.sr-only {';
        echo 'position: absolute !important;';
        echo 'width: 1px !important;';
        echo 'height: 1px !important;';
        echo 'padding: 0 !important;';
        echo 'margin: -1px !important;';
        echo 'overflow: hidden !important;';
        echo 'clip: rect(0, 0, 0, 0) !important;';
        echo 'white-space: nowrap !important;';
        echo 'border: 0 !important;';
        echo '}';
        echo '</style>' . "\n";
    }
    
    /**
     * Add accessibility scripts
     */
    public function add_accessibility_scripts() {
        if ($this->get_setting('enable_keyboard_navigation', true)) {
            $this->add_keyboard_navigation_script();
        }
        
        if ($this->get_setting('enable_focus_trap', true)) {
            $this->add_focus_trap_script();
        }
        
        if ($this->get_setting('enable_announcement_region', true)) {
            $this->add_announcement_region();
        }
    }
    
    /**
     * Add keyboard navigation script
     */
    private function add_keyboard_navigation_script() {
        echo '<script id="oltreblocksy-keyboard-nav">';
        echo '(function() {';
        echo 'let isUsingMouse = false;';
        echo 'document.addEventListener("mousedown", () => isUsingMouse = true);';
        echo 'document.addEventListener("keydown", (e) => {';
        echo 'if (e.key === "Tab") isUsingMouse = false;';
        echo '});';
        echo 'document.addEventListener("focusin", (e) => {';
        echo 'if (!isUsingMouse) e.target.classList.add("focus-visible");';
        echo '});';
        echo 'document.addEventListener("focusout", (e) => {';
        echo 'e.target.classList.remove("focus-visible");';
        echo '});';
        echo '})();';
        echo '</script>' . "\n";
    }
    
    /**
     * Add focus trap script
     */
    private function add_focus_trap_script() {
        echo '<script id="oltreblocksy-focus-trap">';
        echo 'window.oltreBlocksyFocusTrap = {';
        echo 'trap: function(element) {';
        echo 'const focusable = element.querySelectorAll("button, [href], input, select, textarea, [tabindex]:not([tabindex=\\"-1\\"])");';
        echo 'const first = focusable[0];';
        echo 'const last = focusable[focusable.length - 1];';
        echo 'function handleTab(e) {';
        echo 'if (e.key !== "Tab") return;';
        echo 'if (e.shiftKey) {';
        echo 'if (document.activeElement === first) { last.focus(); e.preventDefault(); }';
        echo '} else {';
        echo 'if (document.activeElement === last) { first.focus(); e.preventDefault(); }';
        echo '}';
        echo '}';
        echo 'element.addEventListener("keydown", handleTab);';
        echo 'return () => element.removeEventListener("keydown", handleTab);';
        echo '},';
        echo 'release: function(cleanup) { if (cleanup) cleanup(); }';
        echo '};';
        echo '</script>' . "\n";
    }
    
    /**
     * Add announcement region
     */
    private function add_announcement_region() {
        echo '<div id="a11y-announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>' . "\n";
        
        echo '<script id="oltreblocksy-announcements">';
        echo 'window.announceToScreenReader = function(message, priority = "polite") {';
        echo 'const region = document.getElementById("a11y-announcements");';
        echo 'if (region) {';
        echo 'region.setAttribute("aria-live", priority);';
        echo 'region.textContent = message;';
        echo 'setTimeout(() => region.textContent = "", 1000);';
        echo '}';
        echo '};';
        echo '</script>' . "\n";
    }
    
    /**
     * Enhance menu accessibility
     * 
     * @param array  $atts Menu link attributes
     * @param object $item Menu item
     * @param object $args Menu arguments
     * @param int    $depth Menu depth
     * @return array Enhanced attributes
     */
    public function enhance_menu_accessibility($atts, $item, $args, $depth) {
        // Add ARIA attributes for dropdown menus
        if (in_array('menu-item-has-children', $item->classes)) {
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
        }
        
        // Add descriptive labels for external links
        if (strpos($item->url, home_url()) === false && !empty($item->url)) {
            $atts['aria-label'] = sprintf(
                /* translators: %s: Link text */
                __('%s (opens in new tab)', 'oltreblocksy'),
                $item->title
            );
            $atts['target'] = '_blank';
            $atts['rel'] = 'noopener noreferrer';
        }
        
        return $atts;
    }
    
    /**
     * Improve image accessibility
     * 
     * @param array  $attr Image attributes
     * @param object $attachment Attachment object
     * @param string $size Image size
     * @return array Enhanced attributes
     */
    public function improve_image_accessibility($attr, $attachment, $size) {
        // Ensure alt text is present
        if (empty($attr['alt'])) {
            $alt_text = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
            if (empty($alt_text)) {
                $alt_text = get_the_title($attachment->ID);
            }
            if (empty($alt_text)) {
                // For decorative images, use empty alt
                $attr['alt'] = '';
                $attr['role'] = 'presentation';
            } else {
                $attr['alt'] = trim($alt_text);
            }
        }
        
        // Add ARIA attributes for complex images
        if (strlen($attr['alt']) > 100) {
            $attr['aria-describedby'] = 'img-desc-' . $attachment->ID;
        }
        
        return $attr;
    }
    
    /**
     * Enhance content accessibility
     * 
     * @param string $content Post content
     * @return string Enhanced content
     */
    public function enhance_content_accessibility($content) {
        // Add skip links for long content
        if (strlen(strip_tags($content)) > 2000) {
            $content = $this->add_content_navigation($content);
        }
        
        // Enhance table accessibility
        $content = $this->enhance_tables($content);
        
        // Add ARIA labels to embedded content
        $content = $this->enhance_embeds($content);
        
        // Improve heading structure
        $content = $this->improve_heading_structure($content);
        
        return $content;
    }
    
    /**
     * Add content navigation for long articles
     * 
     * @param string $content Post content
     * @return string Enhanced content
     */
    private function add_content_navigation($content) {
        // Extract headings for table of contents
        preg_match_all('/<h([2-6])[^>]*>(.*?)<\/h[2-6]>/i', $content, $matches, PREG_SET_ORDER);
        
        if (count($matches) < 3) {
            return $content;
        }
        
        $toc = '<nav class="content-navigation" aria-label="' . esc_attr__('Article contents', 'oltreblocksy') . '">';
        $toc .= '<h2>' . __('Contents', 'oltreblocksy') . '</h2>';
        $toc .= '<ol>';
        
        foreach ($matches as $index => $match) {
            $level = intval($match[1]);
            $title = strip_tags($match[2]);
            $id = 'heading-' . ($index + 1);
            
            // Add ID to original heading
            $content = str_replace($match[0], str_replace('>', ' id="' . $id . '">', $match[0]), $content);
            
            $toc .= '<li><a href="#' . $id . '">' . esc_html($title) . '</a></li>';
        }
        
        $toc .= '</ol>';
        $toc .= '</nav>';
        
        // Insert TOC after first paragraph
        $first_paragraph_pos = strpos($content, '</p>');
        if ($first_paragraph_pos !== false) {
            $content = substr_replace($content, '</p>' . $toc, $first_paragraph_pos, 4);
        }
        
        return $content;
    }
    
    /**
     * Enhance table accessibility
     * 
     * @param string $content Post content
     * @return string Enhanced content
     */
    private function enhance_tables($content) {
        // Add table captions and improve structure
        $content = preg_replace_callback('/<table([^>]*)>/i', function($matches) {
            $attributes = $matches[1];
            
            // Add role if not present
            if (strpos($attributes, 'role=') === false) {
                $attributes .= ' role="table"';
            }
            
            return '<table' . $attributes . '>';
        }, $content);
        
        // Ensure table headers have proper scope
        $content = preg_replace('/<th([^>]*)>/i', '<th$1 scope="col">', $content);
        
        return $content;
    }
    
    /**
     * Enhance embedded content
     * 
     * @param string $content Post content
     * @return string Enhanced content
     */
    private function enhance_embeds($content) {
        // Add titles to iframes
        $content = preg_replace_callback('/<iframe([^>]*?)(?:\s+title="[^"]*")?([^>]*?)>/i', function($matches) {
            $before = $matches[1];
            $after = $matches[2];
            
            // Extract src for title generation
            if (preg_match('/src="([^"]+)"/i', $before . $after, $src_match)) {
                $url = $src_match[1];
                $title = __('Embedded content', 'oltreblocksy');
                
                if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                    $title = __('YouTube video', 'oltreblocksy');
                } elseif (strpos($url, 'vimeo.com') !== false) {
                    $title = __('Vimeo video', 'oltreblocksy');
                } elseif (strpos($url, 'twitter.com') !== false) {
                    $title = __('Twitter embed', 'oltreblocksy');
                }
                
                return '<iframe' . $before . ' title="' . esc_attr($title) . '"' . $after . '>';
            }
            
            return $matches[0];
        }, $content);
        
        return $content;
    }
    
    /**
     * Improve heading structure
     * 
     * @param string $content Post content
     * @return string Enhanced content
     */
    private function improve_heading_structure($content) {
        // Check for heading level skipping and add warnings
        preg_match_all('/<h([1-6])[^>]*>/i', $content, $matches);
        
        if (empty($matches[1])) {
            return $content;
        }
        
        $levels = array_map('intval', $matches[1]);
        $current_level = 1; // Assuming page title is h1
        
        foreach ($levels as $index => $level) {
            if ($level > $current_level + 1) {
                // Heading level skipped - this could be an accessibility issue
                // In a production environment, you might want to log this or provide an admin notice
                $this->log("Heading level skipped in content: h{$current_level} to h{$level}", 'warning');
            }
            $current_level = $level;
        }
        
        return $content;
    }
    
    /**
     * Improve form accessibility
     * 
     * @param array $fields Comment form fields
     * @return array Enhanced fields
     */
    public function improve_form_accessibility($fields) {
        foreach ($fields as $key => &$field) {
            // Add required indicators
            if (strpos($field, 'required') !== false && strpos($field, 'aria-required') === false) {
                $field = str_replace('required', 'required aria-required="true"', $field);
            }
            
            // Add describedby for error handling
            $field = preg_replace('/(<input[^>]+name="' . $key . '"[^>]*>)/', '$1<span id="' . $key . '-error" class="error-message sr-only" aria-live="polite"></span>', $field);
        }
        
        return $fields;
    }
    
    /**
     * Add admin accessibility features
     */
    public function add_admin_accessibility_features() {
        // Enhanced admin focus management
        echo '<style>';
        echo '.focus-visible { outline: 2px solid #007cba; outline-offset: 2px; }';
        echo '</style>';
        
        echo '<script>';
        echo '(function() {';
        echo 'let isUsingMouse = false;';
        echo 'document.addEventListener("mousedown", () => isUsingMouse = true);';
        echo 'document.addEventListener("keydown", (e) => { if (e.key === "Tab") isUsingMouse = false; });';
        echo 'document.addEventListener("focusin", (e) => { if (!isUsingMouse) e.target.classList.add("focus-visible"); });';
        echo 'document.addEventListener("focusout", (e) => e.target.classList.remove("focus-visible"));';
        echo '})();';
        echo '</script>';
    }
    
    /**
     * Run accessibility audit
     * 
     * @return array Audit results
     */
    public function run_accessibility_audit() {
        $audit_results = array(
            'issues' => array(),
            'warnings' => array(),
            'score' => 100,
        );
        
        // Check color contrast
        $color_module = oltreblocksy()->get_module('ColorSystem');
        if ($color_module) {
            $primary_color = oltreblocksy_get_theme_mod('oltreblocksy_primary_color', '#1e40af');
            $background_color = oltreblocksy_get_theme_mod('oltreblocksy_background_color', '#ffffff');
            
            $contrast_ratio = $color_module->check_contrast_ratio($primary_color, $background_color);
            
            if ($contrast_ratio < 4.5) {
                $audit_results['issues'][] = array(
                    'type' => 'contrast',
                    'message' => sprintf(__('Primary color contrast ratio (%.2f) does not meet WCAG AA standards (4.5:1)', 'oltreblocksy'), $contrast_ratio),
                    'severity' => 'high',
                );
                $audit_results['score'] -= 20;
            } elseif ($contrast_ratio < 7) {
                $audit_results['warnings'][] = array(
                    'type' => 'contrast',
                    'message' => sprintf(__('Primary color contrast ratio (%.2f) does not meet WCAG AAA standards (7:1)', 'oltreblocksy'), $contrast_ratio),
                    'severity' => 'medium',
                );
                $audit_results['score'] -= 5;
            }
        }
        
        // Check if alt attributes are being used
        if (!$this->get_setting('enforce_alt_text', true)) {
            $audit_results['warnings'][] = array(
                'type' => 'images',
                'message' => __('Alt text enforcement is disabled', 'oltreblocksy'),
                'severity' => 'medium',
            );
            $audit_results['score'] -= 10;
        }
        
        return $audit_results;
    }
    
    /**
     * Customize register for accessibility settings
     * 
     * @param WP_Customize_Manager $wp_customize
     */
    public function customize_register($wp_customize) {
        $wp_customize->add_section('oltreblocksy_accessibility', array(
            'title' => __('Accessibility', 'oltreblocksy'),
            'priority' => 35,
            'description' => __('Configure accessibility features to ensure your site is usable by everyone.', 'oltreblocksy'),
        ));
        
        // Enable keyboard navigation
        $wp_customize->add_setting('oltreblocksy_keyboard_navigation', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('oltreblocksy_keyboard_navigation', array(
            'label' => __('Enhanced Keyboard Navigation', 'oltreblocksy'),
            'description' => __('Improve keyboard navigation with focus management.', 'oltreblocksy'),
            'section' => 'oltreblocksy_accessibility',
            'type' => 'checkbox',
        ));
        
        // Skip links
        $wp_customize->add_setting('oltreblocksy_skip_links', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('oltreblocksy_skip_links', array(
            'label' => __('Skip Links', 'oltreblocksy'),
            'description' => __('Add skip links for keyboard users.', 'oltreblocksy'),
            'section' => 'oltreblocksy_accessibility',
            'type' => 'checkbox',
        ));
        
        // High contrast mode
        $wp_customize->add_setting('oltreblocksy_high_contrast_support', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        
        $wp_customize->add_control('oltreblocksy_high_contrast_support', array(
            'label' => __('High Contrast Mode Support', 'oltreblocksy'),
            'description' => __('Automatically adjust colors for users who prefer high contrast.', 'oltreblocksy'),
            'section' => 'oltreblocksy_accessibility',
            'type' => 'checkbox',
        ));
    }
}