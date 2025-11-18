#!/bin/bash
# Render.com build script for PHP

echo "Installing PHP and dependencies..."

# Install Composer dependencies
if [ -f "composer.json" ]; then
    composer install --no-dev --optimize-autoloader
fi

# Create necessary directories
mkdir -p data
mkdir -p uploads

# Set proper permissions
chmod 755 data
chmod 755 uploads

echo "Build completed successfully!"