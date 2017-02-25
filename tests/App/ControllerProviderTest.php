<?php

namespace ControllerProviderTest;

use Silex\WebTestCase;
use App\ControllerProvider as ControllerProvider;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Config\ConfigProvider as ConfigProvider;

class ControllerProviderTest extends WebTestCase
{
    protected $app;


    protected function setUp()
    {
        parent::setUp();
        date_default_timezone_set('America/New_York');
        self::deleteDirectory(ConfigProvider::getUploadDir($this->app['env']));
        mkdir(ConfigProvider::getUploadDir($this->app['env']));
        mkdir(ConfigProvider::getUploadDir($this->app['env']) . 'create');
    }


    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }


    public function createApplication()
    {
        $this->app = require __DIR__ . '/../test_bootstrap.php';
        return $this->app;
    }


    public function testConnect()
    {
        $app = require __DIR__ . '/../test_bootstrap.php';
        $controllerProvider = new ControllerProvider();
        $controllerCollection = $controllerProvider->connect($app);
        $this->assertNotNull($controllerCollection);
        $this->assertInstanceOf('Silex\ControllerCollection',
            $controllerCollection);
    }


    public function test404()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_NOT_FOUND);
    }


    public function testGetFiles()
    {
        $controllerProvider = new ControllerProvider();
        $response = $controllerProvider->getFiles($this->app);
        $content = $response->getContent();
        $this->assertJson($content);
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_OK);
    }


    public function testUploadNewFile()
    {
        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla.htm");
        $fileUploadPath = ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla.htm";
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        $client = $this->createClient();
        $client->request('POST', '/files', [], ['upload_file' => $fileUpload]);
        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertJson($content);

        $this->assertTrue(json_decode($content, true)['id'] > 0);
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_CREATED);
    }


    public function testGetOneFile()
    {
        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla1.htm");
        $fileUploadPath = ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla1.htm";
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        $clientCreate = $this->createClient();
        $clientCreate->request('POST', '/files', [],
            ['upload_file' => $fileUpload]);
        $response = $clientCreate->getResponse();
        $content = $response->getContent();
        $newFileId = json_decode($content, true)['id'];

        $clientGet = $this->createClient();
        $clientGet->request('GET', '/files/' . $newFileId);
        $response = $clientGet->getResponse();
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_OK);
    }


    public function testGetOneFileMeta()
    {
        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla1.htm");
        $fileUploadPath = ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla1.htm";
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        $clientCreate = $this->createClient();
        $clientCreate->request('POST', '/files', [],
            ['upload_file' => $fileUpload]);
        $response = $clientCreate->getResponse();
        $content = $response->getContent();
        $newFileId = json_decode($content, true)['id'];

        $clientGet = $this->createClient();
        $clientGet->request('GET', '/files/' . $newFileId . '/meta');
        $response = $clientGet->getResponse();
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_OK);
    }


    public function testGetDeleteFile()
    {
        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla2.htm");
        $fileUploadPath = ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla2.htm";
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        $clientCreate = $this->createClient();
        $clientCreate->request('POST', '/files', [],
            ['upload_file' => $fileUpload]);
        $response = $clientCreate->getResponse();
        $content = $response->getContent();
        $newFileId = json_decode($content, true)['id'];

        $clientDelete = $this->createClient();
        $clientDelete->request('DELETE', '/files/' . $newFileId);
        $response = $clientDelete->getResponse();
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_NO_CONTENT);

        $clientDeleteAgain = $this->createClient();
        $clientDeleteAgain->request('DELETE', '/files/' . $newFileId);
        $response = $clientDeleteAgain->getResponse();
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_NOT_FOUND);
    }


    public function testUpdateFileName()
    {
        // Create file
        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla3.htm");
        $fileUploadPath = ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla3.htm";
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        //Upload file
        $clientCreate = $this->createClient();
        $clientCreate->request('POST', '/files', [],
            ['upload_file' => $fileUpload]);
        $response = $clientCreate->getResponse();
        $content = $response->getContent();
        $newFileId = json_decode($content, true)['id'];

        //Update file name
        $newFileName = 'new_file_name.txt';
        $clientUpdateName = $this->createClient();
        $clientUpdateName->request(
            'PUT',
            '/files/' . $newFileId . '/name',
            [],
            [],
            [],
            json_encode(['name' => $newFileName])
        );
        $response = $clientUpdateName->getResponse();
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_OK);

        //Update file name with wrong Id
        $clientUpdateWrongId = $this->createClient();
        $clientUpdateWrongId->request(
            'PUT',
            '/files/' . 1334 . '/name',
            [],
            [],
            [],
            json_encode(['name' => $newFileName])
        );
        $response = $clientUpdateWrongId->getResponse();
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_NOT_FOUND);

        // Create file
        $nameAlreadyExist = 'alreadyExist.txt';
        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->app['env']) . "create/" . $nameAlreadyExist);
        $fileUploadPath = ConfigProvider::getUploadDir($this->app['env']) . "create/" . $nameAlreadyExist;
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        //Upload file
        $clientCreate = $this->createClient();
        $clientCreate->request('POST', '/files', [],
            ['upload_file' => $fileUpload]);

        //Update file name with wrong name
        $clientUpdateWrongName = $this->createClient();
        $clientUpdateWrongName->request(
            'PUT',
            '/files/' . $newFileId . '/name',
            [],
            [],
            [],
            json_encode(['name' => $nameAlreadyExist])
        );
        $response = $clientUpdateWrongName->getResponse();
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_BAD_REQUEST);
    }


    public function testUpdateFile()
    {
        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla2.htm");
        $fileUploadPath = ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla2.htm";
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        $clientCreate = $this->createClient();
        $clientCreate->request('POST', '/files', [],
            ['upload_file' => $fileUpload]);
        $response = $clientCreate->getResponse();
        $content = $response->getContent();
        $newFileId = json_decode($content, true)['id'];

        copy(__DIR__ . '/Data/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->app['env']) . "create/Xsolla2.htm");
        $fileUpload = new UploadedFile(
            $fileUploadPath,
            $fileUploadPath,
            null,
            null,
            null,
            true
        );

        $clientCreate = $this->createClient();
        $clientCreate->request('POST', '/files/' . $newFileId, [],
            ['upload_file' => $fileUpload]);
        $response = $clientCreate->getResponse();
        $this->assertTrue($response->getStatusCode() == HTTPResponse::HTTP_OK);
    }

    public function testUnknownError()
    {
        $clientGet = $this->createClient();
        $clientGet->request('GET', '/files/ololo/meta');
        $response = $clientGet->getResponse();
        $this->assertTrue($response->getStatusCode() != HTTPResponse::HTTP_OK);
    }
}
