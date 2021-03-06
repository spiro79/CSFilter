<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 9:01 PM
 */

use Security\XSSFilter\Filter;

/**
 * Class FilterTest
 */
class FilterTest extends PHPUnit_Framework_TestCase
{

    /**
     * Mocks the external library object dependency
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFilteringLibraryMock()
    {
        $mock = $this->getMockBuilder('Security\XSSFilter\FilteringLibAdapter\FilteringLibAdapterInterface')
            ->setMethods(['clean', 'filterString', 'filterRich', 'filterCustom'])
            ->getMock();
        $mock->method('filterString')
            ->willReturn('Valid string');
        $mock->method('filterRich')
            ->willReturn('<strong>Valid string</strong>');
        $mock->method('filterCustom')
            ->willReturn('Valid string to <a href="http://example.com">http://example.com</a>');
        return $mock;
    }

    /**
     * Test setting the external library
     */
    public function testSetFilteringLib()
    {
        $mock = $this->getFilteringLibraryMock();
        $filterInstance = new Filter();
        $response = $filterInstance->setFilteringLibAdapter($mock);
        $expectedInstance = 'Security\XSSFilter\Filter';
        $this->assertInstanceOf($expectedInstance, $response);
    }

    /**
     * Test that no other interfaces can be set to the instance
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetFilteringLibWithWrongInterface()
    {
        $wrongObj = new stdClass();
        $filterInstance = new Filter();
        $response = $filterInstance->setFilteringLibAdapter($wrongObj);
    }

    /**
     * Test the external lib getter
     */
    public function testGetFilteringLib()
    {
        $mock = $this->getFilteringLibraryMock();
        $filterInstance = new Filter();
        $filterInstance->setFilteringLibAdapter($mock);
        $FilteringLib = $filterInstance->getFilteringLib();
        $expectedInterface = 'Security\XSSFilter\FilteringLibAdapter\FilteringLibAdapterInterface';
        $this->assertInstanceOf($expectedInterface, $FilteringLib);
    }

    /**
     * Tests the exception thrown when the external lib is not set
     * @expectedException \Security\XSSFilter\Exceptions\FilteringLibAdapterNotSetException
     */
    public function testGetFilteringLibWhenUnset()
    {
        $filterInstance = new Filter();
        $FilteringLib = $filterInstance->getFilteringLib();
    }

    /**
     * Test the allowed filters getter
     */
    public function testGetAllowedFilters()
    {
        $reflectionClass = new ReflectionClass('Security\XSSFilter\FilterInterface');
        $constants = $reflectionClass->getConstants();
        $expectedAllowedFilters = [];
        foreach ($constants as $name => $value) {
            if (strstr($name, 'TYPE_')) {
                $expectedAllowedFilters[] = $value;
            }
        }
        $filterInstance = new Filter();
        $allowedFilters = $filterInstance->getAllowedFilters();
        $this->assertEquals($expectedAllowedFilters, $allowedFilters);
    }

    /**
     * Test a string to be filtered to a specific type
     */
    public function testFilter()
    {
        $dirtyVal = '456Garbage text';
        $expectedResult = 456;
        $filterInstance = new Filter();
        $cleanVal = $filterInstance->filter($dirtyVal, FILTER::TYPE_INTEGER);
        $this->assertEquals($expectedResult, $cleanVal);
    }

    /**
     * Test when an invalid filter type is provided
     * @expectedException \Security\XSSFilter\Exceptions\FilterTypeNotValidException
     */
    public function testInvalidFilterType()
    {
        $dirtyVal = '456Garbage text';
        $expectedResult = 456;
        $filterInstance = new Filter();
        $cleanVal = $filterInstance->filter($dirtyVal, PHP_EOL);
        $this->assertEquals($expectedResult, $cleanVal);
    }

    /**
     * Test a string to be filtered to boolean
     */
    public function testFilterBoolean()
    {
        $dirtyVal = 'This will be casted to true';
        $expectedResult = true;
        $filterInstance = new Filter();
        $cleanVal = $filterInstance->filterBool($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
        $dirtyFalseVal = 0; //Will be casted to false
        $expectedFalseResult = false;
        $cleanFalse = $filterInstance->filterBool($dirtyFalseVal);
        $this->assertEquals($expectedFalseResult, $cleanFalse);
    }

    /**
     * Test a string to be filtered to float
     */
    public function testFilterFloat()
    {
        $dirtyVal = 'This will 4 be casted to 0';
        $expectedResult = 0;
        $filterInstance = new Filter();
        $cleanVal = $filterInstance->filterFloat($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
        $dirtyTrueFloat = '-5.9e-10abcder';
        $expectedTrueFloatResult = -5.9e-10;
        $cleanFalse = $filterInstance->filterFloat($dirtyTrueFloat);
        $this->assertEquals($expectedTrueFloatResult, $cleanFalse);
        $trueFloat = 132.22;
        $expectedTrueFloatResult2 = 132.22;
        $cleanFalse = $filterInstance->filterFloat($trueFloat);
        $this->assertEquals($expectedTrueFloatResult2, $cleanFalse);
    }

    /**
     * Test a string to be filtered to integer
     */
    public function testFilterInt()
    {
        $dirtyVal = 'This will 4 be casted to 0';
        $expectedResult = 0;
        $filterInstance = new Filter();
        $cleanVal = $filterInstance->filterInt($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
        $dirtyTrueInt = '-5.9e-10abcder';
        $expectedTrueIntResult = -5;
        $cleanFalse = $filterInstance->filterInt($dirtyTrueInt);
        $this->assertEquals($expectedTrueIntResult, $cleanFalse);
        $dirtyTrueInt = -6.9;
        $expectedTrueIntResult = -6;
        $cleanFalse = $filterInstance->filterInt($dirtyTrueInt);
        $this->assertEquals($expectedTrueIntResult, $cleanFalse);
    }

    /**
     * Test a string to be filtered to number
     */
    public function testFilterNumber()
    {
        $dirtyVal = '<pre>-4.56</pre>';
        $expectedResult = '-4.56';
        $filterInstance = new Filter();
        $cleanVal = $filterInstance->filterNumber($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
    }

    /**
     * Test a string to be filtered to integer
     */
    public function testFilterEmail()
    {
        $dirtyVal = 'my_n@me<>@example.com';
        $expectedResult = 'my_n@me@example.com';
        $filterInstance = new Filter();
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
        $filterInstance = new Filter();
        $FilteringLibrary = $this->getFilteringLibraryMock();
        $filterInstance->setFilteringLibAdapter($FilteringLibrary);
        $cleanVal = $filterInstance->filterString($dirtyVal);
        $this->assertEquals($expectedResult, $cleanVal);
    }

    /**
     * Test a string to be filtered to a clean string
     */
    public function testFilterRich()
    {
        $dirtyVal = '<div onClick="alert(\'Hello World\');"><strong>Valid string</strong></div>';
        $expectedResult = '<strong>Valid string</strong>';
        $filterInstance = new Filter();
        $FilteringLibrary = $this->getFilteringLibraryMock();
        $filterInstance->setFilteringLibAdapter($FilteringLibrary);
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
        $filterInstance = new Filter();
        $FilteringLibrary = $this->getFilteringLibraryMock();
        $filterInstance->setFilteringLibAdapter($FilteringLibrary);
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