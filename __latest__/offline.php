<?php

defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;


$twofactormethods   = AuthenticationHelper::getTwoFactorMethods();
$app                = Factory::getApplication();
$doc                = Factory::getDocument();

//echo '<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>';


$doc->addScript('//maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js',array(),array('async'=>'async'));
$doc->addScript('//kit.fontawesome.com/567fc826d3.js',array(),array('async'=>'async', "crossorigin"=>"anonymous"));
JHtml::_('stylesheet', '//maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css', array('version' => 'auto'));





/*
// Output as HTML5
$this->setHtml5(true);

// Add html5 shiv
JHtml::_('script', 'jui/html5.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

// Styles
JHtml::_('stylesheet', 'templates/system/css/offline.css', array('version' => 'auto'));

if ($this->direction === 'rtl')
{
  JHtml::_('stylesheet', 'templates/system/css/offline_rtl.css', array('version' => 'auto'));
}

JHtml::_('stylesheet', 'templates/system/css/general.css', array('version' => 'auto'));
*/
// Add JavaScript Frameworks
//JHtml::_('bootstrap.framework');


?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <jdoc:include type="head" />



<style>

  body{
      background-color: #e0e0e0;
  }
  div.card_wrapper{
    display: flex; justify-content: center; align-items: center; width: 100%; height: 100vh;
   
  }
  div.card{
    max-width: 25rem; width: 100%; 
    box-shadow: 0 0 50px rgba(51, 51, 51, 0.2);
  );
    border:none;
    border-radius: 25px;
  }
  div#system-message{
    position: fixed;
    top: 0;
    width: 100%;
  }

</style>


<script>

   function enableBtn(){
     document.getElementById("button1").disabled = false;
   }

</script>

</head>
<body>
  <jdoc:include type="message" />


<div class="card_wrapper">

  <div class="card mb-3">
    

    <?php if ($app->get('offline_image') && file_exists($app->get('offline_image'))) : ?>
      <img class="card-img-top" src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>" />
     <?php endif; ?>


      <article class="card-body">

        <h1 class="card-title mb-4 mt-1">
          <?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>
        </h1>
        <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) !== '') : ?>
          <p>
            <?php echo $app->get('offline_message'); ?>
          </p>
        <?php elseif ($app->get('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) !== '') : ?>
          <p>
            <?php echo JText::_('JOFFLINE_MESSAGE'); ?>
          </p>
        <?php endif; ?>

        <hr>

        <form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login" class="form-validate" role="form">

          <div class="input-group mb-3">

            <div class="input-group-prepend">
              <span class="input-group-text" id="username"><i class="fas fa-user fa-fw"></i> </span>
            </div>
            <input name="username" id="username" type="text" class="form-control" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" alt="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" autocomplete="off" autocapitalize="none" />
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="password"><i class="fas fa-unlock-alt fa-fw"></i> </span>
            </div>
            <input type="password" name="password" class="form-control" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" alt="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" id="passwd" />
          </div>

          <?php if (count($twofactormethods) > 1) : ?>

            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="secret" ><i class="fas fa-key fa-fw"></i> </span>
              </div>
              <input type="text" name="secretkey" class="form-control" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" alt="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" id="secretkey" />
            </div>

          <?php endif; ?>


          <?php

      $reCaptchaName = 'recaptcha'; // the name of the captcha plugin - retrieved from the custom component's parameters

      JPluginHelper::importPlugin('captcha', $reCaptchaName);
      $id = 'jform_captcha';

      $dispatcher = JEventDispatcher::getInstance();
      $dispatcher->trigger('onInit', $id);
      $output = $dispatcher->trigger('onDisplay', array($reCaptchaName, $id, "required"));
      echo isset($output[0])? $output[0]:'';

      ?>

          <div class="form-group button_submit d-flex justify-content-center">
              <input type="submit" name="Submit" class="button login btn btn-primary" value="<?php echo JText::_('JLOGIN'); ?>" />
          </div>




          <input type="hidden" name="option" value="com_users" />
          <input type="hidden" name="task" value="user.login" />
          <input type="hidden" name="return" value="<?php echo base64_encode(JUri::base()); ?>" />
          <?php echo JHtml::_('form.token'); ?>

        </form>




      </article>
  </div> <!-- card.// -->

</div>




</body>
</html>
