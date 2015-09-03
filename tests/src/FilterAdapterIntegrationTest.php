<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/26/2015
 * Time: 4:48 PM
 */

use Security\XSSFilter\Filter;
use Security\XSSFilter\FilteringLibAdapter\HTMLPurifierFilteringLibAdapter;

/**
 * Class FilterAdapterIntegrationTest
 */
class FilterAdapterIntegrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test setting the external library
     */
    public function testSetFilteringLib()
    {
        $libAdapter = new HTMLPurifierFilteringLibAdapter();
        $filterInstance = new Filter();
        $response = $filterInstance->setFilteringLibAdapter($libAdapter);
        $expectedInstance = 'Security\XSSFilter\Filter';
        $this->assertInstanceOf($expectedInstance, $response);
    }

    /**
     * Test the external lib getter
     */
    public function testGetFilteringLib()
    {
        $libAdapter = new HTMLPurifierFilteringLibAdapter();
        $filterInstance = new Filter();
        $filterInstance->setFilteringLibAdapter($libAdapter);
        $FilteringLib = $filterInstance->getFilteringLib();
        $expectedInterface = 'Security\XSSFilter\FilteringLibAdapter\FilteringLibAdapterInterface';
        $this->assertInstanceOf($expectedInterface, $FilteringLib);
    }

    /**
     * Test a string to be filtered to a clean string
     */
    public function testFilterString()
    {
        $dirtyVal = '<span onClick="alert(\'Hello World\');">Valid string</span>';
        $expectedResult = 'Valid string';
        $filterInstance = new Filter();
        $FilteringLibrary = new HTMLPurifierFilteringLibAdapter();
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
        $FilteringLibrary = new HTMLPurifierFilteringLibAdapter();
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
        $FilteringLibrary = new HTMLPurifierFilteringLibAdapter();
        $filterInstance->setFilteringLibAdapter($FilteringLibrary);
        $configOptions = [
            'configObjOptions' => [
                'Core.Encoding' => 'UTF-8',
                'HTML.Doctype' => 'XHTML 1.0 Strict',
                'HTML.Allowed' => 'p,a[href|title]',
                'AutoFormat.Linkify' => true,
            ]
        ];
        $cleanVal = $filterInstance->filterCustom($dirtyVal, $configOptions);
        $this->assertEquals($expectedResult, $cleanVal);
    }
}