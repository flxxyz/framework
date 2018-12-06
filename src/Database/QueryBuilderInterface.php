<?php

namespace Col\Database;


interface QueryBuilderInterface
{
    public function prefix($prefix);

    public function table($table);

    public function query($fetch = 'fetchAll');

    public function statement($query);
}