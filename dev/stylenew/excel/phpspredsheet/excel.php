<?php

defined('_JEXEC') or die;

$url = 'http://bat6/index.php?option=com_csvi&view=export&csvi_template_id=80&key=batautotrade&task=export';

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
                            'timeout' => 60,
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

//require '../vendor/autoload.php';
require 'vendor/autoload.php';

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

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A1", "Номенклатура")
    ->setCellValue("B1", "Артикул")
    ->setCellValue("C1", "Бренд")
    ->setCellValue("D1", "Продуктовая лиейка")
    ->setCellValue("E1", "Ёмкость")
    ->setCellValue("F1", "Высота")
    ->setCellValue("G1", "Длина")
    ->setCellValue("H1", "Ширина")
    ->setCellValue("I1", "Полярность")
    ->setCellValue("J1", "Код")
    ->setCellValue("K1", "Пусковой ток")
    ->setCellValue("L1", "Напряжение")
    ->setCellValue("M1", "Объем")
    ->setCellValue("N1", "Вес")
    ->setCellValue("O1", "Вес2")
    ->setCellValue("P1", "Гарантия")
    ->setCellValue("Q1", "Цена Базовая")
    ->setCellValue("R1", "Остаток")
    ->setCellValue("S1", "Путь");

for ($i = 1; $i <= $DataArrayCount; $i++) {
//for ($i = 1; $i <= 5; $i++) {
	$ArrayOfString = explode("|",  $DataArray[$i]);
	$j = $i + 1;
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A$j", "$ArrayOfString[0]")
    ->setCellValue("B$j", "$ArrayOfString[1]")
    ->setCellValue("C$j", "$ArrayOfString[2]")
    ->setCellValue("D$j", "$ArrayOfString[3]")
    ->setCellValue("E$j", "$ArrayOfString[4]")
    ->setCellValue("F$j", "$ArrayOfString[5]")
    ->setCellValue("G$j", "$ArrayOfString[6]")
    ->setCellValue("H$j", "$ArrayOfString[7]")
    ->setCellValue("I$j", "$ArrayOfString[8]")
    ->setCellValue("J$j", "$ArrayOfString[9]")
    ->setCellValue("K$j", "$ArrayOfString[10]")
    ->setCellValue("L$j", "$ArrayOfString[11]")
    ->setCellValue("M$j", "$ArrayOfString[12]")
    ->setCellValue("N$j", "$ArrayOfString[13]")
    ->setCellValue("O$j", "$ArrayOfString[14]")
    ->setCellValue("P$j", "$ArrayOfString[15]")
    ->setCellValue("Q$j", "$ArrayOfString[16]")
    ->setCellValue("R$j", "$ArrayOfString[17]")
    ->setCellValue("S$j", "$ArrayOfString[18]");	
	
	
//	echo $ArrayOfString[0];
//	echo $ArrayOfString[1];
//	echo $ArrayOfString[2];
//	echo $ArrayOfString[3];
//	echo $ArrayOfString[4];
//	echo $ArrayOfString[5];
//	echo $ArrayOfString[6];
//	echo $ArrayOfString[7];
//	echo $ArrayOfString[8];
//	echo $ArrayOfString[9];
//	echo $ArrayOfString[10];
//	echo $ArrayOfString[11];
//	echo $ArrayOfString[12];
//	echo $ArrayOfString[13];
//	echo $ArrayOfString[14];
//	echo $ArrayOfString[15];
//	echo $ArrayOfString[16];
//	echo $ArrayOfString[17];
//	echo $ArrayOfString[18];
}

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
 