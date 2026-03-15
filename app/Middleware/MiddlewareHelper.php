<?php

namespace App\Middleware;

class MiddlewareHelper
{
    public static function rateLimit($maxRequests = 1, $timeWindow = 1)
    {
        return new RateLimitMiddleware($maxRequests, $timeWindow);
    }
    
    public static function ipBlock()
    {
        return new IpBlockMiddleware();
    }

    public static function csrf()
    {
        return new CsrfMiddleware();
    }
}
