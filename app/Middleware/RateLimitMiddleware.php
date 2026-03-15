<?php

namespace App\Middleware;

class RateLimitMiddleware
{
    private $maxRequests;
    private $timeWindow;
    private $storageFile;

    public function __construct($maxRequests = 100, $timeWindow = 3600)
    {
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
        // Legacy file storage path (no longer used after switching to session-based storage)
        $this->storageFile = __DIR__ . '/../../storage/rate_limit.json';
    }

    public function handle()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $now = time();
        
        $data = $this->loadData();
        $this->cleanOldData($data, $now);
        
        if (!isset($data[$ip])) {
            $data[$ip] = [];
        }
        
        $windowStart = $now - $this->timeWindow;
        $recentRequests = array_filter($data[$ip], fn($time) => $time > $windowStart);
        
        if (count($recentRequests) >= $this->maxRequests) {
            $this->sendError();
            return false;
        }
        
        $data[$ip][] = $now;
        $this->saveData($data);
        
        return true;
    }
    
    private function loadData()
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (!isset($_SESSION['rate_limit']) || !is_array($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        return $_SESSION['rate_limit'];
    }
    
    private function saveData($data)
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['rate_limit'] = $data;
    }
    
    private function cleanOldData(&$data, $now)
    {
        $cutoff = $now - $this->timeWindow;
        foreach ($data as $ip => &$requests) {
            $requests = array_filter($requests, fn($time) => $time > $cutoff);
            if (empty($requests)) unset($data[$ip]);
        }
    }
    
    private function sendError()
    {
        http_response_code(429);
        echo json_encode(['error' => 'Too Many Requests']);
        exit;
    }
}
