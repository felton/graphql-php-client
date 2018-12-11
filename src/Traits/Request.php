<?php

namespace GraphQLClient\Traits;

use GraphQLClient\Exception\NotYetImplementedException;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Psr\Http\Message\RequestInterface;

trait Request
{
    use Stream;

    /**
     *  query request object
     *
     * @var Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     *  Request object factory
     *
     * @var \Http\Message\RequestFactory
     */
    protected $requestFactory = null;

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
        $options = $this->getOptions()['request'];

        if (($method = $options['method'] ?? false) && $method == 'GET') {
            /*
             * Handling GET methods is not implemented yet.
             *  example:
             *  $uri = $request->getUri();
             *  $request = $request->withUri($uri->withQuery(http_build_query($data)))
             */
            throw new NotYetImplementedException('Handling GET methods is not implemented yet');
        }

        $request = $this->getRequestFactory()->
            createRequest($options['method'], $this->url, $options['headers']);

        $body = $this->getStreamFactory()->createStream($this->encodeJson($data));

        $request = $request->withBody($body);

        return $request;
    }

    /**
     * Get/Creates a RequestFactory
     *
     * `MessageFactoryDiscovery` will find the appropriate RequestFactory or MessageFactory
     * we only need the factory for Requests.
     *
     * @return Http\Message\RequestFactory message factory
     */
    protected function getRequestFactory() : RequestFactory
    {
        if (!($this->requestFactory instanceof RequestFactory)) {
            $this->setRequestFactory();
        }

        return $this->requestFactory;
    }

    /**
     * Sets or discovers a new RequestFactory
     *
     * @param RequestFactory|null $requestFactory request factory
     */
    public function setRequestFactory(RequestFactory $requestFactory = null)
    {
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    /**
     * Returns the created HTTP request
     *
     * @return mixed Psr\Http\Message\RequestInterface or null if not created
     */
    public function getRequest()
    {
        return $this->request;
    }
}
