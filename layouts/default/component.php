
<?Php
use Joomla\CMS\Factory;
//load sheets and scripts
$doc->addStyleSheet($tpath.'/css/print.css?v=1'); 

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
	  include_once JPATH_THEMES.'/'.$this->template.'/logics/logic.php';
	}
?>

</html>
