<?php

namespace App;

use Silex\WebTestCase;
use Silex\Application as SilexApplication;

/**
 * Class ApplicationTest
 */
class ApplicationTest extends WebTestCase
{
    /**
     * Creates the application.
     *
     * @return object
     */
    public function createApplication()
    {
        return require __DIR__ . '/../test_bootstrap.php';
    }

    public function testCreatingApplication()
    {
        $app = new Application('dev');
        $this->assertNotNull($app);
    }

}
