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
// ******************
?>
	<table class="table table-striped">
		  <thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col" class="product-name">Наименование</th>
				<th scope="col">Остаток</th>
				<th scope="col">Реко-</br>мендо-</br>ванная</br>цена</th>
				<th scope="col">Базовая</br>цена</th>
				<th scope="col">Скидка</th>
				<th scope="col">Цена</th>
				<th scope="col">Размеры</th>
				<th scope="col">Пуск.</br>ток</th>
				<th scope="col">#</th>
			</tr>
		  </thead>
		  <tbody>
	<?php
// *********************
// }DST
			  
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

	$col = 1;
	$nb = 1;
	$row = 1;

// {DST
//***************************************************************************
	
	foreach ( $products as $product ) {
		if(!is_object($product) or empty($product->link)) {
			vmdebug('$product is not object or link empty',$product);
			continue;
		}
		?>
		
			<tr>
				<td>
					<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
						<?php
						echo $product->images[0]->displayMediaThumb('class="browseProductImage-horison"', false);
						?>
					</a>
				</td>
				<td><h2 class="horizon-name"><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h2></td>
				<td><?php echo '<span class="stock-level">'.$product->product_in_stock.'шт. </span>'; ?></td>
				<td><?php
				echo round(($product->allPrices[1][product_price]),2); ?>
	
				</td>
				<td><?php
				echo round(($product->allPrices[$product->selectedPrice][basePrice]),2); ?>						
				</td>
				<td><?php
					if ($product->allPrices[$product->selectedPrice][basePrice] <> $product->allPrices[$product->selectedPrice][salesPrice]) {
					echo '<span class="product-discount">'.((1-$product->allPrices[$product->selectedPrice][salesPrice]/$product->allPrices[$product->selectedPrice][basePrice])*100 ).'% </span>';
					}
					?> </td>
				<td><?php

				echo round(($product->allPrices[$product->selectedPrice][salesPrice]),2);
//				print_r($product);
				//echo shopFunctionsF::renderVmSubLayout('prices_value',array('product'=>$product,'currency'=>$currency));
		
				?></td>
				<td>
					<?php
					if (((int)$product->product_length > 0) &&
						((int)$product->product_width > 0) &&
						((int)$product->product_height > 0)
						) {
						echo '<span class="product-size">'.(int)$product->product_length.'-'.(int)$product->product_width.'-'.(int)$product->product_height.'</span>';
					}?>
					</td>
				<td>
				<?php
					foreach ($product->customfieldsSorted["normal"] as $keyElement=>$elementCustumfield) {
							if ($elementCustumfield->virtuemart_custom_id == '8') {
								echo ($product->customfieldsSorted["normal"][$keyElement]->customfield_value);
							}
						}
				?>
				</td>
				<td>
					<div class="addtocart-horizon">
					<?php
					echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
					</div>
				</td>	
			</tr>

		<?php
		
		// Show the horizontal seperator
		// {DST
		if (false) {
		// }DST
		if ($col == 1 && $nb > $products_per_row) { ?>
	<div class="horizontal-separator"></div>
		<?php }
		// {DST
		}
		// }DST

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
	<?php
	// {DST
	/*
	// }DST	
	?>
	<div class="product vm-products-horizon vm-col<?php echo ' vm-col-' . $products_per_row . $show_vertical_separator ?>">
		<div class="spacer product-container">
			<div class="vm-product-media-container">

					<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
						<?php
						echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
						?>
					</a>

			</div>

			


				<div class="vm-product-descr-container-<?php echo $rowsHeight[$row]['product_s_desc'] ?>">
					<h4><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h4>
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
			<?php /* 
			{DST
			//vm3pr
			vm3pr-hr
			}DST
			*/
		/*?>
			<div class="vm3pr-hr-<?php echo $rowsHeight[$row]['price'] ?>"> <?php
				echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
				<?php  
				//{ DST
				if (false) {
				// }DST
				?>
				<div class="clear"></div>
				<?php
				//{ DST
				}
				// }DST
				?>
			</div>
			<?php //echo $rowsHeight[$row]['customs'] ?>
			<?php /* 
			{DST
			//vm3pr
			vm3pr-hr
			}DST
			*/
		/*?>
			<div class="vm3pr-hr-<?php echo $rowsHeight[$row]['customfields'] ?>"> <?php
				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
			</div>

			<div class="vm-details-button">
				<?php // Product Details Button
				$link = empty($product->link)? $product->canonical:$product->link;
				echo JHtml::link($link.$ItemidStr,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
				//echo JHtml::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE), vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
				?>
			</div>
			
			<div class="vm-product-detail-info">
				<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
				// {DST
				echo '<span class="stock-level">'.$product->product_in_stock.'шт. </span>';
				if (false) {
				// }DST
				if ( VmConfig::get ('display_stock', 1)) { ?>
					<span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span>
				<?php 
				// {DST									 
				}
				// }DST
				?>
				<?php }
				// {DST
		if (((int)$product->product_length > 0) &&
			((int)$product->product_width > 0) &&
			((int)$product->product_height > 0)
			) {
			echo '<span class="product-size">'.'Д-Ш-В ('.(int)$product->product_length.'-'.(int)$product->product_width.'-'.(int)$product->product_height.') </span>';
		}
		if (strlen($product->customfieldsSorted["normal"][21]->customfield_value)> 0 ) {
			echo ' '.$product->customfieldsSorted["normal"][21]->customfield_value.'A ';
		}
				if ($product->allPrices[0][basePrice] <> $product->allPrices[0][salesPrice]) {
					echo '<span class="product-discount"> Базовая <s>'.$product->allPrices[0][basePrice].'</s> р - '.((1-$product->allPrices[0][salesPrice]/$product->allPrices[0][basePrice])*100 ).'% ';
				}
				else {
					echo '<span class="product-discount">'.' '; 	
				}
				// }DST
				//echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
				?>
			</div>
		<?php if($dynamic){
				echo vmJsApi::writeJS();
			} ?>
		</div>
	</div>
		
	<?php
	// {DST
	*/
	// }DST	
	?>

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

	

//***************************************************************************
/*
// }DST	
	
	
	foreach ( $products as $product ) {
		if(!is_object($product) or empty($product->link)) {
			vmdebug('$product is not object or link empty',$product);
			continue;
		}
		// Show the horizontal seperator
		// {DST
		if (false) {
		// }DST
		if ($col == 1 && $nb > $products_per_row) { ?>
	<div class="horizontal-separator"></div>
		<?php }
		// {DST
		}
		// }DST

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
	<div class="product vm-products-horizon vm-col<?php echo ' vm-col-' . $products_per_row . $show_vertical_separator ?>">
		<div class="spacer product-container">
			<div class="vm-product-media-container">

					<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
						<?php
						echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
						?>
					</a>

			</div>

			


				<div class="vm-product-descr-container-<?php echo $rowsHeight[$row]['product_s_desc'] ?>">
					<h4><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h4>
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
			<?php /* 
			{DST
			//vm3pr
			vm3pr-hr
			}DST
			*/
		/*?>
			<div class="vm3pr-hr-<?php echo $rowsHeight[$row]['price'] ?>"> <?php
				echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
				<?php  
				//{ DST
				if (false) {
				// }DST
				?>
				<div class="clear"></div>
				<?php
				//{ DST
				}
				// }DST
				?>
			</div>
			<?php //echo $rowsHeight[$row]['customs'] ?>
			<?php /* 
			{DST
			//vm3pr
			vm3pr-hr
			}DST
			*/
		/*?>
			<div class="vm3pr-hr-<?php echo $rowsHeight[$row]['customfields'] ?>"> <?php
				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
			</div>

			<div class="vm-details-button">
				<?php // Product Details Button
				$link = empty($product->link)? $product->canonical:$product->link;
				echo JHtml::link($link.$ItemidStr,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
				//echo JHtml::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE), vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
				?>
			</div>
			
			<div class="vm-product-detail-info">
				<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
				// {DST
				echo '<span class="stock-level">'.$product->product_in_stock.'шт. </span>';
				if (false) {
				// }DST
				if ( VmConfig::get ('display_stock', 1)) { ?>
					<span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span>
				<?php 
				// {DST									 
				}
				// }DST
				?>
				<?php }
				// {DST
		if (((int)$product->product_length > 0) &&
			((int)$product->product_width > 0) &&
			((int)$product->product_height > 0)
			) {
			echo '<span class="product-size">'.'Д-Ш-В ('.(int)$product->product_length.'-'.(int)$product->product_width.'-'.(int)$product->product_height.') </span>';
		}
		if (strlen($product->customfieldsSorted["normal"][21]->customfield_value)> 0 ) {
			echo ' '.$product->customfieldsSorted["normal"][21]->customfield_value.'A ';
		}
				if ($product->allPrices[0][basePrice] <> $product->allPrices[0][salesPrice]) {
					echo '<span class="product-discount"> Базовая <s>'.$product->allPrices[0][basePrice].'</s> р - '.((1-$product->allPrices[0][salesPrice]/$product->allPrices[0][basePrice])*100 ).'% ';
				}
				else {
					echo '<span class="product-discount">'.' '; 	
				}
				// }DST
				//echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
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
// {DST
	*/
// }DST
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


// {DST
// ******************************			  
?>
		  </tbody>
		</table>
<?php
// ******************************
// }DST
