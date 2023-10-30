<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Jtotal\BUF\BufHelper;

// begin function compress
function compress($buffer)
{
    include_once 'minifier.php';
    $buffer = fn_minify_css($buffer);

    return $buffer;
}

///////////////////
//  CHECK FW //
///////////////////
if (is_file(JPATH_PLUGINS . '/system/jtframework/autoload.php')) {
    require_once JPATH_PLUGINS . '/system/jtframework/autoload.php';
} else {
    $app = Factory::getApplication();
    $app->enqueueMessage(Text::_('JT_TOTALMENU_ERROR'), 'error');
    $app->enqueueMessage(Text::_('JT_FW_NOT_FOUND'), 'error');
    return;
}

if ($base_css_exists == false || $runless == '1' || $buf_edit_base == '1') {
    if (!$base_css_exists) {
        $buf_debug += BUFHelper::addDebug('BASE css | check', 'cubes', 'base css doesn\'t exist... creating it.', $startmicro, 'table-warning', 'runsass_base.php');
    }

    if ($buf_edit_base == 1) {
        $buf_debug += BUFHelper::addDebug('BASE css | check', 'cubes', 'EDIT BASE MODE', $startmicro, 'table-default', 'runsass_base.php');
    }

    $uri = Uri::base();
    $lesspath = JPATH_BASE . '/templates/' . $this->template . '/css';

    $sass_files = array();

    require_once JPATH_BASE . '/templates/' . $this->template . '/src/loadphpscss_base.php';
} else {
    var_dump($base_css_exists);
    var_dump($runless);
    var_dump($buf_edit_base);
}
