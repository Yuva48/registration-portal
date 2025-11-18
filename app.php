<?php
/**
 * Simple deployment check for Render/Railway
 */

// Create data directory if it doesn't exist
if (!is_dir('data')) {
    mkdir('data', 0755, true);
}

// Create uploads directory if it doesn't exist  
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

// Include the main application
include 'index.php';
?>