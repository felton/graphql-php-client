<?php
namespace GraphQLClient\Traits;

use Psr\Http\Message\ResponseInterface;

trait Response
{
    /**
     * PSR7 Response from a GraphQL Server
     *
     * @var Psr\Http\Message\ResponseInterface
     */
    protected $response;

    public function getResponse()
    {
        if ($this->response instanceof ResponseInterface) {
            return $this->response;
        }
        return false;
    }

    /**
     * Get body of response, or throw an exception.
     *
     * @return string Response body
     */
    protected function handleResponse()
    {
        $response = $this->getResponse();

        if (!$response) {
            throw new \Http\Client\Exception\TransferException('Response does not exist.');
        }

        return $response->getBody()->getContents();
    }
}
