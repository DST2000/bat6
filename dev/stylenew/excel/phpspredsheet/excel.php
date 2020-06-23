<?php
defined('_JEXEC') or die;

/* /{DST */
$user=JFactory::getUser();
if($user->id<1){ 
	exit();
}  
/* /}DST */

//require '../vendor/autoload.php';
require 'vendor/autoload.php';
//require 'plugins/csviaddon/virtuemart/com_virtuemart/model/export/price.php';

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/*require_once __DIR__ . '/../Bootstrap.php';*/

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}

/* {DST */
/* Get data for table*/




//$https_user 	= '191033183';
//$https_password = '191033183BB';
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
?>

<?php
// api.php file
function api(){
    if(!empty($_POST)){
        print_r($_POST);
    }else{
        echo file_get_contents('php://input');
    }
}
// Открываем файл с помощью установленных выше HTTP-заголовков
//$file = file_get_contents('http://bat6/index.php?option=com_csvi&view=export&csvi_template_id=80&key=batautotrade&task=export', false, $context);




// Return array with content and headers
$res = HttpPost($url, array('name' => 'Jombo', 'id' => 247), true);

$DataTable = $res[content]; 

print_r($DataTable);

//$goodstable = curl_get_file_contents('http://bat6/index.php?option=com_csvi&view=export&csvi_template_id=80&key=batautotrade&task=export');
//echo 'begin';
//echo $goodstable;
exit();



/* }DST */

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

if ($res != '') {
	$currentNumber = 1;
	$element = $res[$currentNumber-1];
	// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue("A$currentNumber", "$element")
    ->setCellValue('B2', 'world!')
    ->setCellValue('C1', 'Hello')
    ->setCellValue('D2', 'world!');
}
/*
// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Hello')
    ->setCellValue('B2', 'world!')
    ->setCellValue('C1', 'Hello')
    ->setCellValue('D2', 'world!');
*/
/*
// Miscellaneous glyphs, UTF-8
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A4', 'Miscellaneous glyphs')
    ->setCellValue('A5', 'Тест прайс-листа');
*/

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
 