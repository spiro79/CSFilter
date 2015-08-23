<?php
/**
 * Author: Ernesto Spiro Peimbert Andreakis
 * Date: 8/22/2015
 * Time: 3:05 PM
 */

/**
 * Autoloader as taken from:
 * http://www.php-fig.org/psr/psr-4/examples/
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'DE\\CSFilter\\';

    // base directory for the namespace prefix
    //$base_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});