#!/bin/bash

# Script to check system requirements before setting up the application
# Based on docker/8.0/Dockerfile requirements

set -e

echo "========================================="
echo "Sowidu - Requirements Check"
echo "========================================="
echo ""

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Track if all checks pass
all_checks_passed=true

# Function to print success
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

# Function to print error
print_error() {
    echo -e "${RED}✗${NC} $1"
    all_checks_passed=false
}

# Function to print warning
print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# Check if Docker is installed
echo "Checking system requirements..."
echo ""

if command -v docker &> /dev/null; then
    docker_version=$(docker --version | awk '{print $3}' | sed 's/,//')
    print_success "Docker installed (version $docker_version)"
else
    print_error "Docker is not installed"
    echo "  Install from: https://docs.docker.com/get-docker/"
fi

# Check if Docker Compose is installed
if command -v docker-compose &> /dev/null || docker compose version &> /dev/null; then
    if command -v docker-compose &> /dev/null; then
        compose_version=$(docker-compose --version | awk '{print $3}' | sed 's/,//')
    else
        compose_version=$(docker compose version --short)
    fi
    print_success "Docker Compose installed (version $compose_version)"
else
    print_error "Docker Compose is not installed"
    echo "  Usually bundled with Docker Desktop"
fi

# Check if Docker daemon is running
if docker info &> /dev/null; then
    print_success "Docker daemon is running"
else
    print_error "Docker daemon is not running"
    echo "  Start Docker Desktop or run: sudo systemctl start docker"
fi

# Check if Git is installed
if command -v git &> /dev/null; then
    git_version=$(git --version | awk '{print $3}')
    print_success "Git installed (version $git_version)"
else
    print_error "Git is not installed"
fi

# Check SSH configuration for GitHub
echo ""
echo "Checking GitHub SSH access..."
if ssh -T git@github.com 2>&1 | grep -q "successfully authenticated"; then
    print_success "GitHub SSH authentication configured"
elif ssh -T git@github.com 2>&1 | grep -q "Hi"; then
    print_success "GitHub SSH authentication configured"
else
    print_error "GitHub SSH authentication not configured"
    echo "  Run: ssh-keygen -t ed25519 -C 'your_email@example.com'"
    echo "  Add the key to GitHub: https://github.com/settings/keys"
fi

# Check if required files exist
echo ""
echo "Checking project files..."

if [ -f "composer.json" ]; then
    print_success "composer.json found"
else
    print_error "composer.json not found (are you in the project root?)"
fi

if [ -f "package.json" ]; then
    print_success "package.json found"
else
    print_error "package.json not found"
fi

if [ -f "env.local" ]; then
    print_success "env.local template found"
else
    print_warning "env.local template not found"
fi

if [ -f ".env" ]; then
    print_warning ".env file already exists (will not be overwritten)"
else
    print_success ".env file not found (ready to create from template)"
fi

# Check Docker image requirements by testing the PHP image
echo ""
echo "Checking Docker PHP image..."

# Try to pull the required image
if docker pull laravelsail/php80-composer:latest &> /dev/null; then
    print_success "Laravel Sail PHP 8.0 image available"

    # Check PHP extensions in the image
    echo ""
    echo "Verifying PHP extensions in Docker image..."

    required_extensions=(
        "bcmath"
        "curl"
        "exif"
        "gd"
        "imagick"
        "intl"
        "mbstring"
        "mysql"
        "pcov"
        "pdo_mysql"
        "redis"
        "soap"
        "xml"
        "zip"
    )

    for ext in "${required_extensions[@]}"; do
        if docker run --rm laravelsail/php80-composer:latest php -m 2>/dev/null | grep -qi "^$ext$"; then
            print_success "PHP extension: $ext"
        else
            print_warning "PHP extension may be missing: $ext"
        fi
    done
else
    print_error "Failed to pull Laravel Sail PHP 8.0 image"
    echo "  Check your internet connection"
fi

# Check available disk space
echo ""
echo "Checking disk space..."
available_space=$(df -h . | awk 'NR==2 {print $4}')
print_success "Available disk space: $available_space"

# Check if ports are available
echo ""
echo "Checking port availability..."

check_port() {
    local port=$1
    local service=$2
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null 2>&1 || ss -ltn | grep -q ":$port " 2>/dev/null; then
        print_warning "Port $port is in use (needed for $service)"
    else
        print_success "Port $port is available ($service)"
    fi
}

check_port 80 "Application"
check_port 3306 "MySQL"
check_port 6379 "Redis"
check_port 8025 "Mailhog"

# Summary
echo ""
echo "========================================="
if [ "$all_checks_passed" = true ]; then
    echo -e "${GREEN}All critical checks passed!${NC}"
    echo "You can proceed with the installation."
    echo ""
    echo "Next steps:"
    echo "  1. Run: ./scripts/clone_packages.sh"
    echo "  2. Run: cp env.local .env (and configure)"
    echo "  3. Run the composer install command from SETUP.md"
    exit 0
else
    echo -e "${RED}Some checks failed!${NC}"
    echo "Please fix the issues above before proceeding."
    exit 1
fi
echo "========================================="

