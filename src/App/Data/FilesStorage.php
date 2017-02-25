<?php

namespace App\Data;

use App\Config\ConfigProvider;
use Ramsey\Uuid\Uuid;
use Silex\Application as App;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

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
        $db = $app['db'];
        $dataProvider = new DataManager($db);
        $originalName = $file->getClientOriginalName();

        $result = $dataProvider->getFileByName($originalName);

        if ($result > 0) {
            return -1;
        }

        $path = ConfigProvider::getUploadDir($app['env']);
        if ($file->getClientOriginalExtension()) {
            $fileName = strval(Uuid::uuid1()) . '.' . $file->getClientOriginalExtension();
        } else {
            $fileName = strval(Uuid::uuid1());
        }

        $file->move($path, $fileName);
        return $dataProvider->addNewFile($originalName, $fileName);
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
        $result = 0;

        $prevFile = $dataProvider->getOneFile($id);

        if ($prevFile['id'] > 0) {
            $path = ConfigProvider::getUploadDir($app['env']);

            if (file_exists($path . $prevFile['file_name'])
                && is_file($path . $prevFile['file_name'])
            ) {
                unlink($path . $prevFile['file_name']);
            }

            $newFileName = strval(Uuid::uuid1()) . '.' . $file->getClientOriginalExtension();
            $result = $dataProvider->updateFile(
                $id,
                $prevFile['original_name'],
                $newFileName
            );

            $file->move($path, $newFileName);
        }

        return $result;
    }


    /**
     * @param string $newFileName
     * @param integer $id
     * @param App $app
     *
     * @return bool|
     * @throws \Exception
     */
    public static function updateFileName($id, $newFileName, App $app)
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
        $uploadPath = ConfigProvider::getUploadDir($app['env']);

        if (!file_exists($uploadPath . $result['file_name']) || $result == 0) {
            return $app->abort(HTTPResponse::HTTP_NOT_FOUND);
        } else {
            return [
                'filePath' => $uploadPath . $result['file_name'],
                'originalName' => $result['original_name']
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
        $uploadPath = ConfigProvider::getUploadDir($app['env']);

        if (file_exists($uploadPath . $result['file_name'])) {
            if (is_file($uploadPath . $result['file_name'])) {
                unlink($uploadPath . $result['file_name']);
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

        $filePath = ConfigProvider::getUploadDir($app['env']) . $result['file_name'];

        if (!file_exists($filePath) || $result['id'] == 0) {
            return $app->abort(404);
        } else {
            $meta['name'] = $result['original_name'];
            $meta['size'] = filesize($filePath);
            $meta['modified'] = gmdate(DATE_RFC1123, filemtime($filePath));
            $meta['created'] = gmdate(DATE_RFC1123, filectime($filePath));
            $meta['mime_type'] = mime_content_type($filePath);
            $meta['md5'] = hash_file("md5", $filePath);
            try {
                $meta['exif'] = exif_read_data($filePath);
            } catch (\Exception $e) {
                // No exif - it's normal
            }
            return $meta;
        }
    }
}
