<?php
/**
* @package BUF Framework
* @author dibuxo http://www.dibuxo.com
* @copyright Copyright (c) 2005 - 2017 dibuxo
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

jimport('joomla.form.formfield');

class JFormFieldBufReloadSass extends JFormField
{
    protected	$type = 'bufreloadsass';

    protected function getInput() {

      $session = JFactory::getSession();
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
