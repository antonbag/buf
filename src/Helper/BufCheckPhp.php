<?php

/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2021 jtotal
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Jtotal\BUF\Site\Helper;

use Joomla\CMS\Factory;
use Jtotal\BUF\Site\Helper\BufHelper;

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Helper class for rendering Offcanvas elements.
 *
 * @since  1.0
 */
class BufCheckPhp
{

    /**
     * Check if PHP version meets the minimum requirement for the given Joomla version
     *
     * @param   string  $jversion  The Joomla version to check against
     *
     * @return  void
     * @since   1.0
     */
    public static function checkPhpVersion()
    {
     
        $jversion = BufHelper::getJVersion();

        $minVersions = [
            '3' => '5.6.0',
            '4' => '7.3.0',
            '5' => '8.1.0',
            '6' => '8.3.0'
        ];

        if (!isset($minVersions[$jversion])) {
            return;
        }

        $minVersion = $minVersions[$jversion];

        if (defined('PHP_VERSION')) {
            $version = PHP_VERSION;
        } elseif (function_exists('phpversion')) {
            $version = phpversion();
        } else {
            // No version info. Use minimum required version as fallback.
            $version = $minVersion;
        }

        // An old PHP version is installed.
        if (!version_compare($version, $minVersion, '>=')) {

            Factory::getApplication()->enqueueMessage(
                \Joomla\CMS\Language\Text::sprintf('You are using PHP %s. Please upgrade to PHP %s or higher.', $version, $minVersion),
                'warning'
            );
        }
    }
}
