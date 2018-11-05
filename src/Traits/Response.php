<?php
namespace GraphQLClient\Traits;

use GraphQLClient\Exception\QueryException;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\TransferException;
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
     * @param boolean $json Return successful response as JSON
     *
     * @throws QueryException if there is an error in the query response.
     * @throws HttpException if the status code of the response is not successful.
     *
     * @return string Response body
     */
    protected function handleResponse($json = true)
    {
        $response = $this->getResponse();
        $responseBody = '';

        if (!$response) {
            throw new TransferException('Response does not exist.');
        }

        if ($response->getStatusCode() === 200 && $this->responseIsJSON()) {
            // Check for application/json header
            // Usually the error in this case comes from the json parser if text/html is returned
            // Let's check the response so we can be more clear with our error message
            $responseBody = $response->getBody()->getContents();
            $responseJSON = $this->decodeJson($responseBody);
            $successfulData = $responseJSON['data'] ?? [];

            if ($errors = $responseJSON['errors'] ?? false) {
                // throw an exception with errors and any other successful data
                // returned before the error occurred, if any
                throw new QueryException($errors, $successfulData);
            }

            return $json ? $successfulData : $responseBody;
        }

        // Provide request and response to the user on any other error for inspection
        throw new HttpException('Bad content type', $this->getRequest(), $response);
    }

    /**
     * Helper function to check if response is JSON
     *
     * @return boolean TRUE if response is JSON
     */
    protected function responseIsJSON()
    {
        return $this->hasHeader('application/json');
    }
}
