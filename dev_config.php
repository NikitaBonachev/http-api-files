<?php
return [
    'database' => [
        'dbname' => 'xsolla_test',
        'user' => 'api',
        'password' => '21333',
        'host' => 'localhost',
        'driver' => 'pdo_mysql'
    ],
    'doctrine' => [
        'db.options' => [
            'driver' => 'pdo_mysql',
            'path' => __DIR__ . '/cache/xsolla_test.db'
        ]
    ],
];
