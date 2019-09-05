<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_batbrands
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('BatbrandsHelper', JPATH_ADMINISTRATOR . '/components/com_batbrands/helpers/batbrands.php');

/**
 * View to edit a batbrand.
 *
 * @since  1.5
 */
class BatbrandsViewBatbrand extends JViewLegacy
{
	/**
	 * The JForm object
	 *
	 * @var  JForm
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var  object
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user       = JFactory::getUser();
		$userId     = $user->id;
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Since we don't track these assets at the item level, use the category id.
		$canDo = JHelperContent::getActions('com_batbrands', 'category', $this->item->catid);

		JToolbarHelper::title($isNew ? JText::_('COM_BATBRANDS_MANAGER_BATBRAND_NEW') : JText::_('COM_BATBRANDS_MANAGER_BATBRAND_EDIT'), 'bookmark batbrands');
		JToolbarHelper::title($isNew ? JText::_('COM_BATBRANDS_MANAGER_BATBRAND_NEW') : JText::_('COM_BATBRANDS_MANAGER_BATBRAND_EDIT'), 'bookmark batbrands');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_batbrands', 'core.create')) > 0))
		{
			JToolbarHelper::apply('batbrand.apply');
			JToolbarHelper::save('batbrand.save');

			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2new('batbrand.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('batbrand.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('batbrand.cancel');
		}
		else
		{
			if (JComponentHelper::isEnabled('com_contenthistory') && $this->state->params->get('save_history', 0) && $canDo->get('core.edit'))
			{
				JToolbarHelper::versions('com_batbrands.batbrand', $this->item->id);
			}

			JToolbarHelper::cancel('batbrand.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_BATBRANDS_BATBRANDS_EDIT');
	}
}
