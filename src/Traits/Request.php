<?php

namespace GraphQLClient\Traits;

use Http\Message\MessageFactory\GuzzleMessageFactory;
use Psr\Http\Message\RequestInterface;
use GraphQLClient\Exception\NotImplementedException;

trait Request
{
    /**
     *  query request object
     *
     * @var Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     *  Request object factory
     *
     * @var \Http\Message\MessageFactory
     */
    protected $messageFactory = null;

    /**
     * Build our query request
     *
     * @param array $data Array with query and variables to send to a
     *                    GraphQL Server
     *
     * @return Psr\Http\Message\RequestInterface GraphQL Request
     */
    public function buildRequest($data) : RequestInterface
    {
        $options = $this->getOptions();

        $request = $this->getMessageFactory()->
            createRequest($options['method'], $this->url, $options['headers']);

        if (($method = $options['method'] ?? false) && $method == 'GET') {

            /*
             * Handling GET methods is not implemented yet.
             *  example:
             *  $uri = $request->getUri();
             *  $request = $request->withUri($uri->withQuery(http_build_query($data)))
             */
            throw new NotImplementedException();
        }
        $request = $request->withBody(\GuzzleHttp\Psr7\stream_for(json_encode($data)));

        return $request;
    }

    /**
     * Get/Creates a MessageFactory
     *
     * @return Http\Message\MessageFactory\GuzzleMessageFactory message factory
     */
    protected function getMessageFactory()
    {
        return $this->messageFactory ?? new GuzzleMessageFactory();
    }
}
