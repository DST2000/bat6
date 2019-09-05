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

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with countries.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.6.0
 */
class CsviFormFieldVirtuemartCountry extends JFormFieldCsviForm
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $type = 'VirtuemartCountry';

	/**
	 * Get the options.
	 *
	 * @return  array  An array of countries.
	 *
	 * @since   6.6.0
	 *
	 * @throws  Exception
	 */
	protected function getOptions()
	{
		// Array of supported countries
		$supportedCountries = $this->db->quote(
				array(
					'AU',
					'BR',
					'CZ',
					'FR',
					'DE',
					'IT',
					'JP',
					'NL',
					'ES',
					'CH',
					'GB',
					'US',
					'AT',
					'BE',
					'CA',
					'DK',
					'IN',
					'MX',
					'NO',
					'PL',
					'RU',
					'SE',
					'TR'
			)
		);

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('virtuemart_country_id', 'value'))
			->select($this->db->quoteName('country_name', 'text'))
			->from($this->db->quoteName('#__virtuemart_countries'))
			->order($this->db->quoteName('country_name'))
			->where($this->db->quoteName('country_2_code') . ' IN (' . implode(',', $supportedCountries) . ')');
		$this->db->setQuery($query);
		$options = $this->db->loadObjectList();

		if (count($options) === 0)
		{
			$options = array();
		}

		return array_merge(parent::getOptions(), $options);
	}
}
