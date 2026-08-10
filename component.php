<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2005 - 2026 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Filesystem\File;

include_once JPATH_THEMES . '/' . $this->template . '/logics/logic_base.php';

// variables
$app   = Factory::getApplication();
$doc   = $app->getDocument();
$tpath = $this->baseurl . '/templates/' . $this->template;

// generator tag
$this->setGenerator(null);

//LAYOUT COMPONENT
if (file_exists($tpath_abs . '/layouts/' . $buf_layout . '/component.php')) {
    include_once JPATH_THEMES . '/' . $this->template . '/layouts/' . $buf_layout . '/component.php';
    return;
}

//load sheets and scripts
if (File::exists($cachepath . 'print.css')) {
    $doc->getWebAssetManager()->registerAndUseStyle('buf.print', $cache_tpath . '/css/print.css', ['version' => '1']);
}

?><!doctype html>

<html lang="<?php echo $this->language; ?>">

<head>
    <?php
    $headDebugService = 'component.php';
    include JPATH_THEMES . '/' . $this->template . '/logics/head_common.php';
    ?>

    <?php //cache control ?>
    <?php echo $buf_cache_control; ?>
</head>

<body class="<?php echo 'tmpl_document ' .
    (($menu->getActive() == $menu->getDefault()) ? ('front') : ('site')) . '
    ' . $active->alias . ' ' . $pageclass . ' ' . $docalias; ?>"
>

    <div class="contenidos wrapper row">
        <article class="contenido buf_component">
            <jdoc:include type="message" />
            <jdoc:include type="component" />
        </article>
    </div>
        
    <?php //if ($_GET['print'] == '1') echo '<script type="text/javascript">window.print();</script>'; ?>

</body>

<?Php
$app  = Factory::getApplication();
$templateparams = $app->getTemplate(true)->params;

//LOGIC
if (!$templateparams->get('buf_edit_base', 0)) {
    if (!$check_jtfw || $check_jtfw == '1.0.0' || !$check_jtlibs || $check_jtlibs == '1.0.0') {
        //
    } else {
        include_once JPATH_THEMES . '/' . $this->template . '/logics/logic.php';
    }
}
?>

</html>
