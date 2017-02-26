<?php

namespace DataManagerTest;

use PHPUnit\Framework\TestCase;
use App\Data\DataManager as DataManager;

require __DIR__ . '/../../test_bootstrap.php';

/**
 * Class DataManagerTest
 */
class DataManagerTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        $dataProvider = self::getDataProvider();
        $dropTableMethod = self::getMethod('dropTable');
        $dropTableMethod->invokeArgs($dataProvider, []);
    }


    /**
     * @return DataManager
     */
    private function getDataProvider()
    {
        $app = require __DIR__ . '/../../test_bootstrap.php';
        $db = $app['db'];
        return new DataManager($db);
    }


    public function testConstruct()
    {
        $this->assertNotNull(self::getDataProvider());
    }


    public function testDropTable()
    {
        $dataProvider = self::getDataProvider();
        $dropTableMethod = self::getMethod('dropTable');
        $result = $dropTableMethod->invokeArgs($dataProvider, []);
        $this->assertTrue($result);
    }


    /**
     * @expectedException \Exception
     */
    public function testDoubleDropTable()
    {
        $dataProvider = self::getDataProvider();
        $dropTableMethod = self::getMethod('dropTable');
        $resultFirst = $dropTableMethod->invokeArgs($dataProvider, []);
        $resultSecond = $dropTableMethod->invokeArgs($dataProvider, []);
        $this->assertTrue($resultFirst);
        $this->expectException($resultSecond);
    }


    public function testAddNewFile()
    {
        $dataProvider = self::getDataProvider();
        $id = $dataProvider->addNewFile('originalName.txt', 'fileName.txt');
        $this->assertTrue($id > 0);
    }


    public function testGetFilesList()
    {
        $dataProvider = self::getDataProvider();
        $list = $dataProvider->getFilesList();
        $this->assertTrue(is_array($list));
    }


    public function testGetFilesListAfterAdding()
    {
        $originalName = 'originalName.txt';
        $dataProvider = self::getDataProvider();
        $id = $dataProvider->addNewFile($originalName, 'fileName.txt');
        $list = $dataProvider->getFilesList();

        foreach ($list as $file) {
            if ($file['id'] == $id) {
                $this->assertTrue($file['name'] == $originalName);
            }
        }
    }


    public function testDeleteFile()
    {
        $originalName = 'fileToDelete.txt';
        $dataProvider = self::getDataProvider();
        $id = $dataProvider->addNewFile($originalName, 'fileName.txt');
        $list = $dataProvider->getFilesList();

        foreach ($list as $file) {
            if ($file['id'] == $id) {
                $this->assertTrue($file['name'] == $originalName);
            }
        }

        $dataProvider->deleteFile($id);
        $list = $dataProvider->getFilesList();
        $wasDeleted = true;
        foreach ($list as $file) {
            if ($file['id'] == $id) {
                $wasDeleted = true;
            }
        }

        $this->assertTrue($wasDeleted);
    }


    public function testGetOneFile()
    {
        $originalName = 'getOneFile.txt';
        $fileName = 'getOneFileName.txt';
        $dataProvider = self::getDataProvider();
        $id = $dataProvider->addNewFile($originalName, $fileName);
        $getOne = $dataProvider->getOneFile($id);
        $this->assertTrue(
            $getOne['id'] == $id
            && $getOne['original_name'] == $originalName
            && $getOne['file_name'] == $fileName
        );
    }


    public function testUpdateFile()
    {
        $oldOriginalName = 'oldOriginalName.txt';
        $oldFileName = 'oldFileName.txt';
        $dataProvider = self::getDataProvider();
        $id = $dataProvider->addNewFile($oldOriginalName, $oldFileName);

        // Update only original name
        $newOriginalName = 'newOriginalName.txt';
        $dataProvider->updateFile($id, $newOriginalName);

        $getFile = $dataProvider->getOneFile($id);
        $this->assertTrue(
            $getFile['id'] == $id
            && $getFile['original_name'] == $newOriginalName
            && $getFile['file_name'] == $oldFileName
        );

        // Update original name and file name
        $newOriginalName2 = 'newOriginalName2.txt';
        $newFileName = 'newFileName.txt';
        $dataProvider->updateFile($id, $newOriginalName2, $newFileName);

        $getFile = $dataProvider->getOneFile($id);
        $this->assertTrue(
            $getFile['id'] == $id
            && $getFile['original_name'] == $newOriginalName2
            && $getFile['file_name'] == $newFileName
        );

        // Update with non exist Id
        $result = $dataProvider->updateFile(
            99999,
            $newOriginalName2,
            $newFileName
        );

        $this->assertTrue($result == -1);

        // Update file with already exist name
        $alreadyExistName = 'alreadyExist.txt';
        $dataProvider = self::getDataProvider();
        $dataProvider->addNewFile($alreadyExistName, $alreadyExistName);
        $result = $dataProvider->updateFile($id, $alreadyExistName);
        $this->assertTrue($result == 0);
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
}
