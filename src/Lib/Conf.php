<?php

namespace Col\Lib;


class Conf
{
    /**
     * @var null | Conf
     */
    private static $instence = null;

    /**
     * @var string
     */
    private static $filename = '';

    /**
     * @var array
     * @example [
     *   'app' => require_once('app.php'),
     *   'database' => require_once('database.php'),
     *    ...
     * ]
     */
    private static $data = [];

    public static function make()
    {
        if (is_null(self::$instence)) {
            self::$instence = new self();
        }
    }

    /**
     * @return Conf|null
     */
    public static function getInstence()
    {
        return self::$instence;
    }

    /*
     * 取
     */
    public static function get($filename = '', $key = null)
    {
        if ($filename === '') {
            self::$filename = '';

            return null;
        }
        if (!is_file(realpath(BASE_DIR."config/{$filename}.php"))) {
            return null;
        }

        self::handleFile($filename);

        if (is_null($key)) {
            return self::$data[$filename];
        }

        return self::handleKey($key);
    }

    /*
     * 获取键值
     */
    private static function handleKey($key)
    {
        return array_key_exists($key, self::$data[self::$filename])
            ? self::$data[self::$filename][$key] : null;
    }

    /*
     * 缓存配置
     */
    private static function handleFile($filename = '')
    {
        if (self::$filename !== $filename) {
            self::$filename = $filename;
            self::$data[$filename] = require BASE_DIR."config/{$filename}.php";
        }

        return self::$data[$filename];
    }
}
