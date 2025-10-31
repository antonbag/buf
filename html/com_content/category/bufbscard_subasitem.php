<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$lang   = $this->getLanguage();
$user   = Factory::getUser();
$groups = $user->getAuthorisedViewLevels();


if (isset($this->isChildSubCategory)) : ?>
    <?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
        <div class="subcat-title me-2"><a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
            <?php echo $this->escape($child->title); ?></a>
            <?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
                <span class="badge bg-info">
                    <?php //echo Text::_('COM_CONTENT_NUM_ITEMS'); ?>
                    <?php echo $child->getNumItems(true); ?>
                </span>
            <?php endif; ?>

            <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                <a href="#category-<?php echo $child->id; ?>" data-bs-toggle="collapse" class="btn btn-sm float-end" aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"><span class="icon-plus" aria-hidden="true"></span></a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

<?php return; ?>

<?php else : ?>

<?php endif; ?>


<?php 


$bsclass = '';
if ($this->params->get('buf_intro_bs_sm', 3)) {
    $bsclass .= " row-cols-sm-" . $this->params->get('buf_intro_bs_sm', 3);
}

if ($this->params->get('buf_intro_bs_md', 3)) {
    $bsclass .= " row-cols-md-" . $this->params->get('buf_intro_bs_md', 3);
}

if ($this->params->get('buf_intro_bs_lg', 3)) {
    $bsclass .= " row-cols-lg-" . $this->params->get('buf_intro_bs_lg', 3);
}

if ($this->params->get('buf_intro_bs_xl', 3)) {
    $bsclass .= " row-cols-xl-" . $this->params->get('buf_intro_bs_xl', 3);
}

if ($this->params->get('buf_intro_bs_gutter', 4)) {
    $bsclass .= " g-" . $this->params->get('buf_intro_bs_gutter', 4);
}

if(isset($this->isChildSubCategory)) $bsclass = '';

$card_class = ($this->params->get('buf_intro_card_class', '') == '') ? '' : $this->params->get('buf_intro_card_class', '');

if ($this->params->get('buf_intro_card_layout', 'card-group') == 'card-group') {
    echo '<div class="buf_intro_card card-group">';
} else {
    echo '<div class="buf_intro_card row row-cols-1 ' . $bsclass . '">';
}


if ($this->maxLevel != 0 && count($this->children[$this->category->id]) > 0) : ?>




    <?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
        <?php // Check whether category access level allows access to subcategories. ?>
        <?php if (in_array($child->access, $groups)) : ?>
            <?php if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) : ?>
                <div class="com-content-category-blog__child col">

                    <?php if(isset($this->isChildSubCategory)) : ?>
                        <div class="card-child">
                        
                    <?php else : ?>
                        <div class="card <?php echo $card_class; ?>">


                        <?php if ($child->getParams()->get('image')) : ?>
                            <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
                            <?php echo LayoutHelper::render(
                                'joomla.html.image',
                                [
                                    'src' => $child->getParams()->get('image'),
                                    'alt' => empty($child->getParams()->get('image_alt')) && empty($child->getParams()->get('image_alt_empty')) ? false : $child->getParams()->get('image_alt'),
                                    'class' => $child->getParams()->get('class').' card-img-top img-fluid'
                                ]
                            ); ?>
                            </a>
                        <?php endif; ?>

                    <?php endif; ?>
                    
                    <?php if(isset($this->isChildSubCategory)) : ?>
                          <div class="card-body-child row">

                          <h3 class="page-header item-title"><a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
                                <?php echo $this->escape($child->title); ?></a>
                                <?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
                                    <span class="badge bg-info">
                                        <?php //echo Text::_('COM_CONTENT_NUM_ITEMS'); ?>
                                        <?php echo $child->getNumItems(true); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                                    <a href="#category-<?php echo $child->id; ?>" data-bs-toggle="collapse" class="btn btn-sm float-end" aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"><span class="icon-plus" aria-hidden="true"></span></a>
                                <?php endif; ?>
                            </h3>

                    <?php else : ?>
                            <div class="card-body">

                            <?php if ($lang->isRtl()) : ?>
                            <h3 class="page-header item-title">
                                <?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
                                    <span class="badge bg-info tip">
                                        <?php echo $child->getNumItems(true); ?>
                                    </span>
                                <?php endif; ?>
                                <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
                                <?php echo $this->escape($child->title); ?></a>

                                <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                                    <a href="#category-<?php echo $child->id; ?>" data-bs-toggle="collapse" class="btn btn-sm float-end" aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"><span class="icon-plus" aria-hidden="true"></span></a>
                                <?php endif; ?>
                            </h3>
                            <?php else : ?>
                            <h3 class="page-header item-title"><a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
                                <?php echo $this->escape($child->title); ?></a>
                                <?php if ($this->params->get('show_cat_num_articles', 1)) : ?>
                                    <span class="badge bg-info">
                                        <?php //echo Text::_('COM_CONTENT_NUM_ITEMS'); ?>
                                        <?php echo $child->getNumItems(true); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                                    <a href="#category-<?php echo $child->id; ?>" data-bs-toggle="collapse" class="btn btn-sm float-end" aria-label="<?php echo Text::_('JGLOBAL_EXPAND_CATEGORIES'); ?>"><span class="icon-plus" aria-hidden="true"></span></a>
                                <?php endif; ?>
                            </h3>
                            <?php endif; ?>


                    <?php endif; ?>



                            <?php if ($this->params->get('show_subcat_desc') == 1) : ?>
                                <?php if ($child->description) : ?>
                                <div class="com-content-category-blog__description category-desc">
                                    <?php echo HTMLHelper::_('content.prepare', $child->description, '', 'com_content.category'); ?>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
        

                            <?php if ($this->maxLevel > 1 && count($child->getChildren()) > 0) : ?>
                                <div class="com-content-category-blog__children d-flex flex-wrap" id="category-<?php echo $child->id; ?>">
                                        <?php
                                        $this->isChildSubCategory = true;
                                        $this->children[$child->id] = $child->getChildren();
                                        $this->category = $child;
                                        $this->maxLevel--;
                                        echo $this->loadTemplate('subasitem');
                                        $this->isChildSubCategory = null;
                                        $this->category = $child->getParent();
                                        $this->maxLevel++;
                                        ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>

<?php endif;

?>

</div>