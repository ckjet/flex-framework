<?php

namespace Hypilon\Http\Response;

class JsonResponse extends Response
{
    public function __construct($content = null, $statusCode = 200)
    {
        parent::__construct($content, $statusCode);
        $this->contentType = 'application/json';
    }

    public function render()
    {
        foreach ($this->headers->all() as $key => $value) {
            header("{$key}: $value");
        }
        header("Content-type: {$this->contentType}");
        http_response_code($this->statusCode);
        echo json_encode($this->content);
    }
}