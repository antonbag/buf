<?php defined('_JEXEC') or die;

function modChrome_bufbs4($module, &$params, &$attribs) {
	if ($module->content) {

		if($params->get('bootstrap_size', false)){
			$bs4 = 'col-sm-'.$params->get('bootstrap_size').' ';
		}else{
			$bs4 = '';
		}

		$buf_bs4 = '';

		$buf_bs4 .= "<div class=\"" . $bs4. htmlspecialchars($params->get('moduleclass_sfx')) . "\">";
		if ($module->showtitle) {
			$buf_bs4 .= "<h3>" . $module->title . "</h3>";
		}
		$buf_bs4 .= $module->content;
		$buf_bs4 .= "</div>";


		echo $buf_bs4;

	}
}

?>