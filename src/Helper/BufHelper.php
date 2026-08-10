<?php

declare(strict_types=1);

/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2026 jtotal
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

    public static function addDebug($name = '', $icon = '', $value = '', $startmicro = 0, $tr_class = '', $service = ''): array
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
    public static function check_defer_v4($defer): array
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
    public static function getJVersion(): string
    {
        $jversion_api = new Version();
        // Major version only, e.g. "10.2.1" -> "10" (substr(...,0,1) would wrongly give "1").
        [$major] = explode('.', $jversion_api->getShortVersion());
        return $major;
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
        $db = Factory::getContainer()->get('DatabaseDriver');
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

    /**
     * Limit custom head markup to safe link/meta tags.
     */
    public static function sanitizeHeadMarkup(string $markup): string
    {
        $markup = trim($markup);

        if ($markup === '' || !class_exists(\DOMDocument::class)) {
            return '';
        }

        $allowedAttributes = array(
            'link' => array('rel', 'href', 'as', 'crossorigin', 'media', 'type', 'imagesrcset', 'imagesizes', 'referrerpolicy', 'fetchpriority', 'integrity', 'sizes', 'color', 'title', 'hreflang'),
            'meta' => array('name', 'content', 'http-equiv', 'charset', 'property', 'media'),
        );

        $document = new \DOMDocument('1.0', 'UTF-8');
        $previousUseInternalErrors = \libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="utf-8" ?><div id="buf-head-wrapper">' . $markup . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        \libxml_clear_errors();
        \libxml_use_internal_errors($previousUseInternalErrors);

        if (!$loaded) {
            return '';
        }

        $wrapper = $document->getElementsByTagName('div')->item(0);

        if (!$wrapper || $wrapper->getAttribute('id') !== 'buf-head-wrapper') {
            return '';
        }

        $sanitized = '';

        foreach ($wrapper->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $tag = strtolower($child->nodeName);

            if (!isset($allowedAttributes[$tag])) {
                continue;
            }

            $element = $document->createElement($tag);

            foreach ($allowedAttributes[$tag] as $attribute) {
                if (!$child->hasAttribute($attribute)) {
                    continue;
                }

                $value = trim($child->getAttribute($attribute));

                if ($value === '') {
                    continue;
                }

                if ($attribute === 'href') {
                    $value = self::sanitizeHeadUrl($value);

                    if ($value === '') {
                        continue;
                    }
                }

                $element->setAttribute($attribute, $value);
            }

            if (!self::shouldKeepHeadElement($tag, $element)) {
                continue;
            }

            $sanitized .= $document->saveHTML($element);
        }

        return $sanitized;
    }

    private static function sanitizeHeadUrl(string $url): string
    {
        if (preg_match('#^(?:javascript|data):#i', $url)) {
            return '';
        }

        return $url;
    }

    private static function shouldKeepHeadElement(string $tag, \DOMElement $element): bool
    {
        if ($tag === 'link') {
            return $element->hasAttribute('rel') && $element->hasAttribute('href');
        }

        if ($tag === 'meta') {
            return $element->hasAttribute('content')
                || $element->hasAttribute('charset')
                || $element->hasAttribute('http-equiv');
        }

        return false;
    }




}
