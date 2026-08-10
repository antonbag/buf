<?php

declare(strict_types=1);

/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2021 jtotal
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Jtotal\BUF\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;
use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Helper class for rendering Offcanvas elements.
 *
 * @since  1.0
 */
class BufFavicon
{
    private const LEGACY_WORKER_URL = 'buf_worker.js';
    private const LEGACY_CACHE_NAME = 'buf-v1';
    private const LEGACY_CACHE_PREFIX = 'buf-';
    private const WORKER_CLEANUP_VERSION = 'buf-worker-retire-v1';

    public $iconsFolderUrl;
    public $templateparams;

    public function __construct($templateid)
    {
        $templateData = self::getCurrentParams($templateid);

        if (!$templateData) {
            $this->templateparams = new Registry('{}');
            $this->iconsFolderUrl = rtrim(Uri::root(), '/') . '/templates/buf/layouts/default/icons/';
            return;
        }

        $params = $templateData->params ?? '{}';
        $this->templateparams = new Registry($params);
        $buf_layout = $this->templateparams->get('buf_layout', 'default');
        $this->iconsFolderUrl = rtrim(Uri::root(), '/') . '/templates/buf/layouts/' . $buf_layout . '/icons/';
    }

    public static function create_favicons($templateid, $image): string|bool
    {
        $templateData = self::getCurrentParams($templateid);

        if (!$templateData) {
            return 'Template not found';
        }

        $params = $templateData->params ?? '{}';
        $templateparams = new Registry($params);
        $template_name = $templateData->template ?? 'buf';

        $buf_layout = $templateparams->get('buf_layout', 'default');
        $iconsFolderUrl = Uri::root() . 'templates/buf/layouts/' . $buf_layout . '/icons/';

        $image_ok = HTMLHelper::cleanImageURL(JPATH_SITE . '/' . $image);

        $template_path = JPATH_SITE . '/templates/buf/';
        $image_path = $image_ok->url;
        $buf_layout = $templateparams->get('buf_layout', 'default');
        $fav_path = JPATH_SITE . '/templates/buf/layouts/' . $buf_layout . '/icons/';

        if (!Folder::exists($fav_path)) {
            Folder::create($fav_path);
        }

        $uri = Uri::base();

        $error = '';

        $source = $image_path;
        $destination = $fav_path;

        $android = self::_generateAndroid($image_path, $fav_path);

        if ($android !== true) {
            return $android;
        }

        $android_json = self::_generateAndroidJson($template_name, $fav_path, $iconsFolderUrl);

        $ms_json = self::_generateMsXml($template_name, $fav_path, $templateparams->get('buf_mscolor', '#57616d'), $buf_layout);

        $ios = self::_generateIos($image_path, $fav_path);

        $favicon_ico = self::_generateIco($image_path, $fav_path);

        return true;
    }

    private static function _generateAndroid($image, $output)
    {
        $imgSrc = $image;
        //create image from the jpeg
        //var_dump($image);
        $output_file = $output . 'android-icon-512x512.png';
        $myImage = self::redimesionImage($image, $output_file, 512, 512, 0);
        if ($myImage !== true) {
            return $myImage;
        }

        $output_file = $output . 'android-icon-192x192.png';
        $myImage = self::redimesionImage($image, $output_file, 192, 192, 0);
        if ($myImage !== true) {
            return $myImage;
        }
        $image = $output_file;
        $output_file = $output . 'android-icon-144x144.png';
        $myImage = self::redimesionImage($image, $output_file, 144, 144, 0);

        $output_file = $output . 'android-icon-96x96.png';
        $myImage = self::redimesionImage($image, $output_file, 96, 96, 0);

        //$output_file = $output . 'android-icon-96x96.png';
        //$myImage = self::redimesionImage($image, $output_file, 96, 96, 0);

        $output_file = $output . 'android-icon-72x72.png';
        $myImage = self::redimesionImage($image, $output_file, 72, 72, 0);

        $output_file = $output . 'android-icon-48x48.png';
        $myImage = self::redimesionImage($image, $output_file, 48, 48, 0);

        $output_file = $output . 'android-icon-36x36.png';
        $myImage = self::redimesionImage($image, $output_file, 36, 36, 0);

        //favicon PNG
        $output_file = $output . 'favicon-96x96.png';
        $myImage = self::redimesionImage($image, $output_file, 96, 96, 0);

        $output_file = $output . 'favicon-32x32.png';
        $myImage = self::redimesionImage($image, $output_file, 32, 32, 0);

        $output_file = $output . 'favicon-16x16.png';
        $myImage = self::redimesionImage($image, $output_file, 16, 16, 0);

        //MICROSOFT PNG
        $output_file = $output . 'ms-icon-70x70.png';
        $myImage = self::redimesionImage($image, $output_file, 70, 70, 0);

        $output_file = $output . 'ms-icon-144x144.png';
        $myImage = self::redimesionImage($image, $output_file, 144, 144, 0);

        $output_file = $output . 'ms-icon-150x150.png';
        $myImage = self::redimesionImage($image, $output_file, 150, 150, 0);

        return true;
    }

    public static function _generateAndroidJson($template_name, $fav_path, $iconsFolderUrl): bool
    {
        $siteUrl = rtrim(Uri::root(), '/') . '/';

        $icos = array(
            'name'             => $template_name,
            'short_name'       => mb_strimwidth($template_name, 0, 12, ''),
            'id'               => '/',
            'start_url'        => '/',
            'scope'            => '/',
            'display'          => 'standalone',
            'background_color' => '#ffffff',
            'theme_color'      => '#ffffff',
            'icons'            => array(
            array(
                'src' => 'android-icon-36x36.png',
                'sizes' => '36x36',
                'type' => 'image/png',
                'purpose' => 'any',
            ),
            array(
                'src' => 'android-icon-48x48.png',
                'sizes' => '48x48',
                'type' => 'image/png',
                'purpose' => 'any',
            ),
            array(
                'src' => 'android-icon-72x72.png',
                'sizes' => '72x72',
                'type' => 'image/png',
                'purpose' => 'any',
            ),
            array(
                'src' => 'android-icon-96x96.png',
                'sizes' => '96x96',
                'type' => 'image/png',
                'purpose' => 'any',
            ),
            array(
                'src' => 'android-icon-144x144.png',
                'sizes' => '144x144',
                'type' => 'image/png',
                'purpose' => 'any',
            ),
            array(
                'src' => 'android-icon-192x192.png',
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ),
            array(
                'src' => 'android-icon-512x512.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ),
        ),
        );

        $fp = fopen($fav_path . 'manifest.json', 'w');
        fwrite($fp, json_encode($icos));
        fclose($fp);

        return true;
    }

    private static function _generateMsXml($template_name, $fav_path, $color, $buf_layout)
    {

        $xml = '<?xml version="1.0" encoding="utf-8"?>
          <browserconfig>
            <msapplication>
              <tile>
                <square70x70logo src="templates/' . $template_name . '/layouts/' . $buf_layout . '/icons/ms-icon-70x70.png"/>
                <square150x150logo src="templates/' . $template_name . '/layouts/' . $buf_layout . '/icons/ms-icon-150x150.png"/>
                <square310x310logo src="templates/' . $template_name . '/layouts/' . $buf_layout . '/icons/ms-icon-310x310.png"/>
                <TileColor>' . $color . '</TileColor>
              </tile>
            </msapplication>
          </browserconfig>';

        file_put_contents($fav_path . '/browserconfig.xml', $xml);

        return true;
    }
    //MAC IOS
    private static function _generateIos($image, $output)
    {
        $imgSrc = $image;
        //create image from the jpeg
        //var_dump($image);
        $output_file = $output . 'apple-icon-precomposed.png';
        $myImage = self::redimesionImage($image, $output_file, 192, 192, 0);
        if ($myImage !== true) {
            return $myImage;
        }
        $image = $output_file;

        $output_file = $output . 'apple-icon.png';
        $myImage = self::redimesionImage($image, $output_file, 192, 192, 0);

        $output_file = $output . 'apple-icon-180x180.png';
        $myImage = self::redimesionImage($image, $output_file, 180, 180, 0);

        $output_file = $output . 'apple-icon-152x152.png';
        $myImage = self::redimesionImage($image, $output_file, 152, 152, 0);

        $output_file = $output . 'apple-icon-144x144.png';
        $myImage = self::redimesionImage($image, $output_file, 144, 144, 0);

        $output_file = $output . 'apple-icon-120x120.png';
        $myImage = self::redimesionImage($image, $output_file, 120, 120, 0);

        $output_file = $output . 'apple-icon-114x114.png';
        $myImage = self::redimesionImage($image, $output_file, 114, 114, 0);

        $output_file = $output . 'apple-icon-76x76.png';
        $myImage = self::redimesionImage($image, $output_file, 76, 76, 0);

        $output_file = $output . 'apple-icon-72x72.png';
        $myImage = self::redimesionImage($image, $output_file, 72, 72, 0);

        $output_file = $output . 'apple-icon-60x60.png';
        $myImage = self::redimesionImage($image, $output_file, 60, 60, 0);

        $output_file = $output . 'apple-icon-57x57.png';
        $myImage = self::redimesionImage($image, $output_file, 57, 57, 0);

        return true;
    }

    //ICO
    private static function _generateIco($image, $output)
    {

        //ICO root
        $destination = $output . 'favicon.ico';
        //$destination_icons = $output.'icons/favicon.ico';
        $source = $output . '/android-icon-192x192.png';

        $sizes = array(
            array(16, 16),
            array(24, 24),
            array(32, 32),
            array(48, 48),

        );
        $ico_lib = new PhpIco($source, $sizes);
        $ico_lib->save_ico($destination);
        //$ico_lib->save_ico( $destination_icons );
        //base
        //$ico_lib->save_ico(JPATH_SITE.'/templates/buf/');

        return true;
    }

    //TODO create a general class
    private static function getCurrentParams($id)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);

        $query
            ->select(array('template,params'))
            ->from($db->quoteName('#__template_styles'))
            ->where($db->quoteName('id') . ' = ' . $db->quote($id));
        $db->setQuery($query);
        $result = $db->loadObject();

        return $result;
    }

    public static function redimesionImage($src, $dst, $width, $height, $crop = 1): string|bool
    {

        if (!list($w, $h) = getimagesize($src)) {
            return "Unsupported picture type!";
        }

        $type = strtolower(substr(strrchr($src, "."), 1));
        if ($type == 'jpeg') {
            $type = 'jpg';
        }

        switch ($type) {
            case 'bmp':
                $img = imagecreatefromwbmp($src);
                break;
            case 'gif':
                $img = imagecreatefromgif($src);
                break;
            case 'jpg':
                $img = imagecreatefromjpeg($src);
                break;
            case 'png':
                $img = imagecreatefrompng($src);
                break;
            default:
                return "Unsupported picture type!";
        }

        // resize
        if ($crop) {
            if ($w < $width or $h < $height) {
                return "Picture is too small!";
            }

            $ratio = max($width / $w, $height / $h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        } else {
            if ($w < $width and $h < $height) {
                return "Picture is too small!";
            }

            $ratio = min($width / $w, $height / $h);
            $width = $w * $ratio;
            $height = $h * $ratio;
            $x = 0;
        }

        // create canvas
        $size = (int) max($width, $height);
        $new = imagecreatetruecolor($size, $size);

        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagesavealpha($new, true);
            $trans_background = imagecolorallocatealpha($new, 0, 0, 0, 127);
            imagefill($new, 0, 0, $trans_background);
        } elseif ($type == "jpg") {
            $trans_background = imagecolorallocatealpha($new, 255, 255, 255, 0);
            imagefill($new, 0, 0, $trans_background);
        }

        //put images in canvas
        //imagecopyresampled($new, $img, ($size-$width)/2, ($size-$height)/2, $x, 0, $width, $height, $w, $h);

        imagecopyresampled($new, $img,
            (int) (($size - $width) / 2),
            (int) (($size - $height) / 2),
            (int) ($x),
            0,
            (int) ($width),
            (int) ($height),
            (int) ($w),
            (int) ($h)
        );

        //create PNG
        imagepng($new, $dst);
        return true;
    }






    /**
     * Method to add favicon links to the document head.
     *
     * @return void
     *
     * @since 1.0
     */
    public static function addFaviconLinks(): void
    {
        $app = Factory::getApplication();
        $template = $app->getTemplate(true);
        $templateparams = $template->params;
        $doc = $app->getDocument();
        $tpath = 'templates/' . $template->template;
        $buf_layout = $templateparams->get('buf_layout', 'default');

        // Add default favicon
        $faviconPath = $tpath . '/layouts/' . $buf_layout . '/icons/favicon.ico';
        $doc->addFavicon($faviconPath, 'image/vnd.microsoft.icon', 'shortcut icon');

        // Add Apple Touch Icons
        $appleSizes = [57, 60, 72, 76, 114, 120, 144, 152, 180];
        foreach ($appleSizes as $size) {
            $doc->addHeadLink($tpath . '/layouts/' . $buf_layout . '/icons/apple-icon-' . $size . 'x' . $size . '.png', 'apple-touch-icon', 'rel', ['sizes' => $size . 'x' . $size]);
        }

        // Add PNG favicons
        $pngIcons = [
            ['size' => '192x192', 'file' => 'android-icon-192x192.png'],
            ['size' => '16x16', 'file' => 'favicon-16x16.png'],
            ['size' => '32x32', 'file' => 'favicon-32x32.png'],
            ['size' => '96x96', 'file' => 'favicon-96x96.png']
        ];
        foreach ($pngIcons as $icon) {
            $doc->addHeadLink($tpath . '/layouts/' . $buf_layout . '/icons/' . $icon['file'], 'icon', 'rel', ['type' => 'image/png', 'sizes' => $icon['size']]);
        }

        // Add manifest
        $doc->addHeadLink($tpath . '/layouts/' . $buf_layout . '/icons/manifest.json', 'manifest', 'rel');

        // Add meta tags
        $msColor = $templateparams->get('buf_mscolor', '#57616d');
        $doc->setMetaData('msapplication-TileColor', $msColor);
        $doc->setMetaData('msapplication-TileImage', $tpath . '/layouts/' . $buf_layout . '/icons/ms-icon-144x144.png');
        $doc->setMetaData('theme-color', $msColor);
        $doc->setMetaData('msapplication-config', $tpath . '/layouts/' . $buf_layout . '/icons/browserconfig.xml');
        // Add SVG favicon if set
        $svgFavicon = $templateparams->get('buf_favicon_svg', '');
        if (!empty($svgFavicon)) {
            $svgIconUrl = HTMLHelper::cleanImageURL($svgFavicon);
            $doc->addHeadLink(Uri::root() . $svgIconUrl->url, 'icon', 'rel', ['type' => 'image/svg+xml']);
            $doc->addHeadLink(Uri::root() . $svgIconUrl->url, 'mask-icon', 'rel', ['color' => $msColor]);
        }

        // Add Apple mobile web app status bar style
        $doc->setMetaData('apple-mobile-web-app-status-bar-style', 'black-translucent');

        self::addAsyncWorkerMaintenance($doc, $buf_layout);
    }

    /**
     * Replace the legacy worker with idle-time browser cleanup and an optional
     * asynchronous server cleanup through the BUF AJAX plugin.
     */
    private static function addAsyncWorkerMaintenance($doc, string $buf_layout): void
    {
        $maintenanceOptions = [
            'ajaxUrl' => null,
            'cachePrefix' => self::LEGACY_CACHE_PREFIX,
            'cleanupVersion' => self::WORKER_CLEANUP_VERSION,
            'csrfToken' => Session::getFormToken(),
            'layout' => $buf_layout,
            'legacyCacheName' => self::LEGACY_CACHE_NAME,
            'storageKey' => 'buf-worker-cleanup:' . $buf_layout,
            'workerUrl' => rtrim(Uri::root(), '/') . '/' . self::LEGACY_WORKER_URL,
        ];

        if (PluginHelper::isEnabled('ajax', 'bufajax')) {
            $maintenanceOptions['ajaxUrl'] = rtrim(Uri::root(), '/') . '/index.php?option=com_ajax&group=ajax&plugin=bufajax&format=json';
        }

        $maintenanceOptionsJson = json_encode($maintenanceOptions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);

        $doc->addScriptDeclaration(
            <<<JS
(function () {
    const options = {$maintenanceOptionsJson};

    const getStoredVersion = () => {
        try {
            return window.localStorage ? window.localStorage.getItem(options.storageKey) : null;
        } catch (error) {
            return null;
        }
    };

    const setStoredVersion = () => {
        try {
            if (window.localStorage) {
                window.localStorage.setItem(options.storageKey, options.cleanupVersion);
            }
        } catch (error) {
        }
    };

    const matchesLegacyWorker = (scriptUrl) => {
        if (!scriptUrl) {
            return false;
        }

        try {
            return new URL(scriptUrl, window.location.href).pathname === new URL(options.workerUrl, window.location.href).pathname;
        } catch (error) {
            return scriptUrl.indexOf(options.workerUrl) !== -1 || scriptUrl.indexOf('buf_worker.js') !== -1;
        }
    };

    const unregisterLegacyWorkers = async () => {
        if (!('serviceWorker' in navigator) || typeof navigator.serviceWorker.getRegistrations !== 'function') {
            return;
        }

        const registrations = await navigator.serviceWorker.getRegistrations();

        await Promise.all(registrations.map((registration) => {
            const worker = registration.active || registration.waiting || registration.installing;

            if (!worker || !matchesLegacyWorker(worker.scriptURL)) {
                return Promise.resolve(false);
            }

            return registration.unregister();
        }));
    };

    const deleteLegacyCaches = async () => {
        if (!('caches' in window) || typeof window.caches.keys !== 'function') {
            return;
        }

        const cacheKeys = await window.caches.keys();
        const staleKeys = cacheKeys.filter((key) => key === options.legacyCacheName || key.indexOf(options.cachePrefix) === 0);

        await Promise.all(staleKeys.map((key) => window.caches.delete(key)));
    };

    const notifyServerCleanup = async () => {
        if (!options.ajaxUrl || typeof window.fetch !== 'function') {
            return;
        }

        const body = new URLSearchParams();
        body.append(options.csrfToken, '1');
        body.append('action', 'do_cleanup_worker_state');
        body.append('buf_layout', options.layout);
        body.append('cleanup_version', options.cleanupVersion);

        await window.fetch(options.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body.toString(),
            keepalive: true,
        });
    };

    const runMaintenance = async () => {
        if (getStoredVersion() === options.cleanupVersion) {
            return;
        }

        await Promise.allSettled([
            unregisterLegacyWorkers(),
            deleteLegacyCaches(),
            notifyServerCleanup(),
        ]);

        setStoredVersion();
    };

    const scheduleMaintenance = () => {
        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(() => {
                void runMaintenance();
            }, { timeout: 1500 });

            return;
        }

        window.setTimeout(() => {
            void runMaintenance();
        }, 0);
    };

    if (document.readyState === 'complete') {
        scheduleMaintenance();

        return;
    }

    window.addEventListener('load', scheduleMaintenance, { once: true });
})();
JS
        );
    }

}
