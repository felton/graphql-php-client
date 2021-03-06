<?php

namespace GraphQLClient\Tests\Traits;

use Codeception\Util\ReflectionHelper;
use Codeception\Util\Stub;
use GraphQLClient\Traits\Response as ResponseTrait;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;

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
     * Mocked class that uses Response trait
     *
     * @var object
     */
    protected $_response;

    /**
     * Actions to run before each test case
     */
    protected function _before()
    {
        $this->_response = $this->tester->mockTrait(ResponseTrait::class, [
            'getResponse', 'getRequest', 'responseIsJSON',
        ]);
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

        verify($response->getResponse())->false();

        Stub::update($response, [
            'response' => new GuzzleResponse(200),
        ]);

        verify($response->getResponse())->isInstanceOf(ResponseInterface::class);
    }

    /**
     * Test that responses from GraphQL servers get processed
     *
     * @covers ::handleResponse
     * @dataProvider handleResponseProvider
     */
    public function testResponseGetsHandled($data, $expected, $json = true)
    {
        $response = $this->tester->mockResponse(200, [], $data, true);
        Stub::update($this->_response, [
            'getResponse' => $response,
            'responseIsJSON' => true,
        ]);

        $data = ReflectionHelper::invokePrivateMethod($this->_response, 'handleResponse', [$json]);

        verify($data)->equals($expected);
    }

    /**
     * Provider for `testResponseGetsHandled()`
     */
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
                'expected' => '{"data":["foo"]}',
                'json' => false,
            ],
        ];
    }

    /**
     * Test that we throw an HttpException when no response is found
     *
     * @expectedException \Http\Client\Exception\HttpException
     * @covers ::handleResponse
     */
    public function testHttpExceptionIsThrown()
    {
        $httpResponse = $this->tester->mockResponse(400);
        $request = new GuzzleRequest('POST', 'foo.com');

        // Since `getRequest` isn't declared in this trait, using Codeception::Stub
        // has issues, so lets use pure PHPUnit!
        $this->_response->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->_response->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($httpResponse));

        ReflectionHelper::invokePrivateMethod($this->_response, 'handleResponse');
    }

    /**
     * Test that TransferException when we receive a unsuccessful response code
     *
     * @expectedException \Http\Client\Exception\TransferException
     * @covers ::handleResponse
     */
    public function testTransferExceptionIsThrown()
    {
        ReflectionHelper::invokePrivateMethod($this->_response, 'handleResponse');
    }

    /**
     * Do we have an error in our query? Find out in the next QueryException!
     *
     * @expectedException \GraphQLClient\Exception\QueryException
     * @covers ::handleResponse
     */
    public function testQueryExceptionIsThrown()
    {
        $data = ['errors' => ['foo']];
        $httpResponse = $this->tester->mockResponse(200, [], $data, true);
        Stub::update($this->_response, [
            'getResponse' => $httpResponse,
            'responseIsJSON' => true,
        ]);

        ReflectionHelper::invokePrivateMethod($this->_response, 'handleResponse');
    }

    /**
     * Test that we can detect if the response header is
     * `Content-Type: application/json` and its variants
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

    /**
     * dataProvider for `testresponseIsJSON`
     */
    public function jsonResponseProvider()
    {
        return [
            'Full headers' => [
                'header' => [
                    'name' => 'Content-Type',
                    'values' => 'application/json',
                ],
                'expected' => true,
            ],
            'Additional UTF-8 charset appended' => [
                'header' => [
                    'name' => 'Content-Type',
                    'values' => 'application/json; charset=UTF-8',
                ],
                'expected' => true,
            ],
            'No headers' => [
                'header' => [
                    'name' => '',
                    'values' => '',
                ],
                'expected' => false,
            ],
        ];
    }
}
