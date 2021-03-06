<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz, Max Galt
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 9774 2018-03-06 20:43:19Z StefanSTS $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/* Let's see if we found the product */
if (empty($this->product)) {
	echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs',array('product'=>$this->product));



if(vRequest::getInt('print',false)){ ?>
<body onload="javascript:print();">
<?php } ?>

<div class="product-container productdetails-view productdetails">

	<?php
	// { DST
	// Product Navigation
	if (VmConfig::get('product_navigation', 1)) {
	?>
		<div class="product-neighbours">
		
		<?php
		// {DST
//		echo '<pre>';
//		var_dump($this->product->categoryItem[0]);
//		echo '</pre>';
//		stop();
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $this->product->categoryItem[0]["virtuemart_category_id"]) ;
		echo JHtml::_('link', $prev_link, $this->product->categoryItem[0]["category_name"] , array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '0'));
		
		
		
		
		if ($this->product->virtuemart_category_id) {
			$next_link =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
			$categoryName = vmText::_($this->product->category_name) ;
		} else {
			$next_link =  JRoute::_('index.php?option=com_virtuemart');
			$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
		}
		$next_link = JRoute::_($next_link) ;
		echo JHtml::_('link', $next_link, $categoryName , array('rel'=>'next', 'class' => 'next-page','data-dynamic-update' => '0'));
		// }DST
		?>
			
		<?php
		/*
		if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $prev_link, $this->product->neighbours ['previous'][0]
			['product_name'], array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '1'));
		}
		if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('rel'=>'next','class' => 'next-page','data-dynamic-update' => '1'));
		}
		*/
		?>
		<div class="clear"></div>
		</div>
	<?php } // Product Navigation END
	?>

	<?php // Back To Category Button
	// {DST
	/*
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
		$categoryName = vmText::_($this->product->category_name) ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
	
	?>
	<div class="back-to-category">
		<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
	</div>
	*/
	// }DST
	?>
	<div class="clear"></div>
	
	<div class="col-md-12 col-lg-4">
	
	<?php
	echo $this->loadTemplate('images');
	?>
		
	<?php
	$count_images = count ($this->product->images);
	if ($count_images > 1) {
		echo $this->loadTemplate('images_additional');
	}

	// event onContentBeforeDisplay
	echo $this->product->event->beforeDisplayContent; ?>
	
	</div>
	
	<div class="col-md-12 col-lg-8">
	
	<?php // Product Title   ?>
	<?php // {DST
	/*
	//<h1><?php echo $this->product->product_name ?></h1>
	*/
	?>
	<h1><?php echo $this->product->customtitle ?></h1>
	<?php
	// }DST   ?>
	<?php // Product Title END   ?>
		
	<?php // afterDisplayTitle Event
	echo $this->product->event->afterDisplayTitle ?>

	<?php
	// Product Edit Link
	echo $this->edit_link;
	// Product Edit Link END
	?>
		
	<div class="row rowPrice">
		
		<div class="col-md-2 col-xs-6 prodPrice">
			<div class="vm-product-details-container">
				<div class="spacer-buy-area">

				<?php
				// TODO in Multi-Vendor not needed at the moment and just would lead to confusion
				/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
				  $text = vmText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
				  echo '<span class="bold">'. vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
				 */
				?>

				<?php
				echo shopFunctionsF::renderVmSubLayout('rating', array('showRating' => $this->showRating, 'product' => $this->product));

				foreach ($this->productDisplayTypes as $type=>$productDisplayType) {

					foreach ($productDisplayType as $productDisplay) {

						foreach ($productDisplay as $virtuemart_method_id =>$productDisplayHtml) {
							?>
							<div class="<?php echo substr($type, 0, -1) ?> <?php echo substr($type, 0, -1).'-'.$virtuemart_method_id ?>">
								<?php
								echo $productDisplayHtml;
								?>
							</div>
							<?php
						}
					}
				}

				//In case you are not happy using everywhere the same price display fromat, just create your own layout
				//in override /html/fields and use as first parameter the name of your file
				echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));
				?> <div class="clear"></div><?php
//				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));

				echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product));

				// Ask a question about this product
				if (VmConfig::get('ask_question', 0) == 1) {
					$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
					?>
					<div class="ask-a-question">
						<a class="ask-a-question" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
					</div>
				<?php
				}
				?>

				<?php
				// Manufacturer of the Product
				if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
					echo $this->loadTemplate('manufacturer');
				}
				?>

				</div>
			</div>
		</div>
		<div class="col-md-2 col-xs-6 prodPrice discount">
		<?php
		if (($this->product->product_weight > 0) && ((int)$this->product->product_length > 0)) :
		?>
			<span class="oldAkb">Скидка&nbsp;за&nbsp;старый</span>  
			<br >
			<?php 
			/*
			<span class="skidka">-10%</span> <span class="redPrice"><?php echo round((($this->product->prices[product_price])*0.9), 2) ?> p.
			*/?>
			<span>&nbsp;АКБ&nbsp; 
			<span class="redPrice">
							<?php 
							if (($this->product->product_weight > 0) && ((int)$this->product->product_length > 0)){
							//echo (int)round(($product->product_weight)*100)/100;
							// скидка за акб
							echo ((int)round(($this->product->product_weight)*0.6*1.3));
							}
							?>
							</span>&nbsp;p.
			</span>
		<?php endif; ?>			
		</div>
		<div class="col-md-2 col-xs-6 prodPrice">
			<span class="dolg">Рассрочка: </br>от <span class="boldPrice"><?php echo round((($this->product->prices[product_price])/3), 2) ?></span> р./мес.</span>
		</div>
		<div class="col-md-2 col-xs-6 prodPrice">
			<label class="form-check-label" style="font-weight: normal">
				<input class="form-check-input" type="checkbox"> Самовывоз (скидка&nbsp;5р.)
			</label>
		</div>
		<div class="col-md-2 col-xs-6 prodPrice">
			<label class="form-check-label" style="font-weight: normal">
				<?php
				if (($this->product->product_weight > 0) && ((int)$this->product->product_length > 0)) :
				?>
				<input class="form-check-input" type="checkbox"> Сдать аккумулятор
				<?php endif; ?>
			</label>
		</div>
		<div class="col-md-2 col-xs-6 prodPrice">
			<?php
			//echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
			
			<div class="addtocart-area-special">
				<form method="post" class="product js-recalculate" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart',false); ?>" autocomplete="off" >
					<div class="vm-customfields-wrap">
						<?php
						if(!empty($rowHeights['customfields'])) {
							foreach($positions as $pos){
								echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>$pos));
							}
						} ?>
					</div>			
						<?php
						if (!VmConfig::get('use_as_catalog', 0)  ) {
							echo shopFunctionsF::renderVmSubLayout('addtocartbar',array('product'=>$this->product));
						} ?>
					<input type="hidden" name="option" value="com_virtuemart"/>
					<input type="hidden" name="view" value="cart"/>
					<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $this->product->virtuemart_product_id ?>"/>
					<input type="hidden" name="pname" value="<?php echo $this->product->product_name ?>"/>
					<input type="hidden" name="pid" value="<?php echo $this->product->virtuemart_product_id ?>"/>
					<?php
					$itemId=vRequest::getInt('Itemid',false);
					if($itemId){
						echo '<input type="hidden" name="Itemid" value="'.$itemId.'"/>';
					} ?>
				</form>

			</div>
			
			
		</div>
	
	</div>
		
	<div class="row">
		<?php
		echo shopFunctionsF::renderVmSubLayout('customfields_table',array('product'=>$this->product,'position'=>'normal'));
		?>

	</div>	
		
	<div class="row rowPrice">
		
		<div class="col-md-2 col-xs-6 prodPrice">
			<div class="vm-product-details-container">
				<div class="spacer-buy-area">

				<?php
				// TODO in Multi-Vendor not needed at the moment and just would lead to confusion
				/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
				  $text = vmText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
				  echo '<span class="bold">'. vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
				 */
				?>

				<?php
				echo shopFunctionsF::renderVmSubLayout('rating', array('showRating' => $this->showRating, 'product' => $this->product));

				foreach ($this->productDisplayTypes as $type=>$productDisplayType) {

					foreach ($productDisplayType as $productDisplay) {

						foreach ($productDisplay as $virtuemart_method_id =>$productDisplayHtml) {
							?>
							<div class="<?php echo substr($type, 0, -1) ?> <?php echo substr($type, 0, -1).'-'.$virtuemart_method_id ?>">
								<?php
								echo $productDisplayHtml;
								?>
							</div>
							<?php
						}
					}
				}

				//In case you are not happy using everywhere the same price display fromat, just create your own layout
				//in override /html/fields and use as first parameter the name of your file
				echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));
				?> <div class="clear"></div><?php
//				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));

				echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product));

				// Ask a question about this product
				if (VmConfig::get('ask_question', 0) == 1) {
					$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
					?>
					<div class="ask-a-question">
						<a class="ask-a-question" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
					</div>
				<?php
				}
				?>

				<?php
				// Manufacturer of the Product
				if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
					echo $this->loadTemplate('manufacturer');
				}
				?>

				</div>
			</div>
		</div>
		<div class="col-md-2 col-xs-6 prodPrice discount">
		<?php
		if  (($this->product->product_weight > 0) && ((int)$this->product->product_length > 0)) :
		?>
			<span class="oldAkb">Скидка&nbsp;за&nbsp;старый</span>  
			<br >
			<?php 
			/*
			<span class="skidka">-10%</span> <span class="redPrice"><?php echo round((($this->product->prices[product_price])*0.9), 2) ?> p.
			*/?>
			<span>&nbsp;АКБ&nbsp; 
			<span class="redPrice">
							<?php 
							if (($this->product->product_weight > 0) && ((int)$this->product->product_length > 0)){
							//echo (int)round(($product->product_weight)*100)/100;
							// скидка за акб
							echo ((int)round(($this->product->product_weight)*0.6*1.3));
							}
							?>
							</span>&nbsp;p.
			</span>
		<?php endif; ?>		
		</div>
		<div class="col-md-2 col-xs-6 prodPrice">
			<span class="dolg">Рассрочка: </br>от <span class="boldPrice"><?php echo round((($this->product->prices[product_price])/3), 2) ?></span> р./мес.</span>
		</div>
		<div class="col-md-2 col-xs-6 prodPrice">
			<label class="form-check-label" style="font-weight: normal">
				<input class="form-check-input" type="checkbox"> Самовывоз (скидка&nbsp;5р.)
			</label>
		</div>
		<div class="col-md-2 col-xs-6 prodPrice">
			<label class="form-check-label" style="font-weight: normal">
				<?php
				if (($this->product->product_weight > 0) && ((int)$this->product->product_length > 0)) :
				?>
				<input class="form-check-input" type="checkbox"> Сдать аккумулятор
				<?php endif; ?>
			</label>
		</div>
		<div class="col-md-2 col-xs-6 prodPrice">
			<?php
			//echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
			
			<div class="addtocart-area-special">
				<form method="post" class="product js-recalculate" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart',false); ?>" autocomplete="off" >
					<div class="vm-customfields-wrap">
						<?php
						if(!empty($rowHeights['customfields'])) {
							foreach($positions as $pos){
								echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>$pos));
							}
						} ?>
					</div>			
						<?php
						if (!VmConfig::get('use_as_catalog', 0)  ) {
							echo shopFunctionsF::renderVmSubLayout('addtocartbar',array('product'=>$this->product));
						} ?>
					<input type="hidden" name="option" value="com_virtuemart"/>
					<input type="hidden" name="view" value="cart"/>
					<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $this->product->virtuemart_product_id ?>"/>
					<input type="hidden" name="pname" value="<?php echo $this->product->product_name ?>"/>
					<input type="hidden" name="pid" value="<?php echo $this->product->virtuemart_product_id ?>"/>
					<?php
					$itemId=vRequest::getInt('Itemid',false);
					if($itemId){
						echo '<input type="hidden" name="Itemid" value="'.$itemId.'"/>';
					} ?>
				</form>

			</div>
			
			
		</div>
	
	</div>
		
	</div>
	
	

	

	<?php
	// PDF - Print - Email Icon
	if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
	?>
		<div class="icons">
		<?php

		$link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
		//echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
		echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
		$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
		echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
		?>
		<div class="clear"></div>
		</div>
	<?php } // PDF - Print - Email Icon END
	?>

	<?php
	// Product Short Description
	if (!empty($this->product->product_s_desc)) {
	?>
		<div class="product-short-description">
		<?php
		// @todo Test if content plugins modify the product description 
		echo nl2br($this->product->product_s_desc);
		?>
		</div>
	<?php
	} // Product Short Description END

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
	?>

	<div class="vm-product-container">
	<div class="vm-product-media-container">

	</div>

	
	<div class="clear"></div>


	</div>


	<?php
	//echo ($this->product->product_in_stock - $this->product->product_ordered);
	// Product Description
	if (!empty($this->product->product_desc)) {
		?>
		<div class="product-description" >
	<?php // @todo Test if content plugins modify the product description  ?>
		<span class="title"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></span>
	<?php echo $this->product->product_desc; ?>
		</div>
	<?php
	} // Product Description END

	//echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal'));

	// Product Packaging
	$product_packaging = '';
	if ($this->product->product_box) {
	?>
		<div class="product-box">
		<?php
			echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
		?>
		</div>
	<?php } // Product Packaging END ?>

	<?php
	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories'));

	?>

<?php // onContentAfterDisplay event
echo $this->product->event->afterDisplayContent;

echo $this->loadTemplate('reviews');

// Show child categories
if ($this->cat_productdetails)  {
	echo $this->loadTemplate('showcategory');
}
	
	
	// {DST orign
	/*
	// Product Navigation
	if (VmConfig::get('product_navigation', 1)) {
	?>
		<div class="product-neighbours">
		<?php
		if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $prev_link, $this->product->neighbours ['previous'][0]
			['product_name'], array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '1'));
		}
		if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('rel'=>'next','class' => 'next-page','data-dynamic-update' => '1'));
		}
		?>
		<div class="clear"></div>
		</div>
	<?php } // Product Navigation END
	?>

	<?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
		$categoryName = vmText::_($this->product->category_name) ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
	?>
	<div class="back-to-category">
		<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
	</div>

	<?php // Product Title   ?>
	<h1><?php echo $this->product->product_name ?></h1>
	<?php // Product Title END   ?>

	<?php // afterDisplayTitle Event
	echo $this->product->event->afterDisplayTitle ?>

	<?php
	// Product Edit Link
	echo $this->edit_link;
	// Product Edit Link END
	?>

	<?php
	// PDF - Print - Email Icon
	if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
	?>
		<div class="icons">
		<?php

		$link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
		//echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
		echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
		$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
		echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
		?>
		<div class="clear"></div>
		</div>
	<?php } // PDF - Print - Email Icon END
	?>

	<?php
	// Product Short Description
	if (!empty($this->product->product_s_desc)) {
	?>
		<div class="product-short-description">
		<?php
		// @todo Test if content plugins modify the product description 
		echo nl2br($this->product->product_s_desc);
		?>
		</div>
	<?php
	} // Product Short Description END

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
	?>

	<div class="vm-product-container">
	<div class="vm-product-media-container">
<?php
echo $this->loadTemplate('images');
?>
	</div>

	<div class="vm-product-details-container">
		<div class="spacer-buy-area">

		<?php
		// TODO in Multi-Vendor not needed at the moment and just would lead to confusion
		*/
		/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
		  $text = vmText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
		  echo '<span class="bold">'. vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
		 */
		/*
		?>

		<?php
		echo shopFunctionsF::renderVmSubLayout('rating', array('showRating' => $this->showRating, 'product' => $this->product));

		foreach ($this->productDisplayTypes as $type=>$productDisplayType) {

			foreach ($productDisplayType as $productDisplay) {

				foreach ($productDisplay as $virtuemart_method_id =>$productDisplayHtml) {
					?>
					<div class="<?php echo substr($type, 0, -1) ?> <?php echo substr($type, 0, -1).'-'.$virtuemart_method_id ?>">
						<?php
						echo $productDisplayHtml;
						?>
					</div>
					<?php
				}
			}
		}

		//In case you are not happy using everywhere the same price display fromat, just create your own layout
		//in override /html/fields and use as first parameter the name of your file
		echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));
		?> <div class="clear"></div><?php
		echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));

		echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product));

		// Ask a question about this product
		if (VmConfig::get('ask_question', 0) == 1) {
			$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
			?>
			<div class="ask-a-question">
				<a class="ask-a-question" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
			</div>
		<?php
		}
		?>

		<?php
		// Manufacturer of the Product
		if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
			echo $this->loadTemplate('manufacturer');
		}
		?>

		</div>
	</div>
	<div class="clear"></div>


	</div>
<?php
	$count_images = count ($this->product->images);
	if ($count_images > 1) {
		echo $this->loadTemplate('images_additional');
	}

	// event onContentBeforeDisplay
	echo $this->product->event->beforeDisplayContent; ?>

	<?php
	//echo ($this->product->product_in_stock - $this->product->product_ordered);
	// Product Description
	if (!empty($this->product->product_desc)) {
		?>
		<div class="product-description" >
	<?php // @todo Test if content plugins modify the product description  ?>
		<span class="title"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></span>
	<?php echo $this->product->product_desc; ?>
		</div>
	<?php
	} // Product Description END

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal'));

	// Product Packaging
	$product_packaging = '';
	if ($this->product->product_box) {
	?>
		<div class="product-box">
		<?php
			echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
		?>
		</div>
	<?php } // Product Packaging END ?>

	<?php
	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories'));

	?>

<?php // onContentAfterDisplay event
echo $this->product->event->afterDisplayContent;

echo $this->loadTemplate('reviews');

// Show child categories
if ($this->cat_productdetails)  {
	echo $this->loadTemplate('showcategory');
}

// }DST orign
*/

$j = 'jQuery(document).ready(function($) {
	$("form.js-recalculate").each(function(){
		if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
			var id= $(this).find(\'input[name="virtuemart_product_id[]"]\').val();
			Virtuemart.setproducttype($(this),id);

		}
	});
});';
//vmJsApi::addJScript('recalcReady',$j);

if(VmConfig::get ('jdynupdate', TRUE)){

	/** GALT
	 * Notice for Template Developers!
	 * Templates must set a Virtuemart.container variable as it takes part in
	 * dynamic content update.
	 * This variable points to a topmost element that holds other content.
	 */
	$j = "Virtuemart.container = jQuery('.productdetails-view');
Virtuemart.containerSelector = '.productdetails-view';
//Virtuemart.recalculate = true;	//Activate this line to recalculate your product after ajax
";

	vmJsApi::addJScript('ajaxContent',$j);

	$j = "jQuery(document).ready(function($) {
	Virtuemart.stopVmLoading();
	var msg = '';
	$('a[data-dynamic-update=\"1\"]').off('click', Virtuemart.startVmLoading).on('click', {msg:msg}, Virtuemart.startVmLoading);
	$('[data-dynamic-update=\"1\"]').off('change', Virtuemart.startVmLoading).on('change', {msg:msg}, Virtuemart.startVmLoading);
});";

	vmJsApi::addJScript('vmPreloader',$j);
}

echo vmJsApi::writeJS();

if ($this->product->prices['salesPrice'] > 0) {
	echo shopFunctionsF::renderVmSubLayout('snippets',array('product'=>$this->product, 'currency'=>$this->currency, 'showRating'=>$this->showRating));
}

?>
</div>