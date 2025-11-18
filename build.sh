#!/bin/bash
# Render.com build script

echo "Starting Render deployment..."

# Create necessary directories
mkdir -p data
mkdir -p uploads

# Set proper permissions
chmod 755 data
chmod 755 uploads

echo "Build completed successfully!"