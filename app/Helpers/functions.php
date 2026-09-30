<?php

use app\Core\Route;
use app\Core\Router;

if (! function_exists('dd')) {
    /**
     * Dump Down
     *
     * @param mixed $values
     * @return void
     */
    function dd(mixed ...$values): void
    {
        var_dump($values);
        die();
    }
}

if (! function_exists('view')) {
    /**
     * Load view
     *
     * @param string $viewName
     * @param array $data
     * @return \app\Core\ViewResponse
     */
    function view(string $viewName, array $data= []): \app\Core\ViewResponse
    {
        return new \app\Core\ViewResponse($viewName, $data);
    }
}

if (! function_exists('route')) {
    /**
     * @param string $routeName
     * @param mixed ...$params
     * @return Route
     */
    function route(string $routeName, ...$params): Route
    {
        if (count($params) === 1 && is_array($params[0])) {
            $params = $params[0];
        }

        $route = Router::getInstance()->getRouteByName($routeName, $params);

        if (!$route)
            throw new Exception('Route Not Found.');

        return $route;
    }
}

if (! function_exists('assets')) {
    /**
     * @param string $path
     * @return string
     */
    function assets(string $path)
    {
        $appUrl = rtrim(env('APP_URL', ''), '/');
        $path   = ltrim($path, '/');
        return $appUrl . "/assets/$path";
    }
}

if (! function_exists('request')) {
    /**
     * @return app\Core\Request
     */
    function request(): app\Core\Request
    {
        return new app\Core\Request();
    }
}

if (! function_exists('response')) {
    /**
     * @return app\Core\Response
     */
    function response(): app\Core\Response
    {
        return new app\Core\Response();
    }
}

if (! function_exists('auth')) {
    /**
     * @return app\Core\Auth
     */
    function auth(): app\Core\Auth
    {
        return app\Core\Auth::getInstance();
    }
}

if (! function_exists('env')) {
    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    function env(string $name, mixed $default = null): mixed
    {
        return $_ENV[$name] ?? $default;
    }
}