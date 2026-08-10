<?php

declare(strict_types=1);
/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2026 jtotal
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Jtotal\BUF\Site\Helper;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class BufSass
{

    public static $cachepath = JPATH_SITE . '/cache/buf';
    public static $lesspath = JPATH_SITE . '/media/templates/site/buf/css';
    //public static $vendorpath = JPATH_SITE.'/templates/buf/vendor';
    public static $libspath = JPATH_SITE . '/media/templates/site/buf/libs';
    public static $img_path;
    public static $layout_img_path;
    public static $layout_opath;
    public static $buf_layout;
    public static $recache;
    public static $isajax = false;
    public static $cssmix = 0;
    public static $css_sha = '';
    public static $buf_debug = array();
    public static $is_debug = false;
    public static $startmicro;
    public static $layoutpath;
    public static $buf_bs_container_fluid_max;
    public static $buf_bs_container_max;
    public static $buf_bs_container_content_max;
    //breakpoints
    public static $grid_breakpoints;

    //OFFCANVAS
    public static $buf_offcanvas_width = '90';
    public static $buf_offcanvas_width_desktop = '50';
    public static $buf_offcanvas_style = 'buf_off_move';
    public static $buf_offcanvas_bg_color = '#ffffff';
    public static $buf_oc_button_color;
    public static $buf_oc_button_color_hover;
    public static $buf_oc_button_color_active;
    public static $buf_offcanvas_speed;
    public static $buf_oc_button_style;
    public static $buf_oc_button_reverse;
    public static $buf_oc_button_editor;

    public static $buf_topbar_height;
    public static $buf_topbar_color;

    public static $buf_topbar_oc_height;
    public static $buf_topbar_oc_color;

    public static $buf_bs_on = 5;

    public static $buf_bs_css_source = "cdn";

    // Switch "estilos personalizados de Bootstrap" (buf_bs_styles_selector).
    // Hasta ahora sólo controlaba la visibilidad del subform en el backend
    // (showon) y no se leía en PHP: es lo que habilita los overrides.
    public static $buf_bs_styles_selector = 0;

    public static $bs5_all = ["functions", "variables", "maps", "mixins", "utilities", "root", "reboot", "type", "images",
        "containers", "grid", "tables", "forms", "buttons", "transitions", "dropdown",
        "button-group", "nav", "navbar", "card", "accordion", "breadcrumb", "pagination", "badge",
        "alert", "progress", "list-group", "close", "toasts", "modal", "tooltip", "popover",
        "carousel", "spinners", "offcanvas", "helpers", "api"];

    //just in devel
    public static $debug_develmode_bs = false;

    public static $scss;

    public static $scssElements = array();

    public static $templateparams;



    public static function runsass($templateid = '', $params = '', $template_name = '', $startmicro = '', $bs_or_fa = '')
    {
        $uri = Uri::root();

        self::$startmicro = $startmicro;
        $session = Factory::getApplication()->getSession();

        //TEMPLATE PARAMS IF AJAX
        if ($templateid) {
            //is AJAX
            self::$isajax = true;

            //AJAX
            $currentParams = BufSass::getCurrentParams($templateid);

            if (!$currentParams) {
                \Joomla\CMS\Log\Log::add('BUF SASS: template style not found for id ' . (int) $templateid, \Joomla\CMS\Log\Log::ERROR, 'buf');
                return self::$buf_debug;
            }

            $template_name = $currentParams->template;

            $templateparams = new Registry($currentParams->params);
            self::$templateparams = $templateparams;

            self::$buf_debug += self::addDebug('SASS | common', 'cubes', 'common.scss', $startmicro);

            //forces compiles
            $process = 0;

            $session->set('buf_reload_sass', '0');
        } else {
            //NON-AJAX
            //$templateparams = $params;
            $templateparams = new Registry($params);
            self::$templateparams = $templateparams;
            $template_name = $template_name;
            $process = $templateparams->get('runless', 0);
        }

        self::$is_debug = ($templateparams->get('buf_debug', 0) == 1) ? true : false;

        self::$buf_layout = $templateparams->get('buf_layout', 'default');
        self::$img_path = $uri . 'templates/buf/images/';
        self::$layout_img_path = $uri . 'templates/buf/layouts/' . self::$buf_layout . '/images/';
        self::$layout_opath = $uri . 'templates/buf/layouts/' . self::$buf_layout . '/';

        //PARAMS
        $sass_editor = $templateparams->get('create_editor', 0);
        $sass_compresion = $templateparams->get('buf_sass_compression', 'EXPANDED');

        $buf_fa = $templateparams->get('buf_fa', 1);
        $buf_fa_pro = $templateparams->get('buf_fa_pro', 0);
        $buf_fa_selector = $templateparams->get('buf_fa_selector', 'jdefault');
        $buf_fa5_tech = $templateparams->get('buf_fa5_tech', '1');
        $buf_fa5_files = $templateparams->get('buf_fa5_files', array('solid'));
        if (is_string($buf_fa5_files)) {
            $buf_fa5_files = array("solid");
        }

        if ($buf_fa_pro == 0) {
            $buf_fa5_files = array_diff($buf_fa5_files, ["light", "duotone"]);
        }

        $buf_fa5_fa4fallback = $templateparams->get('buf_fa4fallback', 0);

        self::$buf_bs_on = $templateparams->get('buf_bs_on', 5);
        $buf_bs_on = $templateparams->get('buf_bs_on', 5);

        $bs_4 = new Registry;
        $bs_4->loadString(json_encode($templateparams->get('buf_bs_v4')));

        $bs_5 = new Registry;
        $bs_5->loadString(json_encode($templateparams->get('buf_bs_v5')));

        self::$buf_bs_css_source = $bs_5->get('buf_bootstrap_css', 'cdn');

        self::$buf_bs_styles_selector = $templateparams->get('buf_bs_styles_selector', 0);

        $bs_styles = new Registry;
        $bs_styles->loadString(json_encode($templateparams->get('buf_bs_styles')));

        //bs5
        $bs_custom_colors = array(
            'bs_custom_body_bg' => $bs_styles->get('bs_custom_body_bg', ''),
            'bs_custom_body_color' => $bs_styles->get('bs_custom_body_color', ''),
            'bs_custom_primary' => $bs_styles->get('bs_custom_primary', ''),
            'bs_custom_secondary' => $bs_styles->get('bs_custom_secondary', ''),
            'bs_custom_success' => $bs_styles->get('bs_custom_success', ''),
            'bs_custom_info' => $bs_styles->get('bs_custom_info', ''),
            'bs_custom_warning' => $bs_styles->get('bs_custom_warning', ''),
            'bs_custom_danger' => $bs_styles->get('bs_custom_danger', ''),
            'bs_custom_light' => $bs_styles->get('bs_custom_light', ''),
            'bs_custom_dark' => $bs_styles->get('bs_custom_dark', ''),
            'bs_custom_design_rounded' => $bs_styles->get('bs_custom_design_rounded', '1'),
            'bs_custom_design_gradients' => $bs_styles->get('bs_custom_design_gradients', '1'),
            'bs_custom_design_variables' => $templateparams->get('buf_bs_design'),
        );

        self::$grid_breakpoints = array(
            //breakpoints
            'xs' => $bs_styles->get('buf_bs_breakpoint_xs', '0'),
            'sm' => $bs_styles->get('buf_bs_breakpoint_sm', '576'),
            'md' => $bs_styles->get('buf_bs_breakpoint_md', '768'),
            'lg' => $bs_styles->get('buf_bs_breakpoint_lg', '992'),
            'xl' => $bs_styles->get('buf_bs_breakpoint_xl', '1200'),
            'xxl' => $bs_styles->get('buf_bs_breakpoint_xxl', '1400'),
        );

        //SET CONTAINER PARAMS
        self::buf_get_container($templateparams->get('buf_bs_container_fluid_max', '100%'), $templateparams->get('buf_bs_container_max', '1140'), $templateparams->get('buf_bs_container_content_max', '1140'));

        self::$recache = $templateparams->get('force_recache', '0');

        self::$cssmix = $templateparams->get('buf_scss_mix', '0');

        ///////////////////////
        //OFFCANVAS
        ///////////////////////
        self::$buf_offcanvas_bg_color = $templateparams->get('buf_offcanvas_bg_color', '#ffffff');

        self::$buf_offcanvas_width = $templateparams->get('buf_offcanvas_width', '90');
        self::$buf_offcanvas_width_desktop = $templateparams->get('buf_offcanvas_width_desktop', '50');
        self::$buf_offcanvas_style = $templateparams->get('buf_offcanvas_style', 'buf_off_move');
        self::$buf_offcanvas_speed = $templateparams->get('buf_offcanvas_speed', '300');

        $oc_button = new Registry;
        $oc_button->loadString(json_encode($templateparams->get('buf_oc_button')));

        self::$buf_oc_button_style = $oc_button->get('buf_oc_button_style', '3dx');
        self::$buf_oc_button_reverse = $oc_button->get('buf_oc_button_reverse', 'l');
        self::$buf_oc_button_color = $oc_button->get('buf_oc_button_color', '#000000');
        self::$buf_oc_button_color_hover = $oc_button->get('buf_oc_button_color_hover', '#000');
        self::$buf_oc_button_color_active = $oc_button->get('buf_oc_button_color_active', '#000');
        self::$buf_oc_button_editor = $oc_button->get('buf_oc_button_editor', '-ms-flex-pack: justify');

        $buf_topbar = new Registry;
        $buf_topbar->loadString(json_encode($templateparams->get('buf_topbar')));

        self::$buf_topbar_height = $buf_topbar->get('buf_topbar_height', '54');
        self::$buf_topbar_color = $buf_topbar->get('buf_topbar_color', '#fff');

        $buf_topbar_oc = new Registry;
        $buf_topbar_oc->loadString(json_encode($templateparams->get('buf_topbar_oc')));

        self::$buf_topbar_oc_height = $buf_topbar_oc->get('buf_topbar_height', '54');
        self::$buf_topbar_oc_color = $buf_topbar_oc->get('buf_topbar_color', '#fff');

        //CIRCUS start

        self::$layoutpath = JPATH_SITE . '/templates/buf/layouts/' . self::$buf_layout;

        $sass_bs_files = array();
        $sass_fa_files = array();

        self::$cachepath = self::$cachepath . '_' . self::$buf_layout;

        $buf_bs_css_exists = file_exists(self::$cachepath . '/buf_bs.css');
        $buf_fa_css_exists = file_exists(self::$cachepath . '/buf_fa.css');
        $buf_css_exists = file_exists(self::$cachepath . '/buf.css');

        /**********************************/
        /*******   BS & FA FILES     ******/
        /**********************************/

        //AJAX fa
        //if($bs_or_fa != 'fa'){
        if ($process == 0 || $buf_bs_css_exists == false || self::$debug_develmode_bs) {
            if ($buf_bs_on) {
                //BS5
                if ($buf_bs_on == 5 && self::$buf_bs_css_source == 'custom') {
                    if ($bs_5->get('buf_bs_selector', 'recommended') != 'none') {
                        //BOOSTRAP
                        //select all the boxes into bs5files array
                        $bs5files = $bs_5->get('buf_bs_files_core', array());
                        $bs5files = array_merge($bs5files, $bs_5->get('buf_bs_files_layout', array()));
                        $bs5files = array_merge($bs5files, $bs_5->get('buf_bs_files_components', array()));
                        $bs5files = array_merge($bs5files, $bs_5->get('buf_bs_files_helpers', array()));

                        //aproximacion diferente:
                        //recorro el array de todos archivos y veo si coincide. Así preservo el orden.
                        foreach (self::$bs5_all as $key => $value) {
                            if (in_array($value, $bs5files)) {
                                //special api case
                                if ($value == 'api') {
                                    $sass_bs_files += array(self::$libspath . '/bootstrap/scss/utilities/_' . $value . '.scss' => $uri);
                                } else {
                                    $sass_bs_files += array(self::$libspath . '/bootstrap/scss/_' . $value . '.scss' => $uri);
                                }

                                self::$buf_debug += self::addDebug('BS5 | ' . $value, 'bootstrap fab', '/bootstrap/scss/_' . $value . '.scss', $startmicro, 'table-secondary');
                            }
                        }

                        //$sass_bs_files += array(self::$libspath . '/bootstrap/scss/utilities/_api.scss' => $uri);
                    }
                }
            }
        }

        /**********************************/
        /*******      FA FILES       ******/
        /**********************************/

        $fa_process = false;

        //ON
        if ($process == 0 || $buf_fa_css_exists == false) {

            if ($buf_fa_selector == 0) {
                $buf_fa = 0;
            }

            if ($buf_fa_selector == 'jdefault') {
                $buf_fa = 0;
            }

            //files ON
            if ($buf_fa != 0) {
                $fa_process = true;
                //AJAX BS
                if ($bs_or_fa == 'bs') {
                    $fa_process = false;
                }
            } else {
                self::$buf_debug += self::addDebug('FA | ', 'hand-paper fab', 'NOT LOADED', $startmicro, 'table-info');
            }
        }


        


        /**********************************/
        /**********************************/
        /*******   SASS COMPILER     ******/
        /**********************************/
        /**********************************/


        require_once JPATH_SITE . '/templates/buf/src/loadphpscss.php';
        self::$scss = $scss;

        //SET IMPORT PATHS
        self::$scss->addImportPath(self::$libspath); //media/templates/site/buf/libs
        self::$scss->addImportPath(self::$lesspath); //media/templates/site/buf/css
        self::$scss->addImportPath(self::$layoutpath); //templates/buf/layouts/default
        self::$scss->addImportPath(self::$layoutpath . '/elements'); //templates/buf/layouts/default/elements




        if ($sass_compresion == 'EXPANDED') {
            self::$scss->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::EXPANDED);
        } else {
            self::$scss->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
        }
       
        /*****************************************/
        /*******    BUF SCSS compiler       ******/
        /**********************************/
        self::$css_sha = self::get_template_name();

        // MIX
        if (self::$cssmix == '1') {

            //CHECK FILES
            if ($process != 2 || file_exists(self::$cachepath . '/' . self::$css_sha . '_mix.css') == false) {

                //Compile buf
                if (file_exists(self::$cachepath . '/buf_bs.css') != true) {
                    self::buf_bs_scss($scss, $bs_custom_colors, $sass_bs_files, $process, $bs_or_fa);
                }
                //Compile fa
                if (file_exists(self::$cachepath . '/buf_fa.css') != true) {

                    self::buf_fa_scss($scss, $sass_fa_files, $process, $bs_or_fa);
                }

                //Compile template
                if (file_exists(self::$cachepath . '/' . self::$css_sha . '.css') != true) {
                    self::tmpl_scss($scss, $process);
                }

                $buf_bs_css = file_get_contents(self::$cachepath . '/buf_bs.css');
                $buf_fa_css = file_get_contents(self::$cachepath . '/buf_fa.css');
                $tmpl_css = file_get_contents(self::$cachepath . '/' . self::$css_sha . '.css');

                $cssOut = $buf_bs_css . $buf_fa_css . $tmpl_css;

                file_put_contents(self::$cachepath . '/' . self::$css_sha . '_mix.css', $cssOut);

                self::$buf_debug += self::addDebug('CSS | MIX', 'css3-alt fab', 'WRITTEN <small>' . self::$cachepath . '/' . self::$css_sha . '_mix.css </small>', self::$startmicro, 'table-info');
            }

            //NOMIX
        } else {

            // BUF_BS.css
            if ($process == 0 || $buf_bs_css_exists == false || file_exists(self::$cachepath . '/buf_bs.css') == false || self::$debug_develmode_bs) {
                self::buf_bs_scss($scss, $bs_custom_colors, $sass_bs_files, $process, 'bs');
            }

            // BUF_FA.css
            if ($process == 0 || $buf_fa_css_exists == false || file_exists(self::$cachepath . '/buf_fa.css') == false) {
                self::buf_fa_scss($scss, $sass_fa_files, $process, 'fa');
            }

            //JOIN TO BUF
            if ($process == 0 || file_exists(self::$cachepath . '/buf.css') == false || self::$debug_develmode_bs) {

                $buf_bs_css = file_get_contents(self::$cachepath . '/buf_bs.css');
                $buf_fa_css = file_get_contents(self::$cachepath . '/buf_fa.css');

                $cssOut = $buf_bs_css . $buf_fa_css;

                file_put_contents(self::$cachepath . '/buf.css', $cssOut);

                self::$buf_debug += self::addDebug('CSS JOINED', 'css3-alt fab', 'WRITTEN <small>' . self::$cachepath . '/buf.css</small>', self::$startmicro, 'table-info');

            }

            // template.css
            if ($process != 2 || self::$recache || file_exists(self::$cachepath . '/' . self::$css_sha . '.css') == false) {
                self::tmpl_scss($scss, $process);
            }

        }

        //EDITOR
        //editor scss
        if ($sass_editor) {
            if ($process == 0 || file_exists(self::$layoutpath . '/scss/editor.css') == false) {

                $buf_bs_css = file_get_contents(self::$cachepath . '/buf_bs.css');
                $tmpl_css = file_get_contents(self::$cachepath . '/' . self::$css_sha . '.css');
                $cssOut = $buf_bs_css . $tmpl_css;
                file_put_contents(self::$layoutpath . '/scss/editor.css', $cssOut);

            }
        }

        /**********************************/
        /**********************************/
        /******* AJAX BS & FA COMPILATION  *****/
        /**********************************/
        /**********************************/

        if ($session->get('buf_reload_bs_sass') == '1') {

            //Compile buf
            self::buf_bs_scss($scss, $bs_custom_colors, $sass_bs_files, $process, $bs_or_fa);

            $session->set('buf_reload_bs_sass', '0');
            return self::$buf_debug;
        }

        if ($session->get('buf_reload_fa_sass') == '1') {

            //Compile buf

            self::buf_fa_scss($scss, $sass_fa_files, $process, $bs_or_fa);

            $session->set('buf_reload_fa_sass', '0');
            return self::$buf_debug;
        }

        return self::$buf_debug;

    }

    /***************************************************/
    /*******  OVERRIDES DE COLOR SOBRE BS PRECOMP  *****/
    /***************************************************/

    /**
     * Normaliza un color a [r, g, b]. Devuelve null si no se reconoce.
     *
     * Acepta rgb()/rgba() además de hex porque los campos del subform
     * buf_bs_styles son type="color" con format="rgba": guardan
     * "rgba(219, 0, 0, 1)", no "#db0000".
     *
     * El canal alfa se ignora a propósito: los colores de tema de Bootstrap son
     * opacos y shade()/tint() operan sobre RGB.
     */
    private static function bsParseColor($color)
    {
        $color = trim((string) $color);

        if ($color === '') {
            return null;
        }

        if (preg_match('/^rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)/i', $color, $m)) {
            return [
                (int) round((float) $m[1]),
                (int) round((float) $m[2]),
                (int) round((float) $m[3]),
            ];
        }

        $hex = ltrim($color, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // #rrggbbaa: se descarta el alfa
        if (strlen($hex) === 8) {
            $hex = substr($hex, 0, 6);
        }

        if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
            return null;
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    private static function bsRgb2Hex($rgb)
    {
        return sprintf(
            '#%02x%02x%02x',
            max(0, min(255, (int) $rgb[0])),
            max(0, min(255, (int) $rgb[1])),
            max(0, min(255, (int) $rgb[2]))
        );
    }

    /**
     * Equivalente a shade-color() de Sass (mezcla con negro).
     * PHP redondea .5 hacia arriba igual que Sass, así que el resultado es
     * idéntico byte a byte al CSS que compila Bootstrap.
     */
    private static function bsShade($hex, $weight)
    {
        $c = self::bsParseColor($hex);
        $f = 1 - $weight;

        return self::bsRgb2Hex([round($c[0] * $f), round($c[1] * $f), round($c[2] * $f)]);
    }

    /**
     * Equivalente a tint-color() de Sass (mezcla con blanco).
     */
    private static function bsTint($hex, $weight)
    {
        $c = self::bsParseColor($hex);
        $m = function ($v) use ($weight) {
            return round($v + (255 - $v) * $weight);
        };

        return self::bsRgb2Hex([$m($c[0]), $m($c[1]), $m($c[2])]);
    }

    private static function bsRgbList($hex)
    {
        return implode(',', self::bsParseColor($hex));
    }

    /**
     * Luminancia relativa WCAG.
     */
    private static function bsLuminance($hex)
    {
        $lin = [];

        foreach (self::bsParseColor($hex) as $v) {
            $v = $v / 255;
            $lin[] = ($v <= 0.03928) ? $v / 12.92 : pow(($v + 0.055) / 1.055, 2.4);
        }

        return 0.2126 * $lin[0] + 0.7152 * $lin[1] + 0.0722 * $lin[2];
    }

    /**
     * Equivalente a color-contrast() de Bootstrap: prueba blanco, luego negro,
     * contra el ratio mínimo 4.5; si ninguno llega, devuelve el mejor de los dos.
     */
    private static function bsContrast($hex)
    {
        $lb = self::bsLuminance($hex);

        $ratio = function ($other) use ($lb) {
            $lo = self::bsLuminance($other);

            return (max($lb, $lo) + 0.05) / (min($lb, $lo) + 0.05);
        };

        if ($ratio('#ffffff') > 4.5) {
            return '#fff';
        }

        if ($ratio('#000000') > 4.5) {
            return '#000';
        }

        return ($ratio('#ffffff') >= $ratio('#000000')) ? '#fff' : '#000';
    }

    /**
     * Reproduce el mixin button-variant() de Bootstrap 5.3.
     *
     * 'light' y 'dark' son casos especiales: Bootstrap les fuerza shade y tint
     * respectivamente, al contrario de lo que dictaría su color de contraste.
     */
    private static function bsBtnVars($base, $name = '')
    {
        $contrast = self::bsContrast($base);

        if ($name === 'light') {
            $useShade = true;
        } elseif ($name === 'dark') {
            $useShade = false;
        } else {
            $useShade = ($contrast === '#fff');
        }

        return [
            '--bs-btn-color'                 => $contrast,
            '--bs-btn-bg'                    => $base,
            '--bs-btn-border-color'          => $base,
            '--bs-btn-hover-color'           => $contrast,
            '--bs-btn-hover-bg'              => $useShade ? self::bsShade($base, .15) : self::bsTint($base, .15),
            '--bs-btn-hover-border-color'    => $useShade ? self::bsShade($base, .20) : self::bsTint($base, .10),
            // El focus sí sigue el contraste, no el caso especial light/dark.
            '--bs-btn-focus-shadow-rgb'      => self::bsRgbList(
                $contrast === '#fff' ? self::bsTint($base, .15) : self::bsShade($base, .15)
            ),
            '--bs-btn-active-color'          => $contrast,
            '--bs-btn-active-bg'             => $useShade ? self::bsShade($base, .20) : self::bsTint($base, .20),
            '--bs-btn-active-border-color'   => $useShade ? self::bsShade($base, .25) : self::bsTint($base, .10),
            '--bs-btn-disabled-color'        => $contrast,
            '--bs-btn-disabled-bg'           => $base,
            '--bs-btn-disabled-border-color' => $base,
        ];
    }

    private static function bsBtnOutlineVars($base)
    {
        $contrast = self::bsContrast($base);

        return [
            '--bs-btn-color'                 => $base,
            '--bs-btn-border-color'          => $base,
            '--bs-btn-hover-color'           => $contrast,
            '--bs-btn-hover-bg'              => $base,
            '--bs-btn-hover-border-color'    => $base,
            '--bs-btn-focus-shadow-rgb'      => self::bsRgbList($base),
            '--bs-btn-active-color'          => $contrast,
            '--bs-btn-active-bg'             => $base,
            '--bs-btn-active-border-color'   => $base,
            '--bs-btn-disabled-color'        => $base,
            '--bs-btn-disabled-border-color' => $base,
        ];
    }

    private static function bsRule($selector, $vars)
    {
        $out = $selector . '{';

        foreach ($vars as $prop => $value) {
            $out .= $prop . ':' . $value . ';';
        }

        return $out . '}';
    }

    /**
     * Genera CSS plano para recolorear un Bootstrap YA COMPILADO (el de Joomla
     * o el del CDN), donde no hay variables Sass que tocar.
     *
     * Bootstrap 5.3 define --bs-primary en :root pero NUNCA la consume: los
     * colores van horneados como literales (39 apariciones de #0d6efd frente a
     * 0 de var(--bs-primary)). De ahí que hagan falta tres capas:
     *
     *   1) variables derivadas de :root -> utilidades, alerts, list-group, links
     *   2) grupos --bs-* por componente  -> botones, pagination, nav-pills, ...
     *   3) propiedades con literal       -> focus de formularios y form-range
     *
     * @param   array  $bs_custom  Colores del subform buf_bs_styles
     *
     * @return  string  CSS (no SCSS): se concatena tras la compilación
     */
    private static function bsColorOverrides($bs_custom)
    {
        $names = ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'dark'];

        $colors = [];

        foreach ($names as $name) {
            $rgb = self::bsParseColor($bs_custom['bs_custom_' . $name] ?? '');

            // Vacío = respetar el color de Bootstrap. No reconocido = ignorar.
            // Se normaliza a hex para que toda la generación trabaje igual.
            if ($rgb !== null) {
                $colors[$name] = self::bsRgb2Hex($rgb);
            }
        }

        $body_bg    = self::bsParseColor($bs_custom['bs_custom_body_bg'] ?? '');
        $body_color = self::bsParseColor($bs_custom['bs_custom_body_color'] ?? '');
        $rounded    = isset($bs_custom['bs_custom_design_rounded']) ? (string) $bs_custom['bs_custom_design_rounded'] : '1';

        $css = '';

        /*** CAPA 1: variables de :root ***/
        $root = '';

        foreach ($colors as $name => $base) {
            $root .= '--bs-' . $name . ':' . $base . ';';
            $root .= '--bs-' . $name . '-rgb:' . self::bsRgbList($base) . ';';

            // En Bootstrap, light y dark NO derivan sus tonos subtle/emphasis
            // del color: son grises fijos ($gray-700, $gray-200, ...). Se
            // respetan para no romper .alert-light ni .list-group-item-dark.
            if ($name === 'light' || $name === 'dark') {
                continue;
            }

            $root .= '--bs-' . $name . '-text-emphasis:' . self::bsShade($base, .60) . ';';
            $root .= '--bs-' . $name . '-bg-subtle:' . self::bsTint($base, .80) . ';';
            $root .= '--bs-' . $name . '-border-subtle:' . self::bsTint($base, .60) . ';';
        }

        if (isset($colors['primary'])) {
            $link_hover = self::bsShade($colors['primary'], .20);

            $root .= '--bs-link-color:' . $colors['primary'] . ';';
            $root .= '--bs-link-color-rgb:' . self::bsRgbList($colors['primary']) . ';';
            $root .= '--bs-link-hover-color:' . $link_hover . ';';
            $root .= '--bs-link-hover-color-rgb:' . self::bsRgbList($link_hover) . ';';
        }

        if ($body_bg !== null) {
            $root .= '--bs-body-bg:' . self::bsRgb2Hex($body_bg) . ';'
                . '--bs-body-bg-rgb:' . implode(',', $body_bg) . ';';
        }

        if ($body_color !== null) {
            $root .= '--bs-body-color:' . self::bsRgb2Hex($body_color) . ';'
                . '--bs-body-color-rgb:' . implode(',', $body_color) . ';';
        }

        if ($rounded === '0') {
            $root .= '--bs-border-radius:0;--bs-border-radius-sm:0;--bs-border-radius-lg:0;'
                . '--bs-border-radius-xl:0;--bs-border-radius-xxl:0;--bs-border-radius-2xl:0;';
        }

        if ($root === '') {
            return '';
        }

        // Mismo selector que usa Bootstrap: al cargarse después, gana por orden.
        $css .= ':root,[data-bs-theme=light]{' . $root . '}';

        if (empty($colors)) {
            return $css;
        }

        /*** CAPA 2: botones, para todos los colores definidos ***/
        foreach ($colors as $name => $base) {
            $css .= self::bsRule('.btn-' . $name, self::bsBtnVars($base, $name));
            $css .= self::bsRule('.btn-outline-' . $name, self::bsBtnOutlineVars($base));
        }

        // El resto de componentes usan $component-active-bg, que es $primary.
        if (!isset($colors['primary'])) {
            return $css;
        }

        $primary      = $colors['primary'];
        $on_primary   = self::bsContrast($primary);
        $focus_shadow = '0 0 0 .25rem rgba(' . self::bsRgbList($primary) . ',.25)';
        $focus_border = self::bsTint($primary, .50);

        $css .= self::bsRule('.pagination', [
            '--bs-pagination-focus-box-shadow'    => $focus_shadow,
            '--bs-pagination-active-color'        => $on_primary,
            '--bs-pagination-active-bg'           => $primary,
            '--bs-pagination-active-border-color' => $primary,
        ]);

        $css .= self::bsRule('.nav-pills', [
            '--bs-nav-pills-link-active-color' => $on_primary,
            '--bs-nav-pills-link-active-bg'    => $primary,
        ]);

        $css .= self::bsRule('.list-group', [
            '--bs-list-group-active-color'        => $on_primary,
            '--bs-list-group-active-bg'           => $primary,
            '--bs-list-group-active-border-color' => $primary,
        ]);

        $css .= self::bsRule('.dropdown-menu', [
            '--bs-dropdown-link-active-color' => $on_primary,
            '--bs-dropdown-link-active-bg'    => $primary,
        ]);

        $css .= self::bsRule('.progress,.progress-stacked', [
            '--bs-progress-bar-color' => $on_primary,
            '--bs-progress-bar-bg'    => $primary,
        ]);

        $css .= self::bsRule('.btn-close', [
            '--bs-btn-close-focus-shadow' => $focus_shadow,
        ]);

        // Estos dos llevan el color DENTRO de un SVG en data-URI, url-encoded.
        $acc_icon = "url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none' stroke='"
            . str_replace('#', '%23', self::bsShade($primary, .60))
            . "' stroke-linecap='round' stroke-linejoin='round'%3e%3cpath d='m2 5 6 6 6-6'/%3e%3c/svg%3e\")";

        $css .= self::bsRule('.accordion', [
            '--bs-accordion-btn-focus-box-shadow' => $focus_shadow,
            '--bs-accordion-btn-active-icon'      => $acc_icon,
        ]);

        $switch_bg = "url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='"
            . str_replace('#', '%23', $focus_border)
            . "'/%3e%3c/svg%3e\")";

        $css .= self::bsRule('.form-switch .form-check-input:focus', [
            '--bs-form-switch-bg' => $switch_bg,
        ]);

        /*** CAPA 3: aquí Bootstrap no usa variables, hornea el literal ***/
        $thumb_active = self::bsTint($primary, .70);

        $css .= '.form-control:focus,.form-select:focus,.form-check-input:focus{'
            . 'border-color:' . $focus_border . ';box-shadow:' . $focus_shadow . ';}';
        $css .= '.form-check-input:checked,.form-check-input[type=checkbox]:indeterminate{'
            . 'background-color:' . $primary . ';border-color:' . $primary . ';}';
        $css .= '.form-range::-webkit-slider-thumb{background-color:' . $primary . ';}';
        $css .= '.form-range::-moz-range-thumb{background-color:' . $primary . ';}';
        $css .= '.form-range::-webkit-slider-thumb:active{background-color:' . $thumb_active . ';}';
        $css .= '.form-range::-moz-range-thumb:active{background-color:' . $thumb_active . ';}';
        $css .= '.form-range:focus::-webkit-slider-thumb{box-shadow:0 0 0 1px #fff,' . $focus_shadow . ';}';
        $css .= '.form-range:focus::-moz-range-thumb{box-shadow:0 0 0 1px #fff,' . $focus_shadow . ';}';
        $css .= '.nav-link:focus-visible{box-shadow:' . $focus_shadow . ';}';

        return $css;
    }

    /***************************************/
    /*******    COMPILE BUF_BS.CSS       ******/
    /***************************************/
    private static function buf_bs_scss($scss, $bs_custom, $sass_bs_files, $process, $bs_or_fa = '')
    {


        if ($bs_or_fa == 'fa') {
            return;
        }

        $imports = '';

        //COMMONS
        $imports .= '$grid-breakpoints: (
            xs: 0,
            sm: 576px,
            md: 768px,
            lg: 992px,
            xl: 1200px,
            xxl: 1400px
          ) !default;';

        $imports .= '
          $grid-breakpoints: (
              xs: ' . self::$grid_breakpoints['xs'] . ',
              sm: ' . self::$grid_breakpoints['sm'] . 'px,
              md: ' . self::$grid_breakpoints['md'] . 'px,
              lg: ' . self::$grid_breakpoints['lg'] . 'px,
              xl: ' . self::$grid_breakpoints['xl'] . 'px,
              xxl: ' . self::$grid_breakpoints['xxl'] . 'px
            );
          ';

        if (self::$buf_bs_on != 0) {
            if (self::$buf_bs_css_source == "custom") {
                //Defaults
                $imports .= '
                    $blue:    #0d6efd !default;
                    $indigo:  #6610f2 !default;
                    $purple:  #6f42c1 !default;
                    $pink:    #d63384 !default;
                    $red:     #dc3545 !default;
                    $orange:  #fd7e14 !default;
                    $yellow:  #ffc107 !default;
                    $green:   #198754 !default;
                    $teal:    #20c997 !default;
                    $cyan:    #0dcaf0 !default;

                    $white:    #fff !default;
                    $gray-100: #f8f9fa !default;
                    $gray-200: #e9ecef !default;
                    $gray-300: #dee2e6 !default;
                    $gray-400: #ced4da !default;
                    $gray-500: #adb5bd !default;
                    $gray-600: #6c757d !default;
                    $gray-700: #495057 !default;
                    $gray-800: #343a40 !default;
                    $gray-900: #212529 !default;
                    $black:    #000 !default;

                    $primary:       $blue !default;
                    $secondary:     $gray-600 !default;
                    $success:       $green !default;
                    $info:          $cyan !default;
                    $warning:       $yellow !default;
                    $danger:        $red !default;
                    $light:         $gray-100 !default;
                    $dark:          $gray-900 !default;
                ';

                //DESIGN
                //ROUNDED
                $imports .= ($bs_custom['bs_custom_design_rounded'] == "1") ? '$enable-rounded: true;' : '$enable-rounded: false;';

                //GRADIENTS
                $imports .= ($bs_custom['bs_custom_design_gradients'] == "1") ? '$enable-gradients: true;' : '$enable-gradients: false;';

                $imports .= self::bs5_custom($bs_custom);

                $bs_design = new Registry;
                //$bs_design->loadString(json_encode($templateparams->get('buf_bs_design')));

                //$imports .= '$body-color: indigo;';

                //recorro los archivos de bs5
                foreach ($sass_bs_files as $key => $value) {
                    $filename = basename($key);

                    $imports .= '@import "' . $key . '";';

                    //ROOT OVERRIDE
                    if ($filename == '_root.scss') {

                        $imports .= ':root{';

                        if ($bs_custom['bs_custom_body_bg'] != '') {
                            $imports .= '
                                    --bs-body-bg:' . $bs_custom['bs_custom_body_bg'] . ';
                            ';
                        }
                        foreach($bs_custom['bs_custom_design_variables'] as $key => $value){
                             $root_variable = '
                                    --'.$value['buf_bs_root_variable'].':'.$value['buf_bs_root_value'].';
                            ';
                            $imports .= $root_variable;
                        }
                        $imports .= '}';
                    }
                }
            } else {
                self::$buf_debug += self::addDebug('BS CSS SOURCE', 'css3-alt fab', 'NOT CUSTOM <small>' . self::$buf_bs_css_source, self::$startmicro, 'table-info');
            }
        }

        //buf_bs_fluid_max
        $imports .= '@media (min-width: map-get($grid-breakpoints, xl)) {
          .container-fluid {
              max-width: ' . self::$buf_bs_container_fluid_max . ';
          }

          .container {
              max-width: ' . self::$buf_bs_container_max . ';
          }

          #contents.container-fluid{
              max-width: ' . self::$buf_bs_container_content_max . ';
          }
           #contents.container{
              max-width: ' . self::$buf_bs_container_content_max . ';
          }
        }';
        
        try {
            $result = self::$scss->compileString($imports);
            $cssOut = $result->getCss();
        } catch (Exception $e) {
            // Handle compilation error
            $cssOut = '/* SCSS Compilation Error: ' . $e->getMessage() . ' */';
            self::$buf_debug += self::addDebug('SCSS ERROR', 'exclamation-triangle', $e->getMessage(), self::$startmicro, 'table-danger');
        }

        // Overrides de color sobre Bootstrap. Se añaden DESPUÉS de compilar, como
        // CSS plano, para que los data-URI de los SVG no pasen por scssphp.
        // Se aplican en joomla / cdn / custom (todo menos "sin Bootstrap") y sólo
        // con el switch de estilos personalizados activo.
        if (self::$buf_bs_on != 0
            && (string) self::$buf_bs_styles_selector === '1'
            && !in_array((string) self::$buf_bs_css_source, ['0', ''], true)) {
            $overrides = self::bsColorOverrides($bs_custom);

            if ($overrides !== '') {
                $cssOut .= "\n/* BUF | overrides de color Bootstrap */\n" . $overrides . "\n";

                self::$buf_debug += self::addDebug(
                    'BS COLOR OVERRIDES',
                    'palette',
                    'APLICADOS <small>fuente: ' . self::$buf_bs_css_source . '</small>',
                    self::$startmicro,
                    'table-success'
                );
            }
        }



        //Check cache directory is created
        if (!file_exists(self::$cachepath)) {
            mkdir(self::$cachepath, 0775, true);
        }

        if(self::$is_debug) file_put_contents(self::$cachepath . '/buf_bs.scss', $imports);

        file_put_contents(self::$cachepath . '/buf_bs.css', $cssOut);

        //if(self::$cssmix) file_put_contents(self::$cachepath . '/buf_comp.scss', $cssOut);

        if ($process == '0') {
            //ALL
            self::$buf_debug += self::addDebug('CSS BUF | ALL', 'css3-alt fab', 'WRITTEN ALL reload<small>' . self::$cachepath . '/buf_bs.css</small>', self::$startmicro, 'table-info');

        } elseif ($process == '1') {
            //OWN
            self::$buf_debug += self::addDebug('CSS BUF | OWN', 'css3-alt fab', 'WRITTEN OWN reload<small>' . self::$cachepath . '/buf_bs.css</small>', self::$startmicro, 'table-info');
        } else {
            //PRODUCTION
            self::$buf_debug += self::addDebug('CSS BUF | PROD', 'css3-alt fab', 'WRITTEN NOT EXISTS <small>' . self::$cachepath . '/buf_bs.css</small>', self::$startmicro, 'table-warning');
        }

    }

    /***************************************/
    /*******    COMPILE BUF_FA.CSS       ******/
    /***************************************/

    private static function buf_fa_scss($scss, $sass_fa_files, $process, $bs_or_fa = '')
    {
        if ($bs_or_fa == 'bs') {
            return;
        }

        $imports = '';

        foreach ($sass_fa_files as $key => $value) {
            $imports .= '@import "' . $key . '";';
        }
   
        $result = self::$scss->compileString($imports);
        $cssOut = $result->getCss();
        //Check cache directory is created
        if (!file_exists(self::$cachepath)) {
            mkdir(self::$cachepath, 0775, true);
        }

        file_put_contents(self::$cachepath . '/buf_fa.css', $cssOut);

        if ($process == '0') {
            //ALL
            self::$buf_debug += self::addDebug('CSS BUF FA | ALL', 'css3-alt fab', 'WRITTEN ALL reload<small>' . self::$cachepath . '/buf_fa.css</small>', self::$startmicro, 'table-info');
        } elseif ($process == '1') {
            //OWN
            self::$buf_debug += self::addDebug('CSS BUF FA | OWN', 'css3-alt fab', 'WRITTEN OWN reload<small>' . self::$cachepath . '/buf_fa.css</small>', self::$startmicro, 'table-info');
        } else {
            //PRODUCTION
            self::$buf_debug += self::addDebug('CSS BUF FA | PROD', 'css3-alt fab', 'WRITTEN NOT EXISTS <small>' . self::$cachepath . '/buf_fa.css</small>', self::$startmicro, 'table-warning');
        }
    }

    /***************************************/
    /*******    COMPILE TEMPLATE.CSS       ******/
    /***************************************/

    private static function tmpl_scss($scss, $process)
    {

        $imports_css = '';

        //VARIABLES GENERALES
        $imports_css .= '$img_path: "' . self::$img_path . '";';
        $imports_css .= '$layout_img_path: "' . self::$layout_img_path . '";';
        $imports_css .= '$layout_path: "' . self::$layoutpath . '";';
        $imports_css .= '$layout_opath: "' . self::$layout_opath . '";';

        $imports_css .= '
        $grid-breakpoints: (
            xs: ' . self::$grid_breakpoints['xs'] . ',
            sm: ' . self::$grid_breakpoints['sm'] . 'px,
            md: ' . self::$grid_breakpoints['md'] . 'px,
            lg: ' . self::$grid_breakpoints['lg'] . 'px,
            xl: ' . self::$grid_breakpoints['xl'] . 'px,
            xxl: ' . self::$grid_breakpoints['xxl'] . 'px
          );
        ';

        //OFFCANVAS
        $imports_css .= self::getOffcanvasStyles();

        $imports_css .= '@import "common.scss";';
        $imports_css .= '@import "scss/template.scss";';


        $scssElements = self::$templateparams->get('buf_layout_css_elements', array());

        $scssContent = '';


        if (!empty($scssElements)) {
            foreach ($scssElements as $element) {
                $elementPath = self::$layoutpath . '/elements/' . $element . '.scss';
                if (file_exists($elementPath)) {
                    $scssContent .= "@import '{$elementPath}'; \n";
                    $imports_css .= '@import "'.$element.'.scss";';
                    self::$buf_debug += self::addDebug('Scss Element | '.$element, 'css3-alt fab', 'LOADED ' . $element . '.scss', self::$startmicro, 'table-secondary');
                }
            }
        }

        //DEBUG
        if(self::$is_debug) file_put_contents(self::$cachepath . '/'.self::$css_sha.'.scss', $imports_css);

        $result = self::$scss->compileString($imports_css);
        $cssOut = $result->getCss();

        //CREATE CACHE CSS
        file_put_contents(self::$cachepath . '/' . self::$css_sha . '.css', $cssOut);

        if ($process == '0') {
            //ALL
            self::$buf_debug += self::addDebug('CSS tmpl | ALL', 'css3-alt fab', 'WRITTEN ALL reload<small>' . self::$cachepath . '/' . self::$css_sha . '.css</small>', self::$startmicro, 'table-info');
        } elseif ($process == '1') {
            //OWN
            self::$buf_debug += self::addDebug('CSS tmpl | OWN', 'css3-alt fab', 'WRITTEN OWN reload<small>' . self::$cachepath . '/' . self::$css_sha . '.css</small>', self::$startmicro, 'table-info');
        } else {
            //PRODUCTION
            self::$buf_debug += self::addDebug('CSS tmpl | PROD', 'css3-alt fab', 'WRITTEN NOT EXISTS <small>' . self::$cachepath . '/' . self::$css_sha . '.css</small>', self::$startmicro, 'table-warning');
        }
    }

    private static function getOffcanvasStyles()
    {

        //OFFCANVAS VARIABLES
        $imports_css = '';

        $imports_css .= '$buf_offcanvas_bg_color: ' . self::$buf_offcanvas_bg_color . ';';
        $imports_css .= '$buf_offcanvas_width: ' . self::$buf_offcanvas_width . '%;';
        $imports_css .= '$buf_offcanvas_width_desktop: ' . self::$buf_offcanvas_width_desktop . '%;';
        $imports_css .= '$buf_oc_speed:' . self::$buf_offcanvas_speed . 'ms;';

        $imports_css .= '$buf_oc_button_color: ' . self::$buf_oc_button_color . ';';
        $imports_css .= '$buf_oc_button_color_hover: ' . self::$buf_oc_button_color_hover . ';';
        $imports_css .= '$buf_oc_button_color_active: ' . self::$buf_oc_button_color_active . ';';

        //I need to define -ms to avoid empty error;
        $imports_css .= '%buf_oc_button_editor{' . self::$buf_oc_button_editor . '};';

        $buf_reverse = (self::$buf_oc_button_reverse == 'r') ? '-r' : '';
        $imports_css .= '$buf_oc_button_style:' . self::$buf_oc_button_style . $buf_reverse . ';';

        $imports_css .= '$buf_topbar_height:' . self::$buf_topbar_height . 'px;';
        $imports_css .= '$buf_topbar_color:' . self::$buf_topbar_color . ';';

        $imports_css .= '$buf_topbar_oc_height:' . self::$buf_topbar_oc_height . 'px;';
        $imports_css .= '$buf_topbar_oc_color:' . self::$buf_topbar_oc_color . ';';

        //BOOSTRAP
        $imports_css .= '
        .offcanvas, .offcanvas-lg, .offcanvas-md, .offcanvas-sm, .offcanvas-xl, .offcanvas-xxl{
            --bs-offcanvas-bg: ' . self::$buf_offcanvas_bg_color . ';
            --bs-offcanvas-width: ' .self::$buf_offcanvas_width . '%;
        }';
      
        //button
        self::$scss->addImportPath(self::$libspath . '/offcanvas/hamburgers/types');
        $imports_css .= '@import "offcanvas/hamburgers/bufburger.scss";';

        $imports_css .= '@import "offcanvas/hamburgers/types/'.self::$buf_oc_button_style . $buf_reverse.'";';

        //bar
        $imports_css .= '@import "offcanvas/offcanvas.scss";';

        return $imports_css;
    }

    /***************************************/
    /****    GET CONTAINER PARAMS       ****/
    /***************************************/

    private static function buf_get_container($buf_bs_container_fluid_max = '100%', $buf_bs_container_max = '1140', $buf_bs_container_content_max = '1140')
    {

        $findpercentage = '%';

        //CONTAINER FLUID MAX WIDTH
        $pos = strpos($buf_bs_container_fluid_max, $findpercentage);

        if ($pos === false) {
            self::$buf_bs_container_fluid_max = $buf_bs_container_fluid_max . 'px';
        } else {
            self::$buf_bs_container_fluid_max = $buf_bs_container_fluid_max;
        }

        //CONTAINER MAX WIDTH
        $pos = strpos($buf_bs_container_max, $findpercentage);

        if ($pos === false) {
            self::$buf_bs_container_max = $buf_bs_container_max . 'px';
        } else {
            self::$buf_bs_container_max = $buf_bs_container_max;
        }

        //CONTENT CONTAINER MAX WIDTH
        $pos = strpos($buf_bs_container_content_max, $findpercentage);

        if ($pos === false) {
            self::$buf_bs_container_content_max = $buf_bs_container_content_max . 'px';
        } else {
            self::$buf_bs_container_content_max = $buf_bs_container_content_max;
        }
    }

    /***************************************/
    /****     GET TEMPLATE NAME         ****/
    /***************************************/
    private static function get_template_name()
    {

        if (!file_exists(self::$cachepath)) {
            mkdir(self::$cachepath, 0775, true);
        }

        $session = Factory::getApplication()->getSession();

        //RECACHE ACTIVATED
        if (self::$recache) {
            $sha = self::$buf_layout . '_template_';
            $sha .= hash('crc32', self::$buf_layout . 'template.css') . rand();

            $session->set('buf_template_sha', $sha);

            self::$buf_debug += self::addDebug('recache | ', 'smile-o', 'RECACHE activated: <small>' . $sha . '</small>', self::$startmicro);

        } else {
            //RECACHE NOT ACTIVATED
            $sha = self::$buf_layout . '_template';
            //$session->set('buf_template_sha', $sha);
        }

        //if(self::$cssmix) $sha .= '_mix';

        return $sha;
    }

    private static function getCurrentParams($id)
    {
        //V3

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select(array($db->quoteName('template'), $db->quoteName('params')))
            ->from($db->quoteName('#__template_styles'))
            ->where($db->quoteName('id') . ' = ' . $db->quote($id));

        //$sql = "SELECT template,params FROM `#__template_styles` WHERE `id` = $id";
        $db->setQuery($query);
        //$db->query();
        $row = $db->loadObject();
        $json = $row;

        return $json;

    }



    //BS4 CUSTOM COLORS
    private static function bs4_custom($bs4_custom)
    {
        $custom = '';

        if ($bs4_custom['bs_custom_body_bg']) {
            $custom .= '$body-bg: ' . $bs4_custom['bs_custom_body_bg'] . ';';
        }

        if ($bs4_custom['bs_custom_body_color']) {
            $custom .= '$body-color: ' . $bs4_custom['bs_custom_body_color'] . ';';
        }

        $custom .= '$theme-colors: (';

        $custom .= ($bs4_custom['bs_custom_primary']) ? '"primary": ' . $bs4_custom['bs_custom_primary'] . ',' : '"primary": $primary,';
        $custom .= ($bs4_custom['bs_custom_secondary']) ? '"secondary": ' . $bs4_custom['bs_custom_secondary'] . ',' : '"secondary": $secondary,';
        $custom .= ($bs4_custom['bs_custom_success']) ? '"success": ' . $bs4_custom['bs_custom_success'] . ',' : '"success": $success,';
        $custom .= ($bs4_custom['bs_custom_info']) ? '"info": ' . $bs4_custom['bs_custom_info'] . ',' : '"info": $info,';
        $custom .= ($bs4_custom['bs_custom_warning']) ? '"warning": ' . $bs4_custom['bs_custom_warning'] . ',' : '"warning": $warning,';
        $custom .= ($bs4_custom['bs_custom_danger']) ? '"danger": ' . $bs4_custom['bs_custom_danger'] . ',' : '"danger": $danger,';
        $custom .= ($bs4_custom['bs_custom_light']) ? '"light": ' . $bs4_custom['bs_custom_light'] . ',' : '"light": $light,';
        $custom .= ($bs4_custom['bs_custom_dark']) ? '"dark": ' . $bs4_custom['bs_custom_dark'] . ',' : '"dark": $dark,';

        //$custom .= '"primary": '.$bs4_custom['bs4_custom_primary'].',';

        $custom .= ');';

        return $custom;
    }

    //BS5 CUSTOM COLORS
    private static function bs5_custom($bs5_custom)
    {
        //llamo a bs4 porque es igual
        $custom = self::bs4_custom($bs5_custom);
        return $custom;
    }

    /***************************************/
    /***************************************/
    /************ ESSENTIAL FUNCS **********/
    /***************************************/
    /***************************************/

    private static function addDebug($name = '', $icon = '', $value = '', $startmicro = 0, $tr_class = '', $service = 'bufsass.php')
    {

        if (self::$isajax) {
            return array($name => array('icon' => $icon, 'value' => $value, 'totaltime' => '', 'tr_class' => '', 'service' => ''));
        }

        $current_time = microtime(true);
        $totaltime = $current_time - $startmicro;

        $mireturn = array($name => array('icon' => $icon, 'value' => $value, 'totaltime' => $totaltime * 10000, 'tr_class' => $tr_class, 'service' => $service));
        return $mireturn;
    }

}
