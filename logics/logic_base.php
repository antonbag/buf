<?php defined( '_JEXEC' ) or die; 

//2.1.0
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Environment\Browser;
use Joomla\Application\Web\WebClient;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper;

///////////////////////
//INIT DEBUG
///////////////////////

$startmicro = microtime(TRUE);
$buf_debug = array();
$buf_debug += addDebug('START', 'flag-checkered', 'start', $startmicro, 'table-success', 'logic_base.php');



///////////////////////
//CHECK PHP VERSION
///////////////////////
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	## No version info. I'll lie and hope for the best.
	$version = '5.6.0';
}

// An old PHP versIon is installed.
if(!version_compare($version, '5.6.0', '>=')){ echo Text::_('You are using an old PHP version. Please upgrade to a newer version');}



//2.2.0
///////////////////////
//DEVEL
///////////////////////
$devel_mode = file_exists (JPATH_SITE.'/templates/buf/composer.json');
if($devel_mode){
	include_once JPATH_THEMES.'/'.$this->template.'/buf_devel.php';
}


$app  = Factory::getApplication();
$doc = Factory::getDocument();
$session = Factory::getSession();
$jinput = $app->input;


///////////////////////
//APP PARAMS
///////////////////////
$params = $app->getParams();


$templateparams	= $app->getTemplate(true)->params;
$menu = $app->getMenu();

$active = (object) array('alias' => 'noactivefound');
if($app->getMenu()->getActive()){
	$active = $app->getMenu()->getActive();
}

$menutype = ($menu->getActive() != null) ? $menu->getActive()->menutype: '';

$docalias = OutputFilter::stringUrlSafe($doc->title);
$pageclass = $params->get('pageclass_sfx','');

$bs_version = $templateparams->get("buf_bs_on", 4);



//LAYOUTS
///////////////////////
$buf_layout = $templateparams->get('buf_layout','default');
$buf_load_layout_js = $templateparams->get('buf_load_layout_js',1);

$edit = ($jinput->getValue('layout') == 'edit') ? true : false;
$edit_base_input = ($jinput->getValue('edit_base') == 'true') ? true : false;



//PATHS
///////////////////////
$tpath = $this->baseurl.'/templates/'.$this->template;
$tpath_abs = JPATH_SITE.'/templates/buf';
$layoutpath = JPATH_SITE.'/templates/buf/layouts/'.$buf_layout;
$cachepath = JPATH_CACHE.'/buf_'.$buf_layout.'/';
$cache_tpath = $this->baseurl.'/cache/buf_'.$buf_layout.'/';
$libspath = JPATH_SITE.'/templates/buf/libs';
$jtfw_libspath = JPATH_LIBRARIES.'/jtlibs';



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

if($detection == 'device' || $detection == 'mix'){

	$detected_file = false;

	if(!class_exists('Mobile_Detect')){
		if(file_exists(JPATH_LIBRARIES.'/jtlibs/mobiledetectlib/Mobile_Detect.php')){
			require_once JPATH_LIBRARIES.'/jtlibs/mobiledetectlib/Mobile_Detect.php';
			$detected_file = true;
		}
	}else{
		$detected_file = true;
	}



	if($detected_file){
		$tablet = $templateparams->get('buf_tablet_as_mobile', 1);
		$detect = new Mobile_Detect;
		if ( $detect->isMobile() ) {
			if($detect->isTablet()){ 
				if($tablet == 0){
					$jmobile = false;
				}else{
					$jmobile = true;
				}
			}else{
				$jmobile = true;
			}
		}else{
			$jmobile = false;
		}
		$ismobile = $jmobile;
	}else{
		$app->enqueueMessage('Class mobile_detect not found. Possible solution: install JT framework libraries.', 'warning');
	}

}


//DEVICE 
$body_mobile = ($ismobile ? 'device_mobile':'device_not_mobile');
$body_mobile .= ' detecion_mode_'.$detection;

$buf_debug_param = $templateparams->get('buf_debug', 0);
$buf_anal_url = Uri::base().'templates/buf/js/analytics/buf_anal.js';




//STYLE
///////////////////////
$container = ($templateparams->get('buf_container',0) ? 'container-fluid' : 'container');
$content_container = ($templateparams->get('buf_content_container',0) ? 'container-fluid' : 'container');


//OFFCANVAS
$buf_offcanvas = false;

//activated
if($templateparams->get('buf_offcanvas',0) == 1){
	$buf_offcanvas = true;

//only mobile
}elseif($templateparams->get('buf_offcanvas',0) == 2){

	//device mode
	if($detection == 'device'){
		if($ismobile) $buf_offcanvas = true;
	}else{
		$buf_offcanvas = 'mobile';
	}
}

//TOPBAR
///////////////////////
$buf_topbar = new Registry; 

$buf_topbar->loadString(json_encode($templateparams->get('buf_topbar'))); 

$buf_topbar_on = $buf_topbar->get('buf_topbar_on',0);
$buf_topbar_logo_img = $buf_topbar->get('buf_topbar_logo','');
$buf_topbar_logo_fallback = $buf_topbar->get('buf_topbar_logo_fallback','');
$buf_topbar_logo_pos = $buf_topbar->get('buf_topbar_logo_pos',"l");
$buf_topbar_height = $buf_topbar->get('buf_topbar_height','54');
$buf_topbar_color = $buf_topbar->get('buf_topbar_color','#fff');

$buf_topbar_classes = '';
$buf_topbar_logo = '';

if($buf_topbar_on) $buf_topbar_classes .= 'buf_topbar_on';



//logo show
if($buf_topbar->get('buf_topbar_image_show','0')){

	//logo
	if($buf_topbar->get('buf_topbar_logo','') != '' || $buf_topbar->get('buf_topbar_logo_fallback','') != ''){


		$buf_topbar_logo .= '<div class="buf_topbar_logo pos_'.$buf_topbar_logo_pos.'">';
		$buf_topbar_logo .= '<a href="index.php">';
			$buf_topbar_logo .= '<picture>';

				//svg
				if($buf_topbar_logo_img != '' && $buf_topbar_logo_fallback != '') {
					$buf_topbar_logo .= '<source type="'.mime_content_type($buf_topbar_logo_img).'" srcset="'.$buf_topbar_logo_img.'">';
				}
				//fallback
				if($buf_topbar_logo_fallback == '' && $buf_topbar_logo_img != ''){
					$buf_topbar_logo .= '<img class="img-fluid" type="'.mime_content_type($buf_topbar_logo_img).'" src='.$buf_topbar_logo_img.' alt="logo"/>';
				}else if($buf_topbar_logo_fallback != ''){
					$buf_topbar_logo .= '<img class="img-fluid" type="'.mime_content_type($buf_topbar_logo_fallback).'" src='.$buf_topbar_logo_fallback.' alt="logo"/>';
				}

			$buf_topbar_logo .= '</picture>';
			
		$buf_topbar_logo .= '</a>';
		$buf_topbar_logo .= '</div>';
	}
}


//TOPBAR IN OFFCANVAS
///////////////////////
$buf_topbar_oc = new Registry; 
$buf_topbar_oc->loadString(json_encode($templateparams->get('buf_topbar_oc'))); 

$buf_topbar_oc_on = $buf_topbar_oc->get('buf_topbar_on',0);
$buf_topbar_oc_logo_img = $buf_topbar_oc->get('buf_topbar_logo','');
$buf_topbar_oc_logo_fallback = $buf_topbar_oc->get('buf_topbar_logo_fallback','');
$buf_topbar_oc_logo_pos = $buf_topbar_oc->get('buf_topbar_logo_pos',"l");
$buf_topbar_oc_height = $buf_topbar_oc->get('buf_topbar_height','90');
$buf_topbar_oc_color = $buf_topbar_oc->get('buf_topbar_color','#fff');

$buf_topbar_oc_classes = '';
$buf_topbar_oc_logo = '';

if($buf_topbar_oc_on) $buf_topbar_oc_classes .= 'buf_topbar_oc_on';



//logo show
if($buf_topbar_oc->get('buf_topbar_image_show','0')){

	//logo
	if($buf_topbar_oc->get('buf_topbar_logo','') != ''){

		$buf_topbar_oc_logo .= '<div class="buf_topbar_logo pos_'.$buf_topbar_oc_logo_pos.'">';
		$buf_topbar_oc_logo .= '<a href="index.php">';
		$buf_topbar_oc_logo .= '<picture>';
			if($buf_topbar_oc_logo_img != '') $buf_topbar_oc_logo .= '<source type="'.mime_content_type($buf_topbar_oc_logo_img).'" srcset="'.$buf_topbar_oc_logo_img.'">';
			if($buf_topbar_oc_logo_fallback != '') $buf_topbar_oc_logo .= '<img class="img-fluid" type="'.mime_content_type($buf_topbar_oc_logo_fallback).'" src='.$buf_topbar_oc_logo_fallback.' alt="logo"/>';
		$buf_topbar_oc_logo .= '</picture>';
			
		$buf_topbar_oc_logo .= '</a>';
		$buf_topbar_oc_logo .= '</div>';
	}
}




///////////////////////
//OFFCANVAS BUTTON
///////////////////////

$oc_button = new Registry; 
$oc_button->loadString(json_encode($templateparams->get('buf_oc_button'))); 

$buf_oc_button_style = $oc_button->get('buf_oc_button_style','3dx');
$buf_oc_button_reverse =  $oc_button->get('buf_oc_button_reverse','l');
$buf_reverse = ($buf_oc_button_reverse == 'r') ? '-r' : '';
$buf_oc_button_vpos =  $oc_button->get('buf_oc_button_vpos','left');
$buf_oc_button_hpos =  $oc_button->get('buf_oc_button_hpos','top');


//JS VARIABLES
//OLD

$doc->addScriptDeclaration("var buf_anal_url = '{$buf_anal_url}';");
$doc->addScriptDeclaration("var buf_path = '{$tpath}';");
$doc->addScriptDeclaration("var buf_debug = '{$buf_debug_param}';");
$doc->addScriptDeclaration("var buf_ismobile = '{$ismobile}';");
$doc->addScriptDeclaration("var buf_layout = '{$buf_layout}';");

$js_params = array();
$js_params['buf_anal_url'] 		= $buf_anal_url;
$js_params['buf_path'] 			= $tpath;
$js_params['debug'] 			= ($buf_debug_param == 1) ? true : false;
$js_params['ismobile'] 			= $ismobile;
$js_params['layout'] 			= $buf_layout;
$js_params['detection']  		= $detection;
$js_params['offcanvas']  		= $buf_offcanvas;
$js_params['media_w']  			= $templateparams->get('buf_offcanvas_max_w', 900);
$js_params['offspeed']  		= $templateparams->get('buf_offcanvas_speed', 300);
$js_params['oc_width']  		= $templateparams->get('buf_offcanvas_width', 90);
$js_params['oc_width_desktop']  = $templateparams->get('buf_offcanvas_width_desktop', 90);
$js_params['oc_style']  		= $templateparams->get('buf_offcanvas_style','buf_off_cover');
$js_params['oc_position']  		= $templateparams->get('buf_offcanvas_position','buf_off_pos_left');



/******************************************************************************/

$buf_offcanvas_position = $templateparams->get('buf_offcanvas_position','buf_off_pos_left');
$buf_offcanvas_style = $templateparams->get('buf_offcanvas_style','buf_off_move');

$buf_offcanvas_positions =  $templateparams->get('buf_offcanvas_positions','');
$buf_offcanvas_modules = '';

/****************************************************/
/******** CUSTOM MODULES IN CANVAS  *****************/
if($buf_offcanvas_positions !=''){

	$buf_offcanvas_modules .= '<div class="offcanvas_module_in">';

		//$buf_offcanvas_positions = 'menu_portada';
		$buf_offcanvas_positions_array = explode(',',$buf_offcanvas_positions);
							
		foreach ($buf_offcanvas_positions_array as $b_off) {
			$modules = ModuleHelper::getModules($b_off);
			
			foreach ($modules as $module) {
				$buf_offcanvas_modules .=  ModuleHelper::renderModule($module,array('buf_offcanvas'=>true));
			}
		}
	$buf_offcanvas_modules .=  '</div>';
}





//IMAGE
$buf_bg_img = $templateparams->get('buf_bg_img',false);


/**********************/
//BOOSTRAP
/**********************/
$buf_bs_on = $templateparams->get('buf_bs_on',4);



//BS LEFT + RIGHT CALCULATIONS
$bs_grid = new Registry; 
$bs_grid->loadString(json_encode($templateparams->get('buf_bs_grid'))); 

$bs_left_pos = $bs_grid->get('buf_bs_left_pos','buf_left');

$bs_left_pos = $bs_grid->get('buf_bs_left_pos','buf_left');
$bs_left_sm = $bs_grid->get('buf_bs_left_sm',3);
$bs_left_md = $bs_grid->get('buf_bs_left_md',3);
$bs_left_lg = $bs_grid->get('buf_bs_left_lg',3);

$bs_right_pos = $bs_grid->get('buf_bs_right_pos','buf_right');
$bs_right_sm = $bs_grid->get('buf_bs_right_sm',3);
$bs_right_md = $bs_grid->get('buf_bs_right_md',3);
$bs_right_lg = $bs_grid->get('buf_bs_right_lg',3);

$buf_right_sm = ' col-sm-'.$bs_right_sm;
$buf_right_md = ' col-md-'.$bs_right_md;
$buf_right_lg = ' col-lg-'.$bs_right_lg;

$buf_left_sm = ' col-sm-'.$bs_left_sm;
$buf_left_md = ' col-md-'.$bs_left_md;
$buf_left_lg = ' col-lg-'.$bs_left_lg;


$content_pral_bs_sm = '';
$content_pral_bs_md = '';
$content_pral_bs_lg = '';

$buf_right = false;
if($this->countModules($bs_right_pos)){
	$buf_right = true;
}


//both
if ($buf_right && $this->countModules($bs_left_pos)){
	$bs_count_sm = 12-$bs_left_sm-$bs_right_sm;
	$content_pral_bs_sm = ' col-sm-'.$bs_count_sm;

	$bs_count_md = 12-$bs_left_md-$bs_right_md;
	$content_pral_bs_md = ' col-md-'.$bs_count_md;

	$bs_count_lg = 12-$bs_left_lg-$bs_right_lg;
	$content_pral_bs_lg = ' col-lg-'.$bs_count_lg;

//right	
}elseif($buf_right){
	$bs_count_sm = 12-$bs_right_sm;
	$content_pral_bs_sm = ' col-sm-'.$bs_count_sm;

	$bs_count_md = 12-$bs_right_md;
	$content_pral_bs_md = ' col-md-'.$bs_count_md;

	$bs_count_lg = 12-$bs_right_lg;
	$content_pral_bs_lg = ' col-lg-'.$bs_count_lg;

//left	
}elseif($this->countModules($bs_left_pos)){
	$bs_count_sm = 12-$bs_left_sm;
	$content_pral_bs_sm = ' col-sm-'.$bs_count_sm;

	$bs_count_md = 12-$bs_left_md;
	$content_pral_bs_md = ' col-md-'.$bs_count_md;

	$bs_count_lg = 12-$bs_left_lg;
	$content_pral_bs_lg = ' col-lg-'.$bs_count_lg;
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



/**********************/
//FONTS
/**********************/
if($templateparams->get('buf_googlefonts', 1) && $templateparams->get('buf_googlefonts_name', '') != ''){
	//$doc->addScript('https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js',"text/javascript" , false, true);
	$gfont = $templateparams->get('buf_googlefonts_name', '');

	$gfont_array = explode('|',$gfont);

	$gfont_families = '';

	foreach ($gfont_array as $key => $value) {
		if(!$key==0) $gfont_families.=",";
		$gfont_families.="'".$value."'";
		
	}
	//option FETCH AND ALTER
		echo "<script>

			const loadGFont = (url) => {
			  // the 'fetch' equivalent has caching issues
			  var xhr = new XMLHttpRequest();
			  xhr.open('GET', url, true);
			  xhr.onreadystatechange = () => {
			    if (xhr.readyState == 4 && xhr.status == 200) {
			      
			      let css = xhr.responseText;

			      const head = document.getElementsByTagName('head')[0];
			      const style = document.createElement('style');
			      style.appendChild(document.createTextNode(css));
			      head.appendChild(style);
			    }
			  };
			  xhr.send();
			}

			loadGFont('https://fonts.googleapis.com/css?family=".$gfont."&display=swap');

		</script>";
	//require 'fonts.php';
}




/***************************************/
//RUN BASE SASS
/***TODO: configure bs4 files in base
/***************************************/
$base_css_exists = file_exists ($cachepath . '/base.css');


if($runless == '1' || $buf_edit_base == 1 || $base_css_exists==false || $edit_base_input){

	//if($buf_bs_on){
		include_once JPATH_THEMES.'/'.$this->template.'/logics/runsass_base.php';
		$buf_debug += addDebug('BS | scss', 'flag', 'Run runsass_base.php', $startmicro, 'table-default','logic_base.php');
	//}
}





/***************************************/
//PARAMS TO JS
/***************************************/
$params_to_js = json_encode(array('params'=>$js_params));
//$doc->addScriptDeclaration("var php_buf_params = '{$params_to_js}';");



/***************************************/
//PARAMS TO JS
/***************************************/

$bodyclass=[];
$bodyclass[]= $browserType;
$bodyclass[]= ($menu->getActive() == $menu->getDefault()) ? 'front' : 'site';
$bodyclass[]= $active->alias;
$bodyclass[]= $pageclass;
$bodyclass[]= 'alias_'.$docalias;
$bodyclass[]= $docalias;
$bodyclass[]= $body_mobile;
$bodyclass[]= 'menutype_'.$menutype;
$bodyclass[]= 'bs_version_'.$bs_version;



/***************************************/
/***************************************/
/************ ESSENTIAL FUNCS **********/
/***************************************/
/***************************************/

function addDebug($name='', $icon='', $value='', $startmicro=0, $tr_class='', $service=''){

	$current_time = microtime(TRUE);
	$totaltime = $current_time - $startmicro;

	$mireturn = array($name => array('icon'=>$icon, 'value'=>$value, 'totaltime'=>$totaltime*10000, 'tr_class'=>$tr_class, 'service'=>$service));
	return $mireturn;
}
