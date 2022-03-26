<?php defined( '_JEXEC' ) or die; 

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;

include_once JPATH_THEMES.'/'.$this->template.'/logics/logic_base.php';

// variables
$doc = Factory::getDocument(); 
$tpath = $this->baseurl.'/templates/'.$this->template;

// generator tag
$this->setGenerator(null);


//LAYOUT COMPONENT
if(file_exists($tpath_abs.'/layouts/'.$buf_layout.'/component.php')){
    //var_dump($tpath_abs.'/layouts/'.$buf_layout);
    include_once JPATH_THEMES.'/'.$this->template.'/layouts/'.$buf_layout.'/component.php';
    return;
}


//load sheets and scripts
if(File::exists($cachepath.'print.css')) $doc->addStyleSheet($cache_tpath.'/css/print.css?v=1'); 

?><!doctype html>

<html lang="<?php echo $this->language; ?>">

<head>
  <jdoc:include type="head" />
  	<style id="buf_style_base">
		<?php 
			//avoid error on load
			echo file_get_contents('cache/buf/base.css');
			//TEMPLATE BASE CSS
		?>
	</style>
</head>

<body class="<?php echo $browserType.' '.(($menu->getActive() == $menu->getDefault()) ? ('front') : ('site')).' '.$active->alias.' '.$pageclass.' '.$docalias; ?>" role="document">

    <div class="contenidos wrapper row">
        <article class="contenido buf_component">
            <jdoc:include type="message" />
            <jdoc:include type="component" />
        </article>
    </div>
        
	<?php //if ($_GET['print'] == '1') echo '<script type="text/javascript">window.print();</script>'; ?>

</body>

<?Php
    $app  = Factory::getApplication();
    $templateparams	= $app->getTemplate(true)->params;

	//LOGIC
	if(!$templateparams->get('buf_edit_base', 0)){
		if(!$check_jtfw || $check_jtfw=='1.0.0' || !$check_jtlibs || $check_jtlibs=='1.0.0'){
				
		}else{
			include_once JPATH_THEMES.'/'.$this->template.'/logics/logic_j'.$jversion.'.php';
		}
	}
?>

</html>

