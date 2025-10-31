<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Asset\AssetManager;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$extraButtons     = AuthenticationHelper::getLoginButtons('form-login');
$app              = Factory::getApplication();
$wa               = $this->getWebAssetManager();

$fullWidth = 1;

// Template path
$templatePath = 'media/templates/site/cassiopeia';

include_once JPATH_THEMES.'/'.$this->template.'/logics/logic_base.php';

include_once JPATH_THEMES.'/'.$this->template.'/logics/logic.php';
// Color Theme
$paramsColorName = $this->params->get('colorName', 'colors_standard');
$assetColorName  = 'theme.' . $paramsColorName;
$wa->registerAndUseStyle($assetColorName, $templatePath . '/css/global/' . $paramsColorName . '.css');



$wa = $this->getWebAssetManager();
$wr = $this->getWebAssetManager()->getRegistry();
$wr->addRegistryFile(JPATH_THEMES . '/' . $this->template . '/joomla.asset.json');


// Use a font scheme if set in the template style options
$paramsFontScheme = $this->params->get('useFontScheme', false);
$fontStyles       = '';

if ($paramsFontScheme) {
    if (stripos($paramsFontScheme, 'https://') === 0) {
        $this->getPreloadManager()->preconnect('https://fonts.googleapis.com/', ['crossorigin' => 'anonymous']);
        $this->getPreloadManager()->preconnect('https://fonts.gstatic.com/', ['crossorigin' => 'anonymous']);
        $this->getPreloadManager()->preload($paramsFontScheme, ['as' => 'style', 'crossorigin' => 'anonymous']);
        $wa->registerAndUseStyle('fontscheme.current', $paramsFontScheme, [], ['media' => 'print', 'rel' => 'lazy-stylesheet', 'onload' => 'this.media=\'all\'', 'crossorigin' => 'anonymous']);

        if (preg_match_all('/family=([^?:]*):/i', $paramsFontScheme, $matches) > 0) {
            $fontStyles = '--cassiopeia-font-family-body: "' . str_replace('+', ' ', $matches[1][0]) . '", sans-serif;
			--cassiopeia-font-family-headings: "' . str_replace('+', ' ', isset($matches[1][1]) ? $matches[1][1] : $matches[1][0]) . '", sans-serif;
			--cassiopeia-font-weight-normal: 400;
			--cassiopeia-font-weight-headings: 700;';
        }
    } else {
        $wa->registerAndUseStyle('fontscheme.current', $paramsFontScheme, ['version' => 'auto'], ['media' => 'print', 'rel' => 'lazy-stylesheet', 'onload' => 'this.media=\'all\'']);
        $this->getPreloadManager()->preload($wa->getAsset('style', 'fontscheme.current')->getUri() . '?' . $this->getMediaVersion(), ['as' => 'style']);
    }
}

// Override 'template.active' asset to set correct ltr/rtl dependency
$wa->registerStyle('template.active', '', [], [], ['template.cassiopeia.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr')]);

// Logo file or site title param
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);

if ($this->params->get('logoFile')) {
    $logo = HTMLHelper::_('image', Uri::root(false) . htmlspecialchars($this->params->get('logoFile'), ENT_QUOTES), $sitename, ['loading' => 'eager', 'decoding' => 'async'], false, 0);
} elseif ($this->params->get('siteTitle')) {
    $logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
} else {
    //$logo = HTMLHelper::_('image', 'logo.svg', $sitename, ['class' => 'logo d-inline-block', 'loading' => 'eager', 'decoding' => 'async'], true, 0);
    $logo = '<span title="' . $sitename . '">' . $sitename . '</span>';
}

// Defer font awesome
$wa->getAsset('style', 'fontawesome')->setAttribute('rel', 'lazy-stylesheet');

?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <jdoc:include type="styles" />
    <jdoc:include type="scripts" />
</head>

<body class="site d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="container-sm">
        <div class="card shadow-lg">
            <div class="card-header text-center bg-primary text-white">
                <?php if (!empty($logo)) : ?>
                    <h1 class="h3 m-0"><?php echo $logo; ?></h1>
                <?php else : ?>
                    <h1 class="h3 m-0"><?php echo $sitename; ?></h1>
                <?php endif; ?>
            </div>

            <div class="card-body text-center">
                <?php if ($app->get('offline_image')) : ?>
                    <div class="mb-3">
                        <?php echo HTMLHelper::_('image', $app->get('offline_image'), $sitename, ['class' => 'img-fluid'], false, 0); ?>
                    </div>
                <?php endif; ?>

                <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
                    <p class="text-muted"> <?php echo $app->get('offline_message'); ?></p>
                <?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
                    <p class="text-muted"> <?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
                <?php endif; ?>
            </div>

            <div class="card-body" style="max-width:600px; margin-left:auto; margin-right:auto;">
                <jdoc:include type="message" />
                <form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login" class="needs-validation" novalidate>
                    <fieldset>
                        <legend class="text-center mb-4 h5">Login</legend>

                        <!-- Username Field -->
                        <div class="mb-3">
                            <label for="username" class="form-label"> <?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                            <input name="username" id="username" type="text" class="form-control" required>
                            <div class="invalid-feedback">Please enter your username.</div>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label"> <?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                            <input name="password" id="password" type="password" class="form-control" required>
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>

                        <!-- Extra Buttons -->
                        <?php foreach ($extraButtons as $button) :
                            $dataAttributeKeys = array_filter(array_keys($button), function ($key) {
                                return substr($key, 0, 5) == 'data-';
                            });
                        ?>
<!--                         <div class="d-grid mb-3">
                            <button type="button" class="btn btn-secondary <?php echo $button['class'] ?? '' ?>"
                                <?php foreach ($dataAttributeKeys as $key) : ?>
                                    <?php echo $key ?>="<?php echo $button[$key] ?>"
                                <?php endforeach; ?>
                                <?php if ($button['onclick']) : ?>
                                    onclick="<?php echo $button['onclick'] ?>"
                                <?php endif; ?>
                                title="<?php echo Text::_($button['label']) ?>"
                                id="<?php echo $button['id'] ?>">
                                <?php if (!empty($button['icon'])) : ?>
                                    <span class="<?php echo $button['icon'] ?>"></span>
                                <?php elseif (!empty($button['image'])) : ?>
                                    <?php echo $button['image']; ?>
                                <?php elseif (!empty($button['svg'])) : ?>
                                    <?php echo $button['svg']; ?>
                                <?php endif; ?>
                                <?php echo Text::_($button['label']) ?>
                            </button>
                        </div> -->
                        <?php endforeach; ?>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" name="Submit" class="btn btn-primary"> <?php echo Text::_('JLOGIN'); ?></button>
                        </div>

                        <!-- Hidden Inputs -->
                        <input type="hidden" name="option" value="com_users">
                        <input type="hidden" name="task" value="user.login">
                        <input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
                        <?php echo HTMLHelper::_('form.token'); ?>
                    </fieldset>
                </form>
            </div>

            <div class="card-footer text-center text-muted small">
                &copy; <?php echo date('Y'); ?> <?php echo $sitename; ?>
            </div>
        </div>
    </div>
</body>


</html>
