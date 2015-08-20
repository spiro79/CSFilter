<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/20/2015
 * Time: 2:40 PM
 */

namespace DE\CSFilter;

/**
 * Class Filter
 * @package DE\CSFilter
 */
class Filter extends SingletonAbstract implements FilterInterface
{
    /**
     * An array with the allowed options for the filters
     * @var array
     */
    protected $allowedFilters = [
        self::TYPE_BOOLEAN,
        self::TYPE_EMAIL,
        self::TYPE_INTEGER,
        self::TYPE_FLOAT,
        self::TYPE_NUMBER,
        self::TYPE_RICH,
        self::TYPE_STRING,
        self::TYPE_UNKNOWN
    ];

    /**
     * The class instance
     * @access protected
     * @var self
     */
    protected static $instance;

    /**
     * Gets the singleton instance
     * @static
     * @return SingletonInterface
     */
    public static function getInstance() {
        if(null === self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function getAllowedFilters() {

    }

    /**
     * Filters a value and returns a boolean
     * @param mixed $dirtyVar The dirty value
     * @return bool
     */
    public static function filterBoolean($dirtyVar) {
        return (bool) $dirtyVar;
    }

    /**
     * Filters a value and returns a numeric string
     * @param string $dirtyVar The dirty value
     * @return string A string with valid numeric characters
     */
    public static function filterNumber($dirtyVar) {
        return filter_var($dirtyVar, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_SCIENTIFIC);
    }

    /**
     * Filters a value and returns a float
     * @param mixed $dirtyVar The dirty value
     * @return float
     */
    public static function filterFloat($dirtyVar) {
        $cleanVar = self::filterNumber($dirtyVar);
        return (float) $cleanVar;
    }

    /**
     * Filters a value and returns an int
     * @param mixed $dirtyVar The dirty value
     * @return int
     */
    public static function filterInt($dirtyVar) {
        $cleanVar = self::filterNumber($dirtyVar);
        return (integer) $cleanVar;
    }

    /**
     * Filters a value and returns a filtered email. This functions does not validate the email.
     * @param string $dirtyVar The dirty value
     * @return string A clean string without characters that do not belong to an email standard
     */
    public static function filterEmail($dirtyVar) {
        return filter_var($dirtyVar, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Filters a variable by applying the specified filter
     * @param mixed $dirtyVar The variable to be filtered
     * @param string $filterType A filter type
     * @return mixed The clean value
     */
    public static function filter($dirtyVar, $filterType)
    {
        // TODO: Implement filter() method.
    }
}