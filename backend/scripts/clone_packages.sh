#!/bin/bash

# Script to clone private packages from GitHub
# Assumes SSH key configuration and repository permissions are already set up

# Destination folder for packages
DESTINATION_FOLDER="../../packages"

# Array of package SSH URLs
PACKAGES=(
    "git@github.com:sowiduteam/translation.git"
    "git@github.com:sowiduteam/contacts.git"
    "git@github.com:sowiduteam/addressable.git"
    "git@github.com:sowiduteam/avatarable.git"
    "git@github.com:sowiduteam/active-status.git"
)

# Create destination folder if it doesn't exist
if [ ! -d "$DESTINATION_FOLDER" ]; then
    echo "Creating destination folder: $DESTINATION_FOLDER"
    mkdir -p "$DESTINATION_FOLDER"
fi

# Change to destination folder
cd "$DESTINATION_FOLDER" || exit 1

echo "Cloning packages to: $(pwd)"
echo "----------------------------------------"

# Counters for summary
cloned_count=0
skipped_count=0
failed_count=0

# Clone each package
for package_url in "${PACKAGES[@]}"; do
    # Extract package name from URL (remove .git extension)
    package_name=$(basename "$package_url" .git)

    echo ""
    echo "Processing $package_name..."

    # Check if package directory already exists
    if [ -d "$package_name" ]; then
        echo "⚠️  Package already exists. Skipping clone."
        ((skipped_count++))
    else
        # Clone the package
        if git clone "$package_url"; then
            echo "✓ Successfully cloned $package_name"
            ((cloned_count++))
        else
            echo "✗ Failed to clone $package_name"
            ((failed_count++))
        fi
    fi
done

echo ""
echo "----------------------------------------"
echo "Summary:"
echo "  Cloned: $cloned_count"
echo "  Skipped (already exist): $skipped_count"
echo "  Failed: $failed_count"
echo "----------------------------------------"
echo "Done!"

