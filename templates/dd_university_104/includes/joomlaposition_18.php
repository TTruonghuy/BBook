<?php
function joomlaposition_18() {
    $document = JFactory::getDocument();
    $view = $document->view;
    $isPreview  = $GLOBALS['theme_settings']['is_preview'];
    if (isset($GLOBALS['isModuleContentExists']) && false == $GLOBALS['isModuleContentExists'])
        $GLOBALS['isModuleContentExists'] = $view->containsModules('box2') ? true : false;
?>
    <?php if ($isPreview || $view->containsModules('box2')) : ?>

    <?php if ($isPreview && !$view->containsModules('box2')) : ?>
    <!-- empty::begin -->
    <?php endif; ?>
    <div class=" bd-joomlaposition-18 clearfix" <?php echo buildDataPositionAttr('box2'); ?>>
        <?php echo $view->position('box2', 'block%joomlaposition_block_18', '18'); ?>
    </div>
    <?php if ($isPreview && !$view->containsModules('box2')) : ?>
    <!-- empty::end -->
    <?php endif; ?>
    <?php endif; ?>
<?php
}