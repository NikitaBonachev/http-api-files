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
     *
     * @return bool
     */
    public function addNewFile($name) {

        $filesTable = $this->filesTableName;
        $createFileQueryText = 'INSERT INTO ' . $filesTable . ' (name) VALUES ("' . $name . '");';

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
        $filesQueryText = 'SELECT * from ' . $filesTable . ';';

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
     * @return bool
     */
    private function createTableFilesList() {
        $filesTable = $this->filesTableName;
        $createQueryText = 'CREATE TABLE ' . $filesTable . '(
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` CHAR(250) NOT NULL,
                PRIMARY KEY(`id`)
            )
        ;';

        $db = $this->db;

        $selCreateQuery = $db->prepare($createQueryText);

        return $selCreateQuery->execute();
    }

}
