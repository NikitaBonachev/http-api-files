<?php

namespace App;

use Silex\WebTestCase;
use Silex\Application as SilexApplication;
use Silex\Provider\WebProfilerServiceProvider;

class ApplicationTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../test_bootstrap.php';
    }

    public function testCreatingApplication()
    {
        $app = new Application('dev');
        $this->assertNotNull($app);
    }

}
