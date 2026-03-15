<?php

/**
 * Lightweight file-based cache layer.
 * Uses configurable path and TTL. Safe to swap driver later while keeping API.
 */
class Cache
{
    // Probability-based GC: 1 in 50 operations will sweep expired files.
    private const GC_SAMPLE_RATE = 50;

    private static function driver(): string
    {
        return Config::get('CACHE_DRIVER', 'file');
    }

    private static function basePath(): string
    {
        $configured = Config::get('CACHE_PATH', __DIR__ . '/../storage/cache');
        // Normalize relative paths against project root
        $path = $configured;
        if ($configured && !preg_match('#^([a-zA-Z]:\\\\|/)#', $configured)) {
            $path = __DIR__ . '/../' . ltrim($configured, '/\\');
        }
        if (!is_dir($path)) {
            // Ensure cache directory exists
            @mkdir($path, 0775, true);
        }
        return rtrim($path, '/\\');
    }

    /**
     * Opportunistically prune expired cache files without requiring a direct hit.
     * Keeps the cache folder from growing indefinitely when entries are never read again.
     */
    private static function pruneExpired(): void
    {
        // Only for file driver, and run at a low frequency to keep overhead small.
        if (self::driver() !== 'file' || mt_rand(1, self::GC_SAMPLE_RATE) !== 1) {
            return;
        }

        $now = time();
        foreach (glob(self::basePath() . '/*.cache') as $file) {
            $raw = @file_get_contents($file);
            if ($raw === false) {
                @unlink($file);
                continue;
            }
            $payload = @unserialize($raw);
            if (!is_array($payload) || !isset($payload['ttl'])) {
                @unlink($file);
                continue;
            }
            if ($payload['ttl'] !== 0 && $payload['ttl'] < $now) {
                @unlink($file);
            }
        }
    }

    private static function filename(string $key): string
    {
        $prefix = Config::get('APP_ENV', 'dev');
        return self::basePath() . '/' . md5($prefix . '|' . $key) . '.cache';
    }

    public static function get(string $key, $default = null)
    {
        self::pruneExpired();

        if (self::driver() !== 'file') {
            // Fallback: no-op driver
            return $default;
        }

        $file = self::filename($key);
        if (!is_file($file)) {
            return $default;
        }

        $raw = @file_get_contents($file);
        if ($raw === false) {
            return $default;
        }

        $payload = @unserialize($raw);
        if (!is_array($payload) || !isset($payload['value'], $payload['ttl'])) {
            @unlink($file);
            return $default;
        }

        // TTL == 0 means never expire
        if ($payload['ttl'] !== 0 && $payload['ttl'] < time()) {
            @unlink($file);
            return $default;
        }

        return $payload['value'];
    }

    public static function delete(string $key): void
    {
        if (self::driver() !== 'file') {
            return;
        }
        $file = self::filename($key);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    public static function flush(): void
    {
        if (self::driver() !== 'file') {
            return;
        }

        foreach (glob(self::basePath() . '/*.cache') as $file) {
            @unlink($file);
        }
    }

    /**
     * Delete all cache entries whose key starts with the given prefix.
     * Supports both 'prefix_' and 'prefix:' formats.
     */
    public static function deleteByPrefix(string $prefix): int
    {
        if (self::driver() !== 'file') {
            return 0;
        }

        $deleted = 0;
        $prefixWithUnderscore = $prefix . '_';
        $prefixWithColon = $prefix . ':';

        foreach (glob(self::basePath() . '/*.cache') as $file) {
            $raw = @file_get_contents($file);
            if ($raw === false) {
                continue;
            }

            $payload = @unserialize($raw);
            if (!is_array($payload) || !isset($payload['key'])) {
                // Skip old cache files without key metadata
                continue;
            }

            $key = $payload['key'];
            // Check if key starts with prefix_ or prefix:
            if (strpos($key, $prefixWithUnderscore) === 0 || strpos($key, $prefixWithColon) === 0) {
                @unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Sanitize value before serialization by removing non-serializable objects (PDO, database connections, etc.)
     * Converts Model instances to arrays recursively.
     */
    private static function sanitizeForCache($value)
    {
        // Handle null, scalar values
        if ($value === null || is_scalar($value)) {
            return $value;
        }

        // Handle arrays - recursively sanitize each element
        if (is_array($value)) {
            $sanitized = [];
            foreach ($value as $key => $item) {
                $sanitized[$key] = self::sanitizeForCache($item);
            }
            return $sanitized;
        }

        // Handle objects
        if (is_object($value)) {
            // Check if it's a PDO object or contains PDO
            if ($value instanceof \PDO) {
                return null; // Remove PDO objects
            }

            // Check if object has toArray() method (Model instances)
            if (method_exists($value, 'toArray')) {
                $array = $value->toArray();
                // Recursively sanitize the array
                return self::sanitizeForCache($array);
            }

            // For other objects, try to convert to array and sanitize
            // This handles objects with public properties
            try {
                $reflection = new \ReflectionClass($value);
                $array = [];
                
                // Get public properties
                foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                    $propName = $property->getName();
                    $propValue = $property->getValue($value);
                    
                    // Skip PDO and database connections
                    if ($propValue instanceof \PDO || 
                        (is_object($propValue) && strpos(get_class($propValue), 'Database') !== false)) {
                        continue;
                    }
                    
                    $array[$propName] = self::sanitizeForCache($propValue);
                }
                
                // Also check for protected/private properties that might be accessible
                // But skip 'db' property which contains PDO
                foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE) as $property) {
                    $propName = $property->getName();
                    
                    // Skip database-related properties
                    if ($propName === 'db' || $propName === 'connection') {
                        continue;
                    }
                    
                    $property->setAccessible(true);
                    $propValue = $property->getValue($value);
                    
                    // Skip PDO and database connections
                    if ($propValue instanceof \PDO || 
                        (is_object($propValue) && strpos(get_class($propValue), 'Database') !== false)) {
                        continue;
                    }
                    
                    $array[$propName] = self::sanitizeForCache($propValue);
                }
                
                return $array;
            } catch (\Exception $e) {
                // If reflection fails, return null to skip this object
                return null;
            }
        }

        // For resources and other types, return null
        return null;
    }

    public static function set(string $key, $value, ?int $seconds = null): bool
    {
        self::pruneExpired();

        if (self::driver() !== 'file') {
            return true; // no-op driver succeeds silently
        }

        $ttlConfig = (int) Config::get('CACHE_DEFAULT_TTL', 600);
        $ttl = $seconds === null ? $ttlConfig : $seconds;
        
        // Sanitize value before serialization
        $sanitizedValue = self::sanitizeForCache($value);
        
        $payload = [
            'key' => $key, // Store key for prefix-based deletion
            'ttl' => $ttl === 0 ? 0 : (time() + $ttl),
            'value' => $sanitizedValue,
        ];

        try {
            return (bool) @file_put_contents(self::filename($key), serialize($payload), LOCK_EX);
        } catch (\Exception $e) {
            // If serialization still fails, log and return false
            error_log("Cache serialization failed for key '{$key}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remember pattern: return cache value or store the callback result.
     */
    public static function remember(string $key, int $seconds, callable $callback)
    {
        $cached = self::get($key, null);
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        self::set($key, $value, $seconds);
        return $value;
    }
}

