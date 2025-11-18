<?php
// Simple PHP server entry point for Railway
$request = $_SERVER['REQUEST_URI'];

if ($request === '/') {
    require 'index.html';
} elseif ($request === '/styles.css') {
    header('Content-Type: text/css');
    require 'styles.css';
} elseif ($request === '/script.js') {
    header('Content-Type: application/javascript');
    require 'script.js';
} elseif ($request === '/process-simple.php') {
    require 'process-simple.php';
} elseif ($request === '/success.php') {
    require 'success.php';
} else {
    http_response_code(404);
    echo 'Not Found';
}
?>