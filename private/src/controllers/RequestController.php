<?php

namespace ChaosWD\Controller;

use stdClass;

class RequestController
{
    protected $routes = [
        'GET' => [],
        'POST' => []
    ];

    public function __construct($routeDIR = CONFIG_PATH . "\\routes.php")
    {
        $this->load($routeDIR);
    }

    public function load($routeDIR)
    {
        $router = $this;
        if (file_exists($routeDIR)) require_once($routeDIR);
        return $router;
    }

    public function get($uri, $namespace, $controllerMethod, $params = [])
    {
        $this->routes['GET'][$uri] = [
            'namespace' => $namespace,
            'controllerMethod' => $controllerMethod,
            'params' => $params
        ];
    }

    public function post($uri, $namespace, $controllerMethod, $params = [])
    {
        $this->routes['POST'][$uri] = [
            'namespace' => $namespace,
            'controllerMethod' => $controllerMethod,
            'params' => $params
        ];
    }

    public function request($req_method, $uri)
    {
        if (array_key_exists($uri, $this->routes[strtoupper($req_method)])) {
            $route = $this->routes[strtoupper($req_method)][$uri];
            $namespace = $route['namespace'];
            $controllerMethod = $route['controllerMethod'];
            [$controller, $method] = explode('@', $controllerMethod);
            $params = $route['params'];
            return $this->dispatch($namespace, $controller, $method, $params);
        }
        return;
    }

    private function dispatch($namespace, $controller, $method, $params)
    {
        $errorLog = new LogController("errorLog");

        $controllerNamespace = "{$namespace}\\{$controller}";

        if (!class_exists($controllerNamespace)) {
            $obj = new stdClass();
            $obj->reason = "ErrorHandling";
            $obj->message = "Class {$controllerNamespace} does not exist.";
            $obj->data = ["namespace" => $namespace, "controller" => $controller, "method" => $method, "params" => $params];
            $errorLog->add($obj);
            exit(header("Location: /"));
        }

        $controllerInstance = new $controllerNamespace;

        if (!method_exists($controllerInstance, $method)) {
            $obj = new stdClass();
            $obj->reason = "ErrorHandling";
            $obj->message = "{$controllerNamespace} does not respond to the {$method} method.";
            $obj->data = ["namespace" => $namespace, "controller" => $controller, "method" => $method, "params" => $params];
            $errorLog->add($obj);
            exit(header("Location: /"));
        }

        return $controllerInstance->$method($params ?? null);
    }
}
