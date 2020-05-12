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

class JFormFieldBufEditorButtons extends JFormField
{
    protected	$type = 'bufeditorbuttons';

    protected function getInput() {

        $app = JFactory::getApplication();
        $buf_plg_url = JURI::root(true).'/plugins/system/bufinit';
        $doc = JFactory::getDocument();
        //$doc->addScriptDeclaration("var pluginVersion = '{$this->getVersion()}';");

        $tpath = JPATH_SITE.'/templates/';
        //$doc->addScriptDeclaration("var tpath = '{$tpath}';");
        
        //$doc->addStyleSheet($buf_plg_url.'/css/bufadmin.css');
        
        $input = $app->input;
        $template_id = $input->get('id',0,'INT');


        //toolbar
        $toolbar = '';

        $toolbar .= '<div class="buf_scss_toolbar">';

         $toolbar .= '<div class="btn-group">
            <a class="btn btn-default buf_scss_save">
                <span class="buf_tb_icon"><i class="fas fa-sync fa-spin"></i></span>
                <i class="fas fa-save"></i> '.JText::_( 'JAPPLY' ).'
            </a></div>';

        $toolbar .= '<div class="buf_toolbar_msg">
                <span class="buf_tb_msg"> </span></div>';
        $toolbar .= '<div class="buf_tb_path"></div>';

            
           
            
            

        $toolbar .= '</div>';
      

        return  $toolbar;

    }

    public function getLabel(){
     //return 'BUTTONS';
    }


}
