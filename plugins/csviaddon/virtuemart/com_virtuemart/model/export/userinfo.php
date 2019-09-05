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
 * Export VirtueMart user info.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class Userinfo extends \CsviModelExports
{
	/**
	 * Export the data.
	 *
	 * @return  bool  True if body is exported | False if body is not exported.
	 *
	 * @since   6.0
	 *
	 * @throws  \RuntimeException
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Build something fancy to only get the fieldnames the user wants
			$userfields   = array();
			$exportfields = $this->fields->getFields();

			// Group by fields
			$groupbyfieldsRaw = json_decode($this->template->get('groupbyfields', '', 'string'));
			$groupby          = array();
			$groupbyfields    = array();

			if (isset($groupbyfieldsRaw->name))
			{
				$groupbyfields = array_flip($groupbyfieldsRaw->name);
			}

			// Sort selected fields
			$sortfieldsRaw = json_decode($this->template->get('sortfields', '', 'string'));
			$sortby        = array();
			$sortbyfields  = array();

			if (isset($sortfieldsRaw->name))
			{
				$sortbyfields = array_flip($sortfieldsRaw->name);
			}

			foreach ($exportfields as $field)
			{
				switch ($field->field_name)
				{
					case 'virtuemart_user_id':
					case 'created_on':
					case 'modified_on':
					case 'locked_on':
					case 'created_by':
					case 'modified_by':
					case 'locked_by':
					case 'agreed':
						$userfields[] = $this->db->quoteName('#__virtuemart_userinfos.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_userinfos.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_userinfos.' . $field->field_name);
						}
						break;
					case 'full_name':
						$userfields[] = $this->db->quoteName('#__virtuemart_userinfos.first_name');
						$userfields[] = $this->db->quoteName('#__virtuemart_userinfos.middle_name');
						$userfields[] = $this->db->quoteName('#__virtuemart_userinfos.last_name');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_userinfos.first_name');
							$groupby[] = $this->db->quoteName('#__virtuemart_userinfos.middle_name');
							$groupby[] = $this->db->quoteName('#__virtuemart_userinfos.last_name');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_userinfos.first_name');
							$sortby[] = $this->db->quoteName('#__virtuemart_userinfos.middle_name');
							$sortby[] = $this->db->quoteName('#__virtuemart_userinfos.last_name');
						}
						break;
					case 'id':
					case 'name':
					case 'username':
					case 'email':
					case 'password':
					case 'block':
					case 'sendEmail':
					case 'registerDate':
					case 'lastvisitDate':
					case 'activation':
					case 'params':
					case 'lastResetTime':
					case 'resetCount':
					case 'otpKey':
					case 'otep':
					case 'requireReset':
						$userfields[] = $this->db->quoteName('#__users.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__users.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__users.' . $field->field_name);
						}
						break;
					case 'usergroup_name':
						$userfields[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_user_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_user_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_user_id');
						}
						break;
					case 'virtuemart_vendor_id':
						$userfields[] = $this->db->quoteName('#__virtuemart_vmusers.virtuemart_vendor_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_vmusers.virtuemart_vendor_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_vmusers.virtuemart_vendor_id');
						}
						break;
					case 'state_2_code':
					case 'state_3_code':
					case 'state_name':
						$userfields[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_state_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_state_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_state_id');
						}
						break;
					case 'country_2_code':
					case 'country_3_code':
					case 'country_name':
					case 'virtuemart_country_id':
						$userfields[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_country_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_country_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_userinfos.virtuemart_country_id');
						}
						break;
					case 'custom':
						break;
					default:
						$userfields[] = $this->db->quoteName($field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName($field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName($field->field_name);
						}
						break;
				}
			}

			/** Export SQL Query
			 * Get all products - including items
			 * as well as products without a price
			 */
			$userfields = array_unique($userfields);
			$query      = $this->db->getQuery(true);
			$query->select(implode(",\n", $userfields));
			$query->from($this->db->quoteName('#__virtuemart_userinfos'));
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_vmusers')
				. ' ON ' . $this->db->quoteName('#__virtuemart_vmusers.virtuemart_user_id') . ' = ' . $this->db->quoteName('#__virtuemart_userinfos.virtuemart_user_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_vmuser_shoppergroups') . ' ON ' . $this->db->quoteName('#__virtuemart_vmuser_shoppergroups.virtuemart_user_id') . ' = ' . $this->db->quoteName('#__virtuemart_userinfos.virtuemart_user_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_vendors') . ' ON ' . $this->db->quoteName('#__virtuemart_vendors.virtuemart_vendor_id') . ' = ' . $this->db->quoteName('#__virtuemart_vmusers.virtuemart_vendor_id')
			);
			$query->leftJoin(
				$this->db->quoteName('#__virtuemart_shoppergroups') . ' ON ' . $this->db->quoteName('#__virtuemart_shoppergroups.virtuemart_shoppergroup_id') . ' = ' . $this->db->quoteName('#__virtuemart_vmuser_shoppergroups.virtuemart_shoppergroup_id')
			);
			$query->rightJoin(
				$this->db->quoteName('#__users')
				. ' ON ' . $this->db->quoteName('#__users.id') . ' = ' . $this->db->quoteName('#__virtuemart_userinfos.virtuemart_user_id')
			);

			// Filter by vendors
			$vendors = $this->template->get('vendors', false);

			if ($vendors && $vendors[0] != 'none')
			{
				$query->where($this->db->quoteName('#__virtuemart_vmusers.virtuemart_vendor_id') . ' IN (\'' . implode("','", $vendors) . '\')');
			}

			// Filter by permissions
			$permissions = $this->template->get('permissions', false);

			if ($permissions && $permissions[0] != 'none')
			{
				$query->where($this->db->quoteName('#__virtuemart_vmusers.perms') . ' IN (\'' . implode("','", $permissions) . '\')');
			}

			// Filter by address type
			$address = $this->template->get('userinfo_address', false);

			if ($address)
			{
				$query->where($this->db->quoteName('#__virtuemart_userinfos.address_type') . ' = ' . $this->db->quote(strtoupper($address)));
			}

			// Filter by user info modified date start
			$date = $this->template->get('userinfomdatestart', false);

			if ($date)
			{
				$userinfomdate = \JFactory::getDate($date);
				$query->where($this->db->quoteName('#__virtuemart_userinfos.modified_on') . ' >= ' . $this->db->quote($userinfomdate->toSql()));
			}

			// Filter by user info date end
			$date = $this->template->get('userinfomdateend', false);

			if ($date)
			{
				$userinfomdate = \JFactory::getDate($date);
				$query->where($this->db->quoteName('#__virtuemart_userinfos.modified_on') . ' <= ' . $this->db->quote($userinfomdate->toSql()));
			}

			// Filter by block status
			$blocked = $this->template->get('blocked', false);

			if ($blocked != '')
			{
				$query->where($this->db->quoteName('#__users.block') . ' = ' . (int) (($blocked) ? 0 : 1));
			}

			// Filter by activated status
			$activated = $this->template->get('activated', false);

			if ($activated !== false)
			{
				if ($activated == '0')
				{
					$query->where($query->length($this->db->quoteName('#__users.activation')) . ' = 32');

				}
				elseif ($activated == '1')
				{
					$query->where($this->db->quoteName('#__users.activation') . ' = ' . $this->db->quote(''));
				}
			}

			// Get the users who are assigned to shopper group
			$shopper_group = $this->template->get('shopper_groups', array());
			$condition     = array();
			$userList      = $this->getNoShoppergroupAssignedUsers();

			// Check if user not assigned to shopper group is filtered
			if ('no' === $shopper_group[0] && 'none' !== $shopper_group[0] && 0 !== count($shopper_group) && !empty($userList))
			{
				$condition[] = "#__users.id IN ('" . implode("','", $userList) . "')";
			}

			if ($shopper_group && 'none' !== $shopper_group[0] && 0 !== count($shopper_group))
			{
				$shopper_group = ArrayHelper::toInteger($shopper_group);
				$condition[]   = "#__virtuemart_vmuser_shoppergroups.virtuemart_shoppergroup_id IN ('" . implode("','", $shopper_group) . "')";
			}

			// Filter according to shopper group filter set
			if (!empty($condition))
			{
				$query->where('(' . implode(' OR ', $condition) . ')');
			}
			else
			{
				// Remove the users who are not in virtuemart shopper group table
				if (!empty($userList))
				{
					$query->where("#__users.id NOT IN ('" . implode("','", $userList) . "')");
				}
			}

			// Group the fields
			$groupby = array_unique($groupby);

			if (!empty($groupby))
			{
				$query->group($groupby);
			}

			// Sort set fields
			$sortby = array_unique($sortby);

			if (!empty($sortby))
			{
				$query->order($sortby);
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
							case 'created_on':
							case 'modified_on':
							case 'locked_on':
							case 'lastvisitdate':
								$fieldvalue = $this->fields->getDateFormat($fieldname, $record->$fieldname, $field->column_header);
								break;
							case 'address_type':
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0)
								{
									$fieldvalue = $field->default_value;
								}

								if ($fieldvalue == 'BT')
								{
									$fieldvalue = \JText::_('COM_CSVI_BILLING_ADDRESS');
								}
								elseif ($fieldvalue == 'ST')
								{
									$fieldvalue = \JText::_('COM_CSVI_SHIPPING_ADDRESS');
								}
								break;
							case 'full_name':
								$fieldvalue = str_replace('  ', ' ', $record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name);
								break;
							case 'usergroup_name':
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('title'))
									->from($this->db->quoteName('#__usergroups'))
									->leftJoin(
										$this->db->quoteName('#__user_usergroup_map') . ' ON ' .
										$this->db->quoteName('#__user_usergroup_map.group_id') . ' = ' . $this->db->quoteName('#__usergroups.id')
									)
									->where($this->db->quoteName('user_id') . ' = ' . (int) $record->virtuemart_user_id);
								$this->db->setQuery($query);
								$fieldvalue = $this->db->loadResult();
								$this->log->add('Usergroup name');
								break;
							case 'state_2_code':
							case 'state_3_code':
							case 'state_name':
								$fieldvalue = '';

								if ($record->virtuemart_state_id)
								{
									$query = $this->db->getQuery(true);
									$query->select($fieldname);
									$query->from('#__virtuemart_states');
									$query->where('virtuemart_state_id = ' . (int) $record->virtuemart_state_id);
									$this->db->setQuery($query);
									$fieldvalue = $this->db->loadResult();
								}
								break;
							case 'country_2_code':
							case 'country_3_code':
							case 'country_name':
								$fieldvalue = '';

								if ($record->virtuemart_country_id)
								{
									$query = $this->db->getQuery(true);
									$query->select($fieldname);
									$query->from('#__virtuemart_countries');
									$query->where('virtuemart_country_id = ' . $record->virtuemart_country_id);
									$this->db->setQuery($query);
									$fieldvalue = $this->db->loadResult();
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

	/**
	 * Get a list of users who are not assigned with shopper group.
	 *
	 * @return  array list of user ids.
	 *
	 * @since   6.6.0
	 *
	 * @throws  \RuntimeException
	 */
	private function getNoShoppergroupAssignedUsers()
	{
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('#__users.id'));
		$query->from($this->db->quoteName('#__users'));
		$query->leftJoin(
			$this->db->quoteName('#__virtuemart_vmuser_shoppergroups')
			. ' ON ' . $this->db->quoteName('#__virtuemart_vmuser_shoppergroups.virtuemart_user_id') . ' = ' . $this->db->quoteName('#__users.id')
		);
		$query->where($this->db->quoteName('#__virtuemart_vmuser_shoppergroups.virtuemart_user_id') . ' IS NULL');
		$this->db->setQuery($query);
		$result = $this->db->loadColumn();

		if (!is_array($result))
		{
			$result = array();
		}

		return $result;
	}
}