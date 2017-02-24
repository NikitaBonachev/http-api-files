<?php
return [
    'databases' => [
        'prod' => [
            'dbname' => '',
            'user' => '',
            'password' => '',
            'host' => '',
            'driver' => ''
        ],
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
        ],
        'doctrine' => [
            'db.options' => [
                'driver' => 'pdo_mysql',
                'path' => __DIR__ . '/cache/xsolla_test.db'
            ]
        ]
    ],
    'uploadDirs' => [
        'prod' => __DIR__.'/upload/',
        'dev' => __DIR__.'/upload/',
        'test' => __DIR__.'/upload/test/'
    ]
];
