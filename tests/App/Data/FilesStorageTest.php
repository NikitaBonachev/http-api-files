<?php

namespace App\Data;

use App\Config\ConfigProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PHPUnit\Framework\TestCase;

/**
 * Class FilesStorageTest
 */
class FilesStorageTest extends TestCase
{
    /**
     * @return \Silex\Application
     */
    private function getApp()
    {
        return require __DIR__ . '/../../test_bootstrap.php';
    }


    /**
     * Delete upload_test directory
     *
     * @param $dir
     * @return bool
     */
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


    /**
     * Return upload dir
     *
     * @return array
     */
    private function uploadDir()
    {
        return ConfigProvider::getUploadDir($this->getApp()['env']);
    }


    /**
     * Create UploadedFile file for tests
     *
     * @param $originalName
     * @return UploadedFile
     */
    private function createUploadFile($originalName)
    {
        $content = "some content here";
        $newFileOriginalName = $originalName;

        $newFilePath = self::uploadDir();
        $newFilePath .= "create/" . $newFileOriginalName;

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
        date_default_timezone_set('Asia/Yekaterinburg');
        self::deleteDirectory(self::uploadDir());

        if (!is_dir(self::uploadDir())) {
            mkdir(self::uploadDir());
        }

        mkdir(self::uploadDir() . 'create');
    }


    protected function tearDown()
    {
        parent::tearDown();
        self::deleteDirectory(self::uploadDir());
        $app = $this->getApp();
        $db = $app['db'];
        $dataProvider = new DataManager($db);
        $dropTableMethod = self::getMethod('dropTable');
        $dropTableMethod->invokeArgs($dataProvider, []);
    }


    /**
     * Get private method for tests
     *
     * @param $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('App\Data\DataManager');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }


    public function testCreateFile()
    {
        // Try to create file
        $newFile = self::createUploadFile('new.txt');
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $this->assertTrue($id > 0);

        // Try to create file with same name
        $newFile = self::createUploadFile('new.txt');
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $this->assertFalse($id > 0);
    }


    public function testUpdateFile()
    {
        $newFile = self::createUploadFile('newFile.txt');
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $newFileUpdate = self::createUploadFile('newUpdate.txt');

        $result = FilesStorage::updateFile(
            $newFileUpdate,
            $id,
            $this->getApp()
        );

        $this->assertTrue($result > 0);
    }


    public function testUpdateFileName()
    {
        $oldName = 'oldName.txt';
        $newName = 'newName.txt';
        $newFile = self::createUploadFile($oldName);
        $id = FilesStorage::createFile($newFile, $this->getApp());

        $resultUpdate = FilesStorage::updateFileName(
            $id,
            $newName,
            $this->getApp()
        );

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

        $this->assertTrue(
            FilesStorage::deleteFile($id, $this->getApp()) > 0
        );
        $this->expectException(FilesStorage::getFile($id, $this->getApp()));
    }


    public function testGetFileMeta()
    {
        $newFile = self::createUploadFile('Xsolla.htm');
        $id = FilesStorage::createFile($newFile, $this->getApp());
        $this->assertTrue(
            is_array(FilesStorage::getFileMeta($id, $this->getApp()))
        );
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testWrongGetFileMeta()
    {
        $this->assertTrue(
            is_array(FilesStorage::getFileMeta(777777, $this->getApp()))
        );
    }
}
