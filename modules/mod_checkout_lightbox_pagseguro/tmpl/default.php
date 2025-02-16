<?php
/**
 * @package Checkout Lightbox with PagSeguro
 * @author Deividson Damasio
 * @copyright 2020 Deividson (https://www.linkedin.com/in/dadeke/)
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
 
defined('_JEXEC') or die;

?>
<div id="message_div<?php echo $module->id; ?>" class="alert alert-danger hide"></div>
<div class="control-group">
	<div class="control-label">
		<h3><?php echo $product_name; ?></h3>
	</div>
</div>
<?php if($show_product_code == "1") { ?>
<div class="control-group">
	<div class="control-label">
		<label>
			<?php echo JText::_('MOD_LIGHTBOXPAGSEGURO_SCODE_LABEL').$product_code; ?></label>
	</div>
</div>
<?php } ?>
<div class="control-group">
	<div class="control-label">
		<label><?php echo $product_description; ?></label>
	</div>
</div>
<?php if($show_quantity == "1") { ?>
<div class="control-group">
	<div class="control-label">
		<label>
			<?php echo JText::_('MOD_LIGHTBOXPAGSEGURO_SQUANTITY_LABEL'); ?>
			<?php if($allow_quantity_change == "1") { ?>
			<input id="quantity<?php echo $module->id; ?>"
				type="text"
				class="quantity"
				value="<?php echo $quantity; ?>"
				onkeypress="return vnumber(event);"
				maxlength="3"
				required />
			<?php } else if($allow_quantity_change == "0") { ?>
			<?php echo $quantity; ?>
			<input id="quantity<?php echo $module->id; ?>"
				type="hidden"
				value="<?php echo $quantity; ?>" />
			<?php } ?>
		</label>
	</div>
</div>
<?php } ?>
<div class="control-group">
	<div class="control-label">
		<label><?php echo JText::_('MOD_LIGHTBOXPAGSEGURO_SBRL_LABEL').$amount; ?></label>
	</div>
</div>
<input id="buttonpay<?php echo $module->id; ?>"
	type="button" class="pagseguro btpay"
	onclick="ProcessarPagSeguro('<?php echo $module->id; ?>', '<?php echo $menu_itemid; ?>');" />
<img id="loading_gif<?php echo $module->id; ?>"
	src="media/mod_checkout_lightbox_pagseguro/images/loading.gif"
	alt="loading.gif"
	class="hide" />
<input id="url_pagseguro<?php echo $module->id; ?>"
	type="hidden"
	value="<?php echo $url_pagseguro; ?>" />