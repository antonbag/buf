<?php

declare(strict_types=1);
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace Jtotal\BUF\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Jtotal\BUF\Site\Helper\BufHelper;

//no direct access
defined('_JEXEC') or die;

class BufDuplicateLayoutField extends FormField
{
    protected $type = 'BufDuplicateLayout';

    protected function getInput(): string
    {

        //$app = JFactory::getApplication();
        $app = Factory::getApplication();

        $tpath = JPATH_SITE . '/templates/';

        $input = $app->getInput();
        $template_id = $input->get('id', 0, 'INT');

        $jversion = BufHelper::getJVersion();

        //toolbar
        $toolbar = '';

        $toolbar .= '<div class="buf_toolbar_wrapper">';

        //DUPLICATE
        $toolbar .= '<div class="buf_toolbar_file_buttons d-flex">';

        $toolbar .= '<div class="buf_duplicate_layout">';

        $toolbar .= '<a class="btn btn-default bg-secondary text-light buf_duplicate_layout_a"><i class="fas fa-plus-circle"></i> ' . Text::_('TPL_BUF_LAYOUT_NEW') . '</a>';

        $toolbar .= '<div class="input-group buf_layout_new_name_wrapper buf_hidden input-prepend input-append">
            <input type="text" name="buf_layout_new_name" id="buf_layout_name" value="" placeholder="layout name" class="form-control input hasTooltip" data-original-title="" title="">
            <a class="btn btn-default btn-outline-secondary buf_btn_create_layout" title="Create layout" href="#"><i class="fa fa-clone"></i> ' . Text::_('TPL_BUF_LAYOUT_CLONE') . '</a>
            <a class="btn btn-default btn-outline-secondary buf_btn_cancel_layout" title="Cancel layout" href="#"><i class="fa fa-times"></i> ' . Text::_('JCANCEL') . '</a>
        </div>';

        $toolbar .= '<span class="buf_duplicate_layout_msg"></span>';

        $toolbar .= '</div>';

        //ZIP
        $toolbar .= '<div class="buf_zip_layout">';

        $toolbar .= '<a class="btn btn-default  btn-primary buf_zip_layout_a"><i class="fas fa-file-archive"></i> ' . Text::_('TPL_BUF_LAYOUT_ZIP') . '</a>';

        $toolbar .= '</div>';

        $toolbar .= '</div>';

        $toolbar .= '<div class="buf_layout_toolbar_status">';
        $toolbar .= '</div>';

        $toolbar .= '</div>';

        return $toolbar;
    }

    public function getLabel(): string
    {
        return Text::_('TPL_BUF_LAYOUT_TOOLS');
    }
}
