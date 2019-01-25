<?php

namespace Hypilon\Http\Response;

class Headers
{
    private $headers = [];

    public function get($name, $defaultValue = null)
    {
        return $this->headers[$name] ?? $defaultValue;
    }

    public function add($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function all()
    {
        return $this->headers;
    }
}