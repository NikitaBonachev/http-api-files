<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Debug\Debug;
use Silex\Provider\DoctrineServiceProvider;

Debug::enable();

$app = new App\Application('dev');

$app->register(
    new DoctrineServiceProvider(),
    [
        'db.options' =>
            [
                'dbname' => 'xsolla_test',
                'user' => 'api',
                'password' => '21333',
                'host' => 'localhost',
                'driver' => 'pdo_mysql'
            ]
    ]
);

$app->run();
