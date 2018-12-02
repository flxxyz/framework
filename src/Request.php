<?php

namespace Col;


class Request
{
    private static $instence;

    protected $server;

    /**
     * 访问路径
     * @var mixed|string
     */
    protected $uri = '/';

    /**
     * 客户端的请求头
     * @var array
     */
    protected $header = [];
    /**
     * 用户客户端标识
     * @var string
     */
    protected $ua = '';

    /**
     * 访问方式
     * @var string
     */
    protected $method = '';

    /**
     * GET访问查询数据
     * @var array
     */
    protected $query = [];

    /**
     * POST访问携带数据
     * @var array
     */
    protected $body = [];

    /**
     * 客户端发送来的文件
     * @var array
     */
    protected $files = [];

    /**
     * 客户端设置的cookie
     * @var array
     */
    protected $cookie = [];

    /**
     * 是否ajax请求
     * @var bool
     */
    protected $ajax;

    /**
     * 初始化客户端访问数据
     * Request constructor.
     */
    private function __construct()
    {
        $this->server = $_SERVER;

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = filter_var($uri, FILTER_SANITIZE_URL);
        if (mb_stripos($uri, $_SERVER['SCRIPT_NAME']) !== false) {
            $uri = mb_substr($uri, mb_strlen($_SERVER['SCRIPT_NAME']));
        } else {
            //unix下不可用
            /*if (mb_stripos($uri, dirname($_SERVER['SCRIPT_NAME'])) !== false) {
                $uri = mb_substr($uri, mb_strlen($_SERVER['SCRIPT_NAME']));
            }*/
        }
        if (mb_substr($uri, -1) === '/') {
            $uri = mb_substr($uri, 0, mb_strlen($uri)-1);
        }
        if ($uri === '' || $uri === 'index.php') {
            $uri = '/';
        }
        $this->uri = $uri;

        $this->ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->header = (function () {
            $t = [];
            foreach ($_SERVER as $k => $v) {
                if (mb_stripos($k, 'http_') !== false) {
                    $t[mb_strtolower(mb_substr($k, 5))] = $v;
                }
            }
            return $t;
        })();

        if (isset($this->header['content_type'])
            && ($this->header['content_type'] == 'application/x-www-form-urlencoded')) {
            parse_str(file_get_contents("php://input"), $input);
        } else {
            $input = json_decode(file_get_contents("php://input"), true);
        }
        //可能返回的不是数组
        $this->body = is_array($input) ? $input : [];
        $this->body = array_merge($this->body, $_POST);
        $this->query = $_GET;
        $this->files = $_FILES ?? [];
        $this->cookie = $_COOKIE;
        $this->ajax = (($this->header['x_requested_with'] ?? false) === 'XMLHttpRequest');
    }

    /**
     * 单实例函数
     * @return Request
     */
    public static function make()
    {
        if (is_null(static::$instence)) {
            static::$instence = new static;
        }

        return static::$instence;
    }

    /**
     * 获取GET数据
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    public function get($key = '', $default = '')
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * 获取POST数据
     * @param string $key
     * @param string $default
     * @return mixed|string
     */
    public function post($key = '', $default = '')
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->ua;
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->ajax;
    }

    private function __clone() {}
}