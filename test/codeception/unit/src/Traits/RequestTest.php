<?php

namespace GraphQLClient\Tests\Traits;

use GraphQLClient\Traits\Request;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Psr\Http\Message\RequestInterface;
use \Http\Message\MessageFactory;
use Codeception\Util\Stub;
use Codeception\Util\ReflectionHelper;

class RequestTest extends \Codeception\Test\Unit
{
    protected $tester;

    protected function _before()
    {
        $this->_request = $this->getMockBuilder(Request::class)
                               ->setMethods(['getMessageFactory', 'getOptions'])
                               ->getMockForTrait();

        $this->_messageFactory = $this->makeEmpty(MessageFactory::class, [
                'createRequest' => new GuzzleRequest('POST', 'foo.com'), ]);
    }

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
     * [testBuildRequestThrowsOnGET description]
     *
     * @expectedException \GraphQLClient\Exception\NotYetImplementedException
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
}
