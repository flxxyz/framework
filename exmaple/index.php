<?php

//引入自动加载文件
require_once __DIR__ . '/../vendor/autoload.php';

// 预定义一些常量
define('DS', DIRECTORY_SEPARATOR, true);
define('BASE_DIR', realpath(__DIR__).DS, true);
define('APP_DIR', realpath(BASE_DIR.'app').DS, true);
define('VIEW_DIR', realpath(APP_DIR.'Views').DS, true);
define('MODEL_DIR', realpath(APP_DIR.'Models').DS, true);
define('CONTROLLER_DIR', realpath(APP_DIR.'Controllers').DS, true);

//模块目录名称
define('MODEL_NAME', 'App', true);
//控制器目录名称
define('CONTROLLER_NAME', 'Controllers', true);

use Col\Route;
use Col\Request;
use Col\Lib\Conf;
use Col\Lib\Logger;
use Col\Lib\Hash;

Conf::make();
Logger::make();

// 设置脚本时区
ini_set('date.timezone', Conf::get('app', 'timezone'));

// 实例路由，注入请求
Route::make(\request());

// 设置路由开始
Route::get('/', 'ExmapleController@index');
Route::any('/closure', function () {
    return '这是一个匿名函数';
});
Route::get('/log', 'ExmapleController@demoLogger');
Route::get('/request', 'ExmapleController@demoRequest');
Route::get('/json', 'ExmapleController@demoJson');
Route::get('/hash', function (Request $request) {
    //基准测试，控制性能开销在10毫秒内
    $timeTarget = 0.01; // 10 毫秒（milliseconds）

    $cost = 5;
    do {
        $start = microtime(true);
        Hash::make($request->getUserAgent(), $cost);
        $cost++;
        $end = microtime(true);
    } while (($end - $start) < $timeTarget);

    return "Appropriate Cost Found: " . ($cost-1);
});
Route::get('/view', 'ExmapleController@demoView');
Route::get('/storage', 'ExmapleController@demoStorage');
// 设置路由结束

Route::end();
