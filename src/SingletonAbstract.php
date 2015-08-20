<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/20/2015
 * Time: 3:08 PM
 */

namespace DE\CSFilter;

/**
 * Class SingletonAbstract
 * @package DE\CSFilter
 */
abstract class SingletonAbstract implements SingletonInterface
{
    /**
     * Protect constructor to prevent creating a new instance of the *Singleton* via the 'new' operator from outside of this class
     * @access protected
     */
    protected function __construct()
    {
    }

    /**
     * Protected clone method to prevent the cloning of the instance of the *Singleton* object
     * @access protected
     */
    protected function __clone()
    {
    }

    /**
     * Protected wakeup method to prevent unserializing of the *Singleton* object
     */
    protected function __wakeup()
    {
    }
}