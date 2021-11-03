<?php
/**
* @package BUF Framework
* @author jtotal https://jtotal.org
* @copyright Copyright (c) 2005 - 2021 jtotal
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/  


//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

//J3.8
//jimport('joomla.form.formfield');
//class JFormFieldBufReloadSass extends JFormField

//J3.9
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Version;

class JFormFieldBufJversion extends FormField
{
    protected	$type = 'bufjversion';

    protected function getInput() {


        $jversion_api = new Version();
        $jversion = substr($jversion_api->getShortVersion(), 0, 1);

      

        $jversion_hidden = '<input
            type="hidden"
            name="'.$this->name.'"
            id="'.$this->id.'" 
            value="'.$jversion.'" 
        >';

        return $jversion_hidden;
    }


   

    public function getLabel(){

    }



}
