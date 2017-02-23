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

        $fileName = strval(Uuid::uuid1()) . '.' . $file->getClientOriginalExtension();

        if ($file->move($path, $fileName)) {
            $db = $app['db'];
            $dataProvider = new \App\Data\DataManager($db);
            $id = $dataProvider->addNewFile($originalName, $fileName);
            return $id;
        } else {
            return false;
        }
    }


    /**
     * @param UploadedFile $file
     * @param integer $id
     * @param App $app
     *
     * @return bool
     */
    public static function updateFile($file, $id, App $app)
    {
        $db = $app['db'];
        $dataProvider = new DataManager($db);

        $prevFile = $dataProvider->getOneFile($id);
        $path = self::baseUploadPath();

        if (file_exists($path . $prevFile['file_name'])
            && is_file($path . $prevFile['file_name'])
        ) {
            unlink($path . $prevFile['file_name']);
        }

        $newFileName = strval(Uuid::uuid1()) . '.' . $file->getClientOriginalExtension();
        $result = $dataProvider->updateFile($id, $prevFile['original_name'], $newFileName);

        if ($file->move($path, $newFileName)) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * @param string $newFileName
     * @param integer $id
     * @param App $app
     *
     * @return bool
     */
    public static function updateFileName($newFileName, $id, App $app)
    {
        $db = $app['db'];
        $dataProvider = new DataManager($db);
        return $dataProvider->updateFile($id, $newFileName);
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
        $dataProvider = new DataManager($db);
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
        $dataProvider = new DataManager($db);
        $result = $dataProvider->getOneFile($id);

        if (file_exists(self::baseUploadPath() . $result['file_name'])) {
            if (is_file(self::baseUploadPath() . $result['file_name'] )) {
                unlink(self::baseUploadPath() . $result['file_name']);
            }
        }

        return $dataProvider->deleteFile($id);
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
        $dataProvider = new DataManager($db);
        $result = $dataProvider->getOneFile($id);

        $filePath = self::baseUploadPath() . $result['file_name'];

        if (!file_exists($filePath)) {
            $app->abort(404);
        } else {

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
