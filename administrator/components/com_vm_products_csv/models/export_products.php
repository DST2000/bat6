<?php
/*
 * @title		com_vm_products_csv
 * @version		2.2
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!');

jimport('joomla.application.component.modellist');

class vm_products_csvModelExport_products extends JModelList
{
	public function getProducts()
	{
		$products = parent::getItems();
		
		/*
		foreach($products as &$product)
		{
		$product->url = 'index.php?option=com_vm_products_csv&amp;task=export_products.edit&amp;product_id='.$product->virtuemart_product_id;
		}
		*/
		
		return $products;
	}
	
	public function getListQuery()
	{
		$query = parent::getListQuery();
		
		$query->select('*');
		$query->from('#__virtuemart_products');
		return $query;
	}
	
}
