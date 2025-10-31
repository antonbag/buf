<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Check if the view is determined by a single tag in the request
$isSingleTag = count($this->item) === 1;
$tag         = $isSingleTag ? $this->item[0] : null;
$htag        = $this->params->get('show_page_heading') ? 'h2' : 'h1';

?>
<div class="mt-4 com-tags-tag tag-category buf_bs_tags<?php echo $this->pageclass_sfx; ?>">

    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
    <?php endif; ?>

    <?php // Display Tag Title 
    ?>
    <?php if ($this->params->get('show_tag_title', 1)) : ?>
        <<?php echo $htag; ?> class="com-tags-tag__title page-title">
            <?php // $this->tags_title is already HTML-prepared (e.g., linked tags) 
            ?>
            <?php echo $this->tags_title; ?>
        </<?php echo $htag; ?>>
    <?php endif; ?>

    <?php // Display Description and Image for a SINGLE Tag 
    ?>
    <?php if ($isSingleTag && ($this->params->get('tag_list_show_tag_image', 1) || $this->params->get('tag_list_show_tag_description', 1))) : ?>
        <?php $images = $tag->images ? json_decode($tag->images) : null; ?>
        <div class="com-tags-tag__description category-desc">
            <?php // Tag Image 
            ?>
            <?php if ($this->params->get('tag_list_show_tag_image', 1) && !empty($images->image_fulltext)) : ?>
                <div class="com-tags-tag__image float-end item-image"> <?php // Added class 
                                                                        ?>
                    <?php echo HTMLHelper::_('image', $images->image_fulltext, $images->image_fulltext_alt ?: $tag->title, ['itemprop' => 'image']); ?>
                </div>
            <?php endif; ?>

            <?php // Tag Description 
            ?>
            <?php if ($this->params->get('tag_list_show_tag_description', 1) && $tag->description) : ?>
                <div class="com-tags-tag__text"> <?php // Added class 
                                                    ?>
                    <?php echo HTMLHelper::_('content.prepare', $tag->description, '', 'com_tags.tag.' . $tag->id); ?>
                </div>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>

    <?php // Display Description and Image from MENU ITEM PARAMS (shown only when NOT a single tag view, based on original logic) 
    ?>
    <?php if (!$isSingleTag && ($this->params->get('tag_list_description') || ($this->params->get('show_description_image', 1) && $this->params->get('tag_list_image')))) : ?>
        <div class="com-tags-tag__description category-desc">
            <?php // Menu Item Param Image 
            ?>
            <?php if ($this->params->get('show_description_image', 1) && $this->params->get('tag_list_image')) : ?>
                <div class="com-tags-tag__image float-end item-image"> <?php // Added class 
                                                                        ?>
                    <?php $imgAlt = $this->params->get('tag_list_image_alt_empty') ? '' : $this->params->get('tag_list_image_alt'); ?>
                    <?php echo HTMLHelper::_('image', $this->params->get('tag_list_image'), $imgAlt, ['itemprop' => 'image']); ?>
                </div>
            <?php endif; ?>

            <?php // Menu Item Param Description 
            ?>
            <?php if ($this->params->get('tag_list_description')) : ?>
                <div class="com-tags-tag__text"> <?php // Added class 
                                                    ?>
                    <?php echo HTMLHelper::_('content.prepare', $this->params->get('tag_list_description'), '', 'com_tags.tag.menuitem'); ?>
                </div>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>

    <?php // Load the template to display the list of tagged items 
    ?>
    <?php echo $this->loadTemplate('item'); ?>

    <?php // Pagination 
    ?>
    <?php if (($this->params->get('show_pagination', 2) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
        <div class="com-tags-tag__pagination pagination-wrap w-100"> <?php // Added class 
                                                                        ?>
            <?php if ($this->params->get('show_pagination_results', 1)) : ?>
                <p class="com-tags-tag__pagination-counter counter float-end pt-3 pe-2"> <?php // Added class 
                                                                                            ?>
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php endif; ?>
            <div class="com-tags-tag__pagination-links pagination"> <?php // Added class 
                                                                    ?>
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>

</div>