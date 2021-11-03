<?php
/**
* @package BUF Framework
* @author jtotal https://jtotal.org
* @copyright Copyright (c) 2005 - 2021 jtotal
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

//jimport('joomla.form.formfield');



class JFormFieldBufbsexists extends FormField
{
    protected	$type = 'bufbsexists';


    protected function getInput() {


      $params = $this->form->getValue('params');
      $layout = $params->buf_layout;
      if($layout == '') $layout = 'default';

      $cachepath = JPATH_SITE.'/cache/buf_'.$layout;

      $file = strtolower($this->element['file']);

      if($file == 'bs'){
        $cssfile = $cachepath. '/buf_bs.css';
        $cleanclass='do_clean_bs';
        $id='compile_bs_sass';
        $name=Text::_( 'TPL_BUF_COMPILE_BS' );
      }elseif($file == 'fa'){
        $cssfile = $cachepath. '/buf_fa.css';
        $cleanclass='do_clean_fa';
        $id='compile_fa_sass';
        $name=Text::_( 'TPL_BUF_COMPILE_FA' );
      }else{
        return;
      }

      $text = '<div class="buf_toolbar_wrapper d-flex flex-wrap '.$cleanclass.'">';

        if(file_exists($cssfile) != true){

            $text .= '<div class="buf_toolbar_file_buttons ">';

              $text .=  '<div class="btn-wrapper" id="'.$id.'"><button class="btn btn-small bg-primary text-light"><i class="fas fa-robot"></i> '.$name.'</button></div>';
              $text .= '</div>';



              $text .= '<div class="buf_toolbar_file_info d-flex flex-wrap">';

              $text .= '<div class="badge badge-warning bg-danger">'.Text::_( 'TPL_BUF_NOT_EXIST' ).'</div>';

             
            $text .= '</div>';
        }else{

          $text .= '<div class="buf_toolbar_file_buttons ">';

            $text .=  '<div class="btn-wrapper" id="'.$id.'"><button class="btn btn-small"><span class="icon-play"></span>'.$name.'</button></div>';
            $text .= '<div class="btn_buf_clean btn"><a href="#" class="'.$cleanclass.'">'. Text::_("TPL_BUF_CLEAN_FILE") .'</a></div>';

          $text .= '</div>';



          $text .= '<div class="buf_toolbar_file_info d-flex flex-wrap">';
          $text .= '<div class=""><small>'.$cssfile. '</small></div><div class="badge badge-success">'.Text::_( 'TPL_BUF_COMPILED' ).'</div>';

            $text .=  "
                    <div>". Text::_( 'TPL_BUF_COMPILATION_TIME' ) . date ("d-m-y H:i:s", filemtime($cssfile)). "</div>
                    <div>". Text::_( 'TPL_BUF_COMPILATION_SIZE' ) .self::human_filesize($cssfile)."</div>";
          $text .= '</div>';
        } 





      $text .= '</div>';


      return $text;

    }

    public function getLabel(){
      $file = strtolower($this->element['file']);

      if($file == 'bs'){
        
        //$text = "Bootstrap css";

      }elseif($file == 'fa'){
        
        //$text =  "Fontawesome css";

      }

       //return $text;
    }

  private static function human_filesize($file, $decimals = 2) {

    $bytes = (int)  filesize($file);
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
  }



}
