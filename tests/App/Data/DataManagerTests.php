<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__.'/../../../src/App/Data/DataManager.php';
require_once __DIR__.'/../../../vendor/autoload.php';

class DataManagerTests extends TestCase {

    public function testClassCreated()
    {
        $dataProvider = new \App\Data\DataManager();
        $this->assertNotNull($dataProvider);
    }

}
