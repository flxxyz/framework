<?php

namespace Col\Lib;


class Logger
{
    private static $instance = null;

    private $log = [];

    private function __construct() {
        $this->log = Config::get('app', 'log');
    }

    /**
     * @return Logger|null
     */
    public static function make()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function info(...$x)
    {
        foreach ($x as $v) {
            $this->message(__FUNCTION__, $v);
        }
    }

    public function notice(...$x)
    {
        foreach ($x as $v) {
            $this->message(__FUNCTION__, $v);
        }
    }

    public function debug(...$x)
    {
        foreach ($x as $v) {
            $this->message(__FUNCTION__, $v);
        }
    }

    public function warn(...$x)
    {
        foreach ($x as $v) {
            $this->message(__FUNCTION__, $v);
        }
    }

    public function err(...$x)
    {
        foreach ($x as $v) {
            $this->message(__FUNCTION__, $v);
        }
    }

    private function message($channel, $v)
    {
        $datetime = new \DateTime();
        $time = $datetime->format($this->log['format']);
        $channel = strtoupper($channel);

        $data = $v;
        if (is_array($v) || is_object($v)) {
            $data = json_encode($v, JSON_UNESCAPED_UNICODE);
        }
        $data = "[{$time}] [{$channel}] {$data}" . PHP_EOL;

        $filename = $this->log['dir'] . date('Y-m-d') . '.log';
        if (!is_dir($this->log['dir'])) {
            touch($filename);
        }

        file_put_contents($filename, $data, FILE_APPEND);
    }

    private function __clone() {}
}