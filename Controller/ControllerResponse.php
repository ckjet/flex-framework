<?php

namespace Hypilon\Controller;

use Hypilon\Config\Config;
use Hypilon\DependencyInjection\Container;
use Hypilon\Http\Request\Request;
use Hypilon\Http\Response\Response;
use Hypilon\Http\Routing\RouteParser;
use Hypilon\Http\Routing\Router;
use Hypilon\Interfaces\ControllerInterface;

class ControllerResponse
{
    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var Request $request
     */
    private $request;

    /**
     * ControllerResponse constructor.
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        $this->router = Container::get('router');
        $this->request = $request;
    }

    /**
     * @param $code
     * @param $message
     */
    public function showError($code, $message)
    {
        if (!$errorCall = Config::get('error' . $code)) {
            die("<b>Error {$code}</b>");
        }
        $attributes['error_message'] = $message;
        if (is_array($errorCall)) {
            $callback = $errorCall[0];
            if (sizeof($errorCall) === 2) {
                $attributes = array_merge($attributes, $errorCall[1]);
            }
        } else {
            $callback = $errorCall;
        }
        $callDetails = explode('@', $callback);
        if (!$this->isCallable($callDetails[0], $callDetails[1])) {
            echo 'Not callable';
        }
        $matched = new RouteParser([
            'callback' => $callback,
            'method' => Request::METHOD_GET,
            'attributes' => $attributes
        ], false);
        $router = new Router();
        $request = new Request();
        $router->setMatched($matched);
        $request->attributes->setArray($attributes);
        $response = $this->getResponse($router, $request);
        $response->setStatusCode($code);
        $response->render();
        exit();
    }

    /**
     * @param Router|null $router
     * @param Request|null $request
     * @return Response
     */
    public function getResponse(Router $router = null, Request $request = null)
    {
        if(!$router) {
            $router = $this->router;
        }
        if(!$request) {
            $request = $this->request;
        }
        $matched = $router->getMatchedDetails();
        $controllerClass = 'Application\\Controller\\' . $matched['controller'];
        if (!class_exists($controllerClass)) {
            $message = '';
            if (Config::get('debug_mode', false)) {
                $message = "Controller class not found: <b>{$controllerClass}</b>";
            }
            $this->showError(404, $message);
        }
        $controller = new $controllerClass();
        if (!$controller instanceof ControllerInterface) {
            $message = '';
            if (Config::get('debug_mode', false)) {
                $message = "Controller must implement <b>ControllerInterface</b>";
            }
            $this->showError(500, $message);
        }
        $beforeResponse = $controller->{'beforeAction'}($request);
        if ($beforeResponse) {
            $message = '';
            if (Config::get('debug_mode', false)) {
                $message = "Before action must be without any response";
            }
            $this->showError(500, $message);
        }
        $action = $matched['action'] . 'Action';
        if (!method_exists($controllerClass, $action)) {
            $message = 'Request page not found';
            if (Config::get('debug_mode', false)) {
                $message = "Action not found: <b>{$controllerClass}:{$action}</b>";
            }
            $this->showError(404, $message);
        }
        $response = $controller->{$action}($request);
        $afterResponse = $controller->{'afterAction'}($request);
        if ($afterResponse) {
            $message = '';
            if (Config::get('debug_mode', false)) {
                $message = "After action must be without any response";
            }
            $this->showError(500, $message);
        }
        return $response;
    }

    /**
     * @param $controller
     * @param $action
     * @return bool
     */
    public function isCallable($controller, $action)
    {
        $controllerClass = 'Application\\Controller\\' . $controller;
        if (!class_exists($controllerClass)) {
            return false;
        }
        $object = new $controllerClass();
        if (!$object instanceof ControllerInterface) {
            return false;
        }
        if (!method_exists($object, $action . 'Action')) {
            return false;
        }
        return true;
    }
}