<?php

namespace Col\Database;

use PDO;
use PDOException;

class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $columns = '*';

    /**
     * @var string
     */
    protected $where = '';

    /**
     * @var string
     */
    protected $update = '';

    /**
     * @var string
     */
    protected $limit = '';

    /**
     * @var mixed|array
     */
    protected $result;

    /**
     * @var array
     */
    protected $bindings = [
        'where' => [],
        'insert' => [],
        'update' => [],
    ];

    /**
     * @var string
     */
    protected $method;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->prefix = $connection->getPrefix();
    }

    public function prefix($prefix): QueryBuilder
    {
        $this->prefix = $prefix ?? $this->prefix;

        return $this;
    }

    public function table($table): QueryBuilder
    {
        $this->table = join('', [
            $this->prefix,
            $table,
        ]);

        return $this;
    }

    public function columns($columns = ['*']): QueryBuilder
    {
        $this->columns =
            join(', ', is_array($columns) ? $columns : func_get_args());

        return $this;
    }

    public function bindings($bindings, $method): QueryBuilder
    {
        $this->method = $method;

        if (count($bindings)) {
            switch ($method) {
                case 'where':
                    $bindingMethod = 'bindingsWhere';
                    break;
                case 'insert':
                    $bindingMethod = 'bindingsInsert';
                    break;
                case 'update':
                    $bindingMethod = 'bindingsUpdate';
                    break;
            }

            $this->bindings[$method] = $this->{$bindingMethod}($bindings);
        }

        return $this;
    }

    protected function bindingsWhere($bindings): array
    {
        $this->where = 'WHERE ';
        $total = count($bindings);
        $end = $total - 1;
        $n = 0;
        $temp = [];

        foreach ($bindings as $key => $binding) {
            $name = $key;
            $operator = '=';

            if (is_array($binding)) {
                $name = $binding[0];
                $operator = $binding[1];
            }

            if ($total > 1) {
                $format = '`%s` %s ?';
                if ($n < $end) {
                    $format .= ' and ';
                }
            } else {
                $format = '`%s` %s ?';
            }

            $this->where .= sprintf($format, $name, $operator);
            $n++;
            $temp[] = is_array($binding) ? $binding[2] : $binding;
        }

        return array_values($temp);
    }

    protected function bindingsInsert($bindings)
    {
        $this->where = 'VALUES ';
        $total = count($bindings);

        $this->where .= sprintf('(%s)', join(', ', array_fill(0, $total, '?')));

        $this->columns(array_map(
            function ($value) {
                return "`{$value}`";
            },
            array_keys($bindings)
        ));

        return array_values($bindings);
    }

    protected function bindingsUpdate($bindings)
    {
        $total = count($bindings);
        $end = $total - 1;
        $n = 0;

        foreach ($bindings as $name => $binding) {
            $format = '`%s` = ?';
            if ($n < $end) {
                $format .= ', ';
            }

            $this->update .= sprintf($format, $name);
            $n++;
        }

        return array_merge(
            array_values($bindings),
            $this->bindings['where']
        );
    }

    public function select(...$columns): QueryBuilder
    {
        $this->columns($columns);

        return $this;
    }

    public function limit($start, $page = null): QueryBuilder
    {
        $limits = [$start ?? 1];

        if ( !is_null($page)) {
            $limits[] = $page;
        }

        $this->limit = 'limit ' . join(',', $limits);

        return $this;
    }

    public function all()
    {
        return $this->query()->result;
    }

    public function find($id = null)
    {
        $bindings = [];

        if ( !is_null($id)) {
            $bindings['id'] = $id;
        }

        return $this->bindings($bindings, 'where')
            ->limit(1)
            ->query('fetch')
            ->result;
    }

    public function get()
    {
        return $this->query()->result;
    }

    public function query($fetch = 'fetchAll'): QueryBuilder
    {
        $sql =
            "SELECT {$this->columns} FROM `{$this->table}` {$this->where} {$this->limit}";

        $statement = $this->statement($sql);

        $this->result = ([$statement, $fetch])(PDO::FETCH_OBJ);

        return $this;
    }

    public function insert()
    {
        $sql = "INSERT INTO `{$this->table}` ({$this->columns}) {$this->where}";

        $this->statement($sql);

        return $this->insert_id();
    }

    public function update($bindings = [])
    {
        $this->bindings($bindings, 'update');

        $sql = "UPDATE `{$this->table}` SET {$this->update} {$this->where}";

        $statement = $this->statement($sql);

        return $statement->rowCount();
    }

    /**
     *
     * @return string
     */
    public function insert_id()
    {
        return $this->connection->getPdo()->lastInsertId();
    }

    public function statement($query)
    {
        try {
            $statement = $this->connection->getPdo()->prepare($query);
            $statement->execute($this->bindings[$this->method]);

            return $statement;
        } catch (PDOException $e) {
            return false;
        }
    }
}