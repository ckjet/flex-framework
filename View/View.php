<?php

namespace Hypilon\View;


use Hypilon\Config\Config;
use Hypilon\Controller\ControllerResponse;
use Hypilon\DependencyInjection\Container;
use Hypilon\Http\Response\Response;
use Hypilon\Http\Routing\RouterHelper;

class View
{
    /**
     * @var string $template
     */
    private $template;

    /**
     * @var array $parameters
     */
    private $parameters;

    /**
     * @var string $content
     */
    private $content;

    /**
     * @var string $layout
     */
    public $layout;

    public function __construct($template, $parameters = [])
    {
        $this->template = $template;
        $this->parameters = $parameters;
    }

    public function getContent()
    {
        $this->build();
        return $this->content;
    }

    public function getResponse()
    {
        $this->build();
        return new Response($this->content);
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function hasLayout()
    {
        if(!$this->layout) {
            return false;
        }
        return true;
    }

    private function build()
    {
        $controllerResponse = new ControllerResponse();
        $templateFile = Config::get('view_dir') . '/' . $this->dotToPath($this->template);
        if (!is_file($templateFile)) {
            $message = '';
            if(Config::get('debug_mode', false)) {
                $message = "Template <b>{$this->template}</b> not found<br/>Expected file <u>{$templateFile}</u>";
            }
            $controllerResponse->showError(404, $message);
        } else {
            $router = Container::get('router');
            ob_start();
            extract($this->parameters);
            $view = new ViewHelper();
            $stringHelper = new StringHelper();
            $router = new RouterHelper($router);
            include($templateFile);
            $viewContent = ob_get_clean();
            $this->content = $viewContent;
            if($this->hasLayout()) {
                $layoutFile = Config::get('view_dir') . '/' . $this->dotToPath($this->layout);
                if (!is_file($layoutFile)) {
                    $message = '';
                    if(Config::get('debug_mode', false)) {
                        $message = "Layout <b>{$this->layout}</b> not found<br/>Expected file <u>{$layoutFile}</u>";
                    }
                    $controllerResponse->showError(404, $message);
                } else {
                    ob_start();
                    extract($this->parameters);
                    include($layoutFile);
                    $this->content = ob_get_clean();
                }
            }
        }
    }

    private function dotToPath($template)
    {
        $parts = explode('.', $template);
        $parts = array_map('ucfirst', $parts);
        $parts[sizeof($parts) - 1] = strtolower($parts[sizeof($parts) - 1]);

        return join('/', $parts) . '.view.php';
    }
}