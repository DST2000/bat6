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

/**
 * Export VirtueMart categories.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class Category extends \CsviModelExports
{
	/**
	 * Export the data.
	 *
	 * @return  bool  True if body is exported | False if body is not exported.
	 *
	 * @since   6.0
	 *
	 * @throws  \CsviException
	 * @throws  \RuntimeException
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Check if we have a language set
			$language = $this->template->get('language', false);

			if (!$language)
			{
				throw new \CsviException(\JText::_('COM_CSVI_NO_LANGUAGE_SET'));
			}

			// Get all categories
			$query = $this->db->getQuery(true);
			$query->select(
				array('LOWER(' . $this->db->quoteName('l.category_name') . ') AS ' . $this->db->quoteName('category_name'),
					$this->db->quoteName('category_child_id', 'cid'),
					$this->db->quoteName('category_parent_id', 'pid')
				)
			);
			$query->from($this->db->quoteName('#__virtuemart_categories', 'c'));
			$query->leftJoin(
					$this->db->quoteName('#__virtuemart_category_categories', 'x')
					. ' ON ' .
					$this->db->quoteName('x.category_child_id') . ' = ' . $this->db->quoteName('c.virtuemart_category_id')
			);
			$query->leftJoin(
					$this->db->quoteName('#__virtuemart_categories_' . $language, 'l')
					. ' ON ' .
					$this->db->quoteName('l.virtuemart_category_id') . ' = ' . $this->db->quoteName('c.virtuemart_category_id')
			);

			$this->db->setQuery($query);

			try
			{
				$cats = $this->db->loadObjectList();

				// Check if there are any categories
				if (empty($cats))
				{
					$this->addExportContent(\JText::_('COM_CSVI_NO_DATA_FOUND'));
					$this->writeOutput();

					return false;
				}
			}
			catch (\Exception $e)
			{
				$this->addExportContent(\JText::sprintf('COM_CSVI_ERROR_RETRIEVING_DATA', $e->getMessage()));
				$this->log->addStats('incorrect', $e->getMessage());

				$this->writeOutput();

				return false;
			}

			// Build something fancy to only get the fieldnames the user wants
			$userfields = array();
			$exportfields = $this->fields->getFields();

			// Group by fields
			$groupbyfields = json_decode($this->template->get('groupbyfields', '', 'string'));
			$groupby = array();

			if (isset($groupbyfields->name))
			{
				$groupbyfields = array_flip($groupbyfields->name);
			}
			else
			{
				$groupbyfields = array();
			}

			// Sort selected fields
			$sortfields = json_decode($this->template->get('sortfields', '', 'string'));
			$sortby = array();

			if (isset($sortfields->name))
			{
				$sortbyfields = array_flip($sortfields->name);
			}
			else
			{
				$sortbyfields = array();
			}

			foreach ($exportfields as $field)
			{
				switch ($field->field_name)
				{
					case 'virtuemart_category_id':
					case 'ordering':
						$userfields[] = $this->db->quoteName('#__virtuemart_categories.' . $field->field_name);

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_categories.' . $field->field_name);
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_categories.' . $field->field_name);
						}
						break;
					case 'file_url':
					case 'file_url_thumb':
					case 'file_title':
					case 'file_description':
					case 'file_meta':
					case 'file_lang':
					case 'file_ordering':
						$userfields[] = $this->db->quoteName('#__virtuemart_category_medias.virtuemart_media_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_category_medias.virtuemart_media_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_category_medias.virtuemart_media_id');
						}
						break;
					case 'category_name':
					case 'category_description':
					case 'metadesc':
					case 'metakey':
					case 'slug':
					case 'category_path':
					case 'category_path_trans':
						$userfields[] = $this->db->quoteName('#__virtuemart_categories.virtuemart_category_id');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_categories.virtuemart_category_id');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_categories.virtuemart_category_id');
						}
						break;
					case 'show_store_desc':
					case 'showcategory_desc':
					case 'showcategory':
					case 'categories_per_row':
					case 'showproducts':
					case 'omitLoaded':
					case 'showsearch':
					case 'productsublayout':
					case 'featured':
					case 'featured_rows':
					case 'omitLoaded_featured':
					case 'discontinued':
					case 'discontinued_rows':
					case 'omitLoaded_discontinued':
					case 'latest':
					case 'latest_rows':
					case 'omitLoaded_latest':
					case 'topten':
					case 'topten_rows':
					case 'omitLoaded_topten':
					case 'recent':
					case 'recent_rows':
					case 'omitLoaded_recent':
						$userfields[] = $this->db->quoteName('#__virtuemart_categories.cat_params');

						if (array_key_exists($field->field_name, $groupbyfields))
						{
							$groupby[] = $this->db->quoteName('#__virtuemart_categories.cat_params');
						}

						if (array_key_exists($field->field_name, $sortbyfields))
						{
							$sortby[] = $this->db->quoteName('#__virtuemart_categories.cat_params');
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

			// Build the query
			$userfields = array_unique($userfields);
			$query = $this->db->getQuery(true);
			$query->select(implode(",\n", $userfields));
			$query->from($this->db->quoteName('#__virtuemart_categories'));
			$query->leftJoin(
					$this->db->quoteName('#__virtuemart_categories_' . $language)
					. ' ON ' .
					$this->db->quoteName('#__virtuemart_categories_' . $language . '.virtuemart_category_id') . ' = ' . $this->db->quoteName('#__virtuemart_categories.virtuemart_category_id')
			);
			$query->leftJoin(
					$this->db->quoteName('#__virtuemart_category_medias')
					. ' ON ' .
					$this->db->quoteName('#__virtuemart_category_medias.virtuemart_category_id') . ' = ' . $this->db->quoteName('#__virtuemart_categories.virtuemart_category_id')
			);

			// Filter by published state
			$publish_state = $this->template->get('publish_state');

			if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0))
			{
				$query->where($this->db->quoteName('#__virtuemart_categories.published') . ' = ' . (int) $publish_state);
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
							case 'category_path':
								$fieldvalue = $this->helper->createCategoryPathById($record->virtuemart_category_id);
								break;
							case 'category_path_trans':
								$fieldvalue = $this->helper->createCategoryPathById($record->virtuemart_category_id, 'target_language');
								break;
							case 'file_url':
							case 'file_url_thumb':
							case 'file_title':
							case 'file_description':
							case 'file_meta':
							case 'file_lang':
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName($fieldname))
									->from($this->db->quoteName('#__virtuemart_medias', 'm'))
									->leftJoin(
										$this->db->quoteName('#__virtuemart_category_medias', 'c')
										. ' ON ' . $this->db->quoteName('m.virtuemart_media_id') . ' = ' . $this->db->quoteName('c.virtuemart_media_id')
									)
									->where($this->db->quoteName('virtuemart_category_id') . ' = ' . (int) $record->virtuemart_category_id)
									->where($this->db->quoteName('file_type') . ' = ' . $this->db->quote('category'))
									->order('c.ordering');
								$this->db->setQuery($query);
								$titles = $this->db->loadColumn();
								$this->log->add('Looking for category images');

								$fieldvalue = '';

								if (is_array($titles))
								{
									$fieldvalue = implode('|', $titles);
								}

								$this->log->add('Found images: ' . $fieldvalue);
								break;
							case 'file_ordering':
								$query = $this->db->getQuery(true)
									->select($this->db->quoteName('c.ordering'))
									->from($this->db->quoteName('#__virtuemart_medias', 'm'))
									->leftJoin(
										$this->db->quoteName('#__virtuemart_category_medias', 'c')
										. ' ON ' . $this->db->quoteName('m.virtuemart_media_id') . ' = ' . $this->db->quoteName('c.virtuemart_media_id')
									)
									->where($this->db->quoteName('virtuemart_category_id') . ' = ' . (int) $record->virtuemart_category_id)
									->where($this->db->quoteName('file_type') . ' = ' . $this->db->quote('category'))
									->order('c.ordering');
								$this->db->setQuery($query);
								$titles = $this->db->loadColumn();
								$fieldvalue = '';

								if (is_array($titles))
								{
									$fieldvalue = implode('|', $titles);
								}

								break;
							case 'category_name':
							case 'category_description':
							case 'metadesc':
							case 'metakey':
							case 'slug':
							case 'customtitle':
								$query = $this->db->getQuery(true);
								$query->select($this->db->quoteName($fieldname));
								$query->from($this->db->quoteName('#__virtuemart_categories_' . $language));
								$query->where($this->db->quoteName('virtuemart_category_id') . ' = ' . (int) $record->virtuemart_category_id);
								$this->db->setQuery($query);
								$fieldvalue = $this->db->loadResult();
								break;
							case 'show_store_desc':
							case 'showcategory_desc':
							case 'showcategory':
							case 'categories_per_row':
							case 'showproducts':
							case 'omitLoaded':
							case 'showsearch':
							case 'productsublayout':
							case 'featured':
							case 'featured_rows':
							case 'omitLoaded_featured':
							case 'discontinued':
							case 'discontinued_rows':
							case 'omitLoaded_discontinued':
							case 'latest':
							case 'latest_rows':
							case 'omitLoaded_latest':
							case 'topten':
							case 'topten_rows':
							case 'omitLoaded_topten':
							case 'recent':
							case 'recent_rows':
							case 'omitLoaded_recent':
								$catParams = explode('|', $record->cat_params);
								$fieldvalue = '';

								foreach ($catParams as $param)
								{
									$indParam = explode('=', $param);

									if ($fieldname === $indParam[0])
									{
										$fieldvalue = str_replace('"', '', $indParam[1]);
									}
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
