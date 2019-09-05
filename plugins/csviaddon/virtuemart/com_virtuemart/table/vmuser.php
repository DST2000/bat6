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

/**
 * User table.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class VirtueMartTableVmuser extends CsviTableDefault
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
		parent::__construct('#__virtuemart_vmusers', 'virtuemart_user_id', $db, $config);
	}

	/**
	 * Check if the shipment method exists.
	 *
	 * @return  bool  True if shipment method language entry exists | False if shipment method language entry does not exist.
	 *
	 * @since   4.0
	 */
	public function check()
	{
		if (!empty($this->virtuemart_user_id))
		{
			$query = $this->db->getQuery(true)
				->select('COUNT(' . $this->db->quoteName($this->_tbl_key) . ') AS total')
				->from($this->db->quoteName($this->_tbl))
				->where($this->db->quoteName($this->_tbl_key) . ' = ' . $this->db->quote($this->virtuemart_user_id));
			$this->db->setQuery($query);

			if ($this->db->loadResult() == 1)
			{
				return true;
			}
			else
			{
				$query = "INSERT IGNORE INTO " . $this->db->quoteName($this->_tbl) . " (" . $this->db->quoteName($this->_tbl_key) . ") VALUES (" . $this->db->quote($this->virtuemart_user_id) . ")";
				$this->db->setQuery($query);
				$this->db->execute();

				return false;
			}
		}
		else
		{
			return true;
		}
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
		$this->virtuemart_user_id = null;

		return true;
	}
}
