<?php

namespace GraphQLClient\Traits;

use Http\Message\MessageFactory\GuzzleMessageFactory;
use Psr\Http\Message\RequestInterface;

trait Request
{
    /**
     * [$request description]
     *
     * @var [type]
     */
    protected $request;

    /**
     * [$messageFactory description]
     *
     * @var null
     */
    protected $messageFactory = null;

    /**
     * [buildRequest description]
     *
     * @param [type] $data [description]
     *
     * @return [type] [description]
     */
    public function buildRequest($data) : RequestInterface
    {
        $options = $this->getOptions();

        $request = $this->getMessageFactory()->
            createRequest($options['method'], $this->url, $options['headers']);

        if (($method = $options['method'] ?? false) && $method == 'GET') {
            $uri = $request->getUri();
            $request = $request->withUri($uri->withQuery(http_build_query($data)));
        } else {
            $request = $request->withBody(\GuzzleHttp\Psr7\stream_for(json_encode($data)));
        }

        return $request;

        // return $this->getMessageFactory()->
        //     createRequest($this->getMethod(), $this->getUrl(), $this->getHeaders());
    }

    protected function getMessageFactory()
    {
        return $this->messageFactory ?? new GuzzleMessageFactory();
    }
}
