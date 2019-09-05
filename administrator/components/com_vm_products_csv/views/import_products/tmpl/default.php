<?php
/*
 * @title		com_vm_products_csv
 * @version		3.0.3
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!'); 
require_once(JPATH_ADMINISTRATOR . '/components/com_vm_products_csv/vm_products_csvUtils.php');
JHTML::_('behavior.tooltip');

$export_products_title = JText::_('COM_VMPCSV_PRODUCTS_EXPORT_TITLE');
$export_products_link = vm_products_csvUtils::admin_url() . 'index.php?option=com_vm_products_csv&amp;view=export_products';
$download_blank_button = JText::_('COM_VMPCSV_DOWNLOAD_BLANK_BTN');
$download_blank_link = vm_products_csvUtils::admin_url() . 'components/com_vm_products_csv/virtuemart_products_blank.csv';
$download_example_button = JText::_('COM_VMPCSV_DOWNLOAD_EXAMPLE_BTN');
$download_example_link = vm_products_csvUtils::admin_url() . 'components/com_vm_products_csv/virtuemart_products_example.csv';
$media_gallery_button = JText::_('COM_VMPCSV_IMPORT_MEDIA_BTN');
$media_gallery_onclick = "var anchor = document.getElementById('toolbar-popup-upload').children[0]; if(document.dispatchEvent) {var click = document.createEvent('MouseEvents'); click.initMouseEvent('click', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, anchor); anchor.dispatchEvent(click);} else anchor.click();";
?>

<form enctype="multipart/form-data" method="post" action="index.php?option=com_vm_products_csv&view=Import_products&task=import_products.doimport"
name="import">
	<fieldset>
		<legend><?php echo JText::_('COM_VMPCSV_PRODUCTS_IMPORT_TITLE'); ?></legend>
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php ini_get('upload_max_filesize'); ?>" />
		<div style="clear:both;">
			<span style="position:relative; top:7px; float:left; margin-right:4px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_CSV_FILE'); ?>:</span>
			<input name="csvfile" type="file">
		</div>
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
			<input type="radio" name="import_mode" value="add"<?php if(isset($_REQUEST['import_mode']) && $_REQUEST['import_mode'] == 'add') echo(' checked="checked"');?> />
			<span style="position:relative; top:6px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_IMPORT_MODE_ADD'); ?></span>
		</div>
		<div style="clear:both;">
			<input id="import_mode_update" type="radio" name="import_mode" value="update"<?php if(!isset($_REQUEST['import_mode']) || $_REQUEST['import_mode'] == 'update') echo(' checked="checked"');?> />
			<span style="position:relative; top:6px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_IMPORT_MODE_UPDATE'); ?></span>
		</div>
		<div style="clear:both;">
			<input type="radio" name="import_mode" value="sync"<?php if(isset($_REQUEST['import_mode']) && $_REQUEST['import_mode'] == 'sync') echo(' checked="checked"');?> onclick="if (!confirm('<?php echo addslashes(JText::_('COM_VMPCSV_PRODUCTS_IMPORT_MODE_SYNC_WARN'));?>')) document.getElementById('import_mode_update').checked = 'checked';" />
			<span style="position:relative; top:6px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_IMPORT_MODE_SYNC'); ?></span>
		</div>
		<div style="clear:both;">
			<input type="checkbox" name="create_manufacturers" value="1"<?php if(!isset($_REQUEST['create_manufacturers']) || $_REQUEST['create_manufacturers']) echo(' checked="checked"');?> />
			<span style="position:relative; top:6px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_CREATE_MANUFACTURERS'); ?></span>
		</div>
		<div id="vmpcsv_create_categories" style="clear:both;">
			<input type="checkbox" name="create_categories" value="1"<?php if(!isset($_REQUEST['create_categories']) || $_REQUEST['create_categories']) echo(' checked="checked"');?> />
			<span style="position:relative; top:6px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_CREATE_CATEGORIES'); ?></span>
		</div>
		<div style="clear:both;">
			<input id="vmpcsv_multicat" onclick="vmpcsvMulticatCbChanged();" type="checkbox" value="1" name="multicat"<?php if (!empty($_REQUEST['multicat'])) echo ' checked="checked"'; ?> />
			<span style="position:relative; top:6px;"><?php echo JText::_('COM_VMPCSV_PRODUCTS_MULTICAT_MODE'); ?>:
			<small><?php echo JText::_('COM_VMPCSV_PRODUCTS_MULTICAT_MODE_DESC'); ?></small>
			</span>
			<script type="text/javascript">
				function vmpcsvMulticatCbChanged()
				{
					document.getElementById('vmpcsv_create_categories').style.display = 
						document.getElementById('vmpcsv_multicat').checked ? 'none' : '';
				}
				vmpcsvMulticatCbChanged();
			</script>
		</div>
		<div style="clear:both;">
			<br>
			<button style="color:#204D02; font-weight:bold;" value="Import" name="Import"><?php echo JText::_('COM_VMPCSV_PRODUCTS_IMPORT_SUBMITBTN'); ?></button> <button
			value="Reset" name="Reset" type="reset"><?php echo JText::_('COM_VMPCSV_RESETBTN'); ?></button><br>
		</div>
	</fieldset>
<?php if(is_array($this->operations_report) && count($this->operations_report))
{
?>
	<fieldset>
		<legend><?php echo JText::_('COM_VMPCSV_IMPORT_RESULTS_TITLE'); ?></legend>
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
		if($product['status'] == JText::_('COM_VMPCSV_PRODUCT_IMPORTED')) { $style="green"; } 
		elseif($product['status'] == JText::_('COM_VMPCSV_PRODUCT_UPDATED')) { $style="blue"; } 
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
	</fieldset>
<?php } ?>
	<fieldset>
		<legend><?php echo JText::_('COM_VMPCSV_PRODUCTS_PREPARE_TITLE'); ?></legend>
		<div style="clear:both;">
			<button title="<?php echo $export_products_title; ?>" href="<?php echo $export_products_link; ?>" onclick="location.href='<?php echo $export_products_link; ?>'; return false;"><?php echo $export_products_title; ?></button> <button
			title="<?php echo $download_blank_button; ?><" href="<?php echo $download_blank_link; ?>" onclick="location.href='<?php echo $download_blank_link; ?>'; return false;"><?php echo $download_blank_button; ?></button> <button
			title="<?php echo $download_example_button; ?>" href="<?php echo $download_example_link; ?>" onclick="location.href='<?php echo $download_example_link; ?>'; return false;"><?php echo $download_example_button; ?></button> <button
			title="<?php echo $media_gallery_button; ?>" href="javascript:<?php echo $media_gallery_onclick; ?>;" onclick="<?php echo $media_gallery_onclick; ?>; return false;"><?php echo $media_gallery_button; ?></button>
		</div>
	</fieldset>
<?php 
	vm_products_csvUtils::drawHelpFieldset(); 
	vm_products_csvUtils::drawVersionFieldset(); 
?>
</form>
