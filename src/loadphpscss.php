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

use Joomla\CMS\Factory;
use ScssPhp\ScssPhp\Compiler;

try {
    $scss = new Compiler();
} catch (\RuntimeException $e) {
    Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
}
