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
    }
}
