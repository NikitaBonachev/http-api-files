<?php

namespace App\Data;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;
use App\Config\ConfigProvider;

class DataManager
{
    public function __construct(SilexApplication $app)
    {
        $app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => ConfigProvider::getDB()
        ));

        $app->register(new \Nutwerk\Provider\DoctrineORMServiceProvider(), array(
            'db.orm.proxies_dir'           => __DIR__.'/../cache/doctrine/proxy',
            'db.orm.proxies_namespace'     => 'DoctrineProxy',
            'db.orm.cache'                 =>
                !$app['debug'] && extension_loaded('apc') ? new ApcCache() : new ArrayCache(),
            'db.orm.auto_generate_proxies' => true,
            'db.orm.entities'              => array(array(
                'type'      => 'annotation',       // как определяем поля в Entity
                'path'      => __DIR__,   // Путь, где храним классы
                'namespace' => 'TestApp\Entity', // Пространство имен
            )),
        ));

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = ConfigProvider::getDB();

        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);


        $conn = ConfigProvider::getDB();

        $config = new Configuration;
        $driverImpl = $config->newDefaultAnnotationDriver(__DIR__.'/Entities');
        $config->setMetadataDriverImpl($driverImpl);
        $config->setProxyDir('/path/to/myproject/lib/MyProject/Proxies');
        $config->setProxyNamespace('App\Data\Entities');

        $this->entityManager = EntityManager::create($conn, $config);
    }
}
