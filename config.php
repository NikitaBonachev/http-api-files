<?php
return [
    'databases' => [
        'dev' => [
            'dbname' => 'xsolla_test',
            'user' => 'api',
            'password' => '21333',
            'host' => 'localhost',
            'driver' => 'pdo_mysql'
        ],
        'test' => [
            'dbname' => 'unit_test',
            'user' => 'root',
            'password' => 'q',
            'host' => 'localhost',
            'driver' => 'pdo_mysql'
        ]
    ],
    'uploadDirs' => [
        'dev' => __DIR__ . '/upload/',
        'test' => __DIR__ . '/tests/upload/'
    ],
    'log' => [
        'dev' => __DIR__ . '/log/errors.log',
        'test' => __DIR__ . '/tests/log/errors.log',
    ]
];
