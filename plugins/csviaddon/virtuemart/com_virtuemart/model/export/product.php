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
 * Exports VirtueMart products.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class Product extends \CsviModelExports
{
	/**
	 * The domain name for URLs.
	 *
	 * @var    string
	 * @since  6.0
	 */
	private $domainName = '';

	/**
	 * Array of prices per product
	 *
	 * @var    array
	 * @since  6.0
	 */
	private $prices = array();

	/**
	 * List of custom fields
	 *
	 * @var    array
	 * @since  6.0
	 */
	private $customFields = array();

	/**
	 * The custom fields that can be used as available field.
	 *
	 * @var    array
	 * @since  6.0
	 */
	private $customFieldsExport = array();

	/**
	 * The multi variant fields that can be used as available field.
	 *
	 * @var    array
	 * @since  6.0
	 */
	private $multiVariantExport = array();

	/**
	 * An array of parent categories
	 *
	 * @var    array
	 * @since  6.5.5
	 */
	private $parentCategories = array();

	/**
	 * VirtueMart helper
	 *
	 * @var    \Com_VirtuemartHelperCom_Virtuemart
	 * @since  6.0
	 */
	protected $helper;

	/**
	 * VirtueMart helper config
	 *
	 * @var    \Com_VirtuemartHelperCom_Virtuemart_Config
	 * @since  6.0
	 */
	protected $helperConfig;

	/**
	 * The multi language CF4ALL fields that can be used as available field.
	 *
	 * @var    array
	 * @since  7.5.0
	 */
	private $multiLangFields = array();

	/**
	 * Export the data.
	 *
	 * @return  bool  True if body is exported | False if body is not exported.
	 *
	 * @since   6.0
	 *
	 * @throws  \CsviException
	 * @throws  \Exception
	 * @throws  \RuntimeException
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Get and set the language
			$language = $this->template->get('language', false);

			if (!$language)
			{
				throw new \CsviException(\JText::_('COM_CSVI_NO_LANGUAGE_SET'));
			}

			// Get some basic data
			$jinput           = \JFactory::getApplication()->input;
			$this->domainName = $this->settings->get('hostname');
			$this->loadCustomFields();
			$this->loadMultiVariantFields();
			$this->loadMultiLanguageCF4ALLFields();
			$exportFields   = $this->fields->getFields();
			$groupByDefault = true;
			$useQuantity    = false;

			// Load the plugins
			$dispatcher = new \RantaiPluginDispatcher;
			$dispatcher->importPlugins('csviext', $this->db);

			$jinput->set('vmlang', substr($language, 0, 2) . '-' . strtoupper(substr($language, 3)));

			// Group by fields
			$groupByFields = json_decode($this->template->get('groupbyfields', '', 'string'));
			$groupBy       = array();

			if (isset($groupByFields->name))
			{
				$groupByFields = array_flip($groupByFields->name);
			}
			else
			{
				$groupByFields = array();
			}

			// Sort selected fields
			$sortFields = json_decode($this->template->get('sortfields', '', 'string'));
			$sortBy     = array();

			if (isset($sortFields->name))
			{
				$sortByFields = array_flip($sortFields->name);
			}
			else
			{
				$sortByFields = array();
			}

			// Build something fancy to only get the fieldnames the user wants
			$userFields   = array();
			$userFields[] = $this->db->quoteName('#__virtuemart_products.virtuemart_product_id');
			$userFields[] = $this->db->quoteName('#__virtuemart_products.product_parent_id');

			foreach ($exportFields as $field)
			{
				switch ($field->field_name)
				{
					case 'created_on':
					case 'modified_on':
					case 'locked_on':
					case 'created_by':
					case 'modified_by':
					case 'locked_by':
					case 'virtuemart_product_id':
					case 'virtuemart_vendor_id':
					case 'hits':
					case 'metaauthor':
					case 'metarobot':
					case 'published':
						$userFields[] = $this->db->quoteName('#__virtuemart_products.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_products.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_products.' . $field->field_name);
						}
						break;
					case 'category_id':
					case 'category_path':
					case 'category_name':
						$userFields[] = $this->db->quoteName('#__virtuemart_product_categories.virtuemart_category_id');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_product_categories.virtuemart_category_id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_categories.virtuemart_category_id');
						}
						break;
					case 'product_ordering':
						$userFields[] = $this->db->quoteName('#__virtuemart_product_categories.ordering', 'product_ordering');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_product_categories.ordering', 'product_ordering');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_categories.ordering', 'product_ordering');
						}
						break;
					case 'related_products':
					case 'related_categories':
						$userFields[] = $this->db->quoteName('#__virtuemart_products.virtuemart_product_id', 'main_product_id');
						break;
					case 'product_box':
						$userFields[] = $this->db->quoteName('#__virtuemart_products.product_params');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_products.product_params');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_products.product_params');
						}
						break;
					case 'product_price':
					case 'price_with_tax':
						$userFields[] = $this->db->quoteName('#__virtuemart_product_prices.product_price');
						$userFields[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
						$userFields[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_product_prices.product_price');
							$groupBy[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
							$groupBy[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_prices.product_price');
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
							$sortBy[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
						}
						break;
					case 'product_url':
						$userFields[] = $this->db->quoteName('#__virtuemart_products.virtuemart_product_id');
						$userFields[] = $this->db->quoteName('#__virtuemart_products.product_url');
						$userFields[] = $this->db->quoteName('#__virtuemart_products.product_parent_id');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_products.virtuemart_product_id');
							$groupBy[] = $this->db->quoteName('#__virtuemart_products.product_url');
							$groupBy[] = $this->db->quoteName('#__virtuemart_products.product_parent_id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_products.virtuemart_product_id');
							$sortBy[] = $this->db->quoteName('#__virtuemart_products.product_url');
							$sortBy[] = $this->db->quoteName('#__virtuemart_products.product_parent_id');
						}
						break;
					case 'price_with_discount':
						$userFields[] = $this->db->quoteName('#__virtuemart_product_prices.product_price');
						$userFields[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_product_prices.product_price');
							$groupBy[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_prices.product_price');
							$sortBy[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
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
						$userFields[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
						$userFields[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
							$groupBy[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_currencies.currency_code_3');
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
						}
						break;
					case 'max_order_level':
					case 'min_order_level':
					case 'step_order_level':
						$userFields[] = $this->db->quoteName('#__virtuemart_products.product_params');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_products.product_params');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_products.product_params');
						}
						break;
					case 'product_discount':
						$userFields[] = $this->db->quoteName('#__virtuemart_product_prices.product_discount_id');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_product_prices.product_discount_id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_prices.product_discount_id');
						}
						break;
					case 'virtuemart_shoppergroup_id':
					case 'shopper_group_name_price':
						$userFields[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');

						if (array_key_exists($field->field_name, $groupByFields))
						{
							$groupBy[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
						}

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id');
						}
						break;
					case 'product_name':
					case 'product_s_desc':
					case 'product_desc':
					case 'product_parent_sku':
					case 'metadesc':
					case 'metakey':
					case 'slug':
					case 'customtitle':
						$userFields[] = $this->db->quoteName('#__virtuemart_products.product_parent_id');
						break;
					case 'price_quantity_start':
					case 'price_quantity_end':
						$groupByDefault = false;
						$useQuantity    = true;
						$userFields[]   = $this->db->quoteName('#__virtuemart_product_prices.' . $field->field_name);

						if (array_key_exists($field->field_name, $sortByFields))
						{
							$sortBy[] = $this->db->quoteName('#__virtuemart_product_prices.' . $field->field_name);
						}
						break;

					// Special treatment, do not export them
					case 'custom_value':
					case 'custom_param':
					case 'custom_price':
					case 'custom_title':
					case 'custom_ordering':
					case 'file_url':
					case 'file_url_thumb':
					case 'file_title':
					case 'file_description':
					case 'file_meta':
					case 'file_lang':
					case 'file_ordering':
					case 'shopper_group_name':
					case 'manufacturer_name':
					case 'picture_url':
					case 'picture_url_thumb':
					case 'custom':
					case 'custom_override':
					case 'custom_disabler':
						break;
					case stripos($field->field_name, 'custom_param_') !== false:
						break;
					default:

						// Do not include custom fields into the query
						if (!in_array($field->field_name, $this->customFieldsExport)
							&& !in_array($field->field_name, $this->multiVariantExport))
						{
							$userFields[] = $this->db->quoteName($field->field_name);

							if (array_key_exists($field->field_name, $groupByFields))
							{
								$groupBy[] = $this->db->quoteName($field->field_name);
							}

							if (array_key_exists($field->field_name, $sortByFields))
							{
								$sortBy[] = $this->db->quoteName($field->field_name);
							}
						}

						break;
				}
			}

			// Export SQL Query
			$userFields = array_unique($userFields);
			$query      = $this->db->getQuery(true);
			$query->select(implode(",\n", $userFields));
			$query->from($this->db->quoteName('#__virtuemart_products'));
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_product_prices')
				. ' ON ' . $this->db->quoteName('#__virtuemart_products.virtuemart_product_id') . ' = ' . $this->db->quoteName('#__virtuemart_product_prices.virtuemart_product_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_product_manufacturers')
				. ' ON ' . $this->db->quoteName('#__virtuemart_products.virtuemart_product_id') . ' = ' . $this->db->quoteName('#__virtuemart_product_manufacturers.virtuemart_product_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_manufacturers')
				. ' ON ' . $this->db->quoteName('#__virtuemart_product_manufacturers.virtuemart_manufacturer_id') . ' = ' . $this->db->quoteName('#__virtuemart_manufacturers.virtuemart_manufacturer_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_product_categories')
				. ' ON ' . $this->db->quoteName('#__virtuemart_products.virtuemart_product_id') . ' = ' . $this->db->quoteName('#__virtuemart_product_categories.virtuemart_product_id')
			);

			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_currencies')
				. ' ON ' . $this->db->quoteName('#__virtuemart_currencies.virtuemart_currency_id') . ' = ' . $this->db->quoteName('#__virtuemart_product_prices.product_currency')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_product_shoppergroups')
				. ' ON ' . $this->db->quoteName('#__virtuemart_product_shoppergroups.virtuemart_product_id') . ' = ' . $this->db->quoteName('#__virtuemart_products.virtuemart_product_id')
			);

			// Filter by product category
			/**
			 * We are doing a selection on categories, need to redo the query to make sure child products get included
			 * 1. Search all product ID's for that particular category
			 * 2. Search for all child product ID's
			 * 3. Load all products with these ids
			 */
			$productCategories = $this->template->get('product_categories', false);

			// Sanity check
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

				// Get only the parent products and products without children
				if ($this->template->get('parent_only', 0, 'bool'))
				{
					// Get all product IDs in the selected categories
					$q_product_ids = "SELECT p.virtuemart_product_id
								FROM #__virtuemart_products p
								LEFT JOIN #__virtuemart_product_categories x
								ON p.virtuemart_product_id = x.virtuemart_product_id
								WHERE x.virtuemart_category_id IN (" . implode(',', $productCategories) . ")
								AND p.product_parent_id = 0";
					$this->db->setQuery($q_product_ids);
					$product_ids = $this->db->loadColumn();
					$this->log->add('Get all the product IDs in the selected categories');
				}
				// Get only the child products and products without children
				elseif ($this->template->get('child_only', 0, 'bool'))
				{
					// Load all non child IDs
					$q_child = "SELECT p.virtuemart_product_id
										FROM #__virtuemart_products p
										LEFT JOIN #__virtuemart_product_categories x
										ON p.virtuemart_product_id = x.virtuemart_product_id
										WHERE x.virtuemart_category_id IN ('" . implode("','", $productCategories) . "')";
					$this->db->setQuery($q_child);
					$allproduct_ids = $this->db->loadColumn();
					$this->log->add('Export query');

					// Get all child product IDs in the selected categories
					$q_child = "SELECT p.virtuemart_product_id
								FROM #__virtuemart_products p
								WHERE p.product_parent_id IN ('" . implode("','", $allproduct_ids) . "')";
					$this->db->setQuery($q_child);
					$child_ids = $this->db->loadColumn();
					$this->log->add('Export query');

					// Get all parent product IDs in the selected categories
					$q_child = "SELECT p.product_parent_id
								FROM #__virtuemart_products p
								WHERE p.virtuemart_product_id IN ('" . implode("','", $child_ids) . "')";
					$this->db->setQuery($q_child);
					$parent_ids = $this->db->loadColumn();
					$this->log->add('Export query');

					// Combine all the IDs
					$product_ids = array_merge($child_ids, array_diff($allproduct_ids, $parent_ids));
				}
				else
				{
					// Get all product IDs
					$q_product_ids = "SELECT p.virtuemart_product_id
								FROM #__virtuemart_products p
								LEFT JOIN #__virtuemart_product_categories x
								ON p.virtuemart_product_id = x.virtuemart_product_id
								WHERE x.virtuemart_category_id IN ('" . implode("','", $productCategories) . "')";
					$this->db->setQuery($q_product_ids);
					$product_ids = $this->db->loadColumn();
					$this->log->add('Export query');

					// Get all child product IDs
					if ($product_ids)
					{
						$q_childproduct_ids = "SELECT p.virtuemart_product_id
									FROM #__virtuemart_products p
									WHERE p.product_parent_id IN ('" . implode("','", $product_ids) . "')";
						$this->db->setQuery($q_childproduct_ids);
						$childproduct_ids = $this->db->loadColumn();
						$this->log->add('Export query');

						// Now we have all the product IDs
						$product_ids = array_merge($product_ids, $childproduct_ids);
					}
				}

				// Check if the user want child products
				if (!empty($product_ids))
				{
					// Remove duplicates
					$cleanIds = array_unique($product_ids, SORT_NUMERIC);

					$query->where('#__virtuemart_products.virtuemart_product_id IN (' . implode(',', $cleanIds) . ')');
				}
			}
			else
			{
				// Filter by published category state
				$category_publish = $this->template->get('publish_state_categories');

				// Filter on parent products and products without children
				if ($this->template->get('parent_only', 0, 'bool'))
				{
					$query->where($this->db->quoteName('#__virtuemart_products.product_parent_id') . ' = 0');

					if (!empty($category_publish))
					{
						$query->leftJoin(
							$this->db->quoteName('#__virtuemart_categories')
							. ' ON ' . $this->db->quoteName('#__virtuemart_categories.virtuemart_category_id') . ' = ' . $this->db->quoteName('#__virtuemart_product_categories.virtuemart_category_id')
						)
							->where($this->db->quoteName('#__virtuemart_categories.published') . ' = ' . (int) $category_publish);
					}
				}

				// Filter on child products and products without children
				elseif ($this->template->get('child_only', 0, 'bool'))
				{
					// Load all non child IDs
					$nonchildQuery = $this->db->getQuery(true)
						->select($this->db->quoteName('p.virtuemart_product_id'))
						->from($this->db->quoteName('#__virtuemart_products', 'p'))
						->where($this->db->quoteName('p.product_parent_id') . ' = 0');

					$state = ($category_publish == '1') ? '0' : '1';

					if (!empty($category_publish))
					{
						$nonchildQuery
							->leftJoin($this->db->quoteName('#__virtuemart_product_categories', 'pc'))
						. ' ON ' . $this->db->quoteName('p.virtuemart_product_id') . ' = ' . $this->db->quoteName('pc.virtuemart_product_id')
							->leftJoin($this->db->quoteName('#__virtuemart_categories', 'c'))
						. ' ON ' . $this->db->quoteName('pc.virtuemart_category_id') . ' = ' . $this->db->quoteName('c.virtuemart_category_id')
							->where($this->db->quoteName('c.published') . ' = ' . (int) $state);
					}

					$this->db->setQuery($nonchildQuery);
					$nonchild_ids = $this->db->loadColumn();
					$this->log->add('Load all non-child IDs', true);

					// Get the child IDs from the filtered category
					if (!empty($category_publish))
					{
						$nonchildQuery
							->clear('join')
							->clear('where')
							->leftJoin($this->db->quoteName('#__virtuemart_product_categories', 'pc'))
						. ' ON ' . $this->db->quoteName('p.virtuemart_product_id') . ' = ' . $this->db->quoteName('pc.virtuemart_product_id')
							->leftJoin($this->db->quoteName('#__virtuemart_categories', 'c'))
						. ' ON ' . $this->db->quoteName('pc.virtuemart_category_id') . ' = ' . $this->db->quoteName('c.virtuemart_category_id')
							->where($this->db->quoteName('p.product_parent_id') . ' IN (' . implode(',', $nonchild_ids) . ')')
							->group($this->db->quoteName('p.virtuemart_product_id'));

						$this->db->setQuery($nonchildQuery);
						$child_ids = $this->db->loadColumn();
						$this->log->add('Get the children from the filtered categories', true);

						if (is_array($child_ids))
						{
							$nonchild_ids = array_merge($nonchild_ids, $child_ids);
						}
					}

					$query->where('#__virtuemart_products.virtuemart_product_id NOT IN (' . implode(',', $nonchild_ids) . ')');
				}
				else
				{
					if (!empty($category_publish))
					{
						// Get all product IDs
						$q_product_ids = "SELECT p.virtuemart_product_id
									FROM #__virtuemart_products p
									LEFT JOIN #__virtuemart_product_categories x
									ON p.virtuemart_product_id = x.virtuemart_product_id
									LEFT JOIN #__virtuemart_categories c
									ON x.virtuemart_category_id = c.virtuemart_category_id
									WHERE c.published = " . $this->db->Quote($category_publish);
						$this->db->setQuery($q_product_ids);
						$product_ids = $this->db->loadColumn();
						$this->log->add('Export query');

						// Get all child product IDs
						if ($product_ids)
						{
							$q_childproduct_ids = "SELECT p.virtuemart_product_id
										FROM #__virtuemart_products p
										WHERE p.product_parent_id IN (" . implode(',', $product_ids) . ")";
							$this->db->setQuery($q_childproduct_ids);
							$childproduct_ids = $this->db->loadColumn();
							$this->log->add('Export query');

							// Now we have all the product IDs
							$product_ids = array_merge($product_ids, $childproduct_ids);
						}

						// Check if the user want child products
						if (!empty($product_ids))
						{
							$query->where('#__virtuemart_products.virtuemart_product_id IN (' . implode(',', $product_ids) . ')');
						}
					}
				}
			}

			// Filter on featured products
			$featured = $this->template->get('featured', '');

			if ($featured)
			{
				$query->where('#__virtuemart_products.product_special = 1');
			}

			// Filter by published state
			$product_publish = $this->template->get('publish_state');

			if ($product_publish !== '' && ($product_publish == 1 || $product_publish == 0))
			{
				$query->where('#__virtuemart_products.published = ' . (int) $product_publish);
			}

			// Include/exclude by product SKU
			$inclproductskufilter = $this->template->get('incl_productskufilter');

			// Filter by product SKU
			$productskufilter = $this->template->get('productskufilter');

			if ($productskufilter)
			{
				$productskufilter .= ',';

				if (strpos($productskufilter, ','))
				{
					$skus     = explode(',', $productskufilter);
					$wildcard = '';
					$normal   = array();

					foreach ($skus as $sku)
					{
						if (!empty($sku))
						{
							if (strpos($sku, '%'))
							{
								// Check if filter is for include or exclude of product SKU
								if ($inclproductskufilter)
								{
									$wildcard .= $this->db->quoteName('#__virtuemart_products.product_sku') . ' LIKE ' . $this->db->quote($sku) . ' OR ';
								}
								else
								{
									$wildcard .= $this->db->quoteName('#__virtuemart_products.product_sku') . ' NOT LIKE ' . $this->db->quote($sku) . ' OR ';
								}
							}
							else
							{
								$normal[] = $this->db->quote($sku);
							}
						}
					}

					if (substr($wildcard, -3) === 'OR ')
					{
						$wildcard = substr($wildcard, 0, -4);
					}

					// If sku filter is include look for matching records, else exclude matching records
					if ($inclproductskufilter)
					{
						if (!empty($wildcard) && !empty($normal))
						{
							$query->where('(' . $wildcard . ' OR ' . $this->db->quoteName('#__virtuemart_products.product_sku') . ' IN (' . implode(',', $normal) . '))');
						}
						elseif (!empty($wildcard))
						{
							$query->where('(' . $wildcard . ')');
						}
						elseif (!empty($normal))
						{
							$query->where('(' . $this->db->quoteName('#__virtuemart_products.product_sku') . ' IN (' . implode(',', $normal) . '))');
						}
					}
					else
					{
						if (!empty($wildcard) && !empty($normal))
						{
							$query->where(
								'(' . $wildcard . ' OR ' . $this->db->quoteName('#__virtuemart_products.product_sku') . ' NOT IN (' . implode(',', $normal) . '))'
							);
						}
						elseif (!empty($wildcard))
						{
							$query->where('(' . $wildcard . ')');
						}
						elseif (!empty($normal))
						{
							$query->where('(' . $this->db->quoteName('#__virtuemart_products.product_sku') . ' NOT IN (' . implode(',', $normal) . '))');
						}
					}
				}
			}

			// Filter on price shopper group
			$shopper_group_price = $this->template->get('shopper_group_price', array());

			if ($shopper_group_price)
			{
				if ($shopper_group_price === '*')
				{
					$query->where(
						'('
						. $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id')
						. ' = ' . $this->db->quote(0)
						. ' OR ' . $this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id') . ' IS NULL)'
					);
				}
				elseif ($shopper_group_price !== 'none')
				{
					$query->where($this->db->quoteName('#__virtuemart_product_prices.virtuemart_shoppergroup_id') . ' = ' . $this->db->quote($shopper_group_price));
				}
			}

			// Filter on product quantities
			$price_quantity_start = $this->template->get('price_quantity_start', null);

			if ($price_quantity_start >= 0 && $price_quantity_start !== null)
			{
				$query->where($this->db->quoteName('#__virtuemart_product_prices.price_quantity_start') . ' = ' . $this->db->quote($price_quantity_start));
			}

			$price_quantity_end = $this->template->get('price_quantity_end', null);

			if ($price_quantity_end >= 0 && $price_quantity_end !== null)
			{
				$query->where($this->db->quoteName('#__virtuemart_product_prices.price_quantity_end') . ' = ' . $this->db->quote($price_quantity_end));
			}

			// Filter on price from
			$priceoperator = $this->template->get('priceoperator', 'gt');
			$pricefrom     = $this->template->get('pricefrom', false);
			$priceto       = $this->template->get('priceto', 0, 'float');

			if ($pricefrom !== false)
			{
				switch ($priceoperator)
				{
					case 'gt':
						$query->where(
							'ROUND('
							. $this->db->quoteName('#__virtuemart_product_prices.product_price') . ', '
							. $this->template->get('export_price_format_decimal', 2, 'int') . ') > ' . $pricefrom
						);
						break;
					case 'eq':
						$query->where(
							'ROUND('
							. $this->db->quoteName('#__virtuemart_product_prices.product_price') . ', '
							. $this->template->get('export_price_format_decimal', 2, 'int') . ') = ' . $pricefrom
						);
						break;
					case 'lt':
						$query->where(
							'ROUND('
							. $this->db->quoteName('#__virtuemart_product_prices.product_price') . ', '
							. $this->template->get('export_price_format_decimal', 2, 'int') . ') < ' . $pricefrom
						);
						break;
					case 'bt':
						$query->where(
							'ROUND('
							. $this->db->quoteName('#__virtuemart_product_prices.product_price') . ', '
							. $this->template->get('export_price_format_decimal', 2, 'int') . ') BETWEEN ' . $pricefrom . ' AND ' . $priceto
						);
						break;
				}
			}

			// Filter by stocklevel start
			$stocklevelstart = $this->template->get('stocklevelstart', 'none', 'cmd');

			if ($stocklevelstart !== 'none')
			{
				$stocklevelstart = (int) $stocklevelstart;
				$query->where('#__virtuemart_products.product_in_stock >= ' . $stocklevelstart);
			}

			// Filter by stocklevel end
			$stocklevelend = $this->template->get('stocklevelend', 'none', 'cmd');

			if ($stocklevelend !== 'none' || ($stocklevelend === 0 && $stocklevelstart === 0))
			{
				$stocklevelend = (int) $stocklevelend;
				$query->where('#__virtuemart_products.product_in_stock <= ' . $stocklevelend);
			}

			// Filter by shopper group id
			$shopper_group = $this->template->get('shopper_groups', array());

			$shopper_group = ArrayHelper::toInteger($shopper_group);

			if ($shopper_group && 0 !== $shopper_group[0] && 0 !== count($shopper_group))
			{
				$query->where("#__virtuemart_product_shoppergroups.virtuemart_shoppergroup_id IN ('" . implode("','", $shopper_group) . "')");
			}

			// Filter by manufacturer
			$manufacturer = $this->template->get('manufacturers', array());

			$manufacturer = ArrayHelper::toInteger($manufacturer);

			if ($manufacturer && 0 !== $manufacturer[0] && 0 !== count($manufacturer))
			{
				$query->where("#__virtuemart_manufacturers.virtuemart_manufacturer_id IN ('" . implode("','", $manufacturer) . "')");
			}

			// Group the fields
			$groupBy = array_unique($groupBy);

			if (!empty($groupBy))
			{
				$query->group($groupBy);
			}
			elseif ($groupByDefault)
			{
				$query->group('#__virtuemart_products.virtuemart_product_id');
			}

			// Sort set fields
			$sortBy = array_unique($sortBy);

			if (!empty($sortBy))
			{
				$query->order($sortBy);
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

					// Reset the prices
					$this->prices = array();

					// Process all the export fields
					foreach ($exportFields as $field)
					{
						// Get the field name
						$fieldname     = $field->field_name;
						$fieldValue    = '';

						// Set the field value
						if (isset($record->$fieldname))
						{
							$fieldValue = $record->$fieldname;
						}

						// Process the field
						switch ($fieldname)
						{
							case 'category_id':
								$fieldValue = trim($this->helper->createCategoryPath($record->virtuemart_product_id, true));

								// Get the parent value if child has no category set
								if (!$fieldValue && $record->product_parent_id > 0)
								{
									$fieldValue = trim($this->helper->createCategoryPath($record->product_parent_id, true));
								}

								break;
							case 'category_path':
								$fieldValue = trim($this->helper->createCategoryPath($record->virtuemart_product_id));

								// Get the parent value if child has no category set
								if (!$fieldValue && $record->product_parent_id > 0)
								{
									$fieldValue = trim($this->helper->createCategoryPath($record->product_parent_id));
								}
								break;
							case 'category_name':
								$category_path = trim($this->helper->createCategoryPathById($record->virtuemart_category_id));

								// Get the parent value if child has no category set
								if (!$category_path && $record->product_parent_id > 0)
								{
									$parentCategoryId = $this->helper->getCategoryId($record->product_parent_id);

									if ($parentCategoryId)
									{
										$parentCategoryId = (array) $parentCategoryId;
									}

									$category_path = trim($this->helper->createCategoryPathById($parentCategoryId));
								}

								$names      = explode($this->template->get('category_separator', '/'), $category_path);
								$fieldValue = array_pop($names);

								break;
							case 'product_name':
							case 'product_s_desc':
							case 'product_desc':
							case 'metadesc':
							case 'metakey':
							case 'slug':
							case 'customtitle':
								$query = $this->db->getQuery(true)
									->select(
										$this->db->quoteName(
											array(
												$fieldname,
												'virtuemart_product_id'
											)
										)
									)
									->from($this->db->quoteName('#__virtuemart_products_' . $language))
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id, 'OR');

								if ($record->product_parent_id > 0)
								{
									$query->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->product_parent_id);
								}

								$this->db->setQuery($query);
								$translation = $this->db->loadAssocList('virtuemart_product_id');
								$fieldValue  = '';

								if (!empty($translation[$record->virtuemart_product_id][$fieldname]))
								{
									$fieldValue = $translation[$record->virtuemart_product_id][$fieldname];
								}
								elseif (!empty($translation[$record->product_parent_id][$fieldname]))
								{
									$fieldValue = $translation[$record->product_parent_id][$fieldname];
								}

								if ($fieldname === 'product_name')
								{
									$fieldValue = html_entity_decode($fieldValue, ENT_QUOTES, "UTF-8");
								}
								break;
							case 'picture_url':
							case 'picture_url_thumb':
								$query = $this->db->getQuery(true);

								if ($fieldname === 'picture_url_thumb')
								{
									$query->select(
										'CASE WHEN ' . $this->db->quoteName('file_url_thumb') . ' = ' . $this->db->quote('') . '
											THEN CONCAT(' . $this->db->quoteName('file_url') . ',' . $this->db->quote('-_-') . ')
											ELSE ' . $this->db->quoteName('file_url_thumb') . ' END');
								}
								else
								{
									$query->select($this->db->quoteName('file_url'));
								}

								$query->from($this->db->quoteName('#__virtuemart_medias'));
								$query->leftJoin(
									$this->db->quoteName('#__virtuemart_product_medias')
									. ' ON ' . $this->db->quoteName('#__virtuemart_product_medias.virtuemart_media_id')
									. ' = ' . $this->db->quoteName('#__virtuemart_medias.virtuemart_media_id')
								);
								$query->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);
								$query->where($this->db->quoteName('file_mimetype') . ' LIKE ' . $this->db->quote('image/%'));
								$query->order($this->db->quoteName('#__virtuemart_product_medias.ordering'));
								$this->db->setQuery($query, 0, $this->template->get('picture_limit', 1));
								$images = $this->db->loadColumn();

								// If child product has no image get parent product image
								if (count($images) == 0)
								{
									$images = $this->getParentImage($record->product_parent_id, $fieldname);
								}

								foreach ($images as $i => $image)
								{
									// Check if we need to create a dynamic image name
									if (substr($image, -3) === '-_-')
									{
										$width  = $this->helperConfig->get('img_width');
										$height = $this->helperConfig->get('img_height');

										// Remove marker
										$image = substr($image, 0, -3);

										// Construct the new image name
										$image = dirname($image)
											. '/resized/'
											. basename(\JFile::stripExt($image))
											. '_' . $width . 'x' . $height . '.'
											. \JFile::getExt($image);
									}

									$images[$i] = $this->domainName . '/' . $image;
								}

								// Check if there is already a product full image
								$fieldValue = implode(',', $images);
								break;
							case 'product_parent_sku':
								$query = $this->db->getQuery(true);
								$query->select('product_sku');
								$query->from('#__virtuemart_products');
								$query->where('virtuemart_product_id = ' . $record->product_parent_id);
								$this->db->setQuery($query);

								$fieldValue = $this->db->loadResult();
								break;
							case 'related_products':
								// Get the custom ID
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('#__virtuemart_products.product_sku'))
									->from($this->db->quoteName('#__virtuemart_product_customfields'))
									->leftJoin(
										$this->db->quoteName('#__virtuemart_customs')
										. ' ON ' . $this->db->quoteName('#__virtuemart_customs.virtuemart_custom_id')
										. ' = ' . $this->db->quoteName('#__virtuemart_product_customfields.virtuemart_custom_id')
									)
									->leftJoin(
										$this->db->quoteName('#__virtuemart_products')
										. ' ON ' . $this->db->quoteName('#__virtuemart_products.virtuemart_product_id')
										. ' = ' . $this->db->quoteName('#__virtuemart_product_customfields.customfield_value')
									)
									->where($this->db->quoteName('#__virtuemart_customs.field_type') . ' = ' . $this->db->quote('R'))
									->where(
										$this->db->quoteName('#__virtuemart_product_customfields.virtuemart_product_id')
										. ' = ' . $this->db->quote($record->virtuemart_product_id)
									)
									->group($this->db->quoteName('#__virtuemart_products.product_sku'));
								$this->db->setQuery($query);
								$related_records = $this->db->loadColumn();

								if (is_array($related_records))
								{
									$fieldValue = implode('|', $related_records);
								}
								else
								{
									$fieldValue = '';
								}
								break;
							case 'related_categories':
								// Get the custom ID
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('#__virtuemart_product_customfields.customfield_value', 'custom_value'))
									->from($this->db->quoteName('#__virtuemart_product_customfields'))
									->leftJoin(
										$this->db->quoteName('#__virtuemart_customs')
										. ' ON ' . $this->db->quoteName('#__virtuemart_customs.virtuemart_custom_id')
										. ' = ' . $this->db->quoteName('#__virtuemart_product_customfields.virtuemart_custom_id')
									)
									->where($this->db->quoteName('#__virtuemart_customs.field_type') . ' = ' . $this->db->quote('Z'))
									->where(
										$this->db->quoteName('#__virtuemart_product_customfields.virtuemart_product_id')
										. ' = ' . $this->db->quote($record->virtuemart_product_id)
									)
									->group($this->db->quoteName('#__virtuemart_product_customfields.virtuemart_customfield_id'));
								$this->db->setQuery($query);
								$related_records = $this->db->loadColumn();

								if (is_array($related_records))
								{
									$fieldValue = $this->helper->createCategoryPathById($related_records);
								}
								else
								{
									$fieldValue = '';
								}
								break;
							case 'product_available_date':
							case 'created_on':
							case 'modified_on':
							case 'locked_on':
								$fieldValue = $this->fields->getDateFormat($fieldname, $record->$fieldname, $field->column_header);
							break;
							case 'product_box':
								if (strpos($record->product_params, '|'))
								{
									$params = explode('|', $record->product_params);

									foreach ($params as $param)
									{
										if ($param)
										{
											list($param_name, $param_value) = explode('=', $param);

											if ($param_name == $fieldname)
											{
												$fieldValue = str_replace('"', '', $param_value);
											}
										}
									}
								}
								else
								{
									$fieldValue = '';
								}
								break;
							case 'product_price':
								$fieldValue = $this->convertPrice($record->product_price, $record->currency_code_3);
								$fieldValue = $this->formatNumber($fieldValue);

								if (strlen(trim($fieldValue)) == 0)
								{
									$fieldValue = $field->default_value;
								}

								if ($this->template->get('add_currency_to_price'))
								{
									if ($this->template->get('targetcurrency') != '')
									{
										$fieldValue = $this->template->get('targetcurrency') . ' ' . $fieldValue;
									}
									else
									{
										$fieldValue = $record->currency_code_3 . ' ' . $fieldValue;
									}
								}
								break;
							case 'product_override_price':
								$fieldValue = $this->convertPrice($record->product_override_price, $record->currency_code_3);
								$fieldValue = $this->formatNumber($fieldValue);

								if (trim($fieldValue) === '')
								{
									$fieldValue = $field->default_value;
								}

								if ($this->template->get('add_currency_to_price'))
								{
									if ($this->template->get('targetcurrency') !== '')
									{
										$fieldValue = $this->template->get('targetcurrency') . ' ' . $fieldValue;
									}
									else
									{
										$fieldValue = $record->currency_code_3 . ' ' . $fieldValue;
									}
								}
								break;
							case 'product_url':
								// Check if there is already a product URL
								if (is_null($record->product_url) || strlen(trim($record->product_url)) == 0)
								{
									$fieldValue = '';

									// Get the category id
									// Check to see if we have a child product
									$category_id = $this->helper->getCategoryId($record->virtuemart_product_id);

									if ($category_id == 0 && $record->product_parent_id > 0)
									{
										$category_id = $this->helper->getCategoryId($record->product_parent_id);
									}

									if ($category_id > 0)
									{
										// Let's create a SEF URL
										$url = 'option=com_virtuemart&view=productdetails&' .
											'virtuemart_product_id=' . $record->virtuemart_product_id .
											'&virtuemart_category_id=' . $category_id .
											'&Itemid=' . $this->template->get('vm_itemid', 1, 'int');

										$fieldValue = $this->sef->getSefUrl('index.php?' . $url);
									}
								}
								// There is a product URL, use it
								else
								{
									$fieldValue = $record->product_url;
								}

								// Add the suffix
								if (!empty($fieldValue))
								{
									$fieldValue .= $this->template->get('producturl_suffix');
								}
								break;
							case 'basepricewithtax':
							case 'discountedpricewithouttax':
							case 'pricebeforetax':
							case 'salesprice':
							case 'taxamount':
							case 'discountamount':
							case 'pricewithouttax':
								// Get the quantity
								$quantity = $useQuantity ? $record->price_quantity_start : 1;
								$prices   = $this->getProductPrice($record->virtuemart_product_id, $record->virtuemart_shoppergroup_id, $quantity);

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

								// Check if the currency needs to be added
								if ($this->template->get('add_currency_to_price'))
								{
									$fieldValue = $record->currency_code_3 . ' ' . $fieldValue;
								}

								// Export the data
								break;
							case 'product_currency':
								$fieldValue = $record->currency_code_3;

								// Check if we have any content otherwise use the default value
								if ($this->template->get('targetcurrency') !== '')
								{
									$fieldValue = $this->template->get('targetcurrency');
								}
								break;
							case 'manufacturer_name':
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('mf_name'))
									->from($this->db->quoteName('#__virtuemart_manufacturers_' . $language))
									->leftJoin(
										$this->db->quoteName('#__virtuemart_product_manufacturers')
										. ' ON ' . $this->db->quoteName('#__virtuemart_product_manufacturers.virtuemart_manufacturer_id')
										. ' = ' . $this->db->quoteName('#__virtuemart_manufacturers_' . $language . '.virtuemart_manufacturer_id')
									)
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);
								$this->db->setQuery($query);
								$fieldValue = implode('|', $this->db->loadColumn());
								break;
							case 'custom_title':
								// Get the custom title
								$query = $this->db->getQuery(true);
								$query->select($this->db->quoteName('custom_title'));
								$query->from($this->db->quoteName('#__virtuemart_customs', 'c'));
								$query->leftJoin($this->db->quoteName('#__virtuemart_product_customfields', 'f') . ' ON c.virtuemart_custom_id = f.virtuemart_custom_id');
								$query->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);
								$query->where($this->db->quoteName('field_type') . ' NOT IN (' . $this->db->quote('R') . ', ' . $this->db->quote('Z') . ', ' . $this->db->quote('G') . ')');

								// Check if we need to filter
								$title_filter = $this->template->get('custom_title', array(), 'array');

								if (!empty($title_filter) && $title_filter[0] !== '')
								{
									$query->where($this->db->quoteName('f.virtuemart_custom_id') . ' IN (' . implode(',', $title_filter) . ')');
								}

								$query->order(
									array(
										$this->db->quoteName('f.ordering'),
										$this->db->quoteName('f.virtuemart_custom_id')
									)
								);
								$this->db->setQuery($query);
								$titles = $this->db->loadColumn();

								if (is_array($titles))
								{
									$fieldValue = implode('~', $titles);
								}
								else
								{
									$fieldValue = '';
								}
								break;
							case 'custom_value':
							case 'custom_price':
							case 'custom_param':
							case 'custom_ordering':
								// Do some field sanity check if needed
								if ($fieldname !== 'custom_ordering')
								{
									$fieldname = str_ireplace(array('custom_', '_param'), array('customfield_', '_params'), $fieldname);
								}

								if (!isset($this->customFields[$record->virtuemart_product_id][$fieldname]))
								{
									if ($fieldname === 'custom_ordering')
									{
										$qfield = $this->db->quoteName('cf.ordering', 'custom_ordering');
									}
									else
									{
										$qfield = $this->db->quoteName($fieldname);
									}

									$query = $this->db->getQuery(true)
										->select($qfield)
										->select(
											$this->db->quoteName(
												array(
													'cf.virtuemart_customfield_id',
													'cf.virtuemart_custom_id',
													'cf.customfield_params',
													'c.field_type',
													'c.custom_element',
												)
											)
										)
										->from($this->db->quoteName('#__virtuemart_product_customfields', 'cf'))
										->leftJoin(
											$this->db->quoteName('#__virtuemart_customs', 'c')
											. ' ON ' . $this->db->quoteName('c.virtuemart_custom_id') . ' = ' . $this->db->quoteName('cf.virtuemart_custom_id')
										)
										->where($this->db->quoteName('cf.virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);

									// Check if we need to filter
									$title_filter = $this->template->get('custom_title', array());

									if (!empty($title_filter) && $title_filter[0] != '')
									{
										$query->where($this->db->quoteName('cf.virtuemart_custom_id') . ' IN (' . implode(',', $title_filter) . ')');
									}

									$query->order(
										array(
											$this->db->quoteName('cf.ordering'),
											$this->db->quoteName('cf.virtuemart_custom_id')
										)
									);
									$this->db->setQuery($query);
									$customfields = $this->db->loadObjectList();
									$this->log->add('Custom field query');

									if (!empty($customfields))
									{
										$values = array();

										foreach ($customfields as $customfield)
										{
											// Check for groups, we don't need them as they get handled automatically
											if ($customfield->field_type !== 'G')
											{
												if ($fieldname === 'customfield_params' && $customfield->field_type !== 'C')
												{
													// Fire the plugin to empty any values needed
													$result = $dispatcher->trigger(
														'exportCustomValues',
														array(
															'plugin'                    => $customfield->custom_element,
															'custom_param'              => $customfield->customfield_params,
															'virtuemart_product_id'     => $record->virtuemart_product_id,
															'virtuemart_custom_id'      => $customfield->virtuemart_custom_id,
															'virtuemart_customfield_id' => $customfield->virtuemart_customfield_id,
															'log'                       => $this->log
														)
													);

													if (is_array($result) && !empty($result))
													{
														$values = array_merge($values, $result[0]);
													}
													else
													{
														// Create the CSVI format
														// option1[value1#value2;option2[value1#value2
														$values[] = $customfield->customfield_params;
													}
												}
												else
												{
													if ($customfield->$fieldname !== false && !is_null($customfield->$fieldname))
													{
														$fieldValue = $customfield->$fieldname;

														// Apply currency formatting
														if ($fieldname === 'customfield_price')
														{
															$fieldValue = $this->formatNumber($customfield->$fieldname);
														}

														$values[] = $fieldValue;
													}
													else
													{
														$values[] = '';
													}
												}
											}
										}

										$this->customFields[$record->virtuemart_product_id][$fieldname] = $values;
										$fieldValue                                                     = implode('~', $this->customFields[$record->virtuemart_product_id][$fieldname]);
									}
									else
									{
										$fieldValue = '';
									}
								}
								else
								{
									$fieldValue = implode('~', $this->customFields[$record->virtuemart_product_id][$fieldname]);
								}
								break;
							case 'custom_override':
							case 'custom_disabler':
								$query = $this->db->getQuery(true);
								$query->select(
									array(
										$this->db->quoteName('cf.override', 'custom_override'),
										$this->db->quoteName('cf.disabler', 'custom_disabler')
									)
								);
								$query->from($this->db->quoteName('#__virtuemart_customs', 'c'));
								$query->leftJoin($this->db->quoteName('#__virtuemart_product_customfields', 'cf') . ' ON c.virtuemart_custom_id = cf.virtuemart_custom_id');
								$query->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);

								// Check if we need to filter
								$title_filter = $this->template->get('custom_title', array(), 'array');

								if (!empty($title_filter) && $title_filter[0] != '')
								{
									$query->where($this->db->quoteName('cf.virtuemart_custom_id') . ' IN (' . implode(',', $title_filter) . ')');
								}

								$query->order($this->db->quoteName('cf.ordering'))
									->order($this->db->quoteName('cf.virtuemart_custom_id'));
								$this->db->setQuery($query);
								$customfields = $this->db->loadObjectList();

								if (!empty($customfields))
								{
									$values = array();

									foreach ($customfields as $customfield)
									{
										if (in_array($fieldname, array('custom_disabler', 'custom_override')))
										{
											$fieldValue = 'N';

											// If the customfield parent id is set then value is Y
											if ($customfield->$fieldname > 0)
											{
												$fieldValue = 'Y';
											}

											$values[] = $fieldValue;
										}
									}

									$this->customFields[$record->virtuemart_product_id][$fieldname] = $values;
									$fieldValue                                                     = implode('~', $this->customFields[$record->virtuemart_product_id][$fieldname]);
								}
								else
								{
									$fieldValue = '';
								}

								break;
							case 'file_url_thumb':
								$fieldValue = '';
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName(array('file_url', 'file_url_thumb')))
									->from($this->db->quoteName('#__virtuemart_medias', 'm'))
									->leftJoin(
										$this->db->quoteName('#__virtuemart_product_medias', 'p')
										. ' ON ' . $this->db->quoteName('m.virtuemart_media_id') . ' = ' . $this->db->quoteName('p.virtuemart_media_id')
									)
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id)
									->where($this->db->quoteName('file_type') . ' = ' . $this->db->quote('product'))
									->order('p.ordering');
								$this->db->setQuery($query);
								$images = $this->db->loadObject();

								if ($images && empty($images->file_url_thumb))
								{
									$imageSettings = $this->helperConfig->get('img_resize_enable');

									if ($imageSettings)
									{
										$imageWidth    = $this->helperConfig->get('img_width');
										$imageHeight   = $this->helperConfig->get('img_height');
										$imageDetails  = pathinfo($images->file_url);
										$imageFileName = $imageDetails['filename'];
										$extension     = $imageDetails['extension'];
										$thumbnailName = $imageFileName . '_' . $imageWidth . 'x' . $imageHeight . '.' . $extension;
										$fieldValue    = $imageDetails['dirname'] . '/resized/' . $thumbnailName;
									}
								}

								break;
							case 'file_url':
							case 'file_title':
							case 'file_description':
							case 'file_meta':
							case 'file_lang':
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName($fieldname))
									->from($this->db->quoteName('#__virtuemart_medias', 'm'))
									->leftJoin(
										$this->db->quoteName('#__virtuemart_product_medias', 'p')
										. ' ON ' . $this->db->quoteName('m.virtuemart_media_id') . ' = ' . $this->db->quoteName('p.virtuemart_media_id')
									)
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id)
									->where($this->db->quoteName('file_type') . ' = ' . $this->db->quote('product'))
									->order('p.ordering');
								$this->db->setQuery($query);
								$titles = $this->db->loadColumn();
								$this->log->add('Looking for product images');

								$fieldValue = '';

								if (is_array($titles))
								{
									$fieldValue = implode('|', $titles);
								}

								$this->log->add('Found images: ' . $fieldValue);
								break;
							case 'file_ordering':
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('p.ordering'))
									->from($this->db->quoteName('#__virtuemart_medias', 'm'))
									->leftJoin($this->db->quoteName('#__virtuemart_product_medias', 'p') . ' ON m.virtuemart_media_id = p.virtuemart_media_id')
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id)
									->where($this->db->quoteName('file_type') . ' = ' . $this->db->quote('product'))
									->order('p.ordering');
								$this->db->setQuery($query);
								$titles = $this->db->loadColumn();

								if (is_array($titles))
								{
									$fieldValue = implode('|', $titles);
								}
								else
								{
									$fieldValue = '';
								}
								break;
							case 'min_order_level':
							case 'max_order_level':
							case 'step_order_level':
								if (strpos($record->product_params, '|'))
								{
									$params = explode('|', $record->product_params);

									foreach ($params as $param)
									{
										if ($param)
										{
											list($param_name, $param_value) = explode('=', $param);

											if ($param_name == $fieldname)
											{
												$fieldValue = str_replace('"', '', $param_value);
											}
										}
									}
								}
								else
								{
									$fieldValue = '';
								}
								break;
							case 'shopper_group_name':
								$fieldValue = '';
								$query      = $this->db->getQuery(true)
									->select($this->db->quoteName($fieldname))
									->from($this->db->quoteName('#__virtuemart_shoppergroups', 'g'))
									->leftJoin(
										$this->db->quoteName('#__virtuemart_product_shoppergroups', 'p') .
										' ON g.virtuemart_shoppergroup_id = p.virtuemart_shoppergroup_id'
									)
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . $this->db->quote($record->virtuemart_product_id));
								$this->db->setQuery($query);
								$this->log->add('Get shopper group', true);
								$titles        = $this->db->loadColumn();
								$shopperGroups = array();

								if (is_array($titles))
								{
									foreach ($titles as $title)
									{
										$shopperGroups[] = \JText::_($title);
									}

									$fieldValue = implode('|', $shopperGroups);
								}

								break;
							case 'shopper_group_name_price':
								if ($record->virtuemart_shoppergroup_id > 0)
								{
									$query = $this->db->getQuery(true)
										->select($this->db->quoteName('shopper_group_name'))
										->from($this->db->quoteName('#__virtuemart_shoppergroups', 'g'))
										->where($this->db->quoteName('virtuemart_shoppergroup_id') . ' = ' . $this->db->quote($record->virtuemart_shoppergroup_id));
									$this->db->setQuery($query);
									$this->log->add('Get price shopper group', true);
									$fieldValue = $this->db->loadResult();
								}
								else
								{
									$fieldValue = '*';
								}
								break;
							case 'custom':
								if (strlen(trim($fieldValue)) == 0)
								{
									$fieldValue = $field->default_value;
								}
								break;
							case 'product_discount':
								$query = $this->db->getQuery(true);
								$query->select('calc_value_mathop, calc_value');
								$query->from($this->db->quoteName('#__virtuemart_calcs', 'c'));
								$query->where($this->db->quoteName('virtuemart_calc_id') . ' = ' . $this->db->quote($record->product_discount_id));
								$this->db->setQuery($query);
								$discount = $this->db->loadObject();

								if (is_object($discount))
								{
									$fieldValue = $this->formatNumber($discount->calc_value);

									if (strpos($discount->calc_value_mathop, '%') !== false)
									{
										$fieldValue .= '%';
									}
								}
								else
								{
									$fieldValue = '';
								}
								break;
							case 'product_attribute':
								$options = json_decode($fieldValue);

								$values = array();

								if (is_object($options))
								{
									foreach ($options as $option)
									{
										if (is_array($option) || is_object($option))
										{
											foreach ($option as $option_type => $value)
											{
												if (is_array($value))
												{
													foreach ($value as $option_field => $text)
													{
														$values[] = $text;
													}
												}
												else
												{
													// Get the value from the product custom fields
													$query = $this->db->getQuery(true)
														->select($this->db->quoteName('customfield_value'))
														->from($this->db->quoteName('#__virtuemart_product_customfields'))
														->where($this->db->quoteName('virtuemart_customfield_id') . ' = ' . (int) $option_type);
													$this->db->setQuery($query);
													$values[] = $this->db->loadResult();
												}
											}
										}
										else
										{
											// Get the value from the product custom fields
											$query = $this->db->getQuery(true)
												->select($this->db->quoteName('customfield_value'))
												->from($this->db->quoteName('#__virtuemart_product_customfields'))
												->where($this->db->quoteName('virtuemart_customfield_id') . ' = ' . (int) $option);
											$this->db->setQuery($query);
											$values[] = $this->db->loadResult();
										}
									}

									$fieldValue = implode('|', $values);
								}

								if (strlen(trim($fieldValue)) == 0)
								{
									$fieldValue = $field->default_value;
								}
								break;
							default:
								// See if we need to retrieve a custom field
								if (in_array($fieldname, $this->customFieldsExport))
								{
									$query = $this->db->getQuery(true)
										->select($this->db->quoteName(array('p.customfield_value', 'c.field_type')))
										->from($this->db->quoteName('#__virtuemart_product_customfields', 'p'))
										->leftJoin(
											$this->db->quoteName('#__virtuemart_customs', 'c')
											. ' ON ' . $this->db->quoteName('p.virtuemart_custom_id') . ' = ' . $this->db->quoteName('c.virtuemart_custom_id')
										)
										->where($this->db->quoteName('c.custom_title') . ' = ' . $this->db->quote($fieldname))
										->where($this->db->quoteName('p.virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);
									$this->db->setQuery($query);

									$customValue = $this->db->loadObject();

									if ($customValue)
									{
										$fieldValue = $customValue->customfield_value;

										// Check if we are exporting media custom field id
										if ($customValue->field_type === 'M')
										{
											$query->clear()
												->select($this->db->quoteName('file_url'))
												->from($this->db->quoteName('#__virtuemart_medias'))
												->where($this->db->quoteName('virtuemart_media_id') . ' = ' . (int) $customValue->customfield_value);
											$this->db->setQuery($query);
											$fieldValue = $this->db->loadResult();
										}
									}
								}
								elseif (in_array($fieldname, $this->multiVariantExport))
								{
									$fieldValue = $this->getMultivariantValue($record, $fieldname);
								}
								elseif (in_array($fieldname, $this->multiLangFields))
								{
									$values = array();

									$language = substr($fieldname, strpos($fieldname, "_") + 7);
									$query    = $this->db->getQuery(true)
										->select(
											$this->db->quoteName(
												array(
													'cf.virtuemart_customfield_id',
													'cf.virtuemart_custom_id',
													'cf.customfield_params',
													'c.field_type',
													'c.custom_element',
												)
											)
										)
										->from($this->db->quoteName('#__virtuemart_product_customfields', 'cf'))
										->leftJoin(
											$this->db->quoteName('#__virtuemart_customs', 'c')
											. ' ON ' . $this->db->quoteName('c.virtuemart_custom_id') . ' = ' . $this->db->quoteName('cf.virtuemart_custom_id')
										)
										->where($this->db->quoteName('cf.virtuemart_product_id') . ' = ' . (int) $record->virtuemart_product_id);

									// Check if we need to filter
									$title_filter = $this->template->get('custom_title', array());

									if (!empty($title_filter) && $title_filter[0] != '')
									{
										$query->where($this->db->quoteName('cf.virtuemart_custom_id') . ' IN (' . implode(',', $title_filter) . ')');
									}

									$query->order(
										array(
											$this->db->quoteName('cf.ordering'),
											$this->db->quoteName('cf.virtuemart_custom_id')
										)
									);
									$this->db->setQuery($query);
									$customfields = $this->db->loadObjectList();

									foreach ($customfields as $customfield)
									{
										$result = $dispatcher->trigger(
											'exportCustomValues',
											array(
												'plugin'                    => $customfield->custom_element,
												'custom_param'              => $customfield->customfield_params,
												'virtuemart_product_id'     => $record->virtuemart_product_id,
												'virtuemart_custom_id'      => $customfield->virtuemart_custom_id,
												'virtuemart_customfield_id' => $customfield->virtuemart_customfield_id,
												'log'                       => $this->log,
												'lang'                      => $language
											)
										);

										if (is_array($result) && !empty($result))
										{
											$values = array_merge($values, $result[0]);
										}
									}

									$fieldValue = implode('~', $values);
								}

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
	 * Convert prices to the new currency.
	 *
	 * @param   float  $product_price    The price to convert
	 * @param   string $product_currency The currency to convert to
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
	 * Get product prices.
	 *
	 * @param   int $product_id                 The ID of the product.
	 * @param   int $virtuemart_shoppergroup_id The ID of the price shopper group.
	 * @param   int $quantity                   The quantity to use for the prices.
	 *
	 * @return array List of prices.
	 *
	 * @since   4.0
	 */
	private function getProductPrice($product_id, $virtuemart_shoppergroup_id, $quantity = 1)
	{
		$sid = $virtuemart_shoppergroup_id;

		if (!isset($this->prices[$sid][$product_id]))
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
			$force           = $this->template->get('force_shopper_group_price', 'none');
			$sid             = $virtuemart_shoppergroup_id;
			$setShopperGroup = true;

			if ($force !== 'none')
			{
				// Force the shopper group ID
				$virtuemart_shoppergroup_id = (int) $force;

				// Set a shopper group identifier for caching prices
				$sid = $virtuemart_shoppergroup_id;

				// Make the shopper groups an array if it is not 0, so the selected shopper groups are used
				if ($virtuemart_shoppergroup_id !== 0)
				{
					$virtuemart_shoppergroup_id = (array) $virtuemart_shoppergroup_id;
				}
				else
				{
					$setShopperGroup = false;
				}
			}
			else
			{
				$virtuemart_shoppergroup_id = (array) $virtuemart_shoppergroup_id;
			}

			// Set the shopper group
			if ($setShopperGroup)
			{
				$calc->setShopperGroup($virtuemart_shoppergroup_id);
			}

			$this->log->add('Use shopper group ID: ' . $sid);

			// Load the product helper
			require_once JPATH_ADMINISTRATOR . '/components/com_virtuemart/models/product.php';
			$product = new \VirtueMartModelProduct;

			// Load the product info
			$product = $product->getProductSingle($product_id, true, $quantity, true, $virtuemart_shoppergroup_id);

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
			$prices = $calc->getProductPrices($product);

			if (is_array($prices))
			{
				$this->prices[$sid][$product_id] = array_change_key_case($prices, CASE_LOWER);
			}
			else
			{
				$this->prices[$sid][$product_id] = array();
			}
		}

		return $this->prices[$sid][$product_id];
	}

	/**
	 * Get a list of custom fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   4.4.1
	 *
	 * @throws  \RuntimeException
	 */
	private function loadCustomFields()
	{
		$query = $this->db->getQuery(true);
		$query->select('TRIM(' . $this->db->quoteName('custom_title') . ') AS ' . $this->db->quoteName('title'));
		$query->from($this->db->quoteName('#__virtuemart_customs'));
		$query->where(
			$this->db->quoteName('field_type') . ' IN ('
			. $this->db->quote('S') . ','
			. $this->db->quote('I') . ','
			. $this->db->quote('B') . ','
			. $this->db->quote('D') . ','
			. $this->db->quote('T') . ','
			. $this->db->quote('M') . ','
			. $this->db->quote('Y') . ','
			. $this->db->quote('X') .
			')'
		);
		$this->db->setQuery($query);
		$result = $this->db->loadColumn();

		if (!is_array($result))
		{
			$result = array();
		}

		$this->customFieldsExport = $result;
	}

	/**
	 * Get a list of multi variant fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 *
	 * @throws \RuntimeException
	 */
	private function loadMultiVariantFields()
	{
		// Use the ramifications of the Multi Variant plugin as regular fields
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('cf.customfield_params'))
			->from($this->db->quoteName('#__virtuemart_customs', 'c'))
			->leftJoin(
				$this->db->quoteName('#__virtuemart_product_customfields', 'cf')
				. ' ON ' . $this->db->quoteName('cf.virtuemart_custom_id') . ' = ' . $this->db->quoteName('c.virtuemart_custom_id')
			)
			->where($this->db->quoteName('c.field_type') . ' = ' . $this->db->quote('C'));
		$this->db->setQuery($query);

		$params = $this->db->loadColumn();

		foreach ($params as $param)
		{
			// Get the different segments
			$segments = explode('|', $param);

			// Process each segment
			foreach ($segments as $segment)
			{
				if ($firstpos = stripos($segment, '='))
				{
					// Get the values
					$group = substr($segment, 0, $firstpos);
					$value = substr($segment, $firstpos + 1);

					if ($group === 'selectoptions')
					{
						$values = json_decode($value);

						if (!empty($values))
						{
							foreach ($values as $ramification)
							{
								$csvi_name = trim($ramification->voption);

								if ($ramification->voption === 'clabels')
								{
									$csvi_name = trim($ramification->clabel);
								}

								$this->multiVariantExport[] = $csvi_name;
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Get the multi variant value.
	 *
	 * @param   object $record    The current record with data.
	 * @param   string $fieldname The name of the field to get the value for.
	 *
	 * @return string The value found.
	 *
	 * @since   6.0
	 *
	 * @throws  \RuntimeException
	 */
	private function getMultivariantValue($record, $fieldname)
	{
		$virtuemart_product_id = (int) $record->virtuemart_product_id;
		$parent_id             = (int) $record->virtuemart_product_id;

		// See if this is a child product
		if ($record->product_parent_id > 0)
		{
			$parent_id = (int) $record->product_parent_id;
		}

		$query = $this->db->getQuery(true)
			->select(
				$this->db->quoteName(
					array(
						'cf.virtuemart_customfield_id',
						'cf.virtuemart_custom_id',
						'cf.customfield_value',
						'cf.customfield_params',
					)
				)
			)
			->from($this->db->quoteName('#__virtuemart_product_customfields', 'cf'))
			->leftJoin(
				$this->db->quoteName('#__virtuemart_customs', 'c')
				. ' ON ' . $this->db->quoteName('c.virtuemart_custom_id') . ' = ' . $this->db->quoteName('cf.virtuemart_custom_id')
			)
			->where($this->db->quoteName('virtuemart_product_id') . ' = ' . $parent_id)
			->where($this->db->quoteName('c.field_type') . ' = ' . $this->db->quote('C'));

		$this->db->setQuery($query);
		$customfield = $this->db->loadObject();
		$this->log->add('Load the multi variant parameters');

		// Set the variables
		$selectoptions = array();
		$options       = array();
		$position      = false;
		$fieldvalue    = '';

		if (isset($customfield->customfield_params))
		{
			// Get the different segments
			$segments = explode('|', $customfield->customfield_params);

			// Process each segment
			foreach ($segments as $segment)
			{
				if ($firstpos = stripos($segment, '='))
				{
					// Get the values
					$group = substr($segment, 0, $firstpos);
					$value = substr($segment, $firstpos + 1);

					// Assign the value to it's group
					$$group = json_decode($value);
				}
			}

			// Find the position of the field
			foreach ($selectoptions as $position => $selectoption)
			{
				$csvi_name = trim($selectoption->voption);

				if ($selectoption->voption === 'clabels')
				{
					$csvi_name = trim($selectoption->clabel);
				}

				if ($csvi_name === $fieldname)
				{
					break;
				}
			}

			if ($position !== false && isset($options->$virtuemart_product_id))
			{
				$values = $options->$virtuemart_product_id;

				if (isset($values[$position]))
				{
					$fieldvalue = $values[$position];

					// Check if there is dot in the value, it is usually a price value
					if (is_numeric($fieldvalue))
					{
						$fieldvalue = $this->formatNumber($fieldvalue);
					}
				}
			}
		}

		return $fieldvalue;
	}

	/**
	 * Format a value to a number.
	 *
	 * @param   float $fieldValue The value to format as number.
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

	/**
	 * Get parent image for a product.
	 *
	 * @param   int    $productParentId The product ID to get the image.
	 * @param   string $fieldName       The field name to export.
	 *
	 * @return  string image url .
	 *
	 * @since  6.3.0
	 *
	 * @throws  \RuntimeException
	 */
	private function getParentImage($productParentId, $fieldName)
	{
		$query = $this->db->getQuery(true);

		if ($fieldName === 'picture_url_thumb')
		{
			$query->select(
				'CASE WHEN ' . $this->db->quoteName('file_url_thumb') . ' = ' . $this->db->quote('') . '
											THEN CONCAT(' . $this->db->quoteName('file_url') . ',' . $this->db->quote('-_-') . ')
											ELSE ' . $this->db->quoteName('file_url_thumb') . ' END ');
		}
		else
		{
			$query->select($this->db->quoteName('file_url'));
		}

		$query->from($this->db->quoteName('#__virtuemart_medias'))
			->leftJoin(
				$this->db->quoteName('#__virtuemart_product_medias')
				. ' ON ' . $this->db->quoteName('#__virtuemart_product_medias.virtuemart_media_id')
				. ' = ' . $this->db->quoteName('#__virtuemart_medias.virtuemart_media_id')
			)
			->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $productParentId)
			->where($this->db->quoteName('file_mimetype') . ' LIKE ' . $this->db->quote('image/%'))
			->order($this->db->quoteName('#__virtuemart_product_medias.ordering'));

		$this->db->setQuery($query, 0, 1);

		$imageURL = $this->db->loadColumn();

		// Debug log entry for query
		$this->log->add(\JText::_('COM_CSVI_FIND_PRODUCT_PARENT_IMAGE'), true);

		// Do the check till the image is retrieved from product anchestors
		if (count($imageURL) === 0)
		{
			// Check if the product parent id is not 0
			if (null !== $productParentId)
			{
				// Get the next level parent id
				$ancestorParentId = $this->getProductParentId($productParentId);

				// If current product has no image check the parent image
				$this->getParentImage($ancestorParentId, $fieldName);
			}
			else
			{
				$imageURL = array();
			}
		}

		// Return the product parent image
		return $imageURL;
	}

	/**
	 * Get the parent ID for a product.
	 *
	 * @param   int $productId The product ID to get the parent for
	 *
	 * @return  int  The parent product ID.
	 *
	 * @since   3.0
	 *
	 * @throws  \RuntimeException
	 */
	private function getProductParentId($productId)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('product_parent_id'))
			->from($this->db->quoteName('#__virtuemart_products'))
			->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $productId);
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	/**
	 * Get multi language fields for CF4ALL
	 *
	 * @return  void
	 *
	 * @since   7.5.0
	 */
	private function loadMultiLanguageCF4ALLFields()
	{
		$languages = $this->helper->getActiveLanguages();
		$multiLangFields = array();

		foreach ($languages as $language)
		{
			if ($language !== 'en-GB')
			{
				$multiLangFields[] = 'custom_param_' . strtolower(str_replace('-', '_', $language));
			}
		}

		$this->multiLangFields = $multiLangFields;
	}
}
