<?php
/**
 * @package     CSVI
 * @subpackage  VirtueMart
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2018 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with manufacturers.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class JFormFieldCsviVirtuemartManufacturer extends JFormFieldCsviForm
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $type = 'CsviVirtuemartManufacturer';

	/**
	 * Get the options.
	 *
	 * @return  array  An array of customfields.
	 *
	 * @since   4.0
	 *
	 * @throws  RuntimeException
	 *
	 * @todo    Change to autoloader
	 */
	protected function getOptions()
	{
		// Get the default language from virtuemart config
		$templateId   = JFactory::getApplication()->input->get('csvi_template_id', 0, 'int');
		$settings     = new CsviHelperSettings($this->db);
		$log          = new CsviHelperLog($settings, $this->db);
		$template     = new CsviHelperTemplate($templateId);
		$fields       = new CsviHelperFields($template, $log, $this->db);
		$languageCode = $template->get('language');

		if (!$languageCode)
		{
			require_once JPATH_PLUGINS . '/csviaddon/virtuemart/com_virtuemart/helper/com_virtuemart.php';
			$helperConfig = new Com_VirtuemartHelperCom_Virtuemart($template, $log, $fields, $this->db);
			$languageCode = $helperConfig->getDefaultLanguage();
		}

		$lang = strtolower(str_replace('-', '_', $languageCode));

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('virtuemart_manufacturer_id', 'value') . ',' . $this->db->quoteName('mf_name', 'text'))
			->from($this->db->quoteName('#__virtuemart_manufacturers_' . $lang));
		$this->db->setQuery($query);
		$options = $this->db->loadObjectList();

		if (0 === count($options))
		{
			$options = array();
		}

		return array_merge(parent::getOptions(), $options);
	}
}
