<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 3:12 PM
 */


require_once 'vendor/autoload.php';
require_once './autoload.php';

use DE\CSFilter\SFilter;
use \DE\CSFilter\ExternalLib\HTMLPurifierExternalLib;

$dirtyVar = '<div onClick="alert(\'Hello World\');"><strong>Valid string</strong> to http://example.com</div>';

$configOptions = [
    'configObjOptions' => [
        'Core.Encoding' => 'UTF-8',
        'HTML.Doctype' => 'XHTML 1.0 Strict',
        'HTML.Allowed' => 'p,a[href|title]',
        'AutoFormat.Linkify' => true
    ]
];

$externalLibrary = new HTMLPurifierExternalLib();

SFilter::setFilter($externalLibrary);

$cleanVar = SFilter::filter($dirtyVar,Filter::TYPE_CUSTOM, $configOptions);

echo $cleanVar;