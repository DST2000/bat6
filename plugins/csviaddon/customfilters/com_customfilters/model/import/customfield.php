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

namespace customfilters\com_customfilters\model\import;

defined('_JEXEC') or die;

/**
 * Custom field import.
 *
 * @package     CSVI
 * @subpackage  Customfilters
 * @since       6.0
 */
class Customfield extends \RantaiImportEngine
{
	/**
	 * Custom field table.
	 *
	 * @var    \CustomfiltersTableCustomfield
	 * @since  6.0
	 */
	private $customfields = null;

	/**
	 * Start the product import process.
	 *
	 * @return  bool  True on success | false on failure.
	 *
	 * @since   6.0
	 */
	public function getStart()
	{
		// Process data
		foreach ($this->fields->getData() as $fields)
		{
			foreach ($fields as $name => $details)
			{
				$value = $details->value;

				switch ($name)
				{
					case 'published':
					case 'smart_search':
					case 'expanded':
						switch ($value)
						{
							case 'n':
							case 'N':
							case '0':
								$value = 0;
								break;
							default:
								$value = 1;
							break;
						}

						$this->setState($name, $value);
						break;
					default:
						$this->setState($name, $value);
						break;
				}
			}
		}

		// All is good
		return true;
	}

	/**
	 * Process a record.
	 *
	 * @return  bool  Returns true if all is OK | Returns false if no product SKU or product ID can be found.
	 *
	 * @since   6.0
	 */
	public function getProcessRecord()
	{
		// Get the custom field ID
		if (!$this->getState('vm_custom_id', false) && $this->getState('custom_title', false))
		{
			$this->vm_custom_id = $this->getCustomId();

			if ($this->vm_custom_id)
			{
				$this->setState('vm_custom_id', $this->vm_custom_id);
			}

			if (!$this->getState('vm_custom_id', false))
			{
				$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_NO_VM_CUSTOMID_FOUND', $this->custom_title));

				return false;
			}
		}

		// Get the display type
		if (!$this->getState('type_id', false) && $this->getState('display_type', false))
		{
			$this->type_id = $this->getDisplayType();

			if (!$this->getState('type_id', false))
			{
				$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_NO_DISPLAY_TYPE_FOUND', $this->display_type));
			}
		}

		// Check for params
		if ($this->getState('smart_search', false) || $this->getState('expanded', false) || $this->getState('scrollbar_after', false))
		{
			$params                  = new \stdClass;
			$params->smart_search    = ($this->getState('smart_search', false)) ? 1 : 0;
			$params->expanded        = ($this->getState('expanded', false)) ? 1 : 0;
			$params->scrollbar_after = ($this->getState('scrollbar_after', false)) ? $this->getState('scrollbar_after', false) : '';
			$this->params            = json_encode($params);
		}

		// Bind the data
		$this->customfields->bind($this->state);

		// Check the data
		$this->customfields->check();

		// Check the alias
		if (empty($this->customfields->id) && !$this->getState('alias', false))
		{
			if ($this->getState('custom_title', false))
			{
				$this->customfields->alias = \JFilterOutput::stringURLUnicodeSlug($this->custom_title);
			}
		}

		try
		{
			$this->customfields->store();
		}
		catch (\Exception $exception)
		{
			$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_CUSTOMFIELD_NOT_ADDED', $exception->getMessage()));
		}

		return true;
	}

	/**
	 * Load the necessary tables.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function loadTables()
	{
		$this->customfields = $this->getTable('Customfield');
	}

	/**
	 * Clear the loaded tables.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function clearTables()
	{
		$this->customfields->reset();
	}

	/**
	 * Get the custom field ID.
	 *
	 * @return  int  The custom field ID.
	 *
	 * @since   4.2
	 */
	private function getCustomId()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('virtuemart_custom_id'))
			->from($this->db->quoteName('#__virtuemart_customs'))
			->where($this->db->quoteName('custom_title') . ' = ' . $this->db->quote($this->custom_title));
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	/**
	 * Get the type ID.
	 *
	 * @return  string  The type ID.
	 *
	 * @since   4.2
	 */
	private function getDisplayType()
	{
		switch ($this->getState('display_type', false))
		{
			case 'select':
				return '1';
			case 'radio':
				return '2';
			case 'checkbox':
				return '3';
			case 'link':
				return '4';
			case 'range_inputs':
				return '5';
			case 'range_slider':
				return '6';
			case 'range_input_slider':
				return '5,6';
			case 'range_calendars':
				return '8';
			case 'color_btn_single':
				return '9';
			case 'color_btn_multi':
				return '10';
			case 'button_single':
				return '11';
			case 'button_multi':
				return '12';
		}

		return '';
	}
}
