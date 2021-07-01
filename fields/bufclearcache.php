<?php
/**
* @package BUF Framework
* @author dibuxo http://www.dibuxo.com
* @copyright Copyright (c) 2005 - 2019 dibuxo
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

//jimport('joomla.form.formfield');
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class JFormFieldBufclearcache extends FormField
{
    protected	$type = 'clearcache';

    protected function getInput() {

      $cacheb = '<div class="btn-wrapper" id="toolbar-buf_empy_cache"><button class="btn btn-small buf_clearcache">
        <span class="buf_clearcache_icon"><i class="fas fa-trash-alt"></i></span>
        <span class="buf_clear_cache_status"></span>
        <span class="buf_clearcache_text">
        '.Text::_( 'TPL_BUF_CLEAR_CACHE' ).'</span>
        
      </button></div>';

      return $cacheb;

    }

    public function getLabel(){
      //return JText::_('TPL_BUF_CURRENT_FAVICON');
    }


}
