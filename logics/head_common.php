<?php

/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © jtotal.org
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 *
 * Bloque <head> común a index.php y component.php: viewport, metas, CSS
 * crítico inline (base.css), preloads saneados y estilos/scripts de Joomla.
 * No incluye favicons ni Cache-Control: cada documento los añade tras este
 * include según corresponda (component.php nunca cargó favicons).
 *
 * Variables esperadas del scope llamante (definidas por logic_base.php):
 * $buf_layout, $runless, $remaining_minutes, $webapp_capable, $head_preload,
 * $buf_load_resources, $buf_debug, $startmicro, $wa.
 *
 * @var string $headDebugService Etiqueta de origen para el log de debug
 *             ('index.php' o 'component.php'), para no perder ese detalle
 *             al compartir el bloque entre ambos documentos.
 */

defined('_JEXEC') or die;

use Jtotal\BUF\Site\Helper\BufHelper;

$headDebugService ??= 'index.php';
?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0">

    <jdoc:include type="metas" />
    <?php
    //PRELOAD
    echo $head_preload;
    ?>

    <?php
    if ($webapp_capable) {
        echo '<meta name="mobile-web-app-capable" content="yes">';
    }
    ?>

    <?php
    //BASE CSS
    $baseCssPath = JPATH_SITE . '/cache/buf_' . $buf_layout . '/base.css';
    $hasBaseCss = is_file($baseCssPath);

    if ($hasBaseCss && filesize($baseCssPath) < 14336) {
        echo '<style id="buf_style_base">';
        echo file_get_contents($baseCssPath);
        if ($runless == '1' && $remaining_minutes > 0) {
            echo "#superwrapper{padding-bottom:48px}";
        }
        echo '</style>';
        $buf_debug += BufHelper::addDebug('BASE CSS style', 'thumbs-up', 'LOADED', $startmicro, 'table-success', $headDebugService);
    } elseif ($hasBaseCss) {
        // Enlace directo (fuera del WebAssetManager): garantiza que este <link>
        // quede físicamente antes de <jdoc:include type="styles" /> en el HTML,
        // ya que el orden de los assets gestionados por $wa NO depende de la
        // posición del código que los activa, sino de su orden interno de
        // resolución (dependencias/FIFO), que aquí no queremos arriesgar.
        echo '<link rel="stylesheet" id="buf_style_base" href="'
            . $cache_opath . 'base.css?' . filemtime($baseCssPath) . '">';
        $buf_debug += BufHelper::addDebug('BASE CSS <link>', 'thumbs-up', 'LOADED', $startmicro, 'table-success', $headDebugService);
    } else {
        $buf_debug += BufHelper::addDebug('BASE CSS missing', 'exclamation-triangle', 'MISSING', $startmicro, 'table-danger', $headDebugService);
    }
    ?>

    <?php
        //PRELOAD
        echo BufHelper::sanitizeHeadMarkup((string) $buf_load_resources);
        //var_dump($buf_load_resources);
    ?>

    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
