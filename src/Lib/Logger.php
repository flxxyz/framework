<?php

namespace Col\Lib;

use Col\Exceptions\LoggerException;
use Col\Exceptions\DirException;

/**
 * Class Logger
 *
 * @package Col\Lib
 * @method static notice($expression)
 * @method static info($expression)
 * @method static debug($expression)
 * @method static warn($expression)
 * @method static error($expression)
 */
class Logger
{
    /**
     * @var Logger | null
     */
    private static $instance = null;

    /**
     * @var array
     */
    private static $methods = [
        'NOTICE',
        'INFO',
        'DEBUG',
        'WARN',
        'ERROR',
    ];

    private $conf = [];

    private function __construct()
    {
        $this->conf = Conf::get('log');
    }

    public static function make()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
    }

    /**
     * 写入
     *
     * @param $content
     * @throws DirException
     */
    private function write($content)
    {
        if (!is_dir(BASE_DIR.'log')) {
            $mkdirState = mkdir(BASE_DIR.'log', 777, true);
            if (!$mkdirState) {
                throw new DirException('创建log目录失败');
            }
        }

        $filename = $this->conf['dir'].date('Y-m-d').'.log';
        $fp = fopen($filename, 'a+');
        fwrite($fp, $content);
        fclose($fp);
    }

    /**
     * 静态方法通用处理
     *
     * @param $name
     * @param $arguments
     * @throws DirException
     * @throws LoggerException
     */
    public static function __callStatic($name, $arguments)
    {
        if (is_null(self::$instance)) {
            throw new LoggerException('请先实例化Logger类');
        }

        if (in_array(strtoupper($name), self::$methods)) {
            $datetime = new \DateTime();
            $time = $datetime->format(self::$instance->conf['format']);
            foreach ($arguments as $value) {
                if (is_array($value) || is_object($value)) {
                    $data = json_encode($value, JSON_UNESCAPED_UNICODE);
                } else {
                    $data = $value;
                }

                $row = sprintf('[%s] [%s] %s'.PHP_EOL,
                    $time,
                    strtoupper($name),
                    $data
                );
                self::$instance->write($row);
            }
        }
    }

    private function __clone()
    {
    }
}
