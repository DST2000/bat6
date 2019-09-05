<?php
/*
 * @title		com_vm_products_csv
 * @version		3.1.3g
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2014 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!');
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport( 'joomla.filter.output' );
require_once(JPATH_ADMINISTRATOR . '/components/com_vm_products_csv/File_CSV_DataSource.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_vm_products_csv/vm_products_csvUtils.php');

class vm_products_csvModelDoImport extends JModelAdmin
{
	protected $image_mime_types = array(
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'png' => 'image/png',
		'gif' => 'image/gif'
	);
	protected $uploadName  = 'csvfile';
	protected $import_mode = 'add';
	protected $records = array();
	public $csvFilePath;

	public function upload_file()
	{
		//any errors the server registered on uploading
		$fileError = (isset($_FILES[$this->uploadName]) && !empty($_FILES[$this->uploadName]['error'])) ? $_FILES[$this->uploadName]['error'] : '';
		$fileTemp = (isset($_FILES[$this->uploadName]) && !empty($_FILES[$this->uploadName]['tmp_name'])) ? $_FILES[$this->uploadName]['tmp_name'] : '';
		if ($fileError > 0) {
			switch ($fileError) {
				case 1:
					JError::raiseWarning(100, JText::_('COM_VMPCSV_FILE_LARGER_THAN_PHP_INI_ALLOWS'));
					JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_MEDIA_NOT_SUPPORTED', $fileTemp));
		
				case 2:
					JError::raiseWarning(100, JText::_('COM_VMPCSV_FILE_LARGER_THAN_HTML_FORM_ALLOWS'));
		
				case 3:
					JError::raiseWarning(100, JText::_('COM_VMPCSV_ERROR_PARTIAL_UPLOAD'));
		
				case 4:
					JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_FILE_NOT_FOUND', $fileTemp));
			}
			return false;
		}
		$uploadPath = (isset($_FILES[$this->uploadName]) && !empty($_FILES[$this->uploadName]['name'])) ? $_FILES[$this->uploadName]['name'] : '';
		$uploadPath = JPATH_SITE.DS.'tmp'.DS.$uploadPath;
		if(!JFile::upload($fileTemp, $uploadPath)) {
			JError::raiseWarning(100, JText::_('COM_VMPCSV_FILE_MOVE_ERROR'));
			return false;
		}
		$this->csvFilePath = $uploadPath;
		return true;
	}
	
	public function parseCsvFile($csvfile)
	{
		if(!file_exists($csvfile)) {
			JError::raiseWarning( 100, JText::sprintf('COM_VMPCSV_FILE_NOT_FOUND', $csvfile) );
			return false;
		}
		// sometime filesize doesnt work :( ...
		/*
		$fh = fopen($csvfile, 'rb');
		fseek($fh, 0, SEEK_END);
		$filesize = ftell($fh);
		fclose($fh);
		*/
		if(!filesize($csvfile)) {
			JError::raiseWarning( 100, JText::_('COM_VMPCSV_ERROR_FILE_EMPTY') );
			return false;
		}
		// parse the csv file ...
		$csv = new File_CSV_DataSource;
		$csv->load($csvfile);
		$this->records = $csv->connect();
		unlink($csvfile);
		if ($csv->error) {
			JError::raiseWarning( 100, $csv->error );
			return false;
		}

		// mode sync allows passing and empty csv to delete all records ... 
		if (isset($_REQUEST['import_mode']))
			$this->import_mode = $_REQUEST['import_mode'];

		// did we get anything ...
		if(!count($this->records)) {
			if ($this->import_mode != 'sync') {
				JError::raiseWarning( 100, JText::_('COM_VMPCSV_ERROR_EMPTY_CSV'));
				return false;
			}
		}
		else {
			// make sure the columns are as expected.
			$record_header = preg_grep('/product_(image|thumbnail)_/', array_keys($this->records[0]), PREG_GREP_INVERT);
			$fields_header = preg_grep('/product_(image|thumbnail)_/', array_keys(vm_products_csvUtils::$csv_fields), PREG_GREP_INVERT);
			if($record_header != $fields_header) {
				JError::raiseWarning( 100, JText::_('COM_VMPCSV_ERROR_COLUMNS') . "<br/>\n" . implode(',', $record_header) . "<br/>\n" . implode(',', $fields_header));
				return false;
			}
		}

		return true;
	}

	protected function encodeFields($csv_enc, $db_enc, &$record)
	{
		if (strtoupper(preg_replace('/[-_]/', '', $csv_enc)) == strtoupper(preg_replace('/[-_]/', '', $db_enc)))
			return;
		set_error_handler('vm_products_csvUtils::iconvErrorHandler', E_NOTICE);
		foreach ($record as &$field)
			$field = iconv($csv_enc, $db_enc, $field);
		restore_error_handler();
	}

	public function do_product_import()
	{
		// check dependancies ...
		if (!vm_products_csvUtils::requireIconv())
			return false;
		// set some vars ...
		$recnum = 0;
		$report = array();
		$cuser = JFactory::getUser();
		$db = $this->getDbo();
		$cuserid = $cuser->get('id');
		$prevvals = array(
			'manufacturer' => array(),
			'category' => array(),
		);
		$vmlangs = vm_products_csvUtils::getVmLangs($db);
		$vmlang = array_keys($vmlangs);
		$vmlang = $vmlang[0];
		$lang_ext = (!isset($_REQUEST['vmlang'])) ? $vmlang : $_REQUEST['vmlang'];
		$csv_enc = vm_products_csvUtils::getCsvCharsetByName($_REQUEST['encoding']);
		$db_enc = vm_products_csvUtils::getDbCharset($db);
		$is_VM3 = (float)substr(VmConfig::getInstalledVersion(), 0, 3) > 2.6;
		$custom_name = $is_VM3 ? 'customfield' : 'custom';
		$custom_param = $is_VM3 ? 'customfield_params' : 'custom_param';
		$product_parents = array();
		$custom_fields_ids = array();
		$char_tr_map = require(JPATH_ADMINISTRATOR . '/components/com_vm_products_csv/character_translations.php');
		// First, the basic product information: product_sku, product_name, product_short_desc, product_desc
		foreach ($this->records as $record) 
		{
			$this->encodeFields($csv_enc, $db_enc, $record);

			// the default report data ...
			$report[$recnum]['product_id'] = 0;
			$report[$recnum]['product_sku'] = $record['product_sku'];
			$report[$recnum]['product_name'] = $record['product_name'];
			$report[$recnum]['status'] =  JText::_('COM_VMPCSV_PRODUCT_IMPORTED');
			$report[$recnum]['report'] = JText::_('COM_VMPCSV_PRODUCT_IMPORT_SUCCESS');
			// validate the record ...
			if (!$this->validateRecord($record, $report, $recnum))
				continue;
			// sort out the sku ...
			$product_id = 0;
			if (!$record['product_sku']) {
				// make the sku ...
				$record['product_sku'] = vm_products_csvUtils::makeSKU($db, $record['product_name']);
				$report[$recnum]['product_sku'] = $record['product_sku'];
			} else {
				// is this SKU is already in the database.
				$rows = vm_products_csvUtils::getProductBySKU($db, $record['product_sku']);
				if($rows) {
					if (count($rows) == 1 && ($this->import_mode == 'update' || $this->import_mode == 'sync')) {
						$product_id = $rows[0]->virtuemart_product_id;
						$report[$recnum]['product_id'] = $product_id;
						$report[$recnum]['status'] =  JText::_('COM_VMPCSV_PRODUCT_UPDATED');
						$report[$recnum]['report'] = JText::sprintf('COM_VMPCSV_PRODUCT_ALREADY_IN_DB', $record['product_sku']);
					} else {
						$this->report_failure(JText::_('COM_VMPCSV_DUPLICATE_SKU') . ': ' .$record['product_sku'], $report, $recnum);
						continue;

					}
				}
			}

			// make sure the product_special and published values are ints ..
			foreach (array('featured_product', 'published') as $key) {
				if (!$record[$key] || strtolower(substr($record[$key], 0, 1)) == 'n')
					$record[$key] = 0;
				else
					$record[$key] = 1;
			}
			
			// update or insert the basic product information.
			$the_date=date('Y-m-d H:i:s');
			$metaauth = 'VM Products CSV !ULTIMATE! v3 by eKerner.com';
			if ($product_id) {
				$db->setQuery("
					UPDATE	#__virtuemart_products
					SET 	product_weight='{$record['product_weight']}',
						product_weight_uom='{$record['product_weight_unit']}',
						product_length='{$record['product_length']}',
						product_width='{$record['product_width']}',
						product_height='{$record['product_height']}',
						product_lwh_uom='{$record['product_lwh_unit']}',
						product_in_stock='{$record['product_stock_quantity']}',
						product_availability='{$record['product_availability']}',
						product_special='{$record['featured_product']}',
						product_unit='{$record['product_weight_unit']}',
						metaauthor='{$metaauth}',
						modified_on='{$the_date}',
						modified_by='{$cuserid}',
						published='{$record['published']}'
					WHERE	virtuemart_product_id='{$product_id}'
					LIMIT	1
				");
						//product_available_date='{$the_date}',
				$db->query();
				if ($this->report_db_error($db, $report, $recnum))
					continue;
			}
			else {
				$db->setQuery("
					INSERT INTO #__virtuemart_products (
						virtuemart_vendor_id,
						product_sku,
						product_weight,
						product_weight_uom,
						product_length,
						product_width,
						product_height,
						product_lwh_uom,
						product_in_stock,
						product_availability,
						product_special,
						product_unit,
						metaauthor,
						product_available_date,
						modified_on,
						modified_by,
						created_on,
						created_by,
						published
					) VALUES (
						'{$record['virtuemart_vendor_id']}',
						'{$record['product_sku']}',
						'{$record['product_weight']}',
						'{$record['product_weight_unit']}',
						'{$record['product_length']}',
						'{$record['product_width']}',
						'{$record['product_height']}',
						'{$record['product_lwh_unit']}',
						'{$record['product_stock_quantity']}',
						'{$record['product_availability']}',
						'{$record['featured_product']}',
						'{$record['product_weight_unit']}',
						'{$metaauth}',
						'$the_date',
						'$the_date',
						'$cuserid',
						'$the_date',
						'$cuserid',
						'{$record['published']}'
					)
				");
				$db->query();
				if ($this->report_db_error($db, $report, $recnum))
					continue;
				// get the id that the product was assigned ...
				$rows = vm_products_csvUtils::getProductBySKU($db, $record['product_sku']);
				$product_id = $rows[0]->virtuemart_product_id;
				if (!$product_id) {
					$this->report_failure(JText::_('COM_VMPCSV_GET_PRODUCT_ID_FAILURE'), $report, $recnum);
					continue;
				}
				$report[$recnum]['product_id'] = $product_id;
			}
			
			// save the product parent id by product parent sku ...
			$product_parent_id = 0;
			if ($record['product_parent_sku']) {
				$db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_products WHERE product_sku='{$record['product_parent_sku']}' LIMIT 1");
				$product_parent_id = $db->loadResult();
				if (!$product_parent_id)
					$product_parent_id = 0;
			}
			$product_parents[] = array(
				'recnum' => $recnum,
				'virtuemart_product_id' => $product_id,
				'product_parent_id' => $product_parent_id
			);

			// the product descriptions ...
			$sitename = JFactory::getApplication()->getCfg('sitename');
			if (!$record['meta_description'])
				$record['meta_description'] = $sitename . ' - ' . $record['product_name'];
			if (!$record['meta_keywords'])
				$record['meta_keywords']  = preg_replace('/\s+/', ', ', $record['product_name']);
			$record['product_alias'] = preg_replace('/\W+/', '-', strtr($record['product_alias'], $char_tr_map));
			if (!$record['product_alias'])
				$record['product_alias'] = $this->makeSlug(strtr($record['product_name'], $char_tr_map), "SELECT slug FROM #__virtuemart_products_{$lang_ext} WHERE slug = '%s' LIMIT 1", $db);
			// does the row exists ?..
			$db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_products_{$lang_ext} WHERE virtuemart_product_id='{$product_id}' LIMIT 1");
			if ($db->loadResult()) // ... yes it does
				$db->setQuery("
					UPDATE #__virtuemart_products_{$lang_ext} SET
						product_s_desc='" . $this->mysql_escape($record['product_short_desc']) . "',
						product_desc='" . $this->mysql_escape($record['product_desc']) . "',
						product_name='" . $this->mysql_escape($record['product_name']) . "',
						metadesc='" . $this->mysql_escape(strip_tags($record['meta_description'])) . "',
						metakey='" . $this->mysql_escape($record['meta_keywords']) . "',
						customtitle='" . $this->mysql_escape(strip_tags($sitename . ' - ' . $record['product_name'])) . "',
						slug='{$record['product_alias']}' 
					WHERE
						virtuemart_product_id='$product_id' 
					LIMIT 1
				");
			else
				$db->setQuery("
					INSERT INTO #__virtuemart_products_{$lang_ext} (
						virtuemart_product_id,
						product_s_desc,
						product_desc,
						product_name,
						metadesc,
						metakey,
						customtitle,
						slug 
					) VALUES (
						'$product_id',
						'" . $this->mysql_escape($record['product_short_desc']) . "',
						'" . $this->mysql_escape($record['product_desc']) . "',
						'" . $this->mysql_escape($record['product_name']) . "',
						'" . $this->mysql_escape(strip_tags($record['meta_description'])) . "',
						'" . $this->mysql_escape($record['meta_keywords']) . "',
						'" . $this->mysql_escape(strip_tags($sitename . ' - ' . $record['product_name'])) . "',
						'{$record['product_alias']}'
					)
				");
			$db->query();
			if ($this->report_db_error($db, $report, $recnum))
				continue;
			
			// Media import
			// This only imports the required data in the database, it doesn't upload the images themselves.
			// You can append as many images as you like to the csv ...
			$i = 0;
			while (array_key_exists('product_image_' . ++$i, $record))
			{
				$image_name = basename($record['product_image_' . $i]);

				// import media unless imported ...
				$media_id = $this->importMedia('product', $record['product_image_' . $i], $record['product_thumbnail_' . $i], $record, $report, $recnum, $db);
				if (!$media_id)
					continue;

				// is the media assocated with the product ...
				$db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id='{$product_id}' AND virtuemart_media_id='{$media_id}' LIMIT 1");
				if (!$db->loadResult()) { // media not already associated ...
					// Now we can associate the two.
					$db->setQuery("INSERT INTO #__virtuemart_product_medias (virtuemart_product_id, virtuemart_media_id, ordering) VALUES ('$product_id', '$media_id', '$i')");
					$db->query();
					//if ($this->report_db_error($db, $report, $recnum, null, null, false))
					if ($this->report_db_error($db, $report, $recnum, JText::sprintf('COM_VMPCSV_MEDIA_IMPORT_FAILURE', $image_name), null, false))
						continue;
				}
			}
			
			//Category import
			$multicat = !empty($_REQUEST['multicat']);
			// determine the category parent ...
			if (!$multicat && $record['category_parent_id_or_name']) {
				if (!preg_match('/^\d+$/', $record['category_parent_id_or_name'])) {
				// get the latest category with given name ...
					$db->setQuery("
						SELECT		l.virtuemart_category_id
						FROM		#__virtuemart_categories_{$lang_ext} as l,
								#__virtuemart_categories as c
						WHERE	
								l.category_name = '".addslashes($record['category_parent_id_or_name'])."'
									AND
								c.virtuemart_category_id = l.virtuemart_category_id
						ORDER BY	c.modified_on DESC
						LIMIT		1

					");
					$record['category_parent_id_or_name'] = $db->loadResult();
				}
			}
			if (!$record['category_parent_id_or_name'])
				$record['category_parent_id_or_name'] = 0;
			
			// determine or create the category ...
			$created_cat = false;
			if (!$multicat && !$record['category_id'] && $record['category_name']) {
				if (!$record['category_parent_id_or_name'])
				// try get the cat_id by name ...
					$db->setQuery("
						SELECT		l.virtuemart_category_id
						FROM		#__virtuemart_categories_{$lang_ext} as l,
								#__virtuemart_categories as c
						WHERE	
								l.category_name = '".addslashes($record['category_name'])."'
									AND
								c.virtuemart_category_id = l.virtuemart_category_id
						ORDER BY	c.modified_on DESC
						LIMIT		1
					");
				else
					// try get the cat_id by parent id and name ...
					$db->setQuery("
						SELECT		l.virtuemart_category_id
						FROM		#__virtuemart_categories_{$lang_ext} as l,
								#__virtuemart_categories as c,
								#__virtuemart_category_categories as r
						WHERE	
								l.category_name = '".addslashes($record['category_name'])."'
									AND
								c.virtuemart_category_id = l.virtuemart_category_id
									AND
								r.category_child_id = c.virtuemart_category_id
									AND 
								r.category_parent_id = {$record['category_parent_id_or_name']}
						ORDER BY	c.modified_on DESC
						LIMIT		1
					");
				$record['category_id'] = $db->loadResult();
				if ($this->report_db_error($db, $report, $recnum))
					continue;
				if (!$record['category_id'] && $_REQUEST['create_categories']) {
					// we need to create the category ...
					$db->setQuery("
						INSERT INTO #__virtuemart_categories (
							virtuemart_vendor_id,
							category_template,
							category_layout,
							category_product_layout,
							products_per_row,
							limit_list_step,
							limit_list_initial,
							metarobot,
							metaauthor,
							published,
							created_on,
							created_by,
							modified_on,
							modified_by
						) VALUES (
							'{$record['virtuemart_vendor_id']}',
							0,
							0,
							0,
							0,
							10,
							10,
							'',
							'{$metaauth}',
							1,
							'{$the_date}',
							'{$cuserid}',
							'{$the_date}',
							'{$cuserid}'
						)
					");
					$db->query();
					if ($this->report_db_error($db, $report, $recnum))
						continue;

					// get most recently created cat id ...
					$db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_categories WHERE created_by = '{$cuserid}' AND created_on = '{$the_date}' ORDER BY virtuemart_category_id DESC LIMIT 1");
					$record['category_id'] = $db->loadResult();
					if (!$record['category_id']) die('!category_id'); // sanity

					// create entry in category lang table ...	
					$slug = $this->makeSlug($record['category_name'], "SELECT slug FROM #__virtuemart_categories_{$lang_ext} WHERE slug = '%s' LIMIT 1", $db);
					if (!$db->getErrorNum()) {
						$db->setQuery("
							INSERT IGNORE INTO #__virtuemart_categories_{$lang_ext} (
								virtuemart_category_id,
								category_name,
								category_description,
								metadesc,
								metakey,
								customtitle,
								slug
							) VALUES (
								'{$record['category_id']}',
								'" . addslashes($record['category_name']) . "',
								'" . addslashes($record['category_desc']) . "',
								'" . addslashes(strip_tags($record['category_desc'])) . "',
								'" . addslashes(preg_replace('/[,\s]+/', ', ', $record['category_name'])) . "',
								'" . addslashes($record['category_name']) . "',
								'{$slug}'
							)
						");
						$db->query();
					}
					if ($this->report_db_error($db, $report, $recnum))
						continue;
					// and the cat reference table ...
					$db->setQuery("INSERT IGNORE INTO #__virtuemart_category_categories (category_parent_id, category_child_id, ordering) VALUES ({$record['category_parent_id_or_name']}, {$record['category_id']}, 0)");
					$db->query();
					if ($this->report_db_error($db, $report, $recnum))
						continue;
					$created_cat = true;
					// add image media ..
					if (!empty($record['category_image'])) {
						$media_id = $this->importMedia('category', $record['category_image'], '', $record, $report, $recnum, $db);
						if ($media_id)
							$this->associateCatMedia($record['category_id'], $media_id, $db);
					}
					$prevvals['category'][$record['category_id']] = array(
						'category_name' => $record['category_name'],
						'category_desc' => $record['category_desc'],
						'category_image' => $record['category_image']
					);
				}
				if (!$record['category_id']) {
					$this->report_failure(JText::_('COM_VMPCSV_RESOLVE_CATEGORY_FAILURE'), $report, $recnum);
					continue;
				}
			}

			if ($record['category_id']) {
				// category association. does the row exist ?..
				$catids = preg_split('/\s*,\s*/', $record['category_id']);
				foreach ($catids as $catid) {
					$catid = trim($catid);
					if (!$catid)
						continue;
					$db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id='{$product_id}' AND virtuemart_category_id='{$catid}'");
					if (!$db->loadResult()) { // ... no
						// insert the category association ...
						$db->setQuery("INSERT INTO #__virtuemart_product_categories (virtuemart_product_id, virtuemart_category_id) VALUES ('$product_id','{$catid}')");
						$db->query();
						if ($db->getErrorNum())
							break;
					}
				}
				if ($this->report_db_error($db, $report, $recnum))
					continue;

				// if not created cat then may need to update it ...
				if (!$multicat && !$created_cat) {
					if (!isset($prevvals['category'][$record['category_id']]))
						$prevvals['category'][$record['category_id']] = array();
					$prev_cat = &$prevvals['category'][$record['category_id']];
					// update of cat name may be required ...
					if ($record['category_name'] && (empty($prev_cat['category_name']) || $prev_cat['category_name'] != $record['category_name'])) {
						$db->setQuery("
							UPDATE IGNORE	#__virtuemart_categories_{$lang_ext}
							SET		category_name = '" . addslashes($record['category_name']) . "',
									metakey = '" . addslashes(preg_replace('/[,\s]+/', ', ', $record['category_name'])) . "',
									customtitle = '" . addslashes($record['category_name']) . "'
							WHERE		virtuemart_category_id = {$record['category_id']}
							LIMIT		1
						");
						$db->query();
						if ($this->report_db_error($db, $report, $recnum))
							continue;
						$prev_cat['category_name'] = $record['category_name'];
					}
					// update of cat description may be required ...
					if ($record['category_desc'] && (empty($prev_cat['category_desc']) || $prev_cat['category_desc'] != $record['category_desc'])) {
						$db->setQuery("
							UPDATE IGNORE	#__virtuemart_categories_{$lang_ext}
							SET		category_description = '" . addslashes($record['category_desc']) . "',
									metadesc = '" . addslashes(strip_tags($record['category_desc'])) . "'
							WHERE		virtuemart_category_id = {$record['category_id']}
							LIMIT		1
						");
						$db->query();
						if ($this->report_db_error($db, $report, $recnum))
							continue;
						$prev_cat['category_desc'] = $record['category_desc'];
					}
					// update of category image may be required ...
					if ($record['category_image'] && (empty($prev_cat['category_image']) || $prev_cat['category_image'] != $record['category_image'])) {
						$media_id = $this->importMedia('category', $record['category_image'], '', $record, $report, $recnum, $db);
						if ($media_id)
							$this->associateCatMedia($record['category_id'], $media_id, $db);
						$prev_cat['category_image'] = $record['category_image'];
					}
				}

				// category parent association ...
				if (!$multicat) {
					// does the row exist ?..
					$db->setQuery("SELECT category_child_id, category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id='{$record['category_id']}' LIMIT 1");
					$row = $db->loadAssoc();
					if ($row) {
						// yes, does it need updating ...
						if ($row['category_parent_id'] != $record['category_parent_id_or_name']) {
							// yes, update it ...
							$db->setQuery("UPDATE #__virtuemart_category_categories SET category_parent_id = {$record['category_parent_id_or_name']} WHERE category_child_id = {$row['category_child_id']} LIMIT 1");
							$db->query();
						}
					}
					else {
						// no, create it ...
						$db->setQuery("INSERT INTO #__virtuemart_category_categories (category_parent_id, category_child_id) VALUES ('{$record['category_parent_id_or_name']}','{$record['category_id']}')");
						$db->query();
					}
				}
				if ($this->report_db_error($db, $report, $recnum))
					continue;
			}
			else
				$record['category_id'] = 0;

			// Price import.
			// get the currency id.
			$db->setQuery("SELECT virtuemart_currency_id FROM #__virtuemart_currencies WHERE currency_code_3='{$record['product_currency_code']}' LIMIT 1");
			$currency_id = $db->loadResult();
			if ($this->report_db_error($db, $report, $recnum))
				continue;
			// get the product_tax_id and product_discount_id ...
			$product_tax_id = $this->getProductCalcId('product_tax_id_or_name', $record, $db);
			$product_discount_id = $this->getProductCalcId('product_discount_id_or_name', $record, $db);
			// does the row exists ?..
			$db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_product_prices WHERE virtuemart_product_id='{$product_id}' LIMIT 1");
			if ($db->loadResult()) // yes
				// update the price information
				$db->setQuery("UPDATE #__virtuemart_product_prices SET product_price='{$record['product_price']}', product_currency='{$currency_id}', product_tax_id='{$product_tax_id}', product_discount_id='{$product_discount_id}' WHERE virtuemart_product_id='{$product_id}' LIMIT 1");
			else
				// insert the price information
				$db->setQuery("INSERT INTO #__virtuemart_product_prices (virtuemart_product_id, product_price, product_currency, product_tax_id, product_discount_id) VALUES ('{$product_id}', '{$record['product_price']}', '{$currency_id}', '{$product_tax_id}', '{$product_discount_id}')");
			$db->query();
			if ($this->report_db_error($db, $report, $recnum))
				continue;

			// Manufacturer Import: find or create manufacturer ...
			$manufacturer_id = null;
			$created_mf = false;
			if ($record['manufacturer_name']) {
				// does the named mf already exist? ..
				$db->setQuery("SELECT virtuemart_manufacturer_id FROM #__virtuemart_manufacturers_{$lang_ext} WHERE mf_name = '" . $this->mysql_escape($record['manufacturer_name']) . "' LIMIT 1");
				$manufacturer_id = $db->loadResult();
				// no, create new mf if create_manufacturers ...
				if (!$manufacturer_id && $_REQUEST['create_manufacturers']) {
					$db->setQuery("
						INSERT INTO #__virtuemart_manufacturers (
							published,
							created_on,
							created_by
						) VALUES (
							1,
							'{$the_date}',
							'{$cuserid}'
						)
					");
					$db->query();
					if (!$db->getErrorNum()) {
						$db->setQuery("SELECT virtuemart_manufacturer_id FROM #__virtuemart_manufacturers WHERE created_by = '{$cuserid}' AND created_on = '{$the_date}' ORDER BY virtuemart_manufacturer_id DESC LIMIT 1");
						$manufacturer_id = $db->loadResult();
						if (!$manufacturer_id) die('!$manufacturer_id'); // sanity
						$slug = $this->makeSlug($record['manufacturer_name'], "SELECT slug FROM #__virtuemart_manufacturers_{$lang_ext} WHERE slug = '%s' LIMIT 1", $db);
						if (!$db->getErrorNum()) {
							$db->setQuery("
								INSERT INTO #__virtuemart_manufacturers_{$lang_ext} (
									virtuemart_manufacturer_id,
									mf_name,
									mf_desc,
									slug
								) VALUES (
									'{$manufacturer_id}',
									'" . $this->mysql_escape($record['manufacturer_name']) . "',
									'" . $this->mysql_escape($record['manufacturer_desc']) . "',
									'{$slug}'
								)
							");
							$db->query();
						}
					}
					if ($db->getErrorNum())
						$this->report_failure($db->getErrorMsg(), $report, $recnum, false);
					else {
						$created_mf = true;
						// add image media ..
						if (!empty($record['manufacturer_image'])) {
							$media_id = $this->importMedia('manufacturer', $record['manufacturer_image'], '', $record, $report, $recnum, $db);
							if ($media_id)
								$this->associateMfMedia($manufacturer_id, $media_id, $db);
						}
						$prevvals['manufacturer'][$manufacturer_id] = array(
							'manufacturer_name' => $record['manufacturer_name'],
							'manufacturer_desc' => $record['manufacturer_desc'],
							'manufacturer_image' => $record['manufacturer_image']
						);
					}
				}
			}

			if ($manufacturer_id) {
				// manufacturer association. does the row exist ?..
				$db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id='{$product_id}' AND virtuemart_manufacturer_id='{$manufacturer_id}' LIMIT 1");
				if (!$db->loadResult()) { // ... no
					// insert the manufacturer association ...
					$db->setQuery("INSERT INTO #__virtuemart_product_manufacturers (virtuemart_product_id, virtuemart_manufacturer_id) VALUES ('{$product_id}', '{$manufacturer_id}')");
					$db->query();
					if ($db->getErrorNum())
						$this->report_failure($db->getErrorMsg(), $report, $recnum, false);
				}

				if (!$created_mf) {
					// update of manufacturer description may be required ...
					if (!isset($prevvals['manufacturer'][$manufacturer_id]))
						$prevvals['manufacturer'][$manufacturer_id] = array();
					$prev_mf = &$prevvals['manufacturer'][$manufacturer_id];
					if ($record['manufacturer_desc'] && (empty($prev_mf['manufacturer_desc']) || $prev_mf['manufacturer_desc'] != $record['manufacturer_desc'])) {
						$db->setQuery("UPDATE IGNORE #__virtuemart_manufacturers_{$lang_ext} SET mf_desc = '" . $this->mysql_escape($record['manufacturer_desc']) . "' WHERE virtuemart_manufacturer_id = {$manufacturer_id} LIMIT 1");
						$db->query();
						if ($db->getErrorNum())
							$this->report_failure($db->getErrorMsg(), $report, $recnum, false);
						$prev_mf['manufacturer_desc'] = $record['manufacturer_desc'];
					}
					// update of manufacturer image may be required ...
					if ($record['manufacturer_image'] && (!isset($prev_mf) || empty($prev_mf['manufacturer_image']) || $prev_mf['manufacturer_image'] != $record['manufacturer_image'])) {
						$media_id = $this->importMedia('manufacturer', $record['manufacturer_image'], '', $record, $report, $recnum, $db);
						if ($media_id)
							$this->associateMfMedia($manufacturer_id, $media_id, $db);
						$prev_mf['manufacturer_image'] = $record['manufacturer_image'];
					}
				}
			}

			// the downloadable product details if applicable ...
			$downloadable_plugin_id = vm_products_csvUtils::getCustomFieldId($db, 'downloadable', 'custom_element');
			if ($downloadable_plugin_id) {
				$downloadable_params = vm_products_csvUtils::getDownloadableParams($db, $product_id, $downloadable_plugin_id);
				if ($record['downloadable_media_id']) {
					// update the params if required ...
					if (!$record['downloadable_order_states'])
						$record['downloadable_order_states'] = 'Confirmed, Shipped';
					if (!$record['downloadable_expiry'])
						$record['downloadable_expiry'] = '0 days';
					$order_states = vm_products_csvUtils::getOrderStates($db);
					$passed_order_states = preg_split('/\s*,\s*/', $record['downloadable_order_states']);
					$downloadable_order_states = array();
					foreach($passed_order_states as $state_name)
						if ($order_states[$state_name])
							$downloadable_order_states[] = $order_states[$state_name];
					if (!$downloadable_order_states)
						$downloadable_order_states = array('C', 'S');
					list($quantity, $period) = preg_split('/\s+/', strtolower(trim($record['downloadable_expiry'])));
					if (substr($period, -1) != 's')
						$period .= 's';
					$custom_params = json_encode(array(
						'downloadable_media_id' => $record['downloadable_media_id'],
						'downloadable_order_states' => $downloadable_order_states,
						'downloadable_expires_quantity' => $quantity,
						'downloadable_expires_period' => $period
					));
					if ($downloadable_params) // row exists ...
						$db->setQuery("UPDATE #__virtuemart_product_customfields SET {$custom_param} = '{$custom_params}' WHERE virtuemart_product_id = {$product_id} AND virtuemart_custom_id = {$downloadable_plugin_id} LIMIT 1");
					else
						$db->setQuery("INSERT INTO #__virtuemart_product_customfields (virtuemart_product_id, virtuemart_custom_id, {$custom_name}_value, {$custom_param}) VALUES ({$product_id}, {$downloadable_plugin_id}, 'downloadable', '{$custom_params}')");
				}
				else {
					// delete the row if exists ...
					if ($downloadable_params) {
						$db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = {$product_id} AND virtuemart_custom_id = {$downloadable_plugin_id} LIMIT 1");
					}
				}
				$db->query();
				$this->report_db_error($db, $report, $recnum, null, null, false);
			}
			// import the Cart Variants - custom field ...
			// first delete all Cart Variants for this product ...
			$db->setQuery("
				DELETE FROM 
					#__virtuemart_product_customfields
				WHERE
					virtuemart_product_id = {$product_id}
						AND 
					virtuemart_custom_id IN (
						SELECT virtuemart_custom_id FROM #__virtuemart_customs WHERE field_type = 'V'
					)
			")->query();
			// add the values from the csv ...
			$custom_fields = array_filter(array_map('trim', explode(',', $record['custom_cart_variants'])));
			$ordering = array();
			foreach ($custom_fields as $custom_field) {
				// parse the custom field ...
				list ($name, $value, $price) = explode(':', $custom_field);
				if (!($name && $value)) {
					$this->report_failure('Cart Variant Corrupt: '.$custom_field, $report, $recnum, false);
					continue;
				}
				$price = (float)$price;
				// find or create custom field ...
				if (!$custom_fields_ids[$name]) {
					$custom_fields_ids[$name] = vm_products_csvUtils::getCustomFieldId($db, $name);

					if (!$custom_fields_ids[$name]) {
						$db->setQuery("
							INSERT INTO 
								#__virtuemart_customs (show_title, virtuemart_vendor_id, custom_title, field_type, is_cart_attribute, created_on, created_by, modified_on, modified_by)
								VALUES (1, {$record['virtuemart_vendor_id']}, '{$name}', 'V', 1, '{$the_date}', {$cuserid}, '{$the_date}', {$cuserid})
							")->query();
						$this->report_db_error($db, $report, $recnum, null, null, false);
						$custom_fields_ids[$name] = vm_products_csvUtils::getCustomFieldId($db, $name);
					}
				}
				// for field ordering ...
				if (empty($ordering[$name]))
					$ordering[$name] = 0;
				// insert the values ...
				$db->setQuery("
					INSERT INTO 
						#__virtuemart_product_customfields (virtuemart_product_id, virtuemart_custom_id, {$custom_name}_value, {$custom_name}_price, created_on, created_by, modified_on, modified_by, ordering)
						VALUES ({$product_id}, {$custom_fields_ids[$name]}, '{$value}', '{$price}', '{$the_date}', {$cuserid}, '{$the_date}', {$cuserid}, ".($ordering[$name]++).")
				")->query();
				$this->report_db_error($db, $report, $recnum, null, null, false);
			}

			$recnum++;
		}

		// update product parent id by sku here so that child producst can be listed before their parents ...
		foreach ($product_parents as $product_parent) {
			$status = $report[$product_parent['recnum']]['status'];
			if (!($status == JText::_('COM_VMPCSV_PRODUCT_IMPORTED') || $status == JText::_('COM_VMPCSV_PRODUCT_UPDATED')))	
				continue;
			$db->setQuery("UPDATE #__virtuemart_products SET product_parent_id = {$product_parent['product_parent_id']} WHERE virtuemart_product_id = {$product_parent['virtuemart_product_id']} LIMIT 1");
			$db->query();
			$this->report_db_error($db, $report, $product_parent['recnum'], null, null, false);
		}

		// if mode sync then need to delete products from DB that are not in the CSV ...
		if ($this->import_mode == 'sync') {
			$sku_sql = '';
			foreach ($report as $p) {
				if ($sku_sql)
					$sku_sql .= "', '";
				$sku_sql .= $this->mysql_escape($p['product_sku']);
			}
			$sql = "
				SELECT	p.virtuemart_product_id,
					p.product_sku,
					l.product_name
				FROM	#__virtuemart_products as p,
					#__virtuemart_products_{$lang_ext} as l
				WHERE	l.virtuemart_product_id = p.virtuemart_product_id
			";
			if ($sku_sql)
				$sql .= "AND p.product_sku NOT IN ('{$sku_sql}')";
			$db->setQuery($sql);
			$rows = $db->loadAssocList();
			if (!$this->report_db_error($db, $report, $tmp = null)) {
				$productModel = VmModel::getModel('product');
				foreach ($rows as $row) {
					$report[$recnum]['product_id'] = $row['virtuemart_product_id'];
					$report[$recnum]['product_sku'] = $row['product_sku'];
					$report[$recnum]['product_name'] = $row['product_name'];
					$report[$recnum]['status'] =  JText::_('COM_VMPCSV_PRODUCT_DELETED');
					$report[$recnum]['report'] = JText::_('COM_VMPCSV_PRODUCT_DELETE_SUCCESS');
					if(!$productModel->remove(array($row['virtuemart_product_id']))) {
						$report[$recnum]['status'] =  JText::_('COM_VMPCSV_PRODUCT_NOT_DELETED');
						$report[$recnum]['report'] = JText::_('COM_VMPCSV_PRODUCT_DELETE_FAILURE');
					}
					$recnum++;
				}
			}
		}
		
		JFactory::getApplication()->enqueueMessage( JText::_('COM_VMPCSV_IMPORT_DONE') );
		return $report;
	}

	protected function getProductCalcId($fieldName, $record, $db)
	{
		if (!$record[$fieldName])
			return 0;
		if ($record[$fieldName] == -1)
			return -1;
		if (preg_match('/^\d+$/', $record[$fieldName]))
			return $record[$fieldName];
		// for additional kinds see: administrator/components/com_virtuemart/views/calc/view.html.php
		//$calc_kind = ($fieldName == 'product_tax_id_or_name') ? 'Tax' : 'DBTax';
		//$db->setQuery("SELECT virtuemart_calc_id FROM #__virtuemart_calcs WHERE calc_name='{$record[$fieldName]}' AND calc_kind = '{$calc_kind}' AND virtuemart_vendor_id = {$record['virtuemart_vendor_id']} LIMIT 1");
		$db->setQuery("SELECT virtuemart_calc_id FROM #__virtuemart_calcs WHERE calc_name='{$record[$fieldName]}' AND virtuemart_vendor_id = {$record['virtuemart_vendor_id']} LIMIT 1");
		$res = $db->loadResult();
		if ($res)
			return $res;
		return -1;
	}

	protected function makeSlug($name, $sql = '', $db = null)
	{
		$slug = JFilterOutput::stringURLSafe($name);
		if ($sql && $db) {
			$idx = 2;
			$_slug = $slug;
			while (!!$db->setQuery(sprintf($sql, $slug))->loadResult())
				$slug = $_slug . '-' . $idx++;
		}
		return $slug;
	}

	protected function validateRecord(&$record, &$report, &$recnum)
	{
		$db = $this->getDbo();
		foreach(vm_products_csvUtils::$csv_fields as $field => $cfg) {
			if (!$cfg)
				continue;
			// set the defaults if empty and has default ...
			if ($record[$field] === '' && isset($cfg['default']))
				$record[$field] = $cfg['default'];
			if (!$record[$field]) {
				// assure the required fields ...
				if (!empty($cfg['required'])) {
					$this->report_failure(JText::sprintf('COM_VMPCSV_MISSING_REQUIRED_FIELD', $field), $report, $recnum);
					return false;
				}
			}
			else {
				if (!empty($cfg['select'])) {
					// some fields require database tests ...
					$sql = sprintf($cfg['select'], $this->mysql_escape($record[$field]));
					$db->setQuery($sql);
					$res = $db->loadResult();
					if ($this->report_db_error($db, $report, $recnum))
						return false;
					if (!$res) {
						$this->report_failure(JText::sprintf('COM_VMPCSV_INVALID_VALUE', $field, $record[$field]), $report, $recnum);
						return false;
					}
				}
				if (!empty($cfg['function']) && !call_user_func($cfg['function'], $field, $record[$field], $record, $db)) {
						$this->report_failure(JText::sprintf('COM_VMPCSV_INVALID_VALUE', $field, $record[$field]), $report, $recnum);
						return false;
				}
			}
		}
		return true;
	}
	
	// imports media into db if not exists, returns media id ...
	protected function importMedia($media_type, $image_uri, $thumb_uri = '', $record, &$report, &$recnum, $db = null)
	{
		if (!$image_uri)
			return false;
		if (!$db)
			$db = $this->getDbo();
		// get the path and basename ...
		$image_name = basename($image_uri);
		$image_uri = preg_replace('#^' . JPATH_SITE . '/?#', '', $image_uri);
		if (!file_exists(JPATH_SITE . DS . $image_uri))
			JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_MEDIA_NOT_FOUND', $image_uri));
		// thumbnail - I dont use this - leave blank if using auto thumb generation ...
		if ($thumb_uri) {
			$thumb_uri = preg_replace('#^' . JPATH_SITE . '/?#', '', $thumb_uri);
			if (!file_exists(JPATH_SITE . DS . $thumb_uri))
				JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_MEDIA_NOT_FOUND', $thumb_uri));
		}
		// need media id and thumb url ...
		$media_id_sql = "SELECT virtuemart_media_id FROM #__virtuemart_medias WHERE file_url='{$image_uri}' LIMIT 1";
		$db->setQuery($media_id_sql);
		$media_id = $db->loadResult();
		if (!$media_id) { // media not already imported
			$mimetype = $this->getImageMimeType($image_name);
			if (!$mimetype) {
				JError::raiseWarning(100, JText::sprintf('COM_VMPCSV_MEDIA_NOT_SUPPORTED', $image_name));
				return false;
			}
			// Import the media
			$image_alt = trim(preg_replace('/\W+/', ' ', preg_replace('/\.\w+$/', '', $image_name)));
			$file_is_product_image = ($media_type == 'product') ? 1 : 0;
			$db->setQuery("INSERT INTO #__virtuemart_medias (virtuemart_vendor_id, file_title, file_meta, file_mimetype, file_type, file_url, file_url_thumb, file_is_product_image, published) VALUES ('{$record['virtuemart_vendor_id']}', '{$image_name}', '{$image_alt}', '{$mimetype}', '{$media_type}', '{$image_uri}','{$thumb_uri}','{$file_is_product_image}','1')");
			$db->query();
			//if ($this->report_db_error($db, $report, $recnum, null, null, false))
			if ($this->report_db_error($db, $report, $recnum,  JText::sprintf('COM_VMPCSV_MEDIA_IMPORT_FAILURE', $image_name), null, false))
				return false;

			// load the media id ...
			$db->setQuery($media_id_sql);
			$media_id = $db->loadResult();
		}	
		elseif ($thumb_uri) // media exists, possible update of file_url_thumb only ...
			$db->setQuery("UPDATE IGNORE #__virtuemart_medias SET file_url_thumb = '{$thumb_uri}' WHERE virtuemart_media_id = {$media_id} LIMIT 1")->query();
		// check for errors ...
		if ($this->report_db_error($db, $report, $recnum, JText::sprintf('COM_VMPCSV_MEDIA_IMPORT_FAILURE', $image_name), $db->getErrorNum() || !$media_id, false))
			return false;
		return $media_id;
	}

	// adds category media association ..
	protected function associateCatMedia ($category_id, $media_id, $db = null)
	{
		if (!($category_id && $media_id))
			return;
		if (!$db)
			$db = $this->getDbo();
		$db->setQuery("INSERT IGNORE INTO #__virtuemart_category_medias (virtuemart_category_id, virtuemart_media_id, ordering) VALUES ({$category_id}, {$media_id}, 1)");
		$db->query();
	}

	// adds manufacturer media association ..
	protected function associateMfMedia ($manufacturer_id, $media_id, $db = null)
	{
		if (!($manufacturer_id && $media_id))
			return;
		if (!$db)
			$db = $this->getDbo();
		$db->setQuery("INSERT IGNORE INTO #__virtuemart_manufacturer_medias (virtuemart_manufacturer_id, virtuemart_media_id, ordering) VALUES ({$manufacturer_id}, {$media_id}, 1)");
		$db->query();
	}

	protected function getImageMimeType($image_name)
	{
		$ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
		return $this->image_mime_types[$ext];
	}
	
	protected function mysql_escape($sql)
	{
		return addslashes($sql);
	}
	
	protected function report_db_error($db, &$report, &$recnum, $msg = '', $condition = null, $increment = true)
	{
		$errnum = $db->getErrorNum();
		if ($condition === null)
			$condition = $errnum;
		if (!($condition && $errnum))
			return false;
		if (!$msg)
			$msg = $db->getErrorMsg();
		$this->report_failure($msg, $report, $recnum, $increment);
		return true;
	}

	protected function report_failure($msg, &$report, &$recnum, $increment = true)
	{
		if ($report && is_int($recnum) && isset($report[$recnum])) {
			$report[$recnum]['status'] = JText::_((!$increment) ? 'COM_VMPCSV_PRODUCT_IMPORT_ERRORS' : 'COM_VMPCSV_PRODUCT_NOT_IMPORTED');
			$report[$recnum]['report'] = $msg;
		}
		else
			JError::raiseWarning(100, $msg);
		if ($increment && is_int($recnum))
			$recnum++;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		return $this->loadForm();
	}
}
