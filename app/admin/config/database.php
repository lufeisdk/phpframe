<?php
return [
    # 默认读取default配置
    'default' => [
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'user' => 'root',
        'passwd' => 'root',
        'dbname' => 'phpframe',
        'port' => 3306,
        'prefix' => 'pf_',
        'charset' => 'utf8mb4'
    ],

    'centos6' => [
        'type' => 'mysql',
        'host' => '192.168.209.128',
        'user' => 'root',
        'passwd' => 'abcYSSysys2020@gd.com',
        'dbname' => 'gm_log',
        'port' => 3306,
        'prefix' => '',
        'charset' => 'utf8mb4'
    ],
];