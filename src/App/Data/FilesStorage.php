<?php

namespace App\Data;

use Ramsey\Uuid\Uuid;
use Silex\Application as App;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesStorage
{

    private static function baseUploadPath() {
        return __DIR__.'/../../../upload/';
    }

    /**
     * @param UploadedFile $file
     * @param App $app
     *
     * @return bool
     */
    public static function createFile($file, App $app)
    {
        $path = self::baseUploadPath();
        $originalName = $file->getClientOriginalName();

        $uuid1 = strval(Uuid::uuid1()) . '.' . $file->getClientOriginalExtension();

        if ($file->move($path, $uuid1)) {
            $db = $app['db'];
            $dataProvider = new \App\Data\DataManager($db);
        //    $id = $dataProvider->addNewFile($originalName, $uuid1);
            $id = $dataProvider->addNewFile($originalName, $uuid1);
            return $id;
        } else {
            return false;
        }
    }

    /**
     * @param integer $id
     * @param App $app
     *
     * @return array
     */
    public static function getFile($id, App $app)
    {
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result = $dataProvider->getOneFile($id);

        if (!file_exists(self::baseUploadPath() . $result['file_name'])) {
            $app->abort(404);
        } else {
            return [
                'filePath' => self::baseUploadPath() . $result['file_name'],
                'fileName' => $result['original_name']
            ];
        }
    }


    /**
     * @param $id
     * @param App $app
     * @return int
     */
    public static function deleteFile($id, App $app)
    {
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result = $dataProvider->getOneFile($id);

        if (!file_exists(self::baseUploadPath() . $result['file_name'])) {
            $app->abort(404);
        } else {
            unlink(self::baseUploadPath() . $result['file_name']);
            return $dataProvider->deleteFile($id);
        }
    }

    /**
     * @param integer $id
     * @param App $app
     *
     * @return bool
     */
    public static function getFileMeta($id, App $app)
    {
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result = $dataProvider->getOneFile($id);

        $filePath = self::baseUploadPath() . $result['file_name'];

        if (!file_exists($filePath)) {
            $app->abort(404);
        } else {

        //  $meta['stat'] = stat($filePath);
            $meta['mime'] = mime_content_type($filePath);
            $meta['meta_tags'] = get_meta_tags($filePath);
            try {
                $meta['exif'] = exif_read_data($filePath);
            } catch (\Exception $e) {

            }

            return $meta;
        }
    }

}
