<?php

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration BEFORE using Config
require_once __DIR__ . '/../config/config.php';

// Load helpers
require_once __DIR__ . '/Helpers/CityHelper.php';
require_once __DIR__ . '/Helpers/CommonHelper.php';
require_once __DIR__ . '/Helpers/ErrorHandler.php';

// Set timezone
date_default_timezone_set(Config::get('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'));

// Set error reporting based on environment
// Force debug mode for development
$isDebug = Config::bool('APP_DEBUG', false); // Default to true for development

if ($isDebug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // Let ErrorHandler handle display
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

// Initialize custom error handler
ErrorHandler::init();

// PSR-4 like autoloader for App namespace and core classes
spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');

    // Load App\* classes from app/ directory
    if (strpos($class, 'App\\') === 0) {
        $relative = substr($class, 4); // remove 'App\'
        $path = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }

    // Load core classes in global namespace from core/
    $corePath = __DIR__ . '/../core/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($corePath)) {
        require_once $corePath;
        return;
    }

    // Fallback: direct path under app/
    $fallbackAppPath = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($fallbackAppPath)) {
        require_once $fallbackAppPath;
    }
});

// Helper functions are now loaded from CommonHelper.php
// This keeps bootstrap.php clean and focused on initialization

// Database connections are now lazy-loaded
// They will be initialized only when actually needed by models/repositories
// This allows projects to use only named connections (e.g., 'COMMON') without requiring default DB config
