<?php // no direct access
defined('_JEXEC') or die('Restricted access');

//dump ($cart,'mod cart');
// Ajax is displayed in vm_cart_products
// ALL THE DISPLAY IS Done by Ajax using "hiddencontainer" ?>

<!-- Virtuemart 2 Ajax Card -->
<span class="hidden vmCartModule <?php echo $params->get('moduleclass_sfx'); ?>" id="vmCartModule<?php echo $params->get('moduleid_sfx'); ?>">
<input class="total_products_from_cart" type="text" ><?php echo  $data->totalProduct?></input>
<?php // {DST ?>
<script>
jQuery(function($){
		textcart = <?php echo  $data->totalProduct?>;
		$("span.total_products_from_cart").html(textcart);
		$("span.cart-order-quantity").html(textcart);
});
</script>
<?php // }DST ?>

<?php
// {DST if ($show_product_list) {
if (false) {
	?>
	<div class="hiddencontainer" style=" display: none; ">
		<div class="vmcontainer">
			<div class="product_row">
				<span class="quantity"></span>&nbsp;x&nbsp;<span class="product_name"></span>

			<?php if ($show_price and $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
				<div class="subtotal_with_tax" style="float: right;"></div>
			<?php } ?>
			<div class="customProductData"></div>
			</div>
		</div>
	</div>
	<div class="vm_cart_products">
		<div class="vmcontainer">

		<?php
			foreach ($data->products as $product){
				?><div class="product_row">
					<span class="quantity"><?php echo  $product['quantity'] ?></span>&nbsp;x&nbsp;<span class="product_name"><?php echo  $product['product_name'] ?></span>
				<?php if ($show_price and $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
				  <div class="subtotal_with_tax" style="float: right;"><?php echo $product['subtotal_with_tax'] ?></div>
				<?php } ?>
				<?php if ( !empty($product['customProductData']) ) { ?>
					<div class="customProductData"><?php echo $product['customProductData'] ?></div>

				<?php } ?>

			</div>
		<?php }
		?>
		</div>
	</div>
<?php } ?>
<?php
// {DST
if (false) {
// }DST
?>
	<div class="total" style="float: right;">
		<?php if ($data->totalProduct and $show_price and $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
		<?php echo $data->billTotal; ?>
		<?php } ?>
	</div>

<div class="total_products"><?php echo  $data->totalProductTxt ?></div>
<div class="show_cart">
	<?php if ($data->totalProduct) echo  $data->cart_show; ?>
</div>
<div style="clear:both;"></div>
<?php
$view = vRequest::getCmd('view');
if($view!='cart' and $view!='user'){
	?><div class="payments-signin-button" ></div><?php
}
?>
<?php
// {DST
}
// }DST
?>
	
<noscript>
<?php echo vmText::_('MOD_VIRTUEMART_CART_AJAX_CART_PLZ_JAVASCRIPT') ?>
</noscript>
</span>

