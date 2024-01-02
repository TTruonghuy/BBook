<!DOCTYPE html>
<html dir="ltr"><head>
   <?php include("$themeDir/site/base.php"); ?>
   <?php include("$themeDir/site/style.php"); ?>  
    
    
</head>
<body class=" bootstrap bd-body-1 
 bd-homepage bd-pagebackground-124 bd-margins">
    <?php include("$themeDir/site/header.php"); ?>  
	
		<section class=" bd-section-22 bd-page-width bd-tagstyles bd-bootstrap-btn bd-btn-default " id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_22');
?>
    </div>
</section>
	
		<?php if ($slic == 1 | $slic == 2) { ?><?php include("$themeDir/site/slideshow.php"); ?><?php } ?> 
	
		<section class=" bd-section-25 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_26');
?>
    </div>
</section>
	
		<?php if ($pbc == 1 | $pbc == 2) { ?><?php include("$themeDir/site/persons.php"); ?><?php } ?>  
	
		<section class=" bd-section-23 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_24');
?>
    </div>
</section>
	
		<?php if ($iic == 1 | $iic == 2) { ?><?php include("$themeDir/site/textbox.php"); ?> <?php } ?> 
	
		<section class=" bd-section-20 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_20');
?>
    </div>
</section>
	
		<?php if ($vic == 1 | $vic == 2) { ?><?php include("$themeDir/site/video.php"); ?><?php } ?>
	
		<section class=" bd-section-19 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_18');
?>
    </div>
</section>
	
		<?php if ($aic == 1 | $aic == 2) { ?><?php include("$themeDir/site/textbox1.php"); ?> <?php } ?>
	
		<section class=" bd-section-18 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_15');
?>
    </div>
</section>
	
		<?php if ($qx == 1 | $qx == 2) { ?><?php include("$themeDir/site/textbox2.php"); ?> <?php } ?>
	
		<section class=" bd-section-17 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_13');
?>
    </div>
</section>
	
		<?php if ($cbx1 == 1 | $cbx1 == 2) { ?><?php include("$themeDir/site/circle.php"); ?> <?php } ?>
	
		<section class=" bd-section-15 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_12');
?>
    </div>
</section>
	
		<div class="bd-containereffect-2 container-effect container ">
<div class=" bd-stretchtobottom-1 bd-stretch-to-bottom" data-control-selector=".bd-contentlayout-9">
<div class="bd-contentlayout-9   bd-sheetstyles  bd-no-margins bd-margins" >
    <div class="bd-container-inner">

        <div class="bd-flex-vertical bd-stretch-inner bd-contentlayout-offset">
            
 <?php renderTemplateFromIncludes('sidebar_area_1'); ?>
            <div class="bd-flex-horizontal bd-flex-wide bd-no-margins">
                
 <?php renderTemplateFromIncludes('sidebar_area_3'); ?>
                <div class="bd-flex-vertical bd-flex-wide bd-no-margins">
                    
 <?php renderTemplateFromIncludes('sidebar_area_5'); ?>

                    <div class=" bd-layoutitemsbox-27 bd-flex-wide bd-no-margins">
    <div class=" bd-content-9">
    <?php
            $document = JFactory::getDocument();
            echo $document->view->renderSystemMessages();
    $document->view->componentWrapper('common');
    echo '<jdoc:include type="component" />';
    ?>
</div>
</div>

                    
 <?php renderTemplateFromIncludes('sidebar_area_6'); ?>
                </div>
                
 <?php renderTemplateFromIncludes('sidebar_area_2'); ?>
            </div>
            
 <?php renderTemplateFromIncludes('sidebar_area_4'); ?>
        </div>

    </div>
</div></div>
</div>
	
		<section class=" bd-section-30 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box8" data-section-title="box8">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_30');
?>
    </div>
</section>
	
		<?php if ($dsb == 1 | $dsb == 2) { ?><?php include("$themeDir/site/icon.php"); ?> <?php } ?>
	
		<section class=" bd-section-13 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box8" data-section-title="box8">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_10');
?>
    </div>
</section>
	
		<?php if ($gic == 1 | $gic == 2) { ?><?php include("$themeDir/site/gallery.php"); ?> <?php } ?>
	
		<section class=" bd-section-12 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="box9" data-section-title="box9">
    <div class="bd-container-inner bd-margins clearfix">
        <?php
    renderTemplateFromIncludes('joomlaposition_6');
?>
    </div>
</section>
	
		<?php if ($fsb == 1 | $fsb == 2) { ?><?php include("$themeDir/site/footer.php"); ?> <?php } ?>
        <?php include("$themeDir/site/design.php"); ?>
	
		<div data-smooth-scroll data-animation-time="250" class=" bd-smoothscroll-3"><a href="#" class=" bd-backtotop-1 ">
    <span class="bd-icon-66 bd-icon "></span>
</a></div>
</body>
</html>