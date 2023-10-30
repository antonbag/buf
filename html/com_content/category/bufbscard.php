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

$htag    = $this->params->get('show_page_heading') ? 'h2' : 'h1';


?>
<div class="buf_bs_card <?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
		<h2> <?php echo $this->escape($this->params->get('page_subheading')); ?>
			<?php if ($this->params->get('show_category_title')) : ?>
				<span class="subheading-category"><?php echo $this->category->title; ?></span>
			<?php endif; ?>
		</h2>
	<?php endif; ?>
	<?php echo $afterDisplayTitle; ?>

	<?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
		<?php $this->category->tagLayout = new FileLayout('joomla.content.tags'); ?>
		<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
	<?php endif; ?>

	<?php if ($beforeDisplayContent || $afterDisplayContent || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
		<div class="category-desc clearfix">
			<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
				<img loading="lazy" src="<?php echo $this->category->getParams()->get('image'); ?>" alt="<?php echo htmlspecialchars($this->category->getParams()->get('image_alt'), ENT_COMPAT, 'UTF-8'); ?>"/>
			<?php endif; ?>
			<?php echo $beforeDisplayContent; ?>
			<?php if ($this->params->get('show_description') && $this->category->description) : ?>
				<?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
			<?php endif; ?>
			<?php echo $afterDisplayContent; ?>
		</div>
	<?php endif; ?>

	<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
		<?php if ($this->params->get('show_no_articles', 1)) : ?>
			<p><?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?></p>
		<?php endif; ?>
	<?php endif; ?>


<?php 

	/************************ */
	/************************ */
	/***LEADING******* */
	/************************ */


	$bsclass = '';
	/*OLD
	if($this->params->get('buf_bs_sm', 3)) $bsclass .=" col-sm-".$this->params->get('buf_bs_sm', 3);		
	if($this->params->get('buf_bs_md', 3)) $bsclass .=" col-md-".$this->params->get('buf_bs_md', 3);		
	if($this->params->get('buf_bs_lg', 3)) $bsclass .=" col-lg-".$this->params->get('buf_bs_lg', 3);		
	*/

	if($this->params->get('buf_leading_bs_sm', 3)) $bsclass .=" row-cols-sm-".$this->params->get('buf_leading_bs_sm', 3);		
	if($this->params->get('buf_leading_bs_md', 3)) $bsclass .=" row-cols-md-".$this->params->get('buf_leading_bs_md', 3);		
	if($this->params->get('buf_leading_bs_lg', 3)) $bsclass .=" row-cols-lg-".$this->params->get('buf_leading_bs_lg', 3);
	if($this->params->get('buf_leading_bs_xl', 3)) $bsclass .=" row-cols-xl-".$this->params->get('buf_leading_bs_xl', 3);
	if($this->params->get('buf_leading_bs_gutter', 4)) $bsclass .=" g-".$this->params->get('buf_leading_bs_gutter', 4);

	$card_class = ($this->params->get('buf_leading_card_class', '') == '') ? '' : $this->params->get('buf_leading_card_class', '');

	if($this->params->get('buf_leading_card_layout', 'card-group') == 'card-group'){
		echo '<div class="buf_leading_card card-group">';
	}else{
		echo '<div class="buf_leading_card row row-cols-1 '.$bsclass.'">';
	}

?>




	<?php if (!empty($this->lead_items)) : ?>
		
			<?php foreach ($this->lead_items as &$item) : ?>

				<div class="col <?php //echo $bsclass; ?>">
					<div class="card item-leading <?php echo $card_class; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?><?php echo $item->featured == 1 ? ' card-featured' : null; ?>"
					 itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
						<?php
						$this->item = &$item;
						echo $this->loadTemplate('item');
						?>
					</div>
			    </div><!-- end items-leading -->

			<?php endforeach; ?>
		
	<?php endif; ?>

	<?php
	$introcount = count($this->intro_items);
	$counter = 0;
	?>

</div>

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



	<?php if (!empty($this->intro_items)) : ?>
		<?php foreach ($this->intro_items as $key => &$item) : ?>
			
			<div class="col <?php //echo $bsclass; ?>">
				<div class="card item <?php echo $card_class; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?><?php echo $item->featured == 1 ? ' card-featured' : null; ?>" 
				itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">				
					<?php
						$this->item = &$item;
						echo $this->loadTemplate('item');
					?>
				</div><!-- end card -->
			</div><!-- end card wrapper -->

		<?php endforeach; ?>
	<?php endif; ?>


</div>

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
			<?php echo $this->loadTemplate('children'); ?> </div>
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
