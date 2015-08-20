<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/20/2015
 * Time: 3:06 PM
 */

namespace DE\CSFilter;

/**
 * Interface SingletonInterface
 * @package DE\CSFilter
 */
interface SingletonInterface
{
    /**
     * Gets the singleton instance
     * @static
     * @return SingletonInterface
     */
    public static function getInstance();
}