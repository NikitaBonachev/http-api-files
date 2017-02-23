<?php

namespace App\Data;

use Doctrine\ORM\EntityRepository;

class FilesStorage extends EntityRepository
{
    public function getFilesList($number = false)
    {
        $dql = "SELECT id, fileName FROM files";

        $query = $this->getEntityManager()->createQuery($dql);

        if (is_numeric($number)) {
            $query->setMaxResults($number);
        }

        return $query->getResult();
    }

}
