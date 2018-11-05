<?php

namespace GraphQLClient\Tests\Traits;

use GraphQLClient\Traits\Request;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Psr\Http\Message\RequestInterface;
use \Http\Message\MessageFactory;
use Codeception\Util\Stub;
use Codeception\Util\ReflectionHelper;

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
     * Mocked Request class
     *
     * @var object
     */
    protected $_request;

    /**
     * Mocked MessageFactory
     *
     * @var object
     */
    protected $_messageFactory;

    /**
     * Create a mock Request class and message factory before each test case
     */
    protected function _before()
    {
        $this->_request = $this->getMockBuilder(Request::class)
                               ->setMethods(['getMessageFactory', 'getOptions'])
                               ->getMockForTrait();

        $this->_messageFactory = $this->makeEmpty(MessageFactory::class, [
                'createRequest' => new GuzzleRequest('POST', 'foo.com'), ]);
    }

    /**
     * Test that we can build a POST request
     *
     * @covers ::buildRequest
     */
    public function testbuildRequestBuildsRequest()
    {
        $request = $this->_request;

        // setting method return values using PHPUnit since mixing with Stub::update causes issues
        $request->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue(['method' => 'POST', 'headers' => ['header1' => 'bar']]));

        Stub::update($request, [
            'url' => 'foo.com',
            'getMessageFactory' => $this->_messageFactory,
        ]);

        $r = $request->buildRequest(['foo']);

        verify($r)->isInstanceOf(RequestInterface::class);
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
        $request = $this->_request;

        // setting method return values using PHPUnit since mixing with Stub::update causes issues
        $request->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue(['method' => 'GET']));

        $request->buildRequest(['foo']);
    }

    /**
     * Test that we return a messageFactory
     *
     * @covers ::getMessageFactory
     */
    public function testMessageFactory()
    {
        // create empty trait mock since _request has an empty `getMessageFactory`
        $request = $this->getMockBuilder(Request::class)
                               ->getMockForTrait();

        Stub::update($request, [
            'messageFactory' => null,
        ]);

        $factory = ReflectionHelper::invokePrivateMethod($request, 'getMessageFactory');

        verify($factory)->isInstanceOf(MessageFactory::class);

        Stub::update($request, [
            'messageFactory' => $this->_messageFactory,
        ]);

        $factory = ReflectionHelper::invokePrivateMethod($request, 'getMessageFactory');

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
