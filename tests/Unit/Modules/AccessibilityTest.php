<?php

use OltreBlocksy\Modules\Accessibility;
use Brain\Monkey;

/**
 * Test Accessibility module functionality
 */
describe('Accessibility Module', function () {

    beforeEach(function () {
        // Create a testable version of Accessibility module
        $this->accessibility = new class extends Accessibility {
            public function __construct() {
                // Skip parent constructor to avoid WordPress dependencies
                $this->name = 'Accessibility';
                $this->version = '1.0.0';
                $this->dependencies = [];
                $this->enabled = true;
                $this->settings = [];
            }
            
            // Expose protected methods for testing
            public function test_add_content_navigation($content) {
                return $this->add_content_navigation($content);
            }
            
            public function test_enhance_tables($content) {
                return $this->enhance_tables($content);
            }
            
            public function test_enhance_embeds($content) {
                return $this->enhance_embeds($content);
            }
            
            public function test_improve_heading_structure($content) {
                return $this->improve_heading_structure($content);
            }
            
            // Override get_setting for testing
            public function get_setting($key, $default = null) {
                $test_settings = [
                    'enable_keyboard_navigation' => true,
                    'enable_focus_trap' => true,
                    'enable_announcement_region' => true,
                    'enforce_alt_text' => true
                ];
                
                return $test_settings[$key] ?? $default;
            }
        };
    });

    it('can be instantiated', function () {
        expect($this->accessibility)->toBeInstanceOf(Accessibility::class);
    });

    it('returns correct module name', function () {
        expect($this->accessibility->get_name())->toBe('Accessibility');
    });

    it('enhances menu accessibility with ARIA attributes', function () {
        $item = (object) [
            'classes' => ['menu-item-has-children'],
            'title' => 'Test Menu Item',
            'url' => 'https://example.com'
        ];
        
        $atts = [];
        $args = new stdClass();
        $depth = 0;
        
        $result = $this->accessibility->enhance_menu_accessibility($atts, $item, $args, $depth);
        
        expect($result)->toHaveKey('aria-haspopup');
        expect($result['aria-haspopup'])->toBe('true');
        expect($result)->toHaveKey('aria-expanded');
        expect($result['aria-expanded'])->toBe('false');
    });

    it('adds external link attributes for accessibility', function () {
        $item = (object) [
            'classes' => [],
            'title' => 'External Link',
            'url' => 'https://external-site.com'
        ];
        
        // Mock home_url to return a different domain
        Monkey\Functions\when('home_url')->justReturn('https://mysite.com');
        
        $atts = [];
        $result = $this->accessibility->enhance_menu_accessibility($atts, $item, new stdClass(), 0);
        
        expect($result)->toHaveKey('aria-label');
        expect($result['aria-label'])->toContain('opens in new tab');
        expect($result)->toHaveKey('target');
        expect($result['target'])->toBe('_blank');
        expect($result)->toHaveKey('rel');
        expect($result['rel'])->toBe('noopener noreferrer');
    });

    it('improves image accessibility with alt text', function () {
        $attachment = (object) ['ID' => 123];
        $attr = ['src' => 'image.jpg'];
        
        // Mock WordPress functions
        Monkey\Functions\when('get_post_meta')
            ->with(123, '_wp_attachment_image_alt', true)
            ->justReturn('Test image description');
        
        $result = $this->accessibility->improve_image_accessibility($attr, $attachment, 'large');
        
        expect($result)->toHaveKey('alt');
        expect($result['alt'])->toBe('Test image description');
    });

    it('adds role presentation for decorative images', function () {
        $attachment = (object) ['ID' => 123];
        $attr = ['src' => 'decorative.jpg'];
        
        // Mock empty alt text from meta and title
        Monkey\Functions\when('get_post_meta')
            ->with(123, '_wp_attachment_image_alt', true)
            ->justReturn('');
        Monkey\Functions\when('get_the_title')
            ->with(123)
            ->justReturn('');
        
        $result = $this->accessibility->improve_image_accessibility($attr, $attachment, 'large');
        
        expect($result)->toHaveKey('alt');
        expect($result['alt'])->toBe('');
        expect($result)->toHaveKey('role');
        expect($result['role'])->toBe('presentation');
    });

    it('adds aria-describedby for complex images', function () {
        $attachment = (object) ['ID' => 123];
        $long_alt = str_repeat('This is a very long description for a complex image. ', 10);
        $attr = ['src' => 'complex-chart.jpg', 'alt' => $long_alt];
        
        $result = $this->accessibility->improve_image_accessibility($attr, $attachment, 'large');
        
        expect($result)->toHaveKey('aria-describedby');
        expect($result['aria-describedby'])->toBe('img-desc-123');
    });

    it('adds content navigation for long articles', function () {
        $long_content = "
            <p>Introduction paragraph...</p>
            <h2>First Section</h2>
            <p>Content of first section...</p>
            <h3>Subsection</h3>
            <p>Content of subsection...</p>
            <h2>Second Section</h2>
            <p>Content of second section...</p>
        ";
        
        // Make it long enough to trigger navigation
        $long_content .= str_repeat('<p>Additional content. </p>', 50);
        
        // Mock translation functions
        Monkey\Functions\when('esc_attr__')->returnArg();
        Monkey\Functions\when('__')->returnArg();
        Monkey\Functions\when('esc_html')->returnArg();
        
        $result = $this->accessibility->test_add_content_navigation($long_content);
        
        expect($result)->toContain('<nav class="content-navigation"');
        expect($result)->toContain('aria-label="Article contents"');
        expect($result)->toContain('<h2>Contents</h2>');
        expect($result)->toContain('href="#heading-1"');
        expect($result)->toContain('id="heading-1"');
    });

    it('does not add navigation for short content', function () {
        $short_content = "<p>This is a short article.</p><h2>One heading</h2><p>Not much content.</p>";
        
        $result = $this->accessibility->test_add_content_navigation($short_content);
        
        expect($result)->not->toContain('<nav class="content-navigation"');
        expect($result)->toBe($short_content);
    });

    it('enhances table accessibility', function () {
        $content = '<table><tr><th>Header 1</th><th>Header 2</th></tr><tr><td>Cell 1</td><td>Cell 2</td></tr></table>';
        
        $result = $this->accessibility->test_enhance_tables($content);
        
        expect($result)->toContain('role="table"');
        expect($result)->toContain('scope="col"');
    });

    it('enhances iframe accessibility with titles', function () {
        $content = '<iframe src="https://youtube.com/embed/abc123"></iframe>';
        
        // Mock translation function
        Monkey\Functions\when('__')->returnArg();
        Monkey\Functions\when('esc_attr')->returnArg();
        
        $result = $this->accessibility->test_enhance_embeds($content);
        
        expect($result)->toContain('title="YouTube video"');
    });

    it('handles different embed types correctly', function () {
        // Mock translation functions
        Monkey\Functions\when('__')->returnArg();
        Monkey\Functions\when('esc_attr')->returnArg();
        
        $youtube_content = '<iframe src="https://youtube.com/embed/abc123"></iframe>';
        $result = $this->accessibility->test_enhance_embeds($youtube_content);
        expect($result)->toContain('title="YouTube video"');
        
        $vimeo_content = '<iframe src="https://player.vimeo.com/video/123456"></iframe>';
        $result = $this->accessibility->test_enhance_embeds($vimeo_content);
        expect($result)->toContain('title="Vimeo video"');
        
        $twitter_content = '<iframe src="https://platform.twitter.com/embed/tweet.js"></iframe>';
        $result = $this->accessibility->test_enhance_embeds($twitter_content);
        expect($result)->toContain('title="Twitter embed"');
        
        $generic_content = '<iframe src="https://example.com/embed"></iframe>';
        $result = $this->accessibility->test_enhance_embeds($generic_content);
        expect($result)->toContain('title="Embedded content"');
    });

    it('improves form accessibility', function () {
        $fields = [
            'author' => '<input type="text" name="author" required>',
            'email' => '<input type="email" name="email" required>'
        ];
        
        $result = $this->accessibility->improve_form_accessibility($fields);
        
        expect($result['author'])->toContain('aria-required="true"');
        expect($result['author'])->toContain('id="author-error"');
        expect($result['author'])->toContain('aria-live="polite"');
        
        expect($result['email'])->toContain('aria-required="true"');
        expect($result['email'])->toContain('id="email-error"');
    });

    it('runs accessibility audit', function () {
        // Mock oltreblocksy function
        Monkey\Functions\when('oltreblocksy')->justReturn((object)['get_module' => function() { return null; }]);
        
        $audit = $this->accessibility->run_accessibility_audit();
        
        expect($audit)->toBeArray();
        expect($audit)->toHaveKey('issues');
        expect($audit)->toHaveKey('warnings');
        expect($audit)->toHaveKey('score');
        expect($audit['score'])->toBeInt();
        expect($audit['score'])->toBeGreaterThanOrEqualTo(0);
        expect($audit['score'])->toBeLessThanOrEqualTo(100);
    });

    it('handles customizer registration', function () {
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Expect section and settings to be added
        $wp_customize->shouldReceive('add_section')->once();
        $wp_customize->shouldReceive('add_setting')->atLeast(3);
        $wp_customize->shouldReceive('add_control')->atLeast(3);
        
        $this->accessibility->customize_register($wp_customize);
        
        // If we reach here without exceptions, the test passes
        expect(true)->toBeTrue();
    });

    it('checks for heading level skipping', function () {
        $content_with_skipping = '
            <h1>Main Title</h1>
            <p>Some content</p>
            <h3>Skipped H2</h3>
            <p>More content</p>
        ';
        
        // This should log a warning but not break
        $result = $this->accessibility->test_improve_heading_structure($content_with_skipping);
        
        expect($result)->toBe($content_with_skipping);
    });

    it('handles proper heading hierarchy', function () {
        $content_proper = '
            <h1>Main Title</h1>
            <p>Some content</p>
            <h2>Section Title</h2>
            <p>More content</p>
            <h3>Subsection</h3>
            <p>Even more content</p>
        ';
        
        $result = $this->accessibility->test_improve_heading_structure($content_proper);
        
        expect($result)->toBe($content_proper);
    });

    it('provides keyboard navigation script', function () {
        ob_start();
        $this->accessibility->add_accessibility_scripts();
        $output = ob_get_clean();
        
        expect($output)->toContain('focus-visible');
        expect($output)->toContain('keydown');
        expect($output)->toContain('mousedown');
    });

    it('provides focus trap functionality', function () {
        ob_start();
        $this->accessibility->add_accessibility_scripts();
        $output = ob_get_clean();
        
        expect($output)->toContain('oltreBlocksyFocusTrap');
        expect($output)->toContain('trap: function');
        expect($output)->toContain('release: function');
    });

    it('provides announcement region', function () {
        ob_start();
        $this->accessibility->add_accessibility_scripts();
        $output = ob_get_clean();
        
        expect($output)->toContain('id="a11y-announcements"');
        expect($output)->toContain('aria-live="polite"');
        expect($output)->toContain('announceToScreenReader');
    });

    it('adds reduced motion support', function () {
        ob_start();
        $this->accessibility->add_accessibility_features();
        $output = ob_get_clean();
        
        expect($output)->toContain('@media (prefers-reduced-motion: reduce)');
        expect($output)->toContain('animation-duration: 0.01ms !important');
        expect($output)->toContain('transition-duration: 0.01ms !important');
    });

    it('adds high contrast mode support', function () {
        ob_start();
        $this->accessibility->add_accessibility_features();
        $output = ob_get_clean();
        
        expect($output)->toContain('@media (prefers-contrast: high)');
        expect($output)->toContain('--color-text: #000000');
        expect($output)->toContain('--color-background: #ffffff');
    });

    it('provides proper focus management styles', function () {
        ob_start();
        $this->accessibility->add_accessibility_features();
        $output = ob_get_clean();
        
        expect($output)->toContain('.focus-visible');
        expect($output)->toContain('outline: 2px solid');
        expect($output)->toContain('.sr-only');
        expect($output)->toContain('position: absolute !important');
    });

});