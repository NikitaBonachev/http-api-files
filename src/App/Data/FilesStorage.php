<?php

namespace App\Data;

use Ramsey\Uuid\Uuid;
use Silex\Application as App;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesStorage
{

    /**
     * @param UploadedFile $file
     * @param App $app
     *
     * @return bool
     */
    public static function createFile($file, App $app)
    {
        $path = __DIR__.'/../../../upload/';
        $filename = $file->getClientOriginalName();

        $uuid1 = Uuid::uuid1();

        if ($file->move($path, $uuid1)) {
            $db = $app['db'];
            $dataProvider = new \App\Data\DataManager($db);
            $id = $dataProvider->addNewFile($filename, $uuid1);
            return $id;
        } else {
            return false;
        }
    }

}
