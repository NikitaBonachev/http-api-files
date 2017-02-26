<?php

namespace App;

use Silex\Application as SilexApplication;
use Silex\Provider\WebProfilerServiceProvider;

/**
 * Class Application
 * @package App
 */
class Application extends SilexApplication
{
    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $env
     */
    public function __construct($env)
    {
        parent::__construct();
        $app = $this;
        $app->mount('', new ControllerProvider());
    }
}
