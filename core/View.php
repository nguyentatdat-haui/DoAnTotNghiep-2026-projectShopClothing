<?php

class View
{
    private static $data = [];
    private static $blocks = [];
    private static $currentBlock = null;

    public static function render($view, $data = [])
    {
        self::$data = array_merge(self::$data, $data);
        
        $viewPath = self::getViewPath($view);
        
        if (!file_exists($viewPath)) {
            throw new Exception("View not found: {$view}");
        }

        // Start output buffering for view content
        ob_start();
        
        // Extract data to variables
        extract(self::$data);
        
        // Include the view file
        include $viewPath;
        
        // Get the content
        $content = ob_get_clean();
        
        // Process blocks
        $content = self::processBlocks($content);
        
        // Set content for layout
        self::$data['content'] = $content;
        
        // Layout: allow override via data (e.g. admin uses layouts/admin)
        $layout = self::$data['_layout'] ?? 'layouts/app';
        unset(self::$data['_layout']);
        
        return self::extend($layout);
    }

    public static function make($view, $data = [])
    {
        return self::render($view, $data);
    }

    public static function share($key, $value = null)
    {
        if (is_array($key)) {
            self::$data = array_merge(self::$data, $key);
        } else {
            self::$data[$key] = $value;
        }
    }

    public static function with($key, $value = null)
    {
        return self::share($key, $value);
    }

    public static function startBlock($name)
    {
        self::$currentBlock = $name;
        ob_start();
    }

    public static function endBlock()
    {
        if (self::$currentBlock) {
            self::$blocks[self::$currentBlock] = ob_get_clean();
            self::$currentBlock = null;
        }
    }

    public static function block($name, $default = '')
    {
        return self::$blocks[$name] ?? $default;
    }

    public static function extend($layout)
    {
        $layoutPath = self::getViewPath($layout);
        
        if (!file_exists($layoutPath)) {
            throw new Exception("Layout not found: {$layout}");
        }

        // Start output buffering for layout
        ob_start();
        
        // Extract data to variables
        extract(self::$data);
        
        // Include the layout file
        include $layoutPath;
        
        // Get the final content
        $content = ob_get_clean();
        
        return $content;
    }

    public static function include($view, $data = [])
    {
        $viewPath = self::getViewPath($view);
        
        if (!file_exists($viewPath)) {
            throw new Exception("Include view not found: {$view}");
        }

        // Extract data to variables
        extract(array_merge(self::$data, $data));
        
        // Include the view file
        include $viewPath;
    }

    public static function component($component, $data = [])
    {
        return self::include("components.{$component}", $data);
    }

    public static function partial($partial, $data = [])
    {
        return self::include("partials.{$partial}", $data);
    }

    private static function getViewPath($view)
    {
        $view = str_replace('.', '/', $view);
        return __DIR__ . "/../app/views/{$view}.php";
    }

    private static function processBlocks($content)
    {
        // Process @block directives
        $content = preg_replace_callback('/@block\([\'"]([^\'"]+)[\'"]\)/', function($matches) {
            return self::block($matches[1]);
        }, $content);

        // Process @yield directives
        $content = preg_replace_callback('/@yield\([\'"]([^\'"]+)[\'"]\)/', function($matches) {
            return self::block($matches[1]);
        }, $content);

        return $content;
    }

    public static function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data);
    }

    public static function redirect($url, $status = 302)
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    public static function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }

    public static function withInput($data = null)
    {
        if ($data === null) {
            $data = $_POST;
        }
        
        self::share('_old_input', $data);
        return $data;
    }

    public static function old($key, $default = null)
    {
        return self::$data['_old_input'][$key] ?? $default;
    }

    public static function csrf()
    {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }

    public static function csrfField()
    {
        return '<input type="hidden" name="_token" value="' . self::csrf() . '">';
    }

    public static function method($method)
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}
