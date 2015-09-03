<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/20/2015
 * Time: 2:40 PM
 */

namespace FireEngine\XSSFilter;

use FireEngine\XSSFilter\Exceptions\FilteringLibAdapterNotSetException;
use FireEngine\XSSFilter\Exceptions\FilterTypeNotValidException;
use \ReflectionClass;
use FireEngine\XSSFilter\FilteringLibAdapter\FilteringLibAdapterInterface;

/**
 * Class Filter
 * @package FireEngine\XSSFilter
 */
class Filter implements FilterInterface
{
    /**
     * An instance of the external library to be used to execute complex filters
     * @var FilteringLibAdapterInterface
     */
    protected $FilteringLib;
    /**
     * An array with the allowed options for the filters
     * @access protected
     * @var array
     */
    protected $allowedFilters = [];

    /**
     * Initialize the object
     */
    protected function init()
    {
        $reflectionClass = new ReflectionClass('FireEngine\XSSFilter\FilterInterface');
        $constants = $reflectionClass->getConstants();
        foreach ($constants as $name => $value) {
            if (strstr($name, 'TYPE_')) {
                $this->allowedFilters[] = $value;
            }
        }
    }

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * External lib setter
     * @param FilteringLibAdapterInterface $FilteringLib
     * @return $this
     */
    public function setFilteringLibAdapter(FilteringLibAdapterInterface $FilteringLib)
    {
        $this->FilteringLib = $FilteringLib;
        return $this;
    }

    /**
     * External lib getter
     * @return FilteringLibAdapterInterface
     * @throws FilteringLibAdapterNotSetException
     */
    public function getFilteringLib()
    {
        if (!$this->FilteringLib instanceof FilteringLibAdapterInterface) {
            throw new FilteringLibAdapterNotSetException('An external library has not been set.');
        }
        return $this->FilteringLib;
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
    public function filterBool($dirtyVar)
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
     * @throws FilteringLibAdapterNotSetException
     */
    public function filterString($dirtyVar, array $options = [])
    {
        return $this->getFilteringLib()->filterString($dirtyVar, $options);
    }

    /**
     * Cleans a string allowing certain tags (p, a[href|title],abbr[title],acronym[title],b,strong,blockquote[cite],code,em,i)
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options [OPTIONAL]
     * @return string The cleansed string
     * @throws FilteringLibAdapterNotSetException
     */
    public function filterRich($dirtyVar, array $options = [])
    {
        return $this->getFilteringLib()->filterRich($dirtyVar, $options);
    }

    /**
     * Cleans a string by custom rules set through the options array
     * @param string $dirtyVar The dirty string
     * @param array $options Additional options. For config options use an index named self::CUSTOM_CONFIGURATIONS_INDEX_NAME
     * @return string The cleansed string
     * @throws FilteringLibAdapterNotSetException
     */
    public function filterCustom($dirtyVar, array $options = [])
    {
        return $this->getFilteringLib()->filterCustom($dirtyVar, $options);
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
        $filterType = strtolower($filterType);
        $method = 'filter' . ucfirst($filterType);
        if (method_exists($this, $method)) {
            if ($filterType !== self::TYPE_CUSTOM) {
                $cleanVar = $this->$method($dirtyVar);
            } else {
                $cleanVar = $this->$method($dirtyVar, $options);
            }
        } else {
            $validTypes = implode('\',\'', $this->allowedFilters);
            throw new FilterTypeNotValidException("Filter type provided is not valid. Got: '{$filterType}'. Expecting one of: '{$validTypes}'");
        }
        return $cleanVar;
    }
}