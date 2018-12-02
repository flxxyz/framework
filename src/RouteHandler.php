<?php

namespace Col;


use Col\Exceptions\MethodNotFoundException;

class RouteHandler
{
    /**
     * @var array
     */
    protected static $httpMethods = [
        'get'    => ['GET', 'HEAD'],
        'post'   => ['POST'],
        'put'    => ['PUT', 'PATCH'],
        'delete' => ['DELETE'],
    ];

    /**
     * @var array
     */
    public static $routes = [];

    /**
     * @var Request
     */
    protected static $request;

    /**
     * @var callable|array
     */
    protected static $routeCallback;

    /**
     * 路由添加方法
     * @param $uri
     * @param $callback
     * @param $action
     */
    protected static function add($uri, $callback, $action)
    {
        foreach (static::$httpMethods[$action] as $method) {
            static::$routes[$uri][$method] = $callback;
        }
    }

    /**
     * 处理路由消息
     * @return array|\Closure|null|string
     * @throws \ReflectionException
     */
    protected static function routeHandler()
    {
        foreach (static::$routes as $uri => $route) {
            if (static::$request->getUri() !== $uri) {
                continue;
            }

            if (!isset($route[static::$request->getMethod()])) {
                return null;
            }

            $closure = $route[static::$request->getMethod()];

            if (!isset($closure)) {
                return null;
            }

            if (is_string($closure)) {
                if (mb_strpos($closure, '@') === false
                    || mb_strpos($closure, '@') === 0
                ) {
                    return null;
                }

                $action = explode('@', $closure);
                $c = "\\App\\Controllers\\{$action[0]}";
                $a = $action[1];

                $rc = new \ReflectionClass(new $c);
                if (!$rc->hasMethod($a)) {
                    return static::methodNotFound($rc->getName(), $a);
                }

                $closure = [(new $c), $a];
            }

            return $closure;
        }

        return null;
    }

    protected static function methodNotFound(...$v)
    {
        return function ($a) use ($v) {
            echo "'{$v[0]}' does not have a method '{$v[1]}'";
            //throw new MethodNotFoundException();
        };
    }
}