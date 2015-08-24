<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 9:01 PM
 */

use DE\CSFilter\Filter;

/**
 * Class FilterTest
 */
class FilterTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Filter::tearDown();
    }

    /**
     * Mocks the external library object dependency
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExternalLibraryMock()
    {
        $mock = $this->getMockBuilder('DE\CSFilter\ExternalLib\ExternalLibInterface')
            ->setMethods(['filterString', 'filterRich', 'filterCustom'])
            ->getMock();
        $mock->method('filterString')
            ->willReturn('Valid string');
        $mock->method('filterRich')
            ->willReturn('<p><strong>Valid string</strong></p>');
        $mock->method('filterCustom')
            ->willReturn('Valid string to <a href="http://example.com">http://example.com</a>');
        return $mock;
    }

    /**
     * Test the instance getter
     */
    public function testGetInstance()
    {
        $expectedInstance = 'DE\CSFilter\Filter';
        $filterInstance = Filter::getInstance();
        $instanceOfExpectedClass = $filterInstance instanceof $expectedInstance;
        $this->assertTrue($instanceOfExpectedClass);
    }

    /**
     * Test the Filter object is not instantiable
     */
    public function testSingleton()
    {
        $reflectionInstance = new ReflectionClass('DE\CSFilter\Filter');
        $isInstantiable = $reflectionInstance->isInstantiable();
        $this->assertFalse($isInstantiable);
    }

    /**
     * Test setting the external library
     */
    public function testSetExternalLib()
    {
        $mock = $this->getExternalLibraryMock();
        $filterInstance = Filter::getInstance();
        $response = $filterInstance->setExternalLib($mock);
        $expectedInstance = 'DE\CSFilter\Filter';
        $this->assertInstanceOf($expectedInstance, $response);
    }

    /**
     * Test that no other interfaces can be set to the instance
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetExternalLibWithWrongInterface()
    {
        $wrongObj = new stdClass();
        $filterInstance = Filter::getInstance();
        $response = $filterInstance->setExternalLib($wrongObj);
    }

    /**
     * Test the external lib getter
     */
    public function testGetExternalLib()
    {
        $mock = $this->getExternalLibraryMock();
        $filterInstance = Filter::getInstance();
        $filterInstance->setExternalLib($mock);
        $externalLib = $filterInstance->getExternalLib();
        $expectedInterface = 'DE\CSFilter\ExternalLib\ExternalLibInterface';
        $this->assertInstanceOf($expectedInterface, $externalLib);
    }

    /**
     * Tests the exception thrown when the external lib is not set
     * @expectedException \DE\CSFilter\Exception
     */
    public function testGetExternalLibWhenUnset()
    {
        $filterInstance = Filter::getInstance();
        $externalLib = $filterInstance->getExternalLib();
    }

    /**
     * Test the allowed filters getter
     */
    public function testGetAllowedFilters()
    {
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
    public function testFilterBoolean()
    {
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

    /**
     * Test a string to be filtered to float
     */
    public function testFilterFloat()
    {
        $dirtyVal = 'This will 4 be casted to 0';
        $expectedResult = 0;
        $filterInstance = Filter::getInstance();
        $cleanVal = $filterInstance->filterFloat($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
        $dirtyTrueFloat = '-5.9e-10abcder';
        $expectedTrueFloatResult = -5.9e-10;
        $cleanFalse = $filterInstance->filterFloat($dirtyTrueFloat);
        $this->assertEquals($expectedTrueFloatResult, $cleanFalse);
    }

    /**
     * Test a string to be filtered to integer
     */
    public function testFilterInt()
    {
        $dirtyVal = 'This will 4 be casted to 0';
        $expectedResult = 0;
        $filterInstance = Filter::getInstance();
        $cleanVal = $filterInstance->filterInt($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
        $dirtyTrueInt = '-5.9e-10abcder';
        $expectedTrueIntResult = -5;
        $cleanFalse = $filterInstance->filterInt($dirtyTrueInt);
        $this->assertEquals($expectedTrueIntResult, $cleanFalse);
    }

    /**
     * Test a string to be filtered to integer
     */
    public function testFilterEmail()
    {
        $dirtyVal = 'my_n@me<>@example.com';
        $expectedResult = 'my_n@me@example.com';
        $filterInstance = Filter::getInstance();
        $cleanVal = $filterInstance->filterEmail($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
    }

    /**
     * Test a string to be filtered to a clean string
     */
    public function testFilterString()
    {
        $dirtyVal = '<span onClick="alert(\'Hello World\');">Valid string</span>';
        $expectedResult = 'Valid string';
        $filterInstance = Filter::getInstance();
        $externalLibrary = $this->getExternalLibraryMock();
        $filterInstance->setExternalLib($externalLibrary);
        $cleanVal = $filterInstance->filterString($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
    }

    /**
     * Test a string to be filtered to a clean string
     */
    public function testFilterRich()
    {
        $dirtyVal = '<div onClick="alert(\'Hello World\');"><strong>Valid string</strong></div>';
        $expectedResult = '<p><strong>Valid string</strong></p>';
        $filterInstance = Filter::getInstance();
        $externalLibrary = $this->getExternalLibraryMock();
        $filterInstance->setExternalLib($externalLibrary);
        $cleanVal = $filterInstance->filterRich($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
    }

    /**
     * Test a string to be filtered to a clean string with custom rules
     */
    public function testFilterCustom()
    {
        $dirtyVal = '<div onClick="alert(\'Hello World\');"><strong>Valid string</strong> to http://example.com</div>';
        $expectedResult = 'Valid string to <a href="http://example.com">http://example.com</a>';
        $filterInstance = Filter::getInstance();
        $externalLibrary = $this->getExternalLibraryMock();
        $filterInstance->setExternalLib($externalLibrary);
        $configOptions = [
            'Core.Encoding' => 'UTF-8',
            'HTML.Doctype' => 'XHTML 1.0 Strict',
            'HTML.Allowed' => 'p,a[href|title]',
            'AutoFormat.Linkify' => true,
        ];
        $cleanVal = $filterInstance->filterCustom($dirtyVal, $configOptions);
        $this->assertEquals($expectedResult, $cleanVal);
    }
}