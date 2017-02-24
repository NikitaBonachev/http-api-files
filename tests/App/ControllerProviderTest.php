<?php

namespace App;

use PHPUnit\Framework\TestCase;
use Silex\Application as App;

class ControllerProviderTest extends TestCase
{
    public function testConnect()
    {
        $app = require __DIR__.'/../test_bootstrap.php';
        $controllerProvider = new ControllerProvider();
        $controllerCollection = $controllerProvider->connect($app);
        $this->assertNotNull($controllerCollection);
        $this->assertInstanceOf('Silex\ControllerCollection', $controllerCollection);
    }
}
