<?php

declare(strict_types=1);

/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2026 jtotal
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Jtotal\BUF\Site\Helper;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Helper class for rendering Offcanvas elements.
 *
 * @since  1.0
 */
class BufOffcanvas
{

    /**
     * Renders the Offcanvas element based on the provided template parameters.
     *
     * @param   Registry  $templateparams  The template parameters.
     *
     * @return  string  The rendered Offcanvas HTML.
     *
     * @since   1.0
     */
    public static function renderOffcanvas(Registry $templateparams): string
    {


        // --- Obtener parámetros necesarios ---
        $buf_offcanvas = $templateparams->get('buf_offcanvas', 0); // Asegúrate que 'buf_offcanvas' es el nombre real en tu templateDetails.xml
        $buf_offcanvas_selector = $templateparams->get('buf_offcanvas_selector', 'buf_offcanvas_bootstrap'); // Valor por defecto

        // Si el offcanvas no está habilitado, no retornamos nada
        if (!$buf_offcanvas) {
            return '';
        }

        /**********************LOAD OFFCANVAS POSITIONS**************************************/
        $buf_offcanvas_position = $templateparams->get('buf_offcanvas_position', 'buf_off_pos_left');
        $buf_offcanvas_style = $templateparams->get('buf_offcanvas_style', 'buf_off_move');

        $buf_offcanvas_positions = $templateparams->get('buf_offcanvas_positions', array());

        $buf_offcanvas_positions_array = $buf_offcanvas_positions;
        //old versions of buf
        if (!is_array($buf_offcanvas_positions)) {
            $buf_offcanvas_positions_array = explode(',', $buf_offcanvas_positions);
        }

        /**********************LOAD OFFCANVAS MODULES**************************************/
        $buf_offcanvas_loadmodules = $templateparams->get('buf_offcanvas_loadmodules', array());

        /****************************************************/
        /******** CUSTOM MODULES IN CANVAS  *****************/
        $buf_offcanvas_modules = '';
        if (!empty($buf_offcanvas_positions) || !empty($buf_offcanvas_loadmodules)) {
            $buf_offcanvas_modules .= '<div class="offcanvas_module_in">';

            if (!empty($buf_offcanvas_positions)) {
                foreach ($buf_offcanvas_positions_array as $b_off) {
                    $modules = ModuleHelper::getModules($b_off);

                    foreach ($modules as $module) {
                        $buf_offcanvas_modules .= ModuleHelper::renderModule($module, array('buf_offcanvas' => true));
                    }
                }
            }

            if (!empty($buf_offcanvas_loadmodules)) {
                foreach ($buf_offcanvas_loadmodules as $moduleid) {
                    $module = ModuleHelper::getModuleById($moduleid);
                    $buf_offcanvas_modules .= ModuleHelper::renderModule($module, array('buf_offcanvas' => true));
                }
            }

            $buf_offcanvas_modules .= '</div>';
        }



        //TOPBAR IN OFFCANVAS
        // (getDefaultOffCanvas()/getBootstrapOffCanvas() derivan sus propias variables
        // a partir de este Registry vía self::buildTopbarOcContext(); no se necesita
        // más que construirlo aquí.)
        ///////////////////////
        $buf_topbar_oc = new Registry;
        $buf_topbar_oc->loadString(json_encode($templateparams->get('buf_topbar_oc')));

        ///////////////////////
        //OFFCANVAS BUTTON
        ///////////////////////
        $oc_button = new Registry;
        $oc_button->loadString(json_encode($templateparams->get('buf_oc_button')));
        $oc_button->set('buf_offcanvas_selector', $buf_offcanvas_selector);

        // --- Iniciar buffer de salida para capturar el HTML ---
        ob_start();




        // --- Lógica de renderizado ---
        if ($buf_offcanvas_selector === 'buf_offcanvas_bootstrap') :
            //BS BUTTON
            echo self::getBsOffCanvasButton($oc_button);

            // --- BOOTSTRAP OFFCANVAS ---
            echo self::getBootstrapOffCanvas($templateparams, $buf_topbar_oc, $buf_offcanvas_modules);
        elseif ($buf_offcanvas_selector === 'buf_offcanvas_default') :
            //BUTTON
            echo self::getOffCanvasButton($oc_button);

            // --- DEFAULT OFFCANVAS ---
            echo self::getDefaultOffCanvas($templateparams, $buf_topbar_oc, $buf_offcanvas_modules);
        endif;

        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Deriva del Registry de topbar-en-offcanvas los valores que necesita el marcado
     * (activo, clases CSS, logo ya renderizado y módulo asociado). Único punto de esta
     * derivación: antes estaba duplicada literalmente en getDefaultOffCanvas() y
     * getBootstrapOffCanvas().
     *
     * @return array{on: bool, classes: string, logo: string, module: string}
     */
    private static function buildTopbarOcContext(Registry $buf_topbar_oc): array
    {
        $on = (bool) $buf_topbar_oc->get('buf_topbar_on', 0);
        $module = $buf_topbar_oc->get('buf_topbar_module', '');
        $logo = $buf_topbar_oc->get('buf_topbar_image_show', '0')
            ? self::getTopBarImages($buf_topbar_oc)
            : '';

        return [
            'on' => $on,
            'classes' => $on ? 'buf_topbar_oc_on' : '',
            'logo' => $logo,
            'module' => $module,
        ];
    }

    private static function getDefaultOffCanvas($templateparams, $buf_topbar_oc, $buf_offcanvas_modules)
    {

        $buf_offcanvas_style = $templateparams->get('buf_offcanvas_style', 'slide');
        $buf_offcanvas_position = $templateparams->get('buf_offcanvas_position', 'left');

        [
            'on' => $buf_topbar_oc_on,
            'classes' => $buf_topbar_oc_classes,
            'logo' => $buf_topbar_oc_logo,
            'module' => $buf_topbar_oc_module,
        ] = self::buildTopbarOcContext($buf_topbar_oc);

        ob_start();
        ?>
        <div id="buf_offcanvas" aria-modal="true" role="dialog" aria-label="offcanvas" tabindex="-1"
            class="buf_offcanvas <?php echo htmlspecialchars($buf_offcanvas_style, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($buf_offcanvas_position, ENT_QUOTES, 'UTF-8'); ?>">

            <?php if ($buf_topbar_oc_on) : ?>
                <div id="buf_topbar_oc" class="<?php echo htmlspecialchars($buf_topbar_oc_classes, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo $buf_topbar_oc_logo;
                    ?>
                    <?php if ($buf_topbar_oc_module) : ?>
                        <div class="buf_topbar_oc_modulewrapper">
                            <jdoc:include type="modules"
                                name="<?php echo htmlspecialchars($buf_topbar_oc_module, ENT_QUOTES, 'UTF-8'); ?>" style="none" />
                        </div>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div id="buf_topbar_oc" class="buf_topbar_off"></div>
            <?php endif; ?>
            <div class="offcanvas-inner <?php echo 'buf_topbar_oc_' . ($buf_topbar_oc_on ? 'on' : 'off'); ?>">
                <jdoc:include type="modules" name="offcanvas" style="none" />
                <?php
                // Módulos/contenido personalizado pasado como argumento
                echo $buf_offcanvas_modules;

                ?>
            </div>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Generates the HTML for the Bootstrap Offcanvas.
     *
     * @param   Registry  $buf_topbar_oc        The top bar parameters.
     * @param   string    $buf_offcanvas_modules  The Offcanvas modules.
     *
     * @return  string  The generated HTML for the Bootstrap Offcanvas.
     *
     * @since   1.0
     */
    private static function getBootstrapOffCanvas($templateparams, $buf_topbar_oc, $buf_offcanvas_modules)
    {

        $bs_styles = new Registry;
        $bs_styles->loadString(json_encode($templateparams->get('buf_bs_styles')));
        $grid_breakpoints = array(
            //breakpoints
            'xs' => $bs_styles->get('buf_bs_breakpoint_xs', '0'),
            'sm' => $bs_styles->get('buf_bs_breakpoint_sm', '576'),
            'md' => $bs_styles->get('buf_bs_breakpoint_md', '768'),
            'lg' => $bs_styles->get('buf_bs_breakpoint_lg', '992'),
            'xl' => $bs_styles->get('buf_bs_breakpoint_xl', '1200'),
            'xxl' => $bs_styles->get('buf_bs_breakpoint_xxl', '1400'),
        );

        $allowedBreakpoints = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];
        $requestedBreakpoint = $templateparams->get('buf_offcanvas_max_w', 'lg');
        $check_breakpoint = in_array($requestedBreakpoint, $allowedBreakpoints, true)
            ? $requestedBreakpoint
            : 'lg';

        //offcanvas max width match with breakpoint
        $buf_offcanvas_max_w = $grid_breakpoints[$check_breakpoint];

        // Nota: la cabecera del offcanvas se delega en BufTopBar::getTopBar() más abajo,
        // que deriva sus propias variables desde $buf_topbar. No se necesita duplicar
        // aquí esa derivación (ver getDefaultOffCanvas() / buildTopbarOcContext() para
        // el caso en que sí hace falta, en la variante sin Bootstrap).

        $buf_offcanvas_bs_scroll = ($templateparams->get('buf_offcanvas_bs_scroll', 0)) ? 'data-bs-scroll="true"' : '';
        $buf_offcanvas_bs_backdrop = ($templateparams->get('buf_offcanvas_bs_backdrop', 0)) ? 'data-bs-backdrop="true"' : 'data-bs-backdrop="false"';
        $buf_offcanvas_bs_static_backdrop = ($templateparams->get('buf_offcanvas_bs_static_backdrop', 0) && $templateparams->get('buf_offcanvas_bs_backdrop', 0)) ? 'data-bs-static="true"' : '';
        $buf_offcanvas_bs_placement = $templateparams->get('buf_offcanvas_bs_placement', 'start');

        if ($buf_offcanvas_bs_placement == 'top') {
            $buf_offcanvas_bs_position = 'offcanvas-top';
        } elseif ($buf_offcanvas_bs_placement == 'bottom') {
            $buf_offcanvas_bs_position = 'offcanvas-bottom';
        } elseif ($buf_offcanvas_bs_placement == 'end') {
            $buf_offcanvas_bs_position = 'offcanvas-end';
        } else {
            $buf_offcanvas_bs_position = 'offcanvas-start';
        }

        $buf_topbar = new Registry;
        $buf_topbar->loadString(json_encode($templateparams->get('buf_topbar_oc')));
        //$buf_topbar_show_on_scroll = $buf_topbar->get('buf_show_on_scroll', '');
        //$buf_show_on_scroll_onlymobile = ($buf_topbar->get('buf_show_on_scroll_onlymobile', false) == true)  ? true : false;


        ob_start();
        ?>
        <div class="offcanvas <?php echo $buf_offcanvas_bs_position; ?>"
            tabindex="-1"
            id="bsOffcanvas"
            inert
            aria-label="<?php echo Text::_('TPL_BUF_OFFCANVAS_LABEL'); ?>"
            <?php echo $buf_offcanvas_bs_scroll; ?>
            <?php echo $buf_offcanvas_bs_backdrop; ?>
            <?php echo $buf_offcanvas_bs_static_backdrop; ?>>

            <?php
                echo BufTopBar::getTopBar('buf_topbar_oc', $buf_topbar, $buf_offcanvas_max_w, Text::_('TPL_BUF_OFFCANVAS_TOPBAR_MOBILE_LABEL'));
            ?>

            <?php
            /* Descomenta si necesitas cabecera estándar de Bootstrap
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="bufOffcanvasBsLabel">Offcanvas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            */
            ?>

            <div class="offcanvas-body">
                <jdoc:include type="modules" name="offcanvas" style="none" />
                <?php
                // Módulos/contenido personalizado pasado como argumento


                echo $buf_offcanvas_modules;
                ?>
            </div>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


    /**
     * Generates the HTML for the Offcanvas button.
     *
     * @param   Registry  $oc_button  The Offcanvas button parameters.
     *
     * @return  string  The generated HTML for the Offcanvas button.
     *
     * @since   1.0
     */
    private static function getOffCanvasButton($oc_button)
    {
        $buf_oc_button_style = $oc_button->get('buf_oc_button_style', '3dx');
        $buf_oc_button_reverse = $oc_button->get('buf_oc_button_reverse', 'l');
        $buf_oc_reverse = ($buf_oc_button_reverse == 'r') ? '-r' : '';
        $buf_oc_button_vpos = $oc_button->get('buf_oc_button_vpos', 'left');
        $buf_oc_button_hpos = $oc_button->get('buf_oc_button_hpos', 'top');

        $buf_oc_button_bs_tags = '';
        if ($oc_button->get('buf_offcanvas_selector', 'buf_offcanvas_default') == 'buf_offcanvas_bootstrap') {
            $buf_oc_button_bs_tags = 'data-bs-toggle="offcanvas" data-bs-target="#bsOffcanvas" aria-controls="bsOffcanvas"';
        }

        ob_start();
        ?>

        <button type="button" id="bufoc_button"
            class="hamburger hamburger--<?php echo htmlspecialchars($buf_oc_button_style, ENT_QUOTES, 'UTF-8') . htmlspecialchars($buf_oc_reverse, ENT_QUOTES, 'UTF-8'); ?> oc_button_vpos_<?php echo htmlspecialchars($buf_oc_button_vpos, ENT_QUOTES, 'UTF-8'); ?> oc_button_hpos_<?php echo htmlspecialchars($buf_oc_button_hpos, ENT_QUOTES, 'UTF-8'); ?>"
            aria-label="<?php echo htmlspecialchars(Text::_('TPL_BUF_OFFCANVAS_TOGGLE'), ENT_QUOTES, 'UTF-8'); ?>" aria-controls="buf_offcanvas" aria-expanded="false" <?php echo $buf_oc_button_bs_tags; ?>>
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    private static function getBsOffCanvasButton($oc_button)
    {
        $buf_oc_button_style = $oc_button->get('buf_oc_button_style', '3dx');
        $buf_oc_button_reverse = $oc_button->get('buf_oc_button_reverse', 'l');
        $buf_oc_reverse = ($buf_oc_button_reverse == 'r') ? '-r' : '';
        $buf_oc_button_vpos = $oc_button->get('buf_oc_button_vpos', 'left');
        $buf_oc_button_hpos = $oc_button->get('buf_oc_button_hpos', 'top');

        $buf_oc_button_bs_tags = 'data-bs-toggle="offcanvas" data-bs-target="#bsOffcanvas" aria-controls="bsOffcanvas"';

        ob_start();
        ?>

        <button type="button" id="bufoc_button"
            class="hamburger hamburger--<?php echo htmlspecialchars($buf_oc_button_style, ENT_QUOTES, 'UTF-8') . htmlspecialchars($buf_oc_reverse, ENT_QUOTES, 'UTF-8'); ?> oc_button_vpos_<?php echo htmlspecialchars($buf_oc_button_vpos, ENT_QUOTES, 'UTF-8'); ?> oc_button_hpos_<?php echo htmlspecialchars($buf_oc_button_hpos, ENT_QUOTES, 'UTF-8'); ?>"
            aria-label="<?php echo htmlspecialchars(Text::_('TPL_BUF_OFFCANVAS_TOGGLE'), ENT_QUOTES, 'UTF-8'); ?>" aria-controls="bsOffcanvas" aria-expanded="false" <?php echo $buf_oc_button_bs_tags; ?>>
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


    public static function getTopBarImages($buf_topbar): ?string
    {
        $buf_topbar_logo = '';

        // Logo: al menos uno de los dos debe existir
        if ($buf_topbar->get('buf_topbar_logo', '') == '' && $buf_topbar->get('buf_topbar_logo_fallback', '') == '') {
            return null;
        }

        $buf_topbar_logo_img      = HTMLHelper::cleanImageURL($buf_topbar->get('buf_topbar_logo', ''));
        $buf_topbar_logo_fallback = HTMLHelper::cleanImageURL($buf_topbar->get('buf_topbar_logo_fallback', ''));

        // Verificar existencia de archivos
        if ($buf_topbar->get('buf_topbar_logo', '') != '' && !is_file($buf_topbar_logo_img->url)) {
            return null;
        }
        if ($buf_topbar->get('buf_topbar_logo_fallback', '') != '' && !is_file($buf_topbar_logo_fallback->url)) {
            return null;
        }

        // Cachear mime types (evitar múltiples llamadas al sistema de archivos)
        $mime_main     = $buf_topbar_logo_img->url     != '' ? mime_content_type($buf_topbar_logo_img->url)     : '';
        $mime_fallback = $buf_topbar_logo_fallback->url != '' ? mime_content_type($buf_topbar_logo_fallback->url) : '';

        $alt        = htmlspecialchars($buf_topbar->get('buf_topbar_logo_alt', 'logo'));
        $logo_pos   = $buf_topbar->get('buf_topbar_logo_pos', 'l');
        $w100_class = $buf_topbar->get('buf_topbar_module', '') == '' ? 'w100' : '';
        $has_source = $buf_topbar_logo_img->url != '' && $buf_topbar_logo_fallback->url != '';

        $buf_topbar_logo .= '<div class="buf_topbar_logo pos_' . $logo_pos . ' ' . $w100_class . '">';
        $buf_topbar_logo .= '<a href="/index.php" aria-label="' . htmlspecialchars(Text::_('TPL_BUF_TOPBAR_HOME'), ENT_QUOTES, 'UTF-8') . '">';

        // Usar <picture> solo cuando hay source + fallback
        if ($has_source) {
            $buf_topbar_logo .= '<picture>';
            $buf_topbar_logo .= '<source type="' . $mime_main . '" srcset="' . $buf_topbar_logo_img->url . '">';
        }

        // <img>: fallback si existe, de lo contrario imagen principal
        if ($buf_topbar_logo_fallback->url != '') {
            $buf_topbar_logo .= '<img'
                . ' class="img-fluid"'
                . ' src="' . $buf_topbar_logo_fallback->url . '"'
                . ' alt="' . $alt . '"'
                . ' width="' . $buf_topbar_logo_fallback->attributes['width'] . '"'
                . ' height="' . $buf_topbar_logo_fallback->attributes['height'] . '"'
                . '>';
        } else {
            // Solo imagen principal, sin fallback
            $width_height = ($mime_main != 'image/svg+xml')
                ? ' width="' . $buf_topbar_logo_img->attributes['width'] . '"'
                . ' height="' . $buf_topbar_logo_img->attributes['height'] . '"'
                : '';
            $buf_topbar_logo .= '<img'
                . ' class="img-fluid"'
                . ' src="' . $buf_topbar_logo_img->url . '"'
                . ' alt="' . $alt . '"'
                . $width_height
                . '>';
        }

        if ($has_source) {
            $buf_topbar_logo .= '</picture>';
        }

        $buf_topbar_logo .= '</a>';
        $buf_topbar_logo .= '</div>';

        return $buf_topbar_logo;
    }
}
