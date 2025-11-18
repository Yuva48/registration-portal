#!/bin/bash

# Install dependencies if composer.json exists
if [ -f "composer.json" ]; then
    echo "Installing PHP dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# Create required directories
echo "Setting up directories..."
mkdir -p data uploads
chmod 755 data uploads

echo "Build completed successfully!"