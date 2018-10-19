<?php

namespace GraphQLClient\Tests;

use GraphQLClient\Client;
use GraphQLClient\Traits\Response;

/**
 *  @coversDefaultClass GraphQLClient\Client
 */
class ClientTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        $this->_client = new Client('foo.com');
    }

    protected function _after()
    {
    }

    /**
     * [testSomeFeature description]
     *
     * @covers ::getOptions
     */
    public function testSomeFeature()
    {
        $client = $this->make('GraphQLClient\\Client', [
            'getOptions' => 'foo',
        ]);

        $m = $this->getMockForTrait(Response::class);
        //\Codeception\Util\Debug::debug($m);

        verify($this->_client->getOptions())->notEquals([]);
    }

    /**
     * [testConstructor description]
     *
     * @covers ::__construct
     */
    public function testConstructor()
    {
    }

    /**
     * [testQueryBuildsDataArray description]
     *
     * @covers ::query
     */
    public function testQueryBuildsDataArray()
    {
        // $client = $this->make('GraphQLClient\\Client', [
        //     'buildRequest' =>
        // ]);
    }
}
