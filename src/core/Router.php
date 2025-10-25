<?php

namespace core;

use core\exceptions\AuthException;
use core\exceptions\ForbiddenException;
use core\exceptions\NotFoundException;
use ReflectionException;
use ReflectionMethod;

final class Router
{
    private array $routes = [];
    private static ?Router $instance = null;

    public static function getInstance(): ?Router
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    
    public static function get($route, $handler, $options = []): Router
    {
        return self::getInstance()->addRoute('GET', $route, $handler, $options);
    }

    public static function post($route, $handler, $options = []): Router
    {
        return self::getInstance()->addRoute('POST', $route, $handler, $options);
    }

    /** @noinspection PhpUnused */
    public static function put($route, $handler, $options = []): Router
    {
        return self::getInstance()->addRoute('PUT', $route, $handler, $options);
    }

    /** @noinspection PhpUnused */
    public static function delete($route, $handler, $options = []): Router
    {
        return self::getInstance()->addRoute('DELETE', $route, $handler, $options);
    }

    /** @noinspection PhpUnused */
    public static function patch($route, $handler, $options = []): Router
    {
        return self::getInstance()->addRoute('PATCH', $route, $handler, $options);
    }

    public static function redirect($from, $to, $status = 302): Router
    {
        return self::getInstance()->addRoute('GET', $from, ['redirect' => $to, 'status' => $status]);
    }

    public static function group($prefix, $callback, $options = []): void
    {
        $router = self::getInstance();
        $previousGroupPrefix = $router->currentGroupPrefix;
        $previousGroupOptions = $router->currentGroupOptions;

        $router->currentGroupPrefix = $prefix;
        $router->currentGroupOptions = $options;

        call_user_func($callback, $router);

        $router->currentGroupPrefix = $previousGroupPrefix;
        $router->currentGroupOptions = $previousGroupOptions;
    }

    /** @noinspection PhpUnused */
    public static function resource($route, $controller, $options = []): void
    {
        $router = self::getInstance();
        $name = $options['name'] ?? str_replace('/', '.', trim($route, '/'));

        $router->addRoute('GET', $route, [$controller, 'index'], array_merge($options, ['name' => $name . '.index']));
        $router->addRoute('GET', "$route/create", [$controller, 'create'], array_merge($options, ['name' => $name . '.create']));
        $router->addRoute('POST', $route, [$controller, 'store'], array_merge($options, ['name' => $name . '.store']));
        $router->addRoute('GET', "$route/{id}", [$controller, 'show'], array_merge($options, ['name' => $name . '.show']));
        $router->addRoute('GET', "$route/{id}/edit", [$controller, 'edit'], array_merge($options, ['name' => $name . '.edit']));
        $router->addRoute('PUT', "$route/{id}", [$controller, 'update'], array_merge($options, ['name' => $name . '.update']));
        $router->addRoute('PATCH', "$route/{id}", [$controller, 'update'], array_merge($options, ['name' => $name . '.update']));
        $router->addRoute('DELETE', "$route/{id}", [$controller, 'destroy'], array_merge($options, ['name' => $name . '.destroy']));
    }

    private string $currentGroupPrefix = '';
    private array $currentGroupOptions = [];

    private function addRoute($method, $route, $handler, $options = []): Router
    {
        
        if ($this->currentGroupPrefix) {
            $route = $this->currentGroupPrefix . $route;
        }
        
        $mergedOptions = array_merge($this->currentGroupOptions, $options);

        
        if (is_array($handler) && count($handler) === 2) {
            if (isset($handler['redirect'])) {
                $handler = [
                    'redirect' => @$handler['redirect'],
                ];
            }
            else {
                $handler = [
                    'controller' => @$handler[0],
                    'action' => @$handler[1]
                ];
            }
        } elseif (is_string($handler)) {
            
            if (str_contains($handler, '@')) {
                list($controller, $action) = explode('@', $handler);
                $handler = [
                    'controller' => $controller,
                    'action' => $action
                ];
            }
        }

        
        if (is_array($handler)) {
            $handler = array_merge($handler, $mergedOptions);
        }

        $this->routes[$method][$route] = $handler;
        return $this;
    }

    /**
     * @throws NotFoundException
     * @throws ForbiddenException
     * @throws AuthException
     * @throws ReflectionException
     */
    public function route($uri, $method = 'GET'): void
    {
        $uri = $this->normalizeUri($uri);
        
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        if (isset($this->routes[$method][$uri])) {
            $this->handleRoute($this->routes[$method][$uri]);
            return;
        }

        
        $routeMatch = $this->findRouteMatch($uri, $method);
        if ($routeMatch) {
            $this->handleRoute($routeMatch['config'], $routeMatch['params']);
            return;
        }

        $this->notFound();
    }

    private function normalizeUri($uri): string
    {
        
        $uri = strtok($uri, '?');

        
        if ($uri === '' || $uri === '/') {
            return '/';
        }

        $uri = '/' . trim($uri, '/');

        if ($uri === '') {
            return '/';
        }
        
        return $uri;
    }

    private function findRouteMatch($uri, $method): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route => $config) {
            
            if (!str_contains($route, '{')) {
                continue;
            }

            $pattern = $this->convertRouteToPattern($route);
            if (preg_match($pattern, $uri, $matches)) {
                $params = $this->extractParams($matches);
                return [
                    'config' => $config,
                    'params' => $params
                ];
            }
        }

        return null;
    }

    private function convertRouteToPattern($route): string
    {
        
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)}/', '(?P<$1>[^/]+)', $route);
        
        $pattern = str_replace('/', '\/', $pattern);
        return '#^' . $pattern . '$#';
    }

    private function extractParams($matches): array
    {
        return array_filter($matches, function ($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @throws NotFoundException
     * @throws ForbiddenException
     * @throws ReflectionException
     * @throws AuthException
     */
    private function handleRoute($routeConfig, $params = []): void
    {
        
        if (isset($routeConfig['redirect'])) {
            $status = $routeConfig['status'] ?? 302;
            header("Location: {$routeConfig['redirect']}", true, $status);
            exit;
        }

        
        if (!isset($routeConfig['controller']) || !isset($routeConfig['action'])) {
            error_log("Invalid route configuration: missing controller or action");
            throw new NotFoundException("Route configuration is invalid");
        }

        
        if (isset($routeConfig['protected']) && $routeConfig['protected'] === true) {
            if (!$this->isAuthenticated()) {
                throw new AuthException("Authentication required");
            }
        }

        
        if (isset($routeConfig['permission'])) {
            if (!$this->hasPermission($routeConfig['permission'])) {
                throw new ForbiddenException("Insufficient permissions");
            }
        }

        
        if (isset($routeConfig['middleware'])) {
            $this->handleMiddleware($routeConfig['middleware']);
        }

        $this->dispatch($routeConfig['controller'], $routeConfig['action'], $params);
    }

    private function hasPermission($permission): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userPermissions = $_SESSION['user_permissions'] ?? [];
        return in_array($permission, $userPermissions);
    }
    
    private function handleMiddleware($middleware): void
    {
        
        if (is_string($middleware)) {
            $middleware = [$middleware];
        }

        foreach ($middleware as $mw) {
            $middlewareClass = "app\\Middleware\\" . $mw;
            if (class_exists($middlewareClass)) {
                $mwInstance = new $middlewareClass();
                if (method_exists($mwInstance, 'handle')) {
                    $mwInstance->handle();
                }
            }
        }
    }

    /**
     * @throws NotFoundException
     * @throws ReflectionException
     */
    private function dispatch($controller, $action, $params = []): void
    {
        if (!str_contains($controller, '\\')) {
            $controller = "app\\Controllers\\" . $controller;
        }

        if (!class_exists($controller)) {
            error_log("Controller not found: " . $controller);
            $this->notFound();
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $action)) {
            error_log("Action not found: " . $controller . "::" . $action);
            $this->notFound();
        }

        
        $reflectionMethod = new ReflectionMethod($controllerInstance, $action);
        $methodParams = $reflectionMethod->getParameters();

        $args = [];
        foreach ($methodParams as $param) {
            $paramType = $param->getType();

            
            if ($paramType && !$paramType->isBuiltin()) {
                $className = $paramType->getName();

                
                if (is_subclass_of($className, 'core\Request') || $className === 'core\Request') {
                    $requestInstance = new $className();

                    
                    if (is_subclass_of($className, 'core\FormRequest')) {
                        $requestInstance->validated();
                    }

                    $args[] = $requestInstance;
                    continue;
                }
            }
            
            if (isset($params[$param->getName()])) {
                $args[] = $params[$param->getName()];
            }
            
            elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
            
            else {
                $args[] = null;
            }
        }

        
        call_user_func_array([$controllerInstance, $action], $args);
    }    
    
    private function isAuthenticated(): bool
    {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    /**
     * @throws NotFoundException
     */
    private function notFound()
    {
        throw new NotFoundException();
    }

    /** @noinspection PhpUnused */
    public static function routeUrl($name, $params = [])
    {
        $router = self::getInstance();
        foreach ($router->routes as $routes) {
            foreach ($routes as $route => $config) {
                if (isset($config['name']) && $config['name'] === $name) {
                    return $router->replaceRouteParams($route, $params);
                }
            }
        }
        return null;
    }

    private function replaceRouteParams($route, $params)
    {
        foreach ($params as $key => $value) {
            $route = str_replace("{{$key}}", $value, $route);
        }
        return $route;
    }

    /**
     * @throws NotFoundException
     * @throws ForbiddenException
     * @throws ReflectionException
     * @throws AuthException
     */
    public static function handle($uri = null, $method = null): void
    {
        if ($uri === null) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        if ($method === null) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        self::getInstance()->route($uri, $method);
    }
}