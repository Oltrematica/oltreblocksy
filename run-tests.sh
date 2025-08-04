#!/bin/bash

# OltreBlocksy Theme Test Runner
# Comprehensive test suite for the OltreBlocksy WordPress theme

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install Composer first."
    exit 1
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    print_warning "Vendor directory not found. Installing dependencies..."
    composer install
fi

# Function to run tests with timing
run_test_suite() {
    local suite_name=$1
    local command=$2
    
    print_status "Running $suite_name tests..."
    start_time=$(date +%s)
    
    if eval $command; then
        end_time=$(date +%s)
        duration=$((end_time - start_time))
        print_success "$suite_name tests completed in ${duration}s"
        return 0
    else
        print_error "$suite_name tests failed"
        return 1
    fi
}

# Default to running all tests
TEST_SUITE=${1:-"all"}

print_status "OltreBlocksy Theme Test Suite"
print_status "=============================="

case $TEST_SUITE in
    "unit")
        run_test_suite "Unit" "vendor/bin/pest tests/Unit --verbose"
        ;;
    "integration")
        run_test_suite "Integration" "vendor/bin/pest tests/Integration --verbose"
        ;;
    "performance")
        run_test_suite "Performance" "vendor/bin/pest tests/Performance --verbose"
        ;;
    "coverage")
        print_status "Running tests with coverage report..."
        if vendor/bin/pest --coverage --coverage-html=coverage-html --coverage-text; then
            print_success "Coverage report generated in coverage-html/"
            print_status "Text coverage report:"
            cat coverage.txt 2>/dev/null || echo "Text coverage report not generated"
        else
            print_error "Coverage tests failed"
            exit 1
        fi
        ;;
    "fast")
        print_status "Running fast test suite (unit tests only)..."
        run_test_suite "Fast" "vendor/bin/pest tests/Unit"
        ;;
    "ci")
        print_status "Running CI test suite..."
        
        # Run tests with minimal output for CI
        if vendor/bin/pest --compact; then
            print_success "All CI tests passed"
        else
            print_error "CI tests failed"
            exit 1
        fi
        ;;
    "watch")
        print_status "Starting test watcher..."
        print_warning "Tests will re-run when files change. Press Ctrl+C to stop."
        vendor/bin/pest --watch
        ;;
    "profile")
        print_status "Running performance profiling..."
        
        # Run performance tests with timing
        start_time=$(date +%s)
        vendor/bin/pest tests/Performance --verbose
        end_time=$(date +%s)
        duration=$((end_time - start_time))
        
        print_status "Performance Analysis:"
        print_status "Total execution time: ${duration}s"
        
        # Check memory usage if available
        if command -v free &> /dev/null; then
            print_status "Current memory usage:"
            free -h
        fi
        ;;
    "validate")
        print_status "Validating test environment..."
        
        # Check PHP version
        php_version=$(php -v | head -n1 | cut -d' ' -f2)
        print_status "PHP Version: $php_version"
        
        # Check required extensions
        required_extensions=("json" "mbstring" "xml")
        for ext in "${required_extensions[@]}"; do
            if php -m | grep -q "$ext"; then
                print_success "PHP extension '$ext' is available"
            else
                print_error "Required PHP extension '$ext' is missing"
                exit 1
            fi
        done
        
        # Check Pest installation
        if [ -f "vendor/bin/pest" ]; then
            pest_version=$(vendor/bin/pest --version | cut -d' ' -f2)
            print_success "Pest version $pest_version is installed"
        else
            print_error "Pest is not installed"
            exit 1
        fi
        
        # Validate test files
        test_files=$(find tests -name "*.php" | wc -l)
        print_status "Found $test_files test files"
        
        print_success "Test environment validation completed"
        ;;
    "help")
        echo "OltreBlocksy Theme Test Runner"
        echo ""
        echo "Usage: $0 [command]"
        echo ""
        echo "Commands:"
        echo "  all          Run all test suites (default)"
        echo "  unit         Run unit tests only"
        echo "  integration  Run integration tests only"
        echo "  performance  Run performance benchmarks only"
        echo "  coverage     Run tests with coverage report"
        echo "  fast         Run quick unit tests (for development)"
        echo "  ci           Run tests in CI mode (minimal output)"
        echo "  watch        Watch files and re-run tests on changes"
        echo "  profile      Run performance profiling"
        echo "  validate     Validate test environment"
        echo "  help         Show this help message"
        echo ""
        echo "Examples:"
        echo "  $0                    # Run all tests"
        echo "  $0 unit              # Run unit tests only"
        echo "  $0 coverage          # Generate coverage report"
        echo "  $0 watch             # Watch for file changes"
        ;;
    "all"|*)
        print_status "Running complete test suite..."
        
        # Run all test suites
        failed_suites=()
        
        if ! run_test_suite "Unit" "vendor/bin/pest tests/Unit"; then
            failed_suites+=("Unit")
        fi
        
        if ! run_test_suite "Integration" "vendor/bin/pest tests/Integration"; then
            failed_suites+=("Integration")
        fi
        
        if ! run_test_suite "Performance" "vendor/bin/pest tests/Performance"; then
            failed_suites+=("Performance")
        fi
        
        # Summary
        if [ ${#failed_suites[@]} -eq 0 ]; then
            print_success "All test suites passed! 🎉"
            
            # Generate quick coverage report if requested
            if [ "$2" = "--coverage" ]; then
                print_status "Generating coverage report..."
                vendor/bin/pest --coverage --coverage-text | tail -n 20
            fi
        else
            print_error "Failed test suites: ${failed_suites[*]}"
            exit 1
        fi
        ;;
esac

print_status "Test execution completed."