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

defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;


/**
 * Export VirtueMart multiple prices.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class Price extends \CsviModelExports
{
	/**
	 * Array of prices per product
	 *
	 * @var    array
	 * @since  6.0
	 */
	private $prices = array();

	/**
	 * An array of parent categories
	 *
	 * @var    array
	 * @since  6.5.5
	 */
	private $parentCategories = array();

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

			// Build something fancy to only get the fieldnames the user wants
			$userfields = array();
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
					case 'product_sku':
						$userfields[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id');
						}
						break;
					case 'product_name':
						$userfields[] = $this->db->quoteName('#__virtuemart_products.virtuemart_product_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_products.virtuemart_product_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_products.virtuemart_product_id');
						}
						break;
					case 'created_by':
					case 'created_on':
					case 'locked_by':
					case 'locked_on':
					case 'modified_by':
					case 'modified_on':
					case 'virtuemart_product_id':
					case 'virtuemart_shoppergroup_id':
						$userfields[] = $this->db->quoteName('#__virtuemart_product_prices.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_product_prices.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_product_prices.' . $field->field_name);
						}
						break;
					case 'basepricewithtax':
					case 'discountedpricewithouttax':
					case 'pricebeforetax':
					case 'salesprice':
					case 'taxamount':
					case 'discountamount':
					case 'pricewithouttax':
					case 'product_currency':
						$userfields[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
						$userfields[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
						$userfields[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
							$groupby[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
							$groupby[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
							$sortby[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
							$sortby[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id');
						}
						break;
					case 'custom':
						break;
					default:
						$userfields[] = $this->db->quoteName($field->field_name);

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
			$query->from($this->db->quoteName('#__virtuemart_product_prices'));
			$query->leftJoin(
					$this->db->quoteName('#__virtuemart_products')
					. ' ON ' . $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id') . ' = ' . $this->db->quoteName('#__virtuemart_products.virtuemart_product_id')
			);
			$query->leftJoin(
					$this->db->quoteName('#__virtuemart_shoppergroups')
					. ' ON ' . $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id') . ' = ' . $this->db->quoteName('#__virtuemart_shoppergroups.virtuemart_shoppergroup_id')
			);
			$query->leftJoin(
					$this->db->quoteName('#__virtuemart_currencies')
					. ' ON ' . $this->db->quoteName('#__virtuemart_product_prices.product_currency') . ' = ' . $this->db->quoteName('#__virtuemart_currencies.virtuemart_currency_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_product_categories')
				. ' ON ' . $this->db->quoteName('#__virtuemart_product_categories.virtuemart_product_id') . ' = ' . $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id')
			);

			// Filter by published state
			$publish_state = $this->template->get('publish_state');

			if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0))
			{
				$query->where($this->db->quoteName('#__virtuemart_products.published') . ' = ' . (int) $publish_state);
			}

			// Shopper group selector
			$shopper_group = $this->template->get('shopper_groups', array());

			if ($shopper_group && $shopper_group[0] != 'none')
			{
				$query->where($this->db->quoteName('#__virtuemart_shoppergroups.virtuemart_shoppergroup_id') . " IN ('" . implode("','", $shopper_group) . "')");
			}

			// Filter by product category
			$productCategories = $this->template->get('product_categories', false);
			$productCategories = ArrayHelper::toInteger($productCategories);

			if ($productCategories && 0 !== $productCategories[0] && 0 !== count($productCategories))
			{
				// If selected get products of all subcategories as well
				if ($this->template->get('incl_subcategory', false))
				{
					foreach ($productCategories as $productcategory)
					{
						$subids = $this->helper->getSubCategoryIds($productcategory);

						if ($subids)
						{
							$productCategories = array_merge($productCategories, $subids);
						}
					}
				}

				$query->where($this->db->quoteName('#__virtuemart_product_categories.virtuemart_category_id') . ' IN (' . implode(',', $productCategories) . ')');
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
				foreach ($records as $record)
				{
					$this->log->incrementLinenumber();

					foreach ($exportfields as $field)
					{
						$fieldname = $field->field_name;

						// Set the field value
						if (isset($record->$fieldname))
						{
							$fieldValue = $record->$fieldname;
						}
						else
						{
							$fieldValue = '';
						}

						// Process the field
						switch ($fieldname)
						{
							case 'product_sku':
								$query = $this->db->getQuery(true);
								$query->select($this->db->quoteName('product_sku'));
								$query->from($this->db->quoteName('#__virtuemart_products'));
								$query->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);
								$this->db->setQuery($query);
								$fieldValue = $this->db->loadResult();
								break;
							case 'product_name':
								$query = $this->db->getQuery(true);
								$query->select($this->db->quoteName($fieldname));
								$query->from($this->db->quoteName('#__virtuemart_products_' . $language));
								$query->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);
								$this->db->setQuery($query);
								$fieldValue = $this->db->loadResult();
								break;
							case 'product_price':
							case 'product_override_price':
								$fieldValue = number_format(
									$record->$fieldname,
									$this->template->get('export_price_format_decimal', 2, 'int'),
									$this->template->get('export_price_format_decsep'),
									$this->template->get('export_price_format_thousep')
								);
								break;
							case 'product_price_publish_up':
							case 'product_price_publish_down':
							case 'created_on':
							case 'locked_on':
							case 'modified_on':
								$fieldValue = $this->fields->getDateFormat($fieldname, $record->$fieldname, $field->column_header);
								break;
							case 'product_currency':
								$fieldValue = $record->currency_code_3;
								break;
							case 'shopper_group_name':
								// Check if the shopper group name is empty
								if (empty($field->default_value) && empty($fieldValue))
								{
									$fieldValue = '*';
								}

								$fieldValue = \JText::_($fieldValue);
								break;
							case 'basepricewithtax':
							case 'discountedpricewithouttax':
							case 'pricebeforetax':
							case 'salesprice':
							case 'taxamount':
							case 'discountamount':
							case 'pricewithouttax':
								$prices = $this->getProductPrice($record->virtuemart_product_id, $record->virtuemart_shoppergroup_id, $record->price_quantity_start);

								// Retrieve the requested price field
								if (isset($prices[$fieldname]))
								{
									$fieldValue = $prices[$fieldname];

									// Apply conversion if applicable
									if (!in_array($fieldValue, array('taxamount', 'discountamount')))
									{
										$fieldValue = $this->convertPrice($fieldValue, $record->currency_code_3);
									}

									$fieldValue = $this->formatNumber($fieldValue);
								}
								else
								{
									$fieldValue = null;
								}

								// Check if we have any content otherwise use the default value
								if (trim($fieldValue) === '')
								{
									$fieldValue = $field->default_value;
								}
								break;
							default:
								$fieldValue = \JText::_($fieldValue);
								break;
						}

						// Store the field value
						$this->fields->set($field->csvi_templatefield_id, $fieldValue);
					}

					// Output the data
					$this->addExportFields();

					// Output the contents
					$this->writeOutput();
				}
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
	 * Get product prices.
	 *
	 * @param   int  $product_id                  The ID of the product.
	 * @param   int  $virtuemart_shoppergroup_id  The ID of the price shopper group.
	 * @param   int  $quantity                    The quantity to get the price for.
	 *
	 * @return array List of prices.
	 *
	 * @since   4.0
	 */
	private function getProductPrice($product_id, $virtuemart_shoppergroup_id, $quantity)
	{
		$sid = $virtuemart_shoppergroup_id;

		// Check if the quantity is zero, if so force it to 1
		$quantity = (int) $quantity === 0 ? 1 : $quantity;

		if (!isset($this->prices[$sid . $quantity][$product_id]))
		{
			// Define VM constant to make the classes work
			if (!defined('JPATH_VM_ADMINISTRATOR'))
			{
				define('JPATH_VM_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_virtuemart/');
			}

			// Load the calculation helper
			require_once JPATH_PLUGINS . '/csviaddon/virtuemart/com_virtuemart/helper/com_virtuemart_calculation.php';

			// Load the configuration for the currency formatting
			require_once JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';

			// Load the VirtueMart configuration
			\VmConfig::loadConfig();

			// Load the calculation helper
			/** @var \CsviVmPrices $calc */
			$calc = \CsviVmPrices::getInstance();

			// Check if we need to use a template shopper group
			$sid = $virtuemart_shoppergroup_id;
			$virtuemart_shoppergroup_id = (array) $virtuemart_shoppergroup_id;

			// Set the shopper group
			$calc->setShopperGroup($virtuemart_shoppergroup_id);

			$this->log->add('Use shopper group ID: ' . $sid);

			// Load the product helper
			require_once JPATH_ADMINISTRATOR . '/components/com_virtuemart/models/product.php';
			$product = new \VirtueMartModelProduct;

			// Load the product info
			$product = $product->getProductSingle($product_id, true, $quantity, false, $virtuemart_shoppergroup_id);

			// See if this is a child product
			if (!$product->virtuemart_category_id && $product->product_parent_id > 0)
			{
				if (!array_key_exists($product->product_parent_id, $this->parentCategories))
				{
					// Set a category on the child product so it calculates the correct price
					$categoryIds = $this->helper->createCategoryPath($product->product_parent_id, true);

					$this->parentCategories[$product->product_parent_id] = $categoryIds;
				}

				if (array_key_exists($product->product_parent_id, $this->parentCategories))
				{
					$product->categories = explode('|', $this->parentCategories[$product->product_parent_id]);

					if (array_key_exists(0, $product->categories))
					{
						$product->virtuemart_category_id = $product->categories[0];
					}
				}
			}

			// Clear any existing prices
			$calc->productPrices = array();

			// Get the prices
			$prices = $calc->getProductPrices($product, 0.0, $quantity);

			if (is_array($prices))
			{
				$this->prices[$sid . $quantity][$product_id] = array_change_key_case($prices, CASE_LOWER);
			}
			else
			{
				$this->prices[$sid . $quantity][$product_id] = array();
			}
		}

		return $this->prices[$sid . $quantity][$product_id];
	}

	/**
	 * Convert prices to the new currency.
	 *
	 * @param   float   $product_price     The price to convert
	 * @param   string  $product_currency  The currency to convert to
	 *
	 * @return  float  A converted price.
	 *
	 * @since   4.0
	 *
	 * @throws  \RuntimeException
	 */
	private function convertPrice($product_price, $product_currency)
	{
		if (empty($product_price))
		{
			return $product_price;
		}
		else
		{
			// See if we need to convert the price
			if ($this->template->get('targetcurrency', '') !== '')
			{
				$query = $this->db->getQuery(true);
				$query->select($this->db->quoteName('currency_code') . ', ' . $this->db->quoteName('currency_rate'));
				$query->from($this->db->quoteName('#__csvi_currency'));
				$query->where(
					$this->db->quoteName('currency_code')
					. ' IN ('
					. $this->db->quote($product_currency) . ', ' . $this->db->quote($this->template->get('targetcurrency', 'EUR'))
					. ')'
				);
				$this->db->setQuery($query);
				$rates = $this->db->loadObjectList('currency_code');

				// Convert to base price
				$baseprice = $product_price / $rates[strtoupper($product_currency)]->currency_rate;

				// Convert to destination currency
				return $baseprice * $rates[strtoupper($this->template->get('targetcurrency', 'EUR'))]->currency_rate;
			}
			else
			{
				return $product_price;
			}
		}
	}

	/**
	 * Format a value to a number.
	 *
	 * @param   float  $fieldValue  The value to format as number.
	 *
	 * @return  string  The formatted number.
	 *
	 * @since   6.0
	 */
	private function formatNumber($fieldValue)
	{
		return number_format(
			$fieldValue,
			$this->template->get('export_price_format_decimal', 2, 'int'),
			$this->template->get('export_price_format_decsep'),
			$this->template->get('export_price_format_thousep')
		);
	}
}
