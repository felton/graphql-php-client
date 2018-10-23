<?php

namespace GraphQLClient\Tests;

use GraphQLClient\Client;
use Http\Mock\Client as MockClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Codeception\Util\ReflectionHelper;

/**
 *  @coversDefaultClass GraphQLClient\Client
 */
class ClientTest extends \Codeception\Test\Unit
{
    protected $tester;

    protected $_client;

    protected function _before()
    {
        $this->_client = new Client('foo.com');
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $client = $this->make(Client::class, [
            'setOptions' => self::Once(),
            'getOptions' => ['bar'],
            'buildClient' => self::Once(),
        ]);

        $client->__construct('foo', []);
        verify($client->getUrl())->equals('foo');
        verify($client->getOptions())->equals(['bar']);
    }

    /**
     * @covers ::resolveOptions
     * @dataProvider optionProvider
     */
    public function testOptionsGetResolved($options, $expected)
    {
        $resolvedOptions = ReflectionHelper::invokePrivateMethod($this->_client, 'resolveOptions', [$options]);

        verify($resolvedOptions)->equals($expected);
    }

    public function optionProvider()
    {
        $defaults = $defaultGET = $defaultGraphQL = [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        $defaultGET['method'] = 'GET';

        $defaultGraphQL['headers']['Content-Type'] = 'application/graphql';

        return [
            'Empty options' => [
                'options' => [],
                'expected' => $defaults,
            ],
            'Single Option same default' => [
                'options' => ['method' => 'POST'],
                'expected' => $defaults,
            ],
            'Single option, GET method' => [
                'options' => ['method' => 'GET'],
                'expected' => $defaultGET,
            ],
            'Single option, new content-type' => [
                'options' => ['headers' => ['Content-Type' => 'application/graphql']],
                'expected' => $defaultGraphQL,
            ],
        ];
    }

    /**
     * @covers ::buildClient
     */
    public function testClientGetsBuilt()
    {
        $client = $this->make('GraphQLClient\\Client', [
            'buildClient' => self::Once(function ($options) {
            }),
        ]);

        $method = new \ReflectionMethod(get_class($client), 'buildClient');

        $method->setAccessible(true);

        $method->invokeArgs($client, []);
    }

    /**
     * @covers ::query
     */
    public function testQueryBuildsAndSendsRequests()
    {
        $mockHttpClient = new MockClient();
        $success = $this->makeEmpty(ResponseInterface::class, [
            'getStatusCode' => 200,
        ]);
        $mockHttpClient->addResponse($success);

        $client = $this->make('GraphQLClient\\Client', [
            'buildRequest' => $this->makeEmpty(RequestInterface::class),
            'httpClient' => $mockHttpClient,
            'handleResponse' => self::Once(),
        ]);

        $client->query('foo');

        $response = $client->getResponse();
        verify($response)->isInstanceOf(ResponseInterface::class);
        verify($response->getStatusCode())->equals(200);
    }

    /**
     * @covers ::query
     * @dataProvider queryProvider
     */
    public function testQueryBuildsDataArrayAndSends($query, $variables, $expected)
    {
        $request = $this->makeEmpty(RequestInterface::class);
        $mockHttpClient = new MockClient();

        $client = $this->make('GraphQLClient\\Client', [
            'buildRequest' => function ($data) use ($expected, $request) {
                verify($data)->equals($expected);
                return $request;
            },
            'httpClient' => $mockHttpClient,
            'handleResponse' => self::Once(),
        ]);

        $client->query($query, $variables);
    }

    public function queryProvider()
    {
        $query = '{id}';
        return [
            'empty' => [
                'query' => '',
                'variables' => [],
                'expected' => [
                    'query' => '',
                ],
            ],
            'standard' => [
                'query' => $query,
                'variables' => ['foo' => 1],
                'expected' => [
                    'query' => $query,
                    'variables' => ['foo' => 1],
                ],
            ],

        ];
    }

    /**
     * @covers ::query
     * @expectedException RuntimeException
     */
    public function testQueryThrowsException()
    {
        $mockHttpClient = new MockClient();

        $mockHttpClient->addException(new \Http\Client\Exception\TransferException());

        $client = $this->make(Client::class, [
            'httpClient' => $mockHttpClient,
            'buildRequest' => $this->makeEmpty(RequestInterface::class),
            'handleResponse' => self::Never(),

        ]);

        $client->query('foo');
    }

    /**
     * @covers ::getOptions
     * @covers ::getUrl
     */
    public function testGetters()
    {
        $client = new Client('foo.com', []);

        verify($client->getOptions())->equals([
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $url = $this->_client->getUrl();

        verify($url)->equals('foo.com');
    }

    /**
     * @covers ::setOptions
     * @covers ::setUrl
     */
    public function testSetters()
    {
        $client = new Client('foo.com', []);

        $client->setUrl('bar.com');

        verify($client->getUrl())->equals('bar.com');
    }
}
