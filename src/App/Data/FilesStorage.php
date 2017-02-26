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
     * Create new file
     *
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

        // Check if file with this name doesn't exist in database
        $result = $dataProvider->getFileByName($originalName);
        if ($result > 0) {
            return -1;
        }

        $path = ConfigProvider::getUploadDir($app['env']);
        $fileName = strval(Uuid::uuid1());

        if ($file->getClientOriginalExtension()) {
            $fileName .= '.' . $file->getClientOriginalExtension();
        }

        $file->move($path, $fileName);

        // Add new file in DB. Return ID
        return $dataProvider->addNewFile($originalName, $fileName);
    }


    /**
     * Update content of existing file
     *
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

        // Check that file exist
        if ($prevFile['id'] > 0) {
            $previousFilePath = ConfigProvider::getUploadDir($app['env']);
            $previousFilePath .= $prevFile['file_name'];

            if (file_exists($previousFilePath)
                && is_file($previousFilePath)
            ) {
                $newFileContent = file_get_contents($file->getRealPath());
                file_put_contents($previousFilePath, $newFileContent);
                $result = $prevFile['id'];
            }
        }

        return $result;
    }


    /**
     * Rename existing file
     *
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
     * Get content of file
     *
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
            $filePath = $uploadPath . $result['file_name'];
            return [
                'filePath' => $filePath,
                'originalName' => $result['original_name'],
                'Content-Type' => mime_content_type($filePath),
                'filename' => $result['original_name']
            ];
        }
    }


    /**
     * Delete file
     *
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
        $filePath = $uploadPath . $result['file_name'];

        if (file_exists($filePath)) {
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        return $dataProvider->deleteFile($id);
    }


    /**
     * Get meta-data of file
     *
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

        $filePath = ConfigProvider::getUploadDir($app['env']);
        $filePath .= $result['file_name'];

        if (!file_exists($filePath) || $result['id'] == 0) {
            return $app->abort(404);
        } else {
            $meta['name'] = $result['original_name'];
            $meta['size'] = filesize($filePath);
            $meta['filemtime'] = gmdate(DATE_RFC1123, filemtime($filePath));
            $meta['filectime'] = gmdate(DATE_RFC1123, filectime($filePath));
            $meta['fileatime'] = gmdate(DATE_RFC1123, fileatime($filePath));
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
