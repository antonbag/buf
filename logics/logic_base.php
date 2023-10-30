<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

//4.0.1
use Joomla\CMS\Environment\Browser;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Jtotal\BUF\BufHelper;

///////////////////////
//INIT DEBUG
///////////////////////

$app = property_exists($this, 'app') ? $this->app : Factory::getApplication();
$doc = $app->getDocument();
$s = $app->getSession();
$session = $s;
$jinput = $app->input;
$lang = Factory::getLanguage();

if (is_file(JPATH_PLUGINS . '/system/jtframework/autoload.php')) {
    require_once JPATH_PLUGINS . '/system/jtframework/autoload.php';
} else {
    $app = Factory::getApplication();
    $app->enqueueMessage(Text::_('JT_FW_NOT_FOUND'), 'error');
}

include_once JPATH_SITE . '/templates/buf/src/bufhelper.php';

$startmicro = microtime(true);
$buf_debug = array();
$buf_debug += BufHelper::addDebug('START', 'flag-checkered', 'start', $startmicro, 'table-success', 'logic_base.php');

$jversion = BufHelper::getJVersion();
$tmplComponent = ($jinput->get('tmpl', '') == 'component') ? true : false;

if ($jversion == '4') {
    $wa = $this->getWebAssetManager();
    // Add Asset registry files
    $wr = $this->getWebAssetManager()->getRegistry();
}

///////////////////////
//CHECK PHP VERSION
///////////////////////
if ($jversion == '3') {
    if (defined('PHP_VERSION')) {
        $version = PHP_VERSION;
    } elseif (function_exists('phpversion')) {
        $version = phpversion();
    } else {
        ## No version info. I'll lie and hope for the best.
        $version = '5.6.0';
    }

    // An old PHP versIon is installed.
    if (!version_compare($version, '5.6.0', '>=')) {
        echo Text::_('You are using an old PHP version. Please upgrade to a newer version');
    }
}

if ($jversion == '4') {
    if (defined('PHP_VERSION')) {
        $version = PHP_VERSION;
    } elseif (function_exists('phpversion')) {
        $version = phpversion();
    } else {
        ## No version info. I'll lie and hope for the best.
        $version = '7.3.0';
    }

    // An old PHP versIon is installed.
    if (!version_compare($version, '7.3.0', '>=')) {
        echo Text::_('You are using an old PHP version. Please upgrade to a newer version');
    }
}

//2.2.0
///////////////////////
//DEVEL
///////////////////////
$devel_mode = file_exists(JPATH_SITE . '/templates/buf/composer.json');
if ($devel_mode) {
    include_once JPATH_THEMES . '/' . $this->template . '/buf_devel.php';
}

//* check JTFRAMEWORK
$check_jtfw = BufHelper::getExtensionVersion('jtframework', '');
if (!$check_jtfw || $check_jtfw == '1.0.0') {
    $app->enqueueMessage('<strong>JT Framework required.</strong>
        Please, <a href="index.php?option=com_installer&view=update" class="btn btn-default">update</a>
        or
        <a href="https://users.jtotal.org/SOFT/framework/JTframework/pkg_jtfw_current.zip" target="_blank" class="btn btn-default">Download</a> </span>', 'error');
}

//* check LIBS
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

if ($bs_version == 4 || $bs_version == 3) {
    $app->enqueueMessage('
    <strong>Wrong BS selected. (v.' . $bs_version . ') </strong>
    Please, select a correct BS version in template settings.
    ', 'error');
}

//LAYOUTS
///////////////////////
$buf_layout = $templateparams->get('buf_layout', 'default');
$buf_load_layout_js = $templateparams->get('buf_load_layout_js', 1);

$edit = ($jinput->getValue('layout') == 'edit') ? true : false;
$edit_base_input = ($jinput->getValue('edit_base') == 'true') ? true : false;

//PATHS
///////////////////////
$tpath = $this->baseurl . '/templates/' . $this->template; //
$opath = uri::base() . 'templates/' . $this->template;
$tpath_abs = JPATH_SITE . '/templates/buf';
$layoutpath = JPATH_SITE . '/templates/buf/layouts/' . $buf_layout;
$cachepath = JPATH_SITE . '/cache/buf_' . $buf_layout . '/';
$cache_opath = 'cache/buf_' . $buf_layout . '/';
$cache_tpath = $this->baseurl . '/cache/buf_' . $buf_layout . '/';
$libspath = JPATH_SITE . '/templates/buf/libs';
$jtfw_libspath = JPATH_LIBRARIES . '/jtlibs';
$libs_media_tpath = $this->baseurl . '/media/jtlibs';
$libs_media_opath = uri::base() . 'media/jtlibs';
$jconfig = Factory::getConfig();

//GET BROWSER
//JOOMLA WAY

//jimport('joomla.environment.browser');
$browser = Browser::getInstance();
$browserType = $browser->getBrowser();

//$appWeb      = new JApplicationWebClient;
/*
$appWeb      = new WebClient;
$ismobile = $appWeb->mobile;
 */

///////////////////////
//  DETEC MOBILE
///////////////////////
$ismobile = false;
$detection = $templateparams->get('buf_offcanvas_detection', 'media');


//DEVICE
$body_mobile = ($ismobile ? 'device_mobile' : 'device_not_mobile');
$body_mobile .= ' detecion_mode_' . $detection;

$buf_debug_param = $templateparams->get('buf_debug', 0);
$buf_anal_url = Uri::base() . 'templates/buf/js/analytics/buf_anal.js';

//STYLE
///////////////////////
//CONTAINER
if ($templateparams->get('buf_container', '0') == '-1') {
    $container = '';
} elseif ($templateparams->get('buf_container', '0') == '0') {
    $container = 'container';
} else {
    $container = 'container-fluid';
}

//CONTENT CONTAINER
if ($templateparams->get('buf_content_container', '0') == '-1') {
    $content_container = '';
} elseif ($templateparams->get('buf_content_container', '0') == '0') {
    $content_container = 'container';
} else {
    $content_container = 'container-fluid';
}

//OFFCANVAS
$buf_offcanvas = false;

$buf_offcanvas_max_w = $templateparams->get('buf_offcanvas_max_w', 991);

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
///////////////////////
$buf_topbar = new Registry;

$buf_topbar->loadString(json_encode($templateparams->get('buf_topbar')));

$buf_topbar_on = $buf_topbar->get('buf_topbar_on', 0);
$buf_topbar_logo_img = HTMLHelper::cleanImageURL($buf_topbar->get('buf_topbar_logo', ''));
$buf_topbar_logo_fallback = HTMLHelper::cleanImageURL($buf_topbar->get('buf_topbar_logo_fallback', ''));
$buf_topbar_logo_alt = $buf_topbar->get('buf_topbar_logo_alt', 'logo');
$buf_topbar_logo_pos = $buf_topbar->get('buf_topbar_logo_pos', "l");
$buf_topbar_height = $buf_topbar->get('buf_topbar_height', '54');
$buf_topbar_color = $buf_topbar->get('buf_topbar_color', '#fff');
$buf_topbar_module = $buf_topbar->get('buf_topbar_module', '');

$buf_topbar_classes = '';
$buf_topbar_logo = '';

if ($buf_topbar_on) {
    $buf_topbar_classes .= 'buf_topbar_on';
}

//logo show
if ($buf_topbar->get('buf_topbar_image_show', '0')) {
    $buf_topbar_logo = getTopBarImages($buf_topbar);
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
    $buf_topbar_oc_logo = getTopBarImages($buf_topbar_oc);
}

///////////////////////
//OFFCANVAS BUTTON
///////////////////////

$oc_button = new Registry;
$oc_button->loadString(json_encode($templateparams->get('buf_oc_button')));

$buf_oc_button_style = $oc_button->get('buf_oc_button_style', '3dx');
$buf_oc_button_reverse = $oc_button->get('buf_oc_button_reverse', 'l');
$buf_reverse = ($buf_oc_button_reverse == 'r') ? '-r' : '';
$buf_oc_button_vpos = $oc_button->get('buf_oc_button_vpos', 'left');
$buf_oc_button_hpos = $oc_button->get('buf_oc_button_hpos', 'top');

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
$js_params['media_w'] = $templateparams->get('buf_offcanvas_max_w', 900);
$js_params['offspeed'] = $templateparams->get('buf_offcanvas_speed', 300);
$js_params['oc_width'] = $templateparams->get('buf_offcanvas_width', 90);
$js_params['oc_width_desktop'] = $templateparams->get('buf_offcanvas_width_desktop', 90);
$js_params['oc_style'] = $templateparams->get('buf_offcanvas_style', 'buf_off_cover');
$js_params['oc_position'] = $templateparams->get('buf_offcanvas_position', 'buf_off_pos_left');

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

    //$buf_offcanvas_positions = 'menu_portada';

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

//IMAGE
$buf_bg_img = $templateparams->get('buf_bg_img', false);

/**********************/
//BOOSTRAP
/**********************/
$buf_bs_on = $templateparams->get('buf_bs_on', 4);

//BS LEFT + RIGHT CALCULATIONS
$bs_grid = new Registry;
$bs_grid->loadString(json_encode($templateparams->get('buf_bs_grid')));

$bs_left_pos = $bs_grid->get('buf_bs_left_pos', 'buf_left');

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

/**********************/
//FONTAWESOME
/**********************/
$buf_fa = $templateparams->get('buf_fa', 1);
$buf_fa_selector = $templateparams->get('buf_fa_selector', 5);
$buf_fa5_tech = $templateparams->get('buf_fa5_tech', '1');

/**********************/
//DEV
/**********************/
$runless = $templateparams->get('runless', '2');
$buf_edit_base = $templateparams->get('buf_edit_base', '0');
$css_mix = $templateparams->get('buf_scss_mix', '0');

/***************************************/
//RUN BASE SASS
/***TODO: configure bs4 files in base
/***************************************/
$base_css_exists = file_exists($cachepath . '/base.css');

if ($runless == '1' || $buf_edit_base == 1 || $base_css_exists == false || $edit_base_input) {
    include_once JPATH_THEMES . '/' . $this->template . '/logics/runsass_base.php';
}

/***************************************/
//PARAMS TO JS
/***************************************/
$params_to_js = json_encode(array('params' => $js_params));
$doc->addScriptDeclaration("var php_buf_params = '{$params_to_js}';");

$hasClass = '';

if ($this->countModules('sidebar-left', true)) {
    $hasClass .= ' has-sidebar-left';
}

if ($this->countModules('sidebar-right', true)) {
    $hasClass .= ' has-sidebar-right';
}

$bodyclass = [];
$bodyclass[] = $browserType;
$bodyclass[] = ($menu->getActive() == $menu->getDefault()) ? 'front' : 'site';
$bodyclass[] = $active->alias;
$bodyclass[] = $pageclass;
$bodyclass[] = 'alias_' . $docalias;
$bodyclass[] = $docalias;
$bodyclass[] = $body_mobile;
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
    $session = Factory::getSession();
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
/*******  UNSET J4 DEP **********/
/***************************/
//$unset_js = $templateparams->get('buf_unset', array());

/***************************/
/*******  preload  **********/
/***************************/

//BUF CSS
$preload_buf_css = $templateparams->get('buf_optimize_preload_buf', '0');
if ($css_mix) {
    $preload_buf_css = false;
}

$buf_debug += BufHelper::addDebug('PRELOAD css BUF', 'code', '<strong>Preload</strong> <small>' . var_export($preload_buf_css, true) . '</small>', $startmicro, 'table-info', 'logic_base.php');

//OWN CSS
$preload_own_css = $templateparams->get('buf_optimize_preload_own', '0');
$buf_debug += BufHelper::addDebug('PRELOAD CSS OWN', 'code', '<strong>Preload</strong> <small>' . var_export($preload_own_css, true) . '</small>', $startmicro, 'table-info', 'logic_base.php');

//JQUERY
$preload_jquery_js = $templateparams->get('buf_optimize_preload_jquery', '0');

/***************************/
/***************************/
/*******  JS jQUERY **********/
/***************************/
/***************************/
$buf_jquery = $templateparams->get('buf_jquery', 2);
$jquery_path = '';

//$buf_jquery == 1 = depcrated custom jquery
if ($buf_jquery == 2 || $edit || $buf_jquery == 1) {
    $wa = $this->getWebAssetManager();
    $wa->getAsset('script', 'jquery');

    //defer
    $defer = BufHelper::check_defer_v4($templateparams->get('buf_jquery_defer', '0'));
    foreach ($defer as $key => $v) {
        //rel="defer" or rel="async"
        $wa->getAsset('script', 'jquery')->setAttribute('rel', $v);
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
//$wa->getAsset('script', 'buflogic.js');

foreach ($defer as $key => $v) {
    //rel="defer" or rel="async"
    $wa->getAsset('script', 'buflogic.js')->setAttribute('rel', $v);
}
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

if ($jversion == '3' || $jversion == '4' || $jversion == '5') {
    if ($preload_buf_css) {
        $head_preload .= '<link rel="preload" href="' . $cache_opath . 'buf.css?' . $doc->getMediaVersion() . '" as="style">';
    }

    if ($preload_own_css) {
        $head_preload .= '<link rel="preload" href="' . $css_path . '?' . $doc->getMediaVersion() . '" as="style">';
    }
}

$buf_topbar_logo_alt = $buf_topbar->get('buf_topbar_logo_alt', 'logo');
$buf_topbar_logo_pos = $buf_topbar->get('buf_topbar_logo_pos', "l");
$buf_topbar_height = $buf_topbar->get('buf_topbar_height', '54');
$buf_topbar_color = $buf_topbar->get('buf_topbar_color', '#fff');
$buf_topbar_module = $buf_topbar->get('buf_topbar_module', '');

function getTopBarImages($buf_topbar)
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
        if (!File::exists($buf_topbar_logo_img->url)) {
            return;
        }
    }

    if ($buf_topbar->get('buf_topbar_logo_fallback', '') != '') {
        if (!File::exists($buf_topbar_logo_fallback->url)) {
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
