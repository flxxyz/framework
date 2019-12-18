<?php


namespace Col\Lib;

use DirectoryIterator;

class Storage
{
    /**
     * @var null | Storage
     */
    private static $instance = null;

    private $conf = [];

    /**
     * 默认使用的驱动选项
     *
     * @var string
     */
    private $driver = 'local';

    /**
     * 默认根目录
     *
     * @var mixed|string
     */
    private $rootPath = '/';

    /**
     * 默认存储目录
     *
     * @var mixed|string
     */
    private $dataPath = BASE_DIR.'data';

    /**
     * 默认返回的时间格式
     *
     * @var mixed|string
     */
    private $dateFormat = 'Y-m-d H:i:s';

    /**
     * 忽略文件列表
     *
     * @var array
     */
    private $ignore = [
        '.',
    ];

    private function __construct()
    {
        $conf = Conf::get('storage');
        $this->driver = $conf['driver'];
        $this->conf = $conf[$this->driver];
        if (!in_array($this->conf['dateFormat'], ['', 'Y-m-d H:i:s'])) {
            $this->dateFormat = $this->conf['dateFormat'];
        }
        if (!in_array($this->conf['rootPath'], ['', '/'])) {
            $this->rootPath = $this->conf['rootPath'];
        }
        if (!in_array($this->conf['dataPath'], [''])) {
            $this->dataPath = $this->conf['dataPath'];
        }
        $this->ignore = array_merge($this->ignore, $this->conf['ignore']);
    }

    public static function make()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 获取当前路径下所有文件及文件夹
     * @param null $path
     * @return array
     */
    public function get($path = null)
    {
        if (is_null($path) || in_array($path, ['', '/'])) {
            $this->ignore[] = '..';
            $path = '';
        }

        $dataPath = realpath(BASE_DIR.$this->dataPath).DS;
        $dataPath .= $path;

        $data = new DirectoryIterator($dataPath);

        $result = [];
        foreach ($data as $file) {
            if (in_array($file->getFilename(), $this->ignore)) {
                continue;
            }

            $t = explode('/', $path);


            $extra = [
                'type' => 'file',
                'name' => $file->getFilename(),
                'size' => '-',
                'time' => '-',
                'md5sum' => '-',
            ];


            if ($file->isDir()) {
                $extra['type'] = 'dir';
                if ($extra['name'] === '..') {
                    //二级目录返回一级目录
                    array_pop($t);
                    $url = (count($t) <= 1) ? '/' : join('/', $t);
                    $extra['link'] = "?path={$url}";
                } else {
                    $extra['link'] = "?path={$path}/{$extra['name']}";
                }
            } else {
                if ($file->isFile()) {
                    $extra['md5sum'] = md5_file($file->getRealPath());
                    $extra['time'] = date($this->dateFormat, $file->getCTime());
                    $extra['size'] = file_unit_conver($file->getSize());
                    $extra['link'] = sprintf('?path=%s&name=%s&down=1',
                        (count($t) <= 1) ? '/' : join('/', $t),
                        urlencode($extra['name'])
                    );
                }
            }

            $result[] = $extra;
        }

        return $this->sort($result);
    }

    public function download($name, $path = null)
    {
        return ['message' => 'download'];
    }

    /**
     * 文件夹排序优先
     * @param $result
     * @return array
     */
    private function sort($result)
    {
        $dirs = [];
        $files = [];
        foreach ($result as $value) {
            if ($value['type'] === 'dir') {
                $dirs[$value['name']] = $value;
            } else {
                $files[$value['name']] = $value;
            }
        }
        ksort($dirs, SORT_NATURAL | SORT_FLAG_CASE);
        ksort($files, SORT_NATURAL | SORT_FLAG_CASE);
        $result = array_merge($dirs, $files);

        return $result;
    }

    private function __clone()
    {
    }
}
