<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 3:12 PM
 */

require_once 'vendor/autoload.php';
require_once './autoload.php';

use DE\CSFilter\Filter;
use \DE\CSFilter\ExternalLibAdapter\HTMLPurifierExternalLibAdapter;

//Create object and provide adapter
$filter = Filter::getInstance()->setExternalLibAdapter(new HTMLPurifierExternalLibAdapter());

$dirtyVar = '<div onClick="alert(\'Hello World\');"><strong>Valid string</strong> to http://example.com</div>';

$configOptions = [
    'configObjOptions' => [
        'Core.Encoding' => 'UTF-8',
        'HTML.Doctype' => 'XHTML 1.0 Strict',
        'HTML.Allowed' => 'p,a[href|title]',
        'AutoFormat.Linkify' => true
    ]
];

$cleanVar = $filter->filter($dirtyVar,Filter::TYPE_CUSTOM, $configOptions);

echo 'Dirty value:', PHP_EOL, $dirtyVar, PHP_EOL, 'Clean Value:', PHP_EOL, $cleanVar;