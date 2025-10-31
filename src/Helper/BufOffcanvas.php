<?php

/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2021 jtotal
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Jtotal\BUF\Site\Helper;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
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
        if (!empty($buf_offcanvas_positions || !empty($buf_offcanvas_loadmodules))) {
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
        ///////////////////////
        $buf_topbar_oc = new Registry;
        $buf_topbar_oc->loadString(json_encode($templateparams->get('buf_topbar_oc')));

        $buf_topbar_oc_on = $buf_topbar_oc->get('buf_topbar_on', 0);
        $buf_topbar_oc_logo_img = $buf_topbar_oc->get('buf_topbar_logo', '');
        $buf_topbar_oc_logo_fallback = $buf_topbar_oc->get('buf_topbar_logo_fallback', '');
        $buf_topbar_oc_logo_alt = $buf_topbar_oc->get('buf_topbar_logo_alt', 'logo');
        $buf_topbar_oc_logo_pos = $buf_topbar_oc->get('buf_topbar_logo_pos', "l");
        $buf_topbar_oc_height = $buf_topbar_oc->get('buf_topbar_height', '90');
        $buf_topbar_oc_color = $buf_topbar_oc->get('buf_topbar_color', '#fff');
        $buf_topbar_oc_module = $buf_topbar_oc->get('buf_topbar_module', '');

        $buf_topbar_oc_classes = '';
        $buf_topbar_oc_logo = '';

        if ($buf_topbar_oc_on) {
            $buf_topbar_oc_classes .= 'buf_topbar_oc_on';
        }

        //logo show
        if ($buf_topbar_oc->get('buf_topbar_image_show', '0')) {
            $buf_topbar_oc_logo = self::getTopBarImages($buf_topbar_oc);
        }

        ///////////////////////
        //OFFCANVAS BUTTON
        ///////////////////////
        $oc_button = new Registry;
        $oc_button->loadString(json_encode($templateparams->get('buf_oc_button')));
        $oc_button->set('buf_offcanvas_selector', $buf_offcanvas_selector);

        $buf_topbar_oc_on = $templateparams->get('buf_topbar_oc_on', 0);
        $buf_topbar_oc_classes = $templateparams->get('buf_topbar_oc_classes', 'default-topbar-oc-class'); // Proporciona una clase por defecto si es necesario
        $buf_topbar_oc_module = $templateparams->get('buf_topbar_oc_module', ''); // Posición del módulo


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

    private static function getDefaultOffCanvas($templateparams, $buf_topbar_oc, $buf_offcanvas_modules)
    {

        $buf_offcanvas_style = $templateparams->get('buf_offcanvas_style', 'slide');
        $buf_offcanvas_position = $templateparams->get('buf_offcanvas_position', 'left');

        $buf_topbar_oc_on = $buf_topbar_oc->get('buf_topbar_on', 0);
        $buf_topbar_oc_on = $buf_topbar_oc->get('buf_topbar_on', 0);
        $buf_topbar_oc_module = $buf_topbar_oc->get('buf_topbar_module', '');

        $buf_topbar_oc_classes = '';
        $buf_topbar_oc_logo = '';

        if ($buf_topbar_oc_on) {
            $buf_topbar_oc_classes .= 'buf_topbar_oc_on';
        }

        //logo show
        if ($buf_topbar_oc->get('buf_topbar_image_show', '0')) {
            $buf_topbar_oc_logo = self::getTopBarImages($buf_topbar_oc);
        }
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


        $buf_topbar_oc_on = $buf_topbar_oc->get('buf_topbar_on', 0);
        $buf_topbar_oc_on = $buf_topbar_oc->get('buf_topbar_on', 0);
        $buf_topbar_oc_module = $buf_topbar_oc->get('buf_topbar_module', '');

        $buf_topbar_oc_classes = '';
        $buf_topbar_oc_logo = '';

        if ($buf_topbar_oc_on) {
            $buf_topbar_oc_classes .= 'buf_topbar_oc_on';
        }

        //logo show
        if ($buf_topbar_oc->get('buf_topbar_image_show', '0')) {
            $buf_topbar_oc_logo = self::getTopBarImages($buf_topbar_oc);
        }


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
            aria-labelledby="bufOffcanvasBsLabel"
            <?php echo $buf_offcanvas_bs_scroll; ?>
            <?php echo $buf_offcanvas_bs_backdrop; ?>
            <?php echo $buf_offcanvas_bs_static_backdrop; ?>>


            <?php
            echo BufTopBar::getTopBar('buf_topbar_oc', $buf_topbar, $buf_offcanvas_max_w);
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

        <button type="button" tabindex="0" id="bufoc_button"
            class="hamburger hamburger--<?php echo htmlspecialchars($buf_oc_button_style, ENT_QUOTES, 'UTF-8') . htmlspecialchars($buf_oc_reverse, ENT_QUOTES, 'UTF-8'); ?> oc_button_vpos_<?php echo htmlspecialchars($buf_oc_button_vpos, ENT_QUOTES, 'UTF-8'); ?> oc_button_hpos_<?php echo htmlspecialchars($buf_oc_button_hpos, ENT_QUOTES, 'UTF-8'); ?>"
            aria-label="Menu" aria-controls="buf_offcanvas" aria-expanded="false" <?php echo $buf_oc_button_bs_tags; ?>>
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

        <button type="button" tabindex="0" id="bufoc_button"
            class="hamburger hamburger--<?php echo htmlspecialchars($buf_oc_button_style, ENT_QUOTES, 'UTF-8') . htmlspecialchars($buf_oc_reverse, ENT_QUOTES, 'UTF-8'); ?> oc_button_vpos_<?php echo htmlspecialchars($buf_oc_button_vpos, ENT_QUOTES, 'UTF-8'); ?> oc_button_hpos_<?php echo htmlspecialchars($buf_oc_button_hpos, ENT_QUOTES, 'UTF-8'); ?>"
            aria-label="Menu" aria-controls="buf_offcanvas" aria-expanded="false" <?php echo $buf_oc_button_bs_tags; ?>>
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


    public static function getTopBarImages($buf_topbar)
    {

        $buf_topbar_classes = '';
        $buf_topbar_logo = '';

        //logo
        if ($buf_topbar->get('buf_topbar_logo', '') == '' && $buf_topbar->get('buf_topbar_logo_fallback', '') == '') {
            return;
        }

        $buf_topbar_logo_img = HTMLHelper::cleanImageURL($buf_topbar->get('buf_topbar_logo', ''));
        $buf_topbar_logo_fallback = HTMLHelper::cleanImageURL($buf_topbar->get('buf_topbar_logo_fallback', ''));

        //check path
        if ($buf_topbar->get('buf_topbar_logo', '') != '') {
            if (!is_file($buf_topbar_logo_img->url)) {
                return;
            }
        }

        if ($buf_topbar->get('buf_topbar_logo_fallback', '') != '') {
            if (!is_file($buf_topbar_logo_fallback->url)) {
                return;
            }
        }

        $buf_topbar_logo .= '<div class="buf_topbar_logo pos_' . $buf_topbar->get('buf_topbar_logo_pos', "l") . ' ' . (($buf_topbar->get('buf_topbar_module', '') == "" ? "w100" : "")) . '">';
        $buf_topbar_logo .= '<a href="index.php">';

        $buf_topbar_logo .= '<picture>';

        //svg
        if ($buf_topbar_logo_img->url != '' && $buf_topbar_logo_fallback->url != '') {
            $buf_topbar_logo .= '<source type="' . mime_content_type($buf_topbar_logo_img->url) . '" srcset="' . $buf_topbar_logo_img->url . '">';
        }

        //fallback
        if ($buf_topbar_logo_fallback->url == '' && $buf_topbar_logo_img->url != '') {
            $buf_topbar_logo .= '<img class="img-fluid" type="' . mime_content_type($buf_topbar_logo_img->url) . '" src=' . $buf_topbar_logo_img->url . ' alt="' . $buf_topbar->get('buf_topbar_logo_alt', 'logo') . '"';
            if (mime_content_type($buf_topbar_logo_img->url) != 'image/svg+xml') {
                $buf_topbar_logo .= 'width="' . $buf_topbar_logo_img->attributes['width'] . '"
                        height="' . $buf_topbar_logo_img->attributes['height'] . '"';
            }
            $buf_topbar_logo .= '/>';
        } else if ($buf_topbar_logo_fallback->url != '') {
            $buf_topbar_logo .= '<img
                        class="img-fluid"
                        type="' . mime_content_type($buf_topbar_logo_fallback->url) . '"
                        src=' . $buf_topbar_logo_fallback->url . '
                        alt="' . $buf_topbar->get('buf_topbar_logo_alt', 'logo') . '"
                        width="' . $buf_topbar_logo_fallback->attributes['width'] . '"
                        height="' . $buf_topbar_logo_fallback->attributes['height'] . '"
                    />';
        }

        $buf_topbar_logo .= '</picture>';

        $buf_topbar_logo .= '</a>';
        $buf_topbar_logo .= '</div>';

        return $buf_topbar_logo;
    }


}
