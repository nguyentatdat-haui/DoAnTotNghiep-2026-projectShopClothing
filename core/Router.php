<?php

use App\Repositories\LocationCityRepository;
use App\Repositories\LocationDistrictRepository;

class Router
{
    private $routes = [];
    private $middlewares = [];
    private $currentGroup = [];
    private $locationCityRepository;
    private $locationDistrictRepository;

    public function __construct()
    {
        $this->locationCityRepository = new LocationCityRepository();
        $this->locationDistrictRepository = new LocationDistrictRepository();
    }

    public function get($path, $handler, $middleware = [])
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post($path, $handler, $middleware = [])
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put($path, $handler, $middleware = [])
    {
        return $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete($path, $handler, $middleware = [])
    {
        return $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function group($prefix, $callback, $middleware = [])
    {
        $previousGroup = $this->currentGroup;
        $this->currentGroup = [
            'prefix' => $prefix,
            'middleware' => array_merge($this->currentGroup['middleware'] ?? [], $middleware)
        ];

        $callback($this);

        $this->currentGroup = $previousGroup;
    }

    private function addRoute($method, $path, $handler, $middleware = [])
    {
        $fullPath = $this->buildPath($path);
        $fullMiddleware = array_merge($this->currentGroup['middleware'] ?? [], $middleware);

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middleware' => $fullMiddleware,
            'where' => []
        ];

        return new RouteDefinition($this, array_key_last($this->routes));
    }

    public function setRouteConstraints($index, array $constraints)
    {
        if (!isset($this->routes[$index])) {
            return;
        }

        $this->routes[$index]['where'] = array_merge(
            $this->routes[$index]['where'] ?? [],
            $constraints
        );
    }

    private function buildPath($path)
    {
        $prefix = $this->currentGroup['prefix'] ?? '';
        return $prefix . $path;
    }

    public function dispatch($uri, $method = 'GET')
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $method = strtoupper($method);
        
        // Normalize URI: remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['path'], $uri, $route['where'])) {
                $params = $this->extractParams($route['path'], $uri, $route['where']);
                
                // Validate location parameters if they exist
                if (isset($params['city']) || isset($params['district'])) {
                    if (env('WEB_TYPE') != 1) {
                        continue;
                    }
                    if (!$this->validateLocationParams($params)) {
                        // 404 Not Found if location doesn't exist
                        http_response_code(404);
                        echo "404 - Location Not Found";
                        return false;
                    }
                }
                
                // Execute middlewares
                foreach ($route['middleware'] as $middleware) {
                    if (!$this->executeMiddleware($middleware)) {
                        return false;
                    }
                }

                return $this->executeHandler($route['handler'], $params);
            }
        }

        // 404 Not Found
        http_response_code(response_code: 404);
        echo "404 - Page Not Found";
        return false;
    }

    private function matchRoute($routePath, $uri, $constraints = [])
    {
        $paramNames = [];
        $pattern = $this->buildRouteRegex($routePath, $paramNames, $constraints);

        return preg_match($pattern, $uri);
    }

    private function extractParams($routePath, $uri, $constraints = [])
    {
        $paramNames = [];
        $pattern = $this->buildRouteRegex($routePath, $paramNames, $constraints);

        preg_match($pattern, $uri, $matches);
        array_shift($matches); // Remove full match

        $params = [];
        foreach ($paramNames as $index => $name) {
            $params[$name] = $matches[$index] ?? null;
        }

        return $params;
    }

    /**
     * Build a regex pattern for a route path and capture parameter names.
     * Supports `{param}` (any non-slash) and `{param:regex}` similar to Laravel.
     * Also accepts constraints injected via where() for cleaner route definitions.
     */
    private function buildRouteRegex($routePath, &$paramNames = [], $constraints = [])
    {
        // Normalize route path: remove trailing slash except for root
        $normalizedPath = $routePath;
        if ($normalizedPath !== '/' && substr($normalizedPath, -1) === '/') {
            $normalizedPath = rtrim($normalizedPath, '/');
        }

        $paramNames = [];
        $pattern = preg_replace_callback(
            '/\{([^}:]+)(?::([^}]+))?\}/',
            function ($matches) use (&$paramNames, $constraints) {
                $paramNames[] = $matches[1];
                // Inline regex takes priority, then where() constraints, then default.
                $regexPart = $matches[2] ?? ($constraints[$matches[1]] ?? '[^/]+');
                return '(' . $regexPart . ')';
            },
            $normalizedPath
        );

        return '#^' . $pattern . '$#';
    }

    private function executeMiddleware($middleware)
    {
        if (is_string($middleware)) {
            $middlewareClass = $middleware;
            if (class_exists($middlewareClass)) {
                $middlewareInstance = new $middlewareClass();
                return $middlewareInstance->handle();
            }
        } elseif (is_callable($middleware)) {
            return call_user_func($middleware);
        } elseif (is_object($middleware) && method_exists($middleware, 'handle')) {
            // Direct middleware instance passed in routes (e.g., MiddlewareHelper::rateLimit())
            return $middleware->handle();
        }

        return true;
    }

    private function executeHandler($handler, $params = [])
    {
        if (is_string($handler)) {
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                $controllerClass = "App\\Controllers\\{$controller}";
                
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $method)) {
                        return call_user_func_array([$controllerInstance, $method], $params);
                    }
                }
            } elseif (is_callable($handler)) {
                return call_user_func_array($handler, $params);
            }
        } elseif (is_array($handler) && isset($handler[0], $handler[1]) && is_string($handler[0])) {
            // Support [ControllerClass::class, 'method'] style
            $controllerClass = $handler[0];
            $method = $handler[1];
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $params);
                }
            }
        } elseif (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        return false;
    }

    private function validateLocationParams($params)
    {
        $city = $params['city'] ?? null;
        $district = $params['district'] ?? null;
        
        if ($city && $district) {
            // Validate both city and district
            $location = $this->locationDistrictRepository->findActiveByCityAndDistrict($city, $district);
            return $location !== null;
        } elseif ($city) {
            // Validate only city
            $location = $this->locationCityRepository->findActiveByName($city);
            return $location !== null;
        }
        
        return true; // No location params to validate
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}

class RouteDefinition
{
    private $router;
    private $routeIndex;

    public function __construct(Router $router, int $routeIndex)
    {
        $this->router = $router;
        $this->routeIndex = $routeIndex;
    }

    /**
     * Add regex constraints for route parameters, similar to Laravel's where().
     * Usage:
     * $router->get('/user/{id}', 'Controller@method')->where(['id' => '\d+']);
     */
    public function where($param, $regex = null): self
    {
        $constraints = is_array($param) ? $param : [$param => $regex];
        $this->router->setRouteConstraints($this->routeIndex, $constraints);
        return $this;
    }
}
