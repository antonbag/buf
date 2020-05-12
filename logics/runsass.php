<?php defined( '_JEXEC' ) or die;

include_once JPATH_SITE.'/templates/buf/classes/bufsass.php';

//CLASS BUFSASS
$buffles = new BUFsass();
$runless = $buffles::runsass('', $templateparams, 'buf', $startmicro);
$buf_debug += $runless;




