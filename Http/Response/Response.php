<?php

namespace Hypilon\Http\Response;

use Hypilon\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var string $content
     */
    protected $content;

    /**
     * @var int $statusCode
     */
    protected $statusCode = 200;

    /**
     * @var string $contentType
     */
    protected $contentType = 'text/html';

    /**
     * @var Headers $headers
     */
    public $headers;

    public function __construct($content = null, $statusCode = 200)
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = new Headers();
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContentType()
    {
        return $this->content;
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->getStatusCode();
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function render()
    {
        foreach($this->headers->all() as $key => $value) {
            header("{$key}: $value");
        }
        header("Content-type: {$this->contentType}");
        http_response_code($this->statusCode);
        echo $this->content;
    }
}