<?php

namespace Hypilon\Controller;

use Hypilon\Http\Request\Request;
use Hypilon\Interfaces\ControllerInterface;
use Hypilon\Http\Response\Response;
use Hypilon\View\View;

class Controller implements ControllerInterface
{
    /**
     * @var string $layout
     */
    private $layout;

    public function beforeAction(Request $request)
    {
        // TODO: Implement beforeAction() method.
    }

    public function afterAction(Request $request)
    {
        // TODO: Implement afterAction() method.
    }

    protected function setLayout($layout)
    {
        $this->layout = $layout;
    }

    protected function render($template, $parameters = [])
    {
        $view = new View($template, $parameters);
        $view->setLayout($this->layout);

        return $view->getResponse();
    }

    protected function renderView($template, $parameters = [])
    {
        $view = new View($template, $parameters);
        $view->setLayout($this->layout);

        return $view->getContent();
    }

    protected function redirect($url, $statusCode = 301)
    {
        $response = new Response(null, $statusCode);
        $response->headers->add('Location', $url);
        return $response;
    }

    protected function show404error($message = 'Request page is not found')
    {
        $controllerResponse = new ControllerResponse();
        $controllerResponse->showError(404, $message);
    }
}