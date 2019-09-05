<?php
/**
 * @package     CSVI
 * @subpackage  Plugin.Replace
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2015 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Replaces values.
 *
 * @package     CSVI
 * @subpackage  Plugin.Customfieldsforall
 * @since       6.0
 */
class PlgCsviextCustomfieldsforall extends RantaiPluginDispatcher
{
	/**
	 * The unique ID of the plugin
	 *
	 * @var    string
	 * @since  6.0
	 */
	private $id = 'customfieldsforall';

	/**
	 * JDatabase handler
	 *
	 * @var    JDatabase
	 * @since  6.0
	 */
	protected $db = null;

	/**
	 * Construct the class.
	 *
	 * @since   6.0
	 */
	public function __construct()
	{
		// Set the dependencies
		$this->db = JFactory::getDbo();
	}

	/**
	 * Method to get the field options.
	 *
	 * @param   string         $plugin                 The ID of the plugin.
	 * @param   array          $params                 An array of settings.
	 * @param   int            $virtuemart_product_id  The product ID.
	 * @param   int            $virtuemart_custom_id   The custom ID.
	 * @param   CsviHelperLog  $log                    The CSVI logger.
	 *
	 * @return  void
	 *
	 * @since   6.0
	 *
	 * @throws  RuntimeException
	 */
	public function clearCustomValues($plugin, $params, $virtuemart_product_id, $virtuemart_custom_id, $log)
	{
		if ($plugin == $this->id)
		{
			// Get all the values
			$param_entries = explode('#', $params);

			foreach ($param_entries as $pkey => $pvalue)
			{
				$param_entries[$pkey] = $this->db->quote($pvalue);
			}

			// Load the CF4All value IDs
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('customsforall_value_id'))
				->from($this->db->quoteName('#__virtuemart_custom_plg_customsforall_values'))
				->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom_id);
			$this->db->setQuery($query);
			$valueIds = $this->db->loadColumn();

			$log->add('Load cf4all values for custom ID ' . $virtuemart_custom_id);

			// Remove existing values for this parameter and only if the product ID has already been handled
			if (is_array($valueIds) && !empty($valueIds))
			{
				$query = $this->db->getQuery(true)
					->delete($this->db->quoteName('#__virtuemart_product_custom_plg_customsforall'))
					->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
					->where($this->db->quoteName('customsforall_value_id') . ' IN (' . implode(',', $valueIds) . ')');
				$this->db->setQuery($query)->execute();

				$log->add('Delete existing cf4all values for product ID ' . $virtuemart_product_id);
			}
		}
	}

	/**
	 * Method to get the field options.
	 *
	 * @param   string         $plugin                 The ID of the plugin.
	 * @param   array          $params                 An array of settings.
	 * @param   int            $virtuemart_product_id  The product ID.
	 * @param   int            $virtuemart_custom_id   The custom ID.
	 * @param   CsviHelperLog  $log                    The CSVI logger.
	 *
	 * @return  mixed  String if plugin needs to handle request | False if plugin does not handle request.
	 *
	 * @since   6.0
	 */
	public function getCustomParam($plugin, $params, $virtuemart_product_id, $virtuemart_custom_id, $log)
	{
		if ($plugin === $this->id)
		{
			// There are no params
			return '';
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get the field options.
	 *
	 * @param   string         $plugin                     The ID of the plugin.
	 * @param   array          $params                     An array of settings.
	 * @param   int            $virtuemart_product_id      The product ID.
	 * @param   int            $virtuemart_custom_id       The custom ID.
	 * @param   int            $virtuemart_customfield_id  The custom field ID.
	 * @param   CsviHelperLog  $log                        The CSVI logger.
	 * @param   int            $product_parent_id          The product parent ID.
	 * @param   int            $productAsDerived           The product has to be derived product.
	 * @param   array          $multiLangValues            Multi language value array
	 *
	 * @return   mixed  Void if plugin needs to handle request | False if plugin does not handle request.
	 *
	 * @since   6.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function onAfterStoreCustomfield($plugin, $params, $virtuemart_product_id, $virtuemart_custom_id, $virtuemart_customfield_id, $log, $product_parent_id, $productAsDerived, $multiLangValues = array())
	{
		if ($plugin === $this->id)
		{
			$valueNames = array();
			$valueIds = array();

			// Get all the values
			$param_entries = explode('#', $params);
			$param_entries = $this->db->quote($param_entries);
			$mlparam = array();
			$tables = $this->db->getTableList();

			// Multi language entries
			if ($multiLangValues)
			{
				foreach ($multiLangValues as $lang => $langVal)
				{
					$mlparam[$lang] = explode('#', $langVal);
				}
			}

			// Load the CF4All value IDs
			$query = $this->db->getQuery(true)
				->select(
					$this->db->quoteName(
						array(
							'customsforall_value_id',
							'customsforall_value_name',
						)
					)
				)
				->from($this->db->quoteName('#__virtuemart_custom_plg_customsforall_values'))
				->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom_id)
				->where($this->db->quoteName('customsforall_value_name') . ' IN (' . implode(',', $param_entries) . ')');
			$this->db->setQuery($query);
			$result = $this->db->loadObjectList();

			if ($result)
			{
				foreach ($result as $key => $newResult)
				{
					$valueIds[$key]   = $newResult->customsforall_value_id;
					$valueNames[$key] = $this->db->quote($newResult->customsforall_value_name);

					// Also do the multi language table entries
					if ($mlparam)
					{
						foreach ($mlparam as $pkey => $pLangValue)
						{
							$table = $this->db->getPrefix() . 'virtuemart_custom_plg_customsforall_values_' . strtolower(str_replace('-', '_', $pkey));

							if (in_array($table, $tables))
							{
								$tableName = $this->db->quoteName('#__virtuemart_custom_plg_customsforall_values_' . $pkey);
								$query->clear()
									->delete($tableName)
									->where($this->db->quoteName('customsforall_value_id') . ' = ' . (int) $newResult->customsforall_value_id);
								$this->db->setQuery($query)->execute();
								$query->clear()
									->insert($tableName)
									->columns(array('customsforall_value_id', 'customsforall_value_name'))
									->values((int) $newResult->customsforall_value_id . ',' . $this->db->quote($pLangValue[$key]));
								$this->db->setQuery($query)->execute();
							}
							else
							{
								$log->add('Table ' . $table . ' does not exists', false);
								$log->addStats('Skipped', JText::sprintf('COM_CSVI_CF4ALL_TABLE_DOES_NOT_EXISTS', $table));
							}
						}
					}
				}
			}

			$log->add('Retrieve custom field value IDs');

			// Get the leftover values only for insert
			$leftValueIds = array_diff($param_entries, $valueNames);

			// Create the value
			if ($leftValueIds)
			{
				// Get the last entry for ordering for the custom id
				$query->clear()
					->select('MAX(' . $this->db->quoteName('ordering') . ')')
					->from($this->db->quoteName('#__virtuemart_custom_plg_customsforall_values'))
					->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom_id);
				$this->db->setQuery($query);
				$lastOrdering = $this->db->loadResult();

				if (!$lastOrdering)
				{
					$lastOrdering = 0;
				}

				foreach ($leftValueIds as $entry_order => $entry)
				{
					$query->clear()
						->insert($this->db->quoteName('#__virtuemart_custom_plg_customsforall_values'))
						->columns(array('customsforall_value_name', 'virtuemart_custom_id', 'ordering'))
						->values($entry . ',' . (int) $virtuemart_custom_id . ',' . (++$lastOrdering));
					$this->db->setQuery($query)->execute();
					$insertId = $this->db->insertid();
					$valueIds[] = $insertId;

					// Also do the multi language table entries
					if ($mlparam)
					{
						foreach ($mlparam as $pkey => $pLangValue)
						{
							$table = $this->db->getPrefix() . 'virtuemart_custom_plg_customsforall_values_' . strtolower(str_replace('-', '_', $pkey));

							if (in_array($table, $tables))
							{
								$tableName = $this->db->quoteName('#__virtuemart_custom_plg_customsforall_values_' . $pkey);
								$query->clear()
									->delete($tableName)
									->where($this->db->quoteName('customsforall_value_id') . ' = ' . (int) $insertId);
								$this->db->setQuery($query)->execute();
								$query->clear()
									->insert($tableName)
									->columns(array('customsforall_value_id', 'customsforall_value_name'))
									->values((int) $insertId . ',' . $this->db->quote($pLangValue[$entry_order]));
								$this->db->setQuery($query)->execute();
							}
							else
							{
								$log->add('Table ' . $table . ' does not exists', false);
								$log->addStats('Skipped', JText::sprintf('COM_CSVI_CF4ALL_TABLE_DOES_NOT_EXISTS', $table));
							}
						}
					}
				}
			}

			if (is_array($valueIds) && 0 !== count($valueIds))
			{
				foreach ($valueIds as $valueId)
				{
					$query->clear()
						->select($this->db->quoteName('id'))
						->from($this->db->quoteName('#__virtuemart_product_custom_plg_customsforall'))
						->where($this->db->quoteName('customsforall_value_id') . ' = ' . (int) $valueId)
						->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
						->where($this->db->quoteName('customfield_id') . ' = ' . (int) $virtuemart_customfield_id);
					$this->db->setQuery($query);
					$alreadyExists = $this->db->loadResult();

					if (!$alreadyExists)
					{
						// Store the values
						$query->clear()
							->insert($this->db->quoteName('#__virtuemart_product_custom_plg_customsforall'))
							->columns($this->db->quoteName(array('customsforall_value_id', 'virtuemart_product_id', 'customfield_id')))
							->values(
							(int) $valueId . ',' .
							(int) $virtuemart_product_id . ',' .
							(int) $virtuemart_customfield_id
						);
						$this->db->setQuery($query)->execute();
					}
				}

				$log->add('Insert values for custom field');
			}

			return true;
		}

		return false;
	}

	/**
	 * Export the values.
	 *
	 * @param   string         $plugin                     The ID of the plugin.
	 * @param   string         $custom_param               The custom parameter values.
	 * @param   int            $virtuemart_product_id      The product ID.
	 * @param   int            $virtuemart_custom_id       The custom ID.
	 * @param   int            $virtuemart_customfield_id  The custom field ID.
	 * @param   CsviHelperLog  $log                        The CSVI logger.
	 * @param   string         $lang                       Multi language code
	 *
	 * @return  array  List of values to export.
	 *
	 * @since   6.0
	 */
	public function exportCustomValues($plugin, $custom_param, $virtuemart_product_id, $virtuemart_custom_id, $virtuemart_customfield_id, $log, $lang = '')
	{
		if ($plugin == $this->id)
		{
			// Get the values for this custom field
			$query = $this->db->getQuery(true)
				->from($this->db->quoteName('#__virtuemart_product_custom_plg_customsforall', 'r'))
				->leftJoin(
					$this->db->quoteName('#__virtuemart_custom_plg_customsforall_values', 'v')
					. ' ON ' . $this->db->quoteName('r.customsforall_value_id') . ' = ' . $this->db->quoteName('v.customsforall_value_id')
				);

			if ($lang)
			{
				$tableName = '#__virtuemart_custom_plg_customsforall_values_' . $lang;
				$query->select(
					$this->db->quoteName('cv.customsforall_value_name', 'value') . ',' .
					$this->db->quoteName('c.virtuemart_custom_id')
				);
				$query->leftJoin(
					$this->db->quoteName($tableName, 'cv')
					. ' ON ' . $this->db->quoteName('cv.customsforall_value_id') . ' = ' . $this->db->quoteName('v.customsforall_value_id')
				);
			}
			else
			{
				$query->select(
					$this->db->quoteName('v.customsforall_value_name', 'value') . ',' .
					$this->db->quoteName('c.virtuemart_custom_id')
				);
			}

			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_customs', 'c')
				. ' ON ' . $this->db->quoteName('v.virtuemart_custom_id') . ' = ' . $this->db->quoteName('c.virtuemart_custom_id')
			)
				->leftJoin(
					$this->db->quoteName('#__virtuemart_product_customfields', 'f')
					. ' ON ' . $this->db->quoteName('c.virtuemart_custom_id') . ' = ' . $this->db->quoteName('f.virtuemart_custom_id')
					. ' AND ' . $this->db->quoteName('r.virtuemart_product_id') . ' = ' . $this->db->quoteName('f.virtuemart_product_id')
				)
				->where($this->db->quoteName('r.virtuemart_product_id') . ' = ' . (int) $virtuemart_product_id)
				->where($this->db->quoteName('v.virtuemart_custom_id') . ' = ' . (int) $virtuemart_custom_id)
				->where($this->db->quoteName('r.customfield_id') . ' = ' . (int) $virtuemart_customfield_id)
				->group($this->db->quoteName('value'));
			$this->db->setQuery($query);
			$options = $this->db->loadObjectList();

			$log->add('Get CF4All values');

			// Group the data correctly
			$newoptions = array();

			foreach ($options as $option)
			{
				$newoptions[$option->virtuemart_custom_id][] = $option->value;
			}

			$values = array();

			// Create the CSVI format
			// option1[value1#value2;option2[value1#value2
			foreach ($newoptions as $option)
			{
				$values[] = implode('#', $option);
			}

			return $values;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Method to load plugin specific customfields.
	 *
	 * @param   CsviHelperLog  $log  The CSVI logger.
	 *
	 * @return  array  List of custom fields.
	 *
	 * @since   6.6.0
	 *
	 * @throws  RuntimeException
	 */
	public function loadPluginCustomFields($log)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('virtuemart_custom_id', 'id'))
			->select('TRIM(' . $this->db->quoteName('custom_title') . ') AS ' . $this->db->quoteName('title'))
			->select(
				$this->db->quoteName(
					array(
						'custom_parent_id',
						'field_type',
						'ordering'
					)
				)
			)
			->from($this->db->quoteName('#__virtuemart_customs'))
			->where($this->db->quoteName('custom_element') . ' = ' . $this->db->quote('customfieldsforall'));

		$this->db->setQuery($query);

		try
		{
			$customFields = $this->db->loadObjectList();
			$log->add('Get all CF4All custom field title');
		}
		catch (RuntimeException $e)
		{
			$customFields = array();
		}

		return $customFields;
	}

	/**
	 * Method to get custom field value with id.
	 *
	 * @param   string         $key  The custom parameter key.
	 * @param   int            $id   The custom ID.
	 * @param   CsviHelperLog  $log  The CSVI logger.
	 *
	 * @return  string  Custom field value
	 *
	 * @since   7.0
	 *
	 * @throws  RuntimeException
	 */
	public function getPluginCustomFieldValue($key, $id, $log)
	{
		if (strpos($key, 'customsforall_option') !== false)
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('customsforall_value_name'))
				->from($this->db->quoteName('#__virtuemart_custom_plg_customsforall_values', 'cfv'))
				->leftJoin(
					$this->db->quoteName('#__virtuemart_product_custom_plg_customsforall', 'cf')
					. ' ON ' . $this->db->quoteName('cf.customsforall_value_id') . ' = ' . $this->db->quoteName('cfv.customsforall_value_id')
				)
				->where($this->db->quoteName('cf.id') . ' = ' . (int) $id);
			$this->db->setQuery($query);

			try
			{
				$customfieldValue = $this->db->loadResult();
				$log->add('Get CF4All custom field value');
			}
			catch (RuntimeException $e)
			{
				$customfieldValue = '';
			}

			return $customfieldValue;
		}
	}

	/**
	 * Method to check if CF4ALL plugin installed
	 *
	 * @param   string  $plugin  The plugin value
	 *
	 * @return   bool  true if plugin is installed | False if plugin not installed.
	 *
	 * @since   7.3.0
	 */
	public function checkIfPluginInstalled($plugin)
	{
		if ($this->id === $plugin)
		{
			return true;
		}

		return false;
	}
}
