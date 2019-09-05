<?php
/*
 * @title		com_vm_products_csv
 * @version		3.0.3
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!'); 

jimport('joomla.application.component.view');
require_once(JPATH_ADMINISTRATOR . '/components/com_vm_products_csv/vm_products_csvUtils.php');

class vm_products_csvViewImport_products extends JViewLegacy
{
	protected $products;
	protected $pagination;
	public $operations_report = array();
	public $vmlangs = array();
	
	public function display($tpl = null)
	{
		$this->products = $this->get('Products');
		$this->pagination = $this->get('Pagination');

		// Add the langs ...
		$this->vmlangs = vm_products_csvUtils::getVmLangs();

		// Check for errors
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		vm_products_csvUtils::addSubmenu('import_products');
		vm_products_csvUtils::addToolbar('import_products');
		
		parent::display($tpl);
	}
	
}
