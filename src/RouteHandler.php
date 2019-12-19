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
        'any'    => [],
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
     * @var \Closure|null
     */
    protected static $routeCallback = null;

    /**
     * 路由添加方法
     * @param $uri
     * @param $callback
     * @param $action
     */
    protected static function add($uri, $callback, $action)
    {
        foreach (self::$httpMethods[$action] as $method) {
            self::$routes[$uri][$method] = $callback;
        }
    }

    /**
     * 处理路由消息
     * @return \Closure|null
     * @throws \ReflectionException
     */
    protected static function routeHandler()
    {
        foreach (self::$routes as $uri => $route) {
            if (self::$request->getUri() !== $uri) {
                continue;
            }

            $closure = isset($route[self::$request->getMethod()])
                ? $route[self::$request->getMethod()]
                : null;

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
     * @return \Closure
     */
    protected static function methodNotFound()
    {
        $v = func_get_args();
        return function () use ($v) {
            echo "'{$v[0]}' does not have a method '{$v[1]}'<pre>";
            throw new MethodNotFoundException('没有找到该方法,检查一下大小写吧');
        };
    }
}
