<?php
/**
* @package BUF Framework
* @author jtotal https://jtotal.org
* @copyright Copyright (c) 2005 - 2021 jtotal
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/   

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

class JFormFieldBuflibexists extends FormField
{
    protected $type = 'buflibexists';

    protected function getInput() {

        $app = Factory::getApplication();

        $buf_path = URI::root(true).'/templates/buf/backend';
        $doc = Factory::getDocument();
        $doc->addScriptDeclaration("var bufpluginbufajax = '{$this->getVersion()}';");

        $tpath_real = realpath(JPATH_SITE.'/templates/');
        $tpath= str_replace('\\', '\\\\', $tpath_real);
        $tpath .= '/';
        $doc->addScriptDeclaration("var tpath = '{$tpath}';");
        

        $params = $this->form->getValue('params');
        $layout = $params->buf_layout;
        if($layout == '') $layout = 'default';
       
        $doc->addScriptDeclaration("var buf_layout = '{$layout}';");
        
        
        //fa 4
        //$doc->addStyleSheet(JURI::root(true).'/templates/buf/css/font-awesome/font-awesome.min.css');
        //fa 5

        $jversion_api = new Version();
        //$jversion = preg_replace('#^([0-9\.]+)(|.*)$#', '$1', $jversion_api->getShortVersion());
        $jversion = substr($jversion_api->getShortVersion(), 0, 1);

        if($jversion <= "3"){
          /******* JOOMLA 3 ******/
          $doc->addStyleSheet($buf_path.'/css/bufadmin.css');
          //$doc->addScript('https://use.fontawesome.com/releases/v5.7.1/js/all.js', array(), array("defer"=>"defer","integrity"=>'sha384-eVEQC9zshBn0rFj4+TU78eNA19HMNigMviK/PU/FFjLXqa/GKPgX58rvt5Z8PLs7', "crossorigin"=>'anonymous'));
          $doc->addScriptDeclaration("var jversion = '3';");

        }else{
          /******* JOOMLA 4 ******/
          $doc->addStyleSheet($buf_path.'/css/bufadmin4.css');
          //$doc->addStyleSheet('//use.fontawesome.com/releases/v5.7.1/css/all.css', array(), array("integrity"=>'sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr', "crossorigin"=>'anonymous'));
          $doc->addScriptDeclaration("var jversion = '4';");
        }
      
               
        $doc->addScript($buf_path.'/js/bufadmin.js', array(), array("sync"=>'sync'));

        $input = $app->input;
        $template_id = $input->get('id',0,'INT');


        //init variables
        $doc->addScriptDeclaration("var templateid = '{$template_id}';");

        $template_init = '<img class="buf_minilogo_bar" src="../templates/buf/images/buf_logos/logo_buf_64.png"/>';

        //CHECK FILES

        if(is_dir( JPATH_SITE.'/plugins/ajax/bufajax')){
           
          //plugin exists
          if(PluginHelper::isEnabled('ajax', 'bufajax')){
            $template_init .= '<span class="badge badge-success">'.$this->getVersion().'</span>';
          }else{
            $template_init .= '<span class="alert alert-warning buf_ajax_not_enabled"><i class="fas fa-exclamation-triangle"></i> PLUGIN NOT ENABLED. Buf ajax plugin is present but not enabled. ';
            //$template_init .= '<span class="badge badge-danger">PLUGIN NOT INSTALLED</span>';
            $template_init .= '<a href="index.php?option=com_plugins&view=plugins&filter[search]=buf" class="btn btn-default">Enable buf ajax plugin</a> </span>';
          }

        }else{
          $template_init .= '<span class="alert alert-error buf_ajax_not_installed"><i class="fas fa-exclamation-triangle"></i> PLUGIN NOT INSTALLED. Buf ajax plugin is required. ';
          //$template_init .= '<span class="badge badge-danger">PLUGIN NOT INSTALLED</span>';
          $template_init .= '<a href="https://jtotal.org/joomla/templates/buf-template" target="_blank" class="btn btn-default">Download buf ajax plugin</a> </span>';
        }

        //check plugins   
        return $template_init;
    }

    public function getLabel(){

      $content = '<img src="../templates/buf/images/buf_logos/logo_buf_init.png"/> Buf ajax plugin:';

      return $content;
    }

    private function getVersion() {
      $db = Factory::getDBO();
      $query = $db->getQuery(true);

      $element = 'bufajax';
          $folder = 'ajax';
        
        /*
            switch ($plugin) {
              case 'bufinit':
                $element = 'bufinit';
                $folder = 'system';
                break;
              case 'bufajax':
                $element = 'bufajax';
                $folder = 'ajax';
                break;
              default:
                $element = 'bufajax';
                $folder = 'ajax';
                break;
            }
        */

      $query
          ->select(array('*'))
          ->from($db->quoteName('#__extensions'))
          ->where($db->quoteName('type').' = '.$db->quote('plugin'))
          ->where($db->quoteName('element').' = '.$db->quote($element))
          ->where($db->quoteName('folder').' = '.$db->quote($folder));
      $db->setQuery($query);
      $result = $db->loadObject();


      $manifest_cache = json_decode($result->manifest_cache);

      //var_dump($result->enabled);
      if (isset($manifest_cache->version)) {
        return $manifest_cache->version;
      }
      return;
    }

}
