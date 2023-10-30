<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;


use Jtotal\BUF\BUFsass;

//include_once JPATH_SITE.'/templates/buf/src/bufsass.php';

//CLASS BUFSASS
$buffles = new BUFsass();
$runless = $buffles::runsass('', $templateparams, 'buf', $startmicro);
$buf_debug += $runless;
