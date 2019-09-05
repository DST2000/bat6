 <?php  
$user = JFactory::getUser();
$userid = $user->get('id');		 
		 
if ($userid > 0) {
	$db = JFactory::getDBO();
	$query = 'SELECT `discounts` '.' FROM `#__virtuemart_userinfos` '.' WHERE `virtuemart_user_id` = '.$userid;
	$db->setQuery($query);
	$result1 = $db->LoadResult();
	$discounts_j_arr = explode("|", $result1);
	
	foreach ($discounts_j_arr as $discounts_j) {
		$discounts[] = json_decode($discounts_j, true);	
	} 

	if (count($discounts) > 0) {
		echo '<div class="discounts" style="display:none">';
		foreach ($discounts as $key_a => $discount) {
			echo '<div class="discount '.$key_a.'">';
			foreach ($discount as $key => $value) {
				echo '<p class="'.$key.'">'.$value.'</p>';
			}
			echo '</div>';
		}
		echo '</div>';
	}
}



	/* {/DST basePrice  */
		$user = JFactory::getUser();
		$userid = $user->get('id');		 

	if ($product->get('product_discontinued') < 1) {
		if ($userid > 0) {
//			$this->productPrices['salesPrice'] = '888';
//			$this->productPrices['discountedPriceWithoutTax'] = '888';
			
			$product_sku = $product->get('product_sku');
			$product_id = $product->get('virtuemart_product_id');
			if (strlen($product_sku) > 0) {
				// Получен $product_sku
				// Получение базы скидок для клиента
				
				$db = JFactory::getDBO();
				$query = 'SELECT `discounts` '.' FROM `#__virtuemart_userinfos` '.' WHERE `virtuemart_user_id` = '.$userid;
				$db->setQuery($query);
				$result1 = $db->LoadResult();
				$discounts_j_arr = explode("|", $result1);

				foreach ($discounts_j_arr as $discounts_j) {
					$discounts[] = json_decode($discounts_j, true);	
				} 
				$discount_id = '';
				$discount_product_path = '';
				$discount_value = 0;
//				echo '<br/> product sku = '.$product_sku.'<br/>';
				if (count($discounts) > 0) {
					foreach ($discounts as $discount) {
//						echo '<pre class="discount">';
//						echo '<pre>';
//						print_r($discount);
//						echo '</pre>';
						foreach ($discount as $key => $value) {
							if ($key=='id') {
								if ($product_sku == $value) {
									/*echo ('equivalent $product_sku = '.$product_sku
										  .'<br/> id = '.$value
										  .'<br/><br/>');*/
									$discount_id = $value;
								}
							}
							if (($key=='value') && (strlen($discount_id) > 0) && ($product_sku == $discount_id)) {
								/*echo ('discount value with $discount_id = '.$discount_id
										  .'<br/> value = '.$value
										  .'<br/><br/>');*/
								$discount_value = $value;
								break; // unblock after
							}
						}
						//echo '</pre>';
						
					}
					if ((strlen($discount_id) > 0) && ($discount_value > 0)) {
//							echo '<pre> DISCOUNT VALUE by ID<br/>';
//							echo ('Discount '.$discount_value.'<br/>');
//							echo ('$discount_id '.$discount_id.'<br/>');					 
						}
					elseif ($discount_value == 0) {
						// поиск по product_path товара <product_path>00000000007/00000000001</product_path>
						// среди последних строк product_path скидок клиента "product_path":"00000000006/00000000003"
						$customfieldsModel = VmModel::getModel('Customfields');
						$virtuemart_custom_id = (int)323;
											
						$db2 = JFactory::getDBO();
						$query2 = 'SELECT `customfield_value`,`virtuemart_product_id` FROM `#__virtuemart_product_customfields` WHERE  
						`#__virtuemart_product_customfields`.`virtuemart_product_id` LIKE '.$product_id
						.' AND `#__virtuemart_product_customfields`.`virtuemart_custom_id` LIKE '.$virtuemart_custom_id;
						$db2->setQuery($query2);
						$result2 = $db2->LoadResult();
//						echo '<pre> product path from product<br/>';
//						print_r($result2);
//						echo '<br/>';
//						echo '</pre>';
						$product_paths = explode("/", $result2);
						$product_paths_reversed = array_reverse($product_paths);
//						echo('<br/> product_path from product <br/>');
//						echo '<pre>';
//							print_r($product_paths_reversed);
//						echo '<br/>';
//						echo '</pre>';	
						if (count($product_paths_reversed) > 0) {
//							echo '<br/> Start compare <br/>';
							foreach ($product_paths_reversed as $product_paths_compare) {
								foreach ($discounts as $discount) {
									foreach ($discount as $key => $value) {
										if ($key=='product_path') {
											$product_patch_last = explode("/", $value);
											$product_patch_last_reversed = array_reverse($product_patch_last);
											if ($product_patch_last_reversed[0] == $product_paths_compare) {
//												echo ('<pre> Product patch reversed[0] - matched<br/>');
//												echo ('$product_patch_last_reversed[0] '.$product_patch_last_reversed[0].'<br/>');
//												echo ('$product_paths_compare '.$product_paths_compare.'<br/>');
//												echo '<br/> --- <br/>';
//												echo '</pre>';
												$discount_product_path = $value;	
											}										
											else {
//												echo '<pre> $product_paths_compare not match<br/>';
//												echo ('$product_patch_last_reversed[0] '.$product_patch_last_reversed[0].'<br/>');
//												echo ('$product_paths_compare '.$product_paths_compare.'<br/>');
//												echo '<br/> --- <br/>';
//												echo '</pre>';
											}
										} // if ($key=='product_path')
										if (($key=='value') && (strlen($discount_product_path) > 0) && ($discount_value==0)) {	
												$discount_value = $value;
//												echo '<pre> DISCOUNT VALUE by PRODUCT_PATH<br/>';
//												echo ('Discount '.$discount_value.'<br/>');
//												echo ('Product patch '.$discount_product_path.'<br/>');
//												echo '</pre>';
												break; // unblock after
										} // if (($key=='value') && (strlen($discount_product_path) > 0))	
									} // foreach ($discount as $key => $value) 
								} // foreach ($discounts as $discount)
								
							} //foreach ($product_paths_reversed as $product_paths_compare)
						} //if (count($product_paths_reversed) > 0)
					}
					}
					if ($discount_value > 0) {
						$this->productPrices['salesPrice'] = ($this->productPrices['salesPrice'])*((100-$discount_value)/100);
					}
				}	
			}
	} //if ($product->get('product_discontinued') < 1) 

		/* }/DST basePrice */
	 
?>