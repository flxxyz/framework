<?php

namespace Col;


use Col\Lib\Config;

class Route extends RouteHandler
{
    /**
     * 路由析构
     * @param Request $request
     */
    public static function make(Request $request)
    {
        //合并http方法
        static::$httpMethods['any'] = (function () {
            $t = [];
            foreach (static::$httpMethods as $method) {
                $t = array_merge($t, $method);
            }
            return $t;
        })();
        //传递当前请求类
        static::$request = $request;
    }

    /**
     * 路由解构
     * @throws \ReflectionException
     */
    public static function end()
    {
        static::$routeCallback = static::routeHandler() ?? function () {
                http_response_code(404);
                $error_page = Config::get('app', 'error_page');
                include_once  "{$error_page['404']}";
            };

        ob_start();

        $a = (static::$routeCallback)(static::$request);
        if (is_string($a) || is_numeric($a)) {
            echo $a;
        }

        ob_end_flush();
        exit();
    }

    public static function __callStatic($name, $arguments)
    {
        $arguments[] = $name;
        static::add(...$arguments);
    }
}