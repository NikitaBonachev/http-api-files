<?php

namespace App;

use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Data\FilesStorage;

class ControllerProvider implements ControllerProviderInterface
{
    private $app;


    /**
     * @param App $app
     * @return mixed
     */
    public function connect(App $app)
    {
        $this->app = $app;

        $app->error([$this, 'error']);

        $controllers = $app['controllers_factory'];

        // Get all files
        $controllers
            ->get('/files', [$this, 'getFiles'])
            ->bind('files');

        // Upload new file
        $controllers
            ->post('/files', [$this, 'uploadFile']);

        // Download one file
        $controllers
            ->get('/files/{id}', [$this, 'getOneFile']);

        // Get file meta
        $controllers
            ->get('/files/{id}/meta', [$this, 'getOneFileMeta']);

        // Update file
        $controllers
            ->post('/files/{id}', [$this, 'updateFile']);

        // Update file name
        $controllers
            ->put('/files/{id}', [$this, 'updateFileName']);

        // Delete file
        $controllers
            ->delete('/files/{id}', [$this, 'deleteFile']);

        return $controllers;
    }


    /**
     * Get all files
     *
     * @param App $app
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getFiles(App $app)
    {
        echo '<pre style="font-size: 14pt; line-height: 1.5;">';
        var_dump(phpversion());
        echo '</pre>';
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result['list'] = $dataProvider->getFilesList();
        return $app->json($result);
    }


    /**
     * Update file
     *
     * @param App $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateFile(App $app, Request $request, $id)
    {
        $files = $request->files->get('upload_file');
        $result['result'] = FilesStorage::updateFile($files, $id, $app);
        return $app->json($result);
    }


    /**
     * Update file name
     *
     * @param App $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateFileName(App $app, Request $request, $id)
    {
        $newName = json_decode($request->getContent(), true)['name'];
        $result['result'] = FilesStorage::updateFileName($newName, $id, $app);
        return $app->json($id);
    }


    /**
     * Create new file
     *
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function uploadFile(App $app, Request $request)
    {
        $files = $request->files->get('upload_file');
        $result['id'] = FilesStorage::createFile($files, $app);
        return $app->json($result);
    }


    /**
     * Download one file
     *
     * @param App $app
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getOneFile(App $app, $id)
    {
        $fileInfo = FilesStorage::getFile($id, $app);
        return $app->sendFile(
            $fileInfo['filePath'],
            200,
            [
                'Content-Type' => mime_content_type($fileInfo['filePath']),
                'Content-Disposition' => 'inline',
                'filename' => basename($fileInfo['fileName'])
            ]
        );
    }


    /**
     * File meta
     *
     * @param App $app
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOneFileMeta(App $app, $id)
    {
        $fileMeta = FilesStorage::getFileMeta($id, $app);
        return $app->json($fileMeta);
    }


    /**
     * Delete file
     *
     * @param App $app
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteFile(App $app, $id)
    {
        $result = FilesStorage::deleteFile($id, $app);
        return $app->json($result);
    }


    /**
     * Errors
     *
     * @param \Exception $e
     * @param Request $request
     * @param $code
     * @return Response
     */
    public function error(\Exception $e, Request $request, $code)
    {
        if (!$this->app['debug']) {
            switch ($code) {
                case 404:
                    $message = 'The requested page could not be found.';
                    break;
                default:
                    $message = $e . 'We are sorry, but something went terribly wrong.';
            }

            return new Response($message, $code);
        }

    }

}
