<?php
function joomlaposition_24() {
    $document = JFactory::getDocument();
    $view = $document->view;
    $isPreview  = $GLOBALS['theme_settings']['is_preview'];
    if (isset($GLOBALS['isModuleContentExists']) && false == $GLOBALS['isModuleContentExists'])
        $GLOBALS['isModuleContentExists'] = $view->containsModules('boxb') ? true : false;
?>
    <?php if ($isPreview || $view->containsModules('boxb')) : ?>

    <?php if ($isPreview && !$view->containsModules('boxb')) : ?>
    <!-- empty::begin -->
    <?php endif; ?>
    <div class=" bd-joomlaposition-24 clearfix" <?php echo buildDataPositionAttr('boxb'); ?>>
        <?php echo $view->position('boxb', 'block%joomlaposition_block_24', '24'); ?>
    </div>
    <?php if ($isPreview && !$view->containsModules('boxb')) : ?>
    <!-- empty::end -->
    <?php endif; ?>
    <?php endif; ?>
<?php
}