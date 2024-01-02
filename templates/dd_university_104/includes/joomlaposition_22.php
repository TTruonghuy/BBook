<?php
function joomlaposition_22() {
    $document = JFactory::getDocument();
    $view = $document->view;
    $isPreview  = $GLOBALS['theme_settings']['is_preview'];
    if (isset($GLOBALS['isModuleContentExists']) && false == $GLOBALS['isModuleContentExists'])
        $GLOBALS['isModuleContentExists'] = $view->containsModules('slideshow') ? true : false;
?>
    <?php if ($isPreview || $view->containsModules('slideshow')) : ?>

    <?php if ($isPreview && !$view->containsModules('slideshow')) : ?>
    <!-- empty::begin -->
    <?php endif; ?>
    <div class=" bd-joomlaposition-22 bd-page-width  clearfix" <?php echo buildDataPositionAttr('slideshow'); ?>>
        <?php echo $view->position('slideshow', 'block%joomlaposition_block_22', '22'); ?>
    </div>
    <?php if ($isPreview && !$view->containsModules('slideshow')) : ?>
    <!-- empty::end -->
    <?php endif; ?>
    <?php endif; ?>
<?php
}