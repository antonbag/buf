<?php defined( '_JEXEC' ) or die;

use BUF\BufHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

$buf_debug += BufHelper::addDebug('COMPONENTS | loaded', 'joomla fab', 'INIT logic stuff', $startmicro, 'table-success', 'logic.php');

// variables (defined in login_base)
//$app = JFactory::getApplication();
//$doc = JFactory::getDocument();
//$menu = $app->getMenu();
//$active = $app->getMenu()->getActive();
//$params = $app->getParams();
//$pageclass = $params->get('pageclass_sfx');
//$tpath = $this->baseurl.'/templates/'.$this->template;
//$templateparams	= $app->getTemplate(true)->params;

// generator tag
$this->setGenerator(null);

// force latest IE & chrome frame
$doc->setMetadata('x-ua-compatible','IE=edge,chrome=1');



/***************************/
/***************************/
/*****  JS BOOTSTRAP *******/
/***************************/
/***************************/
//** DEPRECATED
//$doc->addScriptDeclaration("var bs_load = 'false';");


if($buf_bs_on){

	/***************************/
	//BS4
	if($buf_bs_on == "4"){

		$doc->addScriptDeclaration("var bs_version = 4;");

		$bs_4 = new Registry; 
		$bs_4->loadString(json_encode($templateparams->get('buf_bs_v4'))); 

		$bs4_js = $bs_4->get("buf_bootstrap_js",'custom');

		if($bs4_js=='joomla' || $edit){
			HTMLHelper::_('bootstrap.framework');
			$buf_debug += BufHelper::addDebug('BOOSTRAP 4', 'code', '<small>JHtml::_(\'bootstrap.framework\')</small>', $startmicro, 'table-info', 'logic.php');

		}elseif($bs4_js=='custom'){

			HTMLHelper::_('bootstrap.framework', false);

			if($templateparams->get('buf_unset','') != ''){
				if (in_array('media/jui/js/bootstrap.min.js', $templateparams->get('buf_unset',''))){
					unset($doc->_scripts[$this->baseurl .'/media/jui/js/bootstrap.min.js']);
					$buf_debug += BufHelper::addDebug('BS joomla JS', 'code', '<strong>UNSET:</strong> <small>media/jui/js/bootstrap.min.js</small>', $startmicro, 'table-info', 'logic.php');
				}
			}
	
			$defer = BufHelper::check_defer_v4($bs_4->get('buf_bs_defer',0));

			//DEPRECATED
			/* 			
			if($defer){
				$doc->addScriptDeclaration("var bs_load = 'true';");

				if(!empty($defer['async'])){
					$doc->addScriptDeclaration("var bs_load_async = 'true';");
				}else{
					$doc->addScriptDeclaration("var bs_load_async = 'false';");
				}

			}else{
				$doc->addScriptDeclaration("var bs_load = 'false';");
				$doc->addScript($libs_media_opath.'/bootstrap4/js/bootstrap.min.js',array(), $defer);
			}
 			*/
		
			//$doc->addScriptDeclaration("var bs_load = 'false';");
			$doc->addScript($libs_media_opath.'/bootstrap4/js/bootstrap.min.js',array(), $defer);

			
			$buf_debug += BufHelper::addDebug('BOOSTRAP 4 custom', 'code', '<strong>/bootstrap4/js/bootstrap.bundle.min.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
		}	
	}




	/***************************/
	//BS5
	if($buf_bs_on == "5"){
		
		$doc->addScriptDeclaration("var bs_version = 5;");

		$bs_5 = new Registry; 
		$bs_5->loadString(json_encode($templateparams->get('buf_bs_v5'))); 

		$bs5_js = $bs_5->get("buf_bootstrap_js",'custom');
		$bs5_js_bundle = $bs_5->get("buf_bs_bundle",'');

		HTMLHelper::_('bootstrap.framework', false);

		//Joomla bootstrap 3 JS.. I dont know if this is a good idea
		if($bs5_js=='joomla' || $edit){
			HTMLHelper::_('bootstrap.framework');
			$buf_debug += BufHelper::addDebug('BOOSTRAP 5', 'code', '<small>JHtml::_(\'bootstrap.framework\')</small>', $startmicro, 'table-info', 'logic.php');
		
		}elseif($bs5_js=='cdn'){
			$doc->addScript(
				'//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.min.js',
				array(), 
				array('integrity'=>'sha512-OvBgP9A2JBgiRad/mM36mkzXSXaJE9BEIENnVEmeZdITvwT09xnxLtT4twkCa8m/loMbPHsvPl0T8lRGVBwjlQ==', 'crossorigin'=>'anonymous', 'referrerpolicy'=>'no-referrer')
			);

			$buf_debug += BufHelper::addDebug('BOOSTRAP 5 custom', 'code', '<strong>cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.min.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
		}elseif($bs5_js=='custom'){

			if($templateparams->get('buf_unset','') != ''){
				if (in_array('media/jui/js/bootstrap.min.js', $templateparams->get('buf_unset',''))){
					unset($doc->_scripts[$this->baseurl .'/media/jui/js/bootstrap.min.js']);
					$buf_debug += BufHelper::addDebug('BS joomla JS', 'code', '<strong>UNSET:</strong> <small>media/jui/js/bootstrap.min.js</small>', $startmicro, 'table-info', 'logic.php');
				}
			}
	
			$defer = BufHelper::check_defer_v4($bs_5->get('buf_bs_defer',0));
			//DEPRECATED
			/* 			
			if($defer){
				$doc->addScriptDeclaration("var bs_load = 'true';");

				if(!empty($defer['async'])){
					$doc->addScriptDeclaration("var bs_load_async = 'true';");
				}else{
					$doc->addScriptDeclaration("var bs_load_async = 'false';");
				}

			}else{
				$doc->addScriptDeclaration("var bs_load = 'false';");
				$doc->addScript($tpath.'/libs/bootstrap/dist/js/bootstrap.bundle.min.js',array(), $defer);
			} 
			*/

					
			//$doc->addScriptDeclaration("var bs_load = 'false';");
			$doc->addScript($libs_media_opath.'/bootstrap/js/bootstrap'.$bs5_js_bundle.'.min.js',array(), $defer);

			HTMLHelper::_('bootstrap.framework', false);
			$buf_debug += BufHelper::addDebug('BOOSTRAP 5 custom', 'code', '<strong>/libs/bootstrap/dist/js/bootstrap'.$bs5_js_bundle.'.min.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
		}	

		/***************************/
		/*******  BS5 CDN LOADING  **********/
		/***************************/

		if($bs_5->get("buf_bootstrap_css",'cdn')=='cdn'){

			$doc->addStyleSheet(
				'//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css',
				array(), 
				array('integrity'=>'sha512-GQGU0fMMi238uA+a/bdWJfpUGKUkBdgfFdgBm72SUQ6BeyWjoY/ton0tEjH+OSH9iP4Dfh+7HM0I9f5eR0L/4w==', 
					'rel'=>'stylesheet',
					'crossorigin'=>'anonymous',
					'referrerpolicy'=>'no-referrer'
				)
			);
		}
	}
}


/***************************/
/*******  FA OPTIONS **********/
/***************************/
$buf_fa_selector = $templateparams->get('buf_fa_selector', '4');
$buf_fa_pro = $templateparams->get('buf_fa_pro', 0);
$buf_fa5_files = $templateparams->get('buf_fa5_files', array('solid'));
$buf_fa6_files = $templateparams->get('buf_fa6_files', array('solid'));
$buf_fa5_fa4fallback = $templateparams->get('buf_fa4fallback', 0);
$buf_fa_defer = $templateparams->get('buf_fa_defer', 0);

/***************************/
/*******  JS LAYOUT ********/

if($buf_load_layout_js){

	$force_recache_string = '';

	if($templateparams->get('force_recache_js', 0)){
		$force_recache_string = '?buf_recached=' . rand();
		//$doc->addScript($tpath.'/layouts/'.$buf_layout.'/js/buf_layout.js?buf_recached='.rand(),array(), $defer);
	}

	$doc->addScript($tpath.'/layouts/'.$buf_layout.'/js/buf_layout.js'.$force_recache_string,array(), $defer);
	$buf_debug += BufHelper::addDebug('LOAD layout js', 'code', '<strong>buf_layout.js RECACHED: '.$force_recache_string.'</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
	
}





/***************************/
/*******  JS CUSTOM ********/

$buf_load_custom_js = $templateparams->get('buf_load_custom_js',array());

foreach ($buf_load_custom_js as $key => $cus_js) {
	if($cus_js->buf_load_custom_js_script == '') continue;
	$defer_custom_js = BufHelper::check_defer_v4($cus_js->buf_js_defer);
	$doc->addScript($tpath.'/layouts/'.$buf_layout.'/js/'.$cus_js->buf_load_custom_js_script,array(), $defer_custom_js);
	$buf_debug += BufHelper::addDebug('LOAD custom script |'.$key, 'code', $buf_layout.'/js/'.'<strong>'.$cus_js->buf_load_custom_js_script.'</strong> <small>'.var_export($defer_custom_js, true).'</small>', $startmicro, 'table-info', 'logic.php');
}



/********************************************************************************************************/
/*****  UNSET SCRIPTS ********/
/********************************************************************************************************/



/**********************/
//JOOMLA scripts UNSET
/**********************/
if($unset_js && $edit==false){

	foreach ($unset_js as $key => $unset) {

		unset($doc->_scripts[$this->baseurl .'/'.$unset]);
		
		$buf_debug += BufHelper::addDebug(' Unset | '.$key, 'trash-alt fas', '<strong>Unset </strong> '.$unset, $startmicro, 'table-danger', 'logic.php');

	}

}

/************************************/
/*******  REMOVE  javascript string ********/
/*
$buf_remove_from_script = $templateparams->get('buf_remove_from_script','');

if($buf_remove_from_script != ''){

	$jscripts = $doc->_script['text/javascript'];

	if (strpos($jscripts, $buf_remove_from_script) !== false) {
		$jscripts_cut = str_replace($buf_remove_from_script,"",$jscripts);

		$doc->_script['text/javascript'] = $jscripts_cut;


		JFactory::getDocument()->addScriptDeclaration('jQuery(document).ready(function($) {
			'.$buf_remove_from_script.'
		});');

		$buf_debug += BufHelper::addDebug('remove from jscripts', 'code', '<strong>'.$buf_remove_from_script.'</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
	}

}
*/



/***************************/
/*******  JS CUSTOM REMOVE ********/





if($templateparams->get('buf_custom_unset','')){
	$buf_custom_unset = $templateparams->get('buf_custom_unset','');

	if($buf_custom_unset != ''){

		foreach ($buf_custom_unset as $key => $defer_cus_js) {


			if($defer_cus_js->buf_custom_unset_script == '') continue;
			
			$defer_cus_js = trim($defer_cus_js->buf_custom_unset_script);
			unset($doc->_scripts[$this->baseurl .'/'.$defer_cus_js]);
	
			//$doc->addScript($defer_cus_js,array(), $defer);
			$buf_debug += BufHelper::addDebug('CUSTOM UNSET| '.$key, 'trash', '<strong>'.$defer_cus_js.'</strong>', $startmicro, 'table-danger', 'logic.php');
			
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

if(($buf_fa_selector == 5) && $buf_fa5_tech == 2){

	$fa5pro_exists = file_exists ($libspath . '/font-awesome/fontawesome5pro/webfonts/fa-brands-400.ttf') ? true:false;
	$fa6pro_exists = file_exists ($libspath . '/font-awesome/fontawesome6pro/webfonts/fa-brands-400.ttf') ? true:false;

	if($buf_fa_selector == 5){

		if($buf_fa_pro && $fa5pro_exists){
			$fa_path = 'fontawesome5pro';
			$dir = $cachepath.'/fontawesome5pro/webfonts';
			$buf_fa_webfont_exists = (count(glob("$dir/*")) === 0) ? false : true;
		}else{
			$fa_path = 'fontawesome5';
			$dir = $cachepath.'/fontawesome5/webfonts';
			$buf_fa_webfont_exists = (count(glob("$dir/*")) === 0) ? false : true;
		}

		if(!$buf_fa_webfont_exists) $buf_fa_run_webfont = true;
	}
}



//TRUE or FALSE
$buf_bs_css_exist = file_exists($cachepath.'/buf_bs.css');
$buf_css_exist = file_exists($cachepath.'/buf.css');
$buf_fa_css = file_exists($cachepath.'/buf_fa.css');


$buf_check_files = true;
if(
	!$buf_bs_css_exist 	|| 
	!$buf_fa_css 		|| 
	!$buf_css_exist 	|| 
	$buf_fa_run_webfont
){

	$buf_check_files = false;

	$buf_debug += BufHelper::addDebug(
		'CHECK CACHE FILES', 'cog', 'bs:'.var_export($buf_bs_css_exist, true).
		' fa:'.var_export($buf_fa_css, true).
		' buf:'.var_export($buf_fa_css, true).
		' fa_wf:'.var_export($buf_fa_run_webfont, true), $startmicro,'table-primary', 'logic.php');

	$buf_debug += BufHelper::addDebug('COMPILER', 'cog', 'ALL', $startmicro,'table-primary','logic.php');


}


if ($templateparams->get('runless', 0) != 2 || !$buf_check_files){
	//include_once JPATH_THEMES.'/'.$this->template.'/logics/runsass.php';
	include_once JPATH_SITE.'/templates/buf/classes/bufsass.php';
	
	//CLASS BUFSASS
	$buffles = new BUFsass();

	$runless = $buffles::runsass('', $templateparams, 'buf', $startmicro);

	$buf_debug += $runless;

	//CHECK IF webfont CACHE EXISTS
	if($buf_fa_run_webfont){
		$buffles::buf_fa_copy_to_cache($fa_path);
		$buf_debug += BufHelper::addDebug('FA cache', 'cog', 'COPY WEBFONTS TO CACHE', $startmicro,'table-primary','logic.php');
	}
}










/***************************/
/***************************/
/*******  CSS  TEMPLATE **********/
/***************************/
/***************************/

//* ASYNC DEPRECATED
if($templateparams->get('buf_load_css_async', 1)){

	if(!$css_mix){

		$doc->addStyleSheet(URI::base(false).$cache_opath.'buf.css'.'?'.$doc->getMediaVersion(), array(), array('media' => 'print', 'rel' => 'lazy-stylesheet', 'onload' => 'this.media=\'all\''));
		//BUF.css
		
		/*
			echo '<noscript id="deferred-styles_buf"><link rel="stylesheet" type="text/css" href="'.$cache_tpath.'buf.css?'.$doc->getMediaVersion().'"/>
			</noscript>';

			$doc->addScriptDeclaration('
				var loadDeferredStyles_buf = function() {
					var addStylesNode = document.getElementById("deferred-styles_buf");
					var replacement = document.createElement("div");
					replacement.innerHTML = addStylesNode.textContent;
					document.body.appendChild(replacement)
					addStylesNode.parentElement.removeChild(addStylesNode);
				};
				var raf = requestAnimationFrame || mozRequestAnimationFrame ||
					webkitRequestAnimationFrame || msRequestAnimationFrame;
				if (raf) raf(function() { window.setTimeout(loadDeferredStyles_buf, 0); });
				else window.addEventListener(\'load\', loadDeferredStyles_buf);
			');
		*/

		$buf_debug += BufHelper::addDebug('BUF css ASYNC', 'thumbs-up', 'LOADED <small>'.$cache_tpath.'buf.css</small>', $startmicro ,'table-success', 'logic.php');
	
	}

	$doc->addStyleSheet(URI::base(false).$css_path.'?'.$doc->getMediaVersion(), array(), array('media' => 'print', 'rel' => 'lazy-stylesheet', 'onload' => 'this.media=\'all\''));
    $buf_debug += BufHelper::addDebug('BUF layout css ASYNC', 'thumbs-up', 'LOADED <small>'.$css_path.'<small>', $startmicro ,'table-success', 'logic.php');

}else{
//BUF SYNC CSS
	$doc->addStyleSheet(URI::base(false).$cache_opath.'buf.css'.'?'.$doc->getMediaVersion());
	$buf_debug += BufHelper::addDebug('BUF css', 'thumbs-up', 'LOADED', $startmicro ,'table-success');
	$doc->addStyleSheet($css_path.'?'.$doc->getMediaVersion());
	$buf_debug += BufHelper::addDebug('TEMPLATE css', 'thumbs-up', 'LOADED', $startmicro ,'table-success');
}




/***************************/
/***************************/
/*******  FA5 JS  **********/
/***************************/
/***************************/

//FA5 SVG+JS
if($buf_fa5_tech == 1 && (int) $buf_fa_selector == 5 ){


	//if fa is activated
	if($buf_fa_selector){

		$fa5pro_exists = file_exists(JPATH_THEMES.'/'.$this->template.'/libs/font-awesome/fontawesome5pro/js/fontawesome.min.js');


		$deferfa = BufHelper::check_defer_v4($templateparams->get('buf_fa_defer',0));


		$buf_debug += BufHelper::addDebug(' FA5JS', 'font-awesome fab', '	--------- FONTAWESOME JS ---------', $startmicro,'table-info', 'logic.php');

		//include_once JPATH_SITE.'/templates/buf/classes/bufsass.php';


		if($fa5pro_exists && $buf_fa_pro){
			//fa5 PRO

			//CHECK IF CACHE EXISTS
	        //$buffles = new BUFsass();
	        //$runsass = $buffles::buf_fa_copy_to_cache('fontawesome5pro');

			//fa5PRO
			foreach ($buf_fa5_files as $key => $value) {
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome5pro/js/'.$value.'.min.js', array(), $deferfa);
				$buf_debug += BufHelper::addDebug($key.' FA5pro', 'font-awesome fab', '/fontawesome5pro/js/'.$value.'.min.js, '.var_export($deferfa, true), $startmicro,'table-info', 'logic.php');
			}
			//fa4 fallback
			if($buf_fa5_fa4fallback){
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome5pro/js/v4-shims.min.js', array(), $deferfa);
				$buf_debug += BufHelper::addDebug($key.' FA5pro', 'font-awesome fab', 'Fallback fa4 loaded', $startmicro,'table-info', 'logic.php');
			}

			$doc->addScript($tpath.'/libs/font-awesome/fontawesome5pro/js/fontawesome.min.js', array(), $deferfa);
			$buf_debug += BufHelper::addDebug(' FA5pro gen', 'font-awesome fab', '/fontawesome5pro/js/fontawesome.min.js, '.var_export($deferfa, true), $startmicro,'table-info', 'logic.php');

		}else{
			//fa5 FREE

			//CHECK IF CACHE EXISTS
	        //$buffles = new BUFsass();
	        //$runsass = $buffles::buf_fa_copy_to_cache('fontawesome5');


			//remove PRO files
	        $buf_fa5_files = \array_diff($buf_fa5_files, ["duotone", "light"]);
	        
			//fa5FREE
			foreach ($buf_fa5_files as $key => $value) {
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome5/js/'.$value.'.min.js', array(), $deferfa);
				$buf_debug += BufHelper::addDebug('FA5 | '.$value, 'font-awesome fab', $value, $startmicro,'table-info', 'logic.php');
			}

			//fa4 fallback
			if($buf_fa5_fa4fallback){
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome5/js/v4-shims.min.js', array(), $deferfa);
				$buf_debug += BufHelper::addDebug('FA5 | ', 'font-awesome fab', 'Fallback fa4 loaded', $startmicro,'table-info', 'logic.php');
			}
			$doc->addScript($tpath.'/libs/font-awesome/fontawesome5/js/fontawesome.min.js', array(), $deferfa);
		}

	}
}




/***************************/
/***************************/
/*******  FA6 JS  **********/
/***************************/
/***************************/

//FA6
if($buf_fa5_tech == 1 && (int) $buf_fa_selector == 6 ){


	//if fa is activated
	if($buf_fa_selector){

		$fa6pro_exists = file_exists(JPATH_THEMES.'/'.$this->template.'/libs/font-awesome/fontawesome6pro/js/fontawesome.min.js');

		$deferfa = BufHelper::check_defer_v4($templateparams->get('buf_fa_defer',0));
		$buf_debug += BufHelper::addDebug(' FA6JS', 'font-awesome fab', '	--------- FONTAWESOME 6 JS ---------', $startmicro,'table-info', 'logic.php');

		//include_once JPATH_SITE.'/templates/buf/classes/bufsass.php';

		if($fa6pro_exists && $buf_fa_pro){
			//fa6PRO
			foreach ($buf_fa6_files as $key => $value) {
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome6pro/js/'.$value.'.min.js', array(), $deferfa);
				$buf_debug += BufHelper::addDebug($key.' FA6pro', 'font-awesome fab', '/fontawesome6pro/js/'.$value.'.min.js, '.var_export($deferfa, true), $startmicro,'table-info', 'logic.php');
			}
	
			$doc->addScript($tpath.'/libs/font-awesome/fontawesome6pro/js/fontawesome.min.js', array(), $deferfa);
			$buf_debug += BufHelper::addDebug(' FA6pro gen', 'font-awesome fab', '/fontawesome6pro/js/fontawesome.min.js, '.var_export($deferfa, true), $startmicro,'table-info', 'logic.php');

		}else{
			//fa6 FREE

			//remove PRO files
	        $buf_fa6_files = \array_diff($buf_fa6_files, ["duotone", "light", "thin"]);
	        
			//fa5FREE
			foreach ($buf_fa6_files as $key => $value) {
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome6/js/'.$value.'.min.js', array(), $deferfa);
				$buf_debug += BufHelper::addDebug('FA6 | '.$value, 'font-awesome fab', $value, $startmicro,'table-info', 'logic.php');
			}

			$doc->addScript($tpath.'/libs/font-awesome/fontawesome6/js/fontawesome.min.js', array(), $deferfa);
		}

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

	if($buf_extras_animate->get('animate_on','0')=='1'){
		//$doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), $defer);
		$doc->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
		$buf_debug += BufHelper::addDebug('ANIMATE', 'helicopter', 'LOADED', $startmicro,'table-info', 'logic.php');
	}

	//* CUSTOM JS
	$buf_extra_custom_js = new Registry; 
	$buf_extra_custom_js->loadString(json_encode($templateparams->get('buf_extra_custom_js'))); 

	foreach ($buf_extra_custom_js as $key => $cus_js) {
		if($cus_js->buf_load_custom_js_script == '') continue;
		$defer_custom_js = BufHelper::check_defer_v4($cus_js->buf_js_defer);
		$doc->addScript($cus_js->buf_load_custom_js_script, array(), $defer_custom_js);
		$buf_debug += BufHelper::addDebug($key, 'code', '<strong>'.$cus_js->buf_load_custom_js_script.'</strong> <small>'.var_export($defer_custom_js, true).'</small>', $startmicro, 'table-info', 'logic.php');
	}
	


/***************************/
/***************************/
/*****  ANALYTICS  *********/
/***************************/
/***************************/

if($templateparams->get('buf_analytics', 0)){
	include_once('googleAnalytics.php');
}





/***************************************/
/*****************DEBUG*****************/
/***************************************/
if($buf_debug_param){

	//LOADED
	$buf_debug += BufHelper::addDebug('JOOMLA!', 'joomla far fab', '--------- LOADED ---------', $startmicro , 'table-success', 'index.php');//

	//COUNT SCRIPTS
	$conta_script = 0;
	foreach ($doc->_scripts as $loadedjs => $jskey) {

		$buf_debug += BufHelper::addDebug('JS | '.$conta_script, 'joomla far fab', '<strong>loaded: </strong><small>'.$loadedjs.'</small>', $startmicro,'table-default', 'logic.php');

		$conta_script += 1;
	}

	include JPATH_THEMES.'/'.$this->template.'/logics/debug.php';
}



/***************************************/
/********   remember development  *********/
/***************************************/

if($templateparams->get('runless', 1) != 2){
	$uri = Uri::getInstance(); 
	$uri_base = $uri->toString().'&edit_base=true';

	echo '<div class="buf_dev_mode">
	  		<div class="buf_dev_msg">
	  			<i class="fas fa-cogs"></i> 
	  			<span>BUF template in development mode. Please use Production for better load times.</span>
	  		</div>
	  		<a class="buf_dev_mode_edit_base" href="'.$uri_base.'"><i class="fas fa-box-open"></i> Base </a>
  			<a class="buf_dev_mode_close"><i class="fas fa-times-circle"></i> Close</a>
  	</div>';
}



/***************************/
/***************************/
/*******  BUF OffCanvas **********/
/***************************/
/***************************/


if(($templateparams->get('buf_offcanvas', 1) != 0) && !$tmplComponent){
	if($buf_debug_param){
		$doc->addScript($tpath.'/js/oc/bufoc.js',array(), array('defer'=>'defer'));
	}else{
		$doc->addScript($tpath.'/js/oc/bufoc.min.js',array(), $defer);
	}

	$buf_debug += BufHelper::addDebug('bufoc.js', 'code', '<strong>OFFCANVAS bufoc.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
}




