<?php

namespace GraphQLClient\Tests\src\Traits;

use Codeception\Util\ReflectionHelper;
use Codeception\Util\Stub;
use GraphQLClient\Traits\Stream;
use Http\Message\StreamFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;

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
     * Mocked StreamFactory
     *
     * @var StreamFactory
     */
    protected $_streamFactory;

    /**
     * Mock Stream trait and StreamFactory
     */
    protected function _before()
    {
        $this->_stream = $this->tester->mockTrait(Stream::class);

        $this->tester->setProperty($this->_stream, 'streamFactory', false);

        $this->_streamFactory = $this->makeEmpty(StreamFactory::class);
    }

    /**
     * Test that we generate a StreamFactory if it
     * doesn't exist when we call `getStreamFactory()`
     *
     * We get a TypeError because null is returned, but `setStreamFactory`
     * should called
     *
     * @covers ::getStreamFactory
     * @expectedException TypeError
     */
    public function testGetStreamFactorySetsFactoryIfMissing()
    {
        $this->_stream = $this->tester->mockTrait(Stream::class, ['setStreamFactory']);

        Stub::update($this->_stream, [
            'setStreamFactory' => self::Once(),
        ]);

        $this->_stream->getStreamFactory();
    }

    /**
     * Test that we return a proper StreamFactory
     *
     * @covers ::getStreamFactory
     */
    public function testGetStreamFactoryReturnsFactory()
    {
        $this->tester->setProperty($this->_stream, 'streamFactory', $this->_streamFactory);

        $factory = $this->_stream->getStreamFactory();

        verify($factory)->equals($this->_streamFactory);
    }

    /**
     * Test that a StreamFactory is discovered when calling `setStreamFactory()`
     *
     * @covers ::setStreamFactory
     */
    public function testSetStreamFactorySetsFactoryWhenCalled()
    {
        // Try to discover a StreamFactory
        $this->_stream->setStreamFactory();

        $factory = ReflectionHelper::readPrivateProperty($this->_stream, 'streamFactory');

        // StreamFactoryDiscovery should find the Guzzle PSR7 implementation included with these tests
        verify($factory)->isInstanceOf(GuzzleStreamFactory::class);
    }

    /**
     * Test that we properly assign a StreamFactory when passed to `setStreamFactory()`
     *
     * @covers ::setStreamFactory
     */
    public function testSetStreamFactorySetsFactoryWhenOneIsPassed()
    {
        // Try to discover a StreamFactory
        $this->_stream->setStreamFactory($this->_streamFactory);

        $factory = ReflectionHelper::readPrivateProperty($this->_stream, 'streamFactory');

        verify($factory)->isInstanceOf(StreamFactory::class);

        verify($factory)->equals($this->_streamFactory);
    }
}
