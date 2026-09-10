<?php

namespace app\Core;

use app\Core\Controller;
use app\Core\Router;

class Route
{
    public string $name = 'get.index';
    public string $httpMethod = 'GET';
    public string $path = '/';
    public string $controller;
    public Controller $intanceController;
    public string $method = 'index';

    private function generateDefaultName(): string
    {
        return str_replace('/', '.', $this->controller) .
                strtolower($this->httpMethod) .
                $this->method;
    }

    /**
     * @param string $path
     * @param array|string $action
     * @return self
     */
    public function _get(string $path, array|string $action): self
    {
        return $this->register('GET', $path, $action);
    }

    /**
     * @param string $path
     * @param array|string $action
     * @return self
     */
    public function _post(string $path, array|string $action): self
    {
        return $this->register('POST', $path, $action);
    }

    /**
     * @param string $path
     * @param array|string $action
     * @return self
     */
    public function _put(string $path, array|string $action): self
    {
        return $this->register('PUT', $path, $action);
    }

    /**
     * @param string $path
     * @param array|string $action
     * @return self
     */
    public function _delete(string $path, array|string $action): self
    {
        return $this->register('DELETE', $path, $action);
    }

    public function isMethod(string $method): bool
    {
        return strtoupper($this->httpMethod) === strtoupper($method);
    }

    public function getHttpMethod(): string 
    {
        return $this->httpMethod;
    }

    private function register(string $httpMethod, string $path, array|string $action): self
    {
        $this->setPath($path);
        $this->setAction($action);

        $this->name = $this->generateDefaultName();
        $this->httpMethod = strtoupper($httpMethod);
        Router::getInstance()->addRoute($this->name, $this);

        return $this;
    }

    public static function __callStatic(string $method, array $arguments)
    {
        $instance = new self();

        $internalMethod = '_' . $method;

        if (method_exists($instance, $internalMethod)) {
            return call_user_func_array([$instance, $internalMethod], $arguments);
        }

        $className = get_class($instance);
        throw new \Exception("Method {$method} does not exist in class {$className}.");
    }

    public function name(string $name): self
    {
        Router::getInstance()->updateRoute($this, $name);
        return $this;
    }

    public function execController()
    {
        return $this->intanceController->{$this->method}();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFullPath(): string
    {
        $appUrl = rtrim(env('APP_URL', ''), '/');
        return $appUrl . $this->path;
    }

    private function setPath(string $path): void
    {
        $this->path = '/' . trim($path, '/');
    }

    private function setAction(array|string $action)
    {
        [$controller, $method] = $this->parseAction($action);

        if (!class_exists($controller))
            throw new \Exception("Non-existent controller {$controller}.");
        
        $instance = new $controller();

        if (!method_exists($instance, $method))
            throw new \Exception("Method {$method} does not exist in controller {$controller}.");

        $this->controller        = $controller;
        $this->method            = $method;
        $this->intanceController = $instance;
    }

    private function parseAction(array|string $action): array
    {
        $defaultMethod = 'index';

        if (is_array($action)) {
            $controller = $action[0] ?? '';
            $method     = $action[1] ?? $defaultMethod;
        } else if (is_string($action) && str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action);
        } else {
            $controller = '';
            $method     = $defaultMethod;
        }

        if (!str_starts_with($controller, 'app\Controllers\\') && !str_starts_with($controller, '\\')) {
            $controller = 'app\Controllers\\' . $controller;
        }
        
        return [$controller, $method];
    }
}