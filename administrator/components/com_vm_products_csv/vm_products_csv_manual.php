<?php
/*
 * Doco for Joomla Virtuemart component com_vm_products_csv
 * @title		com_vm_products_csv
 * @version		3.0.3
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

define('_JEXEC', true); // Not only for JED
defined('_JEXEC') or die('Restricted access!'); // for JED

$page_title = 'Doco for Joomla Component VM Products CSV !ULTIMATE! v3 by eKerner.com';
echo('
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>' . $page_title . '</title>
		<meta name="IEsux" http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta name="description" content="' . $page_title . '" />
		<meta name="keywords" content="eKerner, app, application, developer, technical, solutions, eugene kerner, Australia, Joomla, 2.5, 3, Virtuemart, 2, product, products, component, plugin, csv, spreadsheet, bulk, import, export, update" />
		<meta name="author" content="Eugene Kerner ~ eKerner.com.au ~ IT Consultancy in Australia" />
		<meta name="copyright" content="Copyright &copy; 2012 Eugene Kerner ~ eKerner.com.au ~ IT Consultancy in Australia" />
		<meta name="robots" content="index,follow" />
		<style type="text/css">
			body {
				margin:40px;
			}
			#body_wrap {
				width:100%;
				margin:0 auto;
			}
			p, .p {
				margin:0 40px;
			}
		</style>
	</head>
	<body><div id="body_wrap">
');
require(realpath(dirname(__FILE__)) . '/vm_products_csv.php');
echo('
		<div style="margin:0 40px; width:100%; text-align:center;">
			<p>
				Copyright &copy; ' . date('Y') . ' <a href="http://www.ekerner.com/" title="eKerner - Application Developer">eKerner.com</a>
			</p>
			<p>
				<a href="http://validator.w3.org/check?uri=referer" onclick="window.open(this.href); return false;" title="Valid XHTML 1.0 Strict">
					<img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" height="31" width="88" />
				</a>
			</p>
		</div>
	</div></body>
</html>
');
?>
