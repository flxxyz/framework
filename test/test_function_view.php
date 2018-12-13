<?php
//引入自动加载文件
require_once __DIR__ . '/../vendor/autoload.php';

// 预定义一些常量
define('DS', DIRECTORY_SEPARATOR, true);
define('BASE_DIR', realpath(__DIR__.'/../exmaple').DS, true);
define('APP_DIR', realpath(BASE_DIR.'app').DS, true);
define('VIEW_DIR', realpath(APP_DIR.'Views').DS, true);
define('MODEL_DIR', realpath(APP_DIR.'Models').DS, true);
define('CONTROLLER_DIR', realpath(APP_DIR.'Controllers').DS, true);

view('error/404');
view('error');
