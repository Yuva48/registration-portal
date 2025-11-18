#!/bin/bash
# Render.com build script

echo "Starting Render deployment..."

# Create necessary directories
mkdir -p data
mkdir -p uploads

# Set proper permissions
chmod 755 data
chmod 755 uploads

# Install any PHP dependencies if composer.json exists
if [ -f composer.json ]; then
    composer install --no-dev --optimize-autoloader
fi

echo "Build completed successfully!"