<?php

namespace ApiUtilsTest;

use PHPUnit\Framework\TestCase;
use App\ApiUtils as ApiUtils;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Config\ConfigProvider as ConfigProvider;

require __DIR__ . '/../test_bootstrap.php';

class ApiUtilsTest extends TestCase
{
    public function testCheckRequestFiles()
    {
        $this->assertFalse(ApiUtils::checkRequestFile(null));
        $this->assertFalse(ApiUtils::checkRequestFile(true));

        $app = require __DIR__ . '/../test_bootstrap.php';

        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($app['env']) . "apiTest.htm");

        $fileUploadPath = ConfigProvider::getUploadDir($app['env']) . "apiTest.htm";
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        $this->assertTrue(ApiUtils::checkRequestFile($fileUpload));
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
