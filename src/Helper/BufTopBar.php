<?php

/**
 * @package BUF Framework
 * @author jtotal https://jtotal.org
 * @copyright Copyright (c) 2005 - 2025 jtotal
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Jtotal\BUF\Site\Helper;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;

/**
 * Helper class for rendering Offcanvas elements.
 *
 * @since  1.0
 */
class BufTopBar
{

    public static function getTopBar(string $id, Registry $buf_topbar, $buf_offcanvas_max_w = '992'): string
    {

        $buf_topbar_on = $buf_topbar->get('buf_topbar_on', 0);
        $buf_topbar_logo_img = HTMLHelper::cleanImageURL($buf_topbar->get('buf_topbar_logo', ''));
        $buf_topbar_logo_fallback = HTMLHelper::cleanImageURL($buf_topbar->get('buf_topbar_logo_fallback', ''));
        $buf_topbar_logo_alt = $buf_topbar->get('buf_topbar_logo_alt', 'logo');
        $buf_topbar_logo_pos = $buf_topbar->get('buf_topbar_logo_pos', "l");

        $buf_topbar_color = $buf_topbar->get('buf_topbar_color', '#fff');
        $buf_topbar_module = $buf_topbar->get('buf_topbar_module', '');
        $buf_topbar_show_on_scroll = $buf_topbar->get('buf_show_on_scroll', '');
        $buf_show_on_scroll_onlymobile = ($buf_topbar->get('buf_show_on_scroll_onlymobile', false) == true)  ? true : false;


       
        $buf_topbar_classes = '';
        $buf_topbar_logo = '';

        if ($buf_topbar_on) {
            $buf_topbar_classes .= 'buf_topbar_on';
        }

        //logo show
        if ($buf_topbar->get('buf_topbar_image_show', '0')) {
            $buf_topbar_logo = BufOffcanvas::getTopBarImages($buf_topbar);
        }

        $buf_show_on_scroll_class = '';
        if ($buf_topbar_show_on_scroll) {
            $buf_show_on_scroll_class = ' buf_show_on_scroll';
            if ($buf_show_on_scroll_onlymobile) {
                $buf_show_on_scroll_class .= ' buf_show_on_scroll_only_mobile';
            }
        }


        if (!$buf_topbar_on) {
            return '';
        }

        if ($buf_topbar_show_on_scroll) {
            Factory::getDocument()->getWebAssetManager()->useScript('topbar.js');

            Factory::getDocument()->getWebAssetManager()->addInlineScript('
                document.addEventListener("DOMContentLoaded", function () {
                    BufInitFixedBar("buf_topbar", ' . $buf_show_on_scroll_onlymobile . ');
                });');
        }
        ob_start();
        ?>

        <nav id="<?php echo $id; ?>" role="navigation" aria-label="Topbar"
            role="banner" aria-disabled="true"
            class="<?php echo $buf_topbar_classes . ' ' . $buf_show_on_scroll_class; ?>"
            data-mobile="<?php echo $buf_offcanvas_max_w; ?>">
            <?php
            echo $buf_topbar_logo;
            if ($buf_topbar_module != '') {
                echo '<div class="buf_topbar_modulewrapper">
                    <jdoc:include type="modules" name="' . $buf_topbar_module . '"/>
                </div>
            ';
            }
            ?>

        </nav>
        <?php
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
