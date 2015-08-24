<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 12:54 PM
 */

namespace DE\CSFilter;

/**
 * Class SFilter
 * A facade to let developers use the library as a static obj
 * @package DE\CSFilter
 */
class SFilter
{
    /**
     * The filter class to be used
     * @var Filter
     * @access protected
     * @static
     */
    protected static $filter;

    /**
     * Sets the filter var
     * @access protected
     * @static
     */
    protected static function setFilter()
    {
        if (!self::$filter instanceof Filter) {
            $configs = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.ini');
            if (!isset($configs['default_external_library'])) {
                throw new Exception('Config value \'default_external_library\' was not found on config.ini');
            }
            $className = 'DE\CSFilter\ExternalLib\\' . $configs['default_external_library'];
            self::$filter = Filter::getInstance();
            if (!class_exists($className)) {
                throw new Exception('Class set in config.ini: \'' . $configs['default_external_library'] . '\' does not exist.');
            }
            $externalLibrary = new $className();
            self::$filter->setExternalLib($externalLibrary);
        }
    }

    /**
     * Filters a value and returns a boolean
     * @static
     * @param mixed $dirtyVar The dirty value
     * @return bool
     */
    public static function filterBoolean($dirtyVar)
    {
        self::setFilter();
        return self::$filter->filterBoolean($dirtyVar);
    }

    /**
     * Filters a value and returns a float
     * @static
     * @param mixed $dirtyVar The dirty value
     * @return float
     */
    public static function filterFloat($dirtyVar)
    {
        self::setFilter();
        return self::$filter->filterFloat($dirtyVar);
    }

    /**
     * Filters a value and returns an int
     * @static
     * @param mixed $dirtyVar The dirty value
     * @return int
     */
    public static function filterInt($dirtyVar)
    {
        self::setFilter();
        return self::$filter->filterInt($dirtyVar);
    }

    /**
     * Filters a value and returns a filtered email. This functions does not validate the email.
     * @static
     * @param string $dirtyVar The dirty value
     * @return string A clean string without characters that do not belong to an email standard
     */
    public static function filterEmail($dirtyVar)
    {
        self::setFilter();
        return self::$filter->filterEmail($dirtyVar);
    }

    /**
     * Cleans a string
     * @static
     * @param mixed $dirtyVar The value to be cleansed
     * @param array $options Additional options [OPTIONAL]
     * @return string
     * @throws Exception
     */
    public static function filterString($dirtyVar, array $options = [])
    {
        self::setFilter();
        return self::$filter->filterString($dirtyVar, $options);
    }

    /**
     * Cleans a string allowing certain tags (p, a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i)
     * @static
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options [OPTIONAL]
     * @return string The cleansed string
     * @throws Exception
     */
    public static function filterRich($dirtyVar, array $options = [])
    {
        self::setFilter();
        return self::$filter->filterRich($dirtyVar, $options);
    }

    /**
     * Cleans a string by custom rules set through the options array
     * @static
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options. For config options use an index named self::CUSTOM_CONFIGURATIONS_INDEX_NAME
     * @return string The cleansed string
     * @throws Exception
     */
    public static function filterCustom($dirtyVar, array $options = [])
    {
        self::setFilter();
        return self::$filter->filterCustom($dirtyVar, $options);
    }

    /**
     * Filters a variable by applying the specified filter
     * @static
     * @param mixed $dirtyVar The variable to be filtered
     * @param string $filterType A filter type
     * @param array $options Additional options. For config options use an index named after the CUSTOM_CONFIGURATIONS_INDEX_NAME constant
     * @return mixed The clean value
     * @throws Exception
     */
    public static function filter($dirtyVar, $filterType, array $options = [])
    {
        self::setFilter();
        return self::$filter->filter($dirtyVar, $filterType, $options);
    }
}