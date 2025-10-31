<?php

/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2021 jtotal
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Jtotal\BUF\Site\Helper;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Version;

abstract class BufHelper
{

    /**
     *
     * @param string $name
     * @param string $icon
     * @param string $value
     * @param integer $startmicro
     * @param string $tr_class
     * @param string $service
     * @return array
     */

    public static function addDebug($name = '', $icon = '', $value = '', $startmicro = 0, $tr_class = '', $service = '')
    {

        $current_time = microtime(TRUE);
        $totaltime = $current_time - $startmicro;

        $mireturn = array($name => array('icon' => $icon, 'value' => $value, 'totaltime' => $totaltime * 10000, 'tr_class' => $tr_class, 'service' => $service));
        return $mireturn;
    }

    /**
     * check and apply defer and async 
     *
     * @param [type] $defer
     * @return array
     */
    public static function check_defer_v4($defer)
    {

        //defer, async
        if ($defer == 0) {
            $defer_array = array();
        } elseif ($defer == 1) {
            $defer_array = array('defer' => 'defer');
        } elseif ($defer == 2) {
            $defer_array = array('async' => 'async');
        } else {
            //old browsers
            $defer_array = array('async' => 'async', 'defer' => 'defer');
        }

        return $defer_array;
    }

    /**
     * Get version of Joomla
     *
     * @return string
     */
    public static function getJVersion()
    {
        $jversion_api = new Version();
        $jversion = substr($jversion_api->getShortVersion(), 0, 1);
        return $jversion;
    }



    /**
     * get version of extension
     *
     * @param boolean $element
     * @param boolean $folder
     * @return void
     */
    public static function getExtensionVersion($element = false, $folder = false)
    {

        if (!$element) return;
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(array('*'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element));
        if ($folder) $query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
        $db->setQuery($query);
        $result = $db->loadObject();

        if (!$result) return false;

        $manifest_cache = json_decode($result->manifest_cache);

        //var_dump($result->enabled);
        if (isset($manifest_cache->version)) {

            return $manifest_cache->version;
        }

        return;
    }




}
