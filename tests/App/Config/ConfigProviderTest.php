<?php

namespace App\Config;

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../test_bootstrap.php';

/**
 * Class ConfigProviderTest
 */
class ConfigProviderTest extends TestCase
{
    public function testFile()
    {
        $fileMethod = self::getMethod('file');
        $configProvider = new ConfigProvider();
        $result = $fileMethod->invokeArgs($configProvider, ['databases']);
        $this->assertNotNull($result);
    }


    /**
     * @expectedException \Exception
     */
    public function testWrongPath()
    {
        $fileMethod = self::getMethod('file');
        $configProvider = new ConfigProvider();
        $this->expectException($fileMethod->invokeArgs($configProvider,
            ['super_wrong_db', 'wrong_path']));
    }


    /**
     * @expectedException \Exception
     */
    public function testWrongEnv()
    {
        $env = 'wrong_env';
        $this->expectException(ConfigProvider::getDatabaseConfig($env));
    }


    /**
     * @expectedException \Exception
     */
    public function testWrongFile()
    {
        $fileMethod = self::getMethod('file');
        $configProvider = new ConfigProvider();
        $this->expectException($fileMethod->invokeArgs($configProvider,
            ['super_wrong_db']));
    }


    public function testGetTestDB()
    {
        $env = 'test';
        $databaseConfig = ConfigProvider::getDatabaseConfig($env);
        $this->assertArrayHasKey('dbname', $databaseConfig);
        $this->assertArrayHasKey('user', $databaseConfig);
        $this->assertArrayHasKey('password', $databaseConfig);
        $this->assertArrayHasKey('host', $databaseConfig);
        $this->assertArrayHasKey('driver', $databaseConfig);
    }


    /**
     * @expectedException \Exception
     */
    public function testWrongUploadDir()
    {
        $env = 'wrong_env';
        $this->expectException(ConfigProvider::getUploadDir($env));
    }


    public function testGetUploadDir()
    {
        $env = 'test';
        $this->assertTrue(is_string(ConfigProvider::getUploadDir($env)));
    }


    /**
     * @expectedException \Exception
     */
    public function testGetLog()
    {
        $env = 'test';
        $this->assertTrue(is_string(ConfigProvider::getLogFile($env)));
        $this->expectException(ConfigProvider::getLogFile('wrong'));
    }


    /**
     * Get private method
     *
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('App\Config\ConfigProvider');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
