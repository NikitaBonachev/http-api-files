<?php
namespace App;

use Silex\Application as SilexApplication;
use Silex\Provider\WebProfilerServiceProvider;

class Application extends SilexApplication
{
    public function __construct($env)
    {
        parent::__construct();
        $app = $this;
        $app->mount('', new ControllerProvider());

        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = array(
            'dbname' => 'xsolla_test',
            'user' => 'api',
            'password' => '21333',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $conn->executeQuery("SHOW TABLES");

        var_dump($conn->executeQuery("SHOW TABLES")->fetchAll());

    }
}
