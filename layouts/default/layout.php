<!--BUF TEMPLATE --> 
<?php
/**
 * @author          jtotal <support@jtotal.org>
 * @link            https://jtotal.org
 * @copyright       Copyright © 2023 JTOTAL All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;
?>


<header class="header" role="banner">
<div class="header-inner clearfix d-flex justify-content-center">
    <a class="brand pull-left" href="index.php"> <img width="400" height="207" src="templates/buf/images/buf_logos/logo_buf_text_400.png" class="img-fluid" alt="buf template system"/></a>
    <div class="header-search pull-right">
        <jdoc:include type="modules" name="bufsearch" />
    </div>
</div>
</header>



<!-- MENU TOP -->
<?php if ($this->countModules('menu_header')) : ?>
<div class="menu_header">
    <jdoc:include type="modules" name="menu_header" />
</div>
<?php endif; ?>

<!-- BANNER -->
<?php if ($this->countModules('banner_front')) : ?>
    <div class="row">
        <div class="banner_front">
            <jdoc:include type="modules" name="banner_front" />
        </div>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>


<!-- MENU PRAL -->
<?php if ($this->countModules('menu_principal')) : ?>
    <div id="menu_principal">
        <jdoc:include type="modules" name="menu_principal" />
    </div>
<?php endif; ?>

<div class="clearfix"></div>

<!-- CONTENIDOS -->
<div id="contents" class="<?php echo $content_container;?>">



<!-- BANNER INSIDE -->
<?php if ($this->countModules('banner_page')) : ?>
    <div class="row">
        <div class="banner_page">
            <jdoc:include type="modules" name="banner_page" />
        </div>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>




<!--ALL-->
<?php if ( $this->countModules($bs_right_pos) && $this->countModules($bs_left_pos) ) : ?>

    <div class="row leftandright">

        <div class="buf_left <?php echo $buf_left_sm.$buf_left_md.$buf_left_lg; ?>">
            <jdoc:include type="modules" name="<?php echo $bs_left_pos;?>"/>
        </div>
        <div class="content_pral <?php echo $content_pral_bs_sm.$content_pral_bs_md.$content_pral_bs_lg; ?>">

            <jdoc:include type="message" />
            <jdoc:include type="component" />

            <!-- SUBCOMPONENT -->
            <?php if ($this->countModules('subcomponent', true)) : ?>
            <div id="subcomponent">
                <jdoc:include type="modules" name="subcomponent"/>
            </div>
            <?php endif; ?>

        </div>
        <div class="buf_right <?php echo $buf_right_sm.$buf_right_md.$buf_right_lg; ?>">
            <jdoc:include type="modules" name="<?php echo $bs_right_pos;?>"/>
        </div>

    </div>
<!--left-->
<?php elseif($this->countModules($bs_left_pos)) : ?>

    <div class="row onlyleft">

        <div class="buf_left <?php echo $buf_left_sm.$buf_left_md.$buf_left_lg; ?>">
            <jdoc:include type="modules" name="<?php echo $bs_left_pos;?>"/>
        </div>
        <div class="content_pral <?php echo $content_pral_bs_sm.$content_pral_bs_md.$content_pral_bs_lg; ?>">

            <jdoc:include type="modules" name="visualmenu" />


            <jdoc:include type="message" />
            <jdoc:include type="component" />

            <!-- SUBCOMPONENT -->
            <?php if ($this->countModules('subcomponent', true)) : ?>
            <div id="subcomponent">
                <jdoc:include type="modules" name="subcomponent"/>
            </div>
            <?php endif; ?>

        </div>

    </div>

    <div class="clearfix"></div>

<!--right-->
<?php elseif($this->countModules($bs_right_pos)) : ?>

    <div class="row onlyright">

        <div class="content_pral <?php echo $content_pral_bs_sm.$content_pral_bs_md.$content_pral_bs_lg; ?>">

            <jdoc:include type="message" />
            <jdoc:include type="component" />

            <!-- SUBCOMPONENT -->
            <?php if ($this->countModules('subcomponent', true)) : ?>
            <div id="subcomponent">
                <jdoc:include type="modules" name="subcomponent"/>
            </div>
            <?php endif; ?>

        </div>

        <div class="buf_right <?php echo $buf_right_sm.$buf_right_md.$buf_right_lg; ?>">
            <jdoc:include type="modules" name="<?php echo $bs_right_pos;?>" />
        </div>
        
    </div>

    <div class="clearfix"></div>

    <!--FULL-->
    <?php else : ?>

        <div class="content_pral content_full">
            <jdoc:include type="message" />
            <jdoc:include type="component" />
        </div>


    <?php endif; ?>

    <!-- SUBCONTENT -->
    <div id="subcontent">
        <?php if ($this->countModules('subcontent')) : ?>
            <jdoc:include type="modules" name="subcontent"/>
        <?php endif; ?>
    </div>




</div><!-- fin container -->

<div class="clearfix"></div>




<!-- FOOTER -->
<div id="footer">
    <?php if ($this->countModules('footer')) : ?>
        <jdoc:include type="modules" name="footer"/>
    <?php endif; ?>
</div>






