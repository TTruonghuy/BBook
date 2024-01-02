<?php
function joomlaposition_6() {
    $document = JFactory::getDocument();
    $view = $document->view;
    $isPreview  = $GLOBALS['theme_settings']['is_preview'];
    if (isset($GLOBALS['isModuleContentExists']) && false == $GLOBALS['isModuleContentExists'])
        $GLOBALS['isModuleContentExists'] = $view->containsModules('box8') ? true : false;
?>
    <?php if ($isPreview || $view->containsModules('box8')) : ?>

    <?php if ($isPreview && !$view->containsModules('box8')) : ?>
    <!-- empty::begin -->
    <?php endif; ?>
    <div class=" bd-joomlaposition-6 clearfix" <?php echo buildDataPositionAttr('box8'); ?>>
        <?php echo $view->position('box8', 'block%joomlaposition_block_6', '6'); ?>
    </div>
    <?php if ($isPreview && !$view->containsModules('box8')) : ?>
    <!-- empty::end -->
    <?php endif; ?>
    <?php endif; ?>
<?php
}