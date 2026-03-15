<?php

/**
 * Common Helper Functions
 * 
 * This class contains commonly used helper functions
 * that are available throughout the application.
 */
class CommonHelper
{
    /**
     * Escape output for HTML contexts to mitigate XSS
     */
    public static function e($value)
    {
        return self::escapeHtml($value);
    }

    /**
     * Clear, descriptive alias for escaping HTML contexts
     */
    public static function escapeHtml($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    /**
     * Get configuration value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function config($key, $default = null)
    {
        return Config::get($key, $default);
    }

    /**
     * Render a view
     * 
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function view($view, $data = [])
    {
        return View::render($view, $data);
    }

    /**
     * Redirect to URL
     * 
     * @param string $url
     * @param int $status
     * @return void
     */
    public static function redirect($url, $status = 302)
    {
        return View::redirect($url, $status);
    }

    /**
     * Redirect back to previous page
     * 
     * @return void
     */
    public static function back()
    {
        return View::back();
    }

    /**
     * Generate URL
     * 
     * @param string $path
     * @return string
     */
    public static function url($path = '')
    {
        // Lấy base URL chuẩn (đã xử lý WEB_TYPE, BASE_NAME, v.v.)
        $baseUrl = self::baseUrl();

        // Nếu không truyền path thì trả về nguyên base URL
        if ($path === '' || $path === null) {
            return $baseUrl;
        }

        // Có path thì nối thêm vào sau base URL
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Get base URL
     * 
     * @return string
     */
    public static function baseUrl()
    {
        if (env('WEB_TYPE') == '2') {
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $protocol . $host;

            // Support BASE_NAME from env/config when app is in a subfolder
            $baseName = env('BASE_NAME');
            if (!$baseName) {
                $baseName = self::config('BASE_NAME', null);
            }
            if ($baseName) {
                $baseUrl .= '/' . trim($baseName, '/');
            }

            return $baseUrl;
        } else {
            return self::config('BASE_URL');
        }
    }

    /**
     * Generate asset URL
     * 
     * @param string $path
     * @return string
     */
    public static function asset($path)
    {
        $baseUrl = self::baseUrl();
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Generate route URL (simple implementation)
     * 
     * @param string $name
     * @param array $params
     * @return string
     */
    public static function route($name, $params = [])
    {
        // Simple route helper - can be enhanced
        return self::url($name);
    }

    /**
     * Dump and die
     * 
     * @param mixed $data
     * @return void
     */
    public static function dd($data)
    {
        echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ccc; margin: 10px 0;">';
        var_dump($data);
        echo '</pre>';
        die();
    }

    /**
     * Dump data
     * 
     * @param mixed $data
     * @return void
     */
    public static function dump($data)
    {
        echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ccc; margin: 10px 0;">';
        var_dump($data);
        echo '</pre>';
    }

    /**
     * Debug log to file
     * 
     * @param mixed $data
     * @param string $label
     * @return void
     */
    public static function debugLog($data, $label = 'DEBUG')
    {
        $logFile = __DIR__ . '/../storage/logs/debug.log';
        $timestamp = date('Y-m-d H:i:s');
        $message = "[{$timestamp}] {$label}: " . print_r($data, true) . "\n";
        file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
    }

    /**
     * Pretty print for debugging
     * 
     * @param mixed $data
     * @param string $label
     * @return void
     */
    public static function debug($data, $label = 'DEBUG')
    {
        if (self::config('APP_DEBUG', true)) {
            echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 10px 0; border-radius: 4px;">';
            echo '<strong style="color: #856404;">' . htmlspecialchars($label) . ':</strong><br>';
            echo '<pre style="margin: 5px 0; color: #856404;">';
            print_r($data);
            echo '</pre>';
            echo '</div>';
        }
    }

    /**
     * Generate CSRF token
     * 
     * @return string
     */
    public static function csrfToken()
    {
        return View::csrf();
    }

    /**
     * Generate CSRF field HTML
     * 
     * @return string
     */
    public static function csrfField()
    {
        return View::csrfField();
    }

    /**
     * Generate method field HTML
     * 
     * @param string $method
     * @return string
     */
    public static function methodField($method)
    {
        return View::method($method);
    }

    /**
     * Get old input value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function old($key, $default = null)
    {
        return View::old($key, $default);
    }

    /**
     * Get environment variable
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function env($key, $default = null)
    {
        return Config::get($key, $default);
    }

    /**
     * Return JSON response
     * 
     * @param mixed $data
     * @param int $status
     * @return string
     */
    public static function json($data, $status = 200)
    {
        return View::json($data, $status);
    }

    /**
     * Get city information from URL
     * 
     * @return array|null
     */
    public static function getCityInfo()
    {
        $locationinfo = \App\Helpers\CityHelper::getCityNameFromUrl();
        $cityInfo = \App\Helpers\CityHelper::getCityInfoByName($locationinfo);


        // If city not found, redirect to 404
        if ($cityInfo === null) {
            http_response_code(404);
            include __DIR__ . '/../views/maintenance/index.php';
            exit;
        }

        return $cityInfo;
    }

    /**
     * Get current full URL (with query string)
     * 
     * @return string
     */
    public static function currentUrl()
    {
        $baseUrl = self::baseUrl();
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove base path if app is in subfolder
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $baseDir = dirname($scriptDir);

        foreach ([$scriptDir, $baseDir] as $basePath) {
            if ($basePath && $basePath !== '/' && $basePath !== '\\') {
                if (strpos($requestUri, $basePath) === 0) {
                    $requestUri = substr($requestUri, strlen($basePath));
                }
            }
        }

        // Ensure requestUri starts with /
        if ($requestUri === '' || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }

        return $baseUrl . $requestUri;
    }

    public static function execute_curl_request($url, $data = null, $headers = [], $method = 'GET')
    {
        $ch = curl_init();

        // Basic cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Set method and data
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                // If data is array, determine content type
                if (is_array($data)) {
                    // Check if we need JSON or form data
                    $is_json = false;
                    foreach ($headers as $header) {
                        if (stripos($header, 'application/json') !== false) {
                            $is_json = true;
                            break;
                        }
                    }

                    if ($is_json) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    } else {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    }
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
            }
        }

        // Set headers
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // Execute request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_errno($ch) ? curl_error($ch) : null;

        curl_close($ch);

        return [
            'response' => $response,
            'http_code' => $http_code,
            'error' => $error
        ];
    }
}

// Create global helper functions for backward compatibility
if (!function_exists('config')) {
    function config($key, $default = null)
    {
        return CommonHelper::config($key, $default);
    }
}

if (!function_exists('view')) {
    function view($view, $data = [])
    {
        return CommonHelper::view($view, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $status = 302)
    {
        return CommonHelper::redirect($url, $status);
    }
}

if (!function_exists('back')) {
    function back()
    {
        return CommonHelper::back();
    }
}

if (!function_exists('url')) {
    function url($path = '')
    {
        return CommonHelper::url($path);
    }
}

if (!function_exists('base_url')) {
    function base_url()
    {
        return CommonHelper::baseUrl();
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        return CommonHelper::asset($path);
    }
}

if (!function_exists('route')) {
    function route($name, $params = [])
    {
        return CommonHelper::route($name, $params);
    }
}

if (!function_exists('dd')) {
    function dd($data)
    {
        return CommonHelper::dd($data);
    }
}

if (!function_exists('dump')) {
    function dump($data)
    {
        return CommonHelper::dump($data);
    }
}

if (!function_exists('debugLog')) {
    function debugLog($data, $label = 'DEBUG')
    {
        return CommonHelper::debugLog($data, $label);
    }
}

if (!function_exists('debug')) {
    function debug($data, $label = 'DEBUG')
    {
        return CommonHelper::debug($data, $label);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        return CommonHelper::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return CommonHelper::csrfField();
    }
}

if (!function_exists('method_field')) {
    function method_field($method)
    {
        return CommonHelper::methodField($method);
    }
}

if (!function_exists('old')) {
    function old($key, $default = null)
    {
        return CommonHelper::old($key, $default);
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        return CommonHelper::env($key, $default);
    }
}

if (!function_exists('json')) {
    function json($data, $status = 200)
    {
        return CommonHelper::json($data, $status);
    }
}

if (!function_exists('getCityInfo')) {

    if (env('WEB_TYPE') == '2') {
        function getCityInfo()
        {
            return CommonHelper::getCityInfo();
        }
    } else {
        function getCityInfo()
        {
            // xử lý nếu có rewr url city district sau
            return null;
        }
    }
}

if (!function_exists('current_url')) {
    function current_url()
    {
        return CommonHelper::currentUrl();
    }
}

// Global escape helper
if (!function_exists('e')) {
    function e($value)
    {
        return CommonHelper::e($value);
    }
}

// More descriptive global escape helper
if (!function_exists('escape_html')) {
    function escape_html($value)
    {
        return CommonHelper::escapeHtml($value);
    }
}

// Global cURL helper function
if (!function_exists('execute_curl_request')) {
    function execute_curl_request($url, $data = null, $headers = [], $method = 'GET')
    {
        return CommonHelper::execute_curl_request($url, $data, $headers, $method);
    }
}
