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

class JFormFieldBufbsexists extends JFormField
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
        $name=JText::_( 'TPL_BUF_COMPILE_BS' );
      }elseif($file == 'fa'){
        $cssfile = $cachepath. '/buf_fa.css';
        $cleanclass='do_clean_fa';
        $id='compile_fa_sass';
        $name=JText::_( 'TPL_BUF_COMPILE_FA' );
      }else{
        return;
      }

      $text = '<div class="buf_toolbar_wrapper '.$cleanclass.'">';

        if(file_exists($cssfile) != true){

            $text .= '<div class="buf_toolbar_file_buttons ">';

              $text .=  '<div class="btn-wrapper" id="'.$id.'"><button class="btn btn-small"><span class="icon-play"></span>'.$name.'</button></div>';
              $text .= '</div>';



              $text .= '<div class="buf_toolbar_file_info">';

              $text .= '<div class="badge badge-warning">'.JText::_( 'TPL_BUF_NOT_EXIST' ).'</div>';

             
            $text .= '</div>';
        }else{

          $text .= '<div class="buf_toolbar_file_buttons ">';

            $text .=  '<div class="btn-wrapper" id="'.$id.'"><button class="btn btn-small"><span class="icon-play"></span>'.$name.'</button></div>';
            $text .= '<div class="btn_buf_clean btn"><a href="#" class="'.$cleanclass.'">'. JText::_("TPL_BUF_CLEAN_FILE") .'</a></div>';

          $text .= '</div>';



          $text .= '<div class="buf_toolbar_file_info">';
          $text .= '<div class=""><small>'.$cssfile. '</small></div><div class="badge badge-success">'.JText::_( 'TPL_BUF_COMPILED' ).'</div>';

            $text .=  "
                    <div>". JText::_( 'TPL_BUF_COMPILATION_TIME' ) . date ("d-m-y H:i:s", filemtime($cssfile)). "</div>
                    <div>". JText::_( 'TPL_BUF_COMPILATION_SIZE' ) .self::human_filesize($cssfile)."</div>";
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

  private function human_filesize($file, $decimals = 2) {

    $bytes = (int)  filesize($file);
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
  }



}
