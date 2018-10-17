<?php
namespace GraphQLClient\Traits;

use Psr\Http\Message\ResponseInterface;

trait Response
{
    protected $response;

    public function getResponse()
    {
        if ($this->response instanceof ResponseInterface) {
            return $this->response;
        }
        return false;
    }

    protected function handleResponse()
    {
        return $this->getResponse();
    }
}
