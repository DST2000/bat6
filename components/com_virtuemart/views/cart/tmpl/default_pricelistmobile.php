<?php
/**
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2016 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
?>
<fieldset class="vm-fieldset-pricelist">
<div class="headercard">
	<div class="row">
		<div class="col-xs-6 cart-block">
			<span class="vm-cart-item-name" >Наименование</span>
		</div>
		<div class="col-xs-3 cart-block">
			<span class="vm-cart-item-basicprice">Цена</span>
		</div>
		<div class="col-xs-3 cart-block">
			<span class="vm-cart-item-discount" >% скидки</span>
			</br>
			<span class="vm-cart-item-discounted-price" ><span class='priceColor2'>цена со скидкой</span></span>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6 cart-block">
			<span class="vm-cart-item-sku priceColor2">Дополнительное описание</span>
		</div>
		<div class="col-xs-3 cart-block hidden-xs">
			<span class="vm-cart-item-quantity">Количество</span>
		</div>
		<div class="col-xs-3 cart-block hidden-sm  hidden-md  hidden-lg">
			<span class="vm-cart-item-quantity">Кол-во</span>
		</div>
		<div class="col-xs-3 cart-block">
			<span class="vm-cart-item-total">Сумма</span>
		</div>
	</div>
</div>
<?php
$i = 1;

foreach ($this->cart->products as $pkey => $prow) {
	$prow->prices = array_merge($prow->prices,$this->cart->cartPrices[$pkey]);
?>	
<div class="sectiontableentry<?php echo $i ?>">
<div class="row">
	<div class="col-xs-6 cart-block">
		<span class="vm-cart-item-name" >
		<input type="hidden" name="cartpos[]" value="<?php echo $pkey ?>">
		<?php if ($prow->virtuemart_media_id) { ?>
		<span class="cart-images">
			<?php
			if (!empty($prow->images[0])) {
				echo $prow->images[0]->displayMediaThumb ('', FALSE);
			} ?>
		</span>
		<?php } ?>
		<?php echo JHtml::link ($prow->url, $prow->product_name);
			// {DST}
//			echo $this->customfieldsModel->CustomsFieldCartDisplay ($prow); 
			// }DST
			?>
		</span> 
	</div>
	<div class="col-xs-3 cart-block">
		<span class="vm-cart-item-basicprice" >
		<?php
		if (VmConfig::get ('checkout_show_origprice', 1) && $prow->prices['discountedPriceWithoutTax'] != $prow->prices['priceWithoutTax']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $prow->prices, TRUE, FALSE) . '</span><br />';
		}
		if ($prow->prices['discountedPriceWithoutTax']) {
			echo $this->currencyDisplay->createPriceDiv ('discountedPriceWithoutTax', '', $prow->prices, FALSE, FALSE, 1.0, false, true);
		} else {
			echo $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $prow->prices, FALSE, FALSE, 1.0, false, true);
		} ?>
		</span>
	</div>
	<div class="col-xs-3 cart-block">
		<?php if (VmConfig::get ('show_tax')) { ?>
	<span class="vm-cart-item-tax" ><?php echo "<span class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('taxAmount', '', $prow->prices, FALSE, FALSE, $prow->quantity, false, true) . "</span>" ?></span>
	<?php } ?>
	<?php //{DST ?>
	<?php 
		$discountforSale = round((1-($prow->prices['salesPrice']) /($prow->prices['discountedPriceWithoutTaxTt']/$prow->quantity))*100);
		if ($discountforSale > 0) {
		$priceDiscount = round($prow->prices['salesPrice']*100)/100;
		//$discountforSale = $discountforSale."</br>".$priceDiscount;
		}
		$discountedPriceforSaleDisplay = ($discountforSale > 0) ? "$priceDiscount" : "";
	
		//$discountforSale = round((1-($prow->prices['salesPrice']) /($prow->prices['discountedPriceWithoutTaxTt']/$prow->quantity))*100);
//		if ($discountforSale > 0) {
//		$priceDiscount = round($prow->prices['salesPrice']*100)/100;
//		$discountforSale = $discountforSale."</br>".$priceDiscount;
//		}
		$discountforSaleDisplay = ($discountforSale > 0) ? "-$discountforSale %" : "";
	?>
	<span class="vm-cart-item-discount" ><?php echo "<span class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('discountAmount', '', $prow->prices, FALSE, FALSE, $prow->quantity, false, true) . "</span>". $discountforSaleDisplay ?></span>
		</br>
	<span class="vm-cart-item-discounted-price" ><?php echo "<span class='priceColor2'>" .$discountedPriceforSaleDisplay. "</span>";  ?></span>
	<?php //}DST ?>
	
	</div>
</div>
<div class="row">
	<div class="col-xs-6 cart-block">
		<span class="vm-cart-item-sku priceColor2" ><?php  echo $this->customfieldsModel->CustomsFieldCartDisplay ($prow); ?></span>		
	</div>
	<div class="col-xs-3 cart-block">
		<span class="vm-cart-item-quantity" ><?php
		if ($prow->step_order_level)
			$step=$prow->step_order_level;
		else
			$step=1;
		if($step==0)
			$step=1;
		?>
		<input type="text"
			onblur="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
			onclick="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
			onchange="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
			onsubmit="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
			title="<?php echo  vmText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="quantity-input js-recalculate" size="1" maxlength="3" name="quantity[<?php echo $pkey; ?>]" value="<?php echo $prow->quantity ?>" />
		<button type="submit" class="vmicon vm2-add_quantity_cart" name="updatecart.<?php echo $pkey ?>" title="<?php echo  vmText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" data-dynamic-update="1" ></button>
		<button type="submit" class="vmicon vm2-remove_from_cart" name="delete.<?php echo $pkey ?>" title="<?php echo vmText::_ ('COM_VIRTUEMART_CART_DELETE') ?>" ></button>
		</span>		
	</div>
	<div class="col-xs-3 cart-block">
		<span class="vm-cart-item-total">
		<?php
		if (VmConfig::get ('checkout_show_origprice', 1) && !empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceWithTax'] != $prow->prices['salesPrice']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceWithTax', '', $prow->prices, TRUE, FALSE, $prow->quantity) . '</span><br />';
		}
		elseif (VmConfig::get ('checkout_show_origprice', 1) && empty($prow->prices['basePriceWithTax']) && !empty($prow->prices['basePriceVariant']) && $prow->prices['basePriceVariant'] != $prow->prices['salesPrice']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $prow->prices, TRUE, FALSE, $prow->quantity) . '</span><br />';
		}
		echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $prow->prices, FALSE, FALSE, $prow->quantity) ?>
		</span>
	</div>
</div>
</div>
	<?php
	$i = ($i==1) ? 2 : 1;
} ?>
<!-- //{ edit code -->
	
	
	
<!-- //} edit code -->	
	
<!--Begin of SubTotal, Tax, Shipment, Coupon Discount and Total listing -->
<?php if (VmConfig::get ('show_tax')) {
	$colspan = 3;
} else {
	$colspan = 2;
} ?>
<tr>
	<?php 
	// {DST
	/* <td colspan="4">&nbsp;</td> */
	// }DST
	?>	
	<td colspan="5">&nbsp;</td>
	<td colspan="<?php echo $colspan ?>">
		<hr/>
	</td>
</tr>
<tr class="sectiontableentry1">
	<?php 
	// {DST
	/* <td colspan="4" style="text-align: right;"><?php echo vmText::_ ('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td> */
	// }DST
	?>
	<td colspan="5" style="text-align: right;"><?php echo vmText::_ ('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('taxAmount', '', $this->cart->cartPrices, FALSE, false, true) . "</span>" ?></td>
	<?php } ?>
	<td style="text-align: right;"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('discountAmount', '', $this->cart->cartPrices, FALSE) . "</span>" ?></td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $this->cart->cartPrices, FALSE) ?></td>
</tr>

<?php


if (VmConfig::get ('coupons_enable')) {
?>
<tr class="sectiontableentry2">
	<?php 
	// {DST
	/* <td colspan="4" style="text-align: left;"> */
	// }DST
	?>
	<td colspan="5" style="text-align: left;">
		<?php if (!empty($this->layoutName) && $this->layoutName == $this->cart->layout) {
		echo $this->loadTemplate ('coupon');
		} ?>
		<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
		<?php
		echo $this->cart->cartData['couponCode'];
		echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')') : '';
		?>
	</td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ('couponTax', '', $this->cart->cartPrices['couponTax'], FALSE); ?> </td>
	<?php } ?>
	<td style="text-align: right;">&nbsp;</td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceCoupon', '', $this->cart->cartPrices['salesPriceCoupon'], FALSE); ?> </td>
	<?php } else { ?>
	&nbsp;</td>
	<td colspan="<?php echo $colspan ?>" style="text-align: left;">&nbsp;</td>
	<?php }	?>
</tr>
<?php } ?>

<?php
foreach ($this->cart->cartData['DBTaxRulesBill'] as $rule) {
?>
<tr class="sectiontableentry<?php echo $i ?>">
	<?php 
	// {DST
	/* <td colspan="4" style="text-align: right;"><?php echo $rule['calc_name'] ?> </td> */
	// }DST
	?>
	<td colspan="5" style="text-align: right;"><?php echo $rule['calc_name'] ?> </td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"></td>
	<?php } ?>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>&nbsp;</td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>&nbsp;</td>
</tr>
	<?php
	if ($i) {
		$i = 1;
	} else {
		$i = 0;
	}
} ?>

<?php

foreach ($this->cart->cartData['taxRulesBill'] as $rule) {
	if($rule['calc_value_mathop']=='avalara') continue;
	?>
<tr class="sectiontableentry<?php echo $i ?>">
	<?php 
	// {DST
	/* <td colspan="4" style="text-align: right;"><?php echo $rule['calc_name'] ?> </td> */
	// }DST
	?>
	<td colspan="5" style="text-align: right;"><?php echo $rule['calc_name'] ?> </td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>&nbsp;</td>
	<?php } ?>
	<td style="text-align: right;"><?php ?> </td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>&nbsp;</td>
</tr>
	<?php
	if ($i) {
		$i = 1;
	} else {
		$i = 0;
	}
}

foreach ($this->cart->cartData['DATaxRulesBill'] as $rule) {
	?>
<tr class="sectiontableentry<?php echo $i ?>">
	<?php 
	// {DST
	/* <td colspan="4" style="text-align: right;"><?php echo   $rule['calc_name'] ?> </td> */
	// }DST
	?>
	<td colspan="5" style="text-align: right;"><?php echo   $rule['calc_name'] ?> </td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;">&nbsp;</td>
	<?php } ?>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
</tr>
	<?php
	if ($i) {
		$i = 1;
	} else {
		$i = 0;
	}
}

if (VmConfig::get('oncheckout_opc',true) or
	!VmConfig::get('oncheckout_show_steps',false) or
	(!VmConfig::get('oncheckout_opc',true) and VmConfig::get('oncheckout_show_steps',false) and
	!empty($this->cart->virtuemart_shipmentmethod_id) )
) { ?>
<tr class="sectiontableentry1" style="vertical-align:top;">
	<?php if (!$this->cart->automaticSelectedShipment) { ?>
	<?php 
	// {DST
	/* <td colspan="4" style="align:left;vertical-align:top;"> */
	// }DST
	?>
	<td colspan="5" style="align:left;vertical-align:top;">
		<?php
		echo '<h3>'.vmText::_ ('COM_VIRTUEMART_CART_SELECTED_SHIPMENT').'</h3>';
		echo $this->cart->cartData['shipmentName'].'<br/>';
		if (!empty($this->layoutName) and $this->layoutName == $this->cart->layout) {
			if (VmConfig::get('oncheckout_opc', 0)) {
				$previouslayout = $this->setLayout('select');
				echo $this->loadTemplate('shipment');
				$this->setLayout($previouslayout);
			} else {
				echo JHtml::_('link', JRoute::_('index.php?option=com_virtuemart&view=cart&task=edit_shipment', $this->useXHTML, $this->useSSL), $this->select_shipment_text, 'class=""');
			}
		} else {
			echo vmText::_ ('COM_VIRTUEMART_CART_SHIPPING');
		}
	echo '</td>';
	} else {
	?>
	<?php 
	// {DST
	/* <td colspan="4" style="align:left;vertical-align:top;"> */
	// }DST
	?>
	<td colspan="5" style="align:left;vertical-align:top;">
		<?php echo '<h3>'.vmText::_ ('COM_VIRTUEMART_CART_SELECTED_SHIPMENT').'</h3>'; ?>
		<?php echo $this->cart->cartData['shipmentName'];
		echo '<span class="floatright">' . $this->currencyDisplay->createPriceDiv ('shipmentValue', '', $this->cart->cartPrices['shipmentValue'], FALSE) . '</span>';
		?>
        <?php 
        //{DST
        echo '<div class="spaceCart"><div>';    
        //}DST
        ?>
	</td>
	<?php } ?>

	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php
		echo "<span class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('shipmentTax', '', $this->cart->cartPrices['shipmentTax'], FALSE) . "</span>"; ?> </td>
	<?php } ?>
	<td style="text-align: right;"><?php if($this->cart->cartPrices['salesPriceShipment'] < 0) echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], FALSE); ?></td>
	<td style="text-align: right;"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], FALSE); ?> </td>
</tr>
<?php } ?>
<?php if ($this->cart->pricesUnformatted['salesPrice']>0.0 and
	(VmConfig::get('oncheckout_opc',true) or
		!VmConfig::get('oncheckout_show_steps',false) or
		( (!VmConfig::get('oncheckout_opc',true) and VmConfig::get('oncheckout_show_steps',false) ) and !empty($this->cart->virtuemart_paymentmethod_id))
	)
) { ?>
<tr class="sectiontableentry1" style="vertical-align:top;">
	<?php if (!$this->cart->automaticSelectedPayment) { ?>
	<?php 
	// {DST
	/* <td colspan="4" style="align:left;vertical-align:top;"> */
	// }DST
	?>
	<td colspan="5" style="align:left;vertical-align:top;">
		<?php
		echo '<h3>'.vmText::_ ('COM_VIRTUEMART_CART_SELECTED_PAYMENT').'</h3>';
		echo $this->cart->cartData['paymentName'].'<br/>';
		if (!empty($this->layoutName) && $this->layoutName == $this->cart->layout) {
			if (VmConfig::get('oncheckout_opc', 0)) {
				$previouslayout = $this->setLayout('select');
				echo $this->loadTemplate('payment');
				$this->setLayout($previouslayout);
			} else {
				echo JHtml::_('link', JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment', $this->useXHTML, $this->useSSL), $this->select_payment_text, 'class=""');
			}
		} else {
		echo vmText::_ ('COM_VIRTUEMART_CART_PAYMENT');
		} ?></td>
	<?php } else { ?>
	<td colspan="4" style="align:left;vertical-align:top;" >
		<?php echo '<h4>'.vmText::_ ('COM_VIRTUEMART_CART_SELECTED_PAYMENT').'</h4>'; ?>
		<?php echo $this->cart->cartData['paymentName']; ?> </td>
	<?php } ?>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('paymentTax', '', $this->cart->cartPrices['paymentTax'], FALSE) . "</span>"; ?> </td>
	<?php } ?>
	<td style="text-align: right;" ><?php if($this->cart->cartPrices['salesPricePayment'] < 0) echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], FALSE); ?></td>
	<td style="text-align: right;" ><?php  echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], FALSE); ?> </td>
</tr>
<?php } ?>

<tr>
	<?php 
	// {DST
	/* <td colspan="4">&nbsp;</td> */
	// }DST
	?>
	<td colspan="5">&nbsp;</td>
	<td colspan="<?php echo $colspan ?>">
		<hr/>
	</td>
</tr>

<tr class="sectiontableentry2">
	<?php 
	// {DST
	/* <td colspan="4" style="text-align: right;"><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL') ?>:</td> */
	// }DST
	?>
	<td colspan="5" style="text-align: right;"><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL') ?>:</td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;"> <?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], FALSE) . "</span>" ?> </td>
	<?php } ?>
	<td style="text-align: right;"> <?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('billDiscountAmount', '', $this->cart->cartPrices['billDiscountAmount'], FALSE) . "</span>" ?> </td>
	<td style="text-align: right;"><strong><?php echo $this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->cartPrices['billTotal'], FALSE); ?></strong></td>
</tr>

<?php
if ($this->totalInPaymentCurrency) {
?>
<tr class="sectiontableentry2">
	<?php 
	// {DST
	/* <td colspan="4" style="text-align: right;"><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>:</td> */
	// }DST
	?>
	<td colspan="5" style="text-align: right;"><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>:</td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td style="text-align: right;">&nbsp;</td>
	<?php } ?>
	<td style="text-align: right;">&nbsp;</td>
	<td style="text-align: right;"><strong><?php echo $this->totalInPaymentCurrency;   ?></strong></td>
</tr>
	<?php
}

//Show VAT tax separated
if(!empty($this->cart->cartData)){
	if(!empty($this->cart->cartData['VatTax'])){
		$c = count($this->cart->cartData['VatTax']);
		if (!VmConfig::get ('show_tax') or $c>1) {
			if($c>0){ ?>

<tr class="sectiontableentry2">
	<td colspan="3">&nbsp;</td>
	<td colspan="2" style="text-align: left;border-bottom: 1px solid #333;"><?php echo vmText::_ ('COM_VIRTUEMART_TOTAL_INCL_TAX') ?></td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td>&nbsp;</td>
	<?php } ?>
	<td>&nbsp;</td>
</tr>
			<?php
			}
			foreach( $this->cart->cartData['VatTax'] as $vatTax ) {
				if(!empty($vatTax['result'])) { ?>
<tr class="sectiontableentry<?php echo $i ?>">
	<td colspan="3">&nbsp;</td>
	<td style="text-align: right;"><?php echo shopFunctionsF::getTaxNameWithValue($vatTax['calc_name'],$vatTax['calc_value']) ?></td>
	<td style="text-align: right;"><span class="priceColor2"><?php echo $this->currencyDisplay->createPriceDiv( 'taxAmount', '', $vatTax['result'], FALSE, false, 1.0,false,true ) ?></span></td>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td >&nbsp;</td>
	<?php } ?>
	<td>&nbsp;</td>
</tr>
				<?php
				}
			}
		}
	}
}
?>

</table>
</fieldset>
