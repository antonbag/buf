<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>


<div class="search<?php echo $moduleclass_sfx ?>">
	<form action="<?php echo JRoute::_('index.php');?>" method="post" class="form-inline">
	
       <div class="input-group">
            
            <?php
               $output = '<input name="searchword" placeholder="' . $text . '" id="mod-search-searchword" maxlength="' . $maxlength . '"  class="inputbox search-query form-control" type="text" size="' . $width . '" value="" aria-describedby="buf_button_search" />';

                if ($button) :
                    if ($imagebutton) :
                        //$btn_output = ' <input type="image" value="' . $button_text . '" class="button" src="' . $img . '" onclick="this.form.searchword.focus();"/>';
                        $btn_output = ' <div class="input-group-append"> <button id="buf_button_search" class="button btn btn-outline-secondary" ><i class="fas fa-search"></i></button></div>';
                    else :
                        $btn_output = ' <div class="input-group-append"> <button id="buf_button_search" class="button btn btn-outline-secondary"><i class="fas fa-search"></i> ' . $button_text . '</button></div>';
                    endif;

                    switch ($button_pos) :
                        case 'top' :
                            $output = $btn_output . '<br />' . $output;
                            break;

                        case 'bottom' :
                            $output .= '<br />' . $btn_output;
                            break;

                        case 'right' :
                            $output .= $btn_output;
                            break;

                        case 'left' :
                        default :
                            $output = $btn_output . $output;
                            break;
                    endswitch;

                endif;

                echo $output;
            ?>
            <input type="hidden" name="task" value="search" />
            <input type="hidden" name="option" value="com_search" />
            <input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
        
        </div>
		
	</form>
</div>
