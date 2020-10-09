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
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;

class JFormFieldBuffavicons extends JFormField
{
    protected	$type = 'buffavicons';
    protected function getInput() {

      $jinput 	= Factory::getApplication()->input;

      $templateid = $jinput->get('id');
      $params = new Registry($this->form->getData("data")->get('params'));


      $upload_svg = "


        function buf_favicon_svg(detelesvg='false'){


          var data =  new FormData();

          if(detelesvg=='false'){
            data.append( 'upload_file_svg', jQuery('#jform_params_buf_favicon_svg')[0].files[0]);
          }
                  
          data.append( 'token', '".JSession::getFormToken()."');
          data.append( 'templateid', '".$templateid."');
          data.append( 'buf_layout', '".$params->get('buf_layout','default')."');
             
          var getUrl = 'index.php?option=com_ajax&group=ajax&plugin=bufajax&format=json&action=do_get_favicon_svg&raw=true&detelesvg='+detelesvg;
        
          var jqxhr = jQuery.ajax({
        
            type: 'POST',
            url: getUrl,
            cache: false,
            dataType: 'json',
            data:data,
            processData: false,
            contentType: false,
            jsonp: false
        
          }).done(function(e) {
            //mapbutton();
            console.log(e.data);
            if(e.data == 'deleted'){
              jQuery('.buffavicons_thumbs_svg_img img').hide();

            }else{
              jQuery('.buffavicons_thumbs_svg_img img').attr('src',e.data).show();
            }
                    
          })
          .fail(function(e) {
        
            console.log('ERROR');
            console.log(e);
        
          });
        
        }
        
       
        ";



      $doc = Factory::getDocument();
      $doc->addCustomTag('<script type="text/javascript">'.$upload_svg.'</script>');

    
      //check plugins
      $favicons = '';

      if(file_exists (JPATH_SITE.'/templates/buf/layouts/'.$params->get('buf_layout','default').'/icons/favicon-32x32.png')){

        $favicons .= '<div class="buffavicons_thumbs_wrapper">';
        
          $favicons .= '<div class="buffavicons_thumbs buffavicons_thumbs_svg">';
            $favicons .= '<strong>svg</strong><a class="btn btn_svg_destroy" onclick="buf_favicon_svg(\'true\')" style="float:right;">x</a>';
            $favicons .= '<div class="buffavicons_thumbs_svg_img">';
              if(file_exists (JPATH_SITE.'/templates/buf/layouts/'.$params->get('buf_layout','default').'/icons/svgfavicon.svg')){
                $favicons .= '<img src="../templates/buf/layouts/'.$params->get('buf_layout','default').'/icons/svgfavicon.svg" width="100"/>';
              }else{
                $favicons .= '<img src="../templates/buf/images/icons/svgfavicon.svg" width="64"/>';
                //$favicons .= '<br><small>not present</small>';
              }
            $favicons .= '</div>';
          $favicons .= '</div>';
  

          $favicons .= '<div class="buffavicons_thumbs">';
            $favicons .= '<img src="../templates/buf/layouts/'.$params->get('buf_layout','default').'/icons/favicon-96x96.png" title="96x96"/>';
          $favicons .= '</div>';

          $favicons .= '<div class="buffavicons_thumbs">';
            $favicons .= '<img src="../templates/buf/layouts/'.$params->get('buf_layout','default').'/icons/apple-icon-57x57.png" title="57x57"/>';
          $favicons .= '</div>';


          $favicons .= '<div class="buffavicons_thumbs">';
            $favicons .= '<img src="../templates/buf/layouts/'.$params->get('buf_layout','default').'/icons/favicon-16x16.png" title="16x16"/>';
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
