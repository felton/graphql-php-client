<?php
namespace GraphQLClient\Traits;

use Psr\Http\Message\ResponseInterface;

trait Response
{
    /**
     * [$response description]
     *
     * @var [type]
     */
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
        return $this->getResponse()->getBody()->getContents();
    }
}
