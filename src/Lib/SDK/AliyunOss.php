<?php

namespace Col\Lib\SDK;


use Col\Lib\Config;
use OSS\Core\OssException;
use OSS\OssClient;

class AliyunOss
{
    /**
     * @var OssClient
     */
    private static $instance;

    public $accessKeyId = '';
    public $accessKeySecret = '';
    public $endpoint = '';
    public $bucket = '';

    private function __construct()
    {
        $config = Config::get('storage', 'aliyun_oss');
        $this->accessKeyId = $config['accessKeyId'];
        $this->accessKeySecret = $config['accessKeySecret'];
        $this->endpoint = $config['endpoint'];
        $this->bucket = $config['bucket'];

        try {
            static::$instance = new OssClient(
                $this->accessKeyId,
                $this->accessKeySecret,
                $this->endpoint
            );
        } catch (OssException $e) {
            logger()->err('创建oss客户端错误: '.$e->getMessage());
            return false;
        }
    }

    /**
     * @return OssClient
     */
    public static function make()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    private function __clone() {}
}