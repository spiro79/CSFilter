<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 9:01 PM
 */

use DE\CSFilter\Filter;

class FilterTest extends PHPUnit_Framework_TestCase
{
    public function tearDown() {
        Filter::tearDown();
    }

    /**
     * Mocks the external library object dependency
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExternalLibraryMock() {
        $mock = $this->getMockBuilder('DE\CSFilter\ExternalLib\ExternalLibInterface')
                     ->setMethods(['clean','filterString','filterRich','filterCustom'])
                     ->getMock();
        return $mock;
    }

    /**
     * Test the instance getter
     */
    public function testGetInstance() {
        $expectedInstance = 'DE\CSFilter\Filter';
        $filterInstance = Filter::getInstance();
        $instanceOfExpectedClass = $filterInstance instanceof $expectedInstance;
        $this->assertTrue($instanceOfExpectedClass);
    }

    /**
     * Test the Filter object is not instantiable
     */
    public function testSingleton() {
        $reflectionInstance = new ReflectionClass('DE\CSFilter\Filter');
        $isInstantiable = $reflectionInstance->isInstantiable();
        $this->assertFalse($isInstantiable);
    }

    /**
     * Test setting the external library
     */
    public function testSetExternalLib() {
        $mock = $this->getExternalLibraryMock();
        $filterInstance = Filter::getInstance();
        $response = $filterInstance->setExternalLib($mock);
        $expectedInstance = 'DE\CSFilter\Filter';
        $this->assertInstanceOf($expectedInstance,$response);
    }

    /**
     * Test that no other interfaces can be set to the instance
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetExternalLibWithWrongInterface() {
        $wrongObj = new stdClass();
        $filterInstance = Filter::getInstance();
        $response = $filterInstance->setExternalLib($wrongObj);
    }

    /**
     * Test the external lib getter
     */
    public function testGetExternalLib() {
        $mock = $this->getExternalLibraryMock();
        $filterInstance = Filter::getInstance();
        $filterInstance->setExternalLib($mock);
        $externalLib = $filterInstance->getExternalLib();
        $expectedInterface = 'DE\CSFilter\ExternalLib\ExternalLibInterface';
        $this->assertInstanceOf($expectedInterface,$externalLib);
    }

    /**
     * Tests the exception thrown when the external lib is not set
     * @expectedException \DE\CSFilter\Exception
     */
    public function testGetExternalLibWhenUnset() {
        $filterInstance = Filter::getInstance();
        $externalLib = $filterInstance->getExternalLib();
    }

    /**
     * Test the allowed filters getter
     */
    public function testGetAllowedFilters() {
        $reflectionClass = new ReflectionClass('DE\CSFilter\FilterInterface');
        $constants = $reflectionClass->getConstants();
        $expectedAllowedFilters = array_values($constants);
        $filterInstance = Filter::getInstance();
        $allowedFilters = $filterInstance->getAllowedFilters();
        $this->assertEquals($expectedAllowedFilters, $allowedFilters);
    }

    /**
     * Test a string to be filtered to boolean
     */
    public function testFilterBoolean() {
        $dirtyVal = 'This will be casted to true';
        $expectedResult = true;
        $filterInstance = Filter::getInstance();
        $cleanVal = $filterInstance->filterBoolean($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
        $dirtyFalseVal = 0; //Will be casted to false
        $expectedFalseResult = false;
        $cleanFalse = $filterInstance->filterBoolean($dirtyFalseVal);
        $this->assertEquals($expectedFalseResult, $cleanFalse);
    }
}