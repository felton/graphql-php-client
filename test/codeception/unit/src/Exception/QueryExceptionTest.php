<?php

namespace GraphQLClient\Tests\src\Exception;

use GraphQLClient\Exception\QueryException;

/**
 * @coversDefaultClass GraphQLClient\Exception\QueryException
 */
class QueryExceptionTest extends \Codeception\Test\Unit
{
    /**
     * @var \GraphQLClient\Tests\UnitTester
     */
    protected $tester;

    /**
     * QueryException object
     *
     * @var \GraphQLClient\Exception\QueryException
     */
    protected $exception;

    /**
     * GraphQL error data
     *
     * @var array
     */
    protected $errors = [
        [
            'message' => 'Default Error Message',
            'locations' => [
                'line' => 2,
                'column' => 2,
            ],
            'path' => ['content', 'images', 1, 'url'],
            'extensions' => [],
        ],
        [
            'message' => 'Additional Error Message',
        ],
    ];

    /**
     * Successful data
     *
     * @var array
     */
    protected $data = [
        'content' => [
            'id' => 1234,
            'title' => 'Foo',
            'image' => [
                'url' => 'foo.com/img.jpg',
            ],
        ],
    ];

    /**
     * Response body
     *
     * @var string
     */
    protected $responseBody = '{"data":{"content":["id":123]}}';

    protected function _before()
    {
        $this->exception = new QueryException($this->errors, $this->data, $this->responseBody);
    }

    /**
     * Constructor grabs passed data, and calls parent with
     * the first error message.
     *
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $message = $this->errors[0]['message'] . ' - ' . $this->errors[1]['message'];

        verify($this->exception->getMessage())->equals($message);
    }

    /**
     * Return Locations column
     *
     * @covers ::getLocations
     * @covers ::errorColumn
     */
    public function testLocations()
    {
        verify($this->exception->getLocations())->equals([$this->errors[0]['locations']]);
    }

    /**
     * Return path column
     *
     * @covers ::getPath
     * @covers ::errorColumn
     */
    public function testPath()
    {
        verify($this->exception->getPath())->equals([$this->errors[0]['path']]);
    }

    /**
     * Return extensions column
     *
     * @covers ::getExtensions
     * @covers ::errorColumn
     */
    public function testExtensions()
    {
        verify($this->exception->getExtensions())->equals([$this->errors[0]['extensions']]);
    }

    /**
     * Return successful data
     *
     * @covers ::getData
     * @covers ::errorColumn
     */
    public function testData()
    {
        verify($this->exception->getData())->equals($this->data);
    }

    /**
     * Return response body
     *
     * @covers ::getResponseBody
     */
    public function testResponseBody()
    {
        verify($this->exception->getResponseBody())->equals($this->responseBody);
    }
}
