<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/20/2015
 * Time: 2:28 PM
 */

namespace FireEngine\XSSFilter;

/**
 * Interface FilterInterface
 * @package FireEngine\XSSFilter
 */
interface FilterInterface
{
    /**
     * Defines the value for the default charset
     */
    const DEFAULT_CHARSET = 'UTF-8';
    /**
     * The name of the index used in the options array to set the conf settings
     */
    const CUSTOM_CONFIGURATIONS_INDEX_NAME = 'configObjOptions';
    /**
     * Defines a filter to be applied to a boolean var
     */
    const TYPE_BOOLEAN = 'bool';
    /**
     * Defines a filter to be applied to an integer var
     */
    const TYPE_INTEGER = 'int';
    /**
     * Defines a filter to be applied to a float var
     */
    const TYPE_FLOAT = 'float';
    /**
     * Defines a filter to be applied to an email string
     */
    const TYPE_EMAIL = 'email';
    /**
     * Defines a filter to be applied to a string type var
     */
    const TYPE_STRING = 'string';
    /**
     * Defines a filter to be applied to a rich formatted string
     */
    const TYPE_RICH = 'rich';
    /**
     * Defines a custom filter to be applied to a string
     */
    const TYPE_CUSTOM = 'custom';
    /**
     * Defines a filter to be applied to an unknown type var
     */
    const TYPE_UNKNOWN = 'default';

    /**
     * Filters a variable by applying the specified filter
     * @param mixed $dirtyVar The variable to be filtered
     * @param string $filterType A filter type
     * @return mixed The clean value
     */
    public function filter($dirtyVar, $filterType);
}