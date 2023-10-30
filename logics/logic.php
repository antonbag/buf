<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Jtotal\BUF\BufHelper;
use Jtotal\BUF\BUFsass;

$buf_debug += BufHelper::addDebug('COMPONENTS | loaded', 'joomla fab', 'INIT logic stuff', $startmicro, 'table-success', 'logic.php');

// variables (defined in login_base)
//$app = JFactory::getApplication();
//$doc = JFactory::getDocument();
//$menu = $app->getMenu();
//$active = $app->getMenu()->getActive();
//$params = $app->getParams();
//$pageclass = $params->get('pageclass_sfx');
//$tpath = $this->baseurl.'/templates/'.$this->template;
//$templateparams    = $app->getTemplate(true)->params;

if ($buf_bs_on) {

    /***************************/
    //BS5
    if ($buf_bs_on == "5") {
        $doc->addScriptDeclaration("var bs_version = 5;");

        $bs_5 = new Registry;
        $bs_5->loadString(json_encode($templateparams->get('buf_bs_v5')));

        $bs5_js = $bs_5->get("buf_bootstrap_js", 'custom');
        $bs5_js_bundle = $bs_5->get("buf_bs_bundle", '');

        //Joomla bootstrap
        if ($bs5_js == 'joomla' || $edit) {
            HTMLHelper::_('bootstrap.framework');
            $buf_debug += BufHelper::addDebug('BOOSTRAP 5', 'code', '<small>load joomla bootstrap</small>', $startmicro, 'table-info', 'logic.php');
        } elseif ($bs5_js == 'cdn') {
            $bsCdnUrl = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.min.js';
            $intgrity = 'sha512-WW8/jxkELe2CAiE4LvQfwm1rajOS8PHasCCx+knHG0gBHt8EXxS6T6tJRTGuDQVnluuAvMxWF4j8SNFDKceLFg==';

            $this->getPreloadManager()->preconnect('https://cdnjs.cloudflare.com/', []);
            $this->getPreloadManager()->preload($bsCdnUrl, ['as' => 'script']);
            $wa->registerScript('bootstrap.js', $bsCdnUrl, [], [
                'crossorigin' => 'anonymous',
                'integrity' => $intgrity,
                'referrerpolicy' => 'no-referrer']);

            $wa->getAsset('script', 'bootstrap.js')->setAttribute('defer', true);

            $wa->useScript('bootstrap.js');

            $buf_debug += BufHelper::addDebug('BOOSTRAP 5 custom', 'code', '<strong>cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.min.js</strong> <small>' . var_export($defer, true) . '</small>', $startmicro, 'table-info', 'logic.php');
        } elseif ($bs5_js == 'custom') {
            //modern
            //JTfunc::getPromiseScript([$libs_media_opath.'/bootstrap/js/bootstrap.bundle.min.js']);

            $defer = BufHelper::check_defer_v4($bs_5->get('buf_bs_defer', 0));

            //Default all Joomla Boostrap files
            if ($bs5_js_bundle == '') {
                HTMLHelper::_('bootstrap.framework', true);
            }

            //bundle all Joomla Boostrap files (external, modern)
            if ($bs5_js_bundle == 'bundle') {
                $wa->registerScript('bootstrap.js', $libs_media_opath . '/bootstrap/js/bootstrap' . $bs5_js_bundle . '.min.js', [], []);

                if ($defer) {
                    if (!empty($defer['async'])) {
                        $wa->getAsset('script', 'bootstrap.js')->setAttribute('async', true);
                    } else {
                        $wa->getAsset('script', 'bootstrap.js')->setAttribute('defer', true);
                    }
                }
                $wa->useScript('bootstrap.js');
            }

            //custom joomla files
            if ($bs5_js_bundle == 'custom') {
                foreach ($bs_5->get('buf_bs_js_files', array()) as $js_field) {
                    HTMLHelper::_('bootstrap.' . $js_field);
                }
            }

            //HTMLHelper::_('bootstrap.framework', false);
            $buf_debug += BufHelper::addDebug('BOOSTRAP 5 custom', 'code', '<strong>/libs/bootstrap/dist/js/bootstrap' . $bs5_js_bundle . '.min.js</strong> <small>' . var_export($defer, true) . '</small>', $startmicro, 'table-info', 'logic.php');
        }

        /***************************/
        /*******CSS  BS5 CDN  LOADING  **********/
        /***************************/

        //JOOMLA
        if ($bs_5->get("buf_bootstrap_css", 'joomla') == 'joomla') {
            //HTMLHelper::_('bootstrap.loadcss',true,'ltr',['rel' => 'lazy-stylesheet', 'media' => 'print', 'onload' => 'this.media=\'all\'']);

            $wa->getAsset('style', 'bootstrap.css');
            $wa->useStyle('bootstrap.css');
        }

        //CDN
        if ($bs_5->get("buf_bootstrap_css", 'joomla') == 'cdn') {
            $bsCdnUrl = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css';
            $intgrity = 'sha512-b2QcS5SsA8tZodcDtGRELiGv5SaKSk1vDHDaQRda0htPYWZ6046lr3kJ5bAAQdpV2mmA/4v0wQF9MyU6/pDIAg==';

            $this->getPreloadManager()->preconnect('https://cdnjs.cloudflare.com/', []);
            $this->getPreloadManager()->preload($bsCdnUrl, ['as' => 'style']);
            $wa->registerStyle(
                'bootstrap.css',
                $bsCdnUrl,
                ['integrity' => $intgrity],
                ['crossorigin' => 'anonymous', 'referrerpolicy' => 'no-referrer']
            );

            $wa->getAsset('style', 'bootstrap.css')->setAttribute('rel', 'lazy-stylesheet');

            $wa->useStyle('bootstrap.css');
        }
    }
}

/***************************/
/*******  FA OPTIONS **********/
/***************************/
$buf_fa_selector = $templateparams->get('buf_fa_selector', 'jdefault');
$buf_fa_pro = $templateparams->get('buf_fa_pro', 0);
$buf_fa5_files = $templateparams->get('buf_fa5_files', array('solid'));
$buf_fa6_files = $templateparams->get('buf_fa6_files', array('solid'));
$buf_fa5_fa4fallback = $templateparams->get('buf_fa4fallback', 0);
$buf_fa_defer = $templateparams->get('buf_fa_defer', 0);

/***************************/
/*******  JS LAYOUT ********/

if ($buf_load_layout_js) {
    $force_recache_string = 'auto';

    if ($templateparams->get('force_recache_js', 0)) {
        $force_recache_string = md5(rand());
    }
    $assetOptions = ['version' => $force_recache_string];
    $wa->registerAndUseScript('buf_layout.js', $opath . '/layouts/' . $buf_layout . '/js/buf_layout.js', $assetOptions, ['defer' => true]);

    //$doc->addScript($tpath . '/layouts/' . $buf_layout . '/js/buf_layout.js' . $force_recache_string, array(), $defer);
    $buf_debug += BufHelper::addDebug('LOAD layout js', 'code', '<strong>buf_layout.js RECACHED: ' . $force_recache_string . '</strong> <small>' . var_export($defer, true) . '</small>', $startmicro, 'table-info', 'logic.php');
}

/***************************/
/*******  JS CUSTOM ********/

$buf_load_custom_js = $templateparams->get('buf_load_custom_js', array());

foreach ($buf_load_custom_js as $key => $cus_js) {
    if ($cus_js->buf_load_custom_js_script == '') {
        continue;
    }

    $wa->registerScript('buf_load_custom_js' . $key, $opath . '/layouts/' . $buf_layout . '/js/' . $cus_js->buf_load_custom_js_script, [], []);

    //DEFER ASYNC
    if ($cus_js->buf_js_defer == 1) {
        $wa->getAsset('script', 'buf_extra_custom' . $key)->setAttribute('defer', true);
    } elseif ($cus_js->buf_js_defer == 2) {
        $wa->getAsset('script', 'buf_extra_custom' . $key)->setAttribute('async', true);
    }

    //CUSTOM ATTRIBS
    if ($cus_js->buf_js_attribs != '') {
        foreach ($cus_js->buf_js_attribs as $akey => $att) {
            $wa->getAsset('script', 'buf_load_custom_js' . $key)->setAttribute($att->buf_js_attrib_label, $att->buf_js_attrib_value);
            //to show in debug
            $defer_custom_js[$att->buf_js_attrib_label] = $att->buf_js_attrib_value;
        }
    }

    //$wa->getAsset('script','buf_load_custom_js');
    $wa->useScript('buf_load_custom_js' . $key);

    //$doc->addScript($tpath.'/layouts/'.$buf_layout.'/js/'.$cus_js->buf_load_custom_js_script,array($cus_js->buf_js_attribs), $defer_custom_js);
    $buf_debug += BufHelper::addDebug('LOAD custom script |' . $key, 'code', $buf_layout . '/js/' . '<strong>' . $cus_js->buf_load_custom_js_script . '</strong> <small>' . var_export($defer_custom_js, true) . '</small>', $startmicro, 'table-info', 'logic.php');
}

/********************************************************************************************************/
/*****  UNSET SCRIPTS ********/
/********************************************************************************************************/
if ((bool) $templateparams->get('buf_custom_unset', '')) {
    $buf_custom_unset = $templateparams->get('buf_custom_unset', '');

    if ($buf_custom_unset != '') {
        foreach ($buf_custom_unset as $key => $defer_cus_js) {
            if ($defer_cus_js->buf_custom_unset_script == '') {
                continue;
            }

            $defer_cus_js = trim($defer_cus_js->buf_custom_unset_script);
            unset($doc->_scripts[$this->baseurl . '/' . $defer_cus_js]);

            //$doc->addScript($defer_cus_js,array(), $defer);
            $buf_debug += BufHelper::addDebug('CUSTOM UNSET| ' . $key, 'trash', '<strong>' . $defer_cus_js . '</strong>', $startmicro, 'table-danger', 'logic.php');
        }
    }
}

/***************************/
/***************************/
/*****  PHP COMPILE ********/
/***************************/
/***************************/

//CHECK FA FONTs IN CACHE
/*css+fonts*/

$buf_fa_run_webfont = false;

if ($buf_fa_selector && $buf_fa5_tech == 2 && $buf_fa_selector != 'jdefault') {
    $fa5pro_exists = file_exists($libspath . '/font-awesome/fontawesome5pro/webfonts/fa-brands-400.ttf') ? true : false;

    if ($buf_fa_selector == 5) {
        if ($buf_fa_pro && $fa5pro_exists) {
            $fa_path = 'fontawesome5pro';
            $dir = $cachepath . '/fontawesome5pro/webfonts';
            $buf_fa_webfont_exists = (count(glob("$dir/*")) === 0) ? false : true;
        } else {
            $fa_path = 'fontawesome5';
            $dir = $cachepath . '/fontawesome5/webfonts';
            $buf_fa_webfont_exists = (count(glob("$dir/*")) === 0) ? false : true;
        }

        if (!$buf_fa_webfont_exists) {
            $buf_fa_run_webfont = true;
        }
    }
}

//TRUE or FALSE
$buf_bs_css_exist = file_exists($cachepath . '/buf_bs.css');
$buf_css_exist = file_exists($cachepath . '/buf.css');
$buf_fa_css = file_exists($cachepath . '/buf_fa.css');

$buf_check_files = true;
if (!$buf_bs_css_exist ||
    !$buf_fa_css ||
    !$buf_css_exist ||
    $buf_fa_run_webfont
) {
    $buf_check_files = false;

    $buf_debug += BufHelper::addDebug(
        'CHECK CACHE FILES',
        'cog',
        'bs:' . var_export($buf_bs_css_exist, true) .
        ' fa:' . var_export($buf_fa_css, true) .
        ' buf:' . var_export($buf_fa_css, true) .
        ' fa_wf:' . var_export($buf_fa_run_webfont, true),
        $startmicro,
        'table-primary',
        'logic.php'
    );

    $buf_debug += BufHelper::addDebug('COMPILER', 'cog', 'ALL', $startmicro, 'table-primary', 'logic.php');
}

if ($templateparams->get('runless', 0) != 2 || !$buf_check_files) {
    //include_once JPATH_THEMES.'/'.$this->template.'/logics/runsass.php';
    include_once JPATH_SITE . '/templates/buf/src/bufsass.php';

    //CLASS BUFSASS
    $buffles = new BUFsass();

    $runless = $buffles::runsass('', $templateparams, 'buf', $startmicro);

    $buf_debug += $runless;
}

/***************************/
/***************************/
/*******  CSS  TEMPLATE  j4**********/
/***************************/
/***************************/

//*******  BUF.css **********/
//$this->getPreloadManager()->preconnect($cache_opath, []);
//$this->getPreloadManager()->preload($cache_opath.'/buf.css', ['as' => 'style']);
$wa->registerStyle(
    'buf.css',
    $cache_opath . 'buf.css',
    [],
    ['rel' => 'lazy-stylesheet', 'media' => 'print', 'onload' => 'this.media=\'all\'']
);

//use buf if not mix
if (!$css_mix) {
    $wa->useStyle('buf.css');
    $buf_debug += BufHelper::addDebug('BUF css', 'thumbs-up', 'LOADED', $startmicro, 'table-success');
}

//*******  template.css **********/
$wa->registerStyle(
    'ownBuf.css',
    $css_path,
    [],
    ['rel' => 'lazy-stylesheet', 'media' => 'print', 'onload' => 'this.media=\'all\'']
);

$wa->useStyle('ownBuf.css');

//JOOMLA DEFAULT FA
if ($buf_fa_selector == 'jdefault') {
    $wa->getAsset('style', 'fontawesome')->setAttribute('rel', 'lazy-stylesheet');
    $wa->useStyle('fontawesome');
    $buf_debug += BufHelper::addDebug('FA css JDEFAULT', 'thumbs-up', 'LOADED', $startmicro, 'table-success');
}

/***************************/
/***************************/
/*******  FA6 JS  **********/
/***************************/
/***************************/

//FA6
if ((int) $buf_fa_selector == 6) {
    $fa6pro_exists = file_exists(JPATH_THEMES . '/' . $this->template . '/libs/font-awesome/fontawesome6pro/js/fontawesome.min.js');

    $deferfa = false;
    if ($templateparams->get('buf_fa_defer', 0) >= 1) {
        $deferfa = ($templateparams->get('buf_fa_defer', 0) == 1) ? 'defer' : 'async';
    }

    $buf_debug += BufHelper::addDebug(' FA6JS', 'font-awesome fab', '	--------- FONTAWESOME 6 JS ---------', $startmicro, 'table-info', 'logic.php');

    if ($fa6pro_exists && $buf_fa_pro) {
        //fa6PRO
        foreach ($buf_fa6_files as $key => $value) {
            $wa->registerScript($value . '.min.js', $opath . '/libs/font-awesome/fontawesome6pro/js/' . $value . '.min.js', [], []);
            if ($deferfa) {
                $wa->getAsset('script', $value . '.min.js')->setAttribute($deferfa, true);
            }

            $wa->useScript($value . '.min.js');

            $buf_debug += BufHelper::addDebug($key . ' FA6pro', 'font-awesome fab', '/fontawesome6pro/js/' . $value . '.min.js, ' . var_export($deferfa, true), $startmicro, 'table-info', 'logic.php');
        }

        $wa->registerScript('fontawesome.min.js', $opath . '/libs/font-awesome/fontawesome6pro/js/fontawesome.min.js', [], []);
        if ($deferfa) {
            $wa->getAsset('script', 'fontawesome.min.js')->setAttribute($deferfa, true);
        }

        $wa->useScript('fontawesome.min.js');

        $buf_debug += BufHelper::addDebug(' FA6pro gen', 'font-awesome fab', '/fontawesome6pro/js/fontawesome.min.js, ' . var_export($deferfa, true), $startmicro, 'table-info', 'logic.php');
    } else {
        //fa6 FREE
        //remove PRO files
        $buf_fa6_files = \array_diff($buf_fa6_files, ["duotone", "light", "thin"]);

        //fa6FREE
        foreach ($buf_fa6_files as $key => $value) {
            $wa->registerScript($value . '.min.js', $opath . '/libs/font-awesome/fontawesome6/js/' . $value . '.min.js', [], []);
            if ($deferfa) {
                $wa->getAsset('script', $value . '.min.js')->setAttribute($deferfa, true);
            }

            $wa->useScript($value . '.min.js');
            $buf_debug += BufHelper::addDebug('FA6 | ' . $value, 'font-awesome fab', $value, $startmicro, 'table-info', 'logic.php');
        }

        //$doc->addScript($tpath.'/libs/font-awesome/fontawesome6/js/fontawesome.min.js', array(), $deferfa);

        $wa->registerScript('fontawesome.min.js', $opath . '/libs/font-awesome/fontawesome6/js/fontawesome.min.js', [], []);
        if ($deferfa) {
            $wa->getAsset('script', 'fontawesome.min.js')->setAttribute($deferfa, true);
        }

        $wa->useScript('fontawesome.min.js');

        $buf_debug += BufHelper::addDebug(' FA6 gen', 'font-awesome fab', '/fontawesome6/js/fontawesome.min.js, ' . var_export($deferfa, true), $startmicro, 'table-info', 'logic.php');
    }
}

/***************************/
/***************************/
/*******  EXTRAS  **********/
/***************************/
/***************************/

//* ANIMATE
$buf_extras_animate = new Registry;
$buf_extras_animate->loadString(json_encode($templateparams->get('buf_extra_animatecss')));

if ($buf_extras_animate->get('animate_on', '0') == '1') {
    $wa->registerStyle(
        'animatecss',
        'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
        [],
        ['rel' => 'lazy-stylesheet', 'media' => 'print', 'onload' => 'this.media=\'all\'']
    );
    $wa->useStyle('animatecss');

    $buf_debug += BufHelper::addDebug('ANIMATE', 'helicopter', 'LOADED', $startmicro, 'table-info', 'logic.php');
}

//* CUSTOM JS
$buf_extra_custom_js = new Registry;
$buf_extra_custom_js->loadString(json_encode($templateparams->get('buf_extra_custom_js')));

foreach ($buf_extra_custom_js as $key => $cus_js) {
    if ($cus_js->buf_load_custom_js_script == '') {
        continue;
    }

    $wa->registerScript('buf_extra_custom' . $key, $cus_js->buf_load_custom_js_script, [], []);

    //DEFER ASYNC
    if ($cus_js->buf_js_defer == 1) {
        $wa->getAsset('script', 'buf_extra_custom' . $key)->setAttribute('defer', true);
    } elseif ($cus_js->buf_js_defer == 2) {
        $wa->getAsset('script', 'buf_extra_custom' . $key)->setAttribute('async', true);
    }

    //CUSTOM ATTRIBS
    if ($cus_js->buf_js_attribs != '') {
        foreach ($cus_js->buf_js_attribs as $akey => $att) {
            $wa->getAsset('script', 'buf_extra_custom' . $key)->setAttribute($att->buf_js_attrib_label, $att->buf_js_attrib_value);
            //to show in debug
            $defer_custom_js[$att->buf_js_attrib_label] = $att->buf_js_attrib_value;
        }
    }

    $wa->useScript('buf_extra_custom' . $key);

    $buf_debug += BufHelper::addDebug($key, 'code', '<strong>' . $cus_js->buf_load_custom_js_script . '</strong> <small>' . var_export($defer_custom_js, true) . '</small>', $startmicro, 'table-info', 'logic.php');
}

/***************************************/
/*****************DEBUG*****************/
/***************************************/
if ($buf_debug_param) {
    //LOADED
    $buf_debug += BufHelper::addDebug('JOOMLA!', 'joomla far fab', '--------- LOADED ---------', $startmicro, 'table-success', 'index.php'); //

    //COUNT SCRIPTS
    $conta_script = 0;
    foreach ($doc->_scripts as $loadedjs => $jskey) {
        $buf_debug += BufHelper::addDebug('JS | ' . $conta_script, 'joomla far fab', '<strong>loaded: </strong><small>' . $loadedjs . '</small>', $startmicro, 'table-default', 'logic.php');

        $conta_script += 1;
    }

    include JPATH_THEMES . '/' . $this->template . '/logics/debug.php';
}

/***************************************/
/********   remember development  *********/
/***************************************/

if ($templateparams->get('runless', 1) != 2) {
    $uri = Uri::getInstance();
    $uri_base = $uri->toString() . '&edit_base=true';

    echo '<div class="buf_dev_mode">
	  		<div class="buf_dev_msg">
	  			<i class="fas fa-cogs"></i>
	  			<span>BUF template in development mode. Please use Production for better load times.</span>
	  		</div>
	  		<a class="buf_dev_mode_edit_base" href="' . $uri_base . '"><i class="fas fa-box-open"></i> Base </a>
  			<a class="buf_dev_mode_close" href="#"><i class="fas fa-times-circle"></i> Close</a>
  	</div>';
}

/***************************/
/***************************/
/*******  BUF OffCanvas **********/
/***************************/
/***************************/

if (($templateparams->get('buf_offcanvas', 1) != 0) && !$tmplComponent) {
    $wa->useScript('bufoc.js');

    $buf_debug += BufHelper::addDebug('bufoc.js', 'code', '<strong>OFFCANVAS bufoc.js</strong> <small>' . var_export($defer, true) . '</small>', $startmicro, 'table-info', 'logic.php');
}
