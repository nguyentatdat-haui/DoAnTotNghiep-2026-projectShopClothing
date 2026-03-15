<?php

namespace App\Middleware;

class IpBlockMiddleware
{
    private $blockedIps;
    private $configFile;

    public function __construct()
    {
        $this->configFile = __DIR__ . '/../../config/blocked_ips.json';
        $this->loadBlockedIps();
    }

    public function handle()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        if (in_array($ip, $this->blockedIps)) {
            $this->sendError();
            return false;
        }
        
        return true;
    }
    
    private function loadBlockedIps()
    {
        if (!file_exists($this->configFile)) {
            $this->createDefaultConfig();
        }
        
        $config = json_decode(file_get_contents($this->configFile), true);
        $this->blockedIps = $config['blocked_ips'] ?? [];
    }
    
    private function createDefaultConfig()
    {
        $config = ['blocked_ips' => []];
        if (!is_dir(dirname($this->configFile))) {
            mkdir(dirname($this->configFile), 0755, true);
        }
        file_put_contents($this->configFile, json_encode($config));
    }
    
    private function sendError()
    {
        http_response_code(403);
        echo json_encode(['error' => 'Access Denied']);
        exit;
    }
    
    // Static methods for managing blocked IPs
    public static function addBlockedIp($ip)
    {
        $middleware = new self();
        if (!in_array($ip, $middleware->blockedIps)) {
            $middleware->blockedIps[] = $ip;
            file_put_contents($middleware->configFile, json_encode(['blocked_ips' => $middleware->blockedIps]));
        }
    }
    
    public static function removeBlockedIp($ip)
    {
        $middleware = new self();
        $key = array_search($ip, $middleware->blockedIps);
        if ($key !== false) {
            unset($middleware->blockedIps[$key]);
            file_put_contents($middleware->configFile, json_encode(['blocked_ips' => array_values($middleware->blockedIps)]));
        }
    }
}
