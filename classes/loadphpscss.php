<?php
/**
 * SCSSPHP
 *
 * @copyright 2012-2017 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://leafo.github.io/scssphp
 */

//namespace Leafo\ScssPhp;
use ScssPhp\ScssPhp\Compiler;


try {
    $scss = new Compiler();
 
} catch (\Exception $e) {
    echo '';
    syslog(LOG_ERR, 'scssphp: Unable to compile content');
}

