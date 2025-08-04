/**
 * OltreBlocksy Customizer Controls
 * Enhanced customizer experience with live preview and advanced controls
 * 
 * @package OltreBlocksy
 * @since 1.0.0
 */

(function($) {
    'use strict';

    const OltreBlocksyCustomizer = {
        
        init() {
            this.setupColorControls();
            this.setupRangeControls();
            this.setupConditionalControls();
            this.setupImportExport();
            this.setupPresets();
            this.setupReset();
        },

        /**
         * Enhanced color controls with palette suggestions
         */
        setupColorControls() {
            const colorControls = [
                'oltreblocksy_primary_color',
                'oltreblocksy_secondary_color',
                'oltreblocksy_accent_color'
            ];

            colorControls.forEach(controlId => {
                const control = wp.customize.control(controlId);
                if (!control) return;

                // Add color palette suggestions
                this.addColorPalette(control);
                
                // Add color harmony generator
                this.addColorHarmony(control);
            });
        },

        addColorPalette(control) {
            const container = control.container.find('.customize-control-content');
            const palettes = [
                {
                    name: 'Modern Blue',
                    colors: ['#1e40af', '#64748b', '#f59e0b']
                },
                {
                    name: 'Elegant Purple',
                    colors: ['#7c3aed', '#6b7280', '#10b981']
                },
                {
                    name: 'Warm Orange',
                    colors: ['#ea580c', '#64748b', '#06b6d4']
                },
                {
                    name: 'Classic Green',
                    colors: ['#059669', '#6b7280', '#dc2626']
                }
            ];

            const paletteHtml = `
                <div class="color-palette-suggestions">
                    <p><strong>Color Presets:</strong></p>
                    <div class="palette-grid">
                        ${palettes.map((palette, index) => `
                            <div class="palette-item" data-palette-index="${index}">
                                <div class="palette-colors">
                                    ${palette.colors.map(color => `
                                        <span class="palette-color" style="background-color: ${color}"></span>
                                    `).join('')}
                                </div>
                                <span class="palette-name">${palette.name}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            container.append(paletteHtml);

            // Handle palette selection
            container.on('click', '.palette-item', function() {
                const paletteIndex = parseInt($(this).data('palette-index'));
                const selectedPalette = palettes[paletteIndex];
                if (selectedPalette && selectedPalette.colors) {
                    wp.customize('oltreblocksy_primary_color').set(selectedPalette.colors[0]);
                    wp.customize('oltreblocksy_secondary_color').set(selectedPalette.colors[1]);
                    wp.customize('oltreblocksy_accent_color').set(selectedPalette.colors[2]);
                }
            });
        },

        addColorHarmony(control) {
            const container = control.container.find('.customize-control-content');
            const harmonyHtml = `
                <div class="color-harmony-generator">
                    <button type="button" class="button generate-harmony">Generate Harmony</button>
                    <div class="harmony-options" style="display: none;">
                        <label>
                            <input type="radio" name="harmony-type" value="complementary" checked>
                            Complementary
                        </label>
                        <label>
                            <input type="radio" name="harmony-type" value="triadic">
                            Triadic
                        </label>
                        <label>
                            <input type="radio" name="harmony-type" value="analogous">
                            Analogous
                        </label>
                    </div>
                </div>
            `;

            container.append(harmonyHtml);

            container.on('click', '.generate-harmony', function() {
                const harmonyOptions = container.find('.harmony-options');
                harmonyOptions.toggle();
            });

            container.on('change', 'input[name="harmony-type"]', function() {
                const baseColor = control.setting.get();
                const harmonyType = $(this).val();
                const harmonicColors = OltreBlocksyCustomizer.generateColorHarmony(baseColor, harmonyType);
                
                wp.customize('oltreblocksy_secondary_color').set(harmonicColors.secondary);
                wp.customize('oltreblocksy_accent_color').set(harmonicColors.accent);
            });
        },

        /**
         * Generate color harmony based on color theory
         */
        generateColorHarmony(baseColor, type) {
            const hsl = this.hexToHsl(baseColor);
            let secondaryHue, accentHue;

            switch (type) {
                case 'complementary':
                    secondaryHue = (hsl.h + 180) % 360;
                    accentHue = (hsl.h + 30) % 360;
                    break;
                case 'triadic':
                    secondaryHue = (hsl.h + 120) % 360;
                    accentHue = (hsl.h + 240) % 360;
                    break;
                case 'analogous':
                    secondaryHue = (hsl.h + 30) % 360;
                    accentHue = (hsl.h - 30 + 360) % 360;
                    break;
            }

            return {
                secondary: this.hslToHex(secondaryHue, hsl.s, hsl.l),
                accent: this.hslToHex(accentHue, hsl.s, Math.max(hsl.l - 20, 20))
            };
        },

        /**
         * Enhanced range controls with unit switching and real-time preview
         */
        setupRangeControls() {
            const rangeControls = [
                'oltreblocksy_container_width',
                'oltreblocksy_header_height',
                'oltreblocksy_spacing_scale'
            ];

            rangeControls.forEach(controlId => {
                const control = wp.customize.control(controlId);
                if (!control) return;

                this.enhanceRangeControl(control);
            });
        },

        enhanceRangeControl(control) {
            const container = control.container;
            const input = container.find('input[type="range"]');
            const currentValue = container.find('.customize-control-title');

            // Add value display
            const valueDisplay = $('<span class="range-value"></span>');
            currentValue.append(valueDisplay);

            // Update display on change
            const updateDisplay = () => {
                const value = input.val();
                const unit = control.params.input_attrs?.unit || 'px';
                valueDisplay.text(`(${value}${unit})`);
            };

            input.on('input', updateDisplay);
            updateDisplay(); // Initial display

            // Add preset buttons for common values
            if (control.id === 'oltreblocksy_container_width') {
                this.addPresetButtons(container, [
                    { label: 'Narrow', value: 800 },
                    { label: 'Medium', value: 1200 },
                    { label: 'Wide', value: 1400 },
                    { label: 'Full', value: 1600 }
                ], input);
            }
        },

        addPresetButtons(container, presets, input) {
            const presetsHtml = `
                <div class="range-presets">
                    ${presets.map(preset => `
                        <button type="button" class="button preset-btn" data-value="${preset.value}">
                            ${preset.label}
                        </button>
                    `).join('')}
                </div>
            `;

            container.find('.customize-control-content').append(presetsHtml);

            container.on('click', '.preset-btn', function() {
                const value = $(this).data('value');
                input.val(value).trigger('change');
            });
        },

        /**
         * Conditional control visibility
         */
        setupConditionalControls() {
            // Show/hide posts per row based on blog layout
            wp.customize('oltreblocksy_blog_layout', (value) => {
                value.bind((layout) => {
                    const postsPerRowControl = wp.customize.control('oltreblocksy_posts_per_row');
                    
                    if (postsPerRowControl) {
                        const showPostsPerRow = ['grid', 'masonry', 'cards'].includes(layout);
                        postsPerRowControl.container.toggle(showPostsPerRow);
                    }
                });
            });

            // Show/hide excerpt length based on show excerpt setting
            wp.customize('oltreblocksy_show_excerpt', (value) => {
                value.bind((show) => {
                    const excerptLengthControl = wp.customize.control('oltreblocksy_excerpt_length');
                    if (excerptLengthControl) {
                        excerptLengthControl.container.toggle(show);
                    }
                });
            });

            // Header layout dependent controls
            wp.customize('oltreblocksy_header_layout', (value) => {
                value.bind((layout) => {
                    const stickyHeaderControl = wp.customize.control('oltreblocksy_sticky_header');
                    if (stickyHeaderControl) {
                        // Disable sticky header for certain layouts
                        const allowSticky = !['minimal', 'centered'].includes(layout);
                        stickyHeaderControl.container.find('input').prop('disabled', !allowSticky);
                        if (!allowSticky) {
                            wp.customize('oltreblocksy_sticky_header').set(false);
                        }
                    }
                });
            });
        },

        /**
         * Import/Export functionality
         */
        setupImportExport() {
            // Add import/export section to the customizer
            wp.customize.section.add(new wp.customize.Section('oltreblocksy_import_export', {
                title: 'Import/Export Settings',
                priority: 999,
                content: `
                    <div class="import-export-controls">
                        <div class="control-section">
                            <h3>Export Settings</h3>
                            <p>Download your current theme settings as a JSON file.</p>
                            <button type="button" class="button button-primary export-settings">
                                Export Settings
                            </button>
                        </div>
                        <div class="control-section">
                            <h3>Import Settings</h3>
                            <p>Upload a JSON file to import theme settings.</p>
                            <input type="file" id="import-file" accept=".json" style="margin-bottom: 10px;">
                            <button type="button" class="button import-settings">
                                Import Settings
                            </button>
                        </div>
                        <div class="control-section">
                            <h3>Reset to Defaults</h3>
                            <p>Reset all theme settings to their default values.</p>
                            <button type="button" class="button button-secondary reset-all-settings">
                                Reset All Settings
                            </button>
                        </div>
                    </div>
                `
            }));

            // Handle export
            $(document).on('click', '.export-settings', function() {
                const settings = {};
                wp.customize.each((setting, id) => {
                    if (id.startsWith('oltreblocksy_')) {
                        settings[id] = setting.get();
                    }
                });

                const dataStr = JSON.stringify(settings, null, 2);
                const dataBlob = new Blob([dataStr], { type: 'application/json' });
                const url = URL.createObjectURL(dataBlob);
                
                const link = document.createElement('a');
                link.href = url;
                link.download = 'oltreblocksy-settings.json';
                link.click();
                
                URL.revokeObjectURL(url);
            });

            // Handle import
            $(document).on('click', '.import-settings', function() {
                const fileInput = document.getElementById('import-file');
                const file = fileInput.files[0];
                
                if (!file) {
                    alert('Please select a file to import.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const settings = JSON.parse(e.target.result);
                        
                        Object.entries(settings).forEach(([key, value]) => {
                            const setting = wp.customize(key);
                            if (setting) {
                                setting.set(value);
                            }
                        });
                        
                        alert('Settings imported successfully!');
                    } catch (error) {
                        alert('Error importing settings. Please check the file format.');
                    }
                };
                reader.readAsText(file);
            });
        },

        /**
         * Color scheme presets
         */
        setupPresets() {
            const presets = {
                'modern-blue': {
                    name: 'Modern Blue',
                    settings: {
                        'oltreblocksy_primary_color': '#1e40af',
                        'oltreblocksy_secondary_color': '#64748b',
                        'oltreblocksy_accent_color': '#f59e0b'
                    }
                },
                'elegant-purple': {
                    name: 'Elegant Purple',
                    settings: {
                        'oltreblocksy_primary_color': '#7c3aed',
                        'oltreblocksy_secondary_color': '#6b7280',
                        'oltreblocksy_accent_color': '#10b981'
                    }
                },
                'warm-orange': {
                    name: 'Warm Orange',
                    settings: {
                        'oltreblocksy_primary_color': '#ea580c',
                        'oltreblocksy_secondary_color': '#64748b',
                        'oltreblocksy_accent_color': '#06b6d4'
                    }
                }
            };

            // Add preset selector to colors section
            const colorsSection = wp.customize.section('oltreblocksy_colors');
            if (colorsSection) {
                const presetHtml = `
                    <div class="customize-control customize-control-select color-presets-control">
                        <label class="customize-control-title">Color Presets</label>
                        <select class="color-preset-selector">
                            <option value="">Choose a preset...</option>
                            ${Object.entries(presets).map(([key, preset]) => 
                                `<option value="${key}">${preset.name}</option>`
                            ).join('')}
                        </select>
                    </div>
                `;

                colorsSection.contentContainer.prepend(presetHtml);

                $(document).on('change', '.color-preset-selector', function() {
                    const presetKey = $(this).val();
                    if (!presetKey || !presets[presetKey]) return;

                    const preset = presets[presetKey];
                    Object.entries(preset.settings).forEach(([key, value]) => {
                        const setting = wp.customize(key);
                        if (setting) {
                            setting.set(value);
                        }
                    });
                });
            }
        },

        /**
         * Reset functionality
         */
        setupReset() {
            $(document).on('click', '.reset-all-settings', function() {
                if (!confirm('Are you sure you want to reset all theme settings to defaults? This cannot be undone.')) {
                    return;
                }

                // Reset all oltreblocksy settings to defaults
                wp.customize.each((setting, id) => {
                    if (id.startsWith('oltreblocksy_') && setting.default) {
                        setting.set(setting.default());
                    }
                });

                alert('All settings have been reset to defaults.');
            });
        },

        /**
         * Color utility functions
         */
        hexToHsl(hex) {
            const r = parseInt(hex.slice(1, 3), 16) / 255;
            const g = parseInt(hex.slice(3, 5), 16) / 255;
            const b = parseInt(hex.slice(5, 7), 16) / 255;

            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }

            return { h: h * 360, s: s * 100, l: l * 100 };
        },

        hslToHex(h, s, l) {
            h /= 360;
            s /= 100;
            l /= 100;

            const c = (1 - Math.abs(2 * l - 1)) * s;
            const x = c * (1 - Math.abs((h * 6) % 2 - 1));
            const m = l - c / 2;
            let r = 0, g = 0, b = 0;

            if (0 <= h && h < 1/6) {
                r = c; g = x; b = 0;
            } else if (1/6 <= h && h < 1/3) {
                r = x; g = c; b = 0;
            } else if (1/3 <= h && h < 1/2) {
                r = 0; g = c; b = x;
            } else if (1/2 <= h && h < 2/3) {
                r = 0; g = x; b = c;
            } else if (2/3 <= h && h < 5/6) {
                r = x; g = 0; b = c;
            } else if (5/6 <= h && h < 1) {
                r = c; g = 0; b = x;
            }

            r = Math.round((r + m) * 255);
            g = Math.round((g + m) * 255);
            b = Math.round((b + m) * 255);

            return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
    };

    // Initialize when customizer is ready
    wp.customize.bind('ready', () => {
        OltreBlocksyCustomizer.init();
    });

})(jQuery);