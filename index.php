<?php

/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2005 - 2026 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

declare(strict_types=1);

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Jtotal\BUF\Site\Helper\BufFavicon;
use Jtotal\BUF\Site\Helper\BufHelper;
use Jtotal\BUF\Site\Helper\BufOffcanvas;
use Jtotal\BUF\Site\Helper\BufTopBar;

include_once JPATH_THEMES . '/' . $this->template . '/logics/logic_base.php';

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

<head>
    <?php
    $headDebugService = 'index.php';
    include JPATH_THEMES . '/' . $this->template . '/logics/head_common.php';
    ?>

    <?php //FAVICONS ?>
    <?php BufFavicon::addFaviconLinks(); ?>

    <?php //cache control ?>
    <?php echo $buf_cache_control; ?>

</head>

<body class="<?php echo implode(" ", $bodyclass); ?> buf_offcanvas_hidden">

    <?php
        echo BufTopBar::getTopBar('buf_topbar', $buf_topbar, $buf_offcanvas_max_w, Text::_('TPL_BUF_OFFCANVAS_TOPBAR_LABEL'));
    ?>
   
    <?php
    if ($buf_offcanvas) {
        $offcanvasHtml = BufOffcanvas::renderOffcanvas($templateparams);
        echo $offcanvasHtml;
        $buf_debug += BUFHelper::addDebug('offcanvas_button', 'bars', 'active and module present', $startmicro, 'table-success', 'index.php');
    }
    ?>

    <?php /***************************************/
    /*************SUPERWRAPPER*****************/
    /****************************************/
    ?>
    <div id="superwrapper" class="superwrapper <?php echo $buf_offcanvas_style . ' ' . $buf_offcanvas_position; ?>">

        <?php if ($container != '') :
            ?> 
            <div class="<?php echo $container; ?>">
        <?php endif; ?>

            <?php include_once JPATH_THEMES . '/' . $this->template . '/layouts/' . $buf_layout . '/layout.php'; ?>
            
            <?php if ($container != '') :
                ?> 
            </div>
            <?php endif; ?>

        <?php //SUBFOOTER ?>
        <div id="subfooter">
            <div class="<?php echo $container; ?>">
                <?php if ($this->countModules('subfooter')) : ?>
                    <jdoc:include type="modules" name="subfooter" />
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($templateparams->get('buf_show_credit', "1")) :
        ?>

        <div class="buf_credits">
            <div class="buf_credit_logo">
                <a href="https://jtotal.org/buf-template" target="_blank" rel="nofollow noopener">
                    <img src="<?php echo Uri::root(true); ?>/media/templates/site/buf/images/buf_logos//buf_logo_footer.svg" 
                        width="32" 
                        height="33" 
                        alt="BUF Template" 
                        loading="lazy"
                        onerror="this.onerror=null; this.src='<?php echo Uri::root(true); ?>/media/templates/site/buf/images/buf_logos/buf_logo_footer.png';">

                </a>
            </div>
            <div class="buf_credit_text">
                <p> for Joomla by 
                    <a href="https://jtotal.org/buf-template" 
                       target="_blank" 
                       rel="nofollow noopener">jtotal</a>
                </p>
                <p> © jtotal <?php echo date("Y"); ?></p>
            </div>
        </div>
    <?php endif; ?>



    <?php //BG ?>
    <?php if ($buf_bg_img) : ?>
        <div class="bg">
            <img src="<?php echo htmlspecialchars((string) $buf_bg_img, ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy" decoding="async" aria-hidden="true" />
        </div>
    <?php endif; ?>

    <?php //LOGIC
    if (!$edit_base_input) {
        if (!$templateparams->get('buf_edit_base', 0)) {
            if ($check_jtfw && $check_jtfw !== '1.0.0' && $check_jtlibs && $check_jtlibs !== '1.0.0') {
                include_once JPATH_THEMES . '/' . $this->template . '/logics/logic.php';
            }
        }
    } else {
        echo '<div class="buf_dev_mode" 
            style="position:fixed;
                bottom:0px;
                left:0px;z-index:100;
                width: 100%;
                padding: 4px 10px;
                display: flex;
                align-items: center;
                justify-content: space-between;"
            >
            <a class="buf_dev_mode_edit_base" href="index.php">
                <i class="fas fa-box-open"></i> 
                    Exit base mode
            </a>
    </div>';
    }
    ?>

    <jdoc:include type="modules" name="debug" />
</body>
</html>