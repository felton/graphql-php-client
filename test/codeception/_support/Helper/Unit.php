<?php
namespace GraphQLClient\Tests\Helper;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Unit extends \Codeception\Module
{
    /**
     * An instance of the unit test
     *
     * @var \Codeception\TestInterface
     */
    protected $unitTest;

    /**
     * Actions to peform before a unit test runs. In this case, keep an instance of the
     * unit test to use in other methods here.
     *
     * @param \Codeception\TestInterface $test The unit test curently running
     */
    public function _before(\Codeception\TestInterface $test)
    {
        $this->unitTest = $test;
    }

    /**
     * Mock a trait class
     *
     * @param string $className     Trait name
     * @param array  $methodsToMock Methods to mock, if any
     *
     * @return object Mock object with trait `$className`
     */
    public function mockTrait($className = '', array $methodsToMock = [])
    {
        return $this->unitTest
            ->getMockBuilder($className)
            ->setMethods($methodsToMock)
            ->getMockForTrait();
    }

    public function mockResponse($status = 200, $headers = [], $data = '', $json = false)
    {
        if($data) {
            $data = $json ? json_encode($data) : $data;
            $data = \GuzzleHttp\Psr7\stream_for($data);
        }
        return new GuzzleResponse($status, $headers, $data);
    }

    public function setProperty($class, $property, $value)
    {
        $property = new \ReflectionProperty(get_class($class), $property);
        $property->setAccessible(true);

        $property->setValue($class, $value);


        return $property;
    }
}
