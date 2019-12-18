<?php

namespace Col;


use Col\Lib\Conf;

class Route extends RouteHandler
{
    /**
     * 路由析构
     * @param Request $request
     */
    public static function make(Request $request)
    {
        //合并http方法
        self::$httpMethods['any'] = (function () {
            $t = [];
            foreach (self::$httpMethods as $method) {
                $t = array_merge($t, $method);
            }
            return $t;
        })();
        //传递当前请求类
        self::$request = $request;
    }

    /**
     * 路由解构
     * @throws \ReflectionException
     */
    public static function end()
    {
        self::$routeCallback = self::routeHandler() ?? function () {
                http_response_code(404);
                $error_page = Conf::get('app', 'error_page');
                include_once "{$error_page['404']}";
            };

        ob_start();

        $r = (self::$routeCallback)(self::$request);
        if (is_string($r) || is_numeric($r)) {
            echo $r;
        }

        ob_end_flush();
        exit();
    }

    public static function __callStatic($name, $arguments)
    {
        self::add($arguments[0], $arguments[1], $name);
    }
}
