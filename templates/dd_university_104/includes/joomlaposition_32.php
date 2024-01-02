<?php
function joomlaposition_32() {
    $document = JFactory::getDocument();
    $view = $document->view;
    $isPreview  = $GLOBALS['theme_settings']['is_preview'];
    if (isset($GLOBALS['isModuleContentExists']) && false == $GLOBALS['isModuleContentExists'])
        $GLOBALS['isModuleContentExists'] = $view->containsModules('box9') ? true : false;
?>
    <?php if ($isPreview || $view->containsModules('box9')) : ?>

    <?php if ($isPreview && !$view->containsModules('box9')) : ?>
    <!-- empty::begin -->
    <?php endif; ?>
    <div class=" bd-joomlaposition-32 clearfix" <?php echo buildDataPositionAttr('box9'); ?>>
        <?php echo $view->position('box9', 'block%joomlaposition_block_32', '32'); ?>
    </div>
    <?php if ($isPreview && !$view->containsModules('box9')) : ?>
    <!-- empty::end -->
    <?php endif; ?>
    <?php endif; ?>
<?php
}