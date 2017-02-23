<?php

namespace App\Data\Entities;

/**
 * @Entity @Table(name="files")
 **/
class File
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string") **/
    protected $fileName;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->fileName;
    }

    public function setName($name)
    {
        $this->fileName = $name;
    }
}

