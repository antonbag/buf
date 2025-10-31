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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Tags\Site\Helper\RouteHelper; // Although Route::_($item->link) is usually sufficient now

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('com_tags.tag-default'); // For filter JS, if needed

// Get the user object (usually not needed in frontend display unless checking permissions)
// $user = Factory::getUser();

// Params often used
$params = $this->params;
$showDescription = $params->get('tag_list_show_item_description', 1);
$maxChars = $params->get('tag_list_item_maximum_characters');
$showImage = $params->get('tag_list_show_item_image', 1);

// Custom BUF Params
$bufTitlesH = $params->get('buf_titles_h', '0'); // String '0' to '6'
$bufBeforeDisplay = $params->get('buf_before_display_content', '0');
$bufAfterDisplay = $params->get('buf_after_display_content', '0');
$bufShowReadmore = $params->get('buf_show_readmore', '1'); // Default to 1 maybe?
$bufShowReadmoreTitle = $params->get('buf_show_readmore_title', '0');
$bufShowReadmoreInFooter = $params->get('buf_show_readmore_in_footer', '0');
$bufReadmoreClass = $params->get('buf_readmore_class', '');
$cardLayout = $params->get('buf_intro_card_layout', 'grid');
$cardClass = $params->get('buf_intro_card_class', '');

?>
<div class="com-tags__items items"> <?php // Added items class 
                                    ?>
    <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="com-tags__filter-form">
        <?php if ($params->get('filter_field') || $params->get('show_pagination_limit')) : ?>
            <div class="com-tags__filter-bar filter-bar row mb-3">
                <?php if ($params->get('filter_field')) : ?>
                    <div class="col-md-8 com-tags__filter-search filter-search">
                        <div class="input-group">
                            <label class="input-group-text visually-hidden" for="filter-search">
                                <?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>
                            </label>
                            <input
                                type="text"
                                name="filter-search"
                                id="filter-search"
                                value="<?php echo $this->escape($this->state->get('list.filter')); ?>"
                                class="form-control"
                                onchange="document.adminForm.submit();"
                                placeholder="<?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>"
                                aria-label="<?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>">
                            <button type="submit" name="filter_submit" class="btn btn-primary"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
                            <button type="button" name="filter-clear-button" class="btn btn-secondary" onclick="document.getElementById('filter-search').value='';this.form.submit();"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($params->get('show_pagination_limit')) : ?>
                    <div class="col-md-4 com-tags__filter-limit filter-limit ms-auto text-end">
                        <label for="limit" class="visually-hidden">
                            <?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
                        </label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                <?php endif; ?>
            </div>
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
        <?php
        // Prepare card container classes
        $containerClasses = ['buf_tag_card_wrapper'];
        $bsClass = '';

        if ($cardLayout === 'grid') {
            $containerClasses[] = 'row row-cols-1'; // Base classes for grid
            if ($params->get('buf_intro_bs_sm')) $bsClass .= " row-cols-sm-" . $params->get('buf_intro_bs_sm');
            if ($params->get('buf_intro_bs_md')) $bsClass .= " row-cols-md-" . $params->get('buf_intro_bs_md');
            if ($params->get('buf_intro_bs_lg')) $bsClass .= " row-cols-lg-" . $params->get('buf_intro_bs_lg');
            if ($params->get('buf_intro_bs_xl')) $bsClass .= " row-cols-xl-" . $params->get('buf_intro_bs_xl');
            if ($params->get('buf_intro_bs_gutter')) $bsClass .= " g-" . $params->get('buf_intro_bs_gutter');
            $containerClasses[] = trim($bsClass);
        } else { // card-group
            $containerClasses[] = 'card-group';
        }
        ?>
        <div class="<?php echo implode(' ', array_filter($containerClasses)); ?>">

            <?php foreach ($this->items as $key => $item) : ?>
                <?php
                $images = $item->core_images ? json_decode($item->core_images) : null;
                $itemLink = Route::_($item->link); // Generate route once
                // Check if item is just a category title (not linkable in the same way)
                $isCategoryType = in_array($item->type_alias, ['com_users.category', 'com_banners.category']);
                ?>
                <div class="col <?php // Individual 'col' is enough when using row-cols-* on parent 
                                ?>">
                    <div class="card item mb-3 <?php echo $cardClass; // Add custom class from params 
                                                ?>" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting"> <?php // Consider changing schema type if needed 
                                                                                                                                ?>

                        <?php // === Card Image === 
                        ?>
                        <?php if ($showImage && !empty($images->image_intro)) : ?>
                            <?php if (!$isCategoryType) : ?><a href="<?php echo $itemLink; ?>"><?php endif; ?>
                                <?php echo HTMLHelper::_('image', $images->image_intro, $images->image_intro_alt ?: $item->core_title, ['class' => 'card-img-top img-fluid', 'itemprop' => 'image']); // Corrected class 
                                ?>
                                <?php if (!$isCategoryType) : ?></a><?php endif; ?>
                        <?php endif; ?>

                        <?php // === Card Body === 
                        ?>
                        <div class="card-body d-flex flex-column"> <?php // flex-column helps footer alignment 
                                                                    ?>

                            <?php // === Title === 
                            ?>
                            <?php if ($isCategoryType) : // Non-linkable title 
                            ?>
                                <?php if ($bufTitlesH !== '0') : ?>
                                    <h<?php echo $bufTitlesH; ?> class="card-title item-title" itemprop="headline"><?php echo $this->escape($item->core_title); ?></h<?php echo $bufTitlesH; ?>>
                                <?php else : ?>
                                    <div class="card-title item-title buf-title-none" itemprop="headline"><?php echo $this->escape($item->core_title); ?></div> <?php // Use a div for 'None' 
                                                                                                                                                                ?>
                                <?php endif; ?>
                            <?php else : // Linkable title 
                            ?>
                                <?php if ($bufTitlesH !== '0') : ?>
                                    <h<?php echo $bufTitlesH; ?> class="card-title item-title" itemprop="headline">
                                        <a href="<?php echo $itemLink; ?>" itemprop="url">
                                            <?php echo $this->escape($item->core_title); ?>
                                        </a>
                                    </h<?php echo $bufTitlesH; ?>>
                                <?php else : ?>
                                    <div class="card-title item-title buf-title-none" itemprop="headline"> <?php // Use a div for 'None' 
                                                                                                            ?>
                                        <a href="<?php echo $itemLink; ?>" itemprop="url">
                                            <?php echo $this->escape($item->core_title); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php // === Before Display Content (Plugins) === 
                            ?>
                            <?php if ($bufBeforeDisplay === '1') : ?>
                                <?php echo $item->event->beforeDisplayContent; ?>
                            <?php endif; ?>

                            <?php // === Description (Introtext) === 
                            ?>
                            <?php if ($showDescription && !empty($item->core_body)) : ?>
                                <div class="card-text item-introtext" itemprop="description">
                                    <?php echo HTMLHelper::_('string.truncate', $item->core_body, $maxChars); ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-auto"> <?php // Push content below (like footer) to the bottom 
                                                    ?>
                                <?php // === After Display Content (Plugins) === 
                                ?>
                                <?php if ($bufAfterDisplay === '1') : ?>
                                    <?php echo $item->event->afterDisplayContent; ?>
                                <?php endif; ?>

                                <?php // === Read More (outside footer) === 
                                ?>

                            </div>

                        </div><?php // end card-body 
                                ?>

                        <?php // === Card Footer (Optional Read More) === 
                        ?>

<?php if ($bufShowReadmore === '1' && $bufShowReadmoreInFooter === '1' && !$isCategoryType) : ?>
                                    <div class="card-footer">
                                        <?php
                                        // Prepare params specifically for the readmore layout
                                        $readmoreParams = clone $params; // Clone to avoid modifying original view params object
                                        $readmoreParams->set('access-view', true); // Ensure layout knows item is accessible
                                        $readmoreParams->set('show_readmore_title', (bool) $bufShowReadmoreTitle); // Use core param name expected by layout
                                        $readmoreParams->set('readmore_class', $bufReadmoreClass);
                                        // Link needs to be passed separately if not automatically derived from $item->readmore_link by the layout

                                        // *** AÑADIR ESTA LÍNEA ***
                                        if (!isset($item->alternative_readmore)) {
                                            $item->alternative_readmore = null;
                                        }

                                        ?>
                                        <p class="com-tags__item-readmore readmore m-0"> <?php // Added class, remove margin 
                                                                                            ?>
                                            <?php // Pass the original $item and the cloned/modified $readmoreParams 
                                            ?>
                                            <?php echo LayoutHelper::render('joomla.content.readmore', ['item' => $item, 'params' => $readmoreParams, 'link' => $itemLink]); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>

                    </div><!-- end card -->
                </div><!-- end col wrapper -->
            <?php endforeach; ?>

        </div><!-- end card container -->
    <?php endif; ?>
</div>