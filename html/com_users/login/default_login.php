<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use JTFramework\JTfunc;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;


if (is_file(JPATH_PLUGINS . '/system/jtframework/autoload.php')){
    require_once JPATH_PLUGINS . '/system/jtframework/autoload.php';
}else{
    Factory::getApplication()->enqueueMessage(Text::_('JT_FW_NOT_FOUND'), 'error');
} 

if(JTfunc::isJ4()){
	//get default for J4
	include_once('components/com_users/tmpl/login/default_login.php');
	return;
}





JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

?>
<div class="login<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>
	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
		<div class="login-description">
	<?php endif; ?>
	<?php if ($this->params->get('logindescription_show') == 1) : ?>
		<?php echo $this->params->get('login_description'); ?>
	<?php endif; ?>
	<?php if ($this->params->get('login_image') != '') : ?>
		<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JText::_('COM_USERS_LOGIN_IMAGE_ALT'); ?>" />
	<?php endif; ?>
	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
		</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate form-horizontal well">
		<fieldset>
			<?php echo $this->form->renderFieldset('credentials'); ?>
			<?php if ($this->tfa) : ?>
				<?php echo $this->form->renderField('secretkey'); ?>
			<?php endif; ?>
			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
				<div class="control-group">
					<div class="control-label">
						<label for="remember">
							<?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME'); ?>
						</label>
					</div>
					<div class="controls">
						<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes" />
					</div>
				</div>
			<?php endif; ?>
			
						<div class="control-group">
				<div class="controls">
			          <?php

      $reCaptchaName = 'recaptcha'; // the name of the captcha plugin - retrieved from the custom component's parameters

      JPluginHelper::importPlugin('captcha', $reCaptchaName);
      $id = 'jform_captcha';

      $dispatcher = JEventDispatcher::getInstance();
      $dispatcher->trigger('onInit', $id);
      $output = $dispatcher->trigger('onDisplay', array($reCaptchaName, $id, "required"));
      echo isset($output[0])? $output[0]:'';

      ?>
				</div>
			</div>
			
			
			
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary">
						<?php echo JText::_('JLOGIN'); ?>
					</button>
				</div>
			</div>
			<?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
			<input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
<div>
	<ul class="nav nav-tabs nav-stacked">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
				<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
				<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?>
			</a>
		</li>
		<?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
		<?php if ($usersConfig->get('allowUserRegistration')) : ?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
					<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?>
				</a>
			</li>
		<?php endif; ?>
	</ul>
</div>
