<?php

namespace Hypilon\Http\Request;


class Headers
{
    /**
     * @var array $headers
     */
    private $headers = [];

    public function __construct()
    {
        $this->headers = $this->getAllHeaders();
    }

    public function get($name, $defaultValue = null)
    {
        return $this->headers[$name] ?? $defaultValue;
    }

    private function getAllHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}