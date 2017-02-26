<?php

namespace App;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Config\ConfigProvider as ConfigProvider;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../test_bootstrap.php';

/**
 * Class ApiUtilsTest
 */
class ApiUtilsTest extends TestCase
{
    public function testCheckRequestFiles()
    {
        $app = require __DIR__ . '/../test_bootstrap.php';

        copy(
            __DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($app['env']) . "apiTest.htm"
        );

        $fileUploadPath = ConfigProvider::getUploadDir($app['env']) . "apiTest.htm";

        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        $testRequest = new Request(
            [],
            [],
            [],
            [],
            ['upload_file' => $fileUpload]
        );

        $result = ApiUtils::checkRequestFile($testRequest);
        $this->assertTrue($result instanceof UploadedFile);

        $testRequestEmpty = new Request();
        $this->assertFalse(ApiUtils::checkRequestFile($testRequestEmpty));
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
