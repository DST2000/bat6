<?php
/*
 * @title		com_vm_products_csv
 * @version		3.0.3e
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!');

jimport('joomla.application.component.controlleradmin');

class vm_products_csvControllerExport_products extends JControllerAdmin
{
	protected $text_prefix = 'COM_VMPCSV_PRODUCT_EXPORT';
	public $operations_report = array();

	
	public function doExport($name = 'doExport', $prefix = 'vm_products_csvModel', $config = array('ignore_request' => true))
	{
	
		$model = parent::getModel($name, $prefix, $config);

		// Do the export
		$this->operations_report = $model->do_product_export();
		
		// Create the view
		$view = $this->getView('export_products', 'html');
		
		// Push the model into the view 
		$view->setModel($model, true);
		
		// Set the layout
		$view->setLayout('default');
		$view->operations_report = $this->operations_report;
		// Display the view
		$view->display();
	}
}
