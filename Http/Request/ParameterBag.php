<?php

namespace Hypilon\Http\Request;


class ParameterBag
{
    private $parameters;
    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function get($var, $defaultValue = null)
    {
        return $this->parameters[$var] ?? $defaultValue;
    }

    public function set($var, $value)
    {
        $this->parameters[$var] = $value;
    }

    public function setArray($array = [])
    {

        foreach($array as $var => $value) {
            $this->parameters[$var] = $value;
        }
    }

    public function all()
    {
        return $this->parameters;
    }
}