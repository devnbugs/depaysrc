#!/bin/bash

# ============================================
# OneTeera Deployment & Setup Script
# ============================================
# This script:
# 1. Installs PHP dependencies (Composer)
# 2. Installs Node dependencies (NPM)
# 3. Builds frontend assets
# 4. Optimizes Laravel caches
# 5. Clears and refreshes application state

set -e  # Exit on first error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ============================================
# Helper Functions
# ============================================

print_header() {
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# ============================================
# Pre-flight Checks
# ============================================

print_header "Pre-flight Checks"

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    print_error "composer.json not found. Please run this script from the project root."
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    print_error "PHP is not installed or not in PATH."
    exit 1
fi
print_success "PHP found: $(php -v | head -n 1)"

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed or not in PATH."
    exit 1
fi
print_success "Composer found: $(composer --version)"

# Check if Node is installed
if ! command -v node &> /dev/null; then
    print_warning "Node.js is not installed. Skipping npm install and build."
    SKIP_NPM=true
else
    print_success "Node found: $(node --version)"
    if ! command -v npm &> /dev/null; then
        print_warning "npm is not installed. Skipping npm install and build."
        SKIP_NPM=true
    else
        print_success "npm found: $(npm --version)"
    fi
fi

# ============================================
# Step 1: Install PHP Dependencies
# ============================================

print_header "Step 1: Installing PHP Dependencies"

if [ -d "vendor" ]; then
    print_info "Vendor directory exists. Running 'composer install' to update..."
else
    print_info "Vendor directory not found. Running 'composer install'..."
fi

if composer install --no-interaction --prefer-dist --optimize-autoloader; then
    print_success "PHP dependencies installed successfully"
else
    print_error "Failed to install PHP dependencies"
    exit 1
fi

# ============================================
# Step 2: Install Node Dependencies (if available)
# ============================================

if [ -z "$SKIP_NPM" ]; then
    print_header "Step 2: Installing Node Dependencies"

    if [ -d "node_modules" ]; then
        print_info "Node modules directory exists. Running 'npm install' to update..."
    else
        print_info "Node modules directory not found. Running 'npm install'..."
    fi

    if npm install; then
        print_success "Node dependencies installed successfully"
    else
        print_error "Failed to install Node dependencies"
        exit 1
    fi
else
    print_header "Step 2: Skipping Node Dependencies"
    print_warning "Node/npm not available. Skipping npm install."
fi

# ============================================
# Step 3: Build Frontend Assets (if npm available)
# ============================================

if [ -z "$SKIP_NPM" ]; then
    print_header "Step 3: Building Frontend Assets"

    # Check if build script exists in package.json
    if grep -q '"build"' package.json; then
        print_info "Running 'npm run build'..."
        
        if npm run build; then
            print_success "Frontend assets built successfully"
        else
            print_error "Failed to build frontend assets"
            exit 1
        fi
    else
        print_warning "No build script found in package.json. Skipping build."
    fi
else
    print_header "Step 3: Skipping Frontend Build"
fi

# ============================================
# Step 4: Generate Application Key (if not set)
# ============================================

print_header "Step 4: Checking Application Key"

if grep -q "APP_KEY=$" .env 2>/dev/null || [ ! -f ".env" ]; then
    print_info "Generating application key..."
    
    if php artisan key:generate --force; then
        print_success "Application key generated"
    else
        print_warning "Could not generate application key. It may already exist."
    fi
else
    print_success "Application key already set"
fi

# ============================================
# Step 5: Clear Existing Caches
# ============================================

print_header "Step 5: Clearing Existing Caches"

php artisan config:clear && print_success "Config cache cleared"
php artisan route:clear && print_success "Route cache cleared"
php artisan view:clear && print_success "View cache cleared"
php artisan cache:clear && print_success "Application cache cleared"

# ============================================
# Step 6: Optimize Application
# ============================================

print_header "Step 6: Optimizing Application"

php artisan config:cache && print_success "Config cache built"
php artisan route:cache && print_success "Route cache built"
php artisan view:cache && print_success "View cache built"

# Try to run optimize if available
if php artisan optimize &>/dev/null; then
    print_success "Application optimized"
else
    print_info "Application optimize command not available or already optimized"
fi

# ============================================
# Step 7: Storage & Permissions (Unix/Linux only)
# ============================================

if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "cygwin" && "$OSTYPE" != "win32" ]]; then
    print_header "Step 7: Setting Storage Permissions"

    if [ -d "storage" ]; then
        chmod -R 775 storage || print_warning "Could not set storage permissions (may require sudo)"
        chmod -R 775 bootstrap/cache || print_warning "Could not set bootstrap/cache permissions (may require sudo)"
        print_success "Storage permissions set"
    fi
else
    print_header "Step 7: Skipping Permission Setup"
    print_info "Running on Windows (no chmod needed)"
fi

# ============================================
# Step 8: Summary
# ============================================

print_header "Deployment Complete!"

print_success "All setup steps completed successfully"
echo ""
echo -e "${BLUE}Summary:${NC}"
echo -e "  ${GREEN}✓${NC} PHP dependencies installed"
if [ -z "$SKIP_NPM" ]; then
    echo -e "  ${GREEN}✓${NC} Node dependencies installed"
    echo -e "  ${GREEN}✓${NC} Frontend assets built"
fi
echo -e "  ${GREEN}✓${NC} Application key verified"
echo -e "  ${GREEN}✓${NC} Caches cleared"
echo -e "  ${GREEN}✓${NC} Application optimized"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "  1. Configure your .env file if not already done"
echo "  2. Run migrations: php artisan migrate"
echo "  3. (Optional) Run seeders: php artisan db:seed"
echo "  4. Start your application"
echo ""
echo -e "${GREEN}Happy coding! 🚀${NC}\n"

exit 0
