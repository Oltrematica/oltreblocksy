# OltreBlocksy Theme Testing

This directory contains comprehensive tests for the OltreBlocksy WordPress theme using the Pest testing framework.

## Test Structure

```
tests/
├── Unit/                    # Unit tests for individual components
│   ├── Modules/            # Tests for individual modules
│   │   ├── BaseModuleTest.php
│   │   ├── PerformanceTest.php
│   │   ├── TypographyTest.php
│   │   ├── ColorSystemTest.php
│   │   ├── AccessibilityTest.php
│   │   └── CustomizerTest.php
│   ├── HelpersTest.php     # Tests for helper functions
│   └── ThemeTest.php       # Tests for main theme class
├── Integration/            # Integration tests
│   └── ModuleIntegrationTest.php
├── Performance/           # Performance benchmarks
│   └── PerformanceMetricsTest.php
├── bootstrap.php          # Test bootstrap file
├── TestCase.php          # Base test case class
└── Pest.php              # Pest configuration
```

## Test Categories

### Unit Tests
- **BaseModuleTest**: Tests the abstract base module functionality including settings management, dependency checking, and lifecycle methods
- **PerformanceTest**: Tests performance optimization features like CSS minification, script optimization, lazy loading, and critical CSS generation
- **TypographyTest**: Tests typography system including font loading, CSS generation, fluid typography, and preset management
- **ColorSystemTest**: Tests color palette management, contrast checking, color harmony generation, and dark mode CSS
- **AccessibilityTest**: Tests WCAG compliance features, ARIA enhancements, focus management, and content accessibility improvements
- **CustomizerTest**: Tests WordPress Customizer integration, settings panels, and AJAX functionality
- **HelpersTest**: Tests utility functions for icons, performance metrics, image optimization, and theme options
- **ThemeTest**: Tests main theme class, module loading, hook registration, and WordPress integration

### Integration Tests
- **ModuleIntegrationTest**: Tests how modules work together, cross-module functionality, and shared dependencies

### Performance Tests
- **PerformanceMetricsTest**: Benchmarks critical operations like CSS minification, color calculations, and module initialization

## Running Tests

### Prerequisites

1. Install dependencies:
```bash
composer install
```

2. Ensure PHP 8.0+ is installed

### Running All Tests
```bash
composer test
```

Or directly with Pest:
```bash
vendor/bin/pest
```

### Running Specific Test Suites
```bash
# Unit tests only
composer run test:unit

# Integration tests only
composer run test:integration

# Performance tests only
composer run test:performance
```

### Running Tests with Coverage
```bash
composer run test:coverage
```

### Running Specific Test Files
```bash
vendor/bin/pest tests/Unit/Modules/PerformanceTest.php
```

### Verbose Output
```bash
vendor/bin/pest --verbose
```

## Test Features

### Mocking
The tests use Brain Monkey to mock WordPress functions, ensuring tests run independently of WordPress:

- WordPress core functions (`add_action`, `add_filter`, `get_theme_mod`, etc.)
- Theme-specific functions
- Database operations
- HTTP requests

### Custom Assertions
The base `TestCase` class provides custom assertions:

- `assertCssContains()`: Check CSS contains specific properties
- `assertValidHexColor()`: Validate hex color format
- `assertArrayStructure()`: Validate array structure
- `assertHookAdded()`: Verify WordPress hooks were registered

### Test Data
Tests use realistic data:

- Typography presets with proper font families and weights
- Color palettes with WCAG-compliant contrast ratios
- Performance benchmarks with measurable targets
- Accessibility features with ARIA attributes

## Coverage

The test suite covers:

- ✅ All public methods in modules
- ✅ Core theme functionality
- ✅ WordPress integration points
- ✅ Error handling and edge cases
- ✅ Performance characteristics
- ✅ Accessibility compliance
- ✅ Cross-module interactions

## Writing New Tests

### Basic Test Structure
```php
<?php

describe('Feature Name', function () {
    
    beforeEach(function () {
        // Setup before each test
    });
    
    it('should do something', function () {
        // Test implementation
        expect($result)->toBe($expected);
    });
    
});
```

### Testing WordPress Integration
```php
it('registers WordPress hooks', function () {
    Monkey\Functions\when('add_action')->justReturn(true);
    
    $module = new YourModule();
    
    // Test hook registration
    $this->assertHookAdded('wp_head', [$module, 'method_name']);
});
```

### Testing CSS Output
```php
it('generates valid CSS', function () {
    $css = $module->generate_css();
    
    $this->assertCssContains($css, [
        'color' => '#ff0000',
        'font-size' => '1rem'
    ]);
});
```

## Continuous Integration

The tests are designed to run in CI environments:

- No external dependencies
- Deterministic results
- Fast execution (< 30 seconds for full suite)
- Clear failure messages

## Performance Targets

Tests include performance benchmarks with targets:

- CSS minification: < 100ms for 1000 rules
- Color contrast calculation: < 100ms for 100 combinations
- Module initialization: < 5MB memory usage
- Typography CSS generation: < 200ms for 50 presets

## Contributing

When adding new features:

1. Write tests first (TDD approach)
2. Ensure 100% code coverage for new code
3. Add integration tests for cross-module features
4. Include performance benchmarks for critical operations
5. Update this README if adding new test categories