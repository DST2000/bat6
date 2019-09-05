<?php
/*
 * @title		com_vm_products_csv
 * @version		3.0.3h
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!'); 
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
require_once(JPATH_ADMINISTRATOR . DS.'components'.DS.'com_vm_products_csv'.DS.'vm_products_csvUtils.php');
JHTML::_('behavior.tooltip');
$csv_files = vm_products_csvUtils::getCsvList();
$have_report = is_array($this->operations_report) && count($this->operations_report);
$csv_url = null;
if ($have_report) {
	$csv_uri = $this->operations_report['csv_uri'];
	unset($this->operations_report['csv_uri']);
	if ($csv_uri && file_exists(JPATH_ADMINISTRATOR . DS . $csv_uri))
		$csv_url = vm_products_csvUtils::admin_url() . $csv_uri;
}
?>

<form enctype="multipart/form-data" method="post" action="index.php?option=com_vm_products_csv&view=Export_products&task=export_products.doexport"
name="export">
<fieldset><legend><?php echo JText::_('COM_VMPCSV_PRODUCTS_EXPORT_TITLE'); ?></legend>
	<div style="clear:both;">
		<span style="position:relative; top:7px; float:left; margin-right:4px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_LANGUAGE'); ?>:</span>
		<select name="vmlang">
<?php
	foreach ($this->vmlangs as $code => $title) {
		echo("\t\t\t\t<option value=\"{$code}\"");
		if (isset($_REQUEST['vmlang']) && $_REQUEST['vmlang'] == $code)
			echo(' selected="selected"');
		echo(">{$title}</option>\n");
	}
?>
		</select>
	</div>
	<div style="clear:both;">
		<span style="position:relative; top:7px; float:left; margin-right:4px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_ENCODING'); ?>:</span>
<?php
	$encoding = (empty($_REQUEST['encoding'])) ? null : $_REQUEST['encoding'];
	echo(vm_products_csvUtils::getCharsetSelectHtml($encoding));
?>
	</div>
	<div style="clear:both;">
		<input type="checkbox" value="1" name="multicat"<?php if (!empty($_REQUEST['multicat'])) echo ' checked="checked"'; ?> />
		<span style="position:relative; top:6px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_MULTICAT_MODE'); ?>:
		<small><?php echo JText::_('COM_VMPCSV_PRODUCTS_MULTICAT_MODE_DESC'); ?></small>
		</span>
	</div>
	<div style="clear:both;">
		<button style="color:#204D02; font-weight:bold;" value="Export" name="Export"><?php echo JText::_('COM_VMPCSV_PRODUCTS_EXPORT_SUBMITBTN'); ?></button>
<?php
if ($csv_url) {
	echo('<input type="hidden" name="csv_uri" value="' . $csv_url . '" />');
	$label = JText::_('COM_VMPCSV_PRODUCTS_EXPORT_DOWNLOADBTN');
	echo('<button title="' . $label . '" href="' . $csv_url . '" onclick="location.href = \'' . $csv_url . '\'; return false;">' . $label . '</button>');
}
?>
	</div>
</fieldset>
<?php
if ($csv_files) {
	echo('<fieldset><legend>' . JText::_('COM_VMPCSV_PRODUCTS_DOWNLOAD_TITLE') . '</legend>');
	$label = JText::_('COM_VMPCSV_PRODUCTS_EXPORT_DELETEBTN') . ' [' . count($csv_files) . ']';
	echo('<input type="hidden" name="delete_csv" value="0" />');
	echo('<button title="' . $label . '" onclick="this.form.delete_csv.value=1; this.form.submit();">' . $label . '</button><br class="clr">' . "\n" . '<ol>');
	foreach ($csv_files as $csv_file) {
		$basename = basename($csv_file);
		//$csv_url = vm_products_csvUtils::admin_url() . str_replace(JPATH_ADMINISTRATOR . DS, '', $csv_file);
		$csv_url = vm_products_csvUtils::admin_url() . vm_products_csvUtils::$csv_uri . $basename;
		echo('<li><a href="' . $csv_url . '" title="' . JText::_('COM_VMPCSV_PRODUCTS_EXPORT_DOWNLOADBTN') . ' ' . $basename . '">' . $basename . '</a></li>');
	}
	echo('</ol></fieldset>');
}
vm_products_csvUtils::drawHelpFieldset();
vm_products_csvUtils::drawVersionFieldset(); 
?>
</form>
<?php if($have_report) {
?>
<hr>
<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_VMPCSV_PRODUCT_ID') ?></th>
				<th><?php echo JText::_('COM_VMPCSV_PRODUCT_SKU') ?></th>
				<th><?php echo JText::_('COM_VMPCSV_PRODUCT_NAME') ?></th>
				<th><?php echo JText::_('COM_VMPCSV_PRODUCT_STATUS') ?></th>
				<th><?php echo JText::_('COM_VMPCSV_PRODUCT_REPORT') ?></th>
			</tr>
		</thead><tbody>
<?php foreach($this->operations_report as $i => $product): 
		if($product['status'] == JText::_('COM_VMPCSV_PRODUCT_EXPORTED')) { $style="green"; } 
		else { $style="red"; } ?>
					<tr class="row<?php echo $i % 2 ?>">
					<td class="center" style="background-color: <?php echo $style; ?>">
						<?php echo $product['product_id']; ?>
					</td>
					<td>
						<?php echo $product['product_sku']; ?>
					</td>
					<td>
						<?php echo $product['product_name']; ?>
					</td>
					<td>
						<?php echo $product['status']; ?>
					</td>
					<td>
						<?php echo $product['report']; ?>
					</td>

				</tr>
<?php endforeach ?>
	</tbody></table>
<?php } ?>
