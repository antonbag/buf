<?php defined( '_JEXEC' ) or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// begin function compress
function compress($buffer) 
{
	include_once('minifier.php');
	$buffer = fn_minify_css($buffer);

	return $buffer;
}


///////////////////
//  CHECK FW //
///////////////////
if (is_file(JPATH_PLUGINS . '/system/jtframework/autoload.php'))
{
	require_once JPATH_PLUGINS . '/system/jtframework/autoload.php';
}else{
	$app = Factory::getApplication();
	$app->enqueueMessage(Text::_('JT_TOTALMENU_ERROR'), 'error');
	$app->enqueueMessage(Text::_('JT_FW_NOT_FOUND'), 'error');
	return;
}


if($base_css_exists == false || $runless == '1' || $buf_edit_base == '1'){


	if(!$base_css_exists) $buf_debug += addDebug('BASE css | check', 'cubes', 'base css doesn\'t exist... creating it.', $startmicro, 'table-warning', 'runsass_base.php');

	//if($runless==0) $buf_debug += addDebug('BASE css | check', 'cubes', 'DEV MODE', $startmicro, 'table-default', 'runsass_base.php');

	if($buf_edit_base==1) $buf_debug += addDebug('BASE css | check', 'cubes', 'EDIT BASE MODE', $startmicro, 'table-default', 'runsass_base.php');

	
	$uri = Uri::base();
	$lesspath = JPATH_BASE.'/templates/'.$this->template. '/css';

	$sass_files = array();

	//MINIMUM BOOTSTRAP



	/*
	// Disabled in <v2 class="2.2 85
	if($buf_bs_on){

		if($buf_bs_on == '4'){

			$bsfiles = array('grid');
			foreach ($bsfiles as $key => $value) {
				//$sass_files += array($lesspath . '/bootstrap4/_'.$value.'.scss' => $uri);
				$buf_debug += addDebug('BS4 BASE | '.$value, 'cubes', '/bootstrap4/_'.$value.'.scss', $startmicro, 'table-default', 'runsass_base.php');
			}
		}
	}
	*/

	//precomposer
	//require_once $lesspath.'/scssphp/scss.inc.php';
	//require_once $lesspath.'/scssphp/src/buf_scss_init.php';

	//new
	//require_once JPATH_BASE.'/templates/'.$this->template.'/classes/autoload.php';
	//require_once $lesspath.'/scssphpAnton/loadphpscss_base.php';
	

	//new 2.2.0
	//require_once JPATH_LIBRARIES.'/jtlibs/scssphp/scss.inc.php';
	require_once JPATH_BASE.'/templates/'.$this->template.'/classes/loadphpscss_base.php';




}else{

	var_dump($base_css_exists);
	var_dump($runless);
	var_dump($buf_edit_base);
}

