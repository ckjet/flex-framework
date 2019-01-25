<?php

namespace Hypilon\Http\Routing;

use Hypilon\Interfaces\RouterInterface;
use Hypilon\Interfaces\RoutesInterface;
use Hypilon\Http\Request\Request;

class Router implements RouterInterface
{
    /**
     * @var array $routes
     */
    private $routes = [];

    /**
     * @var RouteParser $matched
     */
    private $matched;

    private $attributes = [];

    public function boot()
    {
        $files = glob($_SERVER['DOCUMENT_ROOT'] . '/../app/Routing/*.php');
        foreach ($files as $file) {
            $class = str_replace([$_SERVER['DOCUMENT_ROOT'] . '/../', '.php', '/', 'app'], ['', '', '\\', 'Application'], $file);
            $configuration = new $class($this);
            if ($configuration instanceof RoutesInterface) {
                $configuration->apply();
            }
        }
    }

    public function get($path, $callback, $attributes = [])
    {
        $this->add($path, $callback, $attributes, Request::METHOD_GET);
    }

    public function post($path, $callback, $attributes = [])
    {
        $this->add($path, $callback, $attributes, Request::METHOD_POST);
    }

    public function put($path, $callback, $attributes = [])
    {
        $this->add($path, $callback, $attributes, Request::METHOD_PUT);
    }

    public function delete($path, $callback, $attributes = [])
    {
        $this->add($path, $callback, $attributes, Request::METHOD_DELETE);
    }

    public function patch($path, $callback, $attributes = [])
    {
        $this->add($path, $callback, $attributes, Request::METHOD_PATCH);
    }

    public function match(Request $request)
    {
        $uri = $request->getUri();
        foreach($this->routes as $route) {
            $parser = new RouteParser($route);
            if(preg_match($parser->getPattern(), $uri, $matches)) {
                if($route['method'] === $request->getMethod()) {
                    array_shift($matches);
                    $this->attributes = $route['attributes'];
                    $attributes = array_combine($parser->getAttributes(), $matches);
                    $this->attributes = array_merge($this->attributes, $attributes);
                    $this->matched = $parser;
                    return true;
                }
            }
        }
        return false;
    }

    public function setMatched(RouteParser $matched)
    {
        $this->matched = $matched;
        return $this;
    }

    public function getMatched()
    {
        return $this->matched;
    }

    public function getMatchedDetails()
    {
        if($this->matched) {
            return $this->matched->getCallDetails();
        }
        return false;
    }

    public function getAttributes()
    {
        if($this->matched) {
            return $this->attributes;
        }
        return [];
    }

    private function add($path, $callback, $attributes, $method)
    {
        $this->routes[] = [
            'path' => $path,
            'callback' => $callback,
            'method' => $method,
            'attributes' => $attributes
        ];
    }

}