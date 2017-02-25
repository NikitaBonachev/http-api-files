<?php

namespace ApiUtilsTest;

use PHPUnit\Framework\TestCase;
use App\ApiUtils as ApiUtils;

require __DIR__ . '/../test_bootstrap.php';

class ApiUtilsTest extends TestCase
{
    public function testCheckRequestFiles()
    {
        $this->assertFalse(ApiUtils::checkRequestFile(null));
        $this->assertTrue(ApiUtils::checkRequestFile(true));
    }

    public function testCheckRequestId()
    {
        $this->assertTrue(ApiUtils::checkRequestId(1) === 1);
        $this->assertTrue(ApiUtils::checkRequestId('1') === 1);
        $this->assertFalse(ApiUtils::checkRequestId('a'));
        $this->assertFalse(ApiUtils::checkRequestId(0));
        $this->assertFalse(ApiUtils::checkRequestId('0'));
        $this->assertFalse(ApiUtils::checkRequestId('.0'));
        $this->assertFalse(ApiUtils::checkRequestId('1.0'));
    }

    public function testCheckRequestString()
    {
        $this->assertTrue(ApiUtils::checkRequestString('adad'));
        $this->assertTrue(ApiUtils::checkRequestString('123'));
        $this->assertFalse(ApiUtils::checkRequestString(''));
        $this->assertFalse(ApiUtils::checkRequestString(null));
        $this->assertFalse(ApiUtils::checkRequestString(123));
    }
}
