<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;

// Create a shortcut for params.
$params = $this->item->params;
$canEdit = $this->item->params->get('access-edit');
$info = $params->get('info_block_position', 0);

//Check if modal
$buf_leading_modal = $params->get('buf_leading_img_modal', 1);
$buf_intro_modal = $params->get('buf_intro_img_modal', 1);

$buf_isModal = true;
if ($this->item->isleading && !$buf_leading_modal) {
    $buf_isModal = false;
}
if (!$this->item->isleading && !$buf_intro_modal) {
    $buf_isModal = false;
}

//Check DATE
$buf_showDate = true;
$buf_leading_date = $params->get('bs_leading_show_publish_date', 1);
$buf_intro_date = $params->get('bs_intro_show_publish_date', 1);

if ($this->item->isleading && !$buf_leading_date) {
    $buf_showDate = false;
}
if (!$this->item->isleading && !$buf_intro_date) {
    $buf_showDate = false;
}

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));
$currentDate = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
    || ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);

?>

<?php

$buf_img_pos = ($this->item->isleading) ? $params->get('buf_leading_img_pos', 'top') : $params->get('buf_intro_img_pos', 'top');

/* $images = json_decode($this->item->images);
$imageUrl = !empty($images->image_intro) ? $images->image_intro : '';
$images->image_intro_alt = !empty($images->image_intro_alt) ? $images->image_intro_alt : $this->item->title;
$imageCaption = !empty($images->image_intro_alt) ? $images->image_intro_alt : $this->item->title;

$this->item->images = json_encode($images);


 */


$images = new Registry(json_decode($this->item->images, true));
$imageUrl = $images->get('image_intro', '');
$images->set('image_intro_alt', $images->get('image_intro_alt', $this->item->title));
$images->set('image_intro_caption', $images->get('image_intro_caption', ''));

$this->item->images = json_encode($images->toArray());

$areImages = true;

if ($imageUrl == '') {
    $buf_img_pos = 'top';
    $areImages = false;
}

?>


<?php if ($buf_img_pos == 'left' || $buf_img_pos == 'right') : ?>
    <div class="row g-0">
<?php endif; ?>



    <?php if ($buf_img_pos == 'left') : ?>
        <div class="col-md-4">
    <?php endif; ?>

        <?php //IMAGE top or left
        ?>
        <?php if (($buf_img_pos == 'left' || $buf_img_pos == 'top') && $areImages) : ?>
            <div class="bs_card_imgwrapper h-100 text-center">
                <?php if ($buf_isModal) : ?>
                <button class="p-0 border-0" data-bs-toggle="modal" data-bs-target="#bufbsmodal<?php echo $this->item->id; ?>" aria-labelledby="bufbsLabel<?php echo $this->item->id; ?>">
                    <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item, null, ['class' => 'img-fluid']); ?>
                </button>
                <!-- Modals -->
                <div class="modal fade" id="bufbsmodal<?php echo $this->item->id; ?>" data-bs-keyboard="false" tabindex="-1" aria-labelledby="bufbsLabel<?php echo $this->item->id; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">

                        <div class="modal-content">

                            <div class="modal-body text-center">
                                <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item, null, ['class' => 'img-fluid']); ?>
                            </div>
                            <div class="modal-footer">

                                <p id="bufbsLabel<?php echo $this->item->id; ?>">
                                    <?Php echo $images->get('image_intro_caption', $this->item->title); ?>
                                </p>

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
            
                <?php else : ?>
                    <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item, null, ['class' => 'img-fluid']); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($buf_img_pos == 'left') : ?>
        </div>
        <?php endif; ?>


    <?php if ($buf_img_pos == 'left' || $buf_img_pos == 'right') : ?>
        <div class="col-md-8">
    <?php endif; ?>
        <?php //BODY
        ?>
        <div class="card-body">
            <?php if ($isUnpublished) : ?>
                <div class="system-unpublished">
            <?php endif; ?>

                <?php //echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>
                <?php echo LayoutHelper::render('buf.content.bufbscard_item_title', $this->item); ?>

                <?php if ($canEdit) : ?>
                    <?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item]); ?>
                <?php endif; ?>

                <?php // @todo Not that elegant would be nice to group the params
                ?>
                <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
                    || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam); ?>

                <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
                    <?php //echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'above']);
                    ?>
                <?php endif; ?>


                <div class="row">
                    <div class="col">

                        <?php if ($buf_showDate) : ?>
                            <small class="buf_bscard_date">
                                <i class="fad fa-calendar-day"></i> <?php $date = date_create($this->item->publish_up);
                                    echo date_format($date, "d/m/Y"); ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="col text-end">
                        <?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
                            <div class="buf_cat_wrapper">
                                <?php if ($this->params->get('link_category', 1)) : ?>
                                    <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($this->item->catid)); ?>">
                                        <span class="buf_cat_bagde">
                                            <i class="fad fa-folder-open"></i> <?php echo $this->item->category_title; ?>
                                        </span>
                                    </a>
                                <?php else : ?>
                                    <span class="buf_cat_bagde">
                                        <i class="fad fa-folder-open"></i> <?php echo $this->item->category_title; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
                    <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
                <?php endif; ?>

                <?php if (!$params->get('show_intro')) : ?>
                    <?php // Content is generated by content plugin event "onContentAfterTitle"
                    ?>
                    <?php echo $this->item->event->afterDisplayTitle; ?>
                <?php endif; ?>

                <?php // Content is generated by content plugin event "onContentBeforeDisplay"
                ?>

                <?php
                //DISPLAY BEFORE CONTENT
                if ($this->item->isleading && $params->get('buf_leading_before_display_content', '0')) {
                    echo $this->item->event->beforeDisplayContent;
                } elseif (!$this->item->isleading && $params->get('buf_before_display_content', '0')) {
                    echo $this->item->event->beforeDisplayContent;
                }

                ?>

                <?php

                //DISPLAY BEFORE CONTENT
                if ($this->item->isleading && $params->get('buf_leading_display_content', '0')) {
                    echo $this->item->introtext;
                } elseif (!$this->item->isleading && $params->get('buf_display_content', '0')) {
                    echo $this->item->introtext;
                }
                ?>

                <?php if ($info == 1 || $info == 2) : ?>
                    <?php if ($useDefList) : ?>
                        <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'below']); ?>
                    <?php endif; ?>
                    <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
                        <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($params->get('show_readmore') && $this->item->readmore) :
                    if ($params->get('access-view')) :
                        $link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
                    else :
                        $menu = Factory::getApplication()->getMenu();
                        $active = $menu->getActive();
                        $itemId = $active->id;
                        $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
                        $link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
                    endif; ?>

                    <?php echo LayoutHelper::render('joomla.content.readmore', ['item' => $this->item, 'params' => $params, 'link' => $link]); ?>

                <?php endif; ?>

                <?php if ($isUnpublished) : ?>
                </div>
                <?php endif; ?>
            <?php
            //Content is generated by content plugin event "onContentAfterDisplay"
            ?>
            <?php

            //DISPLAY BEFORE CONTENT
            if ($this->item->isleading && $params->get('buf_leading_after_display_content', '0')) {
                echo $this->item->event->afterDisplayContent;
            } elseif (!$this->item->isleading && $params->get('buf_after_display_content', '0')) {
                echo $this->item->event->afterDisplayContent;
            }
            ?>

        </div>

        <?php if ($buf_img_pos == 'left' || $buf_img_pos == 'right') : ?>
        </div>
        <?php endif; ?>


    <?php if ($buf_img_pos == 'right') : ?>
        <div class="col-md-4" style="">
    <?php endif; ?>

        <?php //image top or left?>
        <?php if ($buf_img_pos == 'right' && $areImages) : ?> 
            <div class="bs_card_imgwrapper h-100 text-center">
                <?php if ($buf_isModal) : ?>
                    <button  class="p-0 border-0" data-bs-toggle="modal" data-bs-target="#bufbsmodal<?php echo $this->item->id; ?>" aria-labelledby="bufbsLabel<?php echo $this->item->id; ?>">
                        <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
                    </button>
                    <!-- Modals -->
                    <div class="modal fade" id="bufbsmodal<?php echo $this->item->id; ?>" data-bs-keyboard="false" tabindex="-1" aria-labelledby="bufbsLabel<?php echo $this->item->id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">

                            <div class="modal-content">

                                <div class="modal-body text-center">
                                    <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
                                    
                                </div>
                                <div class="modal-footer">

                                    <p id="bufbsLabel<?php echo $this->item->id; ?>">
                                        <?Php echo $images->get('image_intro_caption', $this->item->title); ?>
                                    </p>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                
                <?php else : ?>
                    <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php if ($buf_img_pos == 'right') : ?>
    </div>
    <?php endif; ?>


<?php if ($buf_img_pos == 'left' || $buf_img_pos == 'right') : ?>
</div>
<?php endif; ?>