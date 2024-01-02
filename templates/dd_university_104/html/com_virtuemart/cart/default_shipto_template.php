<?php
defined('_JEXEC') or die;
?>

<?php ob_start(); ?>
<div class="output-shipto">
    <?php if (empty($this->cart->STaddress['fields'])) : ?>
        <?php echo JText::sprintf ('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_EXPLAIN', JText::_ ('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL')); ?>
    <?php else : ?>
        <?php if (!class_exists ('VmHtml')) {
            require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
        }
        echo JText::_ ('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT');
        echo VmHtml::checkbox ('STsameAsBTjs', $this->cart->STsameAsBT) . '<br />';
        ?>
        <div id="output-shipto-display">
            <?php foreach ($this->cart->STaddress['fields'] as $item) : ?>
                <?php if (!empty($item['value'])) : ?>
                    <span class="values<?php echo '-' . $item['name'] ?>"><?php echo $this->escape ($item['value']) ?></span>
                    <?php if ($item['name'] != 'title' and $item['name'] != 'shipto_first_name' and $item['name'] != 'middle_name' and $item['name'] != 'shipto_zip') : ?>
                        <br class="clear"/>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <div class="clear"></div>
</div>
<?php if (!isset($this->cart->lists['current_id'])) {
    $this->cart->lists['current_id'] = 0;
} ?>
<div class=" bd-container-42 bd-tagstyles bd-bootstrap-btn bd-btn-default">
<a class=" btn   btn-default" href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=ST&virtuemart_user_id[]=' . $this->cart->lists['current_id'], $this->useXHTML, $this->useSSL) ?>" >
    <?php echo JText::_ ('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL'); ?>
</a></div>
<?php 
    echo renderTemplateFromIncludes($this->blockFuncName, array(JText::_ ('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'), ob_get_clean())); 
?>
