<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/20/2015
 * Time: 2:40 PM
 */

namespace DE\CSFilter;

use \HTMLPurifier;
use \HTMLPurifier_Config;

/**
 * Class Filter
 * @package DE\CSFilter
 */
class Filter extends SingletonAbstract implements FilterInterface
{
    /**
     * Defines the value for the default charset
     */
    const DEFAULT_CHARSET = 'UTF-8';
    /**
     * Array of configs needed for purifying some variables with the purifier library
     * @var array
     */
    protected static $configs = [];
    /**
     * An instance of the purifiers library to be used
     * @var object
     */
    protected static $purifierLib;
    /**
     * An array with the allowed options for the filters
     * @var array
     */
    protected static $allowedFilters = [
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

    /**
     * Return the array of allowed filters
     * @return array
     */
    public static function getAllowedFilters() {
        return self::$allowedFilters;
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
     * Cleans a variable according to type and settings by using a third party library
     * @param string $dirtyValue The value to be cleaned
     * @param string $filterType What type of filter to apply
     * @param array $optionsForPurifierLib Assoc array of options for the library conf object
     * @return string The clean value
     * @throws Exception
     */
    protected static function clean($dirtyValue, $filterType, array $optionsForPurifierLib = []) {
        if(in_array($filterType, self::$allowedFilters)) {
            $configHash = md5($filterType . json_encode($optionsForPurifierLib));
            if(isset(self::$configs[$configHash])) {
                $configObj = self::$configs[$configHash];
            }
            else {
                $configObj = HTMLPurifier_Config::createDefault();
                foreach($optionsForPurifierLib as $option => $value) {
                    $configObj->set($option,$value);
                }
                self::$configs[$configHash] = $configObj;
            }
            $purifierLib = new HTMLPurifier($configObj);
            $purifiedValue = $purifierLib->purify($dirtyValue);
        }
        else {
            $validTypes = implode('\',\'', self::$allowedFilters);
            throw new Exception("Filter type provided is not valid. Got: '{$filterType}'. Expecting one of: '{$validTypes}'");
        }
        return $purifiedValue;
    }

    /**
     * Cleans a string
     * @param mixed $dirtyVar The value to be cleansed
     * @param array $options Additional options defining the charset to be used as array('charset' => 'ISO8859-1'). Defaults to UTF-8 [OPTIONAL]
     * @return string
     * @throws Exception
     */
    public static function filterString($dirtyVar, array $options = []) {
        $charset = isset($options['charset'])?$options['charset']:self::DEFAULT_CHARSET;
        $configObjOptions = [
            'Core.Encoding' => $charset,
            'HTML.Allowed' => '',
        ];
        return self::clean($dirtyVar, self::TYPE_STRING, $configObjOptions);
    }

    /**
     * Cleans a string allowing certain tags (p, a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i,strike)
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options defining the charset to be used as array('charset' => 'ISO8859-1'). Defaults to UTF-8 [OPTIONAL]
     * @return string The cleansed string
     * @throws Exception
     */
    public static function filterRich($dirtyVar, array $options = []) {
        $charset = isset($options['charset'])?$options['charset']:self::DEFAULT_CHARSET;
        $configObjOptions = [
            'Core.Encoding' => $charset,
            'HTML.Doctype' => 'XHTML 1.0 Strict',
            'HTML.Allowed' => 'p,a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i,strike',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.Linkify' => true,
            'AutoFormat.RemoveEmpty' => true,
        ];
        return self::clean($dirtyVar, self::TYPE_RICH, $configObjOptions);
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