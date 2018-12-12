<?php

namespace App\Controllers;


use App\Models\TestUser;
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
            '/oss' => '阿里云oss测试',
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

    public function demoDb()
    {
        // 通过id查找单条记录
        TestUser::find(1);

        // 查找所有记录，显示部分字段
        TestUser::all('id,name', 'num', 'time');

        // 同上
        TestUser::select('id,name', 'num', 'time')->get();

        // 多种方式条件查找单条(使用find)或多条(使用get)
        TestUser::where([
            ['num', '>=', 5],
            'time' => date('Y-m-d'),
        ])->find();

        // 插入记录
        TestUser::insert([
            'name' => '李美丽',
            'num' => 666,
            'time' => date('Y-m-d'),
        ]);

        // 条件更新
        TestUser::where([
            'id' => 1,
        ])->update([
            'num' => 777,
            'time' => date('Y-m-d'),
        ]);
    }

    public function demoAliyunOss()
    {
        $bucketListInfo = oss()->listBuckets();
        $bucketList = $bucketListInfo->getBucketList();
        foreach($bucketList as $bucket) {
            print($bucket->getLocation() . "\t" . $bucket->getName() . "\t" . $bucket->getCreatedate() . "\n");
        }
    }
}