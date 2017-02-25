<?php

namespace DataManagerTest;

use App\Config\ConfigProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PHPUnit\Framework\TestCase;
use App\Data\FilesStorage as FilesStorage;
use App\Data\DataManager as DataManager;

class FilesStorageTest extends TestCase
{
    private function getApp()
    {
        return require __DIR__ . '/../../test_bootstrap.php';
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


    private function createUploadFile($originalName)
    {
        $content = "some content here";
        $newFileOriginalName = $originalName;
        $newFilePath = ConfigProvider::getUploadDir($this->getApp()['env']) . "create/" . $newFileOriginalName;
        $fp = fopen($newFilePath, "wb");
        fwrite($fp, $content);
        fclose($fp);
        return new UploadedFile(
            $newFilePath,
            $newFileOriginalName,
            null,
            null,
            null,
            true
        );
    }


    protected function setUp()
    {
        parent::setUp();
        date_default_timezone_set('America/New_York');
        self::deleteDirectory(ConfigProvider::getUploadDir($this->getApp()['env']));
        if (!is_dir(ConfigProvider::getUploadDir($this->getApp()['env']))) {
            mkdir(ConfigProvider::getUploadDir($this->getApp()['env']));
        }
        mkdir(ConfigProvider::getUploadDir($this->getApp()['env']) . 'create');
    }


    protected function tearDown()
    {
        parent::tearDown();
        self::deleteDirectory(ConfigProvider::getUploadDir($this->getApp()['env']));
        $app = $this->getApp();
        $db = $app['db'];
        $dataProvider = new DataManager($db);
        $dropTableMethod = self::getMethod('dropTable');
        $dropTableMethod->invokeArgs($dataProvider, []);
    }


    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('App\Data\DataManager');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }


    public function testCreateFile()
    {
        $newFile = self::createUploadFile('new.txt');
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $this->assertTrue($id > 0);
    }


    public function testUpdateFile()
    {
        $newFile = self::createUploadFile('newFile.txt');
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $newFileUpdate = self::createUploadFile('newUpdate.txt');
        $result = FilesStorage::updateFile($newFileUpdate, $id, self::getApp());
        $this->assertTrue($result > 0);
    }


    public function testUpdateFileName()
    {
        $oldName = 'oldName.txt';
        $newName = 'newName.txt';
        $newFile = self::createUploadFile($oldName);
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $resultUpdate = FilesStorage::updateFileName($id, $newName,
            $this->getApp());

        $testUpdate = FilesStorage::getFile($id, $this->getApp());

        $this->assertTrue($resultUpdate > 0);
        $this->assertTrue($newName == $testUpdate['originalName']);
    }


    public function testGetFile()
    {
        $newFileName = 'newFileGet.txt';
        $newFile = self::createUploadFile($newFileName);
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $getFile = FilesStorage::getFile($id, $this->getApp());
        $this->assertTrue(
            $newFileName == $getFile['originalName']
        );
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testGetWrongFile()
    {
        $this->expectException(FilesStorage::getFile(999999, $this->getApp()));
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testDeleteFile()
    {
        $newFileName = 'newFileToDelete.txt';
        $newFile = self::createUploadFile($newFileName);
        $id = FilesStorage::createFile($newFile, $this->getApp());

        $this->assertTrue(FilesStorage::deleteFile($id, $this->getApp()) > 0);
        $this->expectException(FilesStorage::getFile($id, $this->getApp()));
    }


    public function testGetFileMeta()
    {
        copy(__DIR__ . '/TestFiles/Xsolla.htm',
            ConfigProvider::getUploadDir($this->getApp()['env']) . "create/Xsolla.htm");
        $newFile = new UploadedFile(
            ConfigProvider::getUploadDir($this->getApp()['env']) . "create/Xsolla.htm",
            ConfigProvider::getUploadDir($this->getApp()['env']) . "create/Xsolla.htm",
            null,
            null,
            null,
            true
        );
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $this->assertTrue(is_array(FilesStorage::getFileMeta($id,
            $this->getApp())));
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testWrongGetFileMeta()
    {
        $this->assertTrue(is_array(FilesStorage::getFileMeta(777777,
            $this->getApp())));
    }
}
