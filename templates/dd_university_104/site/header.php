<header class=" bd-headerarea-1  bd-margins">
        <section class=" bd-section-11 bd-tagstyles bd-bootstrap-btn bd-btn-default" id="section21" data-section-title="HOTINFO">
    <div class="bd-container-inner bd-margins clearfix">
        <div class=" bd-layoutbox-2 bd-no-margins clearfix">
    <div class="bd-container-inner">
        <div class=" bd-layoutbox-5 bd-no-margins clearfix">
    <div class="bd-container-inner">
        <?php if ($hic == 1) { ?><img class="bd-imagelink-6 bd-own-margins bd-imagestyles   "  src="<?php echo JURI::base() . 'templates/' . JFactory::getApplication()->getTemplate(); ?>/images/designer/voicespeaker.png"><?php } ?>
	
		<p class=" bd-textblock-22 bd-content-element">
<?php if ($hic == 1) { ?><?php echo $document->params->get('hin'); ?><?php } ?>
</p>
    </div>
</div>
	
		<div class=" bd-layoutbox-9 bd-no-margins clearfix">
    <div class="bd-container-inner">
        <div class=" bd-customhtml-2 bd-tagstyles bd-bootstrap-btn bd-btn-default">
    <div class="bd-container-inner bd-content-element">
     
<?php if ($hic == 1) { ?><marquee direction="left" scrollamount="<?php echo $document->params->get('speedh'); ?>" scrolldelay="1" onmouseover="this.stop()" onmouseout="this.start()"><a href="<?php echo $document->params->get('hil'); ?>"><?php echo $document->params->get('hi'); ?></a>
</marquee><?php } ?>

    </div>
</div>
    </div>
</div>
	
		<div class=" bd-layoutbox-13 bd-no-margins clearfix">
    <div class="bd-container-inner">
        <div class=" bd-socialicons-2">
    
         <?php if ($cal == 1) { ?> <?php if ($fc == 1) { ?><a target="_blank" class=" bd-socialicon-11 bd-socialicon" href="<?php echo $document->params->get('facebook'); ?>">
    <span class="bd-icon"></span><span></span>
</a><?php } ?>
    
        <?php if ($tc == 1) { ?><a target="_blank" class=" bd-socialicon-12 bd-socialicon" href="<?php echo $document->params->get('twitter'); ?>">
    <span class="bd-icon"></span><span></span>
</a><?php } ?>
    
        <?php if ($gc == 1) { ?><a target="_blank" class=" bd-socialicon-13 bd-socialicon" href="<?php echo $document->params->get('google'); ?>">
    <span class="bd-icon"></span><span></span>
</a><?php } ?>
    
        <?php if ($pc == 1) { ?><a target="_blank" class=" bd-socialicon-14 bd-socialicon" href="<?php echo $document->params->get('pinterest'); ?>">
    <span class="bd-icon"></span><span></span>
</a><?php } ?><?php } ?>
    
    
    
    
    
    
</div>
    </div>
</div>
	
		<div class=" bd-layoutbox-15 bd-no-margins clearfix">
    <div class="bd-container-inner">
        <?php if ($cser == 1) { ?><form id="search-4" role="form" class=" bd-search-4 hidden-xs form-inline" name="search" <?php echo funcBuildRoute(JFactory::getDocument()->baseurl . '/index.php', 'action'); ?> method="post">
    <div class="bd-container-inner">
        <input type="hidden" name="task" value="search">
        <input type="hidden" name="option" value="com_search">
        <div class="bd-search-wrapper">
            
                <input type="text" name="searchword" class=" bd-bootstrapinput-10 form-control input-sm" placeholder="Search">
                <a href="#" class="bd-icon-27 bd-icon " link-disable="true"></a>
        </div>
        <script>
            (function (jQuery, $) {
                jQuery('.bd-search-4 .bd-icon-27').on('click', function (e) {
                    e.preventDefault();
                    jQuery('#search-4').submit();
                });
            })(window._$, window._$);
        </script>
    </div>
</form>
    </div><?php } ?>
</div>
    </div>
</div>
    </div>
</section>
	
		<section class=" bd-section-1 bd-page-width bd-tagstyles bd-bootstrap-btn bd-btn-default " id="section7" data-section-title="Logo With Contacts">
    <div class="bd-container-inner bd-margins clearfix">
        <div class=" bd-layoutbox-35 bd-no-margins clearfix">
    <div class="bd-container-inner">
        <div class=" bd-layoutbox-37 bd-no-margins clearfix">
    <div class="bd-container-inner">
        <?php
$app = JFactory::getApplication();
$themeParams = $app->getTemplate(true)->params;
$sitename = $app->getCfg('sitename');
$logoSrc = '';
ob_start();
?>
src="<?php echo JURI::base() . 'templates/' . JFactory::getApplication()->getTemplate(); ?>/images/designer/a3cda19b45fda7d7553be47bc1790436_logo.png"
<?php

$logoSrc = ob_get_clean();
$logoLink = '';

if ($themeParams->get('logoFile'))
    $logoSrc = 'src="' . JURI::root() . $themeParams->get('logoFile') . '"';

if ($themeParams->get('logoLink'))
    $logoLink = $themeParams->get('logoLink');

if (!$logoLink)
    $logoLink = JUri::base(true);

?>
<a class=" bd-logo-3 animated bd-animation-2" data-animation-name="zoomIn" data-animation-event="onload" data-animation-duration="1000ms" data-animation-delay="0ms" data-animation-infinited="false" href="<?php echo $logoLink; ?>">
<img class=" bd-imagestyles-6" <?php echo $logoSrc; ?> alt="<?php echo $sitename; ?>">
</a>
    </div>
</div>
	
		<div class=" bd-layoutbox-39 animated bd-animation-14 bd-no-margins clearfix" data-animation-name="slideInDown" data-animation-event="onload" data-animation-duration="1500ms" data-animation-delay="0ms" data-animation-infinited="false">
    <div class="bd-container-inner">
        <?php if ($hcl == 1) { ?> <span class="bd-iconlink-13 bd-own-margins bd-icon-93 bd-icon "></span>
	
		<p class=" bd-textblock-124 bd-no-margins bd-content-element">

<?php echo $document->params->get('hc5'); ?>,<br><?php echo $document->params->get('hc6'); ?>

</p>
    </div>
</div>
	
		<div class=" bd-layoutbox-41 animated bd-animation-10 bd-no-margins clearfix" data-animation-name="slideInDown" data-animation-event="onload" data-animation-duration="1000ms" data-animation-delay="0ms" data-animation-infinited="false">
    <div class="bd-container-inner">
        <span class="bd-iconlink-15 bd-own-margins bd-icon-95 bd-icon "></span>
	
		<p class=" bd-textblock-126 bd-no-margins bd-content-element">

<?php echo $document->params->get('hc2'); ?><br><a href="mailto:<?php echo $document->params->get('hc3'); ?>">
<?php echo $document->params->get('hc3'); ?>
</a>

</p><?php } ?>
    </div>
</div>
    </div>
</div>
    </div>
</section>
	
		<?php
    renderTemplateFromIncludes('hmenu_1', array());
?>
</header>