<?php

namespace app\Core;

use app\Core\Router;
use Closure;

class Route
{
    public string $name = '';
    public string $httpMethod = 'GET';
    public string $path = '/';
    public string $controller = '';
    public mixed $intanceController = null;
    public string $method = 'index';
    private ?Closure $middlewareCallback = null;
    public array $params = [];

    private function generateDefaultName(): string
    {
        return str_replace(['\\', '/'], '.', $this->controller) . 
               '.' . strtolower($this->httpMethod) . 
               '.' . $this->method . '_' . uniqid();
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

    public function _middleware($callback = null): self
    {
        if ($callback !== null) {
            $this->middlewareCallback = Closure::fromCallable($callback);
        }

        return $this;
    }

    public function handleMiddleware(): mixed
    {
        if ($this->middlewareCallback) {
            return ($this->middlewareCallback)();
        }

        return null;
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

        if (empty($this->name)) {
            $this->name = $this->generateDefaultName();
        }

        Router::getInstance()->addRoute($this->name, $this);

        return $this;
    }

    public static function __callStatic(string $method, array $arguments)
    {
        $instance = new self();
        return $instance->__call($method, $arguments);
    }

    public function __call(string $method, array $arguments)
    {
        $internalMethod = '_' . $method;

        if (method_exists($this, $internalMethod)) {
            return call_user_func_array([$this, $internalMethod], $arguments);
        }

        $className = static::class;
        throw new \Exception("Method {$method} does not exist in class {$className}.");
    }

    public function name(string $name): self
    {
        Router::getInstance()->updateRoute($this, $name);
        return $this;
    }

    public function execController()
    {
        if (!$this->intanceController) {
            throw new \Exception("Controller instance not initialized for route {$this->path}");
        }

        return call_user_func_array([$this->intanceController, $this->method], $this->params);
    }

    private function getProcessedPath(): string
    {
        $path = $this->path;

        if (empty($this->params)) return $path;

        $isAssoc = array_keys($this->params) !== range(0, count($this->params) - 1);

        if ($isAssoc) {
            foreach ($this->params as $key => $value) {
                $path = preg_replace('/\{' . $key . '(\?[^\}]*)?\}/', $value, $path);
            }
        } else {
            $paramIndex = 0;
            $params = $this->params;
            
            $path = preg_replace_callback('/\{([a-zA-Z0-9_]+)(\?[^\}]*)?\}/', function($matches) use ($params, &$paramIndex) {
                if (isset($params[$paramIndex])) {
                    $val = $params[$paramIndex];
                    $paramIndex++;
                    return $val;
                }
                return $matches[0];
            }, $path);
        }

        return $path;
    }

    public function getPath(): string
    {
        return $this->getProcessedPath();
    }

    public function getFullPath(array $queryParams = []): string
    {
        
        $appUrl = rtrim(env('APP_URL', ''), '/');
        $path   = $this->getProcessedPath();

        if (!empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }

        return $appUrl . $path;
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

            if ($controller instanceof \Closure) {
                $reflector = new \ReflectionFunction($controller);
                $controller = $reflector->getClosureThis() ?? $controller;
            }

            if (is_object($controller)) {
                $controller = get_class($controller);
            }
        } else if (is_string($action) && str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action);
        } else {
            $controller = is_string($action) ? $action : '';
            $method     = $defaultMethod;
        }

        if (!str_starts_with($controller, 'app\\Controllers\\') && !str_starts_with($controller, '\\')) {
            $controller = 'app\\Controllers\\' . $controller;
        }
        
        return [ltrim($controller, '\\'), $method];
    }
}