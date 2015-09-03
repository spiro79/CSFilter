<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 12:37 AM
 */

namespace FireEngine\XSSFilter\FilteringLibAdapter;

use FireEngine\XSSFilter\Exceptions\ConfigurationIndexNotFoundException;

/**
 * Interface FilteringLibAdapterInterface
 * @package FireEngine\XSSFilter\FilteringLibAdapter
 */
interface FilteringLibAdapterInterface
{
    /**
     * Cleans a variable according to type and settings by using a third party library
     * @param string $dirtyValue The value to be cleaned
     * @param string $filterType What type of filter to apply
     * @param array $optionsForPurifierLib Assoc array of options for the library conf object
     * @return string The clean value
     */
    public function clean($dirtyValue, $filterType, array $optionsForPurifierLib = []);

    /**
     * Cleans a string
     * @param mixed $dirtyVar The value to be cleansed
     * @param array $options Additional options [OPTIONAL]
     * @return string
     */
    public function filterString($dirtyVar, array $options = []);

    /**
     * Cleans a string allowing certain tags (p, a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i,strike)
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options [OPTIONAL]
     * @return string The cleansed string
     */
    public function filterRich($dirtyVar, array $options = []);

    /**
     * Cleans a string by custom rules set through the options array
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options. For config options use an index named self::CUSTOM_CONFIGURATIONS_INDEX_NAME
     * @return string The cleansed string
     * @throws ConfigurationIndexNotFoundException
     */
    public function filterCustom($dirtyVar, array $options = []);
}