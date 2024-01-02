<?php
function joomlaposition_26() {
    $document = JFactory::getDocument();
    $view = $document->view;
    $isPreview  = $GLOBALS['theme_settings']['is_preview'];
    if (isset($GLOBALS['isModuleContentExists']) && false == $GLOBALS['isModuleContentExists'])
        $GLOBALS['isModuleContentExists'] = $view->containsModules('boxa') ? true : false;
?>
    <?php if ($isPreview || $view->containsModules('boxa')) : ?>

    <?php if ($isPreview && !$view->containsModules('boxa')) : ?>
    <!-- empty::begin -->
    <?php endif; ?>
    <div class=" bd-joomlaposition-26 clearfix" <?php echo buildDataPositionAttr('boxa'); ?>>
        <?php echo $view->position('boxa', 'block%joomlaposition_block_26', '26'); ?>
    </div>
    <?php if ($isPreview && !$view->containsModules('boxa')) : ?>
    <!-- empty::end -->
    <?php endif; ?>
    <?php endif; ?>
<?php
}