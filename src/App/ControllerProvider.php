<?php
namespace App;

use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerProvider implements ControllerProviderInterface
{
    private $app;

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
            ->post('/files/{id}', [$this, 'getOneFile']);

        $controllers
            ->get('/', [$this, 'homepage'])
            ->bind('homepage');

        return $controllers;
    }

    public function homepage(App $app)
    {
        $result['state'] = 'homepage';
        return $app->json($result);
    }

    public function getFiles(App $app)
    {
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result['list'] = $dataProvider->getFilesList();
        return $app->json($result);
    }

    public function getOneFile(App $app, $id)
    {
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result['id'] = $dataProvider->getOneFile($id);
        return $app->json($id);
    }

    public function uploadFile(App $app, Request $request)
    {
        $name = $request->get('name');
        $db = $app['db'];
        $dataProvider = new \App\Data\DataManager($db);
        $result['id'] = $dataProvider->addNewFile($name);
        return $app->json($result);
    }

    public function error(\Exception $e, Request $request, $code)
    {
        if ($this->app['debug']) {
            return;
        }

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
