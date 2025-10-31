<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright   Copyright (C) 2025 Jtotal. All rights reserved.
 * @license     GNU General Public License version 2 or later; see https://jtotal.org/LICENSE.txt
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

            $toolbar .= '<div class="btn-group">
                <button type="button" class="btn btn-outline-success buf_scss_create" disabled>
                    <span class="buf_create_icon"><i class="fas fa-sync fa-spin"></i></span>
                    <i class="fas fa-plus"></i> ' . Text::_('JACTION_CREATE') . '
                </button>
            </div>';

            $toolbar .= '<div class="btn-group">
                <button type="button" class="btn btn-outline-danger buf_scss_delete" disabled>
                    <span class="buf_delete_icon"><i class="fas fa-sync fa-spin"></i></span>
                    <i class="fas fa-trash"></i> ' . Text::_('JACTION_DELETE') . '
                </button>
            </div>';




        $toolbar .= '</div>';

        return $toolbar;
    }
}
