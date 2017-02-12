<?php

/**
 * Copied from https://github.com/moagrius/RegexRouter
 */
class Router
{
    private $routes = array();

    private $beforeRouteCallback;

    public function __construct()
    {
        $this->beforeRouteCallback = function() { return true; };
    }


    public function route($pattern, $callback) {
        $this->routes[$pattern] = $callback;
    }

    public function beforeEachRoute($callback) {
        $this->beforeRouteCallback = $callback;
    }

    public function execute($uri) {
        foreach ($this->routes as $pattern => $callback) {
            if (preg_match($pattern, $uri, $params) === 1) {
                call_user_func_array($this->beforeRouteCallback, $params);
                array_shift($params);
                return call_user_func_array($callback, array_values($params));
            }
            else {
                error_log("$pattern did not match $uri");
            }
        }
    }
}