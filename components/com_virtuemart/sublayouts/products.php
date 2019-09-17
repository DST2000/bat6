<?php
/**
 * sublayout products
 *
 * @package	VirtueMart
 * @author Max Milbers
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
 * @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
 */

defined('_JEXEC') or die('Restricted access');
$products_per_row = empty($viewData['products_per_row'])? 1:$viewData['products_per_row'] ;
$currency = $viewData['currency'];
$showRating = $viewData['showRating'];
$verticalseparator = " vertical-separator";
echo shopFunctionsF::renderVmSubLayout('askrecomjs');

$ItemidStr = '';
$Itemid = shopFunctionsF::getLastVisitedItemId();
if(!empty($Itemid)){
	$ItemidStr = '&Itemid='.$Itemid;
}

$dynamic = false;

if (vRequest::getInt('dynamic',false) and vRequest::getInt('virtuemart_product_id',false)) {
	$dynamic = true;
}

// {DST
foreach ($viewData['products'] as $type => $products ) { 
	if( (!empty($type) and count($products)>0) or (count($viewData['products'])>1 and count($products)>0)){
				$productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>
		<div class="<?php echo $type ?>-view">
			<div class="row">
		  <h4><?php echo $productTitle ?></h4>
			</div>
				<?php // Start the Output
			}
	foreach ( $products as $product ) {
		if(!is_object($product) or empty($product->link)) {
			vmdebug('$product is not object or link empty',$product);
			continue;
		}
		// Show Products ?>
		<div class="product col-md-4 col-lg-3 products-height">
			<div class="item productSmCart text-center blockBorder-p0" data-vm="product-container">
				<div class="inc">
				<div class=" prodImgBg">

						<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
							<?php
							echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
							?>
						</a>

				</div>




					<div class="prodTitle">
						<?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?>
						<?php if(!empty($rowsHeight[$row]['product_s_desc'])){
						?>
						<p class="product_s_desc">
							<?php // Product Short Description
							if (!empty($product->product_s_desc)) {
								echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 60, ' ...') ?>
							<?php } ?>
						</p>
				<?php  } ?>
					</div>
					<div class="prodText">
						<?php
						echo 'Артикул: '.($product->customfieldsSorted["normal"][23]->customfield_value) . '</br>';
						if (strlen($product->customfieldsSorted["normal"][4]->customfield_value)>0) {
							echo ($product->customfieldsSorted["normal"][4]->customfield_value.'А/ч ');
						}
		
						if (((int)$product->product_length > 0) &&
							((int)$product->product_width > 0) &&
							((int)$product->product_height > 0)
							) {
							echo '<span class="product-size">'.(int)$product->product_length.'x'.(int)$product->product_width.'x'.(int)$product->product_height .' мм'.'</span>';
						}
						//echo '</br>'.($product->customfieldsSorted["normal"][0]->customfield_params);
						echo '</br>'. str_replace('#', ', ', $product->customfieldsSorted["normal"][0]->customfield_params);
						?>
					</div>


				<?php //echo $rowsHeight[$row]['price'] ?>
				<div class="row prodPriceBlock">
					<div class="prodPrice-black priceLeft col-md-5 col-xs-6">
						<div class="vm3pr-<?php echo $rowsHeight[$row]['price'] ?>"> <?php
							echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
							<div class="clear"></div>
						</div>
					</div>
					<div class="prodPrice priceRight col-md-7 col-xs-6 text-right">Скидка за старый АКБ  
<br style="clear: both">
<p class="skidka">-10%</p> <p class="redPrice"><span><?php echo round((($product->prices[product_price])*0.9), 2) ?></span> p.</p>
					</div>
					<div class="textPrice col-md-12">Рассрочка: от <?php echo round((($product->prices[product_price])/12), 2) ?> р./мес.</div>
					
				</div>
				<?php //echo $rowsHeight[$row]['customs'] ?>

				<div class="details-button">
					<?php // Product Details Button
					$link = empty($product->link)? $product->canonical:$product->link;
					echo JHtml::link($link.$ItemidStr,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'buttonAction' ) );
					//echo JHtml::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE), vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
					?>
				</div>
			<?php if($dynamic){
				echo vmJsApi::writeJS();
			} ?>
			</div>
			</div>
		</div>
		
		<?php
	}
	
	
	
} // foreach ($viewData['products'] as $type => $products )
?>	
<div class="clear"></div>	
<?php 
// }DST
/*
foreach ($viewData['products'] as $type => $products ) {

	$col = 1;
	$nb = 1;
	$row = 1;

	if($dynamic){
		$rowsHeight[$row]['product_s_desc'] = 1;
		$rowsHeight[$row]['price'] = 1;
		$rowsHeight[$row]['customfields'] = 1;
		$col = 2;
		$nb = 2;
	} else {
		$rowsHeight = shopFunctionsF::calculateProductRowsHeights($products,$currency,$products_per_row);

		if( (!empty($type) and count($products)>0) or (count($viewData['products'])>1 and count($products)>0)){
			$productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>
	<div class="<?php echo $type ?>-view">
	  <h4><?php echo $productTitle ?></h4>
			<?php // Start the Output
		}
	}

	// Calculating Products Per Row
	$cellwidth = ' width'.floor ( 100 / $products_per_row );

	$BrowseTotalProducts = count($products);


	foreach ( $products as $product ) {
		if(!is_object($product) or empty($product->link)) {
			vmdebug('$product is not object or link empty',$product);
			continue;
		}
		// Show the horizontal seperator
		if ($col == 1 && $nb > $products_per_row) { ?>
	<div class="horizontal-separator"></div>
		<?php }

		// this is an indicator wether a row needs to be opened or not
		if ($col == 1) { ?>
	<div class="row">
		<?php }

		// Show the vertical seperator
		if ($nb == $products_per_row or $nb % $products_per_row == 0) {
			$show_vertical_separator = ' ';
		} else {
			$show_vertical_separator = $verticalseparator;
		}

    // Show Products ?>
	<div class="product vm-col<?php echo ' vm-col-' . $products_per_row . $show_vertical_separator ?>">
		<div class="spacer product-container" data-vm="product-container">
			<div class="vm-product-media-container">

					<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
						<?php
						echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
						?>
					</a>

			</div>

			<div class="vm-product-rating-container">
				<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
				if ( VmConfig::get ('display_stock', 1)) { ?>
					<span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span>
				<?php }
				echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
				?>
			</div>


				<div class="vm-product-descr-container-<?php echo $rowsHeight[$row]['product_s_desc'] ?>">
					<h2><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h2>
					<?php if(!empty($rowsHeight[$row]['product_s_desc'])){
					?>
					<p class="product_s_desc">
						<?php // Product Short Description
						if (!empty($product->product_s_desc)) {
							echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 60, ' ...') ?>
						<?php } ?>
					</p>
			<?php  } ?>
				</div>


			<?php //echo $rowsHeight[$row]['price'] ?>
			<div class="vm3pr-<?php echo $rowsHeight[$row]['price'] ?>"> <?php
				echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
				<div class="clear"></div>
			</div>
			<?php //echo $rowsHeight[$row]['customs'] ?>
			<div class="vm3pr-<?php echo $rowsHeight[$row]['customfields'] ?>"> <?php
				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
			</div>

			<div class="vm-details-button">
				<?php // Product Details Button
				$link = empty($product->link)? $product->canonical:$product->link;
				echo JHtml::link($link.$ItemidStr,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
				//echo JHtml::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE), vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
				?>
			</div>
		<?php if($dynamic){
			echo vmJsApi::writeJS();
		} ?>
		</div>
	</div>

	<?php
    $nb ++;

      // Do we need to close the current row now?
      if ($col == $products_per_row || $nb>$BrowseTotalProducts) { ?>
    <div class="clear"></div>
  </div>
      <?php
      	$col = 1;
		$row++;
    } else {
      $col ++;
    }
  }

      if( (!empty($type) and count($products)>0) or (count($viewData['products'])>1 and count($products)>0) ){
        // Do we need a final closing row tag?
        //if ($col != 1) {
      ?>
    <div class="clear"></div>
  </div>
    <?php
    // }
    }
}
*/

/*if(vRequest::getInt('dynamic')){
	echo vmJsApi::writeJS();
}*/ ?>
