#!/bin/bash
# install.sh

# Exit immediately if a command exits with a non-zero status.
set -e

# --- Configuration ---
# You can change these variables if needed.
GHOSTSCRIPT_VERSION="10.01.2"
LIBDE265_VERSION="1.0.12"
LIBHEIF_VERSION="1.16.2"

# Temporary directory for downloads and builds
INSTALLS_DIR="/tmp/installs"
IMAGEMAGICK_POLICY_PATH="/etc/ImageMagick-6/policy.xml"

# Update package list and install build essentials
sudo apt update
sudo apt install -y build-essential

# The build-essential package includes:
# - make
# - gcc
# - g++
# - libc6-dev
# - and other essential build tools

# Install additional dependencies for the libraries being compiled
sudo apt install -y wget cmake autotools-dev autoconf libtool pkg-config zlib1g-dev

# Function to check if a command exists.
check_command() {
    if ! command -v "$1" &> /dev/null; then
        echo "Error: Required command '$1' not found. Please install it first."
        exit 1
    fi
}

# Function to install GhostScript.
install_ghostscript() {
    echo "Installing GhostScript..."
    local url="https://github.com/ArtifexSoftware/ghostpdl-downloads/releases/download/gs${GHOSTSCRIPT_VERSION//./}/ghostscript-${GHOSTSCRIPT_VERSION}.tar.gz"
    local dir="ghostscript-${GHOSTSCRIPT_VERSION}"

    mkdir -p "$INSTALLS_DIR"
    wget "$url" -P "$INSTALLS_DIR"
    tar -xvf "${INSTALLS_DIR}/${dir}.tar.gz" -C "$INSTALLS_DIR"

    cd "${INSTALLS_DIR}/${dir}"
    ./configure
    make
    make install

    echo "GhostScript installation complete."
}

# Function to install libde265.
install_libde265() {
    echo "Installing libde265..."
    local url="https://github.com/strukturag/libde265/releases/download/v${LIBDE265_VERSION}/libde265-${LIBDE265_VERSION}.tar.gz"
    local dir="libde265-${LIBDE265_VERSION}"

    mkdir -p "$INSTALLS_DIR"
    wget "$url" -P "$INSTALLS_DIR"
    tar -xvf "${INSTALLS_DIR}/${dir}.tar.gz" -C "$INSTALLS_DIR"

    cd "${INSTALLS_DIR}/${dir}"
    ./autogen.sh
    ./configure
    make -j$(nproc)
    make install

    echo "libde265 installation complete."
}

# Function to install libheif.
install_libheif() {
    echo "Installing libheif..."
    local url="https://github.com/strukturag/libheif/releases/download/v${LIBHEIF_VERSION}/libheif-${LIBHEIF_VERSION}.tar.gz"
    local dir="libheif-${LIBHEIF_VERSION}"

    mkdir -p "$INSTALLS_DIR"
    wget "$url" -P "$INSTALLS_DIR"
    tar -xvf "${INSTALLS_DIR}/${dir}.tar.gz" -C "$INSTALLS_DIR"

    cd "${INSTALLS_DIR}/${dir}"
    mkdir -p build && cd build
    cmake --preset=release ..
    make -j$(nproc)
    make install

    echo "libheif installation complete."
}

# Function to install the Imagick PHP extension.
install_imagick() {
    if [ -z "$1" ]; then
        echo "Error: PHP version not provided for Imagick installation."
        exit 1
    fi

    local php_version="$1"
    echo "Installing Imagick for PHP $php_version..."

    # Install the specific PHP Imagick package
    apt-get update
    apt-get install -y php${php_version}-imagick

    echo "Imagick for PHP $php_version installation complete."
}

# Function to override the ImageMagick policy.
override_imagemagick_policy() {
    echo "Overriding ImageMagick policy.xml..."
    if [ -f "policy.xml" ]; then
        if [ -f "$IMAGEMAGICK_POLICY_PATH" ]; then
            rm "$IMAGEMAGICK_POLICY_PATH"
        fi
        cp "policy.xml" "$IMAGEMAGICK_POLICY_PATH"
        echo "ImageMagick policy.xml overridden."
    else
        echo "Warning: 'policy.xml' not found in the current directory. Skipping policy override."
    fi
}

# --- Main Script Execution ---
echo "Starting software dependency installation..."
check_command "wget"
check_command "tar"
check_command "make"
check_command "cmake"

# The script requires the PHP version as an argument.
if [ -z "$1" ]; then
    echo "Usage: sudo ./install.sh <php_version>"
    echo "Example: sudo ./install.sh 8.2"
    exit 1
fi

install_ghostscript
install_libde265
install_libheif
install_imagick "$1"
override_imagemagick_policy

# Clean up temporary installation files.
echo "Cleaning up temporary files..."
rm -rf "$INSTALLS_DIR"

echo "All installations are complete."
