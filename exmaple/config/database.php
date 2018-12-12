<?php

return [
    /**
     * 默认使用的驱动
     */
    'driver' => 'mysql',
    'mysql'  => [
        /**
         * 数据库地址
         */
        'host'     => '127.0.0.1',
        /**
         * 数据库端口
         */
        'port'     => '3306',
        /**
         * 数据库名称
         */
        'database' => 'test',
        /**
         * 数据库用户名
         */
        'username' => 'root',
        /**
         * 数据库用户密码
         */
        'password' => 'root',
        /**
         * 表前缀
         */
        'prefix'   => 'col_',
        /**
         * PDO连接的其他参数
         */
        'options'  => [],
    ]
];