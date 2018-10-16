<?php

namespace GraphQLClient;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

trait Request
{
    protected $request;

    protected $headers = [];

    protected $baseUrl;

    protected $method = '';

    //protected $queryParams = [];

    public function buildRequest()
    {
        return new Request($this->getMethod(), $this->getBaseUrl(), $this->getHeaders());
    }

    public function getRequest() : RequestInterface
    {
        if (!$this->request) {
            $this->request = $this->buildRequest();
        }
        return $this->request;
    }

    protected function createRequest(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1'
    ): RequestInterface {
        return new Request($method, $uri, $headers, $body, $version);
    }

    public function setHeaders(array $headers = [])
    {
        $this->headers = $headers;
        return $this;
    }

    public function addHeader($key = '', $value = '')
    {
        $this->headers['key'] = $value;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
