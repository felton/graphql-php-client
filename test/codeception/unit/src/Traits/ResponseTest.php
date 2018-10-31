<?php

namespace GraphQLClient\Tests\Traits;

use GraphQLClient\Traits\Response as ResponseTrait;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Codeception\Util\ReflectionHelper;
use Codeception\Util\Stub;

/**
 * @coversDefaultClass GraphQLClient\Traits\Response
 */
class ResponseTest extends \Codeception\Test\Unit
{
    /**
     * Codeception tester
     *
     * @var \Codeception\Module\Unit
     */
    protected $tester;

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
     * testResponseGetsHandled test needs to be refactored!
     *
     * @covers ::handleResponse
     */
    public function testResponseGetsHandled()
    {
        $response = $this->tester->mockTrait(ResponseTrait::class, ['getResponse', 'getRequest', 'responseIsJSON']);

        $jsonData = ['data' => ['foo']];
        $jsonResponse = $this->tester->mockResponse(200, [], ['data' => ['foo']], true);

        /*
            @todo refactor!
         $mResponse = $this->make(GuzzleResponse::class, [
            'getBody' => $this->makeEmpty(StreamInterface::class, [
                'getContents' => Stub::consecutive(['data' => ['foo']])
            ])
        ]);*/

        Stub::update($response, [
            'getResponse' => Stub::consecutive($jsonResponse, false, $jsonResponse),
            'getRequest' => function () {
                return new \GuzzleHttp\Psr7\Request('POST', 'foo.com');
            },
            'responseIsJSON' => Stub::consecutive(true, true, false, true),
        ]);

        $data = ReflectionHelper::invokePrivateMethod($response, 'handleResponse');

        verify($data)->equals(['foo']);

        return $response;
    }

    /**
     * @expectedException \Http\Client\Exception\TransferException
     * @covers ::handleResponse
     * @depends testResponseGetsHandled
     */
    public function testTransferExceptionIsThrown($response)
    {
        ReflectionHelper::invokePrivateMethod($response, 'handleResponse');

        return $response;
    }
}
