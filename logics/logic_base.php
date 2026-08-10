<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2005 - 2026 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

declare(strict_types=1);

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Jtotal\BUF\Site\Helper\BufCheckPhp;
use Jtotal\BUF\Site\Helper\BufHelper;

//DEBUG
$startmicro = microtime(true);
$buf_debug = [];
$buf_debug += BufHelper::addDebug('START', 'flag-checkered', 'start', $startmicro, 'table-success', 'logic_base.php');

//APP
$app = $this->app ?? Factory::getApplication();
$doc = $app->getDocument();
$session = $app->getSession();
$jinput = $app->getInput();

//WEB ASSET
$wa = $this->getWebAssetManager();
$wr = $this->getWebAssetManager()->getRegistry();
$wr->addRegistryFile(JPATH_THEMES . '/' . $this->template . '/joomla.asset.json');

//JVERSION
$jversion = BufHelper::getJVersion();
BufCheckPhp::checkPhpVersion();

//DEVEL
$devel_mode = file_exists(JPATH_SITE . '/templates/buf/composer.json');
if ($devel_mode) {
    include_once JPATH_THEMES . '/' . $this->template . '/buf_devel.php';
}

// check JTFRAMEWORK
$check_jtfw = BufHelper::getExtensionVersion('jtframework', '');
if (!$check_jtfw || $check_jtfw == '1.0.0') {
    $app->enqueueMessage('<strong>JT Framework required.</strong>
        Please, <a href="index.php?option=com_installer&view=update" class="btn btn-default">update</a>
        or
        <a href="https://users.jtotal.org/SOFT/framework/JTframework/pkg_jtfw_current.zip" target="_blank" class="btn btn-default">Download</a> </span>', 'error');
}

// check LIBS
$check_jtlibs = BufHelper::getExtensionVersion('jtlibs', '');

if (!$check_jtlibs || $check_jtlibs == '1.0.0') {
    $app->enqueueMessage('
    <strong>JT libs required.</strong>
    Please, <a href="index.php?option=com_installer&view=update" class="btn btn-default">update</a>
    or
    <a href="https://users.jtotal.org/SOFT/framework/JTlibs/jtlibs_current.zip" target="_blank" class="btn btn-default">Download</a> </span>
    ', 'error');
}

///////////////////////
//APP PARAMS
///////////////////////
$params = $app->getParams();

$tmplComponent = ($jinput->get('tmpl', '') == 'component') ? true : false;
$edit = ($jinput->getValue('layout') == 'edit') ? true : false;
$edit_base_input = ($jinput->getValue('edit_base') == 'true') ? true : false;

$templateparams = $app->getTemplate(true)->params;
$menu = $app->getMenu();

$active = (object) array('alias' => 'noactivefound');
if ($menu->getActive()) {
    $active = $menu->getActive();
}

$menutype = ($menu->getActive() != null) ? $menu->getActive()->menutype : '';
$superParentMenu = ($active->tree != null) ? $menu->getItem($active->tree[0])->alias : '';

$docalias = OutputFilter::stringUrlSafe($doc->title);
$pageclass = $params->get('pageclass_sfx', '');

$bs_version = $templateparams->get("buf_bs_on", 5);

//LAYOUTS
$buf_layout = $templateparams->get('buf_layout', 'default');
$buf_load_layout_js = $templateparams->get('buf_load_layout_js', 1);

//PATHS
$tpath = $this->baseurl . '/templates/' . $this->template; //
$opath = Uri::base() . 'templates/' . $this->template;
$tpath_abs = JPATH_SITE . '/templates/buf';
$tpath_media_abs = JPATH_SITE . '/media/templates/site/buf';
$opath_media = Uri::base() . 'media/templates/site/' . $this->template;
$layoutpath = JPATH_SITE . '/templates/buf/layouts/' . $buf_layout;
$cachepath = JPATH_SITE . '/cache/buf_' . $buf_layout . '/';
$cache_opath = 'cache/buf_' . $buf_layout . '/';
$cache_tpath = $this->baseurl . '/cache/buf_' . $buf_layout . '/';
$libspath = JPATH_SITE . '/media/templates/site/buf/libs';
$jtfw_libspath = JPATH_LIBRARIES . '/jtlibs';
$libs_media_tpath = $this->baseurl . '/media/jtlibs';
$libs_media_opath = Uri::base() . 'media/jtlibs';

// Detección de dispositivo por servidor retirada (mobiledetect eliminado, ver plan §4.7).
// $ismobile queda fijo a false: el modo 'device' del offcanvas no activa nada; usar 'media'.
// La conmutación real móvil/desktop la hace bufoc.js en el cliente vía media query.
$ismobile = false;
$detection = $templateparams->get('buf_offcanvas_detection', 'media');

$buf_debug_param = $templateparams->get('buf_debug', 0);
$buf_anal_url = Uri::base() . 'templates/buf/js/analytics/buf_anal.js';

$webapp_capable = $templateparams->get('webapp_capable', '0');

//STYLE
$containerSetting = (string) $templateparams->get('buf_container', '0');
$container = match ($containerSetting) {
    '-1' => '',
    '0' => 'container',
    default => 'container-fluid',
};
$content_container = match ((string) $templateparams->get('buf_content_container', '0')) {
    '-1' => '',
    '0' => 'container',
    default => 'container-fluid',
};


//OFFCANVAS
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

$buf_offcanvas_max_w = $grid_breakpoints[$check_breakpoint];
$buf_offcanvas = false;

//offcanvas max width match with breakpoint
$buf_offcanvas_max_w = $grid_breakpoints[$check_breakpoint];

$buf_offcanvas_style = $templateparams->get('buf_offcanvas_style', 'buf_off_move');
$buf_offcanvas_position = $templateparams->get('buf_offcanvas_position', 'buf_off_pos_left');

$buf_offcanvas_selector = $templateparams->get('buf_offcanvas_selector', 'buf_offcanvas_default');

//activated
if ($templateparams->get('buf_offcanvas', 0) == 1) {
    $buf_offcanvas = true;

    //only mobile
} elseif ($templateparams->get('buf_offcanvas', 0) == 2) {
    //device mode
    if ($detection == 'device') {
        if ($ismobile) {
            $buf_offcanvas = true;
        }
    } else {
        $buf_offcanvas = 'mobile';
    }
}

//TOPBAR
$buf_topbar = new Registry;
$buf_topbar->loadString(json_encode($templateparams->get('buf_topbar')));
$buf_topbar_height = $buf_topbar->get('buf_topbar_height', '54');

//TOPBAR JS
$buf_topbar_show_on_scroll = $buf_topbar->get('buf_show_on_scroll', '');
if ($buf_topbar_show_on_scroll) {
    // "Sólo móvil" no viaja a JS: es visibilidad por CSS (base_common.scss).
    $wa->useScript('topbar.js');
}

//TOPBAR IN OFFCANVAS
$buf_topbar_oc = new Registry;
$buf_topbar_oc->loadString(json_encode($templateparams->get('buf_topbar_oc')));
$buf_topbar_oc_height = $buf_topbar_oc->get('buf_topbar_height', '90');


///////////////////////
//OFFCANVAS BUTTON
$oc_button = new Registry;
$oc_button->loadString(json_encode($templateparams->get('buf_oc_button')));

$buf_oc_button_style = $oc_button->get('buf_oc_button_style', '3dx');
$buf_oc_button_reverse = $oc_button->get('buf_oc_button_reverse', 'l');
$buf_oc_reverse = ($buf_oc_button_reverse == 'r') ? '-r' : '';
$buf_oc_button_vpos = $oc_button->get('buf_oc_button_vpos', 'left');
$buf_oc_button_hpos = $oc_button->get('buf_oc_button_hpos', 'top');

///////////////////////
//JS VARIABLES
$js_params = array();
$js_params['buf_anal_url'] = $buf_anal_url;
$js_params['buf_path'] = $tpath;
$js_params['jtlibs_media'] = $libs_media_tpath;
$js_params['jtlibs_omedia'] = $libs_media_opath;
$js_params['debug'] = ($buf_debug_param == 1) ? true : false;
$js_params['ismobile'] = $ismobile;
$js_params['layout'] = $buf_layout;
$js_params['detection'] = $detection;
$js_params['offcanvas'] = $buf_offcanvas;
$js_params['buf_offcanvas_selector'] = $buf_offcanvas_selector;
$js_params['media_w'] = $templateparams->get('buf_offcanvas_max_w', 900);
$js_params['offspeed'] = $templateparams->get('buf_offcanvas_speed', 300);
$js_params['oc_width'] = $templateparams->get('buf_offcanvas_width', 90);
$js_params['oc_width_desktop'] = $templateparams->get('buf_offcanvas_width_desktop', 90);
$js_params['oc_style'] = $templateparams->get('buf_offcanvas_style', 'buf_off_cover');
$js_params['oc_position'] = $templateparams->get('buf_offcanvas_position', 'buf_off_pos_left');
$this->addScriptOptions('buf.config', ['params' => $js_params]);

//IMAGE
$buf_bg_img = $templateparams->get('buf_bg_img', false);

///////////////////////
//BOOSTRAP
$buf_bs_on = $templateparams->get('buf_bs_on', 4);

//BS LEFT + RIGHT CALCULATIONS
$bs_grid = new Registry;
$bs_grid->loadString(json_encode($templateparams->get('buf_bs_grid')));

$bs_left_pos = $bs_grid->get('buf_bs_left_pos', 'buf_left');
$bs_left_sm = $bs_grid->get('buf_bs_left_sm', 3);
$bs_left_md = $bs_grid->get('buf_bs_left_md', 3);
$bs_left_lg = $bs_grid->get('buf_bs_left_lg', 3);

$bs_right_pos = $bs_grid->get('buf_bs_right_pos', 'buf_right');
$bs_right_sm = $bs_grid->get('buf_bs_right_sm', 3);
$bs_right_md = $bs_grid->get('buf_bs_right_md', 3);
$bs_right_lg = $bs_grid->get('buf_bs_right_lg', 3);

$buf_right_sm = ' col-sm-' . $bs_right_sm;
$buf_right_md = ' col-md-' . $bs_right_md;
$buf_right_lg = ' col-lg-' . $bs_right_lg;

$buf_left_sm = ' col-sm-' . $bs_left_sm;
$buf_left_md = ' col-md-' . $bs_left_md;
$buf_left_lg = ' col-lg-' . $bs_left_lg;

$content_pral_bs_sm = '';
$content_pral_bs_md = '';
$content_pral_bs_lg = '';

$buf_right = false;
if ($this->countModules($bs_right_pos)) {
    $buf_right = true;
}

//both
if ($buf_right && $this->countModules($bs_left_pos)) {
    $bs_count_sm = 12 - $bs_left_sm - $bs_right_sm;
    $content_pral_bs_sm = ' col-sm-' . $bs_count_sm;

    $bs_count_md = 12 - $bs_left_md - $bs_right_md;
    $content_pral_bs_md = ' col-md-' . $bs_count_md;

    $bs_count_lg = 12 - $bs_left_lg - $bs_right_lg;
    $content_pral_bs_lg = ' col-lg-' . $bs_count_lg;

    //right
} elseif ($buf_right) {
    $bs_count_sm = 12 - $bs_right_sm;
    $content_pral_bs_sm = ' col-sm-' . $bs_count_sm;

    $bs_count_md = 12 - $bs_right_md;
    $content_pral_bs_md = ' col-md-' . $bs_count_md;

    $bs_count_lg = 12 - $bs_right_lg;
    $content_pral_bs_lg = ' col-lg-' . $bs_count_lg;

    //left
} elseif ($this->countModules($bs_left_pos)) {
    $bs_count_sm = 12 - $bs_left_sm;
    $content_pral_bs_sm = ' col-sm-' . $bs_count_sm;

    $bs_count_md = 12 - $bs_left_md;
    $content_pral_bs_md = ' col-md-' . $bs_count_md;

    $bs_count_lg = 12 - $bs_left_lg;
    $content_pral_bs_lg = ' col-lg-' . $bs_count_lg;
}

///////////////////////
//FONTAWESOME
$buf_fa = $templateparams->get('buf_fa', 1);
$buf_fa_selector = $templateparams->get('buf_fa_selector', 5);
$buf_fa5_tech = $templateparams->get('buf_fa5_tech', '1');

///////////////////////
//DEV
$runless = $templateparams->get('runless', '2');
$buf_edit_base = $templateparams->get('buf_edit_base', '0');
$css_mix = $templateparams->get('buf_scss_mix', '0');

$buf_load_css_async = $templateparams->get('buf_load_css_async', '0');

///////////////////////
//COUNTER FOR RUNLESS MODE

if ($runless == '1') {
    $session_key = 'buf_runless_timestamp';
    $runless_timestamp = $session->get($session_key);
    $current_time = time();

    $remaining_time = 1800 - ($current_time - $runless_timestamp);
    $remaining_minutes = ceil($remaining_time / 60);
    $runless = 0;
    
    if (!$runless_timestamp) {
        $session->set($session_key, $current_time);

    } elseif (($current_time - $runless_timestamp) >= 1800) {
        $runless = 2;
    }
       
}




///////////////////////
//RUN BASE SASS
$base_css_exists = file_exists($cachepath . '/base.css');

if ($runless == '1' || $buf_edit_base == 1 || $base_css_exists == false || $edit_base_input) {
    include_once JPATH_THEMES . '/' . $this->template . '/logics/runsass_base.php';
}

$hasClass = '';

if ($this->countModules('sidebar-left', true)) {
    $hasClass .= ' has-sidebar-left';
}

if ($this->countModules('sidebar-right', true)) {
    $hasClass .= ' has-sidebar-right';
}

$bodyclass = [];
$bodyclass[] = ($menu->getActive() == $menu->getDefault()) ? 'front' : 'site';
$bodyclass[] = $active->alias;
$bodyclass[] = $pageclass;
$bodyclass[] = 'alias_' . $docalias;
$bodyclass[] = $docalias;
$bodyclass[] = 'menutype_' . $menutype;
if ($superParentMenu != '') {
    $bodyclass[] = 'superParentMenu_' . $superParentMenu;
}

$bodyclass[] = 'bs_version_' . $bs_version;
$bodyclass[] = $hasClass;

/***************************/
/**********************/
//FONTS
/**********************/
/***************************/
if ($templateparams->get('buf_googlefonts', 1) && $templateparams->get('buf_googlefonts_name', '') != '') {
    include_once 'googleFonts.php';
}

/***************************/
/***************************/
/*******  CSS  RECACHE  **********/
/***************************/
/***************************/
if ($templateparams->get('force_recache', 0)) {
    //true
    $current_css = $session->get('buf_template_sha');
    if ($css_mix) {
        $css_path = $cache_opath . $current_css . '_mix.css';
    } else {
        $css_path = $cache_opath . $current_css . '.css';
    }
} else {
    if ($css_mix) {
        $css_path = $cache_opath . $buf_layout . '_template_mix.css';
    } else {
        $css_path = $cache_opath . $buf_layout . '_template.css';
    }
}


/***************************/
/*******  preload  **********/
/***************************/

//BUF CSS
$preload_buf_css = $templateparams->get('buf_optimize_preload_buf', '0');
if ($css_mix) {
    $preload_buf_css = false;
}

$buf_debug += BufHelper::addDebug(
    'PRELOAD css BUF',
    'code',
    sprintf('<strong>Preload:</strong> %s', $preload_buf_css ? 'enabled' : 'disabled'),
    $startmicro,
    'table-info',
    'logic_base.php'
);

//OWN CSS
$preload_own_css = $templateparams->get('buf_optimize_preload_own', '0');
$buf_debug += BufHelper::addDebug(
    'PRELOAD css OWN',
    'code',
    sprintf('<strong>Preload:</strong> %s', $preload_own_css ? 'enabled' : 'disabled'),
    $startmicro,
    'table-info',
    'logic_base.php'
);




//JQUERY
$preload_jquery_js = $templateparams->get('buf_optimize_preload_jquery', '0');





/***************************/
/***************************/
/*******  JS jQUERY **********/
/***************************/
/***************************/
// jQuery es opt-in (Joomla 6 lo minimiza); si no está seteado, no se carga.
$buf_jquery = $templateparams->get('buf_jquery', 0);
$jquery_path = '';

//$buf_jquery == 1 = depcrated custom jquery
if ($buf_jquery == 2 || $edit || $buf_jquery == 1) {


    $wa->getAsset('script', 'jquery');

    //defer
    $defer = BufHelper::check_defer_v4($templateparams->get('buf_jquery_defer', '0'));

    foreach ($defer as $attr => $value) {
        $attribute = is_int($attr) ? $value : $attr;
        $enabled = is_int($attr) ? true : (bool) $value;

        if (in_array($attribute, ['defer', 'async'], true) && $enabled) {
            $wa->getAsset('script', 'jquery')->setAttribute($attribute, true);
        }
    }

    $jquery_path = $wa->getAsset('script', 'jquery')->getUri() . '?' . $wa->getAsset('script', 'jquery')->getVersion();

    // PRELOAD JQUERY JS
    if ((bool) $jquery_path && (bool) $preload_jquery_js) {
        $this->getPreloadManager()->preload($jquery_path, ['as' => 'script']);
        $buf_debug += BufHelper::addDebug('PRELOAD JQuery js', 'code', '<strong>Preload</strong> <small>' . $preload_jquery_js . ' | ' . $jquery_path . '</small>', $startmicro, 'table-info', 'logic_base.php');
    }

    $wa->useScript('jquery');

    $buf_debug += BufHelper::addDebug('JQUERY joomla', 'code', '<strong>jquery.min.js</strong> <small>' . var_export($defer, true) . '</small>', $startmicro, 'table-info', 'logic_base.php');
}

/***************************/
/***************************/
/*******  JS LOGIC **********/
/***************************/
/***************************/
$defer = BufHelper::check_defer_v4($templateparams->get('buf_js_defer', 1));
$preload_logic_and_bufoc_js = $templateparams->get('buf_optimize_preload_logic_js', '0');



if ((bool) $preload_logic_and_bufoc_js) {
    $this->getPreloadManager()->preload($wa->getAsset('script', 'buflogic.js')->getUri() . '?' . $this->getMediaVersion(), ['as' => 'script']);
}
$wa->useScript('buflogic.js');

$logic_path = $tpath . '/js/logic.min.js';
$buf_debug += BufHelper::addDebug('logic.js', 'code', '<strong>' . $logic_path . '.js</strong> <small>' . var_export($defer, true) . '</small>', $startmicro, 'table-info', 'logic.php');
// PRELOAD LOGIC JS
if ((bool) $preload_logic_and_bufoc_js) {
    $buf_debug += BufHelper::addDebug('PRELOAD logic js', 'code', '<strong>Preload</strong> <small>' . $preload_logic_and_bufoc_js . '</small>', $startmicro, 'table-info', 'logic_base.php');
}

/***************************/
/*******  preload / 2 **********/
/***************************/
$head_preload = '';

if ($preload_buf_css) {
    $head_preload .= '<link rel="preload" href="' . $cache_opath . 'buf.css?' . $doc->getMediaVersion() . '" as="style">';
}

if ($preload_own_css) {
    $head_preload .= '<link rel="preload" href="' . $css_path . '?' . $doc->getMediaVersion() . '" as="style">';
}



//CUSTOM CSS FILES
$buf_extra_custom_css = $templateparams->get('buf_extra_custom_css', array());

foreach ($buf_extra_custom_css as $key => $value) {
    $wa->registerAndUseStyle(
        'buf.custom.' . $key,
        $value->buf_load_custom_css,
        ['version' => 'auto'],
        ['media' => 'print', 'onload' => "this.media='all'"]
    );
}

//* CUSTOM JS
$buf_extra_custom_js = new Registry($templateparams->get('buf_extra_custom_js'));
$defer_custom_js = array();
foreach ($buf_extra_custom_js as $key => $cus_js) {

    $cus_js = new Registry($cus_js);

    if ($cus_js->get('buf_load_custom_js_script') == '') {
        continue;
    }

    $wa->registerScript('buf_extra_custom' . $key, $cus_js->get('buf_load_custom_js_script', ''), [], []);
    //DEFER ASYNC
    if ($cus_js->get('buf_js_defer') == 1) {
        $wa->getAsset('script', 'buf_extra_custom' . $key)->setAttribute('defer', true);
    } elseif ($cus_js->get('buf_js_defer') == 2) {
        $wa->getAsset('script', 'buf_extra_custom' . $key)->setAttribute('async', true);
    }

    //PRECONNECT (only for external domains)
    if ($cus_js->get('buf_js_preconnect')) {
        $parsed_url = parse_url($cus_js->get('buf_load_custom_js_script'));
        if (isset($parsed_url['host']) && $parsed_url['host'] !== Uri::getInstance()->getHost()) {
            $origin = ($parsed_url['scheme'] ?? 'https') . '://' . $parsed_url['host'];
            //$this->getPreloadManager()->preconnect($origin, ['crossorigin' => 'anonymous']);
            $preloadManager = $this->getPreloadManager();
            if ($preloadManager && method_exists($preloadManager, 'preconnect')) {
                $preloadManager->preconnect($origin, ['crossorigin' => 'anonymous']);
            }
        }
    }

    //CUSTOM ATTRIBS
    if ($cus_js->get('buf_js_attribs') != '') {
        foreach ($cus_js->get('buf_js_attribs') as $akey => $att) {
            $wa->getAsset('script', 'buf_extra_custom' . $key)->setAttribute($att->buf_js_attrib_label, $att->buf_js_attrib_value);
            //to show in debug
            $defer_custom_js[$att->buf_js_attrib_label] = $att->buf_js_attrib_value;
        }
    }

    $wa->useScript('buf_extra_custom' . $key);

    $buf_debug += BufHelper::addDebug($key, 'code', '<strong>' . $cus_js->get('buf_load_custom_js_script') . '</strong> <small>' . var_export($defer_custom_js, true) . '</small>', $startmicro, 'table-info', 'logic.php');
}


$buf_load_resources = $templateparams->get('buf_load_resources', '');


//CACHE CONTROL
$buf_cache_control_enable = $templateparams->get('buf_cache_control_enable', 1);
$buf_cache_control = '';

if ($buf_cache_control_enable) {
    $cacheValue = $templateparams->get('buf_cache_control', 'public, max-age=31536000');

    // No cachear públicamente respuestas privadas: usuarios autenticados o envíos POST.
    // Evita que un proxy/CDN sirva la página de un usuario a otro distinto.
    $current_user = $app->getIdentity();
    $is_private   = ($current_user && !$current_user->guest)
        || strtoupper((string) $jinput->getMethod()) === 'POST';

    if ($is_private) {
        $cacheValue = 'private, no-cache, no-store, must-revalidate';
    }

    // Solo enviar header si no se han enviado ya
    if (!headers_sent()) {
        header('Cache-Control: ' . $cacheValue);
    }

    // Meta tag como fallback, sólo para el valor público cacheable
    if (!$is_private) {
        $buf_cache_control = '<meta http-equiv="Cache-Control" content="' . htmlspecialchars($cacheValue, ENT_QUOTES, 'UTF-8') . '">';
    }
}
