<?php

namespace Col;


use Col\Lib\Config;
use PDO;
use Col\Database\Connection;
use Col\Database\QueryBuilder;

/**
 * Class Model
 * @package Col
 */
abstract class Model
{
    use Helpers;

    /**
     * @var Connection
     */
    protected static $connection;

    /**
     * @var QueryBuilder
     */
    protected static $model;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var QueryBuilder
     */
    protected static $queryBuilder;

    public function __construct()
    {
        static::setConnection(Config::get('database'));
        static::$queryBuilder = new QueryBuilder(
            static::getConnection()
        );
    }

    protected static function setConnection($config)
    {
        static::$connection = Connection::make($config);
    }

    protected static function getConnection(): Connection
    {
        return static::$connection;
    }

    protected static function make(): QueryBuilder
    {
        static::$model = (new static)->newQuery();

        return static::$model;
    }

    /**
     * @param $columns
     * @return QueryBuilder
     */
    public static function all(...$columns)
    {
        return static::make()->columns(
            count($columns) ? $columns : ['*']
        )->all();
    }

    public static function get()
    {
        return static::make()->query()->get();
    }

    public static function find($id = null)
    {
        $bindings = [];
        if ( !is_null($id)) {
            $bindings['id'] = $id;
        }

        return static::make()->bindings(
            $bindings,
            'where'
        )->find();
    }

    public static function insert($data = [])
    {
        return static::make()->bindings(
            $data,
            'insert'
        )->insert();
    }

    public static function select($columns = ['*'])
    {
        return static::make()->columns(
            is_array($columns) ? $columns : func_get_args()
        );
    }

    public static function where($bindings = [])
    {
        return static::make()->bindings(
            $bindings,
            'where'
        );
    }

    /**
     * 创建查询构造器
     * @return QueryBuilder
     */
    public function newQuery()
    {
        return static::getQueryBuilder()
            ->prefix(
                $this->getPrefix()
            )
            ->table(
                $this->getTable()
            );
    }

    /**
     * 获取表前缀
     * @return mixed|string
     */
    public function getPrefix()
    {
        if ( !$this->prefix) {
            $this->prefix =
                static::getConnection()->getPrefix();
        }

        return $this->prefix;
    }

    /**
     * 获取model名
     * @return string
     */
    public function getTable()
    {
        if ( !isset($this->table)) {
            return strtolower(basename(
                str_replace('\\', '/', static::class)
            ));
        }

        return $this->table;
    }

    /**
     * 获取查询构造器
     * @return QueryBuilder
     */
    protected static function getQueryBuilder(): QueryBuilder
    {
        if (is_null(static::$connection->getPdo())) {
            //重连数据库
            static::setConnection(Config::get('database'));
            static::$queryBuilder = new QueryBuilder(
                static::getConnection()
            );
        }

        return static::$queryBuilder;
    }

    /**
     * 静态调用 (无法获取model名)
     * @param $name
     * @param $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        //return ([static::getQueryBuilder(), $name])(...$arguments);
    }
}