<?php
/**
 * SCSSPHP
 *
 * @copyright 2012-2020 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://leafo.github.io/scssphp
 */

//namespace Leafo\ScssPhp;
use ScssPhp\ScssPhp\Compiler;


try {
    $scss = new Compiler();
    
    $scss->setFormatter('ScssPhp\ScssPhp\Formatter\Compressed');
    $scss->setImportPaths($tpath_abs.'/css');

    //COMMON BASE   
    $base_common_imports = '';
    $base_common_imports .= '$buf_topbar_height:'.$buf_topbar_height.'px;';
    $base_common_imports .= '$buf_topbar_oc_height:'.$buf_topbar_oc_height.'px;';
    $base_common_imports .= '@import "base_common.scss";';

    $base_common = $scss->compile($base_common_imports);


    
    //LAYOUT BASE    
    $scss->setImportPaths($layoutpath.'/scss');
     
    $base_imports = '';
   
    //$bs4_imports = '@import "'.$layoutpath.'/scss/base.scss";';
    $base_imports .= '@import "base.scss";';
    $buf_debug += addDebug('BASE SASS | ', 'cubes', $layoutpath .'/scss/base.scss', $startmicro, 'table-default', 'loadphpcss_base.php');
   

    


    //$scss->setFormatter('scss_formatter_compressed');
    $cssOut = $scss->compile($base_imports);

    //Check cache directory is created
    if (!file_exists($cachepath)) {
        mkdir($cachepath, 0775, true);
    }
    file_put_contents($cachepath . '/base.css', $base_common.$cssOut);

    //$cosa = $cssOut;


} catch (\Exception $e) {
    echo '';
    syslog(LOG_ERR, 'scssphp: Unable to compile content');
    var_dump('scssphp: Unable to compile content');
}

