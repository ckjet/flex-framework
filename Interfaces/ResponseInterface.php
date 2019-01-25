<?php

namespace Hypilon\Interfaces;


interface ResponseInterface
{
    public function __construct($content = null, $statusCode = 200);

    public function render();
}