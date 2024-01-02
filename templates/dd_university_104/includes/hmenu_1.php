<?php
function hmenu_1() {
    $view = JFactory::getDocument()->view;
    $modulesContains = $view->containsModules('position-1');
    $isPreview  = $GLOBALS['theme_settings']['is_preview'];
    if (isset($GLOBALS['isModuleContentExists']) && false == $GLOBALS['isModuleContentExists'])
        $GLOBALS['isModuleContentExists'] = $view->containsModules('position-1') ? true : false;
    ?>
    <?php if ($isPreview || $modulesContains) : ?>
        <div data-affix
     data-offset=""
     data-fix-at-screen="top"
     data-clip-at-control="top"
     
 data-enable-lg
     
 data-enable-md
     
 data-enable-sm
     
     class=" bd-affix-1 bd-no-margins bd-margins ">
        <nav class=" bd-hmenu-1 bd-page-width  " data-responsive-menu="true" data-responsive-levels="expand on click">
            <?php if ($view->containsModules('position-1')) : ?>
            
                <div class=" bd-responsivemenu-11 collapse-button">
    <div class="bd-container-inner">
        <div class="bd-menuitem-10 ">
            <a  data-toggle="collapse"
                data-target=".bd-hmenu-1 .collapse-button + .navbar-collapse"
                href="#" onclick="return false;">
                    <span>Menu</span>
            </a>
        </div>
    </div>
</div>
                <div class="navbar-collapse collapse">
            <?php echo $view->position('position-1', '', '1', 'hmenu'); ?>
            
                </div>
            <?php else: ?>
                Please add a menu module in the 'position-1' position
            <?php endif; ?>
        </nav>
        </div>
    <?php endif; ?>
<?php
}