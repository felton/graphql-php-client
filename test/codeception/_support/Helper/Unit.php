<?php
namespace GraphQLClient\Tests\Helper;

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
     * @param string $className   Trait name
     * @param array  $methodNames Methods to mock, if any
     *
     * @return object Mock object with trait `$className`
     */
    public function mockTrait($className = '', array $methodNames = [])
    {
        return $this->unitTest
            ->getMockBuilder($className)
            ->setMethods($methodNames)
            ->getMockForTrait();
    }
}
