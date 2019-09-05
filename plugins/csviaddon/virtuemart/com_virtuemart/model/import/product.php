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

namespace virtuemart\com_virtuemart\model\import;

defined('_JEXEC') or die;

/**
 * Main processor for handling product details.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class Product extends \RantaiImportEngine
{
	/**
	 * The product table
	 *
	 * @var    \VirtueMartTableProduct
	 * @since  6.0
	 */
	private $productTable;

	/**
	 * The media table
	 *
	 * @var    \VirtueMartTableMedia
	 * @since  6.0
	 */
	private $mediaTable;

	/**
	 * The product media cross reference table
	 *
	 * @var    \VirtueMartTableProductMedia
	 * @since  6.0
	 */
	private $productMediaTable;

	/**
	 * The product price table
	 *
	 * @var    \VirtueMartTableProductPrice
	 * @since  6.0
	 */
	private $productPriceTable;

	/**
	 * The calculation rule table
	 *
	 * @var    \VirtueMartTableCalc
	 * @since  6.0
	 */
	private $calcsTable;

	/**
	 * The product custom fields table
	 *
	 * @var    \VirtueMartTableProductCustomfield
	 * @since  6.0
	 */
	private $productCustomfieldTable;

	/**
	 * The manufacturer table
	 *
	 * @var    \VirtueMartTableManufacturer
	 * @since  6.0
	 */
	private $manufacturerTable;

	/**
	 * The product manufacturer cross reference table
	 *
	 * @var    \VirtueMartTableProductManufacturer
	 * @since  6.0
	 */
	private $productManufacturerTable;

	/**
	 * The product shopper group cross reference table
	 *
	 * @var    \VirtueMartTableProductShoppergroup
	 * @since  6.0
	 */
	private $productShoppergroupTable;

	/**
	 * The product language table
	 *
	 * @var    \VirtueMartTableProductLang
	 * @since  6.0
	 */
	private $productLangTable;

	/**
	 * The manufacturer language table
	 *
	 * @var    \VirtueMartTableManufacturerLang
	 * @since  6.0
	 */
	private $manufacturerLangTable;

	/**
	 * List of available custom fields
	 *
	 * @var    array
	 * @since  6.0
	 */
	private $customFields = '';

	/**
	 * List of stockable custom fields
	 *
	 * @var    array
	 * @since  7.3.0
	 */
	private $stockableCustomFields = array();

	/**
	 * List of custom fields for all
	 *
	 * @var    array
	 * @since  7.3.0
	 */
	private $customFieldsForAll = array();

	/**
	 * The fields helper
	 *
	 * @var    \CsviHelperImportFields
	 * @since  6.0
	 */
	protected $fields;

	/**
	 * The multi variant fields that can be used as available field.
	 *
	 * @var    array
	 * @since  6.0
	 */
	private $multivariantFields = array();

	/**
	 * List of processed custom fields
	 *
	 * @var    array
	 * @since  6.0
	 */
	private $customTitles = array();

	/**
	 * VirtueMart helper
	 *
	 * @var    \Com_VirtuemartHelperCom_Virtuemart
	 * @since  6.0
	 */
	protected $helper = array();

	/**
	 * The plugin dispatcher
	 *
	 * @var    \RantaiPluginDispatcher
	 * @since  7.3.0
	 */
	private $dispatcher;

	/**
	 * List of plugins that have been checked
	 *
	 * @var    array
	 * @since  7.3.0
	 */
	private $pluginCheck = array();

	/**
	 * Collect deleted product ids
	 *
	 * @var    array
	 *
	 * @since  7.7.0
	 */
	protected $deletedProductIds = array();

	/**
	 * Run this before we start.
	 *
	 * @return  void.
	 *
	 * @throws  \Exception
	 *
	 * @since   6.0
	 */
	public function onBeforeStart()
	{
		// Load the dispatcher
		$this->dispatcher = new \RantaiPluginDispatcher;
		$this->dispatcher->importPlugins('csviext', $this->db);

		// Load the tables that will contain the data
		$this->loadCustomFields();

		$this->loadStockableCustomFields();
		$this->loadCustomFieldsForAll();
	}

	/**
	 * Here starts the processing.
	 *
	 * @return  boolean  Returns true on success | False on failure.
	 *
	 * @throws  \Exception
	 *
	 * @since   3.0
	 */
	public function getStart()
	{
		$vendorName = $this->fields->get('vendor_name', '');
		$this->setState('virtuemart_vendor_id', $this->helper->getVendorId($vendorName));

		// Process data
		foreach ($this->fields->getData() as $fields)
		{
			foreach ($fields as $name => $details)
			{
				$value = $details->value;

				// Check if the field needs extra treatment
				switch ($name)
				{
					case 'product_available_date':
					case 'product_discount_date_start':
					case 'product_discount_date_end':
						$this->setState($name, $this->convertDate($value));
						break;
					case 'product_price':
						// Cannot clean price otherwise we lose calculations
						$this->setState($name, $this->toPeriod($value));
						break;
					case 'product_override_price':
						// Set the value only when there is one, else dont update even if its empty
						if ($value && $value !== false)
						{
							$this->setState($name, $this->toPeriod($value));
						}
						break;
					case 'override':
						// Set the value only when there, else dont update even if its empty
						if ($value || $value !== false)
						{
							switch (strtolower($value))
							{
								case 'y':
								case 'yes':
								case '1':
									$value = 1;
									break;
								case '-1':
									$value = '-1';
									break;
								default:
									$value = 0;
									break;
							}

							$this->setState($name, $value);
						}
						break;
					case 'product_weight':
					case 'product_length':
					case 'product_width':
					case 'product_height':
					case 'product_packaging':
						$this->setState($name, $this->toPeriod($value));
						break;
					case 'related_products':
						if (substr($value, -1, 1) === '|')
						{
							$value = substr($value, 0, -1);
						}

						$this->setState($name, $value);
						break;
					case 'category_id':
					case 'category_path':
						if (strlen(trim($value)) > 0)
						{
							if (stripos($value, '|') > 0)
							{
								$category_ids[$name] = explode('|', $value);
							}
							else
							{
								$category_ids[$name][] = $value;
							}

							$this->setState('category_ids', $category_ids);
						}

						$this->setState($name, $value);
						break;
					case 'price_with_tax':
						$this->setState($name, $this->cleanPrice($value));
						break;
					case 'published':
					case 'media_published':
						switch ($value)
						{
							case 'n':
							case 'no':
							case 'N':
							case 'NO':
							case '0':
								$value = 0;
								break;
							default:
								$value = 1;
								break;
						}

						$this->setState($name, $value);
						break;
					case 'product_special':
					case 'product_discontinued':
						switch ($value)
						{
							case 'y':
							case 'yes':
							case 'Y':
							case 'YES':
							case '1':
								$value = 1;
								break;
							case '-1':
								$value = '-1';
								break;
							default:
								$value = 0;
								break;
						}

						$this->setState($name, $value);
						break;
					case 'product_currency':
						$this->setState($name, $this->helper->getCurrencyId(strtoupper($value), $this->getState('virtuemart_vendor_id')));
						break;
					case 'calc_value':
					case 'calc_value_mathop':
						$this->calcsTable->$name = $value;
						break;
					case 'product_tax':
						$this->setState($name, $this->cleanPrice($value));
						break;
					case 'multi_variant_fields':
						$this->setState($name, $value);

						// Load the multi-variant fields
						$this->loadMultiVariantFields();
						break;
					default:
						$this->setState($name, $value);
						break;
				}
			}
		}

		$field = $this->template->get('update_based_on', 'product_sku');

		if ($this->getState($field, false))
		{
			$this->productTable->setKeyName($field);
		}
		else
		{
			$field = 'virtuemart_product_id';
		}

		if ($this->getState($field, false))
		{
			// Set the record identifier
			$this->recordIdentity = $this->getState($field, '');

			// Load the current product data
			$this->loaded = true;

			if ($this->productTable->load($this->getState($field, false)))
			{
				if (!$this->template->get('overwrite_existing_data'))
				{
					$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_DATA_EXISTS_PRODUCT_SKU', $this->getState($field, '')));
					$this->loaded = false;
				}
				else
				{
					$this->setState('virtuemart_product_id', $this->productTable->get('virtuemart_product_id', 0));
				}
			}
			else
			{
				$virtuemart_product_id = $this->getState('virtuemart_product_id', false);

				if (!$this->getState($field, false) && empty($virtuemart_product_id))
				{
					$this->log->addStats('incorrect', 'COM_CSVI_DEBUG_NO_SKU');
					$this->log->add('COM_CSVI_DEBUG_NO_SKU');

					$this->loaded = false;
				}
				else
				{
					// Product is not found so we need to reset to the primary key field
					$this->productTable->setKeyName('virtuemart_product_id');
					$this->log->add(\JText::sprintf('COM_CSVI_DEBUG_PROCESS_SKU', $this->recordIdentity), false);
				}
			}

			// We need the currency
			if (!$this->getState('product_currency', false) && ($this->getState('product_price', false) || $this->getState('price_with_tax', false)))
			{
				$this->setState('product_currency', $this->helper->getVendorCurrency($this->getState('virtuemart_vendor_id')));
			}

			// Check for child product and get parent SKU if it is
			if ($this->getState('product_parent_sku', false))
			{
				$this->productParentSku();
			}
		}
		else
		{
			$this->loaded = false;

			$this->log->addStats(
				'skipped',
				\JText::sprintf('COM_CSVI_MISSING_REQUIRED_FIELDS_PRODUCT', $this->template->get('update_based_on', 'product_sku')),
				'product'
			);
		}

		return true;
	}

	/**
	 * Process each record and store it in the database.
	 *
	 * @return  bool  Returns true if all is OK | Returns false if no product SKU or product ID can be found.
	 *
	 * @since   3.0
	 */
	public function getProcessRecord()
	{
		if ($this->loaded)
		{
			// Get the needed data
			$virtuemart_product_id = $this->getState('virtuemart_product_id', false);
			$product_delete        = $this->getState('product_delete', 'N');

			// User wants to delete the product
			if ($virtuemart_product_id && $product_delete === 'Y')
			{
				$this->deleteProduct();
			}
			elseif (!$virtuemart_product_id && $product_delete === 'Y')
			{
				$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_NO_PRODUCT_ID_NO_DELETE', $this->recordIdentity));
			}
			elseif (!$virtuemart_product_id && $this->template->get('ignore_non_exist'))
			{
				// Do nothing for new products when user chooses to ignore new products
				$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_DATA_EXISTS_IGNORE_NEW', $this->recordIdentity));
			}
			else
			{
				// Process the product parameters
				$this->processProductParams();

				// Process discount
				if ($this->getState('product_discount', false))
				{
					$this->processDiscount();
				}

				// Process tax
				$this->log->add('Product tax: ' . $this->getState('product_tax'), false);

				if ($this->getState('product_tax'))
				{
					$this->processTax();
				}

				// Process the ICEcat product features
				$this->setState('features', $this->fields->get('features'));

				if ($this->template->get('use_icecat', false, 'bool') && $this->getState('features', false))
				{
					$this->icecatFeatures();
				}

				// Process product info
				if ($this->productQuery())
				{
					// Handle the shopper group(s)
					$this->processShopperGroup();

					// Handle the images
					$this->processMedia();

					// Check if the price is to be updated
					if ($this->getState('product_price', false) || $this->getState('price_with_tax', false) || $this->getState('product_override_price', false))
					{
						$this->priceQuery();
					}

					// Process manufacturer
					$this->manufacturerImport();

					// Delete all custom relation with product if user wants to do it
					if ($this->template->get('delete_product_customfields', false))
					{
						$this->deleteAllCustomValues();
					}

					// Process custom fields
					if ($this->getState('custom_title', false))
					{
						$this->processCustomFields();
					}

					// Check if the field is a custom field used as an available field
					$this->processCustomAvailableFields();

					// Check if the field is a multi variant field used as an available field
					$this->processMultiVariantFields();

					// Force an update of stockable variant parent products
					if ($this->template->get('update_stockable_parent', false))
					{
						$this->processParentValues(false);
					}

					/**
					 * Process related products/categories
					 * Related products are first input in the database as SKU
					 * At the end of the import, this is converted to product ID
					 */
					if ($this->getState('related_products'))
					{
						$this->processRelatedProducts();
					}

					if ($this->getState('related_categories'))
					{
						$this->processRelatedCategories();
					}

					// Process category path
					if (($this->getState('category_path', false) || $this->getState('category_id', false)) && $this->getState('category_ids', false))
					{
						if (null === $this->_categorymodel)
						{
							$this->_categorymodel = new \Com_virtuemartHelperCategory(
								$this->db,
								$this->template,
								$this->log,
								$this->csvihelper,
								$this->fields,
								$this->helper,
								$this->helperconfig,
								$this->userId
							);
						}

						$this->_categorymodel->getStart();

						// Check the categories
						// Do we have IDs
						if (array_key_exists('category_id', $this->getState('category_ids')))
						{
							$this->_categorymodel->checkCategoryPath(
								$this->getState('virtuemart_product_id'),
								false,
								$this->category_ids['category_id'],
								$this->getState('product_ordering')
							);
						}
						elseif (array_key_exists('category_path', $this->getState('category_ids')))
						{
							$this->_categorymodel->checkCategoryPath(
								$this->getState('virtuemart_product_id'),
								$this->category_ids['category_path'],
								false,
								$this->getState('product_ordering')
							);
						}
					}
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * delete all custom fields.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 *
	 * @throws  \Exception
	 */
	private function deleteAllCustomValues()
	{
		$virtuemart_product_id = $this->getState('virtuemart_product_id', false);

		// Load all the custom ids
		$query = $this->db->getQuery(true)
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
			->where($this->db->quoteName('cf.virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
		$this->db->setQuery($query);
		$customIds = $this->db->loadObjectList();

		foreach ($customIds as $customId)
		{
			// Fire the plugin to empty any values needed
			$this->dispatcher->trigger(
				'clearCustomValues',
				array(
					'plugin'                => $customId->custom_element,
					'params'                => $customId->customfield_params,
					'virtuemart_product_id' => $virtuemart_product_id,
					'virtuemart_custom_id'  => $customId->virtuemart_custom_id,
					'log'                   => $this->log
				)
			);

			$this->log->add('Remove custom field data from plugin tables', false);
		}

		// Finally delete all the values in product customfields table except related products and categories
		$query->clear()
			->delete($this->db->quoteName('#__virtuemart_product_customfields'))
			->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
			->where($this->db->quoteName('virtuemart_custom_id') . ' != ' . (int) $this->helper->getRelatedId('R'))
			->where($this->db->quoteName('virtuemart_custom_id') . ' != ' . (int) $this->helper->getRelatedId('Z'));
		$this->db->setQuery($query)->execute();
		$this->log->add('Removed existing customfield relation for product' . $virtuemart_product_id);
	}

	/**
	 * Execute any processes to finalize the import.
	 *
	 * @param   array  $fields  list of fields used for import
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 */
	public function getPostProcessing($fields=array())
	{
		// Related products
		if (in_array('related_products', $fields))
		{
			$this->postProcessRelatedProducts();
		}

		// Related categories
		if (in_array('related_categories', $fields))
		{
			$this->postProcessRelatedCategories();
		}
	}

	/**
	 * Load the product related tables.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 *
	 * @throws  \CsviException
	 * @throws  \RuntimeException
	 */
	public function loadTables()
	{
		// Load the main tables
		$this->productTable = $this->getTable('Product');
		$this->mediaTable = $this->getTable('Media');
		$this->productMediaTable = $this->getTable('ProductMedia');
		$this->productPriceTable = $this->getTable('ProductPrice');
		$this->calcsTable = $this->getTable('Calc');
		$this->productCustomfieldTable = $this->getTable('ProductCustomfield');
		$this->manufacturerTable = $this->getTable('Manufacturer');
		$this->productManufacturerTable = $this->getTable('ProductManufacturer');
		$this->productShoppergroupTable = $this->getTable('ProductShoppergroup');

		// Check if the language tables exist
		$tables = $this->db->getTableList();

		// Get the language to use
		$language = $this->template->get('language');

		if (!in_array($this->db->getPrefix() . 'virtuemart_products_' . $language, $tables, true))
		{
			// Set the name of the table not found
			$tableName = $this->db->getPrefix() . 'virtuemart_products_' . $language;

			$message = \JText::_('COM_CSVI_LANGUAGE_MISSING');

			if ($language)
			{
				$message = \JText::sprintf('COM_CSVI_TABLE_NOT_FOUND', $tableName);
			}

			throw new \CsviException($message, 510);
		}
		elseif (!in_array($this->db->getPrefix() . 'virtuemart_manufacturers_' . $language, $tables, true))
		{
			// Set the name of the table not found
			$tableName = $this->db->getPrefix() . 'virtuemart_manufacturers_' . $language;

			$message = \JText::_('COM_CSVI_LANGUAGE_MISSING');

			if ($language)
			{
				$message = \JText::sprintf('COM_CSVI_TABLE_NOT_FOUND', $tableName);
			}

			throw new \CsviException($message, 510);
		}

		// Load the language tables
		$this->productLangTable = $this->getTable('ProductLang');
		$this->manufacturerLangTable = $this->getTable('ManufacturerLang');
	}

	/**
	 * Get a list of custom fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   4.4.1
	 *
	 * @throws  \Exception
	 */
	private function loadCustomFields()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('virtuemart_custom_id', 'id'))
			->select($this->db->quoteName('custom_parent_id'))
			->select($this->db->quoteName('field_type'))
			->select('TRIM(' . $this->db->quoteName('custom_title') . ') AS ' . $this->db->quoteName('title'))
			->select($this->db->quoteName('ordering'))
			->from($this->db->quoteName('#__virtuemart_customs'))
			->where(
				$this->db->quoteName('field_type') . ' IN ('
					. $this->db->quote('S') . ','
					. $this->db->quote('I') . ','
					. $this->db->quote('B') . ','
					. $this->db->quote('D') . ','
					. $this->db->quote('T') . ','
					. $this->db->quote('M') . ','
					. $this->db->quote('Y') . ','
					. $this->db->quote('X') . ')'
			)
			->order($this->db->quoteName('ordering'));
		$this->db->setQuery($query);
		$this->customFields = $this->db->loadObjectlist();

		// Fire the plugin to load specific custom fields if needed
		$pluginFields = $this->dispatcher->trigger(
			'loadPluginCustomFields',
			array(
				'log' => $this->log
			)
		);

		if ($pluginFields)
		{
			$this->customFields = array_merge($this->customFields, $pluginFields);
		}

		$this->log->add('Load the custom fields');
	}

	/**
	 * Get a list of multi variant fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function loadMultiVariantFields()
	{
		$this->multivariantFields = explode('~', $this->getState('multi_variant_fields'));
	}

	/**
	 * Cleaning the product related tables.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 */
	public function clearTables()
	{
		// Clean the main tables
		$this->productTable->reset();
		$this->mediaTable->reset();
		$this->productMediaTable->reset();
		$this->productPriceTable->reset();
		$this->calcsTable->reset();
		$this->productCustomfieldTable->reset();
		$this->manufacturerTable->reset();
		$this->productManufacturerTable->reset();
		$this->productShoppergroupTable->reset();

		// Clean the language tables
		$this->productLangTable->reset();
		$this->manufacturerLangTable->reset();
	}

	/**
	 * Get the product parent sku if it is a child product.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 *
	 * @throws  \Exception
	 */
	private function productParentSku()
	{
		$this->log->add('Looking for parent SKU', false);

		if ($this->getState('product_sku', false))
		{
			$product_parent_sku = $this->getState('product_parent_sku');

			// Check if we are dealing with a child product
			if (!empty($product_parent_sku) && $product_parent_sku !== $this->getState('product_sku'))
			{
				$this->setState('child_product', true);

				// Get the parent id first
				$query = $this->db->getQuery(true);
				$query->select($this->db->quoteName('virtuemart_product_id'))
					->from($this->db->quoteName('#__virtuemart_products'))
					->where($this->db->quoteName('product_sku') . ' = ' . $this->db->quote($product_parent_sku));
				$this->db->setQuery($query);
				$this->setState('product_parent_id', $this->db->loadResult());
				$this->log->add('Checking product parent SKU');
			}
			else
			{
				$this->setState('product_parent_id', 0);
				$this->setState('child_product', false);
			}
		}
	}

	/**
	 * Creates either an update or insert SQL query for a product.
	 *
	 * @return  bool true if the query executed successful|false if the query failed.
	 *
	 * @since   3.0
	 *
	 * @throws  \Exception
	 */
	private function productQuery()
	{
		// Check if we need to do a stock calculation
		if ($this->getState('product_in_stock', false))
		{
			// Split the modification
			$operation = substr($this->getState('product_in_stock'), 0, 1);
			$value = substr($this->getState('product_in_stock'), 1);

			// Get the database value
			$stock = $this->productTable->product_in_stock;

			// Check what modification we need to do and apply it
			switch ($operation)
			{
				case '+':
					$stock += $value;
					break;
				case '-':
					$stock -= $value;
					break;
				case '/':
					$stock /= $value;
					break;
				case '*':
					$stock *= $value;
					break;
				default:
					// Assign the current price to prevent it being overwritten
					$stock = $this->getState('product_in_stock');
					break;
			}

			$this->setState('product_in_stock', $stock);
		}

		// Bind the initial data
		$this->productTable->bind($this->state);

		// Set the modified date as we are modifying the product
		if (!$this->getState('modified_on'))
		{
			$this->productTable->modified_on = $this->getState('modified_on', $this->date->toSql());
			$this->productTable->modified_by = $this->getState('modified_by', $this->userId);
		}

		// Add a creating date if there is no product_id
		if (!$this->getState('virtuemart_product_id'))
		{
			$this->productTable->created_on = $this->getState('created_on', $this->date->toSql());
			$this->productTable->created_by = $this->getState('created_by', $this->userId);

			// Process default values
			$defaults = array('product_weight', 'product_weight_uom', 'product_length', 'product_width', 'product_height',
						'product_lwh_uom', 'product_url', 'product_in_stock', 'product_ordered', 'low_stock_notification',
						'product_availability', 'product_special', 'product_sales', 'product_unit', 'product_packaging',
						'product_params', 'hits', 'intnotes', 'metarobot', 'metaauthor', 'layout', 'published');

			foreach ($defaults as $field)
			{
				switch ($field)
				{
					case 'product_weight':
					case 'product_length':
					case 'product_width':
					case 'product_height':
					case 'product_in_stock':
					case 'product_ordered':
					case 'low_stock_notification':
					case 'product_special':
					case 'product_sales':
					case 'product_packaging':
					case 'hits':
					case 'layout':
					case 'published':
						if (!$this->getState($field, false))
						{
							$this->productTable->$field = 0;
						}
						break;
					case 'product_weight_uom':
					case 'product_unit':
						if (!$this->getState($field, false))
						{
							$this->productTable->$field = 'KG';
						}
						break;
					case 'product_lwh_uom':
						if (!$this->getState($field, false))
						{
							$this->productTable->$field = 'M';
						}
						break;
					case 'product_url':
					case 'product_availability':
					case 'intnotes':
					case 'metarobot':
					case 'metaauthor':
						if (!$this->getState($field, false))
						{
							$this->productTable->$field = '';
						}
						break;
					case 'product_params':
						if (!$this->getState($field, false))
						{
							$this->productTable->$field = 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|';
						}
						break;
				}
			}
		}

		try
		{
			// Store the product
			$this->productTable->store();

			// If this is a child product, check if we need to update the custom field
			if ($this->getState('child_product'))
			{
				$this->processParentValues();
			}
		}
		catch (\Exception $e)
		{
			$this->log->addStats('error', \JText::_('COM_CSVI_CANNOT_ADD_PRODUCTS'));
			$this->log->add($e->getMessage(), false);

			// Check if error is for duplicate sku and show a friendly message
			if (strpos($e->getMessage(), 'Duplicate entry') !== false)
			{
				$this->log->addStats('error', \JText::_('COM_CSVI_DUPLICATE_SKU_PRODUCTS'));
			}

			return false;
		}

		// Set the product ID
		$this->setState('virtuemart_product_id', $this->productTable->get('virtuemart_product_id'));

		// Set the product ID for the languages
		$this->productLangTable->set('virtuemart_product_id', $this->getState('virtuemart_product_id'));
		$this->productLangTable->set('product_name', $this->getState('product_name'));
		$this->productLangTable->set('slug', $this->getState('slug'));

		// Check if a language entry exists
		if ($this->productLangTable->check())
		{
			// Slug may have changed, refresh it
			$this->setState('slug', $this->productLangTable->get('slug'));

			// Bind the language fields
			$this->productLangTable->bind($this->state);

			// Recreate the slug
			if ($this->template->get('recreate_alias', false))
			{
				$this->productLangTable->createSlug();
			}

			if (!$this->productLangTable->store())
			{
				return false;
			}
		}
		else
		{
			$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_PRODUCT_LANG_NOT_ADDED', $this->productLangTable->getError()));
			$this->log->add('Language query', true);

			return false;
		}

		// All good
		return true;
	}

	/**
	 * Process related products.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 *
	 * @throws  \Exception
	 */
	private function processRelatedProducts()
	{
		$relatedproducts = explode('|', $this->related_products);

		$query = 'INSERT IGNORE INTO ';
		$query .= $this->db->quoteName('#__csvi_related_products') . ' VALUES ';
		$entries = array();

		foreach ($relatedproducts as $relatedproduct)
		{
			$entries[] = '(' . $this->db->quote($this->product_sku) . ', ' . $this->db->quote($relatedproduct) . ')';
		}

		$query .= implode(',', $entries);
		$this->db->setQuery($query)->execute();

		// Remove any existing product relations
		$this->productCustomfieldTable->deleteRelated($this->virtuemart_product_id, $this->virtuemart_vendor_id, $this->helper->getRelatedId('R'));
	}

	/**
	 * Post-process related products.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 *
	 * @throws  \Exception
	 */
	private function postProcessRelatedProducts()
	{
		// Get the related products
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('p1.virtuemart_product_id', 'virtuemart_product_id'))
			->select($this->db->quoteName('p2.virtuemart_product_id', 'customfield_value'))
			->from($this->db->quoteName('#__csvi_related_products', 'r'))
			->leftJoin(
				$this->db->quoteName('#__virtuemart_products', 'p1')
				. ' ON ' . $this->db->quoteName('r.product_sku') . ' = ' . $this->db->quoteName('p1.product_sku')
			)
			->leftJoin(
				$this->db->quoteName('#__virtuemart_products', 'p2')
				. ' ON ' . $this->db->quoteName('r.related_sku') . ' = ' . $this->db->quoteName('p2.product_sku')
			);
		$this->db->setQuery($query);
		$relations = $this->db->loadObjectList();
		$this->log->add('Process related products');

		if (!empty($relations))
		{
			// Store the new relations
			foreach ($relations as $related)
			{
				// Build the object to store
				$related->virtuemart_custom_id = $this->helper->getRelatedId('R');
				$related->published = 0;
				$related->created_on = $this->date->toSql();
				$related->created_by = $this->userId;
				$related->modified_on = $this->date->toSql();
				$related->modified_by = $this->userId;
				$related->customfield_params = '';

				// Bind the data
				$this->productCustomfieldTable->bind($related);

				// Store the data
				$this->productCustomfieldTable->store();

				// Clean the table object for next insert
				$this->productCustomfieldTable->reset();
			}

			// Empty the relations table
			$this->db->truncateTable('#__csvi_related_products');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_NO_RELATED_PRODUCTS_FOUND');
		}
	}

	/**
	 * Process related categories.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 *
	 * @throws  \Exception
	 */
	private function processRelatedCategories()
	{
		if (null === $this->_categorymodel)
		{
			$this->_categorymodel = new \Com_virtuemartHelperCategory(
				$this->db,
				$this->template,
				$this->log,
				$this->csvihelper,
				$this->fields,
				$this->helper,
				$this->helperconfig,
				$this->userId
			);
		}

		$this->_categorymodel->getStart();
		$relatedcategories = explode('|', $this->getState('related_categories'));

		$query = $this->db->getQuery(true)->insert($this->db->quoteName('#__csvi_related_categories'));

		foreach ($relatedcategories as $relatedcategory)
		{
			$query->values($this->db->quote($this->getState('product_sku')) . ', ' . $this->db->quote($relatedcategory));
		}

		$this->db->setQuery($query)->execute();

		// Remove any existing product relations
		$this->productCustomfieldTable->deleteRelated(
			$this->getState('virtuemart_product_id'),
			$this->getState('virtuemart_vendor_id'),
			$this->helper->getRelatedId('Z')
		);
	}

	/**
	 * Post-process related categories.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 *
	 * @throws  \Exception
	 */
	private function postProcessRelatedCategories()
	{
		if (null === $this->_categorymodel)
		{
			$this->_categorymodel = new \Com_virtuemartHelperCategory(
				$this->db,
				$this->template,
				$this->log,
				$this->csvihelper,
				$this->fields,
				$this->helper,
				$this->helperconfig,
				$this->userId
			);
		}

		// Get the related categories
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('p.virtuemart_product_id', 'virtuemart_product_id'))
			->select($this->db->quoteName('r.related_cat'))
			->from($this->db->quoteName('#__csvi_related_categories', 'r'))
			->leftJoin(
				$this->db->quoteName('#__virtuemart_products', 'p')
				. ' ON ' . $this->db->quoteName('r.product_sku') . ' = ' . $this->db->quoteName('p.product_sku')
			);
		$this->db->setQuery($query);
		$relations = $this->db->loadObjectList();
		$this->log->add('Process related categories');

		if (!empty($relations))
		{
			// Store the new relations
			foreach ($relations as $related)
			{
				// Find the category ID
				$ids = $this->_categorymodel->getCategoryIdFromPath($related->related_cat);

				if (array_key_exists('category_id', $ids))
				{
					$related->customfield_value = $ids['category_id'];

					// Build the object to store
					$related->virtuemart_custom_id = $this->helper->getRelatedId('Z');
					$related->published            = 0;
					$related->created_on           = $this->date->toSql();
					$related->created_by           = $this->userId;
					$related->modified_on          = $this->date->toSql();
					$related->modified_by          = $this->userId;
					$related->customfield_param    = '';

					// Bind the data
					$this->productCustomfieldTable->bind($related);

					// Store the data
					$this->productCustomfieldTable->store();

					// Clean the table object for next insert
					$this->productCustomfieldTable->reset();
				}
			}

			// Empty the relations table
			$this->db->truncateTable('#__csvi_related_categories');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_NO_RELATED_CATEGORIES_FOUND');
		}
	}

	/**
	 * Process media files.
	 *
	 * @return  bool Returns true on OK | False on failure.
	 *
	 * @since   4.0
	 *
	 * @throws  \Exception
	 */
	private function processMedia()
	{
		$generateImage = $this->template->get('auto_generate_image_name', false);
		$fileUrl = $this->getState('file_url', '');

		// Check if any image handling needs to be done
		if ($fileUrl || $generateImage)
		{
			// Check if we have any images
			if (('' === $fileUrl) && $generateImage)
			{
				$fileUrl = $this->createImageName();
			}

			// Create an array of images to process
			$images       = explode('|', $fileUrl);
			$thumbs       = explode('|', $this->getState('file_url_thumb'));
			$titles       = explode('|', $this->getState('file_title'));
			$descriptions = explode('|', $this->getState('file_description'));
			$metas        = explode('|', $this->getState('file_meta'));
			$order        = explode('|', $this->getState('file_ordering'));
			$ordering     = 1;
			$max_width    = $this->template->get('resize_max_width', 1024);
			$max_height   = $this->template->get('resize_max_height', 768);

			// Image handling
			$imageHelper = new \CsviHelperImage($this->template, $this->log, $this->csvihelper);

			// Delete existing image links
			if ($this->template->get('delete_product_images', false))
			{
				$query = $this->db->getQuery(true)
					->delete($this->db->quoteName('#__virtuemart_product_medias'))
					->where($this->db->quoteName('virtuemart_product_id') . '=' . $this->getState('virtuemart_product_id'));
				$this->db->setQuery($query)->execute();
				$this->log->add('Delete images');
			}

			foreach ($images as $key => $image)
			{
				$image = trim($image);
				$productFullImageOutput = '';

				// Create image name if needed
				if (count($images) === 1)
				{
					$imgCounter = 0;
				}
				else
				{
					$imgCounter = $key + 1;
				}

				if ($generateImage)
				{
					$productFullImageOutput = $this->createImageName($imgCounter);
				}

				if (!empty($image))
				{
					// Get the image path
					$imgPath = $this->template->get('file_location_product_files', 'images/stories/virtuemart/product/');

					// Make sure the final slash is present
					if (substr($imgPath, -1) !== '/')
					{
						$imgPath .= '/';
					}

					// Verify the original image
					if ($imageHelper->isRemote($image))
					{
						$original = $image;
						$fullPath = $imgPath;
					}
					else
					{
						// Check if the image contains the image path
						$dirname = dirname($image);

						if (strpos($imgPath, $dirname) !== false)
						{
							// Collect rest of folder path if it is more than image default path
							$imageLeftPath = str_replace($imgPath, '', $dirname . '/');
							$image = basename($image);

							if ($imageLeftPath)
							{
								$image = $imageLeftPath . $image;
							}
						}

						$original = $imgPath . $image;

						// Get subfolders
						$pathParts = pathinfo($original);
						$fullPath = $pathParts['dirname'] . '/';
					}

					// Check only if image link has to be updated
					if ($this->template->get('update_only_media_link', false))
					{
						$this->mediaTable->file_url = (empty($fullPath)) ? basename($image) : $fullPath . basename($image);

						// Check if the media image already exists
						if ($this->mediaTable->check())
						{
							$this->productMediaTable->virtuemart_product_id = $this->getState('virtuemart_product_id');
							$this->productMediaTable->virtuemart_media_id = $this->mediaTable->get('virtuemart_media_id');

							if ($this->productMediaTable->check())
							{
								$this->productMediaTable->ordering = (array_key_exists($key, $order) && !empty($order[$key])) ? $order[$key] : $ordering;

								if ($this->productMediaTable->store())
								{
									$this->log->add('Store product image relation', false);
									$ordering++;
								}
							}
							else
							{
								$this->log->add('Product image relation already exist', false);
							}
						}
						else
						{
							$this->log->add('Image ' . $this->mediaTable->file_url . ' does not exist in media table', false);
						}
					}
					else
					{
						// Generate image names
						if ($this->template->get('process_image', false))
						{
							if ($generateImage)
							{
								$fileDetails = $imageHelper->processImage($original, $fullPath, $productFullImageOutput);
							}
							else
							{
								$fileDetails = $imageHelper->processImage($original, $fullPath);
							}
						}
						else
						{
							$fileDetails['exists']      = true;
							$fileDetails['isimage']     = $imageHelper->isImage(JPATH_SITE . '/' . $original);
							$fileDetails['name']        = $image;
							$fileDetails['output_name'] = basename($image);

							if (file_exists(JPATH_SITE . '/' . $image))
							{
								$fileDetails['mime_type'] = $imageHelper->findMimeType($image);
							}
							else
							{
								$fileDetails['mime_type'] = '';
							}

							$fileDetails['output_path'] = $fullPath;
						}

						// Process the file details
						if ($fileDetails['exists'])
						{
							$title                         = array_key_exists($key, $titles) ? $titles[$key] : $fileDetails['output_name'];
							$description                   = array_key_exists($key, $descriptions) ? $descriptions[$key] : '';
							$meta                          = array_key_exists($key, $metas) ? $metas[$key] : '';
							$media                         = array();
							$media['virtuemart_vendor_id'] = $this->getState('virtuemart_vendor_id');

							if ($this->template->get('autofill'))
							{
								$media['file_title']       = $fileDetails['output_name'];
								$media['file_description'] = $fileDetails['output_name'];
								$media['file_meta']        = $fileDetails['output_name'];
							}
							else
							{
								$media['file_title']       = $title;
								$media['file_description'] = $description;
								$media['file_meta']        = $meta;
							}

							$media['file_mimetype']         = $fileDetails['mime_type'];
							$media['file_type']             = 'product';
							$media['file_is_product_image'] = 1;
							$media['file_is_downloadable']  = ($fileDetails['isimage']) ? 0 : 1;
							$media['file_is_forSale']       = 0;
							$media['file_url']              = (empty($fileDetails['output_path'])) ? $fileDetails['output_name'] : $fileDetails['output_path'] . $fileDetails['output_name'];
							$media['published']             = $this->getState('media_published', $this->getState('published', 0));

							// Create the thumbnail
							if ($fileDetails['isimage'])
							{
								$thumb = (isset($thumbs[$key])) ? $thumbs[$key] : null;

								if ($thumb)
								{
									// Check if the image contains the image path
									$dirname = dirname($thumb);

									if (strpos($imgPath . 'resized/', $dirname) !== false)
									{
										// Collect rest of folder path if it is more than image default path
										$imageLeftPath = str_replace($imgPath, '', $dirname . '/');
										$imageThumb    = basename($thumb);

										if ($imageLeftPath)
										{
											$thumb = $imageLeftPath . $imageThumb;
										}
									}
								}

								if ($this->template->get('thumb_create', false))
								{
									$thumbSizes = getimagesize(JPATH_SITE . '/' . $media['file_url']);

									if (empty($thumb) || $generateImage)
									{
										// Get the subfolder structure
										$thumbPath = str_ireplace($imgPath, '', $fullPath);
										$thumb = 'resized/' . $thumbPath . basename($media['file_url']);
									}
									else
									{
										// Check if we are not overwriting any large images
										$thumbPathParts = pathinfo($thumb);

										if ($thumbPathParts['dirname'] === '.')
										{
											$this->log->addStats('incorrect', 'COM_CSVI_THUMB_OVERWRITE_FULL');
											$thumb = false;
										}
									}

									$media['file_url_thumb'] = '';

									if ($thumb && ($thumbSizes[0] < $max_width || $thumbSizes[1] < $max_height))
									{
										$media['file_url_thumb'] = $imageHelper->createThumbnail($media['file_url'], $imgPath, $thumb);
									}
									else
									{
										$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_THUMB_TOO_BIG', $max_width, $max_height, $thumbSizes[0], $thumbSizes[1]));
										$this->log->add('Thumbnail is bigger than maximums set', false);
										$media['file_url_thumb'] = '';
									}
								}
								else
								{
									$thumbPath = '';

									// Check if there is any overlap in paths
									if ($thumb)
									{
										$thumbPath = $fileDetails['output_path'] . $thumb;

										if (strpos($fileDetails['output_path'], dirname($thumb)) !== false)
										{
											// Collect rest of folder path if it is more than image default path
											$imageLeftPath = str_replace(dirname($thumb) . '/', '', $fileDetails['output_path']);

											if ($imageLeftPath)
											{
												$thumbPath = $imageLeftPath . $thumb;
											}
										}
									}

									$media['file_url_thumb'] = empty($thumbPath) ? $media['file_url'] : $thumbPath;

									if (0 === strpos($media['file_url_thumb'], 'http') && null !== $thumb)
									{
										$media['file_url_thumb'] = $thumb;
									}
								}
							}
							else
							{
								$media['file_is_product_image'] = 0;
								$media['file_url_thumb']        = '';
							}

							// Bind the media data
							$this->mediaTable->bind($media);

							// Check if the media image already exists
							$this->mediaTable->check();

							// Store the media data
							if ($this->mediaTable->store())
							{
								// Watermark the image
								if ($this->template->get('full_watermark', 'image') && $fileDetails['isimage'])
								{
									$imageHelper->addWatermark(JPATH_SITE . '/' . $media['file_url']);
								}

								// Store the product image relation
								$data                          = array();
								$data['virtuemart_product_id'] = $this->getState('virtuemart_product_id');
								$data['virtuemart_media_id']   = $this->mediaTable->get('virtuemart_media_id');
								$data['ordering']              = (isset($order[$key]) && !empty($order[$key])) ? $order[$key] : $ordering;

								if ($this->productMediaTable->save($data))
								{
									$this->log->add('Store product image relation', false);
									$ordering++;
								}
							}
							else
							{
								$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_MEDIA_NOT_ADDED', $this->mediaTable->getError()));

								return false;
							}
						}
					}

					// Reset the product media table
					$this->mediaTable->reset();
					$this->productMediaTable->reset();
				}
			}
		}

		return true;
	}

	/**
	 * Manufacturer import.
	 *
	 * Adds or updates a manufacturer and adds a reference to the product.
	 *
	 * @return  bool Returns true on OK | False on failure.
	 *
	 * @since   3.0
	 *
	 * @throws  \Exception
	 */
	private function manufacturerImport()
	{
		$this->log->add('Checking manufacturer', false);

		// Process all manufacturers
		$manufacturerNames = '';
		$manufacturerIds = $this->getState('manufacturer_id', false);
		$field = 'virtuemart_manufacturer_id';

		if (!$manufacturerIds)
		{
			$manufacturerNames = $this->getState('manufacturer_name', false);
			$field = 'mf_name';
		}

		if ($manufacturerIds || $manufacturerNames)
		{
			$manufacturers = explode('|', $manufacturerIds ?: $manufacturerNames);

			// Delete old product manufacturer reference and insert new ones
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__virtuemart_product_manufacturers'))
				->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $this->getState('virtuemart_product_id'));
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete product manufacturer references');

			foreach ($manufacturers as $manufacturer)
			{
				$this->manufacturerLangTable->set($field, $manufacturer);

				// Check for existing manufacturer
				if ($this->manufacturerLangTable->check())
				{
					// Store the manufacturers language details
					if (!$this->manufacturerLangTable->store())
					{
						return false;
					}

					// Set the manufacturer ID
					$this->manufacturerTable->set('virtuemart_manufacturer_id', $this->manufacturerLangTable->get('virtuemart_manufacturer_id'));

					// Check if a manufacturer exists
					if (!$this->manufacturerTable->check() && !$this->manufacturerTable->store())
					{
						return false;
					}

					// Store the cross reference
					$this->productManufacturerTable->set('virtuemart_product_id', $this->getState('virtuemart_product_id'));
					$this->productManufacturerTable->set('virtuemart_manufacturer_id', $this->manufacturerLangTable->get('virtuemart_manufacturer_id'));

					if (!$this->productManufacturerTable->check())
					{
						$this->productManufacturerTable->store();
					}
				}

				$this->manufacturerLangTable->reset();
				$this->manufacturerTable->reset();
				$this->productManufacturerTable->reset();
			}
		}

		return true;
	}

	/**
	 * Creates either an update or insert SQL query for a product price.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 *
	 * @throws  \Exception
	 */
	private function priceQuery()
	{
		// Check if we have a child product with an empty price (will use parents price)
		if ($this->child_product && ($this->product_price == 0) && (null === $this->price_with_tax) && (null === $this->product_tax))
		{
			$this->log->add('COM_CSVI_DEBUG_CHILD_NO_PRICE', false);
		}
		else
		{
			// Remove existing product prices if user wants to
			if ($this->template->get('delete_existing_prices'))
			{
				$virtuemartProductId = $this->getState('virtuemart_product_id', false);

				if ($virtuemartProductId
					&& !in_array($virtuemartProductId, $this->deletedProductIds)
				)
				{
					$query = $this->db->getQuery(true)
						->delete($this->db->quotename('#__virtuemart_product_prices'))
						->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $this->getState('virtuemart_product_id', false));
					$this->db->setQuery($query)->execute();
					$this->log->add('Delete existing product prices to insert new ones');
					$this->deletedProductIds[] = $this->getState('virtuemart_product_id', false);
				}
			}

			// Check if we have an override price, this is always excluding tax
			if ($this->getState('product_override_price', false) && ($this->getState('override', false) === false))
			{
				$this->setState('override', 1);
			}

			// Check if the price is including or excluding tax
			if (($this->getState('product_tax_id', false) || $this->getState('product_tax', false))
				&& $this->getState('price_with_tax', false) && !$this->getState('product_price', false))
			{
				if (strlen($this->getState('price_with_tax', '')) === 0)
				{
					$this->setState('product_price', null);
				}
				else
				{
					// Check if we have an ID or a value
					if ($this->getState('product_tax_id', false))
					{
						// Find the value
						$this->calcsTable->load($this->getState('product_tax_id'));
						$this->setState('product_tax', $this->calcsTable->calc_value);
					}

					$this->setState('product_price', $this->getState('price_with_tax') / (1 + ($this->getState('product_tax') / 100)));
				}
			}
			elseif (strlen($this->getState('product_price', '')) === 0)
			{
				$this->setState('product_price', null);
			}

			// Check if we need to assign a shopper group
			if (null !== $this->shopper_group_name_price)
			{
				if ($this->getState('shopper_group_name_price', '*') === '*')
				{
					$this->setState('virtuemart_shoppergroup_id', 0);
				}
				else
				{
					$this->setState('virtuemart_shoppergroup_id', $this->helper->getShopperGroupId($this->shopper_group_name_price));
				}
			}

			// Bind the fields to check for an existing price
			$this->productPriceTable->bind($this->state);

			// Check if the price already exists
			if (!$this->productPriceTable->check())
			{
				// Price doesn't exist
				if (!$this->productPriceTable->get('price_quantity_start'))
				{
					$this->productPriceTable->price_quantity_start = 0;
				}

				if (!$this->productPriceTable->get('price_quantity_end'))
				{
					$this->productPriceTable->price_quantity_end = 0;
				}

				if ($this->productPriceTable->get('override', false) === false)
				{
					$this->productPriceTable->override = 0;
				}

				if (!$this->productPriceTable->get('product_price_publish_up'))
				{
					$this->productPriceTable->product_price_publish_up = '0000-00-00 00:00:00';
				}

				if (!$this->productPriceTable->get('product_price_publish_down'))
				{
					$this->productPriceTable->product_price_publish_down = '0000-00-00 00:00:00';
				}

				// Set the create date if the user has not done so and there is no product_price_id
				if (!$this->productPriceTable->get('created_on'))
				{
					$this->productPriceTable->created_on = $this->date->toSql();
					$this->productPriceTable->created_by = $this->userId;
				}
			}

			// Check if we need to change the shopper group name
			if (null !== $this->shopper_group_name_new)
			{
				if ($this->shopper_group_name_new === '*')
				{
					$this->productPriceTable->virtuemart_shoppergroup_id = 0;
				}
				else
				{
					$this->productPriceTable->virtuemart_shoppergroup_id = $this->helper->getShopperGroupId($this->shopper_group_name_new);
				}
			}

			// Re-bind because the values are loaded during the check
			$this->productPriceTable->bind($this->state);

			// Calculate the new price
			$this->productPriceTable->CalculatePrice();

			/**
			 * Store the price
			 * Add some variables if needed
			 * Set the modified date if the user has not done so
			 */
			if (!$this->productPriceTable->get('modified_on'))
			{
				$this->productPriceTable->set('modified_on', $this->date->toSql());
				$this->productPriceTable->set('modified_by', $this->userId);
			}

			// Store the price
			$this->productPriceTable->store();
		}
	}

	/**
	 * Stores a product discount.
	 *
	 * @return  bool Returns true on OK | False on failure.
	 *
	 * @since   3.0
	 */
	private function processDiscount()
	{
		$this->log->add('COM_CSVI_DEBUG_PROCESSING_DISCOUNT');

		// Clear the calcs from any data
		$this->calcsTable->reset();

		// Determine if the discount field is a percentage
		if ($this->getState('product_discount', false))
		{
			if (substr($this->getState('product_discount'), -1, 1) === '%')
			{
				$this->calcsTable->calc_value_mathop = '-%';
				$this->calcsTable->calc_value = substr($this->toPeriod($this->product_discount), 0, -1);
			}
			else
			{
				$this->calcsTable->calc_value_mathop = '-';
				$this->calcsTable->calc_value = $this->cleanPrice($this->product_discount);
			}
		}

		if ((null !== $this->calcsTable->calc_value) && $this->calcsTable->calc_value > 0)
		{
			// Add the discount fields
			$this->calcsTable->publish_up = $this->getState('product_discount_date_start');
			$this->calcsTable->publish_down = $this->getState('product_discount_date_end');

			// Add a description to the discount
			$this->calcsTable->calc_name = $this->getState('product_discount');
			$this->calcsTable->calc_descr = $this->getState('product_discount');
			$this->calcsTable->calc_shopper_published = 1;
			$this->calcsTable->calc_vendor_published = 1;
			$this->calcsTable->calc_currency = $this->productPriceTable->product_currency;

			if (empty($this->calc_kind))
			{
				$this->calcsTable->calc_kind = 'DBTax';
			}
			else
			{
				$this->calcsTable->calc_kind = $this->getState('calc_kind');
			}

			// Check if a discount already exists
			$this->calcsTable->check();

			// Store the discount
			if (!$this->calcsTable->store())
			{
				$this->log->add('COM_CSVI_DEBUG_ADD_DISCOUNT', true);

				return false;
			}

			$this->log->add('COM_CSVI_DEBUG_ADD_DISCOUNT', true);

			// Fill the product information with the discount ID
			$this->setState('product_discount_id', $this->calcsTable->virtuemart_calc_id);
		}
		else
		{
			$this->log->add('COM_CSVI_DEBUG_NO_DISCOUNT');
		}

		return true;
	}

	/**
	 * Process a tax rate.
	 *
	 * @return  void.
	 *
	 * @since   4.0
	 */
	private function processTax()
	{
		if ($this->getState('product_tax') > 0)
		{
			$this->log->add('Processing product tax', false);

			// Clear the calcs from any data
			$this->calcsTable->reset();

			// Add some data
			$this->calcsTable->calc_kind = $this->getState('calc_kind', 'Tax');
			$this->calcsTable->calc_value = $this->getState('product_tax');
			$this->calcsTable->calc_value_mathop = '+%';

			// Check if the tax rate already exists
			if (!$this->calcsTable->check())
			{
				$this->log->add('Adding a CSVI generated tax rule', false);

				$this->calcsTable->virtuemart_vendor_id = $this->getState('virtuemart_vendor_id', 1);
				$this->calcsTable->calc_name = \JText::_('COM_CSVI_AUTO_TAX_RATE');
				$this->calcsTable->calc_descr = \JText::_('COM_CSVI_AUTO_TAX_RATE_DESC');
				$this->calcsTable->calc_currency = $this->helper->getVendorCurrency($this->virtuemart_vendor_id);
				$this->calcsTable->calc_shopper_published = 1;
				$this->calcsTable->calc_vendor_published = 1;
				$this->calcsTable->created_on = $this->date->toSql();
				$this->calcsTable->created_by = $this->userId;
				$this->calcsTable->modified_on = $this->date->toSql();
				$this->calcsTable->modified_by = $this->userId;
				$this->calcsTable->store();
			}

			$this->setState('product_tax_id', $this->calcsTable->get('virtuemart_calc_id'));
		}
	}

	/**
	 * Create image name.
	 *
	 * Check if the user wants to have CSVI VirtueMart create the image names if so
	 * create the image names without path.
	 *
	 * @param   int  $ordering  The number to apply to a generated image name.
	 *
	 * @return  string  The name of the image.
	 *
	 * @since   3.0
	 */
	private function createImageName($ordering = 0)
	{
		$this->log->add('Generate image name', false);

		// Create extension
		$ext = $this->template->get('autogenerateext');

		// Check if the user wants to convert the images to a different type
		switch ($this->template->get('type_generate_image_name'))
		{
			case 'product_sku':
				$this->log->add('Create name from product SKU', false);

				if ($this->getState('product_sku', false))
				{
					$name = $this->getState('product_sku');
				}
				else
				{
					$this->log->addStats('error', 'COM_CSVI_CANNOT_FIND_PRODUCT_SKU');

					return false;
				}
				break;
			case 'product_name':
				$this->log->add('Create name from product name', false);

				if ($this->productLangTable->get('product_name', false))
				{
					$name = str_replace(' ', '_', $this->productLangTable->get('product_name'));
				}
				else
				{
					$this->log->addStats('error', 'COM_CSVI_CANNOT_FIND_PRODUCT_NAME');

					return false;
				}
				break;
			case 'product_id':
				$this->log->add('Create name from product ID', false);

				if ($this->getState('virtuemart_product_id'))
				{
					$name = $this->getState('virtuemart_product_id');
				}
				else
				{
					$this->log->addStats('error', 'COM_CSVI_CANNOT_FIND_PRODUCT_ID');

					return false;
				}
				break;
			case 'random':
				$this->log->add('Create a random name', false);
				$name = mt_rand();
				break;
			default:
				$this->log->addStats('error', 'COM_CSVI_CANNOT_FIND_PRODUCT_SKU');

				return false;
				break;
		}

		// Build the new name
		if ($ordering > 0)
		{
			$imageName = $name . '_' . $ordering . '.' . $ext;
		}
		else
		{
			$imageName = $name . '.' . $ext;
		}

		$this->log->add('Created image name: ' . $imageName, false);

		// Check if the user is supplying image data
		if (!$this->getState('file_url', false))
		{
			$this->setState('file_url', $imageName);
		}

		return $imageName;
	}

	/**
	 * Process custom fields.
	 *
	 * @return  void.
	 *
	 * @since   4.0
	 *
	 * @throws  \Exception
	 */
	private function processCustomFields()
	{
		// Get the values
		$values       = explode('~', $this->getState('custom_value'));
		$prices       = explode('~', $this->getState('custom_price'));
		$params       = explode('~', $this->getState('custom_param'));
		$titles       = explode('~', $this->getState('custom_title'));
		$ordering     = explode('~', $this->getState('custom_ordering'));
		$multiples    = explode('~', $this->getState('custom_multiple'));
		$deletes      = explode('~', $this->getState('custom_delete'));
		$overrider    = explode('~', $this->getState('custom_override'));
		$disabler     = explode('~', $this->getState('custom_disabler'));
		$newLangArray = $this->cf4allMultiLangValues();

		// Delete all custom fields
		if (!empty($deletes))
		{
			foreach ($deletes as $key => $value)
			{
				if ($value === 'Y' && isset($titles[$key]))
				{
					// Find the custom details
					$query = $this->db->getQuery(true)
						->select('virtuemart_custom_id')
						->from('#__virtuemart_customs')
						->where($this->db->quoteName('custom_title') . ' = ' . $this->db->quote($titles[$key]));
					$this->db->setQuery($query);
					$virtuemart_custom_id = $this->db->loadResult();

					// Delete the custom entry if it exists
					if ($virtuemart_custom_id)
					{
						$query = $this->db->getQuery(true)
							->delete($this->db->quoteName('#__virtuemart_product_customfields'))
							->where($this->db->quoteName('virtuemart_product_id') . ' = ' . $this->db->quote($this->virtuemart_product_id))
							->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom_id);
						$this->db->setQuery($query)->execute();
						$this->log->add('COM_CSVI_REMOVE_EXISTING_CUSTOM_VALUES', true);
					}
				}
			}
		}

		// Process all fields
		if (count($values) == count($titles))
		{
			// Get the product ID
			$virtuemart_product_id = $this->getState('virtuemart_product_id', false);

			// Process the custom values
			foreach ($values as $key => $value)
			{
				if ($value && !array_key_exists($value, $this->pluginCheck) && !$this->getState('child_product', false))
				{
					// Check if the required plugins are installed
					$this->pluginCheck[$value] = $this->dispatcher->trigger(
						'checkIfPluginInstalled',
						array(
							'plugin' => $value
						)
					);

					if (empty($this->pluginCheck[$value]) && (in_array($titles[$key], $this->stockableCustomFields)))
					{
						$this->log->add('CSVI Stockable custom fields plugin is not installed');
						$this->log->addStats('Error', 'COM_CSVI_STOCKABLECUSTOMFIELDS_NOT_INSTALLED');
					}

					if (empty($this->pluginCheck[$value]) && in_array($titles[$key], $this->customFieldsForAll))
					{
						$this->log->add('CSVI Custom fields for all plugin is not installed');
						$this->log->addStats('Error', 'COM_CSVI_CUSTOMFIELDSFORALL_NOT_INSTALLED');
					}
				}

				// We need to clean the custom titles otherwise values are not cleaned
				if (isset($multiples[$key]) && strtoupper($multiples[$key]) === 'N')
				{
					unset($this->customTitles[$virtuemart_product_id][$titles[$key]]);
				}

				// Check if the value is not deleted
				if ((null === $this->getState('custom_delete')) || (isset($deletes[$key]) && $deletes[$key] !== 'Y'))
				{
					// Get the custom ID
					if (!isset($this->customTitles[$virtuemart_product_id][$titles[$key]]))
					{
						$query = $this->db->getQuery(true)
							->select($this->db->quoteName('custom_parent_id') . ',' . $this->db->quoteName('virtuemart_custom_id'))
							->from($this->db->quoteName('#__virtuemart_customs'))
							->where($this->db->quoteName('custom_title') . ' = ' . $this->db->quote($titles[$key]))
							->where(
								$this->db->quoteName('field_type')
								. ' NOT IN ('
								. $this->db->quote('C') . ','
								. $this->db->quote('R') . ','
								. $this->db->quote('Z')
								. ')'
							);

						$this->db->setQuery($query);

						$virtuemart_custom = $this->db->loadObject();
						$this->log->add('Find the custom field ID');

						if ($virtuemart_custom)
						{
							$this->customTitles[$virtuemart_product_id][$titles[$key]] = $virtuemart_custom;

							// Remove disabler override fields if any
							$this->dispatcher->trigger(
								'clearDisablerOverrideValues',
								array(
									'plugin' => $value,
									'virtuemart_product_id' => $virtuemart_product_id,
									'log' => $this->log
								)
							);

							// Empty out any existing values
							if ($virtuemart_custom->virtuemart_custom_id)
							{
								$query = $this->db->getQuery(true)
									->delete($this->db->quoteName('#__virtuemart_product_customfields'))
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
									->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom->virtuemart_custom_id);
								$this->db->setQuery($query)->execute();
								$this->log->add('Remove existing customfield values');
							}

							// Check if any custom plugins need to be emptied as well
							$this->dispatcher->trigger(
								'clearCustomValues',
								array(
									'plugin' => $value,
									'params' => (isset($params[$key])) ? $params[$key] : '',
									'virtuemart_product_id' => $virtuemart_product_id,
									'virtuemart_custom_id' => $virtuemart_custom->virtuemart_custom_id,
									'log' => $this->log
								)
							);
						}
						else
						{
							$this->log->add('No custom field ID found', false);
							$virtuemart_custom = false;
						}
					}
					else
					{
						$virtuemart_custom = $this->customTitles[$virtuemart_product_id][$titles[$key]];
					}

					if ($virtuemart_custom)
					{
						// Set the product ID
						$this->productCustomfieldTable->virtuemart_product_id = $virtuemart_product_id;
						$this->productCustomfieldTable->virtuemart_custom_id = $virtuemart_custom->virtuemart_custom_id;

						$this->productCustomfieldTable->customfield_value = $value;

						if (isset($prices[$key]))
						{
							$this->productCustomfieldTable->customfield_price = $this->toPeriod($prices[$key]);
						}

						// Set the default ordering if none set in import file
						$this->productCustomfieldTable->ordering = $key;

						if (isset($ordering[$key]))
						{
							$this->productCustomfieldTable->ordering = $ordering[$key];
						}

						// Fire the plugin to get the result
						$result = $this->dispatcher->trigger(
							'getCustomParam',
							array(
								'plugin' => $value,
								'params' => (isset($params[$key])) ? $params[$key] : '',
								'virtuemart_product_id' => $virtuemart_product_id,
								'virtuemart_custom_id' => $virtuemart_custom->virtuemart_custom_id,
								'log' => $this->log
							)
						);

						if (is_array($result) && (0 !== count($result)))
						{
							$this->productCustomfieldTable->customfield_params = $result[0];
						}

						if ($result === false || (0 === count($result)))
						{
							$this->productCustomfieldTable->customfield_params = (isset($params[$key])) ? $params[$key] : '';
						}

						// Check for an existing entry
						if (!$this->productCustomfieldTable->check())
						{
							$this->productCustomfieldTable->created_on = $this->date->toSql();
							$this->productCustomfieldTable->created_by = $this->userId;
						}
						elseif (isset($multiples[$key]) && strtoupper($multiples[$key]) === 'Y')
						{
							$this->productCustomfieldTable->virtuemart_customfield_id = null;
						}

						// Set a modified date
						if (!isset($this->modified_on))
						{
							$this->productCustomfieldTable->modified_on = $this->date->toSql();
							$this->productCustomfieldTable->modified_by = $this->userId;
						}
						else
						{
							$this->productCustomfieldTable->modified_on = $this->modified_on;
							$this->productCustomfieldTable->modified_by = $this->userId;
						}

						// Check for custom disabler and override fields
						if (isset($overrider[$key]) ||  isset($disabler[$key]))
						{
							if ($virtuemart_custom->virtuemart_custom_id > 0)
							{
								// Load the parent custom field id
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('virtuemart_customfield_id'))
									->from($this->db->quoteName('#__virtuemart_product_customfields'))
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $this->getState('product_parent_id'))
									->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom->virtuemart_custom_id)
									->order($this->db->quoteName('ordering'));
								$this->db->setQuery($query);
								$parentCustomfieldId   = $this->db->loadResult();
								$parent_customfield_id = 0;

								if ($parentCustomfieldId)
								{
									$parent_customfield_id = $parentCustomfieldId;
								}

								if (isset($disabler[$key]))
								{
									switch (strtoupper($disabler[$key]))
									{
										case 'Y':
											$this->productCustomfieldTable->disabler = $parent_customfield_id;
											break;
										case 'N':
											$this->productCustomfieldTable->disabler = 0;
											break;
									}
								}

								if (isset($overrider[$key]))
								{
									switch (strtoupper($overrider[$key]))
									{
										case 'Y':
											$this->productCustomfieldTable->override = $parent_customfield_id;
											break;
										case 'N':
											$this->productCustomfieldTable->override = 0;
											break;
									}
								}

								$this->log->add('Custom override and disabler fields updated');
							}
						}

						// Store the custom field
						if ($this->productCustomfieldTable->store())
						{
							// Fire the plugin to do any after save management
							$this->dispatcher->trigger(
								'onAfterStoreCustomfield',
								array(
									'plugin' => $value,
									'params' => (isset($params[$key])) ? $params[$key] : '',
									'virtuemart_product_id' => $virtuemart_product_id,
									'virtuemart_custom_id' => $virtuemart_custom->virtuemart_custom_id,
									'virtuemart_customfield_id' => $this->productCustomfieldTable->virtuemart_customfield_id,
									'log' => $this->log,
									'product_parent_id' => $this->getState('product_parent_id', false),
									'product_as_derived' => $this->getState('product_as_derived', false),
									'multiLang' => (isset($newLangArray[$key])) ? $newLangArray[$key] : '',
								)
							);

							if (isset($overrider[$key]) ||  isset($disabler[$key]))
							{
								$this->dispatcher->trigger(
									'saveDisablerOverride',
									array(
										'plugin'                    => $value,
										'virtuemart_product_id'     => $virtuemart_product_id,
										'virtuemart_custom_id'      => $virtuemart_custom->virtuemart_custom_id,
										'virtuemart_customfield_id' => $this->productCustomfieldTable->virtuemart_customfield_id,
										'log'                       => $this->log,
										'disabler'                  => (isset($disabler[$key])) ? $disabler[$key] : '',
										'override'                  => (isset($overrider[$key])) ? $overrider[$key] : '',
										'totaltitle'                => count($titles)
									)
								);
							}

							// Check if we need to add the parent field
							if ($virtuemart_custom->custom_parent_id > 0)
							{
								// Check if the custom parent is already set
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('virtuemart_customfield_id'))
									->from($this->db->quoteName('#__virtuemart_product_customfields'))
									->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . $virtuemart_custom->custom_parent_id)
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
								$this->db->setQuery($query);
								$cid = $this->db->loadResult();
								$this->log->add('Check if the custom parent is already set');

								if (empty($cid))
								{
									// Add the parent
									$query->clear()
										->from($this->db->quoteName('#__virtuemart_customs'))
										->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom->custom_parent_id)
										->select($this->db->quoteName('custom_value'));

									$this->db->setQuery($query);
									$parent_name = $this->db->loadResult();
									$this->productCustomfieldTable->virtuemart_customfield_id = null;
									$this->productCustomfieldTable->custom_price = null;
									$this->productCustomfieldTable->virtuemart_custom_id = $virtuemart_custom->custom_parent_id;
									$this->productCustomfieldTable->set('customfield_value', $parent_name);

									if ($this->productCustomfieldTable->store())
									{
										$this->log->add('Add the parent');
									}
								}
							}
						}
					}
				}
				else
				{
					// Check if we need to delete any plugin values
					$query = $this->db->getQuery(true)
						->select($this->db->quoteName('custom_parent_id') . ',' . $this->db->quoteName('virtuemart_custom_id'))
						->from($this->db->quoteName('#__virtuemart_customs'))
						->where($this->db->quoteName('custom_title') . ' = ' . $this->db->quote($titles[$key]));
					$this->db->setQuery($query);
					$virtuemart_custom = $this->db->loadObject();
					$this->log->add('Check if we need to delete any plugin values');

					if ($virtuemart_custom)
					{
						if ($value === 'param')
						{
							// Remove existing values for this parameter
							$query = $this->db->getQuery(true)
								->delete($this->db->quoteName('#__virtuemart_product_custom_plg_param_ref'))
								->where($this->db->quoteName('virtuemart_product_id') . '=' . (int) $virtuemart_product_id)
								->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom->virtuemart_custom_id);
							$this->db->setQuery($query)->execute();
						}
					}
				}

				// Reset the field
				$this->productCustomfieldTable->reset();
			}
		}
		else
		{
			$this->log->add('The number of custom titles is not equal to the number of custom values', false);
		}
	}

	/**
	 * Combine the ICEcat features.
	 *
	 * @return  void.
	 *
	 * @since   4.3
	 */
	private function icecatFeatures()
	{
		$table = '<table id="prod_features">';
		$table .= '<thead></thead>';
		$table .= '<tfoot></tfoot>';
		$table .= '<tbody>';

		foreach ($this->getState('features') as $details)
		{
			foreach ($details as $feature => $values)
			{
				$table .= '<tr><td colspan="2" class="feature">' . $feature . '</td></tr>';

				foreach ($values as $name => $value)
				{
					$table .= '<tr><td>' . $name . '</td><td>' . $value . '</td></tr>';
				}
			}
		}

		$table .= '</tbody>';
		$table .= '</table>';

		// Add the table to the product desc
		$this->setState('product_desc', $this->getState('product_desc') . $table);
	}

	/**
	 * Delete a product and its references.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   4.0
	 */
	private function deleteProduct()
	{
		// Deletion based on the real primary key
		$this->productTable->setKeyName('virtuemart_product_id');

		// Get the product ID
		$virtuemart_product_id = $this->getState('virtuemart_product_id');

		// Delete the product
		if ($this->productTable->delete($virtuemart_product_id))
		{
			// Delete product translations
			$languages = $this->csvihelper->getLanguages();
			$tables = $this->db->getTableList();

			foreach ($languages as $language)
			{
				$tableName = '#__virtuemart_products_' . strtolower(str_replace('-', '_', $language->lang_code));

				if (in_array($tableName, $tables))
				{
					$query = $this->db->getQuery(true);
					$query->delete($this->db->quotename($tableName));
					$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
					$this->db->setQuery($query)->execute();
					$this->log->add('Delete product language entries');
				}
			}

			// Delete category reference
			$query = $this->db->getQuery(true);
			$query->delete($this->db->quotename('#__virtuemart_product_categories'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete product category references');

			// Delete manufacturer reference
			$query->clear();
			$query->delete($this->db->quotename('#__virtuemart_product_manufacturers'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete product manufacturer references');

			// Reset child parent reference
			$query->clear();
			$query->update($this->db->quotename('#__virtuemart_products'));
			$query->set($this->db->quotename('product_parent_id') . ' = 0');
			$query->where($this->db->quotename('product_parent_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Reset the child parent reference');

			// Delete prices
			$query->clear();
			$query->delete($this->db->quotename('#__virtuemart_product_prices'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete product prices');

			// Delete shopper groups
			$query->clear();
			$query->delete($this->db->quotename('#__virtuemart_product_shoppergroups'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete product shopper groups');

			// Delete custom fields
			$query->clear();
			$query->delete($this->db->quotename('#__virtuemart_product_customfields'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete product custom fields');

			// Delete media
			$query->clear();
			$query->delete($this->db->quotename('#__virtuemart_product_medias'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete product media references');

			// Delete ratings
			$query->clear();
			$query->delete($this->db->quotename('#__virtuemart_ratings'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete the product ratings');

			// Delete rating reviews
			$query->clear();
			$query->delete($this->db->quotename('#__virtuemart_rating_reviews'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete the product rating reviews');

			// Delete rating votes
			$query->clear();
			$query->delete($this->db->quotename('#__virtuemart_rating_votes'));
			$query->where($this->db->quotename('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
			$this->db->setQuery($query)->execute();
			$this->log->add('Delete the product rating votes');

			$this->log->addStats('deleted', \JText::sprintf('COM_CSVI_PRODUCT_DELETED', $this->getState('recordIdentity')));
		}
		else
		{
			$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_PRODUCT_NOT_DELETED', $this->getState('recordIdentity')));
		}

		return true;
	}

	/**
	 * Convert the product SKU to product ID in the parent properties.
	 *
	 * @param   bool  $child  Set if children are also being imported
	 *
	 * @return  void.
	 *
	 * @since   1.0
	 *
	 * @throws  \Exception
	 */
	private function processParentValues($child=true)
	{
		if ($child && $this->getState('product_sku', false) && $this->getState('product_parent_id', false))
		{
			$param_sku = $this->productTable->get('virtuemart_product_id');
			$sku = $this->getState('product_sku');

			// Load the values
			$query = $this->db->getQuery(true)
				->select('customfield_params')
				->from('#__virtuemart_product_customfields')
				->where('virtuemart_product_id = ' . (int) $this->getState('product_parent_id'))
				->where('customfield_value = ' . $this->db->quote('stockable'));
			$this->db->setQuery($query);
			$params = $this->db->loadResult();
			$values = json_decode($params);

			// Replace the key if it exists
			if (isset($values->child->$sku))
			{
				$values->child->$param_sku = $values->child->$sku;
				unset($values->child->$sku);

				// Store the values
				$query = $this->db->getQuery(true)
					->update('#__virtuemart_product_customfields')
					->set('customfield_params = ' . $this->db->quote(json_encode($values)))
					->where('virtuemart_product_id = ' . (int) $this->getState('product_parent_id'))
					->where('customfield_value = ' . $this->db->quote('stockable'));
				$this->db->setQuery($query)->execute();

				$this->log->add('COM_CSVI_DEBUG_STORE_PARENT_VALUE');
			}
			else
			{
				$this->log->add('No parent found');
			}
		}
		elseif (!$child)
		{
			// Only parents are imported

			// Get all the parameters
			$query = $this->db->getQuery(true)
				->select('customfield_params')
				->from('#__virtuemart_product_customfields')
				->where('virtuemart_product_id = ' . (int) $this->productTable->get('virtuemart_product_id'))
				->where('customfield_value = ' . $this->db->quote('stockable'));
			$this->db->setQuery($query);
			$params = $this->db->loadResult();
			$values = json_decode($params);

			if (is_object($values))
			{
				// Replace the key if it exists
				foreach ($values->child as $child_sku => $details)
				{
					// Get the product ID of the child
					$query = $this->db->getQuery(true)
						->select('virtuemart_product_id')
						->from('#__virtuemart_products')
						->where('product_sku = ' . $this->db->quote($child_sku));
					$this->db->setQuery($query);
					$child_id = $this->db->loadResult();

					if ($child_id)
					{
						$values->child->$child_id = $details;
						unset($values->child->$child_sku);
					}
					else
					{
						$this->log->add('COM_CSVI_DEBUG_NO_CHILD_VALUE_FOUND', true);
					}
				}

				// Store the values
				$query = $this->db->getQuery(true)
					->update($this->db->quoteName('#__virtuemart_product_customfields'))
					->set($this->db->quoteName('customfield_params') . ' = ' . $this->db->quote(json_encode($values)))
					->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $this->productTable->get('virtuemart_product_id'))
					->where($this->db->quoteName('customfield_value') . ' = ' . $this->db->quote('stockable'));
				$this->db->setQuery($query)->execute();
				$this->log->add('COM_CSVI_DEBUG_STORE_PARENT_VALUE');
			}
			else
			{
				$this->log->add('COM_CSVI_DEBUG_NO_PARENT_VALUE_FOUND');
			}
		}
	}

	/**
	 * Process custom fields that are used as available field.
	 *
	 * @return  void.
	 *
	 * @since   4.4.1
	 *
	 * @throws  \Exception
	 */
	private function processCustomAvailableFields()
	{
		// Create the queries
		if (count($this->customFields) > 0)
		{
			foreach ($this->customFields as $field)
			{
				if (isset($field->title) && !empty($field->title))
				{
					$title = $field->title;
					$this->log->add('Processing custom available field: ' . $title, false);

					if ($this->getState($title, false) !== false)
					{
						// Check if we need to do any formatting
						switch ($field->field_type)
						{
							case 'D':
								// Date format needs to be YYYY/MM/DD
								$value = $this->convertDate($this->getState($title), 'date');
								break;
							case 'M':
								// The media field uses a name and we need an ID
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('virtuemart_media_id'))
									->from($this->db->quoteName('#__virtuemart_medias'))
									->where($this->db->quoteName('file_url') . ' = ' . $this->db->quote($this->getState($title)));
								$this->db->setQuery($query);
								$value = $this->db->loadResult();
								$this->log->add(($value) ? 'Found file_url value: ' . $value : 'No file_url value found');
								break;
							default:
								$value = $this->getState($title, false);
								break;
						}

						// Insert query if it is not empty
						if ($value !== false)
						{
							$virtuemart_product_id = $this->getState('virtuemart_product_id');

							// Check if the custom field exists
							$query = $this->db->getQuery(true)
								->select($this->db->quoteName('virtuemart_customfield_id'))
								->from($this->db->quoteName('#__virtuemart_product_customfields'))
								->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
								->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $field->id);
							$this->db->setQuery($query);
							$virtuemart_customfield_id = $this->db->loadResult();

							if ($virtuemart_customfield_id)
							{
								$query = $this->db->getQuery(true)
									->update($this->db->quoteName('#__virtuemart_product_customfields'))
									->set($this->db->quoteName('customfield_value') . ' = ' . $this->db->quote($value))
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
									->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $field->id);
								$this->db->setQuery($query)->execute();
								$this->log->add('Update custom available field value');
							}
							else
							{
								// Find out the next ordering position
								$query->clear()
									->select('MAX(' . $this->db->quoteName('ordering') . ') + 1 AS' . $this->db->quoteName('max'))
									->from($this->db->quoteName('#__virtuemart_product_customfields'))
									->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
									->order($this->db->quoteName('ordering'));
								$this->db->setQuery($query);
								$newOrdering = $this->db->loadResult();

								// Check if the parent is already set
								if ($field->custom_parent_id > 0)
								{
									// Check if the custom parent is already set
									$query = $this->db->getQuery(true)
										->select($this->db->quoteName('virtuemart_customfield_id'))
										->from($this->db->quoteName('#__virtuemart_product_customfields'))
										->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . $field->custom_parent_id)
										->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id);
									$this->db->setQuery($query);
									$cid = $this->db->loadResult();
									$this->log->add('Check if the custom parent is already set');

									if (empty($cid))
									{
										// Add the parent
										$query->clear()
											->from($this->db->quoteName('#__virtuemart_customs'))
											->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $field->custom_parent_id)
											->select($this->db->quoteName('custom_value'));

										$this->db->setQuery($query);
										$parent_name = $this->db->loadResult();
										$this->productCustomfieldTable->reset();
										$this->productCustomfieldTable->virtuemart_customfield_id = null;
										$this->productCustomfieldTable->virtuemart_product_id     = $virtuemart_product_id;
										$this->productCustomfieldTable->custom_price              = null;
										$this->productCustomfieldTable->virtuemart_custom_id      = $field->custom_parent_id;
										$this->productCustomfieldTable->ordering                  = $newOrdering;
										$this->productCustomfieldTable->set('customfield_value', $parent_name);

										if ($this->productCustomfieldTable->store())
										{
											$this->log->add('Add the parent');
										}
									}
								}

								// Set the columns
								$columns = array(
									$this->db->quoteName('virtuemart_product_id'),
									$this->db->quoteName('virtuemart_custom_id'),
									$this->db->quoteName('customfield_value'),
									$this->db->quoteName('ordering')
								);

								// Set the values
								$values = array(
									(int) $virtuemart_product_id,
									(int) $field->id,
									$this->db->quote($value),
									(int) ++$newOrdering
								);

								// Check if we have a child product
								if ($this->getState('child_product', false))
								{
									// Get the parent customfield_id
									$query = $this->db->getQuery(true)
										->select($this->db->quoteName('virtuemart_customfield_id'))
										->from($this->db->quoteName('#__virtuemart_product_customfields'))
										->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
										->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $field->id);
									$this->db->setQuery($query);
									$parent_customfield_id = $this->db->loadResult();

									if ($parent_customfield_id)
									{
										$columns[] = $this->db->quoteName('override');
										$values[]  = (int) $parent_customfield_id;
									}
								}

								$query = $this->db->getQuery(true)
									->insert($this->db->quoteName('#__virtuemart_product_customfields'))
									->columns($columns)
									->values(implode(',', $values));
								$this->db->setQuery($query)->execute();
								$this->log->add('Store custom available field');
							}
						}
					}
				}
			}
		}
		else
		{
			$this->log->add('No custom available fields found');
		}
	}

	/**
	 * Process multi variant fields that are used as available fields.
	 *
	 * selectoptions=[{"voption":"product_length","clabel":"","values":"40.0000\r\n42.0000\r\n45.0000\r\n"},{"voption":"clabels","clabel":"Material","values":"Cotton\r\nLeather\r\nSilver"},{"voption":"clabels","clabel":"Color","values":"white\r\nred\r\ngreen\r\nblue"},{"voption":"clabels","clabel":"Print","values":"Glossy\r\nMat"},{"voption":"product_width","clabel":"","values":"1.0000\r\n2.0000\r\n3.0000"}]|clabels=0|options={"74":["40.0000","Cotton","white","Glossy",""],"75":["40.0000","Cotton","red","0",""],"76":["40.0000","Cotton","green","0",""],"77":["40.0000","Cotton","blue","Mat","1.0000"],"78":["40.0000","Leather","red","0",""],"79":["40.0000","Leather","blue","0",""],"80":["40.0000","Silver","white","0",""],"81":["42.0000","Cotton","white","0",""],"82":["42.0000","Cotton","red","0",""],"83":["42.0000","Cotton","blue","0",""],"84":["42.0000","Cotton","green","0",""],"85":["42.0000","Leather","red","0",""],"86":["42.0000","Leather","green","0",""],"87":["42.0000","Leather","blue","0",""],"88":["42.0000","Silver","white","0",""],"89":["45.0000","Cotton","white","0",""],"90":["45.0000","Cotton","red","0",""],"91":["45.0000","Cotton","green","0",""]}|
	 * selectoptions=[{"voption":"product_length","clabel":"","values":"40.0000\r\n42.0000\r\n45.0000\r\n"},{"voption":"clabels","clabel":"Material","values":"Cotton\r\nLeather\r\nSilver"},{"voption":"clabels","clabel":"Color","values":"white\r\nred\r\ngreen\r\nblue"},{"voption":"clabels","clabel":"Print","values":"Glossy\r\nMat"},{"voption":"product_width","clabel":"","values":"1.0000\r\n2.0000\r\n3.0000"}]|clabels=0|options={"74":["40.0000","Cotton","green","Glossy",""],"75":["40.0000","Cotton","red","0",""],"76":["40.0000","Cotton","green","0",""],"77":["40.0000","Cotton","blue","Mat","1.0000"],"78":["40.0000","Leather","red","0",""],"79":["40.0000","Leather","blue","0",""],"80":["40.0000","Silver","white","0",""],"81":["42.0000","Cotton","white","0",""],"82":["42.0000","Cotton","red","0",""],"83":["42.0000","Cotton","blue","0",""],"84":["42.0000","Cotton","green","0",""],"85":["42.0000","Leather","red","0",""],"86":["42.0000","Leather","green","0",""],"87":["42.0000","Leather","blue","0",""],"88":["42.0000","Silver","white","0",""],"89":["45.0000","Cotton","white","0",""],"90":["45.0000","Cotton","red","0",""],"91":["45.0000","Cotton","green","0",""]}|
	 * usecanonical=0|selectoptions=[{"voption":"clabels","clabel":"Material","values":[]},{"voption":"clabels","clabel":"product_length","values":[]},{"voption":"clabels","clabel":"Color","values":[]},{"voption":"clabels","clabel":"Print","values":[]},{"voption":"clabels","clabel":"product_width","values":[]}]|clabels=[0]|options={"74":["Cotton","40.00","white","Glossy","2.00"],"75":["Cotton","40.00","red","0",""],"76":["Cotton","40.00","green","0",""],"77":["Cotton","40.00","blue","Mat","1.00"],"78":["Leather","40.00","red","0",""],"79":["Leather","40.00","blue","0",""],"80":["Silver","40.00","white","0",""],"81":["Cotton","42.00","white","0",""],"82":["Cotton","42.00","red","0",""],"83":["Cotton","42.00","blue","0",""],"84":["Cotton","42.00","green","0",""],"85":["Leather","42.00","red","0",""],"86":["Leather","42.00","green","0",""],"87":["Leather","42.00","blue","0",""],"88":["Silver","42.00","white","0",""],"89":["Cotton","45.00","white","0",""],"90":["Cotton","45.00","red","0",""],"91":["Cotton","45.00","green","0",""]}|
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 *
	 * @throws  \Exception
	 */
	private function processMultiVariantFields()
	{
		// Check if there are any fields to process
		if (count($this->multivariantFields) > 0)
		{
			$this->log->add('Process multi-variant fields', false);

			// Setup the basic variables
			$usecanonical          = 0;
			$showlabels            = 0;
			$sCustomId             = 0;
			$selectoptions         = array();
			$clabels               = 0;
			$options               = new \stdClass;
			$multiFields           = array();
			$virtuemart_product_id = $this->getState('virtuemart_product_id');
			$values                = array();
			$selectoptionValues    = array();

			// Check if we have a child product
			if ($this->getState('child_product', false))
			{
				// Get the existing customfield_params
				$query = $this->db->getQuery(true)
					->select($this->db->quoteName('customfield_params'))
					->from($this->db->quoteName('#__virtuemart_product_customfields'))
					->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $this->getState('product_parent_id'));
				$this->db->setQuery($query);
				$customfield_params = $this->db->loadResult();
				$this->log->add('Load the existing customfield_params for product parent');

				// Check if it has any content
				if ($customfield_params)
				{
					// Get the different segments
					$segments = explode('|', $customfield_params);

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

							if ($group === 'selectoptions' && is_array($selectoptions))
							{
								foreach ($selectoptions as $selectoption)
								{
									$field = trim($selectoption->voption);

									if ($selectoption->voption === 'clabels')
									{
										$field = trim($selectoption->clabel);
									}

									$multiFields[] = $field;
								}
							}
						}
					}
				}
			}
			else
			{
				$query = $this->db->getQuery(true)
					->select($this->db->quoteName('virtuemart_custom_id'))
					->from($this->db->quoteName('#__virtuemart_customs'))
					->where($this->db->quoteName('custom_title') . ' = ' . $this->db->quote($this->getState('multi_variant_title')))
					->where($this->db->quoteName('field_type') . ' = ' . $this->db->quote('C'));
				$this->db->setQuery($query);
				$virtuemart_custom_id = $this->db->loadResult();
				$this->log->add('Find the multi variant custom field entry');

				if ($virtuemart_custom_id)
				{
					// Check if there is an entry in the virtuemart_product_customfields table
					$query->clear()
						->select($this->db->quoteName('virtuemart_customfield_id'))
						->from($this->db->quoteName('#__virtuemart_product_customfields'))
						->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $this->getState('virtuemart_product_id'))
						->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom_id);
					$this->db->setQuery($query);
					$virtuemart_customfield_id = $this->db->loadResult();
					$this->log->add('Find the multi variant product entry');

					// There is no entry yet, we need to create one
					if (!$virtuemart_customfield_id)
					{
						$query->clear()
							->insert($this->db->quoteName('#__virtuemart_product_customfields'))
							->columns(
								array(
									$this->db->quoteName('virtuemart_product_id'),
									$this->db->quoteName('virtuemart_custom_id'),
									$this->db->quoteName('published'),
									$this->db->quoteName('created_on'),
									$this->db->quoteName('created_by'),
								)
							)
							->values(
								(int) $this->getState('virtuemart_product_id') . ', '
								. (int) $virtuemart_custom_id . ', '
								. '1, '
								. $this->db->quote($this->date->toSql()) . ', '
								. (int) $this->userId
							);
						$this->db->setQuery($query)->execute();
						$this->log->add('Insert the multi variant custom field entry');
					}

					// Prepare the query for adding the fields to the list of available fields
					$mvquery = $this->db->getQuery(true)
						->insert($this->db->quoteName('#__csvi_availablefields'))
						->columns($this->db->quoteName(array('csvi_name', 'component_name', 'component_table', 'component', 'action')));
					$mvclear = $this->db->getQuery(true)
						->delete($this->db->quoteName('#__csvi_availablefields'))
						->where($this->db->quoteName('component_table') . ' = ' . $this->db->quote('product'))
						->where($this->db->quoteName('component') . ' = ' . $this->db->quote('com_virtuemart'));
					$mvexecute = false;
					$mvclearWhere = array();

					// This is a parent product, construct the basic structure
					foreach ($this->multivariantFields as $multiField)
					{
						$field = new \stdClass;
						$field->voption = 'clabels';
						$field->clabel = $multiField;
						$field->values = '';

						$selectoptions[] = $field;

						// Check if the available field already exists
						if (!$this->fields->isFieldAvailable($multiField))
						{
							// Add the values for the list of available fields
							$mvquery->values(
								$this->db->quote($multiField) . ',' .
								$this->db->quote($multiField) . ',' .
								$this->db->quote('product') . ',' .
								$this->db->quote('com_virtuemart') . ',' .
								$this->db->quote('import')
							);
							$mvquery->values(
								$this->db->quote($multiField) . ',' .
								$this->db->quote($multiField) . ',' .
								$this->db->quote('product') . ',' .
								$this->db->quote('com_virtuemart') . ',' .
								$this->db->quote('export')
							);

							// Add the fields to remove to prevent SQL errors
							$mvclearWhere[] = $this->db->quoteName('csvi_name') . ' = ' . $this->db->quote($multiField);

							$mvexecute = true;
						}
					}

					// Add the available fields
					if ($mvexecute)
					{
						$mvclear->where(implode(' OR ', $mvclearWhere));
						$this->db->setQuery($mvclear)->execute();
						$this->db->setQuery($mvquery)->execute();
					}

					$multiFields = $this->multivariantFields;
				}
			}

			foreach ($multiFields as $multiField)
			{
				$importvalue = $this->getState($multiField, '0');

				if (!empty($importvalue))
				{
					if (0 === count($selectoptions))
					{
						$selectoptionValues[$multiField][] = $importvalue;
					}
					else
					{
						// Add the selectoptions value
						foreach ($selectoptions as $selectkey => $selectoption)
						{
							if ($selectoption->clabel == $multiField)
							{
								$selectoptionValues[$multiField][] = $importvalue;
							}
						}
					}
				}

				// Collect the options value
				$values[] = $importvalue;
			}

			// Add the values to the options value
			if (!isset($options))
			{
				$options = new \stdClass;
			}

			// Add the values to the product
			$options->$virtuemart_product_id = $values;

			// Clean up and add the selectionoptions values
			foreach ($selectoptionValues as $clabel => $values)
			{
				foreach ($selectoptions as $selectoptionkey => $selectoption)
				{
					if ($clabel == $selectoption->clabel)
					{
						$existingValues = array();

						if ($selectoption->values)
						{
							$existingValues = explode("\r\n", $selectoption->values);
						}

						$selectoption->values = array();

						if ($values)
						{
							$selectoption->values = implode("\r\n", array_unique($values));

							if ($existingValues)
							{
								$selectoption->values = implode("\r\n", array_unique(array_merge($existingValues, $values)));
							}
						}

						$selectoptions[$selectoptionkey] = $selectoption;
					}
				}
			}

			// Construct the string
			$string = array
			(
				'usecanonical=' . json_encode($usecanonical),
				'showlabels=' . json_encode($showlabels),
				'sCustomId=' . json_encode($sCustomId),
				'selectoptions=' . json_encode($selectoptions),
				'clabels=' . json_encode($clabels),
				'options=' . json_encode($options),
				''
			);

			$customfield_params = implode('|', $string);

			$query = $this->db->getQuery(true)
				->update($this->db->quoteName('#__virtuemart_product_customfields'))
				->set($this->db->quoteName('customfield_params') . ' = ' . $this->db->quote($customfield_params))
				->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $this->getState('product_parent_id', $virtuemart_product_id));
			$this->db->setQuery($query)->execute();

			$this->log->add('Store the updated customfield_params');
		}
	}

	/**
	 * Process the shopper groups.
	 *
	 * @return  bool Returns true on OK | False on failure.
	 *
	 * @since   4.5.2
	 */
	private function processShopperGroup()
	{
		if ($this->getState('shopper_group_name', false))
		{
			// Get the shopper group names
			$names = explode('|', $this->getState('shopper_group_name'));

			foreach ($names as $name)
			{
				$data = array();
				$data['virtuemart_shoppergroup_id'] = $this->helper->getShopperGroupId($name);
				$data['virtuemart_product_id'] = $this->getState('virtuemart_product_id');

				// Process only if shopper group id is not zero
				if ($data['virtuemart_shoppergroup_id'])
				{
					// Set the shopper group ID for other updates
					$this->setState('virtuemart_shoppergroup_id', $data['virtuemart_shoppergroup_id']);

					// Bind the data to check
					$this->productShoppergroupTable->bind($data);

					// Check if a product - shopper group relation exists
					if (!$this->productShoppergroupTable->check())
					{
						if (!$this->productShoppergroupTable->store())
						{
							$this->log->addStats(
								'incorrect',
								\JText::sprintf('COM_CSVI_PRODUCT_SHOPPERGROUP_NOT_ADDED', $this->productShoppergroupTable->getError())
							);

							return false;
						}
					}

					// Clean up
					$this->productShoppergroupTable->reset();
				}
				else
				{
					$this->log->add('Shopper group name not found ' . $name, false);
				}
			}
		}

		return true;
	}

	/**
	 * Process product params
	 *
	 * @return  void.
	 *
	 * @since   7.2.0
	 */
	private function processProductParams()
	{
		$product_params   = $this->getState('product_params', false);

		// User is importing the product_params field, no further processing
		if ($product_params)
		{
			return;
		}

		$min_order_level  = $this->getState('min_order_level', false);
		$max_order_level  = $this->getState('max_order_level', false);
		$product_box      = $this->getState('product_box', false);
		$step_order_level = $this->getState('step_order_level', false);

		if (!$min_order_level && !$max_order_level && !$product_box && !$step_order_level)
		{
			return;
		}

		$newParameter = array();

		$existingParams = $this->productTable->get('product_params');

		if (!$existingParams)
		{
			$existingParams = 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""';
		}

		$existingValues = explode('|', $existingParams);

		foreach ($existingValues as $existingValue)
		{
			$individualParameter = explode('=', $existingValue);

			if ($this->getState($individualParameter[0], ''))
			{
				$newParameter[] = $individualParameter[0] . '="' . $this->getState($individualParameter[0], '') . '"';
			}
			elseif (isset($individualParameter[1]) && str_replace('"', '', $individualParameter[1] !== ''))
			{
				$newParameter[] = $individualParameter[0] . '=' . $individualParameter[1] . '';
			}
			else
			{
				$newParameter[] = $individualParameter[0] . '=""';
			}
		}

		$prodParams = implode('|', $newParameter);
		$this->setState('product_params', $prodParams);
	}

	/**
	 * Get a list of stockable custom fields.
	 *
	 * @return  void.
	 *
	 * @since   7.3.0
	 *
	 * @throws  \Exception
	 */
	private function loadStockableCustomFields()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('custom_title'))
			->from($this->db->quoteName('#__virtuemart_customs'))
			->where($this->db->quoteName('custom_element') . ' = ' . $this->db->quote('stockablecustomfields'))
			->where($this->db->quoteName('field_type') . ' = ' . $this->db->quote('E'));

		$this->db->setQuery($query);
		$customFields = $this->db->loadObject();

		if ($customFields)
		{
			foreach ($customFields as $fields)
			{
				$this->stockableCustomFields[] = $fields;
			}
		}

		$this->log->add('Load stockable custom fields');
	}

	/**
	 * Get a list of custom fields for all.
	 *
	 * @return  void.
	 *
	 * @since   7.3.0
	 *
	 * @throws  \Exception
	 */
	private function loadCustomFieldsForAll()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('custom_title'))
			->from($this->db->quoteName('#__virtuemart_customs'))
			->where($this->db->quoteName('custom_element') . ' = ' . $this->db->quote('customfieldsforall'))
			->where($this->db->quoteName('field_type') . ' = ' . $this->db->quote('E'));

		$this->db->setQuery($query);
		$customFields = $this->db->loadObject();

		if ($customFields)
		{
			foreach ($customFields as $fields)
			{
				$this->customFieldsForAll[] = $fields;
			}
		}

		$this->log->add('Load custom fields for all');
	}

	/**
	 * Get a multi language values of custom fields for all.
	 *
	 * @return  array.
	 *
	 * @since   7.5.0
	 *
	 * @throws  \Exception
	 */
	private function cf4allMultiLangValues()
	{
		$languages      = $this->helper->getActiveLanguages();
		$multiLangArray = array();

		foreach ($languages as $language)
		{
			if ($language !== 'en-GB' && $language !== 'en_gb')
			{
				$languageCode = strtolower(str_replace('-', '_', $language));
				$variable     = 'custom_param_' . $languageCode;

				if ($this->getState($variable))
				{
					$langParams = explode('~', $this->$variable);

					foreach ($langParams as $lparams)
					{
						$multiLangArray[][$languageCode] = $lparams;
					}
				}
			}
		}

		return $multiLangArray;
	}
}
