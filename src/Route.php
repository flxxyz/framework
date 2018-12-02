<?php

namespace Col;


use Col\Lib\Config;

class Route extends RouteHandler
{
    public static function make(Request $request)
    {
        static::$httpMethods['any'] = (function () {
            $t = [];
            foreach (static::$httpMethods as $method) {
                $t = array_merge($t, $method);
            }
            return $t;
        })();
        static::$request = $request;
    }

    public static function end()
    {
        static::$routeCallback = static::routeHandler() ?? function () {
                http_response_code(404);
                $error_page = Config::get('app', 'error_page');
                include_once  $error_page['404'];
            };

        ob_start();

        $a = (static::$routeCallback)(static::$request);
        if (is_string($a) || is_numeric($a)) {
            echo $a;
        }

        ob_end_flush();
        exit();
    }

    public static function action($uri, $callback, $action)
    {
        static::add($uri, $callback, $action);
    }

    public static function __callStatic($name, $arguments)
    {
        $arguments[] = $name;
        ([new static, 'action'])(...$arguments);
    }
}