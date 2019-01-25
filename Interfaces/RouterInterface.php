<?php

namespace Hypilon\Interfaces;

interface RouterInterface
{
    public function get($path, $callback);
    public function post($path, $callback);
}