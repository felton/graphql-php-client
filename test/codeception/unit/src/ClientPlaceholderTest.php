<?php

namespace GraphQLClient\Tests;

use GraphQLClient\ClientPlaceholder;

/**
 *  @coversDefaultClass GraphQLClient\ClientPlaceholder
 */
class ClientPlaceholderTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * [testSomeFeature description]
     *
     * @covers ::__construct()
     */
    public function testSomeFeature()
    {
        $placeholder = new ClientPlaceholder();

        verify($placeholder->didRun)->true();
    }
}
