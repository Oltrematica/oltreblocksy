<?php

use OltreBlocksy\Modules\Base_Module;
use Brain\Monkey;

/**
 * Test Base_Module functionality
 */
describe('Base_Module', function () {

    beforeEach(function () {
        // Create a concrete implementation of Base_Module for testing
        $this->moduleClass = new class extends Base_Module {
            public function __construct() {
                // Skip parent constructor for testing
            }
            
            protected function get_name() {
                return 'TestModule';
            }
            
            protected function init() {
                // Test implementation
            }
            
            // Expose protected methods for testing
            public function test_check_dependencies() {
                return $this->check_dependencies();
            }
            
            public function test_is_dependency_available($dependency) {
                return $this->is_dependency_available($dependency);
            }
            
            public function test_load_settings() {
                $this->load_settings();
            }
            
            // Set dependencies for testing
            public function set_test_dependencies($dependencies) {
                $this->dependencies = $dependencies;
            }
            
            // Set settings for testing
            public function set_test_settings($settings) {
                $this->settings = $settings;
            }
        };
    });

    it('can be instantiated', function () {
        expect($this->moduleClass)->toBeInstanceOf(Base_Module::class);
    });

    it('returns correct module name', function () {
        expect($this->moduleClass->get_name())->toBe('TestModule');
    });

    it('has default enabled status', function () {
        // Mock get_theme_mod to return default enabled status
        Monkey\Functions\when('get_theme_mod')
            ->with('oltreblocksy_module_testmodule_enabled', true)
            ->justReturn(true);
            
        expect($this->moduleClass->is_enabled())->toBeTrue();
    });

    it('can be enabled and disabled', function () {
        // Mock set_theme_mod
        Monkey\Functions\when('set_theme_mod')->justReturn(true);
        
        $this->moduleClass->enable();
        expect($this->moduleClass->is_enabled())->toBeTrue();
        
        $this->moduleClass->disable();
        expect($this->moduleClass->is_enabled())->toBeFalse();
    });

    it('checks plugin dependencies correctly', function () {
        // Mock is_plugin_active
        Monkey\Functions\when('is_plugin_active')
            ->with('test-plugin/test-plugin.php')
            ->justReturn(true);
        
        $dependency = [
            'type' => 'plugin',
            'path' => 'test-plugin/test-plugin.php'
        ];
        
        expect($this->moduleClass->test_is_dependency_available($dependency))->toBeTrue();
    });

    it('checks function dependencies correctly', function () {
        $dependency = [
            'type' => 'function',
            'name' => 'json_encode' // This function exists
        ];
        
        expect($this->moduleClass->test_is_dependency_available($dependency))->toBeTrue();
        
        $dependency = [
            'type' => 'function',
            'name' => 'non_existent_function'
        ];
        
        expect($this->moduleClass->test_is_dependency_available($dependency))->toBeFalse();
    });

    it('checks class dependencies correctly', function () {
        $dependency = [
            'type' => 'class',
            'name' => 'stdClass' // This class exists
        ];
        
        expect($this->moduleClass->test_is_dependency_available($dependency))->toBeTrue();
        
        $dependency = [
            'type' => 'class',
            'name' => 'NonExistentClass'
        ];
        
        expect($this->moduleClass->test_is_dependency_available($dependency))->toBeFalse();
    });

    it('passes dependency check with no dependencies', function () {
        $this->moduleClass->set_test_dependencies([]);
        expect($this->moduleClass->test_check_dependencies())->toBeTrue();
    });

    it('fails dependency check with unmet dependencies', function () {
        $dependencies = [
            [
                'type' => 'function',
                'name' => 'non_existent_function'
            ]
        ];
        
        $this->moduleClass->set_test_dependencies($dependencies);
        expect($this->moduleClass->test_check_dependencies())->toBeFalse();
    });

    it('can get and set individual settings', function () {
        $this->moduleClass->set_test_settings(['test_key' => 'test_value']);
        
        expect($this->moduleClass->get_setting('test_key'))->toBe('test_value');
        expect($this->moduleClass->get_setting('non_existent_key', 'default'))->toBe('default');
    });

    it('can save settings', function () {
        // Mock set_theme_mod
        Monkey\Functions\when('set_theme_mod')->justReturn(true);
        
        $settings = ['key1' => 'value1', 'key2' => 'value2'];
        $this->moduleClass->save_settings($settings);
        
        expect($this->moduleClass->get_setting('key1'))->toBe('value1');
        expect($this->moduleClass->get_setting('key2'))->toBe('value2');
    });

    it('can set individual setting', function () {
        // Mock set_theme_mod
        Monkey\Functions\when('set_theme_mod')->justReturn(true);
        
        $this->moduleClass->set_setting('test_key', 'test_value');
        expect($this->moduleClass->get_setting('test_key'))->toBe('test_value');
    });

    it('returns correct module info', function () {
        $this->moduleClass->set_test_settings(['test_setting' => 'test_value']);
        $this->moduleClass->set_test_dependencies([]);
        
        // Mock is_enabled
        Monkey\Functions\when('get_theme_mod')
            ->with('oltreblocksy_module_testmodule_enabled', true)
            ->justReturn(true);
        
        $info = $this->moduleClass->get_info();
        
        expect($info)->toHaveKey('name');
        expect($info)->toHaveKey('version');
        expect($info)->toHaveKey('enabled');
        expect($info)->toHaveKey('dependencies');
        expect($info)->toHaveKey('settings');
        
        expect($info['name'])->toBe('TestModule');
        expect($info['enabled'])->toBeTrue();
        expect($info['settings'])->toHaveKey('test_setting');
    });

    it('handles module activation', function () {
        // Should not throw any exceptions
        expect(fn() => $this->moduleClass->activate())->not->toThrow(Exception::class);
    });

    it('handles module deactivation', function () {
        // Should not throw any exceptions
        expect(fn() => $this->moduleClass->deactivate())->not->toThrow(Exception::class);
    });

    it('can handle customizer registration', function () {
        // Mock WP_Customize_Manager
        $wp_customize = Mockery::mock('WP_Customize_Manager');
        
        // Should not throw any exceptions
        expect(fn() => $this->moduleClass->customize_register($wp_customize))->not->toThrow(Exception::class);
    });

    it('can handle ajax requests', function () {
        // Should not throw any exceptions
        expect(fn() => $this->moduleClass->handle_ajax())->not->toThrow(Exception::class);
    });

});