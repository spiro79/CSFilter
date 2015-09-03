<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 9:25 AM
 */

namespace Security\XSSFilter\FilteringLibAdapter;

use \HTMLPurifier;
use \HTMLPurifier_Config;
use Security\XSSFilter\Exceptions\ConfigurationIndexNotFoundException;
use Security\XSSFilter\Filter;

/**
 * Class HTMLPurifierFilteringExternalLibAdapter
 * @package Security\XSSFilter\FilteringLibAdapter
 */
class HTMLPurifierFilteringLibAdapter implements FilteringLibAdapterInterface
{
    /**
     * Array of configs needed for purifying some variables with the filtering library
     * @var array
     */
    protected static $configs = [];

    /**
     * Cleans a variable according to type and settings by using a third party library
     * @param string $dirtyValue The value to be cleaned
     * @param string $filterType What type of filter to apply
     * @param array $optionsForFilteringLib Assoc array of options for the library conf object
     * @return string The clean value
     */
    public function clean($dirtyValue, $filterType, array $optionsForFilteringLib = [])
    {
        $configHash = md5($filterType . json_encode($optionsForFilteringLib));
        if (isset(self::$configs[$configHash])) {
            $configObj = self::$configs[$configHash];
        } else {
            $configObj = HTMLPurifier_Config::createDefault();
            foreach ($optionsForFilteringLib as $option => $value) {
                $configObj->set($option, $value);
            }
            self::$configs[$configHash] = $configObj;
        }
        $filteringLib = new HTMLPurifier($configObj);
        return $filteringLib->purify($dirtyValue);
    }

    /**
     * Cleans a string
     * @param mixed $dirtyVar The value to be cleansed
     * @param array $options Additional options [OPTIONAL]
     * @return string
     */
    public function filterString($dirtyVar, array $options = [])
    {
        $charset = isset($options['charset']) ? $options['charset'] : Filter::DEFAULT_CHARSET;
        $configObjOptions = [
            'Core.Encoding' => $charset,
            'HTML.Allowed' => '',
        ];
        return filter_var($this->clean($dirtyVar, Filter::TYPE_STRING, $configObjOptions), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH);
    }

    /**
     * Cleans a string allowing certain tags (p, a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i)
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options [OPTIONAL]
     * @return string The cleansed string
     */
    public function filterRich($dirtyVar, array $options = [])
    {
        $charset = isset($options['charset']) ? $options['charset'] : Filter::DEFAULT_CHARSET;
        $configObjOptions = [
            'Core.Encoding' => $charset,
            'HTML.Doctype' => 'XHTML 1.0 Strict',
            'HTML.Allowed' => 'p,a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i',
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
     * @throws ConfigurationIndexNotFoundException
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
            throw new ConfigurationIndexNotFoundException("Index {$indexName} for custom configurations was not found!!!");
        }
        return $this->clean($dirtyVar, Filter::TYPE_CUSTOM, $configObjOptions);
    }
}