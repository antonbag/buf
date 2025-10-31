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

use Joomla\CMS\Factory;
use ScssPhp\ScssPhp\Compiler;

// Cargar autoloader de Composer usando el método de Joomla 5
if (!class_exists('ScssPhp\ScssPhp\Compiler')) {
    $autoloadPath = JPATH_LIBRARIES . '/jtlibs/vendor/autoload.php';
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
    } else {
        // Es buena idea mostrar un error si la librería no se encuentra
        Factory::getApplication()->enqueueMessage('Error: No se pudo encontrar el autoloader de scssphp.', 'error');
        return;
    }
}

// Compilación (si la necesitas)
try {
    $scss = new Compiler();
} catch (\Exception $e) {
    Factory::getApplication()->enqueueMessage('Error: ' . $e->getMessage(), 'error');
}
