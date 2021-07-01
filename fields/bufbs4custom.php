<?php
/**
* @package BUF Framework
* @author jtotal https://jtotal.org
* @copyright Copyright (c) 2005 - 2021 jtotal
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

//jimport('joomla.form.formfield');
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;


class JFormFieldBufbs4custom extends FormField
{
    protected	$type = 'bufbs4custom';

    protected function getInput() {

      //check plugins
      $bs4_custom_button = '<div class="bufbs4custom_messages">';
      $bs4_custom_button .= '</div>';
      
        $bs4_custom_button .= '<div class="bs4_custom_btn">';
        $bs4_custom_button .= '<a class="btn btn-default">';
        $bs4_custom_button .= '<i class="fa fa-play"></i> ';
        $bs4_custom_button .= '<i class="fa fa-cog fa-spin buf_hide"></i> ';
        $bs4_custom_button .= Text::_('TPL_BUF_BS4_CUSTOM_PROCESS');
        $bs4_custom_button .= '</a></div>';

      return $bs4_custom_button;

    }

    public function getLabel(){
      //return JText::_('TPL_BUF_CURRENT_FAVICON');
    }



}
