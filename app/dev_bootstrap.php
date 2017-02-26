<?php

use Silex\Application as SilexApplication;
use Silex\Provider\WebProfilerServiceProvider;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Silex\Provider\DoctrineServiceProvider;

$loader = require __DIR__ . '/../vendor/autoload.php';

$app_env = 'dev';
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

    $app['security.firewalls'] = [
        'files' => [
            'pattern' => '^/files',
            'http' => true,
            'users' => [
                'admin' => [
                    'ROLE_USER',
                    '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'
                ]
            ]
        ]
    ];

    $app['security.access_rules'] = [
        ['^/files', 'ROLE_USER']
    ];

    $app->register(
        new Silex\Provider\SecurityServiceProvider(),
        [
        'security.firewalls' => $app['security.firewalls']
        ]
    );

} catch (Exception $e) {
    // Exception will be caught in application
}

return [$app, $loader];
