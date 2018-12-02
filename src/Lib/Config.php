<?php

namespace Col\Lib;


class Config
{
    private static $name = '';
    private static $arr = [];

    /**
     * @return array
     */
    public static function getArr()
    {
        return self::$arr;
    }

    /**
     * @return string
     */
    public static function getName()
    {
        return self::$name;
    }

    /*
     * 取
     */
    public static function get($filename = '', $key = null)
    {
        if ($filename === '') {
            self::$name = '';

            return self::$arr;
        }
        if ( !is_file(BASE_DIR . "config/{$filename}.php")) {
            return self::$arr;
        }
        $data = self::file($filename);
        if (is_null($key)) {
            return $data;
        }

        return self::key($key);
    }

    /*
     * 获取键值
     */
    private static function key($key)
    {
        foreach (self::$arr as $k => $v) {
            if ($key !== $k)
                continue;

            return self::$arr[$k];
        }
    }

    /*
     * 缓存配置
     */
    private static function file($filename = '')
    {
        if (self::$name === $filename) {
            return self::$arr;
        } else {
            self::$name = $filename;
            self::$arr = require BASE_DIR . "config/{$filename}.php";

            return self::$arr;
        }
    }
}