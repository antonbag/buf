<!--BUF TEMPLATE / layout: minimalist -->
<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2005 - 2026 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 *
 * Layout "minimalist" — minimalista / moderno.
 *
 * index.php (del framework) ya renderiza por encima de este archivo:
 * topbar, offcanvas + botón hamburguesa, #superwrapper, #subfooter,
 * créditos y la imagen de fondo. Aquí sólo va lo de dentro.
 *
 * Mismo orden y posiciones de módulo que el layout "default"
 * (header, menu_header, banner_front, menu_principal, banner_page,
 * grid buf_left/content_pral/buf_right, footer), pero con marcado
 * semántico (header/nav/aside/footer), igual que "mysite".
 *
 * Variables disponibles (definidas en logics/logic_base.php):
 *   $content_container, $bs_left_pos, $bs_right_pos,
 *   $buf_left_sm|md|lg, $buf_right_sm|md|lg,
 *   $content_pral_bs_sm|md|lg
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$app      = Factory::getApplication();
$sitename = htmlspecialchars((string) $app->get('sitename'), ENT_QUOTES, 'UTF-8');
$homeUrl  = htmlspecialchars((string) $this->baseurl, ENT_QUOTES, 'UTF-8') . '/';

$hasLeft  = (bool) $this->countModules($bs_left_pos);
$hasRight = (bool) $this->countModules($bs_right_pos);

// Si no hay ninguna sidebar, el contenido ocupa todo el ancho
$mainColumnClasses = trim($content_pral_bs_sm . $content_pral_bs_md . $content_pral_bs_lg);
$mainColumnClasses = $mainColumnClasses !== '' ? $mainColumnClasses : 'content_full';
?>

<?php /* ══════════  CABECERA  ══════════ */ ?>
<header class="site-header">
    <div class="container site-header__inner">

        <a class="site-header__brand" href="<?php echo $homeUrl; ?>">
            <?php echo $sitename; ?>
        </a>

        <?php if ($this->countModules('menu_header')) : ?>
            <div class="site-header__search">
                <jdoc:include type="modules" name="menu_header" style="none" />
            </div>
        <?php endif; ?>


        <?php if ($this->countModules('menu')) : ?>
            <nav class="site-header__nav" aria-label="<?php echo Text::_('TPL_BUF_MENU_PRINCIPAL'); ?>">
                <jdoc:include type="modules" name="menu" style="none" />
            </nav>
        <?php endif; ?>

    </div>
</header>

<?php /* ══════════  BANNER PORTADA  ══════════ */ ?>
<?php if ($this->countModules('banner_front')) : ?>
    <div class="banner_front">
        <jdoc:include type="modules" name="banner_front" style="none" />
    </div>
<?php endif; ?>


<?php /* ══════════  CONTENIDO  ══════════ */ ?>
<div id="contents" class="<?php echo $content_container; ?>">

    <?php if ($this->countModules('breadcrumbs')) : ?>
        <div class="breadcrumbs">
            <jdoc:include type="modules" name="breadcrumbs" style="none" />
        </div>
    <?php endif; ?>

    <?php if ($this->countModules('banner_page')) : ?>
        <div class="banner_page">
            <jdoc:include type="modules" name="banner_page" style="none" />
        </div>
    <?php endif; ?>

    <div class="row">

        <?php if ($hasLeft) : ?>
            <aside class="buf_left <?php echo $buf_left_sm . $buf_left_md . $buf_left_lg; ?>">
                <jdoc:include type="modules" name="<?php echo $bs_left_pos; ?>" style="none" />
            </aside>
        <?php endif; ?>

        <div class="content_pral <?php echo $mainColumnClasses; ?>">

            <?php if ($this->countModules('precontent', true)) : ?>
                <div id="precontent">
                    <jdoc:include type="modules" name="precontent" style="none" />
                </div>
            <?php endif; ?>

            <main id="buf-main-content">
                <jdoc:include type="message" />
                <jdoc:include type="component" />
            </main>

            <?php if ($this->countModules('subcomponent', true)) : ?>
                <div id="subcomponent">
                    <jdoc:include type="modules" name="subcomponent" style="none" />
                </div>
            <?php endif; ?>

        </div>

        <?php if ($hasRight) : ?>
            <aside class="buf_right <?php echo $buf_right_sm . $buf_right_md . $buf_right_lg; ?>">
                <jdoc:include type="modules" name="<?php echo $bs_right_pos; ?>" style="none" />
            </aside>
        <?php endif; ?>

    </div>

    <?php if ($this->countModules('subcontent')) : ?>
        <div id="subcontent">
            <jdoc:include type="modules" name="subcontent" style="none" />
        </div>
    <?php endif; ?>

</div>


<?php /* ══════════  FOOTER  ══════════ */ ?>
<?php if ($this->countModules('footer')) : ?>
    <footer id="footer">
        <div class="container">
            <div class="footer-grid">
                <jdoc:include type="modules" name="footer" style="none" />
            </div>
        </div>
    </footer>
<?php endif; ?>
