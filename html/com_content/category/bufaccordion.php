<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content accordion
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;

$app = Factory::getApplication();

$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));

$htag = $this->params->get('show_page_heading') ? 'h2' : 'h1';

?>

<div class="blog_bufaccordion blog" itemscope itemtype="https://schema.org/Blog">


    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
        </div>
    <?php endif; ?>


    <?php if ($this->params->get('show_category_title', 1)) : ?>
        <<?php echo $htag; ?>>
            <?php echo $this->category->title; ?>
        </<?php echo $htag; ?>>
    <?php endif; ?>
    <?php echo $afterDisplayTitle; ?>

    <?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
        <?php $this->category->tagLayout = new FileLayout('joomla.content.tags'); ?>
        <?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
    <?php endif; ?>

    <?php if ($beforeDisplayContent || $afterDisplayContent || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
        <div class="category-desc clearfix">
            <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
                <?php echo LayoutHelper::render(
                    'joomla.html.image',
                    [
                        'src' => $this->category->getParams()->get('image'),
                        'alt' => empty($this->category->getParams()->get('image_alt')) && empty($this->category->getParams()->get('image_alt_empty')) ? false : $this->category->getParams()->get('image_alt'),
                    ]
                ); ?>
            <?php endif; ?>
            <?php echo $beforeDisplayContent; ?>
            <?php if ($this->params->get('show_description') && $this->category->description) : ?>
                <?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
            <?php endif; ?>
            <?php echo $afterDisplayContent; ?>
        </div>
    <?php endif; ?>


    <?php

    $this->show_image = $this->params->get('bufaccordion_show_image', 'hidden');
    $this->bufaccordion_beforeDisplayContent = $this->params->get('bufaccordion_beforeDisplayContent', 0);
    $this->bufaccordion_afterDisplayContent = $this->params->get('bufaccordion_afterDisplayContent', 0);
    ?>







    <?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
        <?php if ($this->params->get('show_no_articles', 1)) : ?>
            <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span><span
                    class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                <?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>


    <?php $leadingcount = 0; ?>
    <?php if (!empty($this->lead_items)) : ?>
        <!-- <div class="com-content-category-blog__items bufaccordion_wrapper accordion <?php //echo $this->params->get('blog_class_leading');
                                                                                            ?>" id="bufaccordion_leading"> -->

        <?php $this->current_accordion = 'bufaccordion_leading';
        echo HTMLHelper::_('bootstrap.startAccordion', $this->current_accordion, array('active' => 'bufaccordion')); ?>
        <?php foreach ($this->lead_items as &$item) : ?>
            <?php
            // Adding slides
            //echo HTMLHelper::_('bootstrap.addSlide','bufaccordion_leading', $item->id, $item->id);
            ?>

            <!-- <div class="card com-content-category-blog__item blog-item" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting"> -->
            <?php
            $this->item = &$item;
            echo $this->loadTemplate('item');
            ?>
            <!-- </div> -->
            <?php $leadingcount++; ?>

            <?php //echo HTMLHelper::_('bootstrap.endSlide');
            ?>

        <?php endforeach; ?>


        <?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
        <!-- </div> -->
    <?php endif; ?>

    <?php
    $introcount = count($this->intro_items);
    $counter = 0;
    ?>

    <?php if (!empty($this->intro_items)) : ?>
        <?php $blogClass = $this->params->get('blog_class', ''); ?>
        <?php if ((int) $this->params->get('num_columns') > 1) : ?>
            <?php $blogClass .= (int) $this->params->get('multi_column_order', 0) === 0 ? ' masonry-' : ' columns-'; ?>
            <?php $blogClass .= (int) $this->params->get('num_columns'); ?>
        <?php endif; ?>
        <!-- <div class="com-content-category-blog__items bufaccordion_wrapper accordion blog-items <?php //echo $blogClass;
                                                                                                    ?>" id="bufaccordion_regular"> -->
        <?php
        $this->current_accordion = 'bufaccordion_regular';
        echo HTMLHelper::_('bootstrap.startAccordion', $this->current_accordion, array('active' => 'bufaccordion')); ?>
        <?php foreach ($this->intro_items as $key => &$item) : ?>

            <?php
            $this->item = &$item;
            echo $this->loadTemplate('item');
            ?>

        <?php endforeach; ?>
        <?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
        <!-- </div> -->
    <?php endif; ?>




    <?php if (!empty($this->link_items)) : ?>
        <div class="items-more">
            <?php echo $this->loadTemplate('links'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
        <div class="com-content-category-blog__children cat-children">
            <?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
                <h3> <?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?> </h3>
            <?php endif; ?>
            <?php echo $this->loadTemplate('children'); ?>
        </div>
    <?php endif; ?>
    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
        <div class="com-content-category-blog__navigation w-100">
            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="com-content-category-blog__counter counter float-end pt-3 pe-2">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php endif; ?>
            <div class="com-content-category-blog__pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- /ACCORDION WRAPPER -->
<!-- </div> -->