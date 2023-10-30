<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 * @version         5.0.0
 */

namespace Jtotal\BUF\Site\Field;

//jimport('joomla.form.formfield');
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

//no direct access
defined('_JEXEC') or die;

class BufclearcacheField extends FormField
{
    protected $type = 'Bufclearcache';

    protected function getInput()
    {
        $cacheb = '<div class="btn-wrapper" id="toolbar-buf_empy_cache"><button class="btn btn-primary btn-small buf_clearcache">
        <span class="buf_clearcache_icon"><i class="fas fa-trash-alt"></i></span>
        <span class="buf_clear_cache_status"></span>
        <span class="buf_clearcache_text">
        ' . Text::_('TPL_BUF_CLEAR_CACHE') . '</span>
        </button></div>';

        return $cacheb;
    }

    public function getLabel()
    {
        //return JText::_('TPL_BUF_CURRENT_FAVICON');
    }
}
