<?php

require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../../src/App/Data/DataManager.php';
require_once __DIR__.'/../../../src/App/Application.php';

use PHPUnit\Framework\TestCase;

class DataManagerTests extends TestCase {

    public function testClassCreated()
    {

        $app = new App\Application('dev');
        $app->register(
            new \Silex\Provider\DoctrineServiceProvider(),
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


        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $this->assertNotNull($dataProvider);
    }

}
