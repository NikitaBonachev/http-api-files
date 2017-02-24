<?php

use Symfony\Component\Debug\Debug;

list($app, $loader) = require __DIR__ . '/../app/dev_bootstrap.php';

if ($app['env'] === 'test') {
    $loader->add('App', __DIR__ . '/../tests');
    return $app;
} else if($app['env'] === 'dev') {
    Debug::enable();
    $app->run();
} else {
    $app->run();
}
