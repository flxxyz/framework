<?php

namespace Col;


use Col\Exceptions\MethodNotFoundException;

class RouteHandler
{
    /**
     * 可定义的http方法
     * @var array
     */
    protected static $httpMethods = [
        'get'    => ['GET', 'HEAD'],
        'post'   => ['POST'],
        'put'    => ['PUT', 'PATCH'],
        'delete' => ['DELETE'],
    ];

    /**
     * 注册路由表
     * @var array
     */
    public static $routes = [];

    /**
     * 当前请求类
     * @var Request
     */
    protected static $request;

    /**
     * 路由处理回调函数
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

            $closure = $route[static::$request->getMethod()] ?? null;

            if (is_null($closure)) {
                return null;
            }

            if (is_string($closure)) {
                if (mb_strpos($closure, '@') === false
                    || mb_strpos($closure, '@') === 0
                ) {
                    return null;
                }

                $action = explode('@', $closure);
                $className = '\\'.MODEL_NAME.'\\'.CONTROLLER_NAME.'\\'.$action[0];
                $method = $action[1];

                $class = new $className;
                $rc = new \ReflectionClass($class);
                if (!$rc->hasMethod($method)) {
                    return static::methodNotFound($rc->getName(), $method);
                }

                $closure = [$class, $method];
            }

            return $closure;
        }

        return null;
    }

    /**
     * 路由方法不存在抛错
     * @param mixed ...$v
     * @return \Closure
     */
    protected static function methodNotFound(...$v)
    {
        return function () use ($v) {
            throw new MethodNotFoundException("'{$v[0]}' does not have a method '{$v[1]}'");
        };
    }
}