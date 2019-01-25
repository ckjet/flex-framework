<?php

namespace Hypilon\Http\Request;


class Request
{
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PURGE = 'PURGE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    /**
     * @var ParameterBag $query
     */
    public $query;

    /**
     * @var ParameterBag $request
     */
    public $request;

    /**
     * @var ParameterBag $attributes
     */
    public $attributes;

    /**
     * @var ParameterBag $server
     */
    public $server;

    /**
     * @var string $method
     */
    private $method;

    /**
     * @var Headers $headers
     */
    private $headers;

    public function __construct()
    {
        $this->query = new ParameterBag($_GET);
        $this->request = new ParameterBag($_POST);
        $this->attributes = new ParameterBag();
        $this->server = new ParameterBag($_SERVER);
        $this->headers = new Headers();
        $this->method = $this->getMethod();
    }

    public function getMethod()
    {

        if (null === $this->method) {
            $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));

            if ('POST' === $this->method) {
                if ($method = $this->headers->get('X-HTTP-METHOD-OVERRIDE')) {
                    $this->method = strtoupper($method);
                }
            }
        }

        return $this->method;
    }

    public function isMethod($method)
    {
        return $this->getMethod() === strtoupper($method);
    }

    public function getUri()
    {
        $fullUri = $this->server->get('REQUEST_URI', '');
        return str_replace('?' . $this->getQueryString(), '', $fullUri);
    }

    public function getDocumentRoot()
    {
        return $this->server->get('DOCUMENT_ROOT', '');
    }

    public function getClientIp()
    {
        return $this->server->get('REMOTE_ADDR', '');
    }

    public function getUserAgent()
    {
        return $this->server->get('HTTP_USER_AGENT', '');
    }

    public function getQueryString()
    {
        return $this->server->get('QUERY_STRING', '');
    }
}