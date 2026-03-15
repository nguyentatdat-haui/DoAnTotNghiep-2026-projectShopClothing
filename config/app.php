<?php

/**
 * Application Configuration
 * 
 * This file contains application-wide configuration settings
 */

return [
    // Application Environment
    'debug' => true, // Set to false for production
    'env' => 'development', // development, staging, production
    
    // Application Settings
    'name' => 'Tsuma Detective',
    'url' => 'http://localhost/tsuma-new',
    'timezone' => 'Asia/Ho_Chi_Minh',
    
    // Error Handling
    'error_reporting' => E_ALL,
    'log_errors' => true,
    'log_file' => 'storage/logs/error.log',
    
    // Database (if needed)
    'database' => [
        'host' => 'localhost',
        'name' => 'your_database_name',
        'user' => 'your_username',
        'pass' => 'your_password',
        'charset' => 'utf8mb4'
    ],

    // Cache
    'cache' => [
        'driver' => 'file',
        'path' => 'storage/cache',
        'default_ttl' => 600, // seconds; 0 = never expire
    ],
];
