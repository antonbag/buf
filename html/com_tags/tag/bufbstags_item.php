<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Tags\Site\Helper\RouteHelper;



/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('com_tags.tag-default');

// Get the user object.
$user = Factory::getUser();

// Check if user is allowed to add/edit based on tags permissions.
// Do we really have to make it so people can see unpublished tags???
$canEdit      = $user->authorise('core.edit', 'com_tags');
$canCreate    = $user->authorise('core.create', 'com_tags');
$canEditState = $user->authorise('core.edit.state', 'com_tags');
?>
<div class="com-tags__items">
    <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
        <?php if ($this->params->get('filter_field') || $this->params->get('show_pagination_limit')) : ?>
            <?php if ($this->params->get('filter_field')) : ?>
                <div class="com-tags-tags__filter btn-group">
                    <label class="filter-search-lbl visually-hidden" for="filter-search">
                        <?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>
                    </label>
                    <input
                        type="text"
                        name="filter-search"
                        id="filter-search"
                        value="<?php echo $this->escape($this->state->get('list.filter')); ?>"
                        class="inputbox" onchange="document.adminForm.submit();"
                        placeholder="<?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>"
                    >
                    <button type="submit" name="filter_submit" class="btn btn-primary"><?php echo Text::_('JGLOBAL_FILTER_BUTTON'); ?></button>
                    <button type="reset" name="filter-clear-button" class="btn btn-secondary"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
                </div>
            <?php endif; ?>
            <?php if ($this->params->get('show_pagination_limit')) : ?>
                <div class="btn-group float-end">
                    <label for="limit" class="visually-hidden">
                        <?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
                    </label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
            <?php endif; ?>

            <input type="hidden" name="limitstart" value="">
            <input type="hidden" name="task" value="">
        <?php endif; ?>
    </form>

    <?php if (empty($this->items)) : ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
            <?php echo Text::_('COM_TAGS_NO_ITEMS'); ?>
        </div>
    <?php else : ?>



<div class="buf_tag_card_wrapper clearfix">
        <?php

        /************************ */
        /************************ */
        /***intro******* */
        /************************ */


    $bsclass = '';
    /*OLD
    if($this->params->get('buf_bs_sm', 3)) $bsclass .=" col-sm-".$this->params->get('buf_bs_sm', 3);		
    if($this->params->get('buf_bs_md', 3)) $bsclass .=" col-md-".$this->params->get('buf_bs_md', 3);		
    if($this->params->get('buf_bs_lg', 3)) $bsclass .=" col-lg-".$this->params->get('buf_bs_lg', 3);		
    */

    if($this->params->get('buf_intro_bs_sm', 3)) $bsclass .=" row-cols-sm-".$this->params->get('buf_intro_bs_sm', 3);		
    if($this->params->get('buf_intro_bs_md', 3)) $bsclass .=" row-cols-md-".$this->params->get('buf_intro_bs_md', 3);		
    if($this->params->get('buf_intro_bs_lg', 3)) $bsclass .=" row-cols-lg-".$this->params->get('buf_intro_bs_lg', 3);
    if($this->params->get('buf_intro_bs_xl', 3)) $bsclass .=" row-cols-xl-".$this->params->get('buf_intro_bs_xl', 3);
    if($this->params->get('buf_intro_bs_gutter', 4)) $bsclass .=" g-".$this->params->get('buf_intro_bs_gutter', 4);

    $card_class = ($this->params->get('buf_intro_card_class', '') == '') ? '' : $this->params->get('buf_intro_card_class', '');

    if($this->params->get('buf_intro_card_layout', 'card-group') == 'card-group'){
        echo '<div class="buf_intro_card card-group">';
    }else{
        echo '<div class="buf_intro_card row row-cols-1 '.$bsclass.'">';
    }



?>


    <?php foreach ($this->items as $key => $item) : ?>

        <?php 
            $images = json_decode($item->core_images); 
            $buf_titles_h = $this->params->get('buf_titles_h', '0');
            $buf_before_display = $this->params->get('buf_before_display_content', '0');
            $buf_after_display = $this->params->get('buf_after_display_content', '0');
            $buf_show_readmore = $this->params->get('buf_show_readmore', '0');
            $buf_show_readmore_in_footer = $this->params->get('buf_show_readmore_in_footer', '0');
            //var_dump($buf_titles_h);
        ?>
       
        <div class="col <?php //echo $bsclass; ?>">
            <div class="card item <?php echo $card_class; ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">				

                <?php //IMAGES ?>
                <?php if ($this->params->get('tag_list_show_item_image', 1) == 1 && !empty($images->image_intro)) : ?>
                    <?php echo HTMLHelper::_('image', $images->image_intro, $images->image_intro_alt,['class' => 'card-img-top, img-fluid']); ?>
                <?php endif; ?>

                <?php //CARD-BODY ?>
                <div class="card-body">

                    <?php if (($item->type_alias === 'com_users.category') || ($item->type_alias === 'com_banners.category')) : ?>
                        <?php
                            if($buf_titles_h != 0){
                                echo '<h' . $buf_titles_h . ' >';
                                echo $this->escape($item->core_title);
                                echo '</h' . $buf_titles_h . '>';
                            }else{
                                echo $this->escape($item->core_title);
                            }
                        ?>

                    <?php else : ?>

                        <?php
                            if($buf_titles_h != 0){
                                echo '<h' . $buf_titles_h . ' >'; ?>
                                    <a href="<?php echo Route::_($item->link); ?>">
                                        <?php echo $this->escape($item->core_title); ?>
                                    </a>
                                <?php echo '</h' . $buf_titles_h . '>';
                            }else{?>
                                <a href="<?php echo Route::_($item->link); ?>">
                                    <?php echo $this->escape($item->core_title); ?>
                                </a>
                            <?php }
                        ?>

                    <?php endif; ?>


                    <?php // Content is generated by content plugin event "onContentAfterTitle" ?>
                    <?php
                    if($buf_before_display != 0){
                        echo $item->event->beforeDisplayContent;
                    } ?>


                    <?php if ($this->params->get('tag_list_show_item_description', 1)) : ?>
                        <span class="tag-body">
                            <?php echo HTMLHelper::_('string.truncate', $item->core_body, $this->params->get('tag_list_item_maximum_characters')); ?>
                        </span>
                    <?php endif; ?>

                    <?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
                    <?php
                    if($buf_after_display != 0){
                        echo $item->event->afterDisplayContent;
                    } ?>

                    <?php
                    if($buf_show_readmore != 0){

                        $this->params->set('link', Route::_($item->link));
                        $this->params->set('access-view', '1');
                        $this->params->set('show_readmore_title', $this->params->get('buf_show_readmore_title', true));
                        //$this->params->set('show_readmore_title', true);
                        $item->title = $this->escape($item->core_title);
                        $item->alternative_readmore = Text::_('JGLOBAL_READ_MORE_TITLE');
                        $item->alternative_readmore = false;

                        if($buf_show_readmore_in_footer == '0'){
                            $this->params->set('readmore_class', $this->params->get('buf_readmore_class', ''));
                            echo LayoutHelper::render('joomla.content.readmore', ['item' => $item, 'params' => $this->params, 'link' => $item->link]); 
                        }
                        
                    } ?>

                </div><?php //end card-body ?>

                <?php
                if($buf_show_readmore_in_footer == '1'){
                    echo '<div class="card-footer">';
                        $this->params->set('readmore_class', $this->params->get('buf_readmore_class', ''));
                        echo LayoutHelper::render('joomla.content.readmore', ['item' => $item, 'params' => $this->params, 'link' => $item->link]); 
                    echo '</div>';
                }?>


            </div><!-- end card -->
        </div><!-- end card wrapper -->

    <?php endforeach; ?>





</div>















                   
















        <ul class="com-tags-tag__category category list-group">
            <?php foreach ($this->items as $i => $item) : ?>
                <?php continue; ?>
                <?php if ($item->core_state == 0) : ?>
                    <li class="list-group-item-danger">
                <?php else : ?>
                    <li class="list-group-item list-group-item-action">
                <?php endif; ?>

                <?php if (($item->type_alias === 'com_users.category') || ($item->type_alias === 'com_banners.category')) : ?>
                    <h3>
                        <?php echo $this->escape($item->core_title); ?>
                    </h3>
                <?php else : ?>
                    <h3>
                        <a href="<?php echo Route::_($item->link); ?>">
                            <?php echo $this->escape($item->core_title); ?>
                        </a>
                    </h3>
                <?php endif; ?>
                <?php // Content is generated by content plugin event "onContentAfterTitle" ?>
                <?php echo $item->event->afterDisplayTitle; ?>
                <?php $images  = json_decode($item->core_images); ?>
                <?php if ($this->params->get('tag_list_show_item_image', 1) == 1 && !empty($images->image_intro)) : ?>
                    <a href="<?php echo Route::_(RouteHelper::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
                        <?php echo HTMLHelper::_('image', $images->image_intro, $images->image_intro_alt); ?>
                    </a>
                <?php endif; ?>
                <?php if ($this->params->get('tag_list_show_item_description', 1)) : ?>
                    <?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
                    <?php echo $item->event->beforeDisplayContent; ?>
                    <span class="tag-body">
                        <?php echo HTMLHelper::_('string.truncate', $item->core_body, $this->params->get('tag_list_item_maximum_characters')); ?>
                    </span>
                    <?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
                    <?php echo $item->event->afterDisplayContent; ?>
                <?php endif; ?>
                    </li>
            <?php endforeach; ?>
        </ul>


    <?php endif; ?>
</div>
