<?php

use Silex\Application as SilexApplication;
use Silex\Provider\DoctrineServiceProvider;

$loader = require __DIR__ . '/../vendor/autoload.php';

$app_env = 'test';
$app = new App\Application($app_env);
$app['env'] = $app_env;

try {
    $app->register(
        new DoctrineServiceProvider(),
        [
            'db.options' => \App\Config\ConfigProvider::getDatabaseConfig($app_env)
        ]
    );

    if (!is_dir(\App\Config\ConfigProvider::getUploadDir($app_env))) {
        mkdir(\App\Config\ConfigProvider::getUploadDir($app_env));
    }

    $app->register(
        new Silex\Provider\MonologServiceProvider(),
        [
            'monolog.logfile' => \App\Config\ConfigProvider::getLogFile($app_env),
        ]
    );

} catch (Exception $e) {
    // Exception will be caught in application
}

return $app;
