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
 * Rating table.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class VirtueMartTableRating extends CsviTableDefault
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
		parent::__construct('#__virtuemart_ratings', 'virtuemart_rating_id', $db, $config);
	}

	/**
	 * Check if there is already an existing review by the user.
	 *
	 * @return  bool  True if the rating exists | False if the rating does not exist.
	 *
	 * @since   3.0
	 */
	public function check()
	{
		if (empty($this->virtuemart_rating_id))
		{
			// Check if a record already exists in the database
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName($this->_tbl_key))
				->from($this->db->quoteName($this->_tbl))
				->where($this->db->quoteName('virtuemart_product_id') . ' = ' . (int) $this->virtuemart_product_id)
				->where($this->db->quoteName('created_by') . ' = ' . (int) $this->created_by);
			$this->db->setQuery($query);
			$id = $this->db->loadResult();
			$this->log->add('Check if a rating exists');

			if ($id)
			{
				$this->virtuemart_rating_id = $id;
			}
		}

		return true;
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
		$this->virtuemart_rating_id = null;

		return true;
	}
}
