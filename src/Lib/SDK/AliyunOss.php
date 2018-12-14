<?php

namespace Col\Lib\SDK;


use Col\Lib\Config;
use OSS\Core\OssException;
use OSS\OssClient;

class AliyunOss
{
    /**
     * @var AliyunOss
     */
    private static $instance;

    /**
     * @var OssClient
     */
    private static $ossClient;

    private $accessKeyId = '';
    private $accessKeySecret = '';
    private $endpoint = '';
    private $bucket = '';
    private $domain = '';
    private $timeout = 3600;

    private function __construct($bucket = null)
    {
        $config = Config::get('storage', 'aliyun_oss');
        $this->accessKeyId = $config['accessKeyId'];
        $this->accessKeySecret = $config['accessKeySecret'];
        $this->endpoint = $config['endpoint'];
        $this->bucket = $bucket ?? $config['bucket'];
        $this->domain = $config['domain'];
        $this->timeout = $config['timeout'];

        try {
            static::$ossClient = $this->newOssClient();
        } catch (OssException $e) {
            logger()->err('创建oss客户端错误: '.$e->getMessage());

            return false;
        }
    }

    /**
     * @param $bucket
     * @return AliyunOss
     */
    public static function make($bucket)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($bucket);
        }

        return static::$instance;
    }

    /**
     * @param string $bucket
     * @return $this
     */
    public function setBucket(string $bucket)
    {
        $this->bucket = $bucket;

        return $this;
    }

    /**
     * @return OssClient
     * @throws OssException
     */
    private function newOssClient()
    {
        return new OssClient(
            $this->accessKeyId,
            $this->accessKeySecret,
            $this->endpoint
        );
    }

    /**
     * @return \OSS\Model\BucketInfo[]
     * @throws OssException
     */
    public function bucketAll()
    {
        $bucketListInfo = static::$ossClient->listBuckets();

        return $bucketListInfo->getBucketList();
    }

    public function getBucketAcl()
    {
        return static::$ossClient->getBucketAcl($this->bucket);
    }

    /**
     * @param null $prefix
     * @param int  $max_keys
     * @param null $delimiter
     * @param null $marker
     * @return array
     * @throws OssException
     */
    public function getList($prefix = null, $max_keys = 100, $delimiter = null, $marker = null)
    {
        $options = [
            'max-keys' => $max_keys
        ];
        if (!is_null($prefix)) {
            $options['prefix'] = $prefix;
//            $prefix = pathinfo($prefix)['dirname'];
        }
        if (!is_null($delimiter)) {
            $options['delimiter'] = $delimiter;
        }
        if (!is_null($marker)) {
            $options['marker'] = $marker;
        }

        $dirList = [];
        $fileList = [];

        while (true) {
            $listObjectInfo = static::$ossClient->listObjects($this->bucket, $options);
            $nextMarker = $listObjectInfo->getNextMarker();
            $listObject = $listObjectInfo->getObjectList();
            $listPrefix = $listObjectInfo->getPrefixList();
            if (!empty($listObject)) {
                foreach ($listObject as $objectInfo) {
                    if ($prefix === $objectInfo->getKey()) {
                        $fileList[] = [
                            'type' => 'dir',
                            'name' => '..',
                            'size' => null,
                            'time' => null,
                            'url' => '?path=',
                        ];
                    } else {
                        $basename = pathinfo($objectInfo->getKey())['basename'];
                        $fileList[] = [
                            'type' => 'file',
                            'name' => $basename,
                            'size' => file_unit_conver($objectInfo->getSize()),
                            'time' => strtotime($objectInfo->getLastModified()),
                            'url' => $this->signUrl($objectInfo->getKey()),
                        ];
                    }
                }
            }

            if (!empty($listPrefix)) {
                foreach ($listPrefix as $prefixInfo) {
                    $dirList[] = [
                        'type' => 'dir',
                        'name' => $prefixInfo->getPrefix(),
                        'size' => null,
                        'time' => null,
                        'url' => '?path=' . $prefixInfo->getPrefix(),
                    ];
                }
            }

            if ($nextMarker === '') {
                break;
            }
        }

        sort($dirList);
        sort($fileList);

        return array_merge($dirList, $fileList);
    }

    /**
     * 授权访问url
     * @param $object
     * @return string
     * @throws OssException
     */
    private function signUrl($object)
    {
        switch ($this->getBucketAcl()) {
            case 'private':
                return static::$ossClient->signUrl($this->bucket, $object, $this->timeout);
                break;
            case 'public-read':
            case 'public-read-write':
            case 'public':
                return '//' . $this->domain . '/' . $object;
                break;
        }
    }

    private function __clone() {}
}