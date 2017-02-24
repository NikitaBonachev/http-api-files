<?php

namespace ConfigProviderTest;

use PHPUnit\Framework\TestCase;
use App\Config\ConfigProvider;
require __DIR__ . '/../../test_bootstrap.php';

class ConfigProviderTest extends TestCase
{
    public function testFile()
    {
        $fileMethod = self::getMethod('file');
        $configProvider = new ConfigProvider();
        $result = $fileMethod->invokeArgs($configProvider, ['database']);
        $this->assertNotNull($result);
    }

    /**
     * @expectedException \Exception
     */
    public function testWrongPath()
    {
        $fileMethod = self::getMethod('file');
        $configProvider = new ConfigProvider();
        $this->expectException($fileMethod->invokeArgs($configProvider, ['super_wrong_db', 'wrong_path']));
    }

    /**
     * @expectedException \Exception
     */
    public function testWrongFile()
    {
        $fileMethod = self::getMethod('file');
        $configProvider = new ConfigProvider();
        $this->expectException($fileMethod->invokeArgs($configProvider, ['super_wrong_db']));
    }

    public function testGetDB()
    {
        $this->assertArrayHasKey('dbname', ConfigProvider::getDB());
        $this->assertArrayHasKey('user', ConfigProvider::getDB());
        $this->assertArrayHasKey('password', ConfigProvider::getDB());
        $this->assertArrayHasKey('host', ConfigProvider::getDB());
        $this->assertArrayHasKey('driver', ConfigProvider::getDB());
    }

    protected static function getMethod($name) {
        $class = new \ReflectionClass('App\Config\ConfigProvider');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
