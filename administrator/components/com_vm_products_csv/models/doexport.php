<?php
/*
 * @title		com_vm_products_csv
 * @version		3.1.3d
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2014 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!');

jimport('joomla.application.component.modeladmin');
require_once(JPATH_ADMINISTRATOR . '/components/com_vm_products_csv/vm_products_csvUtils.php');

class vm_products_csvModelDoExport extends JModelAdmin
{
	protected function loadNextProducts($lang_ext, $db, &$starting_from, $limit = 100)
	{
		if (!$limit)
			$limit = $this->queryLimit;
		$db->setQuery("
			SELECT
				p.virtuemart_product_id,
				p.virtuemart_vendor_id,
				p.product_parent_id,
				p.product_sku,
				p.product_weight,
				p.product_weight_uom as product_weight_unit,
				p.product_length,
				p.product_width,
				p.product_height,
				p.product_lwh_uom as product_lwh_unit,
				p.product_in_stock as product_stock_quantity,
				p.product_availability,
				p.product_special as featured_product,
				p.published
			FROM
				#__virtuemart_products_{$lang_ext} as l,
				#__virtuemart_products as p
			WHERE
				p.virtuemart_product_id = l.virtuemart_product_id
			LIMIT
				{$starting_from}, {$limit}

		");
		if ($db->getErrorNum()) {
			JError::raiseWarning(100, $db->getErrorMsg());
			return null;
		}
		$products = $db->loadAssocList();
		if (!(is_array($products) && ($num_products = count($products))))
			return null;
		$starting_from += $num_products;//$limit;
		return $products;
	}

	public function do_csv_delete($csv_uri = null)
	{
			$csvs = vm_products_csvUtils::getCsvList($csv_uri);
			if (!$csvs)
				return false;
			foreach ($csvs as $csv)
				if (!unlink($csv))
					JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_FILE_DELETE_FAILURE', $dirent));
			JFactory::getApplication()->enqueueMessage(JText::_('COM_VMPCSV_DELETE_DONE'));
			return true;
	}

	public function do_product_export()
	{
		// make sure this user owns the vendor account selected ...
		$isAdmin = vm_products_csvUtils::isSuperAdminUser();
		// may be a delete request, I know I probably shouldnt have stuck it here ...
		//if ($_REQUEST['delete_csv'] && $_REQUEST['csv_uri'])
			//return $this->do_csv_delete($_REQUEST['csv_uri']); // delete the csv file
		if (!empty($_REQUEST['delete_csv']))
			return $this->do_csv_delete(); //delete all csv files
		// check dependancies ...
		if (!vm_products_csvUtils::requireIconv())
			return false;
		// some vars ...
		$db = JFactory::getDbo();
		$vmlangs = vm_products_csvUtils::getVmLangs($db);
		$vmlang = array_keys($vmlangs);
		$vmlang = $vmlang[0];
		$lang_ext = (!isset($_REQUEST['vmlang'])) ? $vmlang : $_REQUEST['vmlang'];
		$csv_enc = vm_products_csvUtils::getCsvCharsetByName($_REQUEST['encoding']);
		$db_enc = vm_products_csvUtils::getDbCharset($db);

		// does the lang table exist ? ...
		$config = JFactory::getConfig();
		$jr_get = (!method_exists($config, 'getValue')) ? 'get' : 'getValue';
		$dbname = $config->$jr_get('db');
		$dbpref = $config->$jr_get('dbprefix');
		$langTableExists = $db->setQuery("SELECT COUNT(TABLE_NAME) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '{$dbpref}virtuemart_products_{$lang_ext}' AND TABLE_SCHEMA = '{$dbname}'")->loadResult();
		if (!$langTableExists) {
			JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_NO_LANG_TABLE', $lang_ext));
			return false;
		}

		// open the output file ...
		$csv_uri = vm_products_csvUtils::$csv_uri . '/virtuemart_products_' . $lang_ext . '_' . date('Ymd_His') . '.csv';
		$csv_file = JPATH_ADMINISTRATOR . '/' . $csv_uri;
		$outFileHandle = fopen($csv_file, 'w');
		if (!$outFileHandle) {
			JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_FILE_WRITE_FAILURE', $csv_file));
			return false;
		}
		// the medias count from the product with the most media files ...
		$db->setQuery('SELECT DISTINCT (SELECT COUNT(*) FROM #__virtuemart_product_medias WHERE virtuemart_product_id=m.virtuemart_product_id) AS count FROM #__virtuemart_product_medias AS m ORDER BY count DESC LIMIT 1');
		if ($db->getErrorNum()) {
			JError::raiseWarning(100, $db->getErrorMsg());
			fclose($outFileHandle);
			unlink($csv_file);
			return false;
		}
		$media_count = (int)$db->loadResult();
		// output the header row ...
		$line = implode(',', array_keys(vm_products_csvUtils::$csv_fields));
		for ($i = 1; $media_count >= $i; $i++)
			$line .= ",product_image_{$i},product_thumbnail_{$i}"; 
		if (fwrite($outFileHandle, $line . "\n") === false) {
			JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_FILE_WRITE_FAILURE', $csv_file));
			fclose($outFileHandle);
			unlink($csv_file);
			return false;
		}
		// counters and reporting ...
		$starting_from = $recnum = 0;
		$report = array();
		// start with the products list, we need to load in chumks
		//  so large product lists dont crash memory, thanks to J! ...
		if ($langTableExists)
		while (($records = $this->loadNextProducts($lang_ext, $db, $starting_from))) {
			foreach ($records as $record) {
				// save the parent id ...
				$product_parent_id = $record['product_parent_id'];
				unset($record['product_parent_id']);
				// order the record ...
				$ordered_record = vm_products_csvUtils::$csv_fields;
				foreach ($ordered_record as &$field)
					$field = '';
				$record = array_merge($ordered_record, $record);
				// the default report data ...
				$report[$recnum]['product_id'] = $record['virtuemart_product_id'];
				$report[$recnum]['product_sku'] = $record['product_sku'];
				$report[$recnum]['product_name'] = '';
				$report[$recnum]['status'] = JText::_('COM_VMPCSV_PRODUCT_EXPORTED');
				$report[$recnum]['report'] = JText::_('COM_VMPCSV_PRODUCT_EXPORT_SUCCESS');
				// append the product parent sku ...
				if(!$this->appendRecordFields($db, $record, $report, $recnum, "
					SELECT
						product_sku as product_parent_sku
					FROM
						#__virtuemart_products
					WHERE
						virtuemart_product_id = {$product_parent_id}
				", $product_parent_id)) continue;
				if (!isset($record['product_parent_sku']))
					$record['product_parent_sku'] = '';
				// get details from the language table ...
				if(!$this->appendRecordFields($db, $record, $report, $recnum, "
					SELECT
						product_name,
						slug as product_alias,
						product_s_desc as product_short_desc,
						product_desc,
						metadesc as meta_description,
						metakey as meta_keywords
					FROM
						#__virtuemart_products_{$lang_ext}
					WHERE
						virtuemart_product_id = {$record['virtuemart_product_id']}
				")) continue;
				$report[$recnum]['product_name'] = $record['product_name'];
				// if no sku then make one ...
				if (!$record['product_sku']) {
					$record['product_sku'] = vm_products_csvUtils::makeSKU($db, $record['product_name']);
					$db->setQuery("UPDATE #__virtuemart_products SET product_sku = '{$record['product_sku']}' WHERE virtuemart_product_id = {$record['virtuemart_product_id']} LIMIT 1");
					if ($db->query() === false) {
						JError::raiseWarning(100, $db->getErrorMsg());
						continue;
					}
					$report[$recnum]['product_sku'] = $record['product_sku'];
				}
				// append the price details ...
				if(!$this->appendRecordFields($db, $record, $report, $recnum, "
					SELECT  product_currency as product_currency_code,
						product_price,
						product_tax_id as product_tax_id_or_name,
						product_discount_id as product_discount_id_or_name
					FROM    #__virtuemart_product_prices
					WHERE   virtuemart_product_id = {$record['virtuemart_product_id']}
					LIMIT   1
				")) continue;
				// fix the currency code ...
				if ($record['product_currency_code'])
					if(!$this->appendRecordFields($db, $record, $report, $recnum, "
						SELECT currency_code_3 as product_currency_code
						FROM   #__virtuemart_currencies
						WHERE  virtuemart_currency_id = {$record['product_currency_code']}
						LIMIT  1
					")) continue;
				// replace the tax id with tax name if set ...
				if(!$this->replaceCalcIdWithCalcName('product_tax_id_or_name', $db, $record, $report, $recnum))
					continue;
				// replace the discount id with discount name if set ...
				if(!$this->replaceCalcIdWithCalcName('product_discount_id_or_name', $db, $record, $report, $recnum))
					continue;
				// append manufacturer details ...
				if(!$this->appendRecordFields($db, $record, $report, $recnum, "
					SELECT  m.mf_name as manufacturer_name, 
						m.mf_desc as manufacturer_desc,
						i.file_url as manufacturer_image
					FROM    #__virtuemart_product_manufacturers as p,
						#__virtuemart_manufacturers_{$lang_ext} as m,
						#__virtuemart_manufacturer_medias as r,
						#__virtuemart_medias as i
					WHERE   p.virtuemart_product_id = {$record['virtuemart_product_id']}
					AND     m.virtuemart_manufacturer_id = p.virtuemart_manufacturer_id
					AND     r.virtuemart_manufacturer_id = m.virtuemart_manufacturer_id
					AND     i.virtuemart_media_id = r.virtuemart_media_id
					LIMIT   1
				")) continue;
				if (!$record['manufacturer_name'])
					if(!$this->appendRecordFields($db, $record, $report, $recnum, "
						SELECT  m.mf_name as manufacturer_name, 
							m.mf_desc as manufacturer_desc,
							'' as manufacturer_image
						FROM    #__virtuemart_product_manufacturers as p,
							#__virtuemart_manufacturers_{$lang_ext} as m
						WHERE   p.virtuemart_product_id = {$record['virtuemart_product_id']}
						AND     m.virtuemart_manufacturer_id = p.virtuemart_manufacturer_id
						LIMIT   1
					")) continue;

				// append category details ...
				if (!empty($_REQUEST['multicat'])) {
					if(!$this->appendRecordFields($db, $record, $report, $recnum, "
						SELECT	GROUP_CONCAT(l.virtuemart_category_id ORDER BY l.virtuemart_category_id SEPARATOR ',') as category_id
						FROM	#__virtuemart_product_categories as p,
							#__virtuemart_categories_{$lang_ext} as l
						WHERE	p.virtuemart_product_id = {$record['virtuemart_product_id']}
								AND
							l.virtuemart_category_id = p.virtuemart_category_id	
					")) continue;
					foreach (array('category_name', 'category_desc', 'category_image', 'category_parent_id_or_name') as $fld)
						$record[$fld] = '';
				}
				else {
					if(!$this->appendRecordFields($db, $record, $report, $recnum, "
						SELECT	l.virtuemart_category_id as category_id,
							l.category_name,
							l.category_description as category_desc
						FROM	#__virtuemart_product_categories as p,
							#__virtuemart_categories_{$lang_ext} as l
						WHERE	p.virtuemart_product_id = {$record['virtuemart_product_id']}
								AND
							l.virtuemart_category_id = p.virtuemart_category_id	
						LIMIT	1
					")) continue;
					if (empty($record['category_id']))
						$record['category_id'] = 0;
					// category image ...
					if(!$this->appendRecordFields($db, $record, $report, $recnum, "
						SELECT	m.file_url as category_image
						FROM	#__virtuemart_category_medias as c,
							#__virtuemart_medias as m
						WHERE	c.virtuemart_category_id = {$record['category_id']}
								AND
							m.virtuemart_media_id = c.virtuemart_media_id
						LIMIT	1
					", $record['category_id'])) continue;
					if (empty($record['category_image']))
						$record['category_image'] = '';
					// the category parent id ...
					if(!$this->appendRecordFields($db, $record, $report, $recnum, "
						SELECT	category_parent_id as category_parent_id_or_name
						FROM	#__virtuemart_category_categories
						WHERE	category_child_id = {$record['category_id']}
						LIMIT	1
					", $record['category_id'])) continue;
					if (empty($record['category_parent_id_or_name']))
						$record['category_parent_id_or_name'] = 0;
				}
				// downloadable plugin fields if installed ...
				$downloadable_params = vm_products_csvUtils::getDownloadableParams($db, $record['virtuemart_product_id']);
				if ($downloadable_params && $downloadable_params['downloadable_media_id']) {
					$downloadable_order_states = array();
					$order_states = vm_products_csvUtils::getOrderStates($db);
					foreach($downloadable_params['downloadable_order_states'] as $state_code)
						if ($order_states[$state_code])
							$downloadable_order_states[] = $order_states[$state_code];
					$record['downloadable_media_id'] = $downloadable_params['downloadable_media_id'];
					$record['downloadable_order_states'] = implode(', ', $downloadable_order_states);
					$record['downloadable_expiry'] = $downloadable_params['downloadable_expires_quantity'] . ' ' . $downloadable_params['downloadable_expires_period'];
				}
				else {
					$record['downloadable_media_id'] = 0;
					$record['downloadable_order_states'] = '';
					$record['downloadable_expiry'] = '';
				}
				// cart variants - custom field ...
				$is_VM3 = (float)substr(VmConfig::getInstalledVersion(), 0, 3) > 2.6;
				$custom_name = $is_VM3 ? 'customfield' : 'custom';
				$custom_cart_variants = array();
				$cart_variants = $db->setQuery("
					SELECT 
						c.custom_title,
						f.{$custom_name}_value AS custom_value,
						f.{$custom_name}_price AS custom_price
					FROM
						#__virtuemart_product_customfields AS f,
						#__virtuemart_customs as c
					WHERE 
						f.virtuemart_product_id = {$record['virtuemart_product_id']}
							AND
						c.virtuemart_custom_id = f.virtuemart_custom_id
							AND
						c.field_type = 'V'
					ORDER BY
						f.ordering ASC
				")->loadAssocList();
				foreach($cart_variants as $cart_variant) {
					foreach (array('custom_title', 'custom_value') as $key)
						$cart_variant[$key] = trim(preg_replace('/[:,\s]+/', ' ', $cart_variant[$key]));
					if (!($cart_variant['custom_title'] && $cart_variant['custom_value']))
						continue;
					$cart_variant['custom_price'] = (float)$cart_variant['custom_price'];
					$custom_cart_variants[] = "{$cart_variant['custom_title']}:{$cart_variant['custom_value']}:{$cart_variant['custom_price']}";
				}
				$record['custom_cart_variants'] = implode(', ', $custom_cart_variants);
				// append all product images and thumbnails by virtuemart_media_id ...
				$db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id={$record['virtuemart_product_id']} ORDER BY ordering");
				$i = 0;
				if ($db->getErrorNum())
					JError::raiseWarning(100, $db->getErrorMsg());
				foreach($db->loadAssocList() as $product_media) {
					// set the image and thumb ...
					$db->setQuery("SELECT file_url, file_url_thumb FROM #__virtuemart_medias WHERE virtuemart_media_id='{$product_media['virtuemart_media_id']}' LIMIT 1");
					if ($db->getErrorNum()) {
						JError::raiseWarning(100, $db->getErrorMsg());
						continue;
					}
					list($file_url, $file_url_thumb) = $db->loadRow();
					$record['product_image_' . ++$i] = $file_url;
					$record['product_thumbnail_' . $i] = $file_url_thumb;
				}
				// fill out the csv row with blank image headers to $media_count ...
				while ($media_count > $i) {
					$record['product_image_' . ++$i] = '';
					$record['product_thumbnail_' . $i] = '';
				}
				// pack the fields in csv format ...
				foreach ($record as &$field)
					if ($field)
						$field = $this->packCsvField($csv_enc, $db_enc, $field);
				// write the record to the output file ...
				unset($record['virtuemart_product_id']);
				$line = implode(',', $record) . "\n";
				if (fwrite($outFileHandle, $line) === false) {
					JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_FILE_WRITE_FAILURE', $csv_file));
					fclose($outFileHandle);
					unlink($csv_file);
					$report[$recnum]['status'] =  JText::_('COM_VMPCSV_PRODUCT_NOT_EXPORTED');
					$report[$recnum]['report'] = JText::sprintf('COM_VMPCSV_FILE_WRITE_FAILURE', $csv_file);
					return false;
				}
				$recnum++;
			}
		}
		fclose($outFileHandle);
		$report['csv_uri'] = $csv_uri;
		JFactory::getApplication()->enqueueMessage(JText::_('COM_VMPCSV_EXPORT_DONE'));
		return $report;
	}

	protected function appendRecordFields($db, &$record, &$report, &$recnum, $sql, $condition = null)
	{
		if ($condition !== null && empty($condition))
			return true;
		$db->setQuery($sql);
		if ($db->getErrorNum()) {
			$msg = $db->getErrorMsg();
			JError::raiseWarning(100, $msg);
			$report[$recnum]['status'] =  JText::_('COM_VMPCSV_PRODUCT_NOT_EXPORTED');
			$report[$recnum]['report'] = $msg;
			$recnum++;
			return false;
		}
		$result = $db->loadAssoc();
		if ($result && is_array($result))
			$record = array_merge($record, $result);
		return true;
	}

	// replaces the calc id referenced by $fieldName with calc name if set ...
	protected function replaceCalcIdWithCalcName($fieldName, $db, &$record, &$report, &$recnum)
	{
				if (!($record[$fieldName] && $record[$fieldName] > 0)) // may be -1
					return true;
				return $this->appendRecordFields($db, $record, $report, $recnum, "
					SELECT calc_name as {$fieldName}
					FROM   #__virtuemart_calcs
					WHERE  virtuemart_calc_id = {$record[$fieldName]}
					LIMIT  1
				");
	}

	protected function packCsvField ($csv_enc, $db_enc, $str = '') 
	{
		if (strtoupper(preg_replace('/[-_]/', '', $csv_enc)) != strtoupper(preg_replace('/[-_]/', '', $db_enc))) {
			set_error_handler('vm_products_csvUtils::iconvErrorHandler', E_NOTICE);
			$str = iconv($db_enc, $csv_enc, $str);
			restore_error_handler();
		}
		if (preg_match('/[\r\n,"]/', $str))
			$str = '"' . str_replace('"', '""', $str) . '"';
		return $str;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm();
	
		return $form;
	}
}
