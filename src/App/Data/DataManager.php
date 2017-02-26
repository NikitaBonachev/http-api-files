<?php

namespace App\Data;

use Doctrine\DBAL\Connection;
use App\Data\Entities;

class DataManager
{
    /**
     * @var Connection
     */
    private $db;


    /**
     * @var string
     */
    private $filesTableName;


    /**
     * Drop table of files. Use it only for tests!
     *
     * @return bool
     * @throws \Exception
     */
    private function dropTable()
    {
        $filesTable = $this->filesTableName;
        $query = "DROP TABLE $filesTable;";
        $db = $this->db;
        $selCreateQuery = $db->prepare($query);
        try {
            return $selCreateQuery->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * Create table of files
     *
     * @return bool
     */
    private function createTableFilesList()
    {
        $filesTable = $this->filesTableName;
        $query = "CREATE TABLE `$filesTable` (
                id INT(11) NOT NULL AUTO_INCREMENT,
                original_name CHAR(250) NOT NULL,
                file_name CHAR(250) NOT NULL,
                PRIMARY KEY(id)
            )
        ;";

        $db = $this->db;
        $selCreateQuery = $db->prepare($query);

        return $selCreateQuery->execute();
    }


    /**
     * Check if file with this ID exist in database
     *
     * @param $id
     * @return bool
     */
    private function isIdExist($id)
    {
        $db = $this->db;
        $filesTable = $this->filesTableName;
        $checkFileExist = "SELECT id FROM $filesTable WHERE id = $id";
        $queryCheckExist = $db->prepare($checkFileExist);
        $queryCheckExist->execute();
        return !!$queryCheckExist->fetch();
    }


    /**
     * Return ID of file by its name
     *
     * @param $name
     * @return bool
     */
    public function getFileByName($name)
    {
        $db = $this->db;
        $filesTable = $this->filesTableName;
        $lowerCaseOriginalName = strtolower($name);
        $checkFileNameQueryText = "SELECT id FROM $filesTable 
                WHERE LOWER(original_name) = '$lowerCaseOriginalName'";
        $queryCheckName = $db->prepare($checkFileNameQueryText);
        $queryCheckName->execute();
        return $queryCheckName->fetch()['id'];
    }


    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->filesTableName = 'files';
        if (!$db->getSchemaManager()->tablesExist(['files'])) {
            self::createTableFilesList();
        }
    }


    /**
     * Create new file in database. Return ID
     *
     * @param string $originalName
     * @param string $fileName
     *
     * @return integer
     */
    public function addNewFile($originalName, $fileName)
    {
        $filesTable = $this->filesTableName;
        $createFileQueryText = "INSERT INTO $filesTable 
        (original_name, file_name) VALUES 
        ('$originalName', '$fileName');";

        $db = $this->db;

        $createFileQuery = $db->prepare($createFileQueryText);
        $createFileQuery->execute();

        return $db->lastInsertId();
    }


    /**
     * Return all files in database
     *
     * @return array
     */
    public function getFilesList()
    {
        $arFiles = [];

        $filesTable = $this->filesTableName;
        $filesQueryText = "SELECT id, original_name as name from $filesTable;";

        $db = $this->db;
        $query = $db->prepare($filesQueryText);

        if ($query->execute()) {
            $arFiles = $query->fetchAll();
        }

        return $arFiles;
    }


    /**
     *  Return info about file
     *
     * @param int $id
     *
     * @return array
     */
    public function getOneFile($id)
    {
        $file = ['id' => 0]; // empty file

        $filesTable = $this->filesTableName;
        $filesQueryText = "SELECT * from $filesTable WHERE id = $id;";

        $db = $this->db;
        $query = $db->prepare($filesQueryText);

        if ($query->execute()) {
            $file = $query->fetch();
        }

        return $file;
    }


    /**
     *  Update info about file in database
     *
     * @param int $id
     * @param string $newOriginalName
     * @param string $newFileName
     *
     * @return integer
     */
    public function updateFile(
        $id,
        $newOriginalName = null,
        $newFileName = null
    ) {
        $filesTable = $this->filesTableName;
        $db = $this->db;

        if (!self::isIdExist($id)) {
            return -1;
        }

        // Check if file with the same name exists
        $foundIdWithSameName = 0;
        if ($newOriginalName) {
            $foundIdWithSameName = self::getFileByName($newOriginalName);
        }

        if ($foundIdWithSameName != $id && $foundIdWithSameName > 0) {
            return 0;
        }

        $filesQueryText = "UPDATE $filesTable ";

        if ($newOriginalName) {
            $filesQueryText .= "SET original_name = '$newOriginalName' ";
        }

        if ($newFileName) {
            if ($newOriginalName) {
                $filesQueryText .= ",";
            }
            $filesQueryText .= "file_name = '$newFileName' ";
        }

        $filesQueryText .= "WHERE id = $id;";
        $db->executeUpdate($filesQueryText);

        return 1;
    }


    /**
     * Remove file from database
     *
     * @param int $id
     *
     * @return int
     */
    public function deleteFile($id)
    {
        $filesTable = $this->filesTableName;
        $db = $this->db;
        $query = "DELETE FROM $filesTable WHERE id = $id;";
        return $db->executeUpdate($query);
    }
}
