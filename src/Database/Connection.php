<?php

namespace Col\Database;

use PDO;
use PDOStatement;

class Connection implements ConnectionInterface
{
    /**
     * @var Connection
     */
    protected static $instance;

    /**
     * @var PDO
     */
    protected $pdo;

    protected $database;

    /**
     * @var array
     */
    protected $config = [];

    public function __construct($config)
    {
        $driver = $config['driver'];
        $info = $config[$driver];
        $this->config = $config[$driver];

        try {
            $this->pdo = new PDO(
                $driver.':host='.$info['host'].';dbname='.$info['database'].';port='.$info['port'],
                $info['username'],
                $info['password'],
                $info['options']
            );
        }catch (ConnectionException $e) {
            die($e->getMessage());
        }

        $this->database = $info['database'];
        //return $this;
    }

    public function getUser()
    {
        return $this->config['username'];
    }

    public function getPass()
    {
        return $this->config['password'];
    }

    public function getDbName()
    {
        return $this->config['database'];
    }

    public function getPrefix()
    {
        return $this->config['prefix'] ?? '';
    }



    public static function make($config)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($config);
        }

        return static::$instance;
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }


    public function select($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            $statement = $this->getPdo()->prepare($query);
            $this->bindValues($statement, $this->prepareBindings($bindings));
            $statement->execute();
            return $statement->fetchAll();
        });
    }

    /**
     * @param \PDOStatement $statement
     * @param array $bindings
     */
    public function bindValues($statement, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1, $value,
                is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }

    public function prepareBindings($bindings = [])
    {
        foreach ($bindings as $key => $value) {
            if (is_bool($value)) {
                $bindings[$key] = (int)$value;
            }
        }

        return $bindings;
    }

    protected function handleQueryException($e, $query, $bindings, $callback)
    {
        throw $e;
    }

    /**
     * @param $query
     * @param $bindings
     * @param $callback
     */
    public function run($query, $bindings, $callback)
    {
        try {
            $res = $this->runQueryCallback($query, $bindings, $callback);
        }catch (QueryException $e) {
            $res = $this->handleQueryException($e, $query, $bindings, $callback);
        }

        return $res;
    }

    public function runQueryCallback($query, $bindings, $callback)
    {
        try {
            $result = $callback($query, $bindings);
        } catch (QueryException $e) {
            throw new QueryException(
                $query, $this->prepareBindings($bindings), $e
            );
        }

        return $result;
    }
}