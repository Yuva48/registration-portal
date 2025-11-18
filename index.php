<?php
/**
 * Entry point for the Registration Portal
 * This file handles basic routing and serves the main application
 */

// Set timezone
date_default_timezone_set('UTC');

// Basic error reporting for production
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 0);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Get the requested path
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Handle basic routing
switch ($path) {
    case '/':
    case '/index.html':
        // Serve the main registration form
        include 'public/index.html';
        break;
    
    case '/styles.css':
        header('Content-Type: text/css');
        include 'public/styles.css';
        break;
    
    case '/script.js':
        header('Content-Type: application/javascript');
        include 'public/script.js';
        break;
    
    case '/process-simple.php':
        include 'public/process-simple.php';
        break;
    
    case '/success.php':
        include 'public/success.php';
        break;
    
    default:
        // 404 for other paths
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Page Not Found</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                h1 { color: #e74c3c; }
            </style>
        </head>
        <body>
            <h1>404 - Page Not Found</h1>
            <p>The page you are looking for does not exist.</p>
            <a href="/">Return to Registration Portal</a>
        </body>
        </html>';
        break;
}
?>