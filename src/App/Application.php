<?php

namespace App;

use Silex\Application as SilexApplication;
use Silex\Provider\WebProfilerServiceProvider;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;

class Application extends SilexApplication
{
    public function __construct($env)
    {
        parent::__construct();
        $app = $this;
        $app->mount('', new ControllerProvider());

        $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver'   => 'pdo_sqlite',
                'path'     => __DIR__.'/app.db',
            ),
        ));
    }
}
