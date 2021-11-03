<?php
/**
* @package BUF Framework
* @author jtotal https://jtotal.org
* @copyright Copyright (c) 2005 - 2021 jtotal
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  

//no direct access
defined('_JEXEC') or die;


//J3.9
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
class JFormFieldBufReloadSass extends FormField
{
    protected	$type = 'bufreloadsass';

    protected function getInput() {

      /** @var CMSApplication $app */
      $app = Factory::getApplication();

      /** @var Session $session */
      $session = $app->getSession();

      $reload_sass = $session->get('buf_reload_sass');
      $reload_bs_sass = $session->get('buf_reload_bs_sass');
      $reload_fa_sass = $session->get('buf_reload_fa_sass');

      $text = '<input type="hidden" id="reloadsass" value="'.$reload_sass.'"/>';
      $text .= '<input type="hidden" id="reloadbs" value="'.$reload_bs_sass.'"/>';
      $text .= '<input type="hidden" id="reloadfa" value="'.$reload_fa_sass.'"/>';

      return $text;

    }

    public function getLabel(){
      //return 'reloadSass';
    }



}
