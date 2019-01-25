<?php

namespace Hypilon;

use Hypilon\Config\Config;
use Hypilon\Database\MySQL;
use Hypilon\DependencyInjection\Container;
use Hypilon\Controller\ControllerResponse;
use Hypilon\Interfaces\ConfigurationInterface;
use Hypilon\Interfaces\DILoaderInterface;
use Hypilon\Interfaces\ResponseInterface;
use Hypilon\Http\Request\Request;
use Hypilon\Http\Routing\Router;

class Loader
{
    public function __construct($debug = false)
    {
        Config::set('debug_mode', (bool)$debug);
    }

    private function showError($code, $message)
    {
        $controllerResponse = new ControllerResponse();
        $controllerResponse->showError($code, $message);
    }

    private function boot()
    {
        $this->registerPaths();
        $this->registerConfiguration();
        $this->registerDI();
    }

    private function registerPaths()
    {
        $root = str_replace('/vendor/hypilon/framework', '', dirname(__FILE__));
        Config::setArray([
            'root_dir' => $root,
            'view_dir' => $root . '/app/Resources/view',
            'controller_dir' => $root . '/app/Controller'
        ]);
    }

    private function registerConfiguration()
    {
        $files = glob(Config::get('root_dir') . '/app/Config/*.php');
        foreach ($files as $file) {
            $class = str_replace([Config::get('root_dir') . '/', '.php', '/', 'app'], ['', '', '\\', 'Application'], $file);
            $configuration = new $class();
            if ($configuration instanceof ConfigurationInterface) {
                $configuration->apply();
            }
        }
    }

    private function registerDI()
    {
        Container::set('db', new MySQL());
        Container::set('router', new Router());
        $files = glob(Config::get('root_dir') . '/app/DependencyInjection/*.php');
        foreach ($files as $file) {
            $class = str_replace([Config::get('root_dir') . '/', '.php', '/', 'app'], ['', '', '\\', 'Application'], $file);
            $configuration = new $class();
            if ($configuration instanceof DILoaderInterface) {
                $configuration->load();
            }
        }
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function handle(Request $request)
    {
        $this->boot();
        /** @var Router $router */
        $router = Container::get('router');
        $router->boot();
        if(!$router->match($request)) {
            $message = "Request page is not found";
            $this->showError(404, $message);
        }
        $attributes = $router->getAttributes();
        $request->attributes->setArray($attributes);
        $controllerResponse = new ControllerResponse($request);
        $response = $controllerResponse->getResponse();
        if($response instanceof ResponseInterface) {
            return $response;
        } else {
            $message = '';
            if(Config::get('debug_mode', false)) {
                $message = "Response must implement <b>ResponseInterface</b>";
            }
            $this->showError(500, $message);
        }
    }
}