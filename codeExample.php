<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 3:12 PM
 */

require_once 'vendor/autoload.php';

use Security\XSSFilter\Filter;
use Security\XSSFilter\FilteringLibAdapter\HTMLPurifierFilteringLibAdapter;

//Create object and provide adapter
$filter = (new Filter())->setFilteringLibAdapter(new HTMLPurifierFilteringLibAdapter());

$dirtyVar = '<div onClick="alert(\'Hello World\');"><strong>Valid string</strong> to http://example.com</div>';

$configOptions = [
    'configObjOptions' => [
        'Core.Encoding' => 'UTF-8',
        'HTML.Doctype' => 'XHTML 1.0 Strict',
        'HTML.Allowed' => 'p,a[href|title]',
        'AutoFormat.Linkify' => true
    ]
];

$cleanVar = $filter->filter($dirtyVar, Filter::TYPE_CUSTOM, $configOptions);

echo 'Dirty value:', PHP_EOL, $dirtyVar, PHP_EOL, 'Clean Value:', PHP_EOL, $cleanVar;