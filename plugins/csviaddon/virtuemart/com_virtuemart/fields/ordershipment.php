<?php
/**
 * List the order shipping
 *
 * @author 		RolandD Cyber Produksi
 * @link 		https://csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2018 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvivirtuemartorderpayment.php 2396 2013-03-24 11:55:23Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with order shipping
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.6.0
 */
class CsvivirtuemartFormFieldOrdershipment extends JFormFieldCsviForm
{
	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JForm  $form  The form to attach to the form field object.
	 *
	 * @since   6.0
	 *
	 * @throws  Exception
	 */
	public function __construct($form = null)
	{
		$this->type = 'OrderShipment';

		parent::__construct($form);
	}

	/**
	 * Select order shipment methods.
	 *
	 * @return  array  An array of shipping methods.
	 *
	 * @since   6.6.0
	 *
	 * @throws  Exception
	 * @throws  CsviException
	 * @throws  RuntimeException
	 *
	 * @todo    Change to autoloader
	 */
	protected function getOptions()
	{
		// Get the default language from virtuemart config
		$templateId = JFactory::getApplication()->input->get('csvi_template_id', 0, 'int');
		$settings   = new CsviHelperSettings($this->db);
		$log        = new CsviHelperLog($settings, $this->db);
		$template   = new CsviHelperTemplate($templateId);
		$fields     = new CsviHelperFields($template, $log, $this->db);
		$language   = $template->get('language', false);

		if (!$language)
		{
			require_once JPATH_PLUGINS . '/csviaddon/virtuemart/com_virtuemart/helper/com_virtuemart.php';
			$helperConfig = new Com_VirtuemartHelperCom_Virtuemart($template, $log, $fields, $this->db);
			$language = $helperConfig->getDefaultLanguage();
		}

		$query = $this->db->getQuery(true)
			->select(
				array(
					$this->db->quoteName('s.virtuemart_shipmentmethod_id', 'value'),
					$this->db->quoteName('s.shipment_name', 'text'))
			)
			->from($this->db->quoteName('#__virtuemart_shipmentmethods_' . $language, 's'))
			->rightJoin(
				$this->db->quoteName('#__virtuemart_orders', 'o')
				. ' ON ' . $this->db->quoteName('o.virtuemart_shipmentmethod_id') . ' = ' . $this->db->quoteName('s.virtuemart_shipmentmethod_id')
			)
			->where($this->db->quoteName('s.shipment_name') . ' != ' . $this->db->quote(''))
			->order($this->db->quoteName('text'))
			->group($this->db->quoteName('value'));
		$this->db->setQuery($query);
		$shippingMethods = $this->db->loadObjectList();

		if (!$shippingMethods)
		{
			$shippingMethods = array();
		}

		return array_merge(parent::getOptions(), $shippingMethods);
	}
}
