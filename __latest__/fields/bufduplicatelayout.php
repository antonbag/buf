<?php
/**
* @package BUF Framework
* @author dibuxo http://www.dibuxo.com
* @copyright Copyright (c) 2005 - 2017 dibuxo
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

//v3
//jimport('joomla.form.formfield');

//v4
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

class JFormFieldBufDuplicateLayout extends FormField
{
    protected	$type = 'bufduplicatelayout';

    protected function getInput() {

        //$app = JFactory::getApplication();
        $app = Factory::getApplication();

        $tpath = JPATH_SITE.'/templates/';

        $input = $app->input;
        $template_id = $input->get('id',0,'INT');

        //toolbar
        $toolbar = '';


        $toolbar .= '<div class="buf_toolbar_wrapper">';

            //DUPLICATE
            $toolbar .= '<div class="buf_toolbar_file_buttons ">';

                $toolbar .= '<div class="buf_duplicate_layout">';
               

                    $toolbar .= '<a class="btn btn-default buf_duplicate_layout_a"><i class="fas fa-plus-circle"></i> '. JText::_( 'TPL_BUF_LAYOUT_NEW' ).'</a>';


                    $toolbar .= '<div class="buf_layout_new_name_wrapper buf_hidden input-prepend input-append">

                            <input type="text" name="buf_layout_new_name" id="buf_layout_name" value="" placeholder="layout name" class="input hasTooltip" data-original-title="" title="">

                            <a class="btn btn-default buf_btn_create_layout" title="Create layout" href="#"><i class="fa fa-clone"></i> '. JText::_( 'TPL_BUF_LAYOUT_CLONE' ).'</a>
                            <a class="btn btn-default buf_btn_cancel_layout" title="Cancel layout" href="#"><i class="fa fa-times"></i> '. JText::_( 'JCANCEL' ).'</a>
                    </div>';

                    $toolbar .= '<span class="buf_duplicate_layout_msg"></span>';   

                $toolbar .= '</div>';

                //ZIP
                $toolbar .= '<div class="buf_zip_layout">';

                    $toolbar .= '<a class="btn btn-default buf_zip_layout_a"><i class="fas fa-file-archive"></i> '. JText::_( 'TPL_BUF_LAYOUT_ZIP' ).'</a>';

                $toolbar .= '</div>';

            $toolbar .= '</div>';

            $toolbar .= '<div class="buf_layout_toolbar_status">';
            $toolbar .= '</div>';

        $toolbar .= '</div>';


      

        return  $toolbar;

    }

    public function getLabel(){
     return JText::_( 'TPL_BUF_LAYOUT_TOOLS');
    }


}
