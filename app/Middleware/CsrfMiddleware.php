<?php

namespace App\Middleware;

class CsrfMiddleware
{
    public function handle()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'GET' || $method === 'HEAD' || $method === 'OPTIONS') {
            return true;
        }

        $token = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        if (!isset($_SESSION['_token']) || !is_string($token) || !hash_equals($_SESSION['_token'], $token)) {
            http_response_code(419);
            echo 'CSRF token mismatch';
            return false;
        }

        return true;
    }
}


