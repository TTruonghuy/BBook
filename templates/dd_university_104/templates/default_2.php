<!DOCTYPE html>
<html dir="ltr">
<head>
      <?php include("$themeDir/site/base.php"); ?>
   <?php include("$themeDir/site/style.php"); ?>  
</head>
<body class=" bootstrap bd-body-5  bd-pagebackground bd-margins">
    <?php include("$themeDir/site/header.php"); ?>
    <?php if ($slic == 2) { ?><?php include("$themeDir/site/slideshow.php"); ?><?php } ?> 
    <?php if ($pbc == 2) { ?><?php include("$themeDir/site/persons.php"); ?><?php } ?>
    <?php if ($iic == 2) { ?><?php include("$themeDir/site/textbox.php"); ?> <?php } ?> 
    <?php if ($vic == 2) { ?><?php include("$themeDir/site/video.php"); ?><?php } ?>
    <?php if ($aic == 2) { ?><?php include("$themeDir/site/textbox1.php"); ?> <?php } ?>
    <?php if ($qx == 2) { ?><?php include("$themeDir/site/textbox2.php"); ?> <?php } ?>
    <?php if ($cbx1 == 2) { ?><?php include("$themeDir/site/circle.php"); ?> <?php } ?>
    
<body class=" bootstrap bd-body-2  bd-pagebackground bd-margins">
<div class=" bd-stretchtobottom-6 bd-stretch-to-bottom" data-control-selector=".bd-contentlayout-2">
  <div class="bd-contentlayout-2  bd-sheetstyles  bd-no-margins bd-margins" >
    <div class="bd-container-inner">

        <div class="bd-flex-vertical bd-stretch-inner bd-contentlayout-offset">
            
 <?php renderTemplateFromIncludes('sidebar_area_1'); ?>
            <div class="bd-flex-horizontal bd-flex-wide bd-no-margins">
                
 <?php renderTemplateFromIncludes('sidebar_area_3'); ?>
                <div class="bd-flex-vertical bd-flex-wide bd-no-margins">
                    

                    <div class=" bd-layoutitemsbox-18 bd-flex-wide bd-no-margins">
    <div class=" bd-content-2">
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
            
        </div>

    </div>
</div></div>
<?php if ($dsb == 2) { ?><?php include("$themeDir/site/icon.php"); ?> <?php } ?>
<?php if ($gic == 2) { ?><?php include("$themeDir/site/gallery.php"); ?> <?php } ?>
<?php if ($fsb == 2) { ?><?php include("$themeDir/site/footer.php"); ?> <?php } ?>
         <?php include("$themeDir/site/design.php"); ?>
	
		
		<div data-smooth-scroll data-animation-time="250" class=" bd-smoothscroll-3"><a href="#" class=" bd-backtotop-1 ">
    <span class="bd-icon-66 bd-icon "></span>
</a></div>
</body>
</html>