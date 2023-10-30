<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace Jtotal\BUF\Site\Field;

//jimport('joomla.form.formfield');
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

//no direct accees
defined('_JEXEC') or die('resticted aceess');

class BufEditorButtonsField extends FormField
{
    protected $type = 'BufEditorButtons';

    protected function getInput()
    {

        $app = Factory::getApplication();
        $buf_plg_url = URI::root(true) . '/plugins/system/bufinit';
        $doc = Factory::getDocument();
        //$doc->addScriptDeclaration("var pluginVersion = '{$this->getVersion()}';");

        $tpath = JPATH_SITE . '/templates/';

        $input = $app->input;
        $template_id = $input->get('id', 0, 'INT');

        //toolbar
        $toolbar = '';

        $toolbar .= '<div class="buf_scss_toolbar">';

        $toolbar .= '<div class="btn-group">
            <a class="btn btn-default buf_scss_save">
                <span class="buf_tb_icon"><i class="fas fa-sync fa-spin"></i></span>
                <i class="fas fa-save"></i> ' . Text::_('JAPPLY') . '
            </a></div>';

        $toolbar .= '<div class="buf_toolbar_msg">
                <span class="buf_tb_msg"> </span></div>';
        $toolbar .= '<div class="buf_tb_path"></div>';

        $toolbar .= '</div>';

        return $toolbar;
    }
}
