<?php

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
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Hello')
    ->setCellValue('B2', 'world!')
    ->setCellValue('C1', 'Hello')
    ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A4', 'Miscellaneous glyphs')
    ->setCellValue('A5', 'Тест прайс-листа');

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Simple');

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
 