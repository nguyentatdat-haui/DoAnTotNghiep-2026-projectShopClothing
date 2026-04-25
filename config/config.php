<?php

class Config
{
    private static $config = [];

    public static function load()
    {
        // Load .env file if exists
        $envFile = __DIR__ . '/../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || strpos($line, '#') === 0) {
                    continue;
                }
                
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    if (preg_match('/^"(.+)"$/', $value, $matches) || preg_match("/^'(.+)'$/", $value, $matches)) {
                        $value = $matches[1];
                    }
                    
                    self::$config[$key] = $value;
                }
            }
        }
        
        // Load app.php config file
        $appConfigFile = __DIR__ . '/app.php';
        if (file_exists($appConfigFile)) {
            $appConfig = include $appConfigFile;
            if (is_array($appConfig)) {
                // Flatten array keys with dot notation
                foreach ($appConfig as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            self::$config[strtoupper($key . '_' . $subKey)] = $subValue;
                        }
                    } else {
                        self::$config[strtoupper($key)] = $value;
                    }
                }
            }
        }
    }

    public static function get($key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }

    public static function all()
    {
        return self::$config;
    }

    /**
     * Retrieve a boolean configuration value with robust string coercion.
     * Accepts true/false, 1/0, on/off, yes/no, y/n (case-insensitive).
     */
    public static function bool($key, $default = false)
    {
        $value = self::get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            $trueValues = ['1', 'true', 'on', 'yes', 'y'];
            $falseValues = ['0', 'false', 'off', 'no', 'n', ''];

            if (in_array($normalized, $trueValues, true)) {
                return true;
            }
            if (in_array($normalized, $falseValues, true)) {
                return false;
            }
        }

        return (bool)$value;
    }
}

// Load configuration on include
Config::load();
