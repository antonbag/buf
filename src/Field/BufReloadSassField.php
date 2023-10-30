<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 * @version         5.0.0
 */

namespace Jtotal\BUF\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

//no direct access
defined('_JEXEC') or die;

class BufReloadSassField extends FormField
{
    protected $type = 'BufReloadSass';

    protected function getInput()
    {

        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        /** @var Session $session */
        $session = $app->getSession();

        $reload_sass = $session->get('buf_reload_sass');
        $reload_bs_sass = $session->get('buf_reload_bs_sass');
        $reload_fa_sass = $session->get('buf_reload_fa_sass');

        $text = '<input type="hidden" id="reloadsass" value="' . $reload_sass . '"/>';
        $text .= '<input type="hidden" id="reloadbs" value="' . $reload_bs_sass . '"/>';
        $text .= '<input type="hidden" id="reloadfa" value="' . $reload_fa_sass . '"/>';

        return $text;
    }

    public function getLabel()
    {
        //return 'reloadSass';
    }
}
