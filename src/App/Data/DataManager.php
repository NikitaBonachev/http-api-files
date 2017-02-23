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
     * @param string $name
     * @param string $fileName
     *
     * @return bool
     */
    public function addNewFile($name, $fileName) {

        $filesTable = $this->filesTableName;
        $createFileQueryText = 'INSERT INTO ' . $filesTable . ' 
        (name, file_name) VALUES ("' . $name . '", "' . $fileName .'");';

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
        $filesQueryText = 'SELECT ID, name from ' . $filesTable . ';';

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
        $file = null;

        $filesTable = $this->filesTableName;
        $filesQueryText = 'SELECT * from ' . $filesTable . ' WHERE id = ' . $id . ';';

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
     * @param string $newName
     *
     * @return bool
     */
    public function updateFile($id, $newName)
    {
        $filesTable = $this->filesTableName;
        $filesQueryText = 'UPDATE ' . $filesTable . ' SET name="' . $newName . '" WHERE ID = ' . $id . ';';
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
        $file = null;

        $filesTable = $this->filesTableName;
        $db = $this->db;
        $query = 'DELETE FROM ' . $filesTable . ' WHERE ID = ' . $id . ';';

        return $db->executeUpdate($query);
    }

    /**
     * @return bool
     */
    private function createTableFilesList() {
        $filesTable = $this->filesTableName;
        $query = 'CREATE TABLE ' . $filesTable . '(
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` CHAR(250) NOT NULL,
                `file_name` CHAR(250) NOT NULL,
                PRIMARY KEY(`id`)
            )
        ;';

        $db = $this->db;

        $selCreateQuery = $db->prepare($query);

        return $selCreateQuery->execute();
    }

}
