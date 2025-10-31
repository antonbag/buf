<?php

/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2021 jtotal
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Jtotal\BUF\Site\Helper\BufHelper;
use Jtotal\Jtfw\Helper\JtScssPhp;

try {
    $scss = JtScssPhp::getScssphp2();
    if (!$scss) {
        return;
    }

    $scss->addImportPath($tpath_media_abs . '/css');

    //COMMON BASE
    $base_common_imports = '';


    //Show bufoc on media
    if ($templateparams->get('buf_offcanvas', 0) == 2) {
        $base_common_imports .= '@media (min-width: ' . $buf_offcanvas_max_w . 'px){
            #bufoc_button{
                display:none !important;
            }
        }';
    }


    $base_common_imports .= '$buf_topbar_height:' . $buf_topbar_height . 'px;';
    $base_common_imports .= '$buf_topbar_oc_height:' . $buf_topbar_oc_height . 'px;';
    $base_common_imports .= '@import "base_common.scss";';

    $result = $scss->compileString($base_common_imports);
    $cssOut = $result->getCss();

    //$cssOut$base_common = $scss->compile($base_common_imports);
    $base_common = $cssOut;

    //LAYOUT BASE
    $scss->addImportPath($layoutpath . '/scss');

    $base_imports = '';

    //$bs4_imports = '@import "'.$layoutpath.'/scss/base.scss";';
    $base_imports .= '@import "base.scss";';
    $buf_debug += BUFHelper::addDebug('BASE SASS | ', 'sass fab', $layoutpath . '/scss/base.scss', $startmicro, 'table-default', 'loadphpcss_base.php');

   //$cssOut = $scss->compile($base_imports);
    $result = $scss->compileString($base_imports);
    $cssOut = $result->getCss();
    //Check cache directory is created
    if (!file_exists($cachepath)) {
        mkdir($cachepath, 0775, true);
    }
    file_put_contents($cachepath . '/base.css', $base_common . $cssOut);

    $buf_debug += BufHelper::addDebug('BASE css', 'css3-alt fab', 'css base compiled', $startmicro, 'table-default', 'logic_base.php');
} catch (\Exception $e) {
    echo 'Error compilando SCSS: ' . $e->getMessage();
    syslog(LOG_ERR, 'scssphp: Unable to compile content - ' . $e->getMessage());
    var_dump('scssphp: Unable to compile content');
}
