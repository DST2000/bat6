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
require_once JPATH_PLUGINS . '/csviaddon/virtuemart/com_virtuemart/helper/com_virtuemart.php';

/**
 * VirtueMart maintenance.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class Com_VirtuemartMaintenance
{
	/**
	 * Database connector
	 *
	 * @var    JDatabaseDriver
	 * @since  6.0
	 */
	private $db = null;

	/**
	 * Logger helper
	 *
	 * @var    CsviHelperLog
	 * @since  6.0
	 */
	private $log = null;

	/**
	 * CSVI Helper.
	 *
	 * @var    CsviHelperCsvi
	 * @since  6.0
	 */
	private $csvihelper = null;

	/**
	 * The categories in the system
	 *
	 * @var    array
	 * @since  3.0
	 */
	private $categories = array();

	/**
	 * A list of levels deep per category
	 *
	 * @var    array
	 * @since  3.0
	 */
	private $catlevels = array();

	/**
	 * A list of subcategories per category
	 *
	 * @var    array
	 * @since  3.0
	 */
	private $catpaths = array();

	/**
	 * Constructor.
	 *
	 * @param   JDatabaseDriver  $db          The database class
	 * @param   CsviHelperLog    $log         The CSVI logger
	 * @param   CsviHelperCsvi   $csvihelper  The CSVI helper
	 * @param   bool             $isCli       Set if we are running CLI mode
	 *
	 * @since   6.0
	 */
	public function __construct(JDatabaseDriver $db, CsviHelperLog $log, CsviHelperCsvi $csvihelper, $isCli = false)
	{
		$this->db         = $db;
		$this->log        = $log;
		$this->csvihelper = $csvihelper;
		$this->isCli      = $isCli;
	}

	/**
	 * Load a number of maintenance tasks.
	 *
	 * @return  array  List of available operations.
	 *
	 * @since   6.0
	 */
	public function getOperations()
	{
		return array('options' => array(
			''                           => JText::_('COM_CSVI_MAKE_CHOICE'),
			'sortcategories'             => JText::_('COM_CSVI_SORTCATEGORIES_LABEL'),
			'removeemptycategories'      => JText::_('COM_CSVI_REMOVEEMPTYCATEGORIES_LABEL'),
			'removeproductprices'        => JText::_('COM_CSVI_REMOVEPRODUCTPRICES_LABEL'),
			'unpublishproductbycategory' => JText::_('COM_CSVI_UNPUBLISHPRODUCTBYCATEGORY_LABEL'),
			'removeproductmedialink'     => JText::_('COM_CSVI_REMOVEPRODUCTMEDIALINK_LABEL'),
			'backupvm'                   => JText::_('COM_CSVI_BACKUPVM_LABEL'),
			'emptydatabase'              => JText::_('COM_CSVI_EMPTYDATABASE_LABEL'),
			'vmexchangerates'            => JText::_('COM_CSVI_VMEXCHANGERATES_LABEL'),
			'ecbexchangerates'           => JText::_('COM_CSVI_ECBEXCHANGERATES_LABEL'),
			'cleanmediafiles'            => JText::_('COM_CSVI_CLEANMEDIAFILES_LABEL'),
			'checkduplicateskus'         => JText::_('COM_CSVI_CHECKDUPLICATESKUS_LABEL'),
			'refreshsefurls'             => JText::_('COM_CSVI_REFRESHSEFURLS_LABEL'),
			'relatedproductsduplicates'  => JText::_('COM_CSVI_RELATEDPRODUCTSDUPLICATES_LABEL'),
		)
		);
	}

	/**
	 * Load the options for a selected operation.
	 *
	 * @param   string  $operation  The operation to get the options for
	 *
	 * @return  string  The options for a selected operation.
	 *
	 * @since   6.0
	 */
	public function getOptions($operation)
	{
		switch ($operation)
		{
			case 'sortcategories':
				$known = JLanguageHelper::getKnownLanguages();
				$options = array();

				foreach ($known as $lang)
				{
					$options[] = JHtml::_('select.option', str_replace('-', '_', strtolower($lang['tag'])), $lang['name']);
				}

				return '<div class="control-group ">
							<div class="control-label">
								<label title="" class="hasTooltip" for="jform_title" id="jform_title-lbl" data-original-title=" ' . JText::_('COM_CSVI_LANGUAGE_DESC') . '">
									' . JText::_('COM_CSVI_SORTCATEGORIES_LABEL') . '
								</label>
							</div>
							<div class="controls">
								' . JHtml::_('select.genericlist', $options, 'form[language]') . '
							</div>
						</div>';
				break;
			case 'removeemptycategories':
				$layout = new JLayoutFile('csvi.modal');

				return $layout->render(
					array(
						'modal-header' => JText::_('COM_CSVI_' . $operation . '_LABEL'),
						'modal-body' => JText::_('COM_CSVI_CONFIRM_CATEGORY_DELETE'),
						'cancel-button' => true
					)
				);
				break;
			case 'emptydatabase':
				$layout = new JLayoutFile('csvi.modal');
				$html = '<span class="help-block">' . JText::_('COM_CSVI_' . $operation . '_DESC') . '</span>';
				$html .= $layout->render(
					array(
						'modal-header' => JText::_('COM_CSVI_' . $operation . '_LABEL'),
						'modal-body' => JText::_('COM_CSVI_CONFIRM_DB_DELETE'),
						'cancel-button' => true
					)
				);

				return $html;
				break;
			case 'backupvm':
				$options = array(
					JHtml::_('select.option',  '0', JText::_('JNO')),
					JHtml::_('select.option',  '1', JText::_('JYES'))
				);

				return '<div class="control-group ">
							<div class="control-label">
								<label title="" class="hasTooltip" for="jform_title" id="jform_title-lbl" data-original-title=" ' . JText::_('COM_CSVI_DROPTABLE_DESC') . '">
									' . JText::_('COM_CSVI_DROPTABLE_LABEL') . '
								</label>
							</div>
							<div class="controls">
								' . JHtml::_('select.genericlist', $options, 'form[droptable]', 'class="input-small"') . '
							</div>
						</div>';
				break;
			case 'refreshsefurls':
				$layoutPath = JPATH_PLUGINS . '/csviaddon/csvi/com_csvi/layouts';
				$layout     = new JLayoutFile('maintenance.' . $operation, $layoutPath);

				return $layout->render();
				break;
			case 'removeproductprices':
				$settings     = new CsviHelperSettings($this->db);
				$log          = new CsviHelperLog($settings, $this->db);
				$template     = new CsviHelperTemplate($template = '');
				$fields       = new CsviHelperFields($template, $log, $this->db);
				$helperFile   = new Com_VirtuemartHelperCom_Virtuemart($template, $this->log, $fields, $this->db);
				$language     = strtolower(str_replace('-', '_', JComponentHelper::getParams('com_languages')->get('site')));
				$manufacturer = $helperFile->getManufacturers($language);

				return '<div class="control-group ">
							<div class="control-label">
								<label title="" class="hasTooltip" for="jform_title" id="jform_title-lbl" data-original-title=" ' . JText::_('COM_CSVI_SELECT_MANUFACTURER_DESC') . '">
									' . JText::_('COM_CSVI_SELECT_MANUFACTURER_LABEL') . '
								</label>
							</div>
							<div class="controls">
								' . JHtml::_('select.genericlist', $manufacturer, 'form[manufacturer]', 'class="input-large"') . '
							</div>
						</div>';
				break;
			default:
				return '<span class="help-block">' . JText::_('COM_CSVI_' . $operation . '_DESC') . '</span>';
				break;
		}
	}

	/**
	 * Sorts all VirtueMart categories in alphabetical order.
	 *
	 * @param   JInput  $input  An instance of JInput.
	 *
	 * @return  bool  True if categories are sorted | False if an error occurred.
	 *
	 * @since   3.0
	 */
	public function sortCategories(JInput $input)
	{
		$jinput = JFactory::getApplication()->input;
		$linenumber = 1;

		// Load the form values
		$language = $input->get('language');

		// Check if the table exists
		$tables = $this->db->getTableList();

		if (!in_array($this->db->getPrefix() . 'virtuemart_categories_' . $language, $tables))
		{
			$this->log->addStats('information', JText::sprintf('COM_CSVI_LANG_TABLE_NOT_EXIST', $language));
		}
		else
		{
			// Get all categories
			$query = $this->db->getQuery(true);
			$query->select('LOWER(' . $this->db->quoteName('category_name') . ') AS ' . $this->db->quoteName('category_name'));
			$query->select($this->db->quoteName('category_child_id', 'cid'));
			$query->select($this->db->quoteName('category_parent_id', 'pid'));
			$query->from($this->db->quoteName('#__virtuemart_categories', 'c'));
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_category_categories', 'cc')
				. ' ON ' . $this->db->quoteName('c.virtuemart_category_id') . ' = ' . $this->db->quoteName('cc.category_child_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_categories_' . $language, 'cl')
				. ' ON ' . $this->db->quoteName('cc.category_child_id') . ' = ' . $this->db->quoteName('cl.virtuemart_category_id')
			);

			// Execute the query
			$this->db->setQuery($query);
			$records = $this->db->loadObjectList();

			if (count($records) > 0)
			{
				$categories = array();

				// Group all categories together according to their level
				foreach ($records as $record)
				{
					$categories[$record->pid][$record->cid] = $record->category_name;
				}

				// Sort the categories and store the item list
				foreach ($categories as $category)
				{
					asort($category);
					$listorder = 1;

					foreach ($category as $category_id => $category_name)
					{
						// Store the new sort order
						$query = $this->db->getQuery(true);
						$query->update($this->db->quoteName('#__virtuemart_categories'));
						$query->set($this->db->quoteName('ordering') . ' = ' . $this->db->quote($listorder));
						$query->where($this->db->quoteName('virtuemart_category_id') . ' = ' . (int) $category_id);
						$this->db->setQuery($query);
						$this->db->execute();

						// Set the line number
						$this->log->setLinenumber($linenumber++);
						$this->log->addStats('information', JText::sprintf('COM_CSVI_SAVED_CATEGORY', $category_name, $listorder));
						$listorder++;
					}
				}
				// Store the log count
				$linenumber--;
				$jinput->set('logcount', $linenumber);
			}
			else
			{
				$this->log->addStats('information', 'COM_CSVI_NO_CATEGORIES_FOUND');
			}
		}

		return true;
	}

	/**
	 * Remove all categories that have no products
	 * Parent categories are only deleted if there are no more children left.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   3.0
	 */
	public function removeEmptyCategories()
	{
		$this->getCategoryTreeModule();
		arsort($this->catlevels);

		foreach ($this->catlevels as $catid => $nrlevels)
		{
			// Check if there are any products in the category
			$this->db->setQuery($this->getCatQuery($catid));

			if ($this->db->loadResult() > 0 && array_key_exists($catid, $this->catpaths))
			{
				foreach ($this->catpaths[$catid] as $level)
				{
					unset($this->catpaths[$level]);
					unset($this->catlevels[$level]);
				}

				unset($this->catpaths[$catid]);
				unset($this->catlevels[$catid]);
			}
			else
			{
				if (array_key_exists($catid, $this->catpaths))
				{
					foreach ($this->catpaths[$catid] as $level)
					{
						$this->db->setQuery($this->getCatQuery($level));

						if ($this->db->loadResult() > 0)
						{
							unset($this->catpaths[$level]);
							unset($this->catlevels[$level]);
						}
					}
				}
			}
		}

		$delcats = array_keys($this->catpaths);

		if (!empty($delcats))
		{
			// Remove all categories except the ones we have
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__virtuemart_categories'))
				->where($this->db->quoteName('virtuemart_category_id') . ' IN (' . implode(', ', $delcats) . ')');
			$this->db->setQuery($query);

			if ($this->db->execute())
			{
				$this->log->addStats('deleted', 'COM_CSVI_MAINTENANCE_CATEGORIES_DELETED');
			}
			else
			{
				$this->log->addStats('incorrect', 'COM_CSVI_MAINTENANCE_CATEGORIES_NOT_DELETED');
			}

			// Remove all category parent-child relations except the ones we have
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__virtuemart_category_categories'))
				->where($this->db->quoteName('category_child_id') . ' IN (' . implode(', ', $delcats) . ')');
			$this->db->setQuery($query);

			if ($this->db->execute())
			{
				$this->log->addStats('deleted', 'COM_CSVI_MAINTENANCE_CATEGORIES_XREF_DELETED');
			}
			else
			{
				$this->log->addStats('incorrect', 'COM_CSVI_MAINTENANCE_CATEGORIES_XREF_NOT_DELETED');
			}

			// Delete category translations
			jimport('joomla.language.helper');
			$languages = array_keys(JLanguageHelper::getLanguages('lang_code'));

			foreach ($languages as $language)
			{
				$query = $this->db->getQuery(true)
					->delete($this->db->quoteName('#__virtuemart_categories_' . strtolower(str_replace('-', '_', $language))))
					->where($this->db->quoteName('virtuemart_category_id') . ' IN (' . implode(', ', $delcats) . ')');
				$this->db->setQuery($query);
				$this->log->add(JText::_('COM_CSVI_DEBUG_DELETE_CATEGORY_LANG_XREF'), true);
				$this->db->execute();
			}

			// Delete media
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__virtuemart_category_medias'))
				->where($this->db->quoteName('virtuemart_category_id') . ' IN (' . implode(', ', $delcats) . ')');
			$this->db->setQuery($query);
			$this->log->add(JText::_('COM_CSVI_DEBUG_DELETE_MEDIA_XREF'), true);
			$this->db->execute();
		}
		else
		{
			$this->log->addStats('information', 'COM_CSVI_NO_CATEGORIES_FOUND');
		}

		return true;
	}

	/**
	 * This function is repsonsible for returning an array containing category information.
	 *
	 * @return  bool  False if no categories are found.
	 *
	 * @since   2.3.6
	 */
	private function getCategoryTreeModule()
	{
		$query = $this->db->getQuery(true);

		// Get all categories
		$query->select($this->db->quoteName('category_child_id', 'cid') . ',' . $this->db->quoteName('category_parent_id', 'pid'))
			->from($this->db->quoteName('#__virtuemart_categories', 'c'))
			->leftJoin(
				$this->db->quoteName('#__virtuemart_category_categories', 'x')
				. ' ON ' . $this->db->quoteName('c.virtuemart_category_id') . ' = ' . $this->db->quoteName('x.category_child_id')
			);

		// Execute the query
		$this->db->setQuery($query);
		$records = $this->db->loadObjectList();

		// Check if there are any records
		if (count($records) == 0)
		{
			$this->categories = false;

			return false;
		}
		else
		{
			$this->categories = array();

			// Group all categories together according to their level
			foreach ($records as $record)
			{
				$this->categories[$record->pid][$record->cid]["category_id"] = $record->pid;
				$this->categories[$record->pid][$record->cid]["category_child_id"] = $record->cid;
			}
		}

		$catpath = array();
		krsort($this->categories);

		foreach ($this->categories as $pid => $categories)
		{
			foreach ($categories as $cid => $category)
			{
				$catpath[$cid] = $pid;
			}

			// Free up memory
			unset($this->categories[$pid]);
		}

		// Clean up unused array
		unset($this->categories);

		foreach ($catpath as $cid => $value)
		{
			$catlevel = $value;
			$this->catpaths[$cid][] = $catlevel;

			while ($catlevel > 0)
			{
				$this->catpaths[$cid][] = $catpath[$catlevel];
				$catlevel = $catpath[$catlevel];
			}

			// Clean up for some memory
			unset($catpath[$cid]);
		}

		// Clean up unused array
		unset($catpath);

		foreach ($this->catpaths as $cid => $paths)
		{
			$this->catlevels[$cid] = count($paths);
		}

		return true;
	}

	/**
	 * Construct a query to count the number of references to a category.
	 *
	 * @param   int  $catid  The ID of the category to filter on
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object.
	 *
	 * @since   3.0
	 */
	private function getCatQuery($catid)
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(*)')
			->from($this->db->quoteName('#__virtuemart_product_categories'))
			->where($this->db->quoteName('virtuemart_category_id') . ' = ' . (int) $catid);

		return $query;
	}

	/**
	 * Remove all product prices.
	 *
	 * @param   JInput  $input  An instance of JInput.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   5.9.5
	 */
	public function removeProductPrices(JInput $input)
	{
		$manufacturer = $input->get('manufacturer');

		if ($manufacturer && $manufacturer !== 'none')
		{
			$pidArray = array();
			$query    = $this->db->getQuery(true)
				->select($this->db->quoteName('virtuemart_product_id'))
				->from($this->db->quoteName('#__virtuemart_product_manufacturers'))
				->where($this->db->quoteName('virtuemart_manufacturer_id') . ' = ' . (int) $manufacturer);
			$this->db->setQuery($query);
			$productIds = $this->db->loadRowList();

			foreach ($productIds as $productId)
			{
				$pidArray[] = $productId[0];
			}

			try
			{
				$query->clear()
					->delete($this->db->quoteName('#__virtuemart_product_prices'))
					->where($this->db->quoteName('virtuemart_product_id') . ' IN (' . implode(', ', $pidArray) . ')');
				$this->db->setQuery($query);
				$this->db->execute();
				$this->log->setLineNumber(1);
				$this->log->addStats('empty', JText::_('COM_CSVI_DEBUG_DELETE_PRODUCT_PRICES_MANUFACTURER'));
			} catch (Exception $e)
			{
				$this->log->addStats('error', $e->getMessage());
			}
		}
		else
		{
			$this->db->truncateTable('#__virtuemart_product_prices');
			$this->log->setLineNumber(1);
			$this->log->addStats('empty', JText::_('COM_CSVI_VM_PRICES_EMPTIED'));
		}

		return true;
	}

	/**
	 * Unpublish products in unpublished categories.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   3.5
	 */
	public function unpublishProductByCategory()
	{
		$this->log->setLineNumber(1);

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('p.virtuemart_product_id'))
			->from($this->db->quoteName('#__virtuemart_products', 'p'))
			->innerJoin(
				$this->db->quoteName('#__virtuemart_product_categories', 'pc')
				. ' ON ' . $this->db->quoteName('p.virtuemart_product_id') . ' = ' . $this->db->quoteName('pc.virtuemart_product_id')
			)
			->innerJoin(
				$this->db->quoteName('#__virtuemart_categories', 'c')
				. ' ON ' . $this->db->quoteName('pc.virtuemart_category_id') . ' = ' . $this->db->quoteName('c.virtuemart_category_id')
			)
			->where($this->db->quoteName('p.published') . ' = 1')
			->where($this->db->quoteName('c.published') . ' = 0');

		$this->db->setQuery($query);
		$ids = $this->db->loadColumn();

		if (!empty($ids))
		{
			// Unpublish the IDs
			$query = $this->db->getQuery(true)
				->update($this->db->quoteName('#__virtuemart_products'))
				->set($this->db->quoteName('published') . ' = 0')
				->where($this->db->quoteName('virtuemart_product_id') . ' IN (' . implode(',', $ids) . ')');
			$this->db->setQuery($query);

			if ($this->db->execute())
			{
				//$jinput->set('linesprocessed', $this->db->getAffectedRows());
				$this->log->addStats('updated', JText::sprintf('COM_CSVI_PRODUCTS_UNPUBLISHED', $this->db->getAffectedRows()));
			}
			else
			{
				$this->log->addStats('incorrect', JText::sprintf('COM_CSVI_PRODUCTS_NOT_UNPUBLISHED', $this->db->getErrorMsg()));
			}
		}
		else
		{
			$this->log->addStats('information', 'COM_CSVI_PRODUCTS_NOT_FOUND');
		}

		return true;
	}

	/**
	 * Remove any links between products and media items.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   3.0
	 */
	public function removeProductMediaLink()
	{
		$this->log->setLineNumber(1);
		$this->db->truncateTable('#__virtuemart_product_medias');
		$this->log->addStats('information', JText::_('COM_CSVI_PRODUCT_MEDIA_LINK_REMOVED'));

		return true;
	}

	/**
	 * Export all VirtueMart tables.
	 *
	 * @param   JInput  $input  A JInput instance.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   3.0
	 */
	public function backupVm(JInput $input)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$jinput = JFactory::getApplication()->input;

		$filepath = CSVIPATH_TMP;
		$filename = 'virtuemart_' . time() . '.sql';
		$file = $filepath . '/' . $filename;
		$sqlstring = '';
		$fp = fopen($file, "w+");

		// Load the form values, if drop statement need to be included
		$dropTable = $input->get('droptable');

		if ($fp)
		{
			// Load a list of VirtueMart tables
			$q = "SHOW TABLES LIKE '" . $this->db->getPrefix() . "virtuemart\_%'";
			$this->db->setQuery($q);
			$tables = $this->db->loadColumn();
			$linenumber = 1;

			foreach ($tables as $table)
			{
				$this->log->setLinenumber($linenumber);

				// Get the create table statement
				$q = "SHOW CREATE TABLE " . $table;
				$this->db->setQuery($q);
				$tcreate = $this->db->loadAssocList();
				$sqlstring .= "-- Table structure for table " . $this->db->quoteName($table) . "\n\n";

				if ($dropTable)
				{
					$sqlstring .= "DROP TABLE IF EXISTS " . $this->db->quoteName($table) . ";\n";
				}

				$sqlstring .= $tcreate[0]['Create Table'] . ";\n\n";

				// Check if there is any data in the table
				$q = "SELECT COUNT(*) FROM " . $this->db->quoteName($table);
				$this->db->setQuery($q);
				$count = $this->db->loadResult();

				if ($count > 0)
				{
					$sqlstring .= "-- Data for table " . $this->db->quoteName($table) . "\n\n";

					// Get the field names
					$q = "SHOW COLUMNS FROM " . $this->db->quoteName($table);
					$this->db->setQuery($q);
					$fields = $this->db->loadObjectList();
					$sqlstring .= 'INSERT INTO ' . $this->db->quoteName($table) . ' (';

					foreach ($fields as $field)
					{
						$sqlstring .= $this->db->quoteName($field->Field) . ',';
					}

					$sqlstring = substr(trim($sqlstring), 0, -1) . ") VALUES \n";
					$start = 0;

					while ($count > 0)
					{
						$q = "SELECT * FROM " . $table . " LIMIT " . $start . ", 50";
						$this->db->setQuery($q);
						$records = $this->db->loadAssocList();

						// Add the values
						foreach ($records as $record)
						{
							foreach ($record as $rkey => $value)
							{
								if (!is_numeric($value))
								{
									$record[$rkey] = $this->db->quote($value);
								}
								else
								{
									$record[$rkey] = $value;
								}
							}

							$sqlstring .= '(' . implode(',', $record) . "),\n";
						}

						$start += 50;
						$count -= 50;

						// Fix the end of the query
						if ($count < 1)
						{
							$sqlstring = substr(trim($sqlstring), 0, -1) . ";\n";
						}

						// Add a linebreak
						$sqlstring .= "\n\n";

						// Write the data to the file
						fwrite($fp, $sqlstring);

						// Empty the string
						$sqlstring = '';
					}

					// Update the log
					$this->log->addStats('added', JText::sprintf('COM_CSVI_BACKUP_COMPLETE_FOR', $table));
					$linenumber++;
				}
			}

			// Store the log count
			$linenumber--;
			$jinput->set('logcount', $linenumber);

			// Zip up the file
			jimport('joomla.filesystem.archive');
			$zip = JArchive::getAdapter('zip');
			$files = array();
			$files[] = array('name' => $filename, 'time' => filemtime($file), 'data' => JFile::read($file));

			if ($zip->create($filepath . '/' . $filename . '.zip', $files))
			{
				// Close the file
				fclose($fp);

				// Remove the SQL file
				JFile::delete($file);

				// Add a download link for the backup
				$this->log->setFilename(JHtml::link(JURI::root() . 'tmp/com_csvi/' . $filename . '.zip', JText::_('COM_CSVI_BACKUP_DOWNLOAD_LINK')));
			}
			else
			{
				$this->log->addStats('incorrect', 'COM_CSVI_BACKUP_NO_ZIP_CREATE');
				$this->log->setFilename($filepath . '/' . $filename);
			}
		}
		else
		{
			$this->log->addStats('incorrect', JText::sprintf('COM_CSVI_COULD_NOT_OPEN_FILE', $file));
		}

		return true;
	}

	/**
	 * Empty VirtueMart tables.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   3.0
	 */
	public function emptyDatabase()
	{
		$linenumber = 1;

		jimport('joomla.language.helper');
		$languages = array_keys(JLanguageHelper::getLanguages('lang_code'));
		$tables = $this->db->getTableList();

		// Empty all the necessary tables
		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_products');
		$this->db->setQuery($q);
		$this->log->add('Empty product table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_PRODUCT_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_PRODUCT_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		foreach ($languages as $language)
		{
			$table = $this->db->getPrefix() . 'virtuemart_products_' . strtolower(str_replace('-', '_', $language));

			if (in_array($table, $tables))
			{
				$q = 'TRUNCATE TABLE ' . $this->db->quoteName($table) . ';';
				$this->db->setQuery($q);
				$this->log->add('Empty product language table');

				if ($this->db->execute())
				{
					$this->log->addStats('empty', JText::sprintf('COM_CSVI_PRODUCT_LANGUAGE_TABLE_HAS_BEEN_EMPTIED', $language));
				}
				else
				{
					$this->log->addStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_LANGUAGE_TABLE_HAS_NOT_BEEN_EMPTIED', $language));
				}
			}
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_product_categories');
		$this->db->setQuery($q);
		$this->log->add('Empty product category link table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_PRODUCT_CATEGORY_LINK_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_PRODUCT_CATEGORY_LINK_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_product_customfields');
		$this->db->setQuery($q);
		$this->log->add('Empty product custom fields table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_PRODUCT_CUSTOMFIELDS_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_PRODUCT_CUSTOMFIELDS_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_product_manufacturers');
		$this->db->setQuery($q);
		$this->log->add('Empty product manufacturer link table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_PRODUCT_MANUFACTURER_LINK_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_PRODUCT_MANUFACTURER_LINK_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_product_medias');
		$this->db->setQuery($q);
		$this->log->add('Empty product medias table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_PRODUCT_MEDIAS_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_PRODUCT_MEDIAS_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_product_prices');
		$this->db->setQuery($q);
		$this->log->add('Empty product price table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_PRODUCT_PRICE_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_PRODUCT_PRICE_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_product_shoppergroups');
		$this->db->setQuery($q);
		$this->log->add('Empty product shoppergroups table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_PRODUCT_SHOPPERGROUPS_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_PRODUCT_SHOPPERGROUPS_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_categories');
		$this->db->setQuery($q);
		$this->log->add('Empty category table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_CATEGORY_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_CATEGORY_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		foreach ($languages as $language)
		{
			$table = $this->db->getPrefix() . 'virtuemart_categories_' . strtolower(str_replace('-', '_', $language));

			if (in_array($table, $tables))
			{
				$q = 'TRUNCATE TABLE ' . $this->db->quoteName($table) . ';';
				$this->db->setQuery($q);
				$this->log->add('Empty category language table');

				if ($this->db->execute())
				{
					$this->log->addStats('empty', JText::sprintf('COM_CSVI_CATEGORY_LANGUAGE_TABLE_HAS_BEEN_EMPTIED', $language));
				}
				else
				{
					$this->log->addStats('incorrect', JText::sprintf('COM_CSVI_CATEGORY_LANGUAGE_TABLE_HAS_NOT_BEEN_EMPTIED', $language));
				}
			}
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_category_categories');
		$this->db->setQuery($q);
		$this->log->add('Empty category link table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_CATEGORY_LINK_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_CATEGORY_LINK_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_category_medias');
		$this->db->setQuery($q);
		$this->log->add('Empty category medias table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_CATEGORY_MEDIAS_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_CATEGORY_MEDIAS_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_manufacturers');
		$this->db->setQuery($q);
		$this->log->add('Empty manufacturers table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_MANUFACTURER_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_MANUFACTURER_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		// Empty manufacturer language table
		foreach ($languages as $language)
		{
			$table = $this->db->getPrefix() . 'virtuemart_manufacturers_' . strtolower(str_replace('-', '_', $language));

			if (in_array($table, $tables))
			{
				$q = 'TRUNCATE TABLE ' . $this->db->quoteName($table) . ';';
				$this->db->setQuery($q);
				$this->log->add('Empty manufacturer language table');

				if ($this->db->execute())
				{
					$this->log->addStats('empty', JText::sprintf('COM_CSVI_MANUFACTURER_LANGUAGE_TABLE_HAS_BEEN_EMPTIED', $language));
				}
				else
				{
					$this->log->addStats('incorrect', JText::sprintf('COM_CSVI_MANUFACTURER_LANGUAGE_TABLE_HAS_NOT_BEEN_EMPTIED', $language));
				}
			}
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_manufacturercategories');
		$this->db->setQuery($q);
		$this->log->add('Empty manufacturer categories table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_MANUFACTURER_CATEGORY_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_MANUFACTURER_CATEGORY_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		// Empty manufacturer language table
		foreach ($languages as $language)
		{
			$table = $this->db->getPrefix() . 'virtuemart_manufacturercategories_' . strtolower(str_replace('-', '_', $language));

			if (in_array($table, $tables))
			{
				$q = 'TRUNCATE TABLE ' . $this->db->quoteName($table) . ';';
				$this->db->setQuery($q);
				$this->log->add('Empty manufacturer categories language table');

				if ($this->db->execute())
				{
					$this->log->addStats('empty', JText::sprintf('COM_CSVI_MANUFACTURER_LANGUAGE_TABLE_HAS_BEEN_EMPTIED', $language));
				}
				else
				{
					$this->log->addStats('incorrect', JText::sprintf('COM_CSVI_MANUFACTURER_LANGUAGE_TABLE_HAS_NOT_BEEN_EMPTIED', $language));
				}
			}
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_manufacturer_medias');
		$this->db->setQuery($q);
		$this->log->add('Empty manufacturer medias table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_MANUFACTURER_MEDIAS_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_MANUFACTURER_MEDIAS_TABLE_HAS_NOT_BEEN_EMPTIED');
		}


		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_ratings');
		$this->db->setQuery($q);
		$this->log->add('Empty ratings table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_RATINGS_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_RATINGS_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_rating_reviews');
		$this->db->setQuery($q);
		$this->log->add('Empty rating reviews table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_RATING_REVIEWS_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_RATING_REVIEWS_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		$this->log->setLinenumber($linenumber++);
		$q = 'TRUNCATE TABLE ' . $this->db->quoteName('#__virtuemart_rating_votes');
		$this->db->setQuery($q);
		$this->log->add('Empty rating votes table');

		if ($this->db->execute())
		{
			$this->log->addStats('empty', 'COM_CSVI_RATING_VOTES_TABLE_HAS_BEEN_EMPTIED');
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_RATING_VOTES_TABLE_HAS_NOT_BEEN_EMPTIED');
		}

		// Store the log count
		$linenumber--;
		$this->log->setLineNumber($linenumber);

		return true;
	}

	/**
	 * Load the exchange rates from VirtueMart.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   3.0
	 */
	public function vmExchangeRates()
	{
		$jinput = JFactory::getApplication()->input;
		$this->log->setLineNumber(1);

		// Empty table
		$this->db->truncateTable('#__csvi_currency');

		// Add the Euro
		$query = $this->db->getQuery(true)
			->insert($this->db->quoteName('#__csvi_currency'))
			->columns(array($this->db->quoteName('currency_code'), $this->db->quoteName('currency_rate')))
			->values($this->db->quote('EUR') . ', 1');
		$this->db->setQuery($query)->execute();

		$squery = $this->db->getQuery(true)
			->select(
				array(
					'null',
					$this->db->quoteName('currency_code_3'),
					$this->db->quoteName('currency_exchange_rate')
				)
			)
			->from($this->db->quoteName('#__virtuemart_currencies'))
			->where($this->db->quoteName('currency_exchange_rate') . ' > 0');

		$q = "INSERT INTO " .
			$this->db->quoteName('#__csvi_currency') .
			"(" . $squery->__toString() . ")";
		$this->db->setQuery($q);

		if ($this->db->execute())
		{
			$this->log->addStats('added', JText::_('COM_CSVI_VM_EXCHANGE_RATES_LOADED'));
		}
		else
		{
			$this->log->addStats('incorrect', JText::_('COM_CSVI_VM_EXCHANGE_RATES_NOT_LOADED'));
		}

		// Store the log count
		$jinput->set('linesprocessed', 1);

		return true;
	}

	/**
	 *  Add exchange rates
	 * The eurofxref-daily.xml file is updated daily between 14:15 and 15:00 CET.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   3.0
	 */
	public function ecbExchangeRates()
	{
		$jinput = JFactory::getApplication()->input;
		$linenumber = 1;

		// Read eurofxref-daily.xml file in memory
		$XMLContent = file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");

		// Process the file
		if ($XMLContent)
		{
			// Empty table
			$this->db->truncateTable('#__csvi_currency');

			// Add the Euro
			$query = $this->db->getQuery(true)
				->insert($this->db->quoteName('#__csvi_currency'))
				->columns(array($this->db->quoteName('currency_code'), $this->db->quoteName('currency_rate')))
				->values($this->db->quote('EUR') . ', 1');
			$this->db->setQuery($query)->execute();

			$currencyCode = array();
			$rate = array();

			foreach ($XMLContent as $line)
			{
				if (preg_match("/currency='([[:alpha:]]+)'/", $line, $currencyCode))
				{
					if (preg_match("/rate='([[:graph:]]+)'/", $line, $rate))
					{
						$rate_name = 'COM_CSVI_EXCHANGE_RATE_' . $currencyCode[1] . '_ADDED';

						$this->log->setLinenumber($linenumber++);
						$query = $this->db->getQuery(true)
							->insert($this->db->quoteName('#__csvi_currency'))
							->columns(array($this->db->quoteName('currency_code'), $this->db->quoteName('currency_rate')))
							->values($this->db->quote($currencyCode[1]) . ', ' . $this->db->quote($rate[1]));
						$this->db->setQuery($query);

						if ($this->db->execute())
						{
							$this->log->addStats('added', JText::_($rate_name));
						}
						else
						{
							$this->log->addStats('incorrect', JText::_($rate_name));
						}
					}
				}
			}
		}
		else
		{
			$this->log->addStats('incorrect', 'COM_CSVI_CANNOT_LOAD_EXCHANGERATE_FILE');
		}

		// Store the log count
		$linenumber--;
		$jinput->set('linesprocessed', $linenumber);

		return true;
	}

	/**
	 * Update available fields that require extra processing.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 *
	 * @throws  \RuntimeException
	 */
	public function updateAvailableFields()
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

		// Start the query
		$query->clear()
			->insert($this->db->quoteName('#__csvi_availablefields'))
			->columns($this->db->quoteName(array('csvi_name', 'component_name', 'component_table', 'component', 'action')));

		$fieldnames = array();

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

					// Assign the value to it's group
					$values = json_decode($value);

					if ($group === 'selectoptions' && is_array($values))
					{
						foreach ($values as $ramification)
						{
							$csvi_name = trim($ramification->voption);

							if ($ramification->voption === 'clabels')
							{
								$csvi_name = trim($ramification->clabel);
							}

							$fieldnames[] = $csvi_name;
						}
					}
				}
			}
		}

		if (count($fieldnames) > 0)
		{
			$fieldnames = array_unique($fieldnames);

			foreach ($fieldnames as $csvi_name)
			{
				$query->values(
					$this->db->quote($csvi_name) . ',' .
					$this->db->quote($csvi_name) . ',' .
					$this->db->quote('product') . ',' .
					$this->db->quote('com_virtuemart') . ',' .
					$this->db->quote('import')
				);
				$query->values(
					$this->db->quote($csvi_name) . ',' .
					$this->db->quote($csvi_name) . ',' .
					$this->db->quote('product') . ',' .
					$this->db->quote('com_virtuemart') . ',' .
					$this->db->quote('export')
				);
			}

			$this->db->setQuery($query)->execute();
		}
	}

	/**
	 * Update Custom available fields that require extra processing.
	 *
	 * @return  void
	 *
	 * @since   6.5.0
	 *
	 * @throws  \RuntimeException
	 */
	public function customAvailableFields()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('calc_name'))
			->from($this->db->quoteName('#__virtuemart_calcs'))
			->where($this->db->quoteName('calc_kind') . ' = ' . $this->db->quote('VatTax'));
		$this->db->setQuery($query);

		$taxes = $this->db->loadColumn();

		// Insert custom_value available field for multi language support
		require_once JPATH_PLUGINS . '/csviaddon/virtuemart/com_virtuemart/helper/com_virtuemart_config.php';
		$helperConfig = new Com_VirtuemartHelperCom_Virtuemart_Config;
		$languages    = $helperConfig->get('active_languages');

		// Start the query
		$query->clear()
			->insert($this->db->quoteName('#__csvi_availablefields'))
			->columns($this->db->quoteName(array('csvi_name', 'component_name', 'component_table', 'component', 'action')));

		$runQuery = false;

		if (count($taxes) > 0)
		{
			foreach ($taxes as $tax)
			{
				if ($tax)
				{
					$query->values(
						$this->db->quote($tax) . ',' .
						$this->db->quote($tax) . ',' .
						$this->db->quote('order') . ',' .
						$this->db->quote('com_virtuemart') . ',' .
						$this->db->quote('export')
					);

					$runQuery = true;
				}
			}
		}


		if ($languages)
		{
			foreach ($languages as $language)
			{
				if ($language && $language !== 'en-GB')
				{
					$languageCode = strtolower(str_replace('-', '_', $language));
					$fieldName    = 'custom_param_' . $languageCode;
					$query->values(
						$this->db->quote($fieldName) . ',' .
						$this->db->quote($fieldName) . ',' .
						$this->db->quote('product') . ',' .
						$this->db->quote('com_virtuemart') . ',' .
						$this->db->quote('import')
					);

					$query->values(
						$this->db->quote($fieldName) . ',' .
						$this->db->quote($fieldName) . ',' .
						$this->db->quote('product') . ',' .
						$this->db->quote('com_virtuemart') . ',' .
						$this->db->quote('export')
					);

					$runQuery = true;
				}
			}
		}

		if ($runQuery)
		{
			$this->db->setQuery($query)->execute();
		}
	}

	/**
	 * Clean up media files for which images are missing
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   6.5.0
	 */
	public function cleanMediaFiles()
	{
		$jinput = JFactory::getApplication()->input;
		$linenumber = 1;

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName(array('file_url', 'virtuemart_media_id', 'file_type')))
			->from($this->db->quoteName('#__virtuemart_medias'));
		$this->db->setQuery($query);

		$mediaResult = $this->db->loadObjectList();

		foreach ($mediaResult as $result)
		{
			$fileUrl = JPATH_SITE . '/' . $result->file_url;
			$mediaId = $result->virtuemart_media_id;
			$fileType = $result->file_type;
			$this->log->setLinenumber($linenumber++);

			// Check if the image exists
			$imageExists = JFile::exists($fileUrl);
			$this->log->add('Check if image exists', false);

			if (!$imageExists)
			{
				$this->log->add('No Image found in the path ' . $fileUrl, false);

				if ($mediaId)
				{
					switch ($fileType)
					{
						case 'product':
							// Clear Product Media Table
							$query->clear()
								->delete($this->db->quoteName('#__virtuemart_product_medias'))
								->where($this->db->quoteName('virtuemart_media_id') . '=' . (int) $mediaId);
							$this->db->setQuery($query)->execute();
							$this->log->add('Cleaned product media table');
							break;
						case 'manufacturer':
							// Clean Manufacturer Media Table
							$query->clear()
								->delete($this->db->quoteName('#__virtuemart_manufacturer_medias'))
								->where($this->db->quoteName('virtuemart_media_id') . '=' . (int) $mediaId);
							$this->db->setQuery($query)->execute();
							$this->log->add('Cleaned manufacturer media table');
							break;
						case 'category':
							// Clean Category Media Table
							$query->clear()
								->delete($this->db->quoteName('#__virtuemart_category_medias'))
								->where($this->db->quoteName('virtuemart_media_id') . '=' . (int) $mediaId);
							$this->db->setQuery($query)->execute();
							$this->log->add('Cleaned category media table');
							break;
						case 'vendor':
							// Clean Vendor Media Table
							$query->clear()
								->delete($this->db->quoteName('#__virtuemart_vendor_medias'))
								->where($this->db->quoteName('virtuemart_media_id') . '=' . (int) $mediaId);
							$this->db->setQuery($query)->execute();
							$this->log->add('Cleaned vendor media table');
							break;
						default:
							// Clean leftover Media Table entry
							$query->clear()
								->delete($this->db->quoteName('#__virtuemart_medias'))
								->where($this->db->quoteName('virtuemart_media_id') . '=' . (int) $mediaId);
							$this->db->setQuery($query)->execute();
							$this->log->add('Cleaned media table as no file type is set');
							break;
					}
				}

				// Finally remove the entry from media table
				$query->clear()
					->delete($this->db->quoteName('#__virtuemart_medias'))
					->where($this->db->quoteName('virtuemart_media_id') . '=' . (int) $mediaId);
				$this->db->setQuery($query)->execute();
				$this->log->add('Cleaned media table');
			}
			else
			{
				$this->log->add('Image exists ' . $fileUrl);
			}
		}

		// Store the log count
		$linenumber--;
		$jinput->set('linesprocessed', $linenumber);

		return true;
	}

	/**
	 * Check for Duplicate skus in VirtueMart product table.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   7.0
	 */
	public function checkDuplicateSKUs()
	{
		$jinput = JFactory::getApplication()->input;
		$linenumber = 1;

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('product_sku'))
			->select('COUNT(' . $this->db->quoteName('product_sku') . ') as skucount')
			->from($this->db->quoteName('#__virtuemart_products'))
			->group($this->db->quoteName('product_sku'))
			->having('COUNT(' . $this->db->quoteName('product_sku') . ') > 1');
		$this->db->setQuery($query);

		$duplicateSKUs = $this->db->loadObjectList();

		if ($duplicateSKUs)
		{
			foreach ($duplicateSKUs as $SKUs)
			{
				$this->log->setLinenumber($linenumber++);

				if (!$SKUs->product_sku)
				{
					$this->log->addStats('error', JText::sprintf('COM_CSVI_CHECKDUPLICATESKUS_EMPTY_MESSAGE', $SKUs->skucount));
				}
				else
				{
					$this->log->addStats('error', JText::sprintf('COM_CSVI_CHECKDUPLICATESKUS_MESSAGE', $SKUs->product_sku, $SKUs->skucount));
				}
			}
		}
		else
		{
			$this->log->addStats('information', JText::_('COM_CSVI_CHECKDUPLICATESKUS_NO_DUPLICATES'));
		}

		// Store the log count
		$linenumber--;
		$jinput->set('linesprocessed', $linenumber);

		return true;
	}

	/**
	 * Threshold available fields for extension
	 *
	 * @return  int Hardcoded available fields
	 *
	 * @since   7.0
	 */
	public function availableFieldsThresholdLimit()
	{
		return 798;
	}

	/**
	 * Refresh the SEF URLs.
	 *
	 * @param   JInput  $input  An instance of JInput.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   7.1.0
	 */
	public function refreshSefUrls(JInput $input)
	{
		// Load all the needed helpers
		$settings = new CsviHelperSettings($this->db);
		$template = new CsviHelperTemplate($input->getInt('template', 0));
		$fields   = new CsviHelperFields($template, $this->log, $this->db);
		$helper   = new Com_VirtuemartHelperCom_Virtuemart($template, $this->log, $fields, $this->db);
		$sef      = new CsviHelperSef($settings, $template, $this->log);

		// Make sure the languages are sorted base on locale instead of random sorting
		$languages = JLanguageHelper::createLanguageList('', JPATH_SITE, true, true);

		if (count($languages) > 1)
		{
			usort(
				$languages,
				function ($a, $b)
				{
					return strcmp($a['value'], $b['value']);
				}
			);
		}

		// Clean the SEF URL table
		if ($input->get('emptytable', false))
		{
			$this->db->truncateTable('#__csvi_sefurls');
		}

		// Get all the products
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName(array('virtuemart_product_id', 'product_parent_id')))
			->from($this->db->quoteName('#__virtuemart_products'));
		$this->db->setQuery($query);

		$records = $this->db->getIterator();
		$urls    = array();

		// Keep track on the number of records
		$count = 1;

		foreach ($records as $record)
		{
			// Get the category id
			$category_id = $helper->getCategoryId($record->virtuemart_product_id);

			// Check to see if we have a child product
			if ($category_id == 0 && $record->product_parent_id > 0)
			{
				$category_id = $helper->getCategoryId($record->product_parent_id);
			}

			if ($category_id > 0)
			{
				// Collect URLs
				$urls[] = 'index.php?option=com_virtuemart&view=productdetails&' .
					'virtuemart_product_id=' . $record->virtuemart_product_id .
					'&virtuemart_category_id=' . $category_id .
					'&Itemid=' . $template->get('vm_itemid', 1, 'int');
			}

			// Process batches of 500 records
			if ($count > 500)
			{
				if ($urls)
				{
					foreach ($languages as $language)
					{
						$sef->getSefUrls($urls, $language['value']);

						$this->log->setLinenumber($this->log->getLinenumber() + count($urls));
					}

					// Clean up
					$urls = array();
					$count = 0;
				}
			}

			$count++;
		}

		// Process the final batch of URLs
		if ($urls)
		{
			foreach ($languages as $language)
			{
				$sef->getSefUrls($urls, $language['value']);

				$this->log->setLinenumber($this->log->getLinenumber() + count($urls));
			}
		}

		return true;
	}

	/**
	 * Check for Duplicate related products and categories
	 *
	 * @return  bool  returns true if row is updated false otherwise.
	 *
	 * @since   7.4.0
	 */
	public function relatedProductsDuplicates()
	{
		$jinput = JFactory::getApplication()->input;
		$linenumber = 1;
		$query = $this->db->getQuery(true)
			->update($this->db->quoteName('#__virtuemart_customs'))
			->set($this->db->quoteName('virtuemart_vendor_id') . ' = 0')
			->where($this->db->quoteName('custom_title') . ' = ' . $this->db->quote('COM_VIRTUEMART_RELATED_PRODUCTS'), 'OR')
			->where($this->db->quoteName('custom_title') . ' = ' . $this->db->quote('COM_VIRTUEMART_RELATED_CATEGORIES'));
		$this->db->setQuery($query);

		if ($this->db->execute())
		{
			$this->log->addStats('information', JText::_('COM_CSVI_RELATED_PRODUCT_CATEGORY_FIXED_DUPLICATES'));
			$linenumber--;
			$jinput->set('linesprocessed', $linenumber);

			return true;
		}

		return false;
	}

	/**
	 * Check for language in example templates
	 *
	 * @param   array  $template  The template to check
	 *
	 * @return  array Updated template
	 *
	 * @since   7.7.0
	 */
	public function checkTemplatesLanguage($template)
	{
		// Check language for VirtueMart table and only when there is a language setting
		if (isset($template['settings']['language']))
		{
			// Get the tables
			$tableList = $this->db->getTableList();

			// Get the table name to check
			$tableName = $this->db->getPrefix() . 'virtuemart_products_en_gb';

			// Check if default English (en_gb) table exists
			if (!in_array($tableName, $tableList))
			{
				require_once JPATH_PLUGINS . '/csviaddon/virtuemart/com_virtuemart/helper/com_virtuemart_config.php';
				$helperConfig = new Com_VirtuemartHelperCom_Virtuemart_Config;
				$languages    = $helperConfig->get('active_languages');

				if ($languages)
				{
					// Take the first language to set in template
					$languageCode = strtolower(str_replace('-', '_', $languages[0]));
					$tableName    = $this->db->getPrefix() . 'virtuemart_products_' . $languageCode;

					if (in_array($tableName, $tableList))
					{
						// Update language to available language in VM
						$template['settings']['language'] = $languages[0];
					}
				}
			}
		}

		return $template;
	}
}
