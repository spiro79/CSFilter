<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/20/2015
 * Time: 2:40 PM
 */

namespace DE\CSFilter;

use DE\CSFilter\Exceptions\ExternalLibAdapterNotSetException;
use DE\CSFilter\Exceptions\FilterTypeNotValidException;
use \ReflectionClass;
use DE\CSFilter\ExternalLibAdapter\ExternalLibAdapterInterface;

/**
 * Class Filter
 * @package DE\CSFilter
 */
class Filter extends SingletonAbstract implements FilterInterface
{
    /**
     * An instance of the external library to be used to execute complex filters
     * @var ExternalLibAdapterInterface
     */
    protected $externalLib;
    /**
     * An array with the allowed options for the filters
     * @access protected
     * @var array
     */
    protected $allowedFilters = [];

    /**
     * The class instance
     * @access protected
     * @var self
     */
    protected static $instance;

    /**
     * Initialize the object
     */
    protected function init()
    {
        $reflectionClass = new ReflectionClass('DE\CSFilter\FilterInterface');
        $constants = $reflectionClass->getConstants();
        self::$instance->allowedFilters = array_values($constants);
    }

    /**
     * Gets the singleton instance
     * @static
     * @return self
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new static();
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * External lib setter
     * @param ExternalLibAdapterInterface $externalLib
     * @return $this
     */
    public function setExternalLibAdapter(ExternalLibAdapterInterface $externalLib)
    {
        $this->externalLib = $externalLib;
        return $this;
    }

    /**
     * External lib getter
     * @return ExternalLibAdapterInterface
     * @throws ExternalLibAdapterNotSetException
     */
    public function getExternalLib()
    {
        if (!$this->externalLib instanceof ExternalLibAdapterInterface) {
            throw new ExternalLibAdapterNotSetException('An external library has not been set.');
        }
        return $this->externalLib;
    }

    /**
     * Return the array of allowed filters
     * @return array
     */
    public function getAllowedFilters()
    {
        return $this->allowedFilters;
    }

    /**
     * Filters a value and returns a boolean
     * @param mixed $dirtyVar The dirty value
     * @return bool
     */
    public function filterBoolean($dirtyVar)
    {
        return (bool)($dirtyVar);
    }

    /**
     * Filters a value and returns a float
     * @param mixed $dirtyVar The dirty value
     * @return float
     */
    public function filterFloat($dirtyVar)
    {
        return floatval($dirtyVar);
    }

    /**
     * Filters a value and returns an int
     * @param mixed $dirtyVar The dirty value
     * @param int $base The base for the conversion [OPTIONAL]
     * @return int
     */
    public function filterInt($dirtyVar, $base = 0)
    {
        return intval($dirtyVar, $base);
    }

    /**
     * Filters a value and returns a filtered email. This functions does not validate the email.
     * Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[].
     * @param string $dirtyVar The dirty value
     * @return string A clean string without characters that do not belong to an email standard
     */
    public function filterEmail($dirtyVar)
    {
        return filter_var($dirtyVar, FILTER_SANITIZE_EMAIL);
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
        return $this->getExternalLib()->filterString($dirtyVar, $options);
    }

    /**
     * Cleans a string allowing certain tags (p, a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i)
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options [OPTIONAL]
     * @return string The cleansed string
     * @throws Exception
     */
    public function filterRich($dirtyVar, array $options = [])
    {
        return $this->getExternalLib()->filterRich($dirtyVar, $options);
    }

    /**
     * Cleans a string by custom rules set through the options array
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options. For config options use an index named self::CUSTOM_CONFIGURATIONS_INDEX_NAME
     * @return string The cleansed string
     * @throws Exception
     */
    public function filterCustom($dirtyVar, array $options = [])
    {
        return $this->getExternalLib()->filterCustom($dirtyVar, $options);
    }

    /**
     * Filters a variable by applying the specified filter
     * @param mixed $dirtyVar The variable to be filtered
     * @param string $filterType A filter type
     * @param array $options Additional options. For config options use an index named after the CUSTOM_CONFIGURATIONS_INDEX_NAME constant
     * @return mixed The clean value
     * @throws FilterTypeNotValidException
     */
    public function filter($dirtyVar, $filterType, array $options = [])
    {
        switch ($filterType) {
            case self::TYPE_BOOLEAN:
                $cleanVar = $this->filterBoolean($dirtyVar);
                break;
            case self::TYPE_FLOAT:
                $cleanVar = $this->filterFloat($dirtyVar);
                break;
            case self::TYPE_INTEGER:
                $cleanVar = $this->filterInt($dirtyVar);
                break;
            case self::TYPE_EMAIL:
                $cleanVar = $this->filterEmail($dirtyVar);
                break;
            case self::TYPE_STRING:
                $cleanVar = $this->filterString($dirtyVar);
                break;
            case self::TYPE_RICH:
                $cleanVar = $this->filterRich($dirtyVar);
                break;
            case self::TYPE_CUSTOM:
                $cleanVar = $this->filterCustom($dirtyVar, $options);
                break;
            default:
                $validTypes = implode('\',\'', $this->allowedFilters);
                throw new FilterTypeNotValidException("Filter type provided is not valid. Got: '{$filterType}'. Expecting one of: '{$validTypes}'");
                break;
        }
        return $cleanVar;
    }

    /**
     * Tear down.
     * Resets everything to initial state. Helps with Unit testing ;)
     */
    public static function tearDown()
    {
        static::$instance = null;
    }
}