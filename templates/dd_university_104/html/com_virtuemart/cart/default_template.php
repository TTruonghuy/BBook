<?php
defined('_JEXEC') or die;
?>
<?php $this->blockFuncName = 'shoppingcart_block_1'; ?>
<div class=" bd-shoppingcart">
    <div class="bd-container-inner">
    <div class=" bd-carttitle-1">
    <h2><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h2>
</div>
            <?php if (VmConfig::get ('oncheckout_show_steps', 1) && $this->checkout_task === 'confirm') : ?>
                <div class="checkoutStep" id="checkoutStep4">
                    <?php echo JText::_ ('COM_VIRTUEMART_USER_FORM_CART_STEP4'); ?>
                </div>
            <?php endif; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                    echo renderTemplateFromIncludes($this->blockFuncName,
                        array('', shopFunctionsF::getLoginForm ($this->cart, FALSE)));
                ?>
            </div>
        </div>
    </div>
    <?php $taskRoute = ''; ?>
    <form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <?php echo $this->loadTemplate('billto_template'); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $this->loadTemplate('shipto_template'); ?>
                </div>
            </div>
        </div>

        <?php echo $this->loadTemplate ('pricelist_template'); ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <?php echo $this->loadTemplate ('info_template'); ?>
                </div>
                <div class="col-md-9">
                    <div id="checkout-advertise-box">
                        <?php if (!empty($this->checkoutAdvertise)) : ?>
                            <?php foreach ($this->checkoutAdvertise as $checkoutAdvertise) : ?>
                                <div class="checkout-advertise">
                                    <?php echo $checkoutAdvertise; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php  echo $this->loadTemplate('cartfields_template'); ?>

                    <div class="checkout-button-top">
                        <?php
$pbClassNames = ' btn   btn-default';
$pbDataAttrs = '';
echo preg_replace('/vm-button-correct([^>]+)/', $pbClassNames . '$1 ' . $pbDataAttrs, $this->checkout_link_html);
?>
                    </div>
                </div>
            </div>
        </div>
        <input type='hidden' name='order_language' value='<?php echo $this->order_language; ?>'/>
        <input type='hidden' name='task' value='updatecart'/>
        <input type='hidden' name='option' value='com_virtuemart'/>
        <input type='hidden' name='view' value='cart'/>
    </form>
    </div>
</div>