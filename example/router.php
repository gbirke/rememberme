<?php

namespace Birke\Rememberme\Example;

/**
 * Inspired by https://github.com/moagrius/RegexRouter
 */
class Router
{
    private $routes = array();

    private $beforeRouteCallback;

    /**
     * Initialize router with empty callback
     */
    public function __construct()
    {
        $this->beforeRouteCallback = function () {
        };
    }

    /**
     * Add a callback for a specific URL pattern
     *
     * @param string   $pattern  Regular expression for matching URLs
     * @param callback $callback Callback to call when the route matches
     */
    public function route($pattern, $callback)
    {
        $this->routes[$pattern] = $callback;
    }

    /**
     * Add a callback that gets called before a route is executed.
     *
     * If the callback should create an output that replaces the route output, it must do its own rendering and
     * call exit();
     *
     * @param callback $callback
     */
    public function beforeEachRoute($callback)
    {
        $this->beforeRouteCallback = $callback;
    }

    /**
     * Match URI to route definition and call it
     *
     * @param string $uri
     * @return mixed
     */
    public function execute($uri)
    {
        foreach ($this->routes as $pattern => $callback) {
            if (preg_match($pattern, $uri, $params) === 1) {
                call_user_func_array($this->beforeRouteCallback, $params);
                array_shift($params);

                return call_user_func_array($callback, array_values($params));
            } else {
                error_log("$pattern did not match $uri");
            }
        }
    }
}
