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

class JFormFieldBuffavicons extends JFormField
{
    protected	$type = 'buffavicons';

    protected function getInput() {

      //check plugins
      $favicons = '';


      if(file_exists (JPATH_SITE.'/templates/buf/images/icons/favicon-32x32.png')){
        $favicons .= '<div class="buffavicons_thumbs_wrapper">';
          $favicons .= '<div class="buffavicons_thumbs">';
         
            $favicons .= '<img src="../templates/buf/images/icons/favicon-96x96.png" title="96x96"/>';

          $favicons .= '</div>';

          $favicons .= '<div class="buffavicons_thumbs">';
         
            $favicons .= '<img src="../templates/buf/images/icons/apple-icon-57x57.png" title="57x57"/>';

          $favicons .= '</div>';


          $favicons .= '<div class="buffavicons_thumbs">';
         
            $favicons .= '<img src="../templates/buf/images/icons/favicon-16x16.png" title="16x16"/>';

          $favicons .= '</div>';

         
        $favicons .= '</div>';

      }else{
        $favicons .= '<div class="buffavicons_thumbs">';
        $favicons .= JText::_('TPL_BUF_FAVICON_NOT_CREATED');
        $favicons .= '</div>';
      }
      $favicons .= '<div class="buffavicons_messages">';
      $favicons .= '</div>';

      return $favicons;

    }

    public function getLabel(){
      //return JText::_('TPL_BUF_CURRENT_FAVICON');
    }


}
