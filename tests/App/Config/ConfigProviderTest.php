<?php

namespace ConfigProviderTest;

use PHPUnit\Framework\TestCase;
use App\Config\ConfigProvider;
require __DIR__ . '/../../test_bootstrap.php';

class ConfigProviderTest extends TestCase
{
    public function testDBConfig()
    {
        $this->assertArrayHasKey('dbname', ConfigProvider::getDB());
        $this->assertArrayHasKey('user', ConfigProvider::getDB());
        $this->assertArrayHasKey('password', ConfigProvider::getDB());
        $this->assertArrayHasKey('host', ConfigProvider::getDB());
        $this->assertArrayHasKey('driver', ConfigProvider::getDB());
    }

}
