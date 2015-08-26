<?php

/**
 * Ernesto Spiro Peimbert Andreakis
 * Date: 8/26/2015
 * Time: 4:48 PM
 */
class FilterAdapterIntegrationTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $requiredFile = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($requiredFile)) {
            $this->markTestSkipped('Vendor autoload not found.');
        }
        require_once $requiredFile;
        if (!class_exists('\HTMLPurifier')) {
            $this->markTestSkipped('Class \HTMLPurifier not found not found.');
        }
    }

    protected function tearDown()
    {
        DE\CSFilter\Filter::tearDown();
    }

    /**
     * Test setting the external library
     */
    public function testSetExternalLib()
    {
        $libAdapter = new \DE\CSFilter\ExternalLibAdapter\HTMLPurifierExternalLibAdapter();
        $filterInstance = DE\CSFilter\Filter::getInstance();
        $response = $filterInstance->setExternalLibAdapter($libAdapter);
        $expectedInstance = 'DE\CSFilter\Filter';
        $this->assertInstanceOf($expectedInstance, $response);
    }

    /**
     * Test the external lib getter
     */
    public function testGetExternalLib()
    {
        $libAdapter = new \DE\CSFilter\ExternalLibAdapter\HTMLPurifierExternalLibAdapter();
        $filterInstance = DE\CSFilter\Filter::getInstance();
        $filterInstance->setExternalLibAdapter($libAdapter);
        $externalLib = $filterInstance->getExternalLib();
        $expectedInterface = 'DE\CSFilter\ExternalLibAdapter\ExternalLibAdapterInterface';
        $this->assertInstanceOf($expectedInterface, $externalLib);
    }

    /**
     * Test a string to be filtered to a clean string
     */
    public function testFilterString()
    {
        $dirtyVal = '<span onClick="alert(\'Hello World\');">Valid string</span>';
        $expectedResult = 'Valid string';
        $filterInstance = DE\CSFilter\Filter::getInstance();
        $externalLibrary = new \DE\CSFilter\ExternalLibAdapter\HTMLPurifierExternalLibAdapter();
        $filterInstance->setExternalLibAdapter($externalLibrary);
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
        $filterInstance = DE\CSFilter\Filter::getInstance();
        $externalLibrary = new \DE\CSFilter\ExternalLibAdapter\HTMLPurifierExternalLibAdapter();
        $filterInstance->setExternalLibAdapter($externalLibrary);
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
        $filterInstance = DE\CSFilter\Filter::getInstance();
        $externalLibrary = new \DE\CSFilter\ExternalLibAdapter\HTMLPurifierExternalLibAdapter();
        $filterInstance->setExternalLibAdapter($externalLibrary);
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