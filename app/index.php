<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Debug\Debug;

Debug::enable();

$app = new App\Application('dev');
$app->run();
