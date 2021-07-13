<?php

defined('_JEXEC') or die;

//$url = 'http://bat6/index.php?option=com_csvi&view=export&csvi_template_id=80&key=batautotrade&task=export';
$url = 'https://bat.by/index.php?option=com_csvi&view=export&csvi_template_id=78&key=batautotrade&task=export';

function HttpPost ($url, $data, $json = false, $selfsigned = false, $token = '', $user = '', $pass = ''){
   
    $data_url = http_build_query ($data);
    $data_len = strlen ($data_url);

    if($json == true){
        // text/html,
        // content-type application/x-www-form-urlencoded
        $content = "Content-type: application/json; charset=utf-8";
        $data_url = json_encode($data);
        $data_len = strlen ($data_url);
    }

    if(!empty($user) && !empty($pass)){
        $auth = "Authorization: Basic ".base64_encode("$user:$pass")."\r\n";
    }

    if(!empty($token)){
        $token = "Authorization: Bearer ".$token;
    }

    if($selfsigned == true){
        $ssl = array(
            "verify_peer" => false,
            "allow_self_signed" => true,
        );
    }else{
        $ssl = array(
            "verify_peer" => true,
            "allow_self_signed" => false,
        );
    }

    return array (
        'content' =>
            file_get_contents (
                $url,
                false,
                stream_context_create (
                    array (
                        'ssl' => $ssl,
                        'http'=>
                        array (
                            'timeout' => 120,
                            'method'=> 'POST'
                            , 'header'=>
                                    $content .
                                    "Access-Control-Allow-Origin: *".
                                    "X-Frame-Options: sameorigin".
                                    "Connection: close\r\n"
                                    ."Content-Length: $data_len\r\n"
                                    .$auth
                                    .$token
                            , 'content'=>$data_url
                        )
                    )
                )
            )
        ,'headers'=>$http_response_header       
    );
}

// api.php file
function api(){
    if(!empty($_POST)){
        print_r($_POST);
    }else{
        echo file_get_contents('php://input');
    }
}


/* /{DST */
$user=JFactory::getUser();
if($user->id<1){ 
	exit();
}  
/* /}DST */

require '../vendor/autoload.php'; //https
//require 'vendor/autoload.php'; //http

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/*require_once __DIR__ . '/../Bootstrap.php';*/

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}




// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Kuk Sergei')
    ->setLastModifiedBy('Kuk Sergei')
    ->setTitle('Price-list_bat_by')
    ->setSubject('Price-list_bat_by')
    ->setDescription('Price-list_bat_by')
    ->setKeywords('price bat_by')
    ->setCategory('price');

// Add some data

$res = HttpPost($url, array('name' => 'Jombo', 'id' => 247), false);

$DataTable = $res['content'];

$DataArray = explode(PHP_EOL, $DataTable);
$DataArrayCount = count($DataArray);

/*$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A1", "Номенклатура")
    ->setCellValue("B1", "Артикул")
    ->setCellValue("C1", "Бренд")
    ->setCellValue("D1", "Продуктовая линейка")
    ->setCellValue("E1", "Ёмкость")
    ->setCellValue("F1", "Габариты")
    ->setCellValue("G1", "Полярность")
    ->setCellValue("H1", "Код")
    ->setCellValue("I1", "Пусковой ток")
    ->setCellValue("J1", "Напряжение")
    ->setCellValue("K1", "Объем")
    ->setCellValue("L1", "Вес")
    ->setCellValue("M1", "Гарантия")
    ->setCellValue("N1", "Цена Базовая")
	->setCellValue("O1", "Скидка")
	->setCellValue("P1", "Цена")
	 ->setCellValue("Q1", "Остаток");*/

/* Orig
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A1", "Номенклатура")		1
    ->setCellValue("B1", "Остаток")				2
    ->setCellValue("C1", "Габариты")			3
    ->setCellValue("D1", "Ёмкость")				4
    ->setCellValue("E1", "Полярность")			5
    ->setCellValue("F1", "Пусковой ток")		6
    ->setCellValue("G1", "Вес")					7
    ->setCellValue("H1", "Бренд")				8
    ->setCellValue("I1", "Артикул")				9
    ->setCellValue("J1", "Цена")				10
    ->setCellValue("K1", "Скидка")				11
    ->setCellValue("L1", "Цена Базовая")		12
    ->setCellValue("M1", "Продуктовая линейка") 13
    ->setCellValue("N1", "Напряжение")			14
	->setCellValue("O1", "Объем")				15
	->setCellValue("P1", "Гарантия")			16
	 ->setCellValue("Q1", "Код");				17 */


$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A1", "Бренд")
    ->setCellValue("B1", "Артикул")
    ->setCellValue("C1", "Номенклатура")
    ->setCellValue("D1", "Ёмкость")
    ->setCellValue("E1", "Пусковой ток")
    ->setCellValue("F1", "Полярность")
    ->setCellValue("G1", "Габариты")
    ->setCellValue("H1", "Вес")
    ->setCellValue("I1", "Остаток")
    ->setCellValue("J1", "Цена");

//    ->setCellValue("Q1", "Остаток")
//    ->setCellValue("R1", "Путь")
//	->setCellValue("S1", "product_sku")
//	->setCellValue("T1", "virtuemart_product_id");

// $DataArray sort
$DataArray = array_slice($DataArray, 1, $DataArrayCount);
sort($DataArray);

for ($i = 1; $i <= $DataArrayCount-2; $i++) {
//for ($i = 1; $i <= 10; $i++) {
	$ArrayOfString = explode("|",  $DataArray[$i]);
	$j = $i + 1;
	
	//*****
	
	$path = $ArrayOfString[12];
	$product_sku  = $ArrayOfString[13];
	$product_id = $ArrayOfString[14];
	
	/* {/DST basePrice  */
		$userid = $user->get('id');		 

	//if ($product->get('product_discontinued') < 1) {
	
			//$product_sku = $product->get('product_sku');
			//$product_id = $product->get('virtuemart_product_id');
			$discount_value = 0;
			if (strlen($product_sku) > 0) {
				// Получен $product_sku
				// Получение базы скидок для клиента
				
				$db = JFactory::getDBO();
				$query = 'SELECT `discounts` '.' FROM `#__virtuemart_userinfos` '.' WHERE `virtuemart_user_id` = '.$userid;
				$db->setQuery($query);
				$result1 = $db->LoadResult();
				
				//print_r($result1);
				
				$discounts_j_arr = explode("|", $result1);

				foreach ($discounts_j_arr as $discounts_j) {
					$discounts[] = json_decode($discounts_j, true);	
				} 
				$discount_id = '';
				$discount_product_path = '';
				
//				echo '<br/> product sku = '.$product_sku.'<br/>';
				if (count($discounts) > 0) {
					foreach ($discounts as $discount) {
//						echo '<pre class="discount">';
//						echo '<pre>';
//						print_r($discount);
//						echo '</pre>';
							if (is_array($discount)) {
								if (count($discount) > 0) {
									foreach ($discount as $key => $value) {
										if (0 == $discount_value) {
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
												$discount_value = (int)$value;
												//break; // unblock after
											}
										}
									}
								}
							}
						//echo '</pre>';
						
					}
					if ((strlen($discount_id) > 0) && ($discount_value > 0)) {
							/*echo '<pre> DISCOUNT VALUE by ID<br/>';
							echo ('Discount '.$discount_value.'<br/>');
							echo ('$discount_id '.$discount_id.'<br/>');*/
						}
					
					elseif (0 == $discount_value) {
						// поиск по product_path товара <product_path>00000000007/00000000001</product_path>
						// среди последних строк product_path скидок клиента "product_path":"00000000006/00000000003"
						//$customfieldsModel = VmModel::getModel('Customfields');
						$virtuemart_custom_id = (int)323; // product_path id
											
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
									if (is_array($discount)) {
										if (count($discount) > 0) {
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
														$discount_value = (int)$value;
		//												echo '<pre> DISCOUNT VALUE by PRODUCT_PATH<br/>';
		//												echo ('Discount '.$discount_value.'<br/>');
		//												echo ('Product patch '.$discount_product_path.'<br/>');
		//												echo '</pre>';
														break; // unblock after
												} // if (($key=='value') && (strlen($discount_product_path) > 0))	
											} // foreach ($discount as $key => $value) 
										}
									}
								} // foreach ($discounts as $discount)
								
							} //foreach ($product_paths_reversed as $product_paths_compare)
						} //if (count($product_paths_reversed) > 0)
					}
					}
					if ($discount_value > 0) {
						$price_discounted = sprintf("%01.2f", $ArrayOfString[11]*((100-$discount_value)/100));
					}else {
						$price_discounted = $ArrayOfString[11];
					}
				}	
	
	//} //if ($product->get('product_discontinued') < 1) 

		/* }/DST basePrice */
	if (strlen($ArrayOfString[2]) > 0) {
	$product_size = "$ArrayOfString[6]/$ArrayOfString[7]/$ArrayOfString[8]";
	} else {
		$product_size = '';
	}
	
	/*$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A$j", "$ArrayOfString[0]")
    ->setCellValue("B$j", "$ArrayOfString[1]")
    ->setCellValue("C$j", "$ArrayOfString[2]")
    ->setCellValue("D$j", "$ArrayOfString[3]")
    ->setCellValue("E$j", "$ArrayOfString[4]")
    ->setCellValue("F$j", "$product_size")
//    ->setCellValue("G$j", "$ArrayOfString[6]")
//    ->setCellValue("H$j", "$ArrayOfString[7]")
    ->setCellValue("G$j", "$ArrayOfString[8]")
    ->setCellValue("H$j", "$ArrayOfString[9]")
    ->setCellValue("I$j", "$ArrayOfString[10]")
    ->setCellValue("J$j", "$ArrayOfString[11]")
    ->setCellValue("K$j", "$ArrayOfString[12]")
    ->setCellValue("L$j", "$ArrayOfString[13]")
    ->setCellValue("M$j", "$ArrayOfString[14]")
    ->setCellValue("N$j", "$ArrayOfString[15]")
	->setCellValue("O$j", "$discount_value")
    ->setCellValue("P$j", "$price_discounted")
	->setCellValue("Q$j", "$ArrayOfString[16]");
//	->setCellValue("Q$j", "$ArrayOfString[16]")	
//	->setCellValue("R$j", "$ArrayOfString[17]")
//    ->setCellValue("S$j", "$ArrayOfString[18]")
//	->setCellValue("T$j", "$ArrayOfString[19]");*/
	
/*
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A$j", "$ArrayOfString[0]")
    ->setCellValue("B$j", "$ArrayOfString[1]")
    ->setCellValue("C$j", "$product_size")
    ->setCellValue("D$j", "$ArrayOfString[5]")
    ->setCellValue("E$j", "$ArrayOfString[6]")
    ->setCellValue("F$j", "$ArrayOfString[7]")
    ->setCellValue("G$j", "$ArrayOfString[8]")
    ->setCellValue("H$j", "$ArrayOfString[9]")
    ->setCellValue("I$j", "$ArrayOfString[10]")
    ->setCellValue("J$j", "$price_discounted")
    ->setCellValue("K$j", "$discount_value")
    ->setCellValue("L$j", "$ArrayOfString[11]")
    ->setCellValue("M$j", "$ArrayOfString[15]")
    ->setCellValue("N$j", "$ArrayOfString[16]")
	->setCellValue("O$j", "$ArrayOfString[17]")
    ->setCellValue("P$j", "$ArrayOfString[18]")
	->setCellValue("Q$j", "$ArrayOfString[19]");	
	*/
	
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A$j", "$ArrayOfString[0]")
    ->setCellValue("B$j", "$ArrayOfString[1]")
    ->setCellValue("C$j", "$ArrayOfString[2]")
    ->setCellValue("D$j", "$ArrayOfString[3]")
    ->setCellValue("E$j", "$ArrayOfString[4]")
    ->setCellValue("F$j", "$ArrayOfString[5]")
    ->setCellValue("G$j", "$product_size")
    ->setCellValue("H$j", "$ArrayOfString[9]")
    ->setCellValue("I$j", "$ArrayOfString[10]")
    ->setCellValue("J$j", "$price_discounted");

	
	
}

$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ]	
    ],
	 'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
    ],
];

//$spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
$spreadsheet->getActiveSheet(0)->getStyle("A1:J$i")->applyFromArray($styleArray);
$spreadsheet->getActiveSheet(0)->setAutoFilter("A1:J$i");

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(16);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(36);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(6);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);


//$spreadsheet->setActiveSheetIndex(0)
//    ->setCellValue('A1', 'Hello')
//    ->setCellValue('B2', 'world!')
//    ->setCellValue('C1', 'Hello')
//    ->setCellValue('D2', 'world!');
//
//// Miscellaneous glyphs, UTF-8
//$spreadsheet->setActiveSheetIndex(0)
//    ->setCellValue('A4', 'Miscellaneous glyphs')
//    ->setCellValue('A5', 'Тест прайс-листа');

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('bat');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xls)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="bat_by_price-list.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xls');
$writer->save('php://output');
exit;
 