<?php
/**
 * @package     Falang for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2011-2021. Faboba.com All rights reserved.
 */

// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<div class="mod-languages buf_bs_languages">

    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php echo Text::_("JFIELD_LANGUAGE_LABEL"); ?>
        </button>

        <ul class="dropdown-menu <?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block';?>">
                <?php foreach($list as $language):?>
                    <?php if ($params->get('show_active', 0) || !$language->active):?>
                        <li class="dropdown-item <?php echo $language->active ? 'lang-active' : '';?>" dir="<?php echo $language->rtl ? 'rtl' : 'ltr' ?>">
                            <?php if ($language->display) { ?>
                                <a href="<?php echo $language->link;?>">
                                    <?php if ($params->get('image', 1)):?>
                                    <?php echo HTMLHelper::_('image', $imagesPath.$language->image.'.'.$imagesType, $language->title_native, array('title'=>$language->title_native), $relativePath);?>
                                    <?php endif; ?>
                                    <?php if ($params->get('show_name', 1)):?>
                                    <?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);?>
                                    <?php endif; ?>
                                    <?php if($language->active){?> <i class="fa-solid fa-check lang_checked"></i> <?php } ?>
                                </a>
                            <?php } else { ?>
                                <?php if ($params->get('image', 1)):?>
                                    <?php echo HTMLHelper::_('image', $imagesPath.$language->image.'.'.$imagesType, $language->title_native, array('title'=>$language->title_native,'style'=>'opacity:0.5'), $relativePath);?>
                                <?php else : ?>
                                    <?php if ($params->get('show_name', 1)):?>
                                    <?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);?>
                                    <?php endif; ?>
                                    <?php if($language->active){?> <i class="fa-solid fa-check lang_checked"></i> <?php } ?>
                                <?php endif; ?>
                            <?php } ?>
                        </li>
                    <?php endif;?>
                <?php endforeach;?>
        </ul>
    </div>



</div>