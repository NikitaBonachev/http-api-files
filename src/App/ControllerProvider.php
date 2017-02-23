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

        $controllers
            ->get('/files', [$this, 'getFiles'])
            ->bind('files');

        $controllers
            ->post('/files', [$this, 'uploadFile']);

        $controllers
            ->get('/files/{id}', [$this, 'getOneFile']);

        $controllers
            ->get('/files/{id}/meta', [$this, 'getOneFileMeta']);

        $controllers
            ->post('/files/{id}', [$this, 'updateFile']);

        $controllers
            ->delete('/files/{id}', [$this, 'deleteFile']);

        $controllers
            ->get('/', [$this, 'homepage'])
            ->bind('homepage');

        return $controllers;
    }


    /**
     * @param App $app
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function homepage(App $app)
    {
        $result['state'] = 'homepage';
        return $app->json($result);
    }


    /**
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
