<?php

namespace App\Helpers;

class Request
{
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function string(string $key, int $maxLen = 255, string $src = 'REQUEST'): string
    {
        $arr = $this->source($src);
        $val = isset($arr[$key]) ? trim((string)$arr[$key]) : '';
        if (mb_strlen($val) > $maxLen) {
            $val = mb_substr($val, 0, $maxLen);
        }
        return $val;
    }

    public function int(string $key, int $default = 0, string $src = 'REQUEST'): int
    {
        $arr = $this->source($src);
        return isset($arr[$key]) ? (int)$arr[$key] : $default;
    }

    public function array(string $key, string $src = 'REQUEST'): array
    {
        $arr = $this->source($src);
        return isset($arr[$key]) && is_array($arr[$key]) ? $arr[$key] : [];
    }

    public function all(string $src = 'REQUEST'): array
    {
        return $this->source($src);
    }

    private function source(string $src): array
    {
        switch (strtoupper($src)) {
            case 'GET':
                return $_GET;
            case 'POST':
                return $_POST;
            default:
                return $_REQUEST;
        }
    }
}


