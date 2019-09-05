<?php
/**
 * @package     CSVI
 * @subpackage  Fields
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - [year] RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with order payments.
 *
 * @package     CSVI
 * @subpackage  Fields
 * @since       4.0
 */
class JFormFieldCsviVirtuemartOrderPayment extends JFormFieldCsviForm
{

	protected $type = 'CsviVirtuemartOrderPayment';

	/**
	 * Specify the options to load based on default site language.
	 *
	 * @todo    Change to autoloader
	 *
	 * @return  array  List of payment options used to order.
	 *
	 * @since   4.0
	 *
	 * @throws  Exception
	 * @throws  CsviException
	 * @throws  RuntimeException
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

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('virtuemart_paymentmethod_id AS value, payment_name AS text')
			->from('#__virtuemart_paymentmethods_' . $languageCode);
		$db->setQuery($query);
		$methods = $db->loadObjectList();

		if (!$methods)
		{
			$methods = array();
		}

		return array_merge(parent::getOptions(), $methods);
	}
}
