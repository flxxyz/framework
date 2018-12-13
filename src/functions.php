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
    function oss($bucket = null) {
        return \Col\Lib\SDK\AliyunOss::make($bucket);
    }
}

if (!function_exists('view')) {
    function view($filename = '', $data = []) {
        $filename = $filename . '.view.php';
        $path = VIEW_DIR . $filename;

        if (!is_file($path)) {
            exit('视图文件不存在=' . $filename);
        }

        extract($data, EXTR_PREFIX_INVALID, 'view_');  // 非法或数字变量, 添加前缀
        include_once "{$path}";
    }
}

if (!function_exists('file_unit_conver')) {
    function file_unit_conver($byte = 0)
    {
        if ($byte == 0) {
            return '0B';
        }
        $bytes = [
            'TB' => pow(1024, 4),
            'GB' => pow(1024, 3),
            'MB' => pow(1024, 2),
            'KB' => 1024,
            'B'  => 1,
        ];
        foreach ($bytes as $name => $value) {
            $n = intval($byte) / $value;
            if (0 != $c = floor($n)) {
                return round($n, 2) . $name;
            }
        }
    }
}