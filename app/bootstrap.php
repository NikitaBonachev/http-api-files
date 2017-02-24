<?php

use Silex\Application as SilexApplication;
use Silex\Provider\WebProfilerServiceProvider;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Silex\Provider\DoctrineServiceProvider;

$loader = require __DIR__.'/../vendor/autoload.php';

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

$app_env = 'dev';

if (isset($app_env) && in_array($app_env, ['prod', 'dev', 'test', 'qa']))
    $app['env'] = $app_env;
else
    $app['env'] = 'prod';

return [$app, $loader];
