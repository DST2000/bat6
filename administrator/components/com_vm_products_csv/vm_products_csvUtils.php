<?php
/*
 * @title		com_vm_products_csv
 * @version		3.1.3h
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2014 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!');
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

class vm_products_csvUtils
{
	protected static $admin_url;
	public static function admin_url()
	{
		if (!self::$admin_url)
			self::$admin_url = JRoute::_(JURI::root() . 'administrator/');
		return self::$admin_url;
	}
	protected static $config_lang;
	protected static function getConfigLang()
	{
		if (!self::$config_lang) {
			$config = JFactory::getConfig();
			$jr_get = (!method_exists($config, 'getValue')) ? 'get' : 'getValue';
			self::$config_lang = $config->$jr_get('config.language');
		}
		return self::$config_lang;
	}
	public static $csv_uri = 'components/com_vm_products_csv/csv/';
	public static $csv_fields = array(
		'virtuemart_vendor_id' => array(
			'required' => true,
			'default' => 1,
			'select' => 'SELECT virtuemart_vendor_id FROM #__virtuemart_vendors WHERE virtuemart_vendor_id=\'%s\' LIMIT 1'
		),
		'product_sku' => array(
			'default' => '',
		),
		'product_parent_sku' => array(
			'default' => '',
		),
		'product_name' => array(
			'required' => true,
		),
		'product_alias' => array(
			'default' => '',
		),
		'product_short_desc' => array(
			'default' => '',
		),
		'product_desc' => array(
			'default' => '',
		),
		'meta_description' => null,
		'meta_keywords' => null,
		'product_price' => array(
			'default' => 0
		),
		'product_currency_code' => array(
			'required' => true,
			'select' => 'SELECT virtuemart_currency_id FROM #__virtuemart_currencies WHERE currency_code_3=\'%s\' LIMIT 1'
		),
		'manufacturer_name' => array(
			'default' => ''
		),
		'manufacturer_desc' => array(
			'default' => ''
		),
		'manufacturer_image' => array(
			'default' => ''
		),
		'category_id' => array(
			//'select' => 'SELECT virtuemart_category_id FROM #__virtuemart_categories where virtuemart_category_id=\'%s\' LIMIT 1',
			'function' => 'vm_products_csvUtils::categoryOK'
		),
		'category_name' => array(
			'default' => '',
			'function' => 'vm_products_csvUtils::categoryOK'
		),
		'category_desc' => array(
			'default' => ''
		),
		'category_image' => array(
			'default' => ''
		),
		'category_parent_id_or_name' => array(
			'default' => '',
			'function' => 'vm_products_csvUtils::categoryParentOK'
		),
		'product_tax_id_or_name' => array(
			'default' => -1,
			'function' => 'vm_products_csvUtils::calcIdOrNameOK'
		),
		'product_discount_id_or_name' => array(
			'default' => -1,
			'function' => 'vm_products_csvUtils::calcIdOrNameOK'
		),
		'product_weight' => array(
			'default' => 0
		),
		'product_weight_unit' => array(
			'default' => 'KG',
			'function' => 'vm_products_csvUtils::weightUnitOK'
		),
		'product_length' => array(
			'default' => 0
		),
		'product_width' => array(
			'default' => 0
		),
		'product_height' => array(
			'default' => 0
		),
		'product_lwh_unit' => array(
			'default' => 'CM',
			'function' => 'vm_products_csvUtils::dimensionUnitOK'
		),
		'product_stock_quantity' => array(
			'default' => 0
		),
		'product_availability' => array(
			'default' => 'In Stock'
		),
		'featured_product' => array(
			'default' => 0
		),
		'published' => array(
			'default' => 1
		),
		'downloadable_media_id' => array(
			'default' => 0,
			'function' => 'vm_products_csvUtils::downloadableMediaIdOK'
		),
		'downloadable_order_states' => array(
			'default' => '',
			'function' => 'vm_products_csvUtils::downloadableOrderStatesOK'
		),
		'downloadable_expiry' => array(
			'default' => '',
			'function' => 'vm_products_csvUtils::downloadableExpiryOK'
		),
		'custom_cart_variants' => array(
			'default' => ''
		),
		/*
		'product_image_1' => null,
		'product_thumbnail_1' => null,
		 . . .
		*/
	);

	public static function getProductBySKU($db, $product_sku)
	{
		$db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_products WHERE product_sku='$product_sku'");
		return $db->loadObjectList();
	}
		
	public static function makeSKU ($db, $product_name = null)
	{
		$_sku = $sku = '';
		$_i = 0;
		if ($product_name) {
			foreach (preg_split('/[^A-Z]+/', strtoupper($product_name)) as $word) {
				if ($_i++ == 5)
					break;
				if ($word)
					$_sku .= substr($word, 0, 1);
			}
		}
		if (!$_sku)
			$_sku = 'VMPCSV';
		$_i = 1;
		$sku = $_sku . '-' . $_i++;
		while (self::getProductBySKU($db, $sku))
			$sku = $_sku . '-' . $_i++;
		return $sku;
	}

	public static function categoryOK ($fieldName, $fieldval, $records, $db) {
		if (!($records['category_id'] || $records['category_name']))
			return false;
		if ($fieldName == 'category_id' && $records['category_id']) {
			$db = JFactory::getDbo();
			$ids = preg_split('/\s*,\s*/', $records['category_id']);
			foreach ($ids as $id) {
				$id = trim($id);
				if (!$id)
					continue;
				$db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_categories where virtuemart_category_id='{$id}' LIMIT 1");
				if (!$db->loadResult())
					return false;
			}
		}
		return true;
	}

	public static function categoryParentOK ($fieldName, $fieldval, $records, $db) {
		if (!$fieldval)
			return true;
		$colname = (!preg_match('/^\d+$/', $fieldval)) ? 'category_name' : 'virtuemart_category_id';
		$lang_ext = $_REQUEST['vmlang'];
		if (!$lang_ext)
			$lang_ext = strtolower(str_replace('-', '_', self::getConfigLang()));
		$sql = "SELECT count(*) as n FROM #__virtuemart_categories_{$lang_ext} WHERE {$colname} = '{$fieldval}'";
		$res = (int)$db->setQuery($sql)->loadResult();
		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}
		return ($res > 0);
	}

	public static function calcIdOrNameOK ($fieldName, $fieldval, $records, $db) {
		if (!$fieldval || $fieldval == -1)
			return true;
		$col_name = (!preg_match('/^\d+$/', $fieldval)) ? 'calc_name' : 'virtuemart_calc_id';
		//$calc_kind = ($fieldName == 'product_tax_id_or_name') ? 'Tax' : 'DBTax';
		//$db->setQuery("SELECT virtuemart_calc_id FROM #__virtuemart_calcs WHERE {$col_name}='{$fieldval}' AND calc_kind = '{$calc_kind}' AND virtuemart_vendor_id = {$records['virtuemart_vendor_id']} LIMIT 1");
		$db->setQuery("SELECT virtuemart_calc_id FROM #__virtuemart_calcs WHERE {$col_name}='{$fieldval}' AND virtuemart_vendor_id = {$records['virtuemart_vendor_id']} LIMIT 1");
		$res = $db->loadResult();
		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}
		return !!$res;
	}

	public static function manufacturerNameOK ($fieldName, $fieldval, $records, $db) {
		if (empty($fieldval))
			return true;
		$db->setQuery("SELECT virtuemart_manufacturer_id FROM #__virtuemart_manufacturers WHERE virtuemart_manufacturer_id='{$fieldval}' LIMIT 1");
		$res = $db->loadResult();
		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}
		return !!$res;
	}

	public static function weightUnitOK($fieldName, $fieldval, $records, $db)
	{
		$validvals = array('kg', 'g', 'mg', 'lb', 'oz');
		$fieldval = strtolower($fieldval);
		return in_array($fieldval, $validvals);
	}

	public static function dimensionUnitOK ($fieldName, $fieldval, $records, $db)
	{
		$validvals = array('m', 'mm', 'cm', 'in', 'ft', 'yd');
		$fieldval = strtolower($fieldval);
		return in_array($fieldval, $validvals);
	}

	public static function downloadableMediaIdOK ($fieldName, $fieldval, $records, $db)
	{
		if (empty($fieldval))
			return true;
		$db->setQuery("SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = {$fieldval} AND file_is_forSale = 1 LIMIT 1");
		return $db->loadResult();
	}

	public static function downloadableOrderStatesOK ($fieldName, $fieldval, $records, $db)
	{
		$order_states = self::getOrderStates($db);
		foreach(preg_split('/\s*,\s*/', $fieldval) as $order_name)
			if (!$order_states[$order_name])
				return false;
		return true;
	}

	public static function downloadableExpiryOK ($fieldName, $fieldval, $records, $db)
	{
		if (empty($fieldval))
			return true;
		list($quantity, $period) = preg_split('/\s+/', strtolower(trim($fieldval)));
		if (!(strlen($quantity) && $period))
			return false;
		if (!in_array($quantity, range(0, 11)))
			return false;
		if (substr($period, -1) != 's')
			$period .= 's';
		if (!in_array($period, array('days', 'weeks', 'months', 'years')))
			return false;
		return true;
	}

	// gets everything except the media ...
	public static function getSelectSql()
	{
		// get this up ya MySQL ...
		// Actually this is only good if vm products are entered correctly,
		//  I dont use it becuase the new way accepts  broken vm product entries,
		//  and even fixes them upon re-import :D 
		//  but its great so Ill leave it here for future use or reference ...
		$lang_ext = $_REQUEST['vmlang']; //strtolower(str_replace('-', '_', self::getConfigLang()));
		return("
		SELECT
			p.virtuemart_product_id as virtuemart_product_id,
			p.virtuemart_vendor_id as virtuemart_vendor_id,
			p.product_sku as product_sku,
			l.product_name as product_name,
			l.product_s_desc as product_short_desc,
			l.product_desc as product_desc,
			l.metadesc as meta_description,
			l.metakey as meta_keywords,
			t.product_price as product_price,
			c.currency_code_3 as product_currency_code,
			m.virtuemart_manufacturer_id as manufacturer_id,
			o.virtuemart_category_id as category_id,
			t.product_tax_id as product_tax_id_or_name,
			t.product_discount_id as product_discount_id_or_name,
			p.product_weight as product_weight,
			p.product_weight_uom as product_weight_unit,
			p.product_length as product_length,
			p.product_width as product_width,
			p.product_height as product_height,
			p.product_lwh_uom as product_lwh_unit,
			p.product_in_stock as product_stock_quantity,
			p.product_availability as product_availability,
			p.product_special as featured_product,
			p.published
		FROM
			#__virtuemart_products as p,
			#__virtuemart_products_{$lang_ext} as l,
			#__virtuemart_product_prices as t,
			#__virtuemart_currencies as c,
			#__virtuemart_product_manufacturers as m,
			#__virtuemart_product_categories as o
		WHERE
			l.virtuemart_product_id = p.virtuemart_product_id AND
			t.virtuemart_product_id = p.virtuemart_product_id AND
			c.virtuemart_currency_id = t.product_currency AND
			m.virtuemart_product_id = p.virtuemart_product_id AND
			o.virtuemart_product_id = p.virtuemart_product_id
		");
	}

	public static function addSubmenu($selected = '')
	{
		foreach (array('PRODUCTS_IMPORT' => 'import_products', 'PRODUCTS_EXPORT' => 'export_products', 'MANUAL' => '') as $name => $type) {
			JSubMenuHelper::addEntry(
				JText::_('COM_VMPCSV_' . $name . '_TITLE'),
				JRoute::_(JURI::root() . 'administrator/index.php?option=com_vm_products_csv' . (!$type ? '' : '&view=' . $type)),
				$type == $selected 
			);
		}
	}

	public static function addToolbar($type = '')
	{
		$classtag = 'vmpcsv' . $type;
		$document = JFactory::getDocument();
		
		// page title ...
		JToolBarHelper::title(JText::_('COM_VMPCSV_TITLE'), $classtag);
		$document->addStyleDeclaration('.icon-48-' . $classtag . ' {background-image: url(../media/com_vm_products_csv/vm_products_csv_logo_48.png);}');
	
		// toolbar functions ...
		JToolBarHelper::back('JTOOLBAR_BACK', 'javascript:history.back();');
		JToolBarHelper::divider();
		JToolBarHelper::media_manager('stories/virtuemart', 'JTOOLBAR_UPLOAD');
		JToolBarHelper::divider();
		JToolBarHelper::help('', false, self::admin_url() . 'components/com_vm_products_csv/vm_products_csv_manual.php');
	}

	public static function drawHelpFieldset()
	{
		$manual_btn = JText::_('COM_VMPCSV_MANUAL_TITLE');
		$manual_link = self::admin_url() . 'components/com_vm_products_csv/vm_products_csv_manual.php';
		$website_link = 'http://shop.ekerner.com/index.php/shop/joomla-extensions/com-vmproductscsv-detail'; // 'http://www.ekerner.com/';
		echo('
			<fieldset>
				<legend>' . JText::_('COM_VMPCSV_HELP_TITLE') . '</legend>
				<div style="clear:both;">
					<button title="' . $manual_btn . '" href="' . $manual_link . '" onclick="Joomla.popupWindow(\'' . $manual_link . '\', \'Help\', 700, 500, 1); return false;">' . $manual_btn . '</button> <button
					title="eKerner.com" href="' . $website_link . '" onclick="window.open(\'' . $website_link . '\'); return false;">' . JText::_('COM_VMPCSV_WEBSITE_BTN') . '</button>
				</div>
			</fieldset>
		');
	}

	public static function drawVersionFieldset()
	{
		echo('
			<fieldset>
				<legend>' . JText::_('COM_VMPCSV_VERSION_TITLE') . '</legend>
				<div style="clear:both;">
					VM Products CSV <span style="color:#204D02;">!ULTIMATE!</span> v3.1.3h by <a href="http://www.ekerner.com/" title="Developer website" onclick="window.open(this.href); return false;">ekerner.com</a>
		');
		if (!class_exists('VmConfig')) {
			require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
			VmConfig::loadConfig();
		}
		$vmlang = VmConfig::get('vmlang');
		if ($vmlang == 'it_it')
			echo('<br /><br />' . JText::_('COM_VMPCSV_ITALIAN_TRANSLATION'));
		elseif ($vmlang == 'es_es')
			echo('<br /><br />' . JText::_('COM_VMPCSV_SPANISH_TRANSLATION'));
		elseif ($vmlang == 'fr_fr')
			echo('<br /><br />' . JText::_('COM_VMPCSV_FRENCH_TRANSLATION'));
		elseif ($vmlang == 'sk_sk')
			echo('<br /><br />' . JText::_('COM_VMPCSV_SLOVAK_TRANSLATION'));
		echo('
				</div>
			</fieldset>
		');
	}

	public static function getCsvList($path = null)
	{
		// check/fix the path ...
		if (!$path)
			$path = self::$csv_uri;
		if (!file_exists($path)) {
			$newpath = JPATH_ADMINISTRATOR . DS . $path;
			if (!file_exists($newpath)) {
				JError::raiseError(500, JText::sprintf('COM_VMPCSV_DIR_READ_FAILURE', $path));
				return null;
			}
			$path = $newpath;
		}
		// if its not on the correct path, then its a bogus request ...
		$path = realpath($path);
		if (strpos($path, realpath(self::$csv_uri)) === null) {
			JError::raiseError(500, 'Piss off!');
			return null;
		}
		// if its a single file just return it now ...
		if (is_file($path)) {
			// if its not a csv file then its a bogus request ...
			if (!self::isCsvFile($path)) {
				JError::raiseError(500, 'Piss off!');
				return null;
			}
			return array($path);
		}
		// ... otherwise its a dir so load all csv files,
		//  without using glob() to be nice for those silly windoze users ... 
		if (!($dirHandle = opendir($path))) {
			JError::raiseError(500, JText::sprintf('COM_VMPCSV_DIR_READ_FAILURE', $path));
			return null;
		}
		$csvList = array();
		while (($dirent = readdir($dirHandle)) !== false) {
			$dirent = $path . DS . $dirent;
			if (!is_file($dirent))
				continue;
			if (self::isCsvFile($dirent))
				$csvList[] = $dirent;
		}
		closedir($dirHandle);
		return (!$csvList) ? null : $csvList;
	}

	public static function isCsvFile($path)
	{
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		return ($ext && $ext == 'csv');
	}

	public static function isSuperAdminUser($user_id = null)
	{
		if (!($user = JFactory::getUser($user_id)))
			return false;
		if(!($groups = $user->getAuthorisedGroups()))
			return false;
		if (!JAccess::getAssetRules(1)->allow('core.admin', $groups))
			return false;
		return true;
	}

	public static function getVmLangs($db = null) 
	{
		if (!$db)
			$db = JFactory::getDbo();
		if (!class_exists('VmConfig')) {
			require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
			VmConfig::loadConfig();
		}
		$vmlangs = array();
		$vmlang = VmConfig::get('vmlang');
		self::addLangTitleByCode($vmlangs, VmConfig::get('vmlang'), $db);
		$alangs = VmConfig::get('active_languages');
		if (is_array($alangs))
			foreach ($alangs as $alang)
				self::addLangTitleByCode($vmlangs, $alang, $db);
		if (!count($vmlangs))
			self::addLangTitleByCode($vmlangs, self::getConfigLang(), $db);
		return $vmlangs;
	}

	private static function addLangTitleByCode(&$langs, $lang_code, $db) {
		if (!$lang_code)
			return;
		list($l, $r) = preg_split('/[-_]/', $lang_code, 2);
		if (!($l && $r))
			return;
		$lang_ext = strtolower($l) . '_' . strtolower($r);
		if (array_key_exists($lang_ext, $langs))
			return;
		$lang_code = strtolower($l) . '-' . strtoupper($r);
		$db->setQuery("SELECT title_native FROM #__languages WHERE lang_code = '{$lang_code}' LIMIT 1");
		$title_native = $db->loadResult();
		if (!$title_native)
			$title_native = $lang_code;
		$langs[$lang_ext] = $title_native;
	}

	public static function requireIconv()
	{
		if (function_exists('iconv'))
			return true;
		JError::raiseWarning(100, JText::_('COM_VMPCSV_NO_ICONV'));
		return false;

	}

	public static function rewordCharset($charset)
	{
		if (strtolower($charset) == 'utf8')
			$charset = 'utf-8';
		return $charset;
	}

	private static $reportedIconvErrors = array();
	public static function iconvErrorHandler($errno, $errstr)
	{
		$md5 = md5($errstr);
		if (array_key_exists($md5, self::$reportedIconvErrors))
			return;
		self::$reportedIconvErrors[$md5] = true;
		JError::raiseWarning(100, $errstr);
	}

	private static $supportedCharsets = null;
	public static function getSupportedCharsets()
	{
		if (!self::$supportedCharsets) {
			self::$supportedCharsets = require(JPATH_ADMINISTRATOR . '/components/com_vm_products_csv/character_sets.php');
			if (!self::$supportedCharsets)
				self::$supportedCharsets = array('Default' => 'CP1252');
		}
		return self::$supportedCharsets;
	}

	public static function getCharsetSelectHtml($selected = 'Default', $name = 'encoding')
	{
		if (!$selected)
			$selected = 'Default';
		$html = "<select name=\"{$name}\">\n";
		$encs = self::getSupportedCharsets();
		foreach ($encs as $name => $enc) {
			$html .= "<option value=\"{$name}\"";
			if ($name == $selected)
				$html .= ' selected="selected"';
			$html .= ">{$name} [{$enc}]</option>\n";
		}
		$html .= '</select>';
		return $html;
	}

	public static function getCsvCharsetByName($name = 'Default')
	{
		if (!$name)
			$name = 'Default';
		$encs = self::getSupportedCharsets();
		$enc = $encs[$name];
		if (!$enc)
			die('Unsupported Character Set: ' . $name);
		return self::rewordCharset($enc);
	}

	private static $dbCharset = null;
	public static function getDbCharset($db)
	{
		if (!self::$dbCharset) {
			$db->setQuery("SHOW VARIABLES LIKE 'character_set_database'");
			$row = $db->loadAssoc();
			self::$dbCharset = self::rewordCharset((!($row && $row['Value'])) ? 'UTF8' : strtoupper($row['Value']));
		}
		return self::$dbCharset;
	}

	public static function getCustomFieldId($db, $name, $column = 'custom_title')
	{
		$db->setQuery("SELECT virtuemart_custom_id FROM #__virtuemart_customs WHERE {$column} = '{$name}' LIMIT 1");
		return $db->loadResult();
	}

	public static function getDownloadableParams($db, $virtuemart_product_id, $downloadable_plugin_id = 0)
	{
		if (!$downloadable_plugin_id)
			$downloadable_plugin_id = self::getCustomFieldId($db, 'downloadable', 'custom_element');
		if (!$downloadable_plugin_id)
			return array();
		$is_VM3 = (float)substr(VmConfig::getInstalledVersion(), 0, 3) > 2.6;
		$custom_param = $is_VM3 ? 'customfield_params' : 'custom_param';
		$db->setQuery("SELECT {$custom_param} FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = {$virtuemart_product_id} AND virtuemart_custom_id = {$downloadable_plugin_id} LIMIT 1");
		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}
		$custom_params = $db->loadResult();
		if (!$custom_params)
			return array();
		return json_decode($custom_params, true);
	}

	public static function getOrderStates($db)
	{
		$db->setQuery("SELECT order_status_code, order_status_name FROM #__virtuemart_orderstates");
		if ($db->getErrorNum()) {
			JError::raiseError(500, $db->getErrorMsg());
			return array();
		}
		$order_states = array();
		foreach ($db->loadAssocList() as $row) {
			$order_states[$row['order_status_code']] = $row['order_status_name'];
			$order_states[$row['order_status_name']] = $row['order_status_code'];
		}
		return $order_states;
	}

}
?>
