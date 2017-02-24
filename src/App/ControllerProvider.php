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
            ->post('/files', [$this, 'uploadNewFile']);

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
            ->put('/files/{id}/name', [$this, 'updateFileName']);

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
        $result['id'] = FilesStorage::updateFile($files, $id, $app);
        return $app->json($result, Response::HTTP_OK);
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
        $result['id'] = FilesStorage::updateFileName($id, $newName, $app);
        return $app->json($id);
    }


    /**
     * Create new file
     *
     * @param App $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function uploadNewFile(App $app, Request $request)
    {
        $files = $request->files->get('upload_file');
        $result['ID'] = FilesStorage::createFile($files, $app);
        return $app->json($result, Response::HTTP_CREATED);
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
            Response::HTTP_OK,
            [
                'Content-Type' => mime_content_type($fileInfo['filePath']),
                'Content-Disposition' => 'inline',
                'filename' => basename($fileInfo['originalName'])
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFile(App $app, $id)
    {
        if (FilesStorage::deleteFile($id, $app) == 1) {
            return new Response('', Response::HTTP_NO_CONTENT);
        } else {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
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
        switch ($code) {
            case Response::HTTP_NOT_FOUND:
                $message = 'The requested page could not be found.';
                break;
            default:
                $message = $e . 'We are sorry, but something went terribly wrong.';
        }

        return $this->app->json(
            [
                "code" =>$code,
                "message" => $message,
                "request" => $request->getContent()
            ],
            $code
        );
    }
}
