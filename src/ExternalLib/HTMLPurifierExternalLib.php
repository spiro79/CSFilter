<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 9:25 AM
 */

namespace DE\CSFilter\ExternalLib;

use \HTMLPurifier;
use \HTMLPurifier_Config;
use DE\CSFilter\Exception;
use DE\CSFilter\Filter;

/**
 * Class HTMLPurifierExternalLib
 * @package DE\CSFilter\ExternalLib
 */
class HTMLPurifierExternalLib implements ExternalLibInterface
{
    /**
     * Array of configs needed for purifying some variables with the purifier library
     * @var array
     */
    protected static $configs = [];

    /**
     * Cleans a variable according to type and settings by using a third party library
     * @param string $dirtyValue The value to be cleaned
     * @param string $filterType What type of filter to apply
     * @param array $optionsForPurifierLib Assoc array of options for the library conf object
     * @return string The clean value
     */
    public function clean($dirtyValue, $filterType, array $optionsForPurifierLib = [])
    {
        $configHash = md5($filterType . json_encode($optionsForPurifierLib));
        if (isset(self::$configs[$configHash])) {
            $configObj = self::$configs[$configHash];
        } else {
            $configObj = HTMLPurifier_Config::createDefault();
            foreach ($optionsForPurifierLib as $option => $value) {
                $configObj->set($option, $value);
            }
            self::$configs[$configHash] = $configObj;
        }
        $purifierLib = new HTMLPurifier($configObj);
        return $purifierLib->purify($dirtyValue);
    }

    /**
     * Cleans a string
     * @param mixed $dirtyVar The value to be cleansed
     * @param array $options Additional options [OPTIONAL]
     * @return string
     * @throws Exception
     */
    public function filterString($dirtyVar, array $options = [])
    {
        $charset = isset($options['charset']) ? $options['charset'] : Filter::DEFAULT_CHARSET;
        $configObjOptions = [
            'Core.Encoding' => $charset,
            'HTML.Allowed' => '',
        ];
        return $this->clean($dirtyVar, Filter::TYPE_STRING, $configObjOptions);
    }

    /**
     * Cleans a string allowing certain tags (p, a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i,strike)
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options [OPTIONAL]
     * @return string The cleansed string
     * @throws Exception
     */
    public function filterRich($dirtyVar, array $options = [])
    {
        $charset = isset($options['charset']) ? $options['charset'] : Filter::DEFAULT_CHARSET;
        $configObjOptions = [
            'Core.Encoding' => $charset,
            'HTML.Doctype' => 'XHTML 1.0 Strict',
            'HTML.Allowed' => 'p,a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i,strike',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.Linkify' => true,
            'AutoFormat.RemoveEmpty' => true,
        ];
        return $this->clean($dirtyVar, Filter::TYPE_RICH, $configObjOptions);
    }

    /**
     * Cleans a string by custom rules set through the options array
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options. For config options use an index named Filter::CUSTOM_CONFIGURATIONS_INDEX_NAME
     * @return string The cleansed string
     * @throws Exception
     */
    public function filterCustom($dirtyVar, array $options = [])
    {
        $charset = isset($options['charset']) ? $options['charset'] : Filter::DEFAULT_CHARSET;
        $configObjOptions = [
            'Core.Encoding' => $charset,
        ];
        if (isset($options[Filter::CUSTOM_CONFIGURATIONS_INDEX_NAME])) {
            $options = $options[Filter::CUSTOM_CONFIGURATIONS_INDEX_NAME] + $configObjOptions;
            $configObjOptions = $options;
        } else {
            $indexName = Filter::CUSTOM_CONFIGURATIONS_INDEX_NAME;
            throw new Exception("Index {$indexName} for custom configurations was not found!!!");
        }
        return $this->clean($dirtyVar, Filter::TYPE_CUSTOM, $configObjOptions);
    }
}