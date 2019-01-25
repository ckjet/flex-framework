<?php

namespace Hypilon\Http\Routing;

class RouteParser
{
    private $route;
    private $pattern;
    private $attributes = [];

    public function __construct($route, $withParse = true)
    {
        $this->route = $route;
        if($withParse) {
            $this->parse();
        }
    }

    private function parse()
    {
        $this->pattern = $this->route['path'];
        if (preg_match_all('/\{.*?\}/', $this->route['path'], $matches)) {
            array_walk($matches, function (&$item) {
                $item = str_replace(['{', '}'], '', $item);
            });
            $this->pattern = preg_replace('#\{.*?\}#', '(.*?)', $this->route['path']);
            $this->attributes = $matches[0];
        }
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getCallDetails()
    {
        $info = explode('@', $this->route['callback']);
        return [
            'controller' => $info[0],
            'action' => $info[1]
        ];
    }

    public function getMethod()
    {
        return $this->route['method'];
    }

    public function getCallback()
    {
        return $this->route['callback'];
    }

    public function getPattern()
    {
        return '#^' . $this->pattern . '$#';
    }
}