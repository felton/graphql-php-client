<?php

namespace GraphQLClient\Tests\Traits;

use GraphQLClient\Traits\Response as ResponseTrait;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Codeception\Util\ReflectionHelper;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Codeception\Util\Stub;

/**
 * @coversDefaultClass GraphQLClient\Traits\Response
 */
class ResponseTest extends \Codeception\Test\Unit
{
    /**
     * Codeception tester
     *
     * @var \GraphQLClient\Tests\UnitTester
     */
    protected $tester;

    /**
     * [$response description]
     *
     * @var [type]
     */
    protected $response;

    /**
     * [_before description]
     */
    protected function _before()
    {
        $this->response = $this->tester->mockTrait(ResponseTrait::class, [
            'getResponse', 'getRequest', 'responseIsJSON',
        ]);

        // $this->response->expects($this->any())
        //     ->method('getResponse')
        //     ->will($this->returnValue(123));

        // \Codeception\Util\Debug::debug($this->response->getResponse());

        // $this->response->expects($this->any())
        //     ->method('getResponse')
        //     ->will($this->returnValue(1234));

        // \Codeception\Util\Debug::debug($this->response->getResponse());

        \Codeception\Util\Debug::debug('Yo!!');
    }

    /**
     * Tests that getResponse() returns the fetched response
     *
     * @covers ::getResponse
     */
    public function testGetResponseReturnsResponses()
    {
        $response = $this->tester->mockTrait(ResponseTrait::class);

        Stub::update($response, [
            'response' => false,
        ]);

        //\Codeception\Util\Debug::debug(\Codeception\Util\ReflectionHelper::readPrivateProperty($response, 'response'));

        verify($response->getResponse())->false();

        Stub::update($response, [
            'response' => new GuzzleResponse(200),
        ]);

        verify($response->getResponse())->isInstanceOf(ResponseInterface::class);
    }

    /**
     * testResponseGetsHandled test needs to be refactored!
     *
     * @covers ::handleResponse
     * @dataProvider handleResponseProvider
     */
    public function testResponseGetsHandled($data, $expected)
    {
        //$response = $this->tester->mockTrait(ResponseTrait::class, ['getResponse', 'getRequest', 'responseIsJSON']);

        //$jsonData = ['data' => ['foo']];
        //$jsonResponse = $this->tester->mockResponse(200, [], ['data' => ['foo']], true);

        /*
            @todo refactor!
         $mResponse = $this->make(GuzzleResponse::class, [
            'getBody' => $this->makeEmpty(StreamInterface::class, [
                'getContents' => Stub::consecutive(['data' => ['foo']])
            ])
        ]);*/
        $response = $this->tester->mockResponse(200, [], $data, true);
        Stub::update($this->response, [
            'getResponse' => $response,
            'responseIsJSON' => true,
        ]);

        $data = ReflectionHelper::invokePrivateMethod($this->response, 'handleResponse');

        verify($data)->equals($expected);
    }

    public function handleResponseProvider()
    {
        $data = ['data' => ['foo']];

        return [
            'Response as JSON' => [
                'response' => $data,
                'expected' => ['foo'],
            ],
            'Response as Text' => [
                'response' => $data,
                'expected' => ['foo'],
            ],
        ];
    }

    /**
     * @expectedException \Http\Client\Exception\HttpException
     * @covers ::handleResponse
     */
    public function testHttpExceptionIsThrown()
    {
        $httpResponse = $this->tester->mockResponse(400);
        $request = new GuzzleRequest('POST', 'foo.com');//$this->makeEmpty(RequestInterface::class);
        // \Codeception\Util\Debug::debug((bool)($request instanceof Psr\Http\Message\RequestInterface));

        //$this->response = $this->tester->mockTrait(ResponseTrait::class, ['getRequest']);

        $this->response->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->response->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($httpResponse));

        // Stub::update( $this->response, [
        //     'response' => $httpResponse,
        //     //'getRequest' => $request,
        //     //'request' => $request,
        // ]);

        // \Codeception\Util\Debug::debug($request);
        ReflectionHelper::invokePrivateMethod($this->response, 'handleResponse');
    }

    /**
     * @expectedException \Http\Client\Exception\TransferException
     * @covers ::handleResponse
     */
    public function testTransferExceptionIsThrown()
    {
        ReflectionHelper::invokePrivateMethod($this->response, 'handleResponse');
    }

    /**
     * @expectedException \GraphQLClient\Exception\QueryException
     * @covers ::handleResponse
     */
    public function testQueryExceptionIsThrown()
    {
        $data = ['errors' => ['foo']];
        $httpResponse = $this->tester->mockResponse(200, [], $data, true);
        Stub::update($this->response, [
            'getResponse' => $httpResponse,
            'responseIsJSON' => true,
        ]);

        ReflectionHelper::invokePrivateMethod($this->response, 'handleResponse');
    }

    /**
     * [providerthingy description]
     *
     * @covers ::responseIsJSON
     * @dataProvider jsonResponseProvider
     */
    public function testresponseIsJSON($header, $expected)
    {
        $response = $this->tester->mockTrait(ResponseTrait::class);

        list('name' => $name, 'values' => $value) = $header;

        $httpResponse = $this->tester->mockResponse(200)->withHeader($name, $value);
        Stub::update($response, [
            'response' => $httpResponse,
        ]);

        $value = ReflectionHelper::invokePrivateMethod($response, 'responseIsJSON');
        verify($value)->equals($expected);
    }

    public function jsonResponseProvider()
    {
        return [
            'One' => [
                'header' => [
                    'name' => 'Content-Type',
                    'values' => 'application/json',
                ], //['Content-Type' => 'application/json'],
                'expected' => true,
            ],
            'Two' => [
                'header' => [
                    'name' => '',
                    'values' => '',
                ],
                'expected' => false,
            ],
        ];
    }
}
