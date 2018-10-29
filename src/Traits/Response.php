<?php
namespace GraphQLClient\Traits;

use GraphQLClient\Exception\QueryException;
use GraphQLClient\Traits\Utils;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\TransferException
use Psr\Http\Message\ResponseInterface;

trait Response
{
    use Utils;

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
        $responseBody = '';

        if (!$response) {
            throw new TransferException('Response does not exist.');
        }

        if ($response->getStatusCode() == 200) {
            // Check for application/json header
            // Usually the error in this case comes from the json parser if text/html is returned
            // Let's check the response so we can be more clear with our error message
            if($this->responseIsJSON()) {
                $responseBody = $response->getBody()->getContents();
                $responseJSON = $this->decodeJson($responseBody, true);

                if($errors = $responseJSON['errors'] ?? false) {
                    throw new QueryException($errors);
                }

                $responseData = $responseJSON['data'];
                return $responseData;
            }

            throw new HttpException('Bad content type', $this->getRequest(), $response);
        }
        throw new HttpException('Bad content type', $this->getRequest(), $response);
    }

    protected function responseIsJSON()
    {
        return $this->hasHeader('application/json');
    }
}
