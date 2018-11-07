<?php

namespace GraphQLClient\Traits;

use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\StreamFactory;

trait Stream
{
    /**
     * PSR-7 Stream creator
     *
     * @var Http\Message\StreamFactory
     */
    protected $streamFactory;

    /**
     * Get our StreamFactory or discover one
     *
     * @return Http\Message\StreamFactory
     */
    public function getStreamFactory(): StreamFactory
    {
        if (!($this->streamFactory instanceof StreamFactory)) {
            $this->setStreamFactory();
        }

        return $this->streamFactory;
    }

    /**
     * Set or discover a new factory
     *
     * @param StreamFactory|null $streamFactory
     */
    public function setStreamFactory(StreamFactory $streamFactory = null)
    {
        $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
    }
}
