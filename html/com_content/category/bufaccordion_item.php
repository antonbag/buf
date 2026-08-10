<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Create a shortcut for params.
$params = $this->item->params;
$canEdit = $this->item->params->get('access-edit');
$info = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

$currentDate = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
    || ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);

?>

<?php
$bufaccordion_mode = $this->params->get('bufaccordion_mode', 'collapse');
//$data_parent = ($bufaccordion_mode == 'collapse') ? '' : 'data-parent="#bufaccordion_'.$this->accordion_id.'"';
?>
<?php //$data_parent_class = ($this->startshow) ? '' : 'collapsed'; ?>
<?php $data_parent_class = 'collapsed';?>

<?php $images = json_decode($this->item->images); ?>


<?php
$image_tab = '';

if (($this->show_image == 'tab' || $this->show_image == 'both') && !empty($images) && !empty($images->image_intro)) {
    $imageAlt = !empty($images->image_intro_alt_empty) ? '' : (!empty($images->image_intro_alt) ? $images->image_intro_alt : $this->item->title);
    $image_tab = '<img width="100" src="' . htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8') . '" alt="' . htmlspecialchars($imageAlt, ENT_COMPAT, 'UTF-8') . '" />';
}

$allowedHeaderLevels = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
$bufaccordion_header_level = strtolower((string) ($this->bufaccordion_header_level ?: 'h3'));
$bufaccordion_header_level = in_array($bufaccordion_header_level, $allowedHeaderLevels, true) ? $bufaccordion_header_level : 'h3';
$accordion_item_id = (string) ($this->item->alias ?: 'item-' . $this->item->id);
$accordion_active_id = (string) ($this->bufaccordion_active_id ?? '');
$accordion_button_id = $accordion_item_id . '-button';
$accordion_heading_id = $accordion_item_id . '-heading';
$accordion_is_expanded = $accordion_item_id === $accordion_active_id;
$accordion_button_class = 'accordion-button' . ($accordion_is_expanded ? '' : ' collapsed');
$accordion_collapse_class = 'accordion-collapse collapse' . ($accordion_is_expanded ? ' show' : '');
$accordion_expanded = $accordion_is_expanded ? 'true' : 'false';
$accordion_after_display_title = trim((string) $this->item->event->afterDisplayTitle);
$accordion_button_id_attr = htmlspecialchars($accordion_button_id, ENT_COMPAT, 'UTF-8');
$accordion_item_id_attr = htmlspecialchars($accordion_item_id, ENT_COMPAT, 'UTF-8');
$accordion_heading_id_attr = htmlspecialchars($accordion_heading_id, ENT_COMPAT, 'UTF-8');
$accordion_title = htmlspecialchars($this->item->title, ENT_COMPAT, 'UTF-8');

if ($bufaccordion_mode == 'collapse') {
    echo HTMLHelper::_('bootstrap.collapse');
}
?>
<?php if ($bufaccordion_mode == 'accordeon') : ?>
<div class="accordion-item buf_accordion_item">
    <<?php echo $bufaccordion_header_level; ?> class="accordion-header" id="<?php echo $accordion_heading_id_attr; ?>">
        <button id="<?php echo $accordion_button_id_attr; ?>" class="<?php echo $accordion_button_class; ?>" type="button" data-bs-toggle="collapse"
            data-bs-target="#<?php echo $accordion_item_id_attr; ?>" aria-expanded="<?php echo $accordion_expanded; ?>"
            aria-controls="<?php echo $accordion_item_id_attr; ?>">
            <?php echo $image_tab; ?>
            <span class="buf_accordion_header_title p-2 align-self-center text-primary"><?php echo $accordion_title; ?></span>
        </button>
    </<?php echo $bufaccordion_header_level; ?>>
    <?php if ($accordion_after_display_title !== '') : ?>
    <div class="buf_accordion_header_meta">
        <?php echo $this->item->event->afterDisplayTitle; ?>
    </div>
    <?php endif; ?>
    <div id="<?php echo $accordion_item_id_attr; ?>" class="<?php echo $accordion_collapse_class; ?>" role="region"
        aria-labelledby="<?php echo $accordion_button_id_attr; ?>">
        <div class="accordion-body">
<?php endif; ?>
<div class="com-content-category-blog__item blog-item" itemprop="blogPost" itemscope
    itemtype="https://schema.org/BlogPosting">
    <?php //echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>

    <?php
if ($this->params->get('bufaccordion_show_full_image', '0')) {
    echo LayoutHelper::render('joomla.content.full_image', $this->item);
}

?>


    <div class="item-content">
        <?php if ($isUnpublished): ?>
        <div class="system-unpublished">
            <?php endif;?>

            <?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>

            <?php if ($canEdit): ?>
            <?php echo LayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item)); ?>
            <?php endif;?>

            <?php // @todo Not that elegant would be nice to group the params ?>
            <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
    || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam);?>

            <?php if ($useDefList && ($info == 0 || $info == 2)): ?>
            <?php echo LayoutHelper::render('joomla.content.info_block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
            <?php endif;?>
            <?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)): ?>
            <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
            <?php endif;?>

            <?php if (!$params->get('show_intro')): ?>
            <?php // Content is generated by content plugin event "onContentAfterTitle" ?>
            <?php echo $this->item->event->afterDisplayTitle; ?>
            <?php endif;?>

            <?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
            <?php echo $this->item->event->beforeDisplayContent; ?>

            <?php echo $this->item->introtext; ?>

            <?php if ($info == 1 || $info == 2): ?>
            <?php if ($useDefList): ?>
            <?php echo LayoutHelper::render('joomla.content.info_block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
            <?php endif;?>
            <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)): ?>
            <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
            <?php endif;?>
            <?php endif;?>

            <?php if ($params->get('show_readmore') && $this->item->readmore):
    if ($params->get('access-view')):
        $link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
    else:
        $menu = Factory::getApplication()->getMenu();
        $active = $menu->getActive();
        $itemId = $active->id;
        $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
        $link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
    endif;?>

	            <?php echo LayoutHelper::render('joomla.content.readmore', array('item' => $this->item, 'params' => $params, 'link' => $link)); ?>

	            <?php endif;?>

            <?php if ($isUnpublished): ?>
        </div>
        <?php endif;?>

        <?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
        <?php echo $this->item->event->afterDisplayContent; ?>
    </div>


    <?php if ($bufaccordion_mode == 'accordeon') : ?>
        </div>
    </div>
</div>
    <?php endif; ?>


</div>