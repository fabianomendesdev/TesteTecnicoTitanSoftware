<?php

namespace app\Core;

use app\Core\Route;
use Exception;

class Router
{
    private static ?Router $instance = null;
    private ?Route $currentRoute = null;
    private array $routes = [];

    private function __construct() {}
    private function __clone() {}

    /**
     * Retorna a instância única (Singleton) da classe.
     * 
     * @return self
     */
    public static function getInstance(): Router
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Retorna o nome da rota atual
     * 
     * @return ?string
     */
    public function getCurrentRouteName(): ?string
    {
        return $this->currentRoute?->name;
    }

    /**
     * Verifica se o nome da rota passada por parâmetro é o mesmo da rota atual
     * Retorna um valor verdadeiro ou falso
     * 
     * @param string $name
     * @return bool
     */
    public function isRoute(string $name): bool
    {
        return $name === $this->getCurrentRouteName();
    }

    /**
     * Adiciona a rota passada por parâmetro no array $routes
     * 
     * @param string $name
     * @param Route $route
     * @return Route
     */
    public function addRoute(string $name, Route $route): Route
    {
        if (array_key_exists($name, $this->routes)) {
            throw new Exception("The route {$name} already exists.");
        }

        $this->routes[$name] = $route;
        return $route;
    }

    /**
     * Atualiza o nome da rota
     * 
     * @param Route &$route
     * @param string $newName
     * @return void
     */
    public function updateRoute(Route &$route, string $newName = ''): void
    {
        if ($newName && $newName !== $route->name) {
            if (array_key_exists($newName, $this->routes)) {
                throw new Exception("The route {$newName} already exists.");
            }

            unset($this->routes[$route->name]);
            $route->name = $newName;
            $this->routes[$route->name] = $route;
        }
    }

    /**
     * Busca a rota pertencente a url e method http passados por parâmetro
     * 
     * @param string $path
     * @param string $method
     * @return Route|null
     */
    public function getRouteByPathAndMethod(string $path, string $method): Route|null
    {
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            if (strtoupper($route->httpMethod) !== strtoupper($method)) {
                continue;
            }

            $routePattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route->path);
            $routePattern = "#^" . $routePattern . "$#";

            if (preg_match($routePattern, $path, $matches)) {
                array_shift($matches);
                $route->params = $matches;
                return $route;
            }
        }

        return null;
    }

    /**
     * Retorna a rota pelo nome da rota passado por parâmetro
     * 
     * @param string $routeName
     * @param array $params
     * @return Route|null
     */
    public function getRouteByName(string $routeName, array $params = []): Route|null
    {
        $route = $this->routes[$routeName] ?? null;

        if ($route) {
            $routeClone = clone $route;
            if (!empty($params)) {
                $routeClone->params = $params;
            }
            return $routeClone;
        }

        return null;
    }

    /**
     * Processa a url atual e abre o controller referente a rota cadastrada
     * 
     * @return void
     */
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

        $this->currentRoute = $route;

        $middlewareResult = $route->handleMiddleware();

        if ($middlewareResult instanceof \app\Core\Contracts\Returns) {
            $middlewareResult->execute();
            return;
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