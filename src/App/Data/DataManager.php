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
     * @param string $originalName
     * @param string $fileName
     * @param string $fileExtension
     *
     * @return bool
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
     * @return \stdClass|null|array
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
     *
     * @param int $id
     *
     * @return \stdClass|null|array
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
     *
     * @param int $id
     * @param string $newOriginalName
     * @param string $newFileName
     *
     * @return bool
     */
    public function updateFile($id, $newOriginalName, $newFileName = null)
    {
        $filesTable = $this->filesTableName;
        $filesQueryText = "UPDATE $filesTable 
            SET original_name = '$newOriginalName'";

        if ($newFileName) {
            $filesQueryText .= ",file_name = '$newFileName'";
        }

        $filesQueryText .= "WHERE id = $id;";
        $db = $this->db;

        return $db->executeUpdate($filesQueryText);
    }


    /**
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
