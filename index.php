<!DOCTYPE html>
<?php defined( '_JEXEC' ) or die; 
include_once JPATH_THEMES.'/'.$this->template.'/logics/logic_base.php';
?>

<html lang="<?php echo $this->language; ?>">
<head>

<?php 
	echo '<script type="text/javascript">';
		echo 'var php_buf_params = \''.$params_to_js.'\';';
	echo '</script>';
?>

	<jdoc:include type="head" />

	<style id="buf_style_base">
		<?php 
			//avoid error on load
			echo file_get_contents('cache/buf_'.$buf_layout.'/base.css');
			//TEMPLATE BASE CSS
			$buf_debug += addDebug('BASE CSS', 'thumbs-up', 'LOADED', $startmicro, 'table-success', 'index.php');
		?>
	</style>

	<link rel="shortcut icon" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/favicon.ico" />

	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,shrink-to-fit=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/apple-icon-180x180.png">

	<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/favicon-16x16.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/favicon-96x96.png">

	<link rel="manifest" href="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/manifest.json">
	
	<meta name="msapplication-TileColor" content="<?php echo $templateparams->get('buf_mscolor','#57616d'); ?>">
	<meta name="msapplication-TileImage" content="<?php echo $tpath; ?>/images/icons/ms-icon-144x144.png">
	<meta name="theme-color" content="<?php echo $templateparams->get('buf_mscolor','#57616d'); ?>">
	<meta name="msapplication-config" content="<?php echo $tpath.'/layouts/'.$buf_layout;?>/icons/browserconfig.xml" />

	<?php
		if(file_exists(JPATH_ROOT . '/templates/buf/layouts/'.$buf_layout.'/icons/svgfavicon.svg')) {
			echo '<link rel="mask-icon" href="'.$tpath . '/layouts/'.$buf_layout.'/icons/svgfavicon.svg" color="'.$templateparams->get('buf_mscolor','#57616d').'">';
		}
	?>


	<meta http-equiv="Cache-control" content="public">

	<!-- Le HTML5 shim and media query for IE8 support -->
	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<script type="text/javascript" src="<?php echo $tpath; ?>/js/respond.min.js"></script>
	<![endif]-->


	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<!-- <link href="templates/buf/libs/bootstrap/dist/css/bootstrap.css" rel="stylesheet"> -->


</head>



<body class="<?php echo implode(" ", $bodyclass); ?> buf_offcanvas_hidden" role="document">

  
	<!-- OFFCANVAS BUTTON-->
	<?php if ($buf_offcanvas) : ?>
			
			<?php $buf_debug += addDebug('offcanvas_button', 'bars', 'active and module present', $startmicro, 'table-success', 'index.php');?>

			<?php if ($buf_topbar_on){
				echo  '<div id="buf_topbar" class="'.$buf_topbar_classes.'">';
				echo $buf_topbar_logo;
			}else{
				echo  '<div id="buf_topbar" class="buf_topbar_off">';
			}
			?>
			

		        <button id="bufoc_button" class="hamburger hamburger--collapse hamburger--<?php echo $buf_oc_button_style.$buf_reverse;?> oc_button_vpos_<?php echo $buf_oc_button_vpos;?> oc_button_hpos_<?php echo $buf_oc_button_hpos;?>
		        " type="button">
				  <span class="hamburger-box">
				    <span class="hamburger-inner"></span>
				  </span>
				</button>


			
			</div>
			


	<?php endif; ?>



	<!-- OFFCANVAS -->
	<?php if ($buf_offcanvas) : ?>
		
			<?php $buf_debug += addDebug('offcanvas', 'bars', 'active', $startmicro, 'table-success', 'index.php');?>
			
			<div id="buf_offcanvas" class="buf_offcanvas <?php echo $buf_offcanvas_style.' '.$buf_offcanvas_position;?>">
				
				<?php if ($buf_topbar_oc_on){
						echo  '<div id="buf_topbar_oc" class="'.$buf_topbar_oc_classes.'">';
						echo $buf_topbar_oc_logo;
					}else{
						echo  '<div id="buf_topbar_oc" class="buf_topbar_off">';
					}
					echo '</div>';
				?>

		        <div class="offcanvas-inner <?php echo 'buf_topbar_oc_'.$buf_topbar_oc_on;?>">

		            <jdoc:include type="modules" name="offcanvas"/>

					<?php

						/****************************************************/
						/******** CUSTOM MODULES IN CANVAS  *****************/
						if($buf_offcanvas_positions !=''){

							echo '<div class="offcanvas_module_in">';

								//$buf_offcanvas_positions = 'menu_portada';
								$buf_offcanvas_positions_array = explode(',',$buf_offcanvas_positions);
													
								jimport('joomla.application.module.helper');

								foreach ($buf_offcanvas_positions_array as $b_off) {
									$modules = JModuleHelper::getModules($b_off);
									
									foreach ($modules as $module) {
										echo JModuleHelper::renderModule($module,array('buf_offcanvas'=>true));
									}
								}
							echo '</div>';
						}
						
						/***************************************/
						/*****************  DEBUG in CANVAS  *****************/
						/***************************************/
						/*
						if($templateparams->get('buf_debug', 0)){
							$buf_debug += addDebug('Page', 'flag-checkered', 'loaded', $startmicro);
							include_once JPATH_THEMES.'/'.$this->template.'/logics/debug.php';
						}*/
					?>

		        </div>
		    </div>

	<?php endif; ?>




<?php /***************************************/
/*************SUPERWRAPPER*****************/
/****************************************/ ?>
	<div id="superwrapper" class="superwrapper <?php echo $buf_offcanvas_style.' '.$buf_offcanvas_position;?>">
		<div class="<?php echo $container;?>">
			<?php
				include_once JPATH_THEMES.'/'.$this->template.'/layouts/'.$buf_layout.'/layout.php'; 
			?>
		</div><!-- fin container -->

		<!-- SUBFOOTER -->
		<div id="subfooter">
            <div class="<?php echo $container;?>">
                <?php if ($this->countModules('subfooter')) : ?>
                    <jdoc:include type="modules" name="subfooter"/>
                <?php endif; ?>
             </div>
		</div>

		
	</div><!-- fin superwrapper -->

	<?php if($templateparams->get('buf_show_credit', "1")) : ?>
	
		<div class="buf_credits">
			<div class="buf_credit_logo">
				<a href="https://jtotal.org/buf-template" target="_blank" rel="nofollow" rel="noopener">
					<picture class="img-responsive">
						<source type="image" srcset="templates/buf/images/buf_logos/buf_logo_footer.svg">
						<img class="img-responsive" src="templates/buf/images/buf_logos/buf_logo_footer.png" width="32" height="33" alt="buf template">
					</picture>
				</a>
			</div>
			<div class="buf_credit_text">
				<p> for Joomla by <a href="https://jtotal.org/buf-template" target="_blank" rel="nofollow" rel="noopener">jtotal</a></p>
				<p> © jtotal <?php echo date("Y"); ?></p>
			</div>
		</div>
	<?php endif; ?>



	<!-- BG -->
	<?php if ($buf_bg_img) : ?>
		<div class="bg">
			<img src="<?php echo $buf_bg_img ?>" alt="background-image"/>
		</div>
	<?php endif; ?>

<?php

	//LOGIC
	flush();
	
	if(!$edit_base_input){
		if(!$templateparams->get('buf_edit_base', 0)){
		  include_once JPATH_THEMES.'/'.$this->template.'/logics/logic.php';
		}
	}else{

		echo '<div class="buf_dev_mode" style="position:fixed; bottom:0px; left:0px;z-index:100; width: 100%;padding: 4px 10px; display: flex;align-items: center;
    				justify-content: space-between;">
    				<a></a>
				  	<a class="buf_dev_mode_edit_base" href="index.php"><i class="fas fa-box-open"></i> Exit base mode</a>
			  			
		</div>';
	}

?>


	<jdoc:include type="modules" name="debug" style="none" />
</body>



</html>





