<?php defined( '_JEXEC' ) or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

$buf_debug += addDebug('COMPONENTS | loaded', 'joomla fab', 'INIT logic stuff', $startmicro, 'table-success', 'logic.php');

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
/*******  JS jQUERY **********/
/***************************/
/***************************/
$buf_jquery = $templateparams->get('buf_jquery',2);
if($buf_jquery==2 || $edit){
	HTMLHelper::_('jquery.framework');
	
}elseif($buf_jquery==1){

	$defer = check_defer_v4($templateparams->get('buf_jquery_defer','0'));

	//v4
	//$doc->addScript($tpath.'/js/jquery/jquery-3.6.0.min.js',array(), $defer);
	//load from JTFW
	$doc->addScript(Uri::base().'libraries/jtlibs/jquery/jquery.min.js',array(), $defer);

	$buf_debug += addDebug('JQUERY custom', 'code', '<strong>jquery.min.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
}



/***************************/
/***************************/
/*****  JS BOOTSTRAP *******/
/***************************/
/***************************/
$doc->addScriptDeclaration("var bs_load = 'false';");


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

			$buf_debug += addDebug('BOOSTRAP 4', 'code', '<small>JHtml::_(\'bootstrap.framework\')</small>', $startmicro, 'table-info', 'logic.php');

		}elseif($bs4_js=='custom'){

			if($templateparams->get('buf_unset','') != ''){
				if (in_array('media/jui/js/bootstrap.min.js', $templateparams->get('buf_unset',''))){
					unset($doc->_scripts[$this->baseurl .'/media/jui/js/bootstrap.min.js']);
					$buf_debug += addDebug('BS joomla JS', 'code', '<strong>UNSET:</strong> <small>media/jui/js/bootstrap.min.js</small>', $startmicro, 'table-info', 'logic.php');
				}
			}

	
			$defer = check_defer_v4($bs_4->get('buf_bs_defer',0));

			if($defer){
				$doc->addScriptDeclaration("var bs_load = 'true';");

				if(!empty($defer['async'])){
					$doc->addScriptDeclaration("var bs_load_async = 'true';");
				}else{
					$doc->addScriptDeclaration("var bs_load_async = 'false';");
				}

			}else{
				$doc->addScriptDeclaration("var bs_load = 'false';");
				$doc->addScript($tpath.'/libs/bootstrap4/dist/js/bootstrap.bundle.min.js',array(), $defer);
			}

			HTMLHelper::_('bootstrap.framework', false);
			$buf_debug += addDebug('BOOSTRAP 4 custom', 'code', '<strong>/libs/bootstrap4/dist/js/bootstrap.bundle.min.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
		}	
	}




	/***************************/
	//BS5
	if($buf_bs_on == "5"){
		
		$doc->addScriptDeclaration("var bs_version = 5;");

		$bs_5 = new Registry; 
		$bs_5->loadString(json_encode($templateparams->get('buf_bs_v5'))); 

		$bs5_js = $bs_5->get("buf_bootstrap_js",'custom');

		//Joomla bootstrap 3 JS.. I dont know if this is a good idea
		if($bs5_js=='joomla' || $edit){

			HTMLHelper::_('bootstrap.framework');
			$buf_debug += addDebug('BOOSTRAP 5', 'code', '<small>JHtml::_(\'bootstrap.framework\')</small>', $startmicro, 'table-info', 'logic.php');
		
		}elseif($bs5_js=='cdn'){
			$doc->addScript(
				'//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js',
				array(), 
				array('integrity'=>'sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM', 'crossorigin'=>'anonymous')
			);

			$buf_debug += addDebug('BOOSTRAP 5 custom', 'code', '<strong>cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
		}elseif($bs5_js=='custom'){

			if($templateparams->get('buf_unset','') != ''){
				if (in_array('media/jui/js/bootstrap.min.js', $templateparams->get('buf_unset',''))){
					unset($doc->_scripts[$this->baseurl .'/media/jui/js/bootstrap.min.js']);
					$buf_debug += addDebug('BS joomla JS', 'code', '<strong>UNSET:</strong> <small>media/jui/js/bootstrap.min.js</small>', $startmicro, 'table-info', 'logic.php');
				}
			}
	
			$defer = check_defer_v4($bs_5->get('buf_bs_defer',0));

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

			HTMLHelper::_('bootstrap.framework', false);
			$buf_debug += addDebug('BOOSTRAP 5 custom', 'code', '<strong>/libs/bootstrap/dist/js/bootstrap.bundle.min.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
		}	

		/***************************/
		/*******  BS5 CDN LOADING  **********/
		/***************************/

		if($bs_5->get("buf_bootstrap_css",'cdn')){
			$doc->addStyleSheet(
				'//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
				array(), 
				array('integrity'=>'sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC', 
				'rel'=>'stylesheet',
				'crossorigin'=>'anonymous')
			);

		}

	}


}



/***************************/
/*******  FA OPTIONS **********/
/***************************/
$buf_fa_selector = $templateparams->get('buf_fa_selector', '4');
$buf_fa_pro = $templateparams->get('buf_fa_pro', 0);
$buf_fa5_files = $templateparams->get('buf_fa5_files', 'solid');
$buf_fa5_fa4fallback = $templateparams->get('buf_fa4fallback', 0);
$buf_fa_defer = $templateparams->get('buf_fa_defer', 0);



/***************************/
/***************************/
/*******  JS LOGIC **********/
/***************************/
/***************************/
$defer = check_defer_v4($templateparams->get('buf_js_defer',1));


if($buf_debug_param){
	$doc->addScript($tpath.'/js/logic.js',array(), $defer);
}else{
	$doc->addScript($tpath.'/js/logic.min.js',array(), $defer);
}

$buf_debug += addDebug('logic.js', 'code', '<strong>logic.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');


/***************************/
/*******  JS LAYOUT ********/

if($buf_load_layout_js){

	if($templateparams->get('force_recache_js', 0)){

		$doc->addScript($tpath.'/layouts/'.$buf_layout.'/js/buf_layout.js?buf_recached='.rand(),array(), $defer);
		$buf_debug += addDebug('LOAD layout js', 'code', '<strong>buf_layout.js RECACHED</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
	}else{

		$doc->addScript($tpath.'/layouts/'.$buf_layout.'/js/buf_layout.js',array(), $defer);
		$buf_debug += addDebug('LOAD layout js', 'code', '<strong>buf_layout.js non RECACHED</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
	}
}


/***************************/
/*******  JS CUSTOM ********/

$buf_load_custom_js = json_decode($templateparams->get('buf_load_custom_js',''));


if(!empty($buf_load_custom_js->buf_load_custom_js_script)){

	if($buf_load_custom_js->buf_load_custom_js_script[0] != ''){
	
		foreach ($buf_load_custom_js->buf_load_custom_js_script as $key => $cus_js) {

			$defer_custom_js = check_defer_v4($buf_load_custom_js->buf_js_defer[$key]);
			$doc->addScript($tpath.'/layouts/'.$buf_layout.'/js/'.$cus_js,array(), $defer_custom_js);
			
			$buf_debug += addDebug('LOAD custom script |'.$key, 'code', $buf_layout.'/js/'.'<strong>'.$cus_js.'</strong> <small>'.var_export($defer_custom_js, true).'</small>', $startmicro, 'table-info', 'logic.php');
		}
	}
}




/********************************************************************************************************/
/*****  UNSET SCRIPTS ********/
/********************************************************************************************************/



/**********************/
//JOOMLA scripts UNSET
/**********************/
if($templateparams->get('buf_unset','') && $edit==false){

	foreach ($templateparams->get('buf_unset','') as $key => $unset) {
		
		unset($doc->_scripts[$this->baseurl .'/'.$unset]);
		
		$buf_debug += addDebug(' Unset_JS logic | '.$key, 'trash far', '<strong>Unset </strong> '.$unset, $startmicro, 'table-danger', 'logic.php');

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

		$buf_debug += addDebug('remove from jscripts', 'code', '<strong>'.$buf_remove_from_script.'</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
	}

}
*/




/***************************/
/*******  JS CUSTOM REMOVE ********/





if($templateparams->get('buf_custom_unset','')){
	$buf_custom_unset = json_decode($templateparams->get('buf_custom_unset',''))->buf_custom_unset_script;

	if($buf_custom_unset != ''){


		foreach ($buf_custom_unset as $key => $defer_cus_js) {
			
			$defer_cus_js = trim($defer_cus_js);
			unset($doc->_scripts[$this->baseurl .'/'.$defer_cus_js]);
	
			//$doc->addScript($defer_cus_js,array(), $defer);
			$buf_debug += addDebug('CUSTOM UNSET| '.$key, 'trash', '<strong>'.$defer_cus_js.'</strong>', $startmicro, 'table-danger', 'logic.php');
			
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

if($buf_fa_selector && $buf_fa5_tech == 2){

	$fa5pro_exists = file_exists ($libspath . '/font-awesome/fontawesome5pro/webfonts/fa-brands-400.ttf') ? true:false;

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

	$buf_debug += addDebug(
		'CHECK CACHE FILES', 'cog', 'bs:'.var_export($buf_bs_css_exist, true).
		' fa:'.var_export($buf_fa_css, true).
		' buf:'.var_export($buf_fa_css, true).
		' fa_wf:'.var_export($buf_fa_run_webfont, true), $startmicro,'table-primary', 'logic.php');

	$buf_debug += addDebug('COMPILER', 'cog', 'ALL', $startmicro,'table-primary','logic.php');


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
		$buf_debug += addDebug('FA cache', 'cog', 'COPY WEBFONTS TO CACHE', $startmicro,'table-primary','logic.php');
	}
}







/***************************/
/***************************/
/*******  CSS  RECACHE  **********/
/***************************/
/***************************/
if($templateparams->get('force_recache', 0)){
	//true
	$session = Factory::getSession();
	$current_css = $session->get('buf_template_sha');
	if($css_mix){
		$css_path = $cache_tpath.$current_css.'_mix.css';
	}else{
		$css_path = $cache_tpath.$current_css.'.css';
	}

}else{
	if($css_mix){
		$css_path = $cache_tpath.$buf_layout.'_template_mix.css';
	}else{
		$css_path = $cache_tpath.$buf_layout.'_template.css';
	}
}


/***************************/
/***************************/
/*******  CSS  TEMPLATE **********/
/***************************/
/***************************/

if($templateparams->get('buf_load_css_async', 1)){

	if(!$css_mix){
	//BUF.css
	echo '<noscript id="deferred-styles_buf"><link rel="stylesheet" type="text/css" href="'.$cache_tpath.'buf.css"/>
    </noscript>';

    echo '<script>
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
    </script>';
    $buf_debug += addDebug('BUF css ASYNC', 'thumbs-up', 'LOADED <small>'.$cache_tpath.'buf.css</small>', $startmicro ,'table-success', 'logic.php');
	}



    //layout template.css
	echo '<noscript id="deferred-styles_buf_layout">
      <link rel="stylesheet" type="text/css" href="'.$css_path.'"/>
    </noscript>';

    echo '<script>
      var loadDeferredStyles_buf_layout = function() {
        var addStylesNode = document.getElementById("deferred-styles_buf_layout");
        var replacement = document.createElement("div");
        replacement.innerHTML = addStylesNode.textContent;
        document.body.appendChild(replacement)
        addStylesNode.parentElement.removeChild(addStylesNode);
      };
      var raf = requestAnimationFrame || mozRequestAnimationFrame ||
          webkitRequestAnimationFrame || msRequestAnimationFrame;
      if (raf) raf(function() { window.setTimeout(loadDeferredStyles_buf_layout, 0); });
      else window.addEventListener(\'load\', loadDeferredStyles_buf_layout);
    </script>';
    $buf_debug += addDebug('BUF layout css ASYNC', 'thumbs-up', 'LOADED <small>'.$css_path.'<small>', $startmicro ,'table-success', 'logic.php');



}else{
//BUF SYNC CSS
	$doc->addStyleSheet($cache_tpath.'/buf.css');
	$buf_debug += addDebug('BUF css', 'thumbs-up', 'LOADED', $startmicro ,'table-success');
	$doc->addStyleSheet($css_path);
	$buf_debug += addDebug('TEMPLATE css', 'thumbs-up', 'LOADED', $startmicro ,'table-success');
}


/***************************/
/***************************/
/*******  FA5 JS  **********/
/***************************/
/***************************/


//FA5 SVG+JS
if($buf_fa5_tech == 1 && $buf_fa_selector=='5'){

	//if fa is activated
	if($buf_fa_selector){

		$fa5pro_exists = file_exists(JPATH_THEMES.'/'.$this->template.'/libs/font-awesome/fontawesome5pro/js/fontawesome.min.js');


		$deferfa = check_defer_v4($templateparams->get('buf_fa_defer',0));


		$buf_debug += addDebug(' FA5JS', 'font-awesome fab', '	--------- FONTAWESOME JS ---------', $startmicro,'table-info', 'logic.php');

		include_once JPATH_SITE.'/templates/buf/classes/bufsass.php';


		if($fa5pro_exists && $buf_fa_pro){
			//fa5 PRO

			//CHECK IF CACHE EXISTS
	        //$buffles = new BUFsass();
	        //$runsass = $buffles::buf_fa_copy_to_cache('fontawesome5pro');

			//fa5PRO
			foreach ($buf_fa5_files as $key => $value) {
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome5pro/js/'.$value.'.min.js', array(), $deferfa);
				$buf_debug += addDebug($key.' FA5pro', 'font-awesome fab', '/fontawesome5pro/js/'.$value.'.min.js, '.var_export($deferfa, true), $startmicro,'table-info', 'logic.php');
			}
			//fa4 fallback
			if($buf_fa5_fa4fallback){
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome5pro/js/v4-shims.min.js', array(), $deferfa);
				$buf_debug += addDebug($key.' FA5pro', 'font-awesome fab', 'Fallback fa4 loaded', $startmicro,'table-info', 'logic.php');
			}

			$doc->addScript($tpath.'/libs/font-awesome/fontawesome5pro/js/fontawesome.min.js', array(), $deferfa);
			$buf_debug += addDebug(' FA5pro gen', 'font-awesome fab', '/fontawesome5pro/js/fontawesome.min.js, '.var_export($deferfa, true), $startmicro,'table-info', 'logic.php');

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
				$buf_debug += addDebug('FA5 | '.$value, 'font-awesome fab', $value, $startmicro,'table-info', 'logic.php');
			}

			//fa4 fallback
			if($buf_fa5_fa4fallback){
				$doc->addScript($tpath.'/libs/font-awesome/fontawesome5/js/v4-shims.min.js', array(), $deferfa);
				$buf_debug += addDebug('FA5 | ', 'font-awesome fab', 'Fallback fa4 loaded', $startmicro,'table-info', 'logic.php');
			}
			$doc->addScript($tpath.'/libs/font-awesome/fontawesome5/js/fontawesome.min.js', array(), $deferfa);
		}

	}
}



/***************************/
/***************************/
/*****  ANALYTICS  *********/
/***************************/
/***************************/

if($templateparams->get('buf_analytics', 0)){
	$a_code = $templateparams->get('buf_analytics_code', 'UA-XXXXX-Y');
	
	$buf_analytics_storage = $templateparams->get('buf_analytics_storage', '0');

	if($a_code != 'UA-XXXXX-Y'){
		
		//$doc->addScript('https://www.googletagmanager.com/gtag/js?id='.$a_code, false, true);
		
		//$doc->addScript('templates/buf/js/analytics/buf_gtag.js?id='.$a_code, false, true);

		$doc->addScriptDeclaration("
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			
			
			
		");
		if($buf_analytics_storage){
			$doc->addScriptDeclaration("ga('create', '".$a_code."', 'auto');");
		}else{
			$doc->addScriptDeclaration("
				ga('create', '".$a_code."', {
					'storage': 'none'
			  	});
			");
		}
		
	}


}












/***************************************/
/*****************DEBUG*****************/
/***************************************/
if($buf_debug_param){

	//LOADED
	$buf_debug += addDebug('JOOMLA!', 'joomla far fab', '--------- LOADED ---------', $startmicro , 'table-success', 'index.php');//

	//COUNT SCRIPTS
	$conta_script = 0;
	foreach ($doc->_scripts as $loadedjs => $jskey) {

		$buf_debug += addDebug('JS | '.$conta_script, 'joomla far fab', '<strong>loaded: </strong><small>'.$loadedjs.'</small>', $startmicro,'table-default', 'logic.php');

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
//$defer = check_defer_v4($templateparams->get('buf_js_defer',1));

//$doc->addScript($tpath.'/js/bufoc.js',array(), $defer);
//$buf_debug += addDebug('bufoc.js', 'code', '<strong>bufoc.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');



if($templateparams->get('buf_offcanvas', 1) != 0){
	if($buf_debug_param){
		$doc->addScript($tpath.'/js/oc/bufoc.js',array(), array('defer'=>'defer'));
	}else{
		$doc->addScript($tpath.'/js/oc/bufoc.min.js',array(), $defer);
	}

	$buf_debug += addDebug('bufoc.js', 'code', '<strong>OFFCANVAS bufoc.js</strong> <small>'.var_export($defer, true).'</small>', $startmicro, 'table-info', 'logic.php');
}






/**
 * @param $defer
 *
 * @return array
 *
 * @since version
 */
function check_defer($defer){

	//defer, async
	if($defer == 0){
		$defer_array = array(false, false);
	}elseif($defer == 1){
		$defer_array = array(true, false);
	}elseif($defer == 2){
		$defer_array = array(false, true);
	}else{
		$defer_array = array(true, true);
	}

	return $defer_array;
}


/**
 * @param $defer
 *
 * @return array
 *
 * @since version 4
 */
function check_defer_v4($defer){

	//defer, async
	if($defer == 0){
		$defer_array = array();
	}elseif($defer == 1){
		$defer_array = array('defer' => 'defer');
	}elseif($defer == 2){
		$defer_array = array('async' => 'async');
	}else{
		$defer_array = array('defer' => 'defer', 'async' => 'async');
	}

	return $defer_array;
}

