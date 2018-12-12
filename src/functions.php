<?php

if (!function_exists('logger')) {
    function logger() {
        return \Col\Lib\Logger::make();
    }
}

if (!function_exists('request')) {
    function request() {
        return \Col\Request::make();
    }
}

if (!function_exists('oss')) {
    function oss() {
        return \Col\Lib\SDK\AliyunOss::make();
    }
}
