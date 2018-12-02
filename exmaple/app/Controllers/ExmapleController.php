<?php

namespace App\Controllers;


use App\Models\User;
use Col\{
    Controller,
    Request
};

class ExmapleController extends Controller
{

    public function index()
    {
        $data = [
            '/' => '首页',
            '/closure' => '路由匿名函数',
            '/log' => '日志记录',
            '/request' => 'Request类集成',
            '/json' => 'json (RESTful API)',
            '/hash' => '基准测试 (哈希类)',
            '/view/?param=我是一个变量哟' => '视图输出，传递变量',
        ];
        $this->assign('urls', $data);

        return $this->display('example');
    }

    public function demoLogger()
    {
        $content = '这里会记录打印一条日志';

        logger()->info($content);

        return $content;
    }

    public function demoRequest(Request $request)
    {
        return $request->getUserAgent();
    }

    public function demoJson(Request $request)
    {
        if ($request->isAjax()) {
            $data = [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ];
        } else {
            $data = [
                'message' => '这不是一个ajax请求',
            ];
        }

        return $this->json($data);
    }

    public function demoView(Request $request)
    {
        $this->assign('test_str', $request->get('param', '666'));

        return $this->display();
    }
}