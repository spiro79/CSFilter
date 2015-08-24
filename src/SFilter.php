<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 12:54 PM
 */

namespace DE\CSFilter;
use DE\CSFilter\ExternalLib\ExternalLibInterface;

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
     * @static
     * @param ExternalLibInterface $externalLib The external lib adapter to be used
     * @throws Exception
     */
    public static function setFilter(ExternalLibInterface $externalLib)
    {
        if (!self::$filter instanceof Filter) {
            self::$filter = Filter::getInstance();
            self::$filter->setExternalLib($externalLib);
        }
    }

    /**
     * Return the filter. If not set throw an Exception
     * @static
     * @return Filter
     * @throws Exception
     */
    public static function getFilter()
    {
        if (!self::$filter instanceof Filter) {
            throw new Exception('Filter not set.');
        } else {
            return self::$filter;
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
        return self::getFilter()->filterBoolean($dirtyVar);
    }

    /**
     * Filters a value and returns a float
     * @static
     * @param mixed $dirtyVar The dirty value
     * @return float
     */
    public static function filterFloat($dirtyVar)
    {
        return self::getFilter()->filterFloat($dirtyVar);
    }

    /**
     * Filters a value and returns an int
     * @static
     * @param mixed $dirtyVar The dirty value
     * @return int
     */
    public static function filterInt($dirtyVar)
    {
        return self::getFilter()->filterInt($dirtyVar);
    }

    /**
     * Filters a value and returns a filtered email. This functions does not validate the email.
     * @static
     * @param string $dirtyVar The dirty value
     * @return string A clean string without characters that do not belong to an email standard
     */
    public static function filterEmail($dirtyVar)
    {
        return self::getFilter()->filterEmail($dirtyVar);
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
        return self::getFilter()->filterString($dirtyVar, $options);
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
        return self::getFilter()->filterRich($dirtyVar, $options);
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
        return self::getFilter()->filterCustom($dirtyVar, $options);
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
        return self::getFilter()->filter($dirtyVar, $filterType, $options);
    }
}