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
            ->put('/files/{id}', [$this, 'updateFile'])
            ->before(function (Request $request) {
                if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                    $data = json_decode($request->getContent(), true);
                    $request->request->replace(is_array($data) ? $data : array());
                }
            });

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
        $db = $app['db'];
        $newName = $request->request->get('name');
        $dataProvider = new \App\Data\DataManager($db);
        $result = $dataProvider->updateFile($id, $newName);
        return $app->json($result);
    }

    /**
     * @param App $app
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOneFile(App $app, $id)
    {
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result = $dataProvider->getOneFile($id);
        return $app->json($result);
    }

    /**
     * @param App $app
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOneFileMeta(App $app, $id)
    {
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result = $dataProvider->getOneFile($id);
        return $app->json($result);
    }


    /**
     * @param App $app
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteFile(App $app, $id)
    {
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result = $dataProvider->deleteFile($id);
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
