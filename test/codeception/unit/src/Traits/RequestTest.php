<?php

namespace GraphQLClient\Tests\Traits;

use Codeception\Util\ReflectionHelper;
use Codeception\Util\Stub;
use GraphQLClient\Traits\Request;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Http\Message\RequestFactory;
use Psr\Http\Message\RequestInterface;

/**
 * @coversDefaultClass GraphQLClient\Traits\Request
 */
class RequestTest extends \Codeception\Test\Unit
{
    /**
     * Codeception tester
     *
     * @var \GraphQLClient\Tests\UnitTester
     */
    protected $tester;

    /**
     * Mocked class that uses Request trait
     *
     * @var object
     */
    protected $_request;

    /**
     * Mocked RequestFactory
     *
     * @var object
     */
    protected $_messageFactory;

    /**
     * Create a mock Request class and message factory before each test case
     */
    protected function _before()
    {
        $this->_request = $this->tester->mockTrait(Request::class, [
            'getRequestFactory', 'getOptions', 'encodeJson', 'getStreamFactory',
        ]);

        $this->_messageFactory = $this->makeEmpty(RequestFactory::class, [
                'createRequest' => new GuzzleRequest('POST', 'foo.com'), ]);
    }

    /**
     * Test that we can build a POST request
     *
     * @covers ::buildRequest
     */
    public function testbuildRequestBuildsRequest()
    {
        $options = ['request' => ['method' => 'POST', 'headers' => ['header1' => 'bar']]];

        $streamFactory = $this->makeEmpty(\Http\Message\StreamFactory::class, [
                'createStream' => \GuzzleHttp\Psr7\stream_for('some-data'), ]);

        // setting method return values using PHPUnit since mixing with Stub::update causes issues
        $this->_request->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($options));

        /*
         * Mock the rest using `Stub::update`
         * This shouldn't completely work, but the code above might have fixed something.
         * @todo Investigate this.
         */
        Stub::update($this->_request, [
            'url' => 'foo.com',
            'encodeJson' => self::Once(),
            'getRequestFactory' => $this->_messageFactory,
            'getStreamFactory' => $streamFactory,
        ]);

        $request = $this->_request->buildRequest(['some-data']);

        verify($request)->isInstanceOf(RequestInterface::class);

        verify($request->getBody()->getContents())->equals('some-data');
    }

    /**
     * Test that we throw NotYetImplementedException when sending a query via GET
     *
     * @expectedException \GraphQLClient\Exception\NotYetImplementedException
     *
     * @covers ::buildRequest
     */
    public function testBuildRequestThrowsOnGET()
    {
        // setting method return values using PHPUnit since mixing with Stub::update causes issues
        $this->_request->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue(['request' => ['method' => 'GET']]));

        $this->_request->buildRequest(['foo']);
    }

    /**
     * Test that we return a RequestFactory
     *
     * @covers ::getRequestFactory
     */
    public function testRequestFactory()
    {
        // create an empty trait mock since $this->_request has an empty `getRequestFactory`
        $request = $this->tester->mockTrait(Request::class);

        Stub::update($request, [
            'requestFactory' => null,
        ]);

        $factory = ReflectionHelper::invokePrivateMethod($request, 'getRequestFactory');

        verify($factory)->isInstanceOf(RequestFactory::class);

        Stub::update($request, [
            'requestFactory' => $this->_messageFactory,
        ]);

        $factory = ReflectionHelper::invokePrivateMethod($request, 'getRequestFactory');

        verify($factory)->equals($this->_messageFactory);
    }

    /**
     * Test that we can retrieve the request object
     *
     * @covers ::getRequest
     */
    public function testGetRequest()
    {
        $request = $this->makeEmpty(RequestInterface::class);

        Stub::update($this->_request, [
            'request' => $this->makeEmpty(RequestInterface::class),
        ]);

        $requestProp = ReflectionHelper::readPrivateProperty($this->_request, 'request');

        verify($requestProp)->equals($request);

        $requestPropFromMethod = ReflectionHelper::invokePrivateMethod($this->_request, 'getRequest');

        verify($requestPropFromMethod)->equals($requestProp);
    }
}
