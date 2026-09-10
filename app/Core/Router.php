<?php

namespace app\Core;

use app\Core\Route;
use Exception;

class Router
{
    private static ?Router $instance = null;
    private array $routes = [];

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): Router
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addRoute(string $name, Route $route): Route
    {
        if (array_key_exists($name, $this->routes)) {
            throw new Exception("The route {$name} already exists.");
        }

        return $route;
    }

    public function updateRoute(Route &$route, string $name = ''): void
    {
        if ($name) {
            if (array_key_exists($name, $this->routes)) {
                throw new Exception("The route {$name} already exists.");
            }

            unset($this->routes[$route->name]);
            $route->name = $name;
        }

        $this->routes[$route->name] = $route;
    }

    public function getRouteByPathAndMethod(string $path, string $method): Route|null
    {
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if (($route->path == $path) && 
                (strtoupper($route->httpMethod) === strtoupper($method))
            ) {
                return $route;
            }
        }

        return null;
    }

    public function getRouteByPath(string $path): Route|null
    {
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if ($route->path == $path) {
                return $route;
            }
        }

        return null;
    }

    public function getRouteByName(string $routeName): Route|null
    {
        return $this->routes[$routeName] ?? null;
    }

    public function process(): void
    {
        $url        = parse_url($_SERVER['REQUEST_URI']);
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $route = $this->getRouteByPathAndMethod($url['path'] ?? '', $httpMethod);

        if (!$route) {
            throw new Exception('Route Not Found.');
        }

        if (!$route->isMethod($httpMethod)) {
            throw new Exception("$httpMethod method not supported.");
        }

        $return = $route->execController();

        if ($return instanceof \app\Core\Contracts\Returns) {
            $return->execute();
            return;
        }

        if (is_array($return) || is_object($return)) {
            response()->json($return);
        }
    }
}