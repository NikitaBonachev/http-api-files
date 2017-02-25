<?php

use Silex\Application as SilexApplication;
use Silex\Provider\WebProfilerServiceProvider;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Silex\Provider\DoctrineServiceProvider;

$loader = require __DIR__ . '/../vendor/autoload.php';

$app_env = 'prod';
$app = new App\Application($app_env);
$app['env'] = $app_env;

$app->register(
    new DoctrineServiceProvider(),
    [
        'db.options' => \App\Config\ConfigProvider::getDatabaseConfig($app_env)
    ]
);

return [$app, $loader];
