<?php

namespace GraphQLClient\Tests\src\Traits;

use Codeception\Util\Stub;
use GraphQLClient\Traits\Stream;
use Http\Message\StreamFactory;

/**
 * @coversDefaultClass GraphQLClient\Traits\Stream
 */
class StreamTest extends \Codeception\Test\Unit
{
    /**
     * Codeception tester
     *
     * @var \GraphQLClient\Tests\UnitTester
     */
    protected $tester;

    /**
     * Mocked class that uses Stream trait
     *
     * @var object
     */
    protected $_stream;

    /**
     * Mock Stream trait and StreamFactory
     */
    protected function _before()
    {
        $this->_stream = $this->tester->mockTrait(Stream::class);

        $this->_streamFactory = $this->makeEmpty(StreamFactory::class);
    }

    /**
     * Test that we return a proper StreamFacory and generate one if it
     * doesn't exist
     *
     * @covers ::getStreamFactory
     * @covers ::setStreamFactory
     */
    public function testGetStreamFactoryReturnsFactory()
    {
        Stub::update($this->_stream, [
            'streamFactory' => false,
        ]);

        $factory = $this->_stream->getStreamFactory();

        verify($factory)->isInstanceOf(StreamFactory::class);

        Stub::update($this->_stream, [
            'streamFactory' => $this->_streamFactory,
        ]);

        $factory = $this->_stream->getStreamFactory();

        verify($factory)->equals($this->_streamFactory);
    }
}
