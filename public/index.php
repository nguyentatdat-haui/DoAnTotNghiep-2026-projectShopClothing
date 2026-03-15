<?php

// Basic security headers
// header('X-Frame-Options: DENY');
// header('X-Content-Type-Options: nosniff');
// header('Referrer-Policy: no-referrer-when-downgrade');
// header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' data: https://fonts.gstatic.com");

// Define the application path
define('APP_PATH', dirname(__DIR__));
spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
    $baseDir = APP_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($class, $prefix, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});


// Include the bootstrap file
require_once APP_PATH . '/app/bootstrap.php';

// Include the router
require_once APP_PATH . '/core/Router.php';

// Create router instance
$router = new Router();

// Include routes
require_once APP_PATH . '/routes/web.php';

// Get the current URI and method
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Normalize URI by removing base paths when app is in subfolder
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);        // e.g. /mvc_base/public
$baseDir   = dirname($scriptDir);                     // e.g. /mvc_base

foreach ([$scriptDir, $baseDir] as $basePath) {
    if ($basePath && $basePath !== '/' && $basePath !== '\\') {
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
    }
}

if ($uri === '' || $uri[0] !== '/') {
    $uri = '/' . $uri;
}

// Handle PUT and DELETE methods
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

// Dispatch the request and output the response
$response = $router->dispatch($uri, $method);
if (is_string($response)) {
    echo $response;
}
