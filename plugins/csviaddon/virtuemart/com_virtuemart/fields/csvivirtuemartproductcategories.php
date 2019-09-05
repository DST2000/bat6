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
 * Select list form field with product categories.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.0
 */
class JFormFieldCsviVirtuemartProductCategories extends JFormFieldCsviForm
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $type = 'CsviVirtuemartProductCategories';

	/**
	 * Select categories.
	 *
	 * @return  array  An array of users.
	 *
	 * @since   4.0
	 *
	 * @todo    Change to autoloader
	 * @todo    Use the VirtueMart helper
	 */
	protected function getOptions()
	{
		$languageCode = $this->form->getValue('language', 'jform', false);

		if (!$languageCode)
		{
			require_once JPATH_PLUGINS . '/csviaddon/virtuemart/com_virtuemart/helper/com_virtuemart_config.php';

			$helperConfig = new Com_VirtuemartHelperCom_Virtuemart_Config;

			$language     = $helperConfig->get('active_languages');
			$languageCode = '';

			if (is_array($language) && array_key_exists(0, $language))
			{
				$languageCode = strtolower(str_replace('-', '_', $language[0]));
			}

			// Check if no language is set in VirtueMart, take the default Joomla! frontend language
			if ('' === $languageCode)
			{
				$languageCode = strtolower(str_replace('-', '_', JComponentHelper::getParams('com_languages')->get('site')));
			}
		}

		$lang = strtolower(str_replace('-', '_', $languageCode));

		require_once JPATH_PLUGINS . '/csviaddon/virtuemart/com_virtuemart/helper/categorylist.php';
		$categoryList = new Com_VirtuemartHelperCategoryList;

		return $categoryList->getCategoryTree($lang);
	}
}
