<?php

namespace Col;

use Col\Lib\Config;

abstract class Controller
{
    private $param = [];

    /**
     * 传递模板数据
     * @param $name
     * @param $value
     * @example $this->assign('param1', '这是字符串');
     */
    protected function assign($name, $value)
    {
        $this->param[$name] = $value;
    }

    /**
     * 输出视图
     * @param array $path
     */
    protected function display($path = [])
    {
        $filename = join(
                '/',
                is_array($path) ? [$this->getMethod()] : func_get_args()
            ) . '.view.php';
        $path = VIEW_DIR . $filename;

        if ( !is_file($path)) {
            exit('视图文件不存在=' . $filename);
        }

        extract($this->param);
        include_once "{$path}";
    }

    /**
     * 取调用的子类方法名
     * @return mixed
     */
    private function getMethod()
    {
        $backtrace = debug_backtrace();
        array_shift($backtrace);

        return $backtrace[1]['function'];
    }

    /**
     * json输出
     * @param array $a
     * @return array|false|string
     */
    protected function json($a = [])
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, Config::get('app', 'allow_origins'))) {
            header('Access-Control-Allow-Origin: ', $origin);
        }

        header('Access-Control-Allow-Methods: ' . Config::get('app', 'allow_methods'));
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/json;charset:utf-8;');
        header('Pragma: no-cache');

        if (is_array($a)) {
            $a = json_encode($a, JSON_UNESCAPED_UNICODE);
        } else {
            $a = [];
        }

        return $a;
    }
}