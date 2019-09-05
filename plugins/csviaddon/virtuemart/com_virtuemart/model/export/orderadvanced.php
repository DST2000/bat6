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

namespace virtuemart\com_virtuemart\model\export;

use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Export VirtueMart orders in XML format.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class Orderadvanced extends \CsviModelExports
{
	/**
	 * Cache shipping values
	 *
	 * @var    array
	 * @since  7.0
	 */
	private $cache = array();

	/**
	 * Export the data.
	 *
	 * @return  bool  True if body is exported | False if body is not exported.
	 *
	 * @since   6.0
	 *
	 * @throws  \CsviException
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Check if we have a language set
			$language = $this->template->get('language', false);

			if (!$language)
			{
				throw new \CsviException(\JText::_('COM_CSVI_NO_LANGUAGE_SET'));
			}

			// Check if the Orderline method exists, if so add the orderline
			if (!method_exists($this->exportclass, 'Order')
				|| !method_exists($this->exportclass, 'Orderline'))
			{
				// The template is set to orderadvanced type but export_site is set to something else. Fixing it
				\JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_csvi/tables');

				/** @var \TableTemplate $templateTable */
				$templateTable = \JTable::getInstance('Template', 'Table');
				$templateTable->load($this->template->getId());
				$options = new Registry;
				$options->loadArray(json_decode($templateTable->get('settings'), true));
				$options->set('export_site', 'orderadvanced');
				$options->set('export_file', 'xml');
				$templateTable->set('settings', json_encode($options->toArray()));
				$templateTable->store();

				throw new \CsviException(\JText::_('COM_CSVI_MISSING_ORDER_FUNCTIONS'));
			}

			$address = strtoupper($this->template->get('order_address', false));

			if ($address == 'BTST')
			{
				$user_info_fields = $this->dbFields('virtuemart_order_userinfos');
			}
			else
			{
				$user_info_fields = array();
			}

			// Build something fancy to only get the fieldnames the user wants
			$userfields = array();

			// Order ID is needed as controller
			$userfields[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_order_id');
			$exportfields = $this->fields->getFields();

			// Group by fields
			$groupbyfields = json_decode($this->template->get('groupbyfields', '', 'string'));
			$groupby = array();

			if (isset($groupbyfields->name))
			{
				$groupbyfields = array_flip($groupbyfields->name);
			}
			else
			{
				$groupbyfields = array();
			}

			// Sort selected fields
			$sortfields = json_decode($this->template->get('sortfields', '', 'string'));
			$sortby = array();

			if (isset($sortfields->name))
			{
				$sortbyfields = array_flip($sortfields->name);
			}
			else
			{
				$sortbyfields = array();
			}

			foreach ($exportfields as $field)
			{
				switch ($field->field_name)
				{
					case 'created_by':
					case 'created_on':
					case 'locked_by':
					case 'locked_on':
					case 'modified_by':
					case 'modified_on':
					case 'order_status':
					case 'virtuemart_user_id':
					case 'virtuemart_vendor_id':
					case 'virtuemart_order_id':
					case 'virtuemart_paymentmethod_id':
					case 'virtuemart_shipmentmethod_id':
						$userfields[] = $this->db->quoteName('#__virtuemart_orders.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_orders.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_orders.' . $field->field_name);
						}
						break;
					case 'email':
						$userfields[] = $this->db->quoteName('user_info1.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('user_info1.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('user_info1.' . $field->field_name);
						}
						break;
					case 'id':
						$userfields[] = $this->db->quoteName('#__users.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__users.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__users.' . $field->field_name);
						}
						break;
					case 'payment_element':
						$userfields[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_paymentmethod_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_paymentmethod_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_paymentmethod_id');
						}
						break;
					case 'shipment_element':
					case 'shipment_name':
						$userfields[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_shipmentmethod_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_shipmentmethod_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_shipmentmethod_id');
						}
						break;
					case 'state_2_code':
					case 'state_3_code':
					case 'state_name':
						if ($address == 'BTST')
						{
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.virtuemart_state_id') . ', ' . $this->db->quoteName('user_info1.virtuemart_state_id') . ') AS ' . $this->db->quoteName('virtuemart_state_id');

							if (array_key_exists($field->field_name, $groupbyfields))
							{
								$groupby[] = $this->db->quoteName('virtuemart_state_id');
							}

							if (array_key_exists($field->field_name, $sortbyfields))
							{
								$sortby[] = $this->db->quoteName('virtuemart_state_id');
							}
						}
						else
						{
							$userfields[] = $this->db->quoteName('user_info1.virtuemart_state_id');

							if (array_key_exists($field->field_name, $groupbyfields))
							{
								$groupby[] = $this->db->quoteName('user_info1.virtuemart_state_id');
							}

							if (array_key_exists($field->field_name, $sortbyfields))
							{
								$sortby[] = $this->db->quoteName('user_info1.virtuemart_state_id');
							}
						}
						break;
					case 'country_2_code':
					case 'country_3_code':
					case 'country_name':
					case 'virtuemart_country_id':
						if ($address == 'BTST')
						{
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.virtuemart_country_id') . ', ' . $this->db->quoteName('user_info1.virtuemart_country_id') . ') AS ' . $this->db->quoteName('virtuemart_country_id');

							if (array_key_exists($field->field_name, $groupbyfields))
							{
								$groupby[] = $this->db->quoteName('virtuemart_country_id');
							}

							if (array_key_exists($field->field_name, $sortbyfields))
							{
								$sortby[] = $this->db->quoteName('virtuemart_country_id');
							}
						}
						else
						{
							$userfields[] = $this->db->quoteName('user_info1.virtuemart_country_id');

							if (array_key_exists($field->field_name, $groupbyfields))
							{
								$groupby[] = $this->db->quoteName('user_info1.virtuemart_country_id');
							}

							if (array_key_exists($field->field_name, $sortbyfields))
							{
								$sortby[] = $this->db->quoteName('user_info1.virtuemart_country_id');
							}
						}
						break;
					case 'user_currency':
						$userfields[] = $this->db->quoteName('#__virtuemart_orders.user_currency_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_orders.user_currency_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_orders.user_currency_id');
						}
						break;
					case 'username':
						$userfields[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_user_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_user_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_orders.virtuemart_user_id');
						}
						break;
					case 'full_name':
						if ($address == 'BTST')
						{
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.first_name') . ', ' . $this->db->quoteName('user_info1.first_name') . ') AS ' . $this->db->quoteName('first_name');
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.middle_name') . ', ' . $this->db->quoteName('user_info1.middle_name') . ') AS ' . $this->db->quoteName('middle_name');
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.last_name') . ', ' . $this->db->quoteName('user_info1.last_name') . ') AS ' . $this->db->quoteName('last_name');

							if (array_key_exists($field->field_name, $groupbyfields))
							{
								$groupby[] = $this->db->quoteName('first_name');
								$groupby[] = $this->db->quoteName('middle_name');
								$groupby[] = $this->db->quoteName('last_name');
							}

							if (array_key_exists($field->field_name, $sortbyfields))
							{
								$sortby[] = $this->db->quoteName('first_name');
								$sortby[] = $this->db->quoteName('middle_name');
								$sortby[] = $this->db->quoteName('last_name');
							}
						}
						else
						{
							$userfields[] = $this->db->quoteName('user_info1.first_name');
							$userfields[] = $this->db->quoteName('user_info1.middle_name');
							$userfields[] = $this->db->quoteName('user_info1.last_name');

							if (array_key_exists($field->field_name, $groupbyfields))
							{
								$groupby[] = $this->db->quoteName('user_info1.first_name');
								$groupby[] = $this->db->quoteName('user_info1.middle_name');
								$groupby[] = $this->db->quoteName('user_info1.last_name');
							}

							if (array_key_exists($field->field_name, $sortbyfields))
							{
								$sortby[] = $this->db->quoteName('user_info1.first_name');
								$sortby[] = $this->db->quoteName('user_info1.middle_name');
								$sortby[] = $this->db->quoteName('user_info1.last_name');
							}
						}
						break;
					case 'shipping_full_name':
						if ($address == 'BTST')
						{
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.first_name') . ', ' . $this->db->quoteName('user_info1.first_name') . ') AS ' . $this->db->quoteName('shipping_first_name');
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.middle_name') . ', ' . $this->db->quoteName('user_info1.middle_name') . ') AS ' . $this->db->quoteName('shipping_middle_name');
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.last_name') . ', ' . $this->db->quoteName('user_info1.last_name') . ') AS ' . $this->db->quoteName('shipping_last_name');

							if (array_key_exists($field->field_name, $groupbyfields))
							{
								$groupby[] = $this->db->quoteName('first_name');
								$groupby[] = $this->db->quoteName('middle_name');
								$groupby[] = $this->db->quoteName('last_name');
							}

							if (array_key_exists($field->field_name, $sortbyfields))
							{
								$sortby[] = $this->db->quoteName('first_name');
								$sortby[] = $this->db->quoteName('middle_name');
								$sortby[] = $this->db->quoteName('last_name');
							}
						}
						break;
					case 'product_price_total':
						$userfields[] = $this->db->quoteName('product_item_price') . ' * ' . $this->db->quoteName('product_quantity') . ' AS ' . $this->db->quoteName('product_price_total');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('product_price_total');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('product_price_total');
						}
						break;
					case 'discount_percentage':
						$userfields[] = '(' . $this->db->quoteName('order_discount') . ' / ' . $this->db->quoteName('order_total') . ') * 100 AS ' . $this->db->quoteName('discount_percentage');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('discount_percentage');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('discount_percentage');
						}
						break;
					case 'virtuemart_product_id':
						$userfields[] = $this->db->quoteName('#__virtuemart_order_items.virtuemart_product_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_order_items.virtuemart_product_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_order_items.virtuemart_product_id');
						}
						break;
					case 'delivery_date':
						$userfields[] = $this->db->quoteName('#__virtuemart_orders.delivery_date');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_orders.delivery_date');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_orders.delivery_date');
						}
						break;
					case 'customer_note':
						$userfields[] = $this->db->quoteName('user_info1.customer_note');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('user_info1.customer_note');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('user_info1.customer_note');
						}
						break;
					case 'shipping_last_name':
					case 'shipping_company':
					case 'shipping_title':
					case 'shipping_first_name':
					case 'shipping_middle_name':
					case 'shipping_phone_1':
					case 'shipping_phone_2':
					case 'shipping_fax':
					case 'shipping_address_1':
					case 'shipping_address_2':
					case 'shipping_city':
					case 'shipping_zip':
					case 'shipping_email':
					case 'shipping_state_name':
					case 'shipping_state_2_code':
					case 'shipping_state_3_code':
					case 'shipping_country_name':
					case 'shipping_country_2_code':
					case 'shipping_country_3_code':
					case 'total_order_items':
					case 'custom':
						// These are man made fields, do not try to get them from the database
						break;
					default:
						if ($address == 'BTST' && preg_match("/" . $field->field_name . "/i", join(",", array_keys($user_info_fields))))
						{
							$userfields[] = 'COALESCE(' . $this->db->quoteName('user_info2.' . $field->field_name) . ', ' . $this->db->quoteName('user_info1.' . $field->field_name) . ') AS ' . $this->db->quoteName($field->field_name);
						}
						else
						{
							$userfields[] = $this->db->quoteName($field->field_name);
						}

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName($field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName($field->field_name);
						}
						break;
				}
			}

			// Build the query
			$userfields = array_unique($userfields);
			$query = $this->db->getQuery(true);
			$query->select(implode(",\n", $userfields));
			$query->from($this->db->quoteName('#__virtuemart_orders'));
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_order_items')
				. ' ON ' . $this->db->quoteName('#__virtuemart_orders.virtuemart_order_id') . ' = ' . $this->db->quoteName('#__virtuemart_order_items.virtuemart_order_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_order_userinfos', 'user_info1')
				. ' ON ' . $this->db->quoteName('#__virtuemart_orders.virtuemart_order_id') . ' = ' . $this->db->quoteName('user_info1.virtuemart_order_id')
			);

			if ($address == 'BTST')
			{
				$query->leftJoin(
					$this->db->quoteName('#__virtuemart_order_userinfos', 'user_info2')
					. ' ON ' . $this->db->quoteName('#__virtuemart_orders.virtuemart_order_id') . ' = ' . $this->db->quoteName('user_info2.virtuemart_order_id')
					. ' AND ' . $this->db->quoteName('user_info2.address_type') . ' = ' . $this->db->quote('ST')
				);
			}

			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_orderstates')
				. ' ON ' . $this->db->quoteName('#__virtuemart_orders.order_status') . ' = ' . $this->db->quoteName('#__virtuemart_orderstates.order_status_code')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_product_manufacturers')
				. ' ON ' . $this->db->quoteName('#__virtuemart_order_items.virtuemart_product_id') . ' = ' . $this->db->quoteName('#__virtuemart_product_manufacturers.virtuemart_product_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_manufacturers')
				. ' ON ' . $this->db->quoteName('#__virtuemart_product_manufacturers.virtuemart_manufacturer_id') . ' = ' . $this->db->quoteName('#__virtuemart_manufacturers.virtuemart_manufacturer_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__users')
				. ' ON ' . $this->db->quoteName('#__users.id') . ' = ' . $this->db->quoteName('user_info1.virtuemart_user_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_countries')
				. ' ON ' .
				$this->db->quoteName('#__virtuemart_countries.virtuemart_country_id') . ' = ' . $this->db->quoteName('user_info1.virtuemart_country_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_invoices')
				. ' ON ' . $this->db->quoteName('#__virtuemart_orders.virtuemart_order_id') . ' = ' . $this->db->quoteName('#__virtuemart_invoices.virtuemart_order_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_paymentmethods_' . $language)
				. ' ON ' . $this->db->quoteName('#__virtuemart_orders.virtuemart_paymentmethod_id') . ' = ' . $this->db->quoteName('#__virtuemart_paymentmethods_' . $language . '.virtuemart_paymentmethod_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_shipmentmethods_' . $language)
				. ' ON ' . $this->db->quoteName('#__virtuemart_orders.virtuemart_shipmentmethod_id') . ' = ' . $this->db->quoteName('#__virtuemart_shipmentmethods_' . $language . '.virtuemart_shipmentmethod_id')
			);

			// Filter by manufacturer
			$manufacturer = $this->template->get('ordermanufacturer', false);

			if ($manufacturer && $manufacturer[0] != 'none')
			{
				$query->where($this->db->quoteName('#__virtuemart_manufacturers.virtuemart_manufacturer_id') . ' IN (' . implode(',', $manufacturer) . ')');
			}

			// Filter by order number start
			$ordernostart = $this->template->get('ordernostart', 0, 'int');

			if ($ordernostart > 0)
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.virtuemart_order_id') . ' >= ' . (int) $ordernostart);
			}

			// Filter by order number end
			$ordernoend = $this->template->get('ordernoend', 0, 'int');

			if ($ordernoend > 0)
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.virtuemart_order_id') . ' <= ' . (int) $ordernoend);
			}

			// Filter by list of order numbers
			$orderlist = $this->template->get('orderlist');

			if ($orderlist)
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.virtuemart_order_id') . ' IN (' . $orderlist . ')');
			}

			// Check for a pre-defined date
			$daterange = $this->template->get('orderdaterange', '');

			if ($daterange != '')
			{
				$jdate       = \JFactory::getDate();
				$currentDate = $this->db->quote($jdate->format('Y-m-d'));

				switch ($daterange)
				{
					case 'lastrun':
						if (substr($this->template->getLastrun(), 0, 4) != '0000')
						{
							$query->where($this->db->quoteName('#__virtuemart_orders.created_on') . ' > ' . $this->db->quote($this->template->getLastrun()));
						}
						break;
					case 'yesterday':
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') = DATE_SUB(' . $currentDate . ', INTERVAL 1 DAY)');
						break;
					case 'thisweek':
						// Get the current day of the week
						$dayofweek = $jdate->__get('dayofweek');
						$offset = $dayofweek - 1;
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') <= ' . $currentDate);
						break;
					case 'lastweek':
						// Get the current day of the week
						$dayofweek = $jdate->__get('dayofweek');
						$offset = $dayofweek + 6;
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') <= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofweek . ' DAY)');
						break;
					case 'thismonth':
						// Get the current day of the week
						$dayofmonth = $jdate->__get('day');
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofmonth . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') <= ' . $currentDate);
						break;
					case 'lastmonth':
						// Get the current day of the week
						$dayofmonth = $jdate->__get('day');
						$month = date('n');
						$year = date('y');

						if ($month > 1)
						{
							$month--;
						}
						else
						{
							$month = 12;
							$year--;
						}

						$daysinmonth = date('t', mktime(0, 0, 0, $month, 25, $year));
						$offset = ($daysinmonth + $dayofmonth) - 1;

						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= DATE_SUB(' . $currentDate . ', INTERVAL ' . $offset . ' DAY)');
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') <= DATE_SUB(' . $currentDate . ', INTERVAL ' . $dayofmonth . ' DAY)');
						break;
					case 'thisquarter':
						// Find out which quarter we are in
						$month = $jdate->__get('month');
						$year = date('Y');
						$quarter = ceil($month / 3);

						switch ($quarter)
						{
							case '1':
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-01-01'));
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year . '-04-01'));
								break;
							case '2':
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-04-01'));
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year . '-07-01'));
								break;
							case '3':
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-07-01'));
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year . '-10-01'));
								break;
							case '4':
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-10-01'));
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year++ . '-01-01'));
								break;
						}
						break;
					case 'lastquarter':
						// Find out which quarter we are in
						$month = $jdate->__get('month');
						$year = date('Y');
						$quarter = ceil($month / 3);

						if ($quarter == 1)
						{
							$quarter = 4;
							$year--;
						}
						else
						{
							$quarter--;
						}

						switch ($quarter)
						{
							case '1':
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-01-01'));
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year . '-04-01'));
								break;
							case '2':
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-04-01'));
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year . '-07-01'));
								break;
							case '3':
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-07-01'));
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year . '-10-01'));
								break;
							case '4':
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-10-01'));
								$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year++ . '-01-01'));
								break;
						}
						break;
					case 'thisyear':
						$year = date('Y');
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-01-01'));
						$year++;
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year . '-01-01'));
						break;
					case 'lastyear':
						$year = date('Y');
						$year--;
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') >= ' . $this->db->quote($year . '-01-01'));
						$year++;
						$query->where('DATE(' . $this->db->quoteName('#__virtuemart_orders.created_on') . ') < ' . $this->db->quote($year . '-01-01'));
						break;
				}
			}
			else
			{
				// Filter by order date start
				$orderdatestart = $this->template->get('orderdatestart', false);

				if ($orderdatestart)
				{
					$orderdate = \JFactory::getDate($orderdatestart);
					$query->where($this->db->quoteName('#__virtuemart_orders') . '.' . $this->db->quoteName('created_on') . ' >= ' . $this->db->quote($orderdate->toSql()));
				}

				// Filter by order date end
				$orderdateend = $this->template->get('orderdateend', false);

				if ($orderdateend)
				{
					$orderdate = \JFactory::getDate($orderdateend);
					$query->where($this->db->quoteName('#__virtuemart_orders') . '.' . $this->db->quoteName('created_on') . ' <= ' . $this->db->quote($orderdate->toSql()));
				}

				// Filter by order modified date start
				$ordermdatestart = $this->template->get('ordermdatestart', false);

				if ($ordermdatestart)
				{
					$ordermdate = \JFactory::getDate($ordermdatestart);
					$query->where($this->db->quoteName('#__virtuemart_orders') . '.' . $this->db->quoteName('modified_on') . ' >= ' . $this->db->quote($ordermdate->toSql()));
				}

				// Filter by order modified date end
				$ordermdateend = $this->template->get('ordermdateend', false);

				if ($ordermdateend)
				{
					$ordermdate = \JFactory::getDate($ordermdateend);
					$query->where($this->db->quoteName('#__virtuemart_orders') . '.' . $this->db->quoteName('modified_on') . ' <= ' . $this->db->quote($ordermdate->toSql()));
				}
			}

			// Filter by order status
			$orderstatus = $this->template->get('orderstatus', false);

			if ($orderstatus && $orderstatus[0] != '')
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.order_status') . ' IN (\'' . implode("','", $orderstatus) . '\')');
			}

			// Filter by order price start
			$pricestart = $this->template->get('orderpricestart', false, 'float');

			if ($pricestart)
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.order_total') . ' >= ' . $pricestart);
			}

			// Filter by order price end
			$priceend = $this->template->get('orderpriceend', false, 'float');

			if ($priceend)
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.order_total') . ' <= ' . $priceend);
			}

			// Filter by order user id
			$orderuser = $this->template->get('orderuser', false);

			if ($orderuser && $orderuser[0] != '')
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.virtuemart_user_id') . ' IN (\'' . implode("','", $orderuser) . '\')');
			}

			// Filter by order product
			$orderproduct = $this->template->get('orderproduct', false);

			if ($orderproduct && $orderproduct[0] != '')
			{
				$query->where($this->db->quoteName('#__virtuemart_order_items.order_item_sku') . ' IN (\'' . implode("','", $orderproduct) . '\')');
			}

			// Filter by address type
			if ($address)
			{
				switch (strtoupper($address))
				{
					case 'BTST':
						$query->where($this->db->quoteName('user_info1.address_type') . ' = ' . $this->db->quote('BT'));
						break;
					default:
						$query->where($this->db->quoteName('user_info1.address_type') . ' = ' . $this->db->quote(strtoupper($address)));
						break;
				}
			}

			// Filter by order currency
			$ordercurrency = $this->template->get('ordercurrency', false);

			if ($ordercurrency && $ordercurrency[0] != '')
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.order_currency') . ' IN (\'' . implode("','", $ordercurrency) . '\')');
			}

			// Filter by payment method
			$orderpayment = $this->template->get('orderpayment', false);

			if ($orderpayment && $orderpayment[0] != '')
			{
				$query->where($this->db->quoteName('#__virtuemart_orders.virtuemart_paymentmethod_id') . ' IN (\'' . implode("','", $orderpayment) . '\')');
			}

			// Group the fields
			$groupby = array_unique($groupby);

			if (!empty($groupby))
			{
				$query->group($groupby);
			}

			// Sort set fields
			$sortby = array_unique($sortby);

			if (!empty($sortby))
			{
				$query->order($sortby);
			}

			// Add export limits
			$limits = $this->getExportLimit();

			// Execute the query
			$this->db->setQuery($query, $limits['offset'], $limits['limit']);
			$records = $this->db->getIterator();
			$this->log->add('Export query' . $query->__toString(), false);

			// Check if there are any records
			$logcount = $this->db->getNumRows();

			if ($logcount > 0)
			{
				$orderid = null;

				foreach ($records as $record)
				{
					$this->log->incrementLinenumber();

					// Add an order
					if (is_null($orderid) || $record->virtuemart_order_id != $orderid)
					{
						if (!is_null($orderid))
						{
							// Output the contents
							$this->addExportContent($this->exportclass->NodeEnd());
							$this->writeOutput();
						}

						$orderid = $record->virtuemart_order_id;
						$this->addExportContent($this->exportclass->Order());
					}

					// Add an orderline
					$this->addExportContent($this->exportclass->Orderline());

					foreach ($exportfields as $field)
					{
						$fieldname = $field->field_name;

						// Set the field value
						if (isset($record->$fieldname))
						{
							$fieldvalue = $record->$fieldname;
						}
						else
						{
							$fieldvalue = '';
						}

						// Process the field
						switch ($fieldname)
						{
							case 'payment_element':
								if (!array_key_exists($fieldname . $record->virtuemart_paymentmethod_id, $this->cache))
								{
									$query->clear()
										->select($fieldname)
										->from($this->db->quoteName('#__virtuemart_paymentmethods'))
										->where($this->db->quoteName('virtuemart_paymentmethod_id') . ' = ' . (int) $record->virtuemart_paymentmethod_id);
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_paymentmethod_id] = $this->db->loadResult();
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_paymentmethod_id];
								break;
							case 'shipment_element':
								if (!array_key_exists($fieldname . $record->virtuemart_shipmentmethod_id, $this->cache))
								{
									$query->clear()
										->select($this->db->quoteName($fieldname))
										->from($this->db->quoteName('#__virtuemart_shipmentmethods'))
										->where($this->db->quoteName('virtuemart_shipmentmethod_id') . ' = ' . (int) $record->virtuemart_shipmentmethod_id);
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_shipmentmethod_id] = $this->db->loadResult();
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_shipmentmethod_id];
								break;
							case 'shipment_name':
								if (!array_key_exists($fieldname . $record->virtuemart_shipmentmethod_id, $this->cache))
								{
									$query->clear()
										->select($this->db->quoteName($fieldname))
										->from($this->db->quoteName('#__virtuemart_shipmentmethods_' . $language))
										->where($this->db->quoteName('virtuemart_shipmentmethod_id') . ' = ' . (int) $record->virtuemart_shipmentmethod_id);
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_shipmentmethod_id] = $this->db->loadResult();
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_shipmentmethod_id];
								break;
							case 'state_2_code':
							case 'state_3_code':
							case 'state_name':
								if (!array_key_exists($fieldname . $record->virtuemart_state_id, $this->cache))
								{
									$query->clear()
										->select($this->db->quoteName($fieldname))
										->from($this->db->quoteName('#__virtuemart_states'))
										->where($this->db->quoteName('virtuemart_state_id') . ' = ' . (int) $record->virtuemart_state_id);
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_state_id] = $this->db->loadResult();
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_state_id];
								break;
							case 'country_2_code':
							case 'country_3_code':
							case 'country_name':
								if (!array_key_exists($fieldname . $record->virtuemart_country_id, $this->cache))
								{
									$query->clear()
										->select($this->db->quoteName($fieldname))
										->from($this->db->quoteName('#__virtuemart_countries'))
										->where($this->db->quoteName('virtuemart_country_id') . ' = ' . (int) $record->virtuemart_country_id);
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_country_id] = $this->db->loadResult();
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_country_id];
								break;
							case 'user_currency':
								if (!array_key_exists($fieldname . $record->user_currency_id, $this->cache))
								{
									$query->clear()
										->select($this->db->quoteName('currency_code_3'))
										->from($this->db->quoteName('#__virtuemart_currencies'))
										->where($this->db->quoteName('virtuemart_currency_id') . ' = ' . (int) $record->user_currency_id);
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->user_currency_id] = $this->db->loadResult();
								}

								$fieldvalue = $this->cache[$fieldname . $record->user_currency_id];
								break;
							case 'user_email':
								$fieldvalue = $record->email;
								break;
							case 'user_id':
								$fieldvalue = $record->virtuemart_user_id;
								break;
							case 'created_on':
							case 'modified_on':
							case 'locked_on':
								$fieldvalue = $this->fields->getDateFormat($fieldname, $record->$fieldname, $field->column_header);
								break;
							case 'address_type':
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0)
								{
									$fieldvalue = $field->default_value;
								}

								if ($fieldvalue == 'BT')
								{
									$fieldvalue = \JText::_('COM_CSVI_BILLING_ADDRESS');
								}
								elseif ($fieldvalue == 'ST')
								{
									$fieldvalue = \JText::_('COM_CSVI_SHIPPING_ADDRESS');
								}
								break;
							case 'full_name':
								$fieldvalue = str_replace('  ', ' ', $record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name);
								break;
							case 'shipping_full_name':
								if ($address == 'BTST')
								{
									$fieldvalue = str_replace('  ', ' ', $record->shipping_first_name . ' ' . $record->shipping_middle_name . ' ' . $record->shipping_last_name);
								}
								break;
							case 'total_order_items':
								if (!array_key_exists($fieldname . $record->virtuemart_order_id, $this->cache))
								{
									$query->clear()
										->select('COUNT(' . $this->db->quoteName('virtuemart_order_id') . ') AS ' . $this->db->quoteName('totalitems'))
										->from($this->db->quoteName('#__virtuemart_order_items'))
										->where($this->db->quoteName('virtuemart_order_id') . ' = ' . (int) $record->virtuemart_order_id);
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_order_id] = $this->db->loadResult();
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_order_id];
								break;
							case 'username':
								if (!array_key_exists($fieldname . $record->virtuemart_user_id, $this->cache))
								{
									$query->clear()
										->select($this->db->quoteName($fieldname))
										->from($this->db->quoteName('#__users'))
										->where($this->db->quoteName('id') . ' = ' . (int) $record->virtuemart_user_id);
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_user_id] = $this->db->loadResult();
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_user_id];
								break;
							case 'order_tax':
							case 'order_total':
							case 'order_subtotal':
							case 'order_shipment':
							case 'order_shipment_tax':
							case 'order_payment':
							case 'order_payment_tax':
							case 'coupon_discount':
							case 'order_discount':
							case 'user_currency_rate':
							case 'product_price_total':
							case 'discount_percentage':
							case 'product_item_price':
							case 'product_tax':
							case 'product_basePriceWithTax':
							case 'product_final_price':
							case 'product_subtotal_discount':
							case 'product_subtotal_with_tax':
								if ($fieldvalue)
								{
									$fieldvalue = number_format(
										$fieldvalue,
										$this->template->get('export_price_format_decimal', 2, 'int'),
										$this->template->get('export_price_format_decsep'),
										$this->template->get('export_price_format_thousep')
									);
								}
								break;
							case 'shipping_last_name':
							case 'shipping_company':
							case 'shipping_title':
							case 'shipping_first_name':
							case 'shipping_middle_name':
							case 'shipping_phone_1':
							case 'shipping_phone_2':
							case 'shipping_fax':
							case 'shipping_address_1':
							case 'shipping_address_2':
							case 'shipping_city':
							case 'shipping_zip':
							case 'shipping_email':
								if (!array_key_exists($fieldname . $record->virtuemart_order_id, $this->cache))
								{
									$query->clear();
									$query->select($this->db->quoteName(str_replace('shipping_', '', $fieldname)));
									$query->from($this->db->quoteName('#__virtuemart_order_userinfos'));
									$query->where($this->db->quoteName('virtuemart_order_id') . ' = ' . (int) $record->virtuemart_order_id);
									$query->where($this->db->quoteName('address_type') . ' = ' . $this->db->quote('ST'));
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_order_id] = $this->db->loadResult();
									$this->log->add('Find shipping last name' . $fieldvalue);
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_order_id];
								break;
							case 'shipping_state_name':
							case 'shipping_state_2_code':
							case 'shipping_state_3_code':
								if (!array_key_exists($fieldname . $record->virtuemart_order_id, $this->cache))
								{
									$query->clear();
									$query->select($this->db->quoteName(str_replace('shipping_', '', $fieldname)));
									$query->from($this->db->quoteName('#__virtuemart_order_userinfos', 'u'));
									$query->leftJoin(
										$this->db->quoteName('#__virtuemart_states', 's')
										. ' ON ' . $this->db->quoteName('s.virtuemart_state_id') . ' = ' . $this->db->quoteName('u.virtuemart_state_id')
									);
									$query->where($this->db->quoteName('u.virtuemart_order_id') . ' = ' . (int) $record->virtuemart_order_id);
									$query->where($this->db->quoteName('u.address_type') . ' = ' . $this->db->quote('ST'));
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_order_id] = $this->db->loadResult();
									$this->log->add('Find ' . $fieldname . ': ' . $fieldvalue);
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_order_id];
								break;
							case 'shipping_country_name':
							case 'shipping_country_2_code':
							case 'shipping_country_3_code':
								if (!array_key_exists($fieldname . $record->virtuemart_order_id, $this->cache))
								{
									$query->clear()
										->select($this->db->quoteName('c.' . str_replace('shipping_', '', $fieldname)))
										->from($this->db->quoteName('#__virtuemart_order_userinfos', 'u'))
										->leftJoin(
											$this->db->quoteName('#__virtuemart_countries', 'c')
											. ' ON ' . $this->db->quoteName('c.virtuemart_country_id') . ' = ' . $this->db->quoteName('u.virtuemart_country_id')
										)
										->where($this->db->quoteName('u.virtuemart_order_id') . ' = ' . (int) $record->virtuemart_order_id)
										->where($this->db->quoteName('u.address_type') . ' = ' . $this->db->quote('ST'));
									$this->db->setQuery($query);
									$this->cache[$fieldname . $record->virtuemart_order_id] = $this->db->loadResult();
									$this->log->add('Find ' . $fieldname . ': ' . $fieldvalue);
								}

								$fieldvalue = $this->cache[$fieldname . $record->virtuemart_order_id];
								break;
							case 'product_attribute':
								$options = json_decode($fieldvalue);
								$values = array();

								if (is_object($options))
								{
									foreach ($options as $virtuemart_custom_id => $option)
									{
										if (is_object($option))
										{
											foreach ($option as $option_type => $value)
											{
												if ($option_type)
												{
													// Get the option type name
													$query = $this->db->getQuery(true)
														->select($this->db->quoteName('c.custom_title'))
														->select($this->db->quoteName('cf.customfield_value'))
														->from($this->db->quoteName('#__virtuemart_product_customfields', 'cf'))
														->leftJoin(
															$this->db->quoteName('#__virtuemart_customs', 'c')
															. ' ON ' . $this->db->quoteName('c.virtuemart_custom_id') . ' = ' . $this->db->quoteName('cf.virtuemart_custom_id')
														)
														->where($this->db->quoteName('cf.virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom_id)
														->where($this->db->quoteName('cf.virtuemart_customfield_id') . ' = ' . (int) $option_type);
													$this->db->setQuery($query);

													$customFieldValue = $this->db->loadRow();
													$values[] = \JText::_($customFieldValue[0]) . ' ' . \JText::_($customFieldValue[1]);
												}

												if (is_array($value) || is_object($value))
												{
													foreach ($value as $option_field => $text)
													{
														// Check if we have a stockable product
														if ($option_type == 'stockable' && $option_field == 'child_id')
														{
															$query = $this->db->getQuery(true);
															$query->select($this->db->quoteName('product_name'));
															$query->from($this->db->quoteName('#__virtuemart_products_' . $language));
															$query->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $text);
															$this->db->setQuery($query);

															$values[] = $this->db->loadResult();
														}
														else
														{
															// Load the plugins
															$dispatcher = new \RantaiPluginDispatcher;
															$dispatcher->importPlugins('csviext', $this->db);

															// Fire the plugin to load specific custom fields if needed
															$customValue = $dispatcher->trigger(
																'getPluginCustomFieldValue',
																array(
																	'keyname' => $option_field,
																	'id' => $text,
																	'log' => $this->log
																)
															);

															if ($customValue)
															{
																$values[] = \JText::_($customValue[0]);
															}
															else
															{
																$values[] = \JText::_($text);
															}
														}
													}
												}
											}
										}
										else
										{
											$query = $this->db->getQuery(true)
												->select($this->db->quoteName('c.custom_title'))
												->select($this->db->quoteName('cf.customfield_value'))
												->from($this->db->quoteName('#__virtuemart_product_customfields', 'cf'))
												->leftJoin(
													$this->db->quoteName('#__virtuemart_customs', 'c')
													. ' ON ' . $this->db->quoteName('c.virtuemart_custom_id') . ' = ' . $this->db->quoteName('cf.virtuemart_custom_id')
												)
												->where($this->db->quoteName('c.virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom_id)
												->where($this->db->quoteName('cf.virtuemart_customfield_id') . ' = ' . (int) $option);

											$this->db->setQuery($query);

											$customFieldData = $this->db->loadRow();

											$values[] = \JText::_($customFieldData[0]) . ' ' . \JText::_($customFieldData[1]);
										}
									}

									if ($values)
									{
										$fieldvalue = implode('|', $values);
									}
								}
								break;
							case 'custom':
								$fieldvalue = $field->default_value;
								break;
						}

						// Store the field value
						$this->fields->set($field->csvi_templatefield_id, $fieldvalue);
					}

					// Output the data
					$this->addExportFields(false);
				}

				// Close the XML structure
				$this->addExportContent($this->exportclass->NodeEnd());
				$this->writeOutput();
			}
			else
			{
				$this->addExportContent(\JText::_('COM_CSVI_NO_DATA_FOUND'));

				// Output the contents
				$this->writeOutput();
			}
		}
	}

	/**
	 * Creates an array of custom database fields the user can use for import/export.
	 *
	 * @param   string  $table  The table to get the fields for.
	 *
	 * @return  array  List of custom database fields.
	 *
	 * @since   3.0
	 */
	private function dbFields($table)
	{
		$customfields = array();
		$q = 'SHOW COLUMNS FROM ' . $this->db->quoteName('#__' . $table);
		$this->db->setQuery($q);
		$fields = $this->db->loadObjectList();

		if (count($fields) > 0)
		{
			foreach ($fields as $field)
			{
				$customfields[$field->Field] = null;
			}
		}

		return $customfields;
	}
}
