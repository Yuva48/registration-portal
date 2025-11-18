<?php
/**
 * Entry point for the Registration Portal on Railway
 */

// Get the port from environment (Railway sets this)
$port = getenv('PORT') ?: 8000;

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
        if (file_exists('index.html')) {
            include 'index.html';
        } else {
            include 'public/index.html';
        }
        break;
    
    case '/styles.css':
        header('Content-Type: text/css');
        if (file_exists('styles.css')) {
            include 'styles.css';
        } else {
            include 'public/styles.css';
        }
        break;
    
    case '/script.js':
        header('Content-Type: application/javascript');
        if (file_exists('script.js')) {
            include 'script.js';
        } else {
            include 'public/script.js';
        }
        break;
    
    case '/process-simple.php':
        if (file_exists('process-simple.php')) {
            include 'process-simple.php';
        } else {
            include 'public/process-simple.php';
        }
        break;
    
    case '/success.php':
        if (file_exists('success.php')) {
            include 'success.php';
        } else {
            include 'public/success.php';
        }
        break;
    
    default:
        // 404 for other paths
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Page Not Found - Registration Portal</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f8f9fa; }
                h1 { color: #e74c3c; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>404 - Page Not Found</h1>
                <p>The page you are looking for does not exist.</p>
                <a href="/" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Return to Registration Portal</a>
            </div>
        </body>
        </html>';
        break;
}
?>