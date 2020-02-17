<?php
namespace Main\Core;

use Main\Controllers\ListController;
use Main\Controllers\InputController;
use Main\Controllers\MainController;
use Main\Controllers\CustomerController;
use Main\Controllers\CarController;
use Main\Controllers\HistoryController;
use Main\Utils\DependencyInjector;

class Router {
    private $di;
    private $routeMap;

    public function __construct(DependencyInjector $di) {
        $this->di = $di;
        $json = file_get_contents(__DIR__ . "/../../config/routes.json");
        $this->routeMap = json_decode($json, true);
    }

    public function route(Request $request): string {
        $path = $request->getPath();

        foreach ($this->routeMap as $route => $info) {
            $map = [];
            $params = isset($info["params"]) ? $info["params"] : null;

            if ($this->match($route, $path, $params, $map)) {
                $controllerName = '\Main\Controllers\\' . $info["controller"] . "Controller";
                $controller = new $controllerName($this->di, $request);
                $methodName = $info["method"];
                return call_user_func_array([$controller, $methodName], $map);
            }
        }

        return "Router Error!";
    }

    private function match($route, $path, $params, &$map) {
        $routeArray = explode("/", $route);
        $pathArray = explode("/", $path);
        $routeSize = count($routeArray);
        $pathSize = count($pathArray);

        if ($routeSize !== $pathSize) {
            return false;
        }

        for ($index = 0; $index < $routeSize; ++$index) {
            $routeName = $routeArray[$index];
            $pathName = $pathArray[$index];

            if ((strlen($routeName) > 0) && $routeName[0] === ":") {
                $key = substr($routeName, 1);
                $value = $pathName;

                if (($params != null) && isset($params[$key]) &&
                    !$this->typeMatch($value, $params[$key])) {
                    return false;
                }

                $map[$key] = urldecode($value);
            }
            else if ($routeName !== $pathName) {
                return false;
            }
        }

        return true;
    }

    private function typeMatch($value, $type) {
        switch ($type) {
            case "number":
                return preg_match('/^[0-9]+$/', $value);
            case "string":
                // TODO other symbols?
                return preg_match('/^[%a-zA-Z0-9,]+$/', $value);
        }

        return true;
    }
}