<?php

namespace GraphQLClient\Tests\src\Traits;

use GraphQLClient\Traits\Utils;

/**
 * @coversDefaultClass GraphQLClient\Traits\Utils
 */
class UtilsTest extends \Codeception\Test\Unit
{
    /**
     * @var \GraphQLClient\Tests\UnitTester
     */
    protected $tester;

    /**
     * Utils mock
     *
     * @var object
     */
    protected $utils;

    protected function _before()
    {
        $this->utils = $this->tester->mockTrait(Utils::class);
    }

    /**
     * Test that we can check a header name and value
     *
     * @covers ::hasHeader
     * @dataProvider headerProvider
     */
    public function testHasHeader($header, $headerToCheck, $expected)
    {
        list('name' => $name, 'value' => $value) = $header;
        $httpResponse = $this->tester->mockResponse()->withHeader($name, $value);

        // mocking separately because `getResponse` is not declared in `Utils`
        $utils = $this->tester->mockTrait(Utils::class, ['getResponse']);

        $utils->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($httpResponse));

        list('name' => $name, 'value' => $value) = $headerToCheck;

        verify($utils->hasHeader($value, $name))->equals($expected);
    }

    /**
     * dataProvider for `testHasHeader()`
     */
    public function headerProvider()
    {
        $default = [
            'value' => 'application/json',
            'name' => 'Content-Type',
        ];

        return [
            'Invalid' => [
                'header' => $default,
                'headerToCheck' => [
                    'value' => 'text/html',
                    'name' => 'Content-Type',
                ],
                'expected' => false,
            ],
           'Json header, default name of Content-Type' => [
                'header' => $default,
                'headerToCheck' => [
                    'value' => 'application/json',
                    'name' => 'Content-Type',
                ],
                'expected' => true,
            ],
            'Additional Headings' => [
                'header' => [
                    'value' => 'application/json; charset=utf-8',
                    'name' => 'Content-Type',
                ],
                'headerToCheck' => $default,
                'expected' => true,
            ],
            'Multiple Headings' => [
                'header' => [
                    'value' => [
                    'no-cache',
                    'no-store',
                ],
                    'name' => 'Cache-Control',
                ],
                'headerToCheck' => [
                    'value' => 'no-store',
                    'name' => 'Cache-Control',
                ],
                'expected' => true,
            ],
        ];
    }

    /**
     * Test that we can encode/decode with no errors.
     *
     * @covers ::encodeJson
     * @covers ::decodeJson
     * @dataProvider jsonProvider
     */
    public function testEncodeAndDecodeJsonWithNoErrors($value)
    {
        $json = $this->utils->encodeJson($value);

        verify($value)->equals($this->utils->decodeJson($json));
    }

    public function jsonProvider()
    {
        return [
            [true],
            [false],
            [999],
            [['foo' => 'bar']],
        ];
    }

    /**
     * Encoding an invalid value should throw an exception
     *
     * @expectedException \UnexpectedValueException
     */
    public function testEncodeJsonThrows()
    {
        $this->utils->encodeJson("\xB1\x31");
    }

    /**
     * Decoding an invalid json string should throw an exception
     *
     * @expectedException \UnexpectedValueException
     */
    public function testDecodeJsonThrows()
    {
        $this->utils->decodeJson('[[throw]');
    }
}
