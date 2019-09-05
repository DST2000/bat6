<?php
/**
 * @package     CSVI
 * @subpackage  Customfilters
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2018 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

namespace customfilters\com_customfilters\model\export;

defined('_JEXEC') or die;

/**
 * Export custom fields.
 *
 * @package     CSVI
 * @subpackage  Customfilters
 * @since       6.0
 */
class Customfield extends \CsviModelExports
{
	/**
	 * Export the data.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Build something fancy to only get the fieldnames the user wants
			$userfields = array();
			$exportfields = $this->fields->getFields();

			// Ignore fields
			$ignore = array('custom', 'custom_title', 'display_type', 'smart_search', 'smart_search', 'expanded', 'scrollbar_after');

			// Group by fields
			$groupbyfields = json_decode($this->template->get('groupbyfields', '', 'string'));

			if (isset($groupbyfields->name))
			{
				$groupbyfields->name = array_diff($groupbyfields->name, $ignore);
				$groupbyfields       = $groupbyfields->name;
			}
			else
			{
				$groupbyfields = array();
			}

			// Sort selected fields
			$sortfields = json_decode($this->template->get('sortfields', '', 'string'));

			if (isset($sortfields->name))
			{
				$sortfields->name = array_diff($sortfields->name, $ignore);
				$sortbyfields     = $sortfields->name;
			}
			else
			{
				$sortbyfields = array();
			}

			foreach ($exportfields as $field)
			{
				switch ($field->field_name)
				{
					case 'id':
						$userfields[] = $this->db->quoteName('c.id');
						break;
					case 'custom_title':
						$userfields[] = $this->db->quoteName('c.vm_custom_id');
						break;
					case 'display_type':
						$userfields[] = $this->db->quoteName('c.type_id');
						$userfields[] = $this->db->quoteName('c.type_id', 'display_type');
						break;
					case 'smart_search':
					case 'expanded':
					case 'scrollbar_after':
						$userfields[] = $this->db->quoteName('c.params');
						break;
					case 'custom':
						break;
					default:
						$userfields[] = $this->db->quoteName($field->field_name);
						break;
				}
			}

			// Build the query
			$userfields = array_unique($userfields);
			$query = $this->db->getQuery(true);
			$query->select(implode(",\n", $userfields));
			$query->from($this->db->quoteName('#__cf_customfields', 'c'));

			// Check if we need to group the orders together
			$groupby = array_unique($groupbyfields);

			if (!empty($groupby))
			{
				$query->group($groupby);
			}

			// Sort set fields
			$sortby = array_unique($sortbyfields);

			if (!empty($sortby))
			{
				$query->order($sortby);
			}

			// Add a limit if user wants us to
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
							$fieldvalue = $record->$fieldname;
						}
						else
						{
							$fieldvalue = '';
						}

						// Process the field
						switch ($fieldname)
						{
							case 'custom_title':
								// Get the custom title
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('custom_title'))
									->from($this->db->quoteName('#__virtuemart_customs'))
									->where($this->db->quoteName('virtuemart_custom_id') . ' = ' . (int) $record->vm_custom_id);
								$this->db->setQuery($query);
								$fieldvalue = $this->db->loadResult();
								break;
							case 'smart_search':
							case 'expanded':
							case 'scrollbar_after':
								$params = json_decode($record->params);

								if (is_object($params))
								{
									$fieldvalue = $params->$fieldname;
								}
								else
								{
									$fieldvalue = '';
								}
								break;
						}

						// Store the field value
						$this->fields->set($field->csvi_templatefield_id, $fieldvalue);
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
}
