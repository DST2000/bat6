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

defined('_JEXEC') or die;

/**
 * Custom field table.
 *
 * @package     CSVI
 * @subpackage  Customfilters
 * @since       6.0
 */
class CustomfiltersTableCustomfield extends CsviTableDefault
{
	/**
	 * Table constructor.
	 *
	 * @param   string     $table   Name of the database table to model.
	 * @param   string     $key     Name of the primary key field in the table.
	 * @param   JDatabase  &$db     Database driver
	 * @param   array      $config  The configuration parameters array
	 *
	 * @since   4.0
	 */
	public function __construct($table, $key, &$db, $config = array())
	{
		parent::__construct('#__cf_customfields', 'id', $db, $config);
	}

	/**
	 * Check if the VirtueMart custom ID already exists.
	 *
	 * @return  bool  Returns true if VirtueMart custom ID has been found | False if no VirtueMart custom ID has been found.
	 *
	 * @since   3.0
	 */
	public function check()
	{
		// Check if the VirtueMart custom ID already exists
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName($this->_tbl_key))
			->from($this->db->quoteName($this->_tbl))
			->where($this->db->quoteName('vm_custom_id') . ' = ' . (int) $this->vm_custom_id);
		$this->db->setQuery($query);
		$this->id = $this->db->loadResult();

		if ($this->id)
		{
			return true;
		}

		return false;
	}

	/**
	 * Reset the primary key.
	 *
	 * @return  boolean  Always returns true.
	 *
	 * @since   6.0
	 */
	public function reset()
	{
		parent::reset();

		// Empty the primary key
		$this->id = null;

		return true;
	}
}
