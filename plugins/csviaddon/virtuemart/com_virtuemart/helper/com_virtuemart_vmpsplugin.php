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

// Include the plugin helper
defined('VMPATH_ADMIN') or define('VMPATH_ADMIN', JPATH_ADMINISTRATOR . '/components/com_virtuemart');
require_once JPATH_ADMINISTRATOR . '/components/com_virtuemart/plugins/vmplugin.php';

/**
 * The VirtueMart payment and shipping plugin helper class.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       6.1
 */
abstract class vmPSPlugin extends vmPlugin
{
	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array   $config    An array that holds the plugin configuration.
	 *
	 * @since   6.6.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->_tablepkey = 'id';
		$this->_idName = 'virtuemart_' . $this->_psType . 'method_id';
		$this->_configTable = '#__virtuemart_' . $this->_psType . 'methods';
		$this->_configTableFieldName = $this->_psType . '_params';
		$this->_configTableFileName = $this->_psType . 'methods';
		$this->_configTableClassName = 'Table' . ucfirst($this->_psType) . 'methods';
		$this->_loggable = true;
	}

	/**
	 * Create a list of elements which match the cart product.
	 *
	 * @param   VirtueMartCart  $cart      An instance of VirtueMartCart.
	 * @param   int             $selected  Value of pre-selected element.
	 * @param   array           &$htmlIn   An array with HTML radiolist items.
	 *
	 * @return  array An array with payment/shipment details.
	 *
	 * @since   6.6.0
	 *
	 * @throws  Exception
	 */
	public function displayListFE(VirtueMartCart $cart, $selected = 0, &$htmlIn)
	{
		$shipmentDetails = array();

		// Load the methods
		$this->getPluginMethods($cart->vendorId);

		foreach ($this->methods as $method)
		{
			if ($this->checkConditions($cart, $method, $cart->cartPrices))
			{
				// Calculate the prices including tax
				$this->setCartPrices($cart, $cart->cartPrices, $method);

				// Get the shipment price
				$shipmentDetails['title'] = $method->shipment_name;
				$shipmentDetails['price'] = $cart->cartPrices['salesPriceShipment'];

				// No need to continue, as we only take one price
				break;
			}
		}

		return $shipmentDetails;
	}

	/**
	 * Check the plugin conditions.
	 *
	 * @param   array  $cart         The cart contents.
	 * @param   array  $method       The plugin method.
	 * @param   array  $cart_prices  The cart prices.
	 *
	 * @return  bool  Always returning false, method should be overriden by plugin.
	 *
	 * @since   6.6.3
	 */
	protected function checkConditions($cart, $method, $cart_prices)
	{
		return false;
	}

	/**
	 * Push variables somewhere.
	 *
	 * @return  array  List of variables.
	 *
	 * @since   6.6.0
	 */
	public function getVarsToPush ()
	{
		return self::getVarsToPushByXML($this->_xmlFile, $this->_name . 'Form');
	}

	/**
	 * Set convertible fields.
	 *
	 * @param   array  $toConvert  An array of fields to convert.
	 *
	 * @return  void
	 *
	 * @since   6.6.3
	 */
	public function setConvertable($toConvert)
	{
		$this->_toConvert = $toConvert;
	}

	/**
	 * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
	 * The plugin must check first if it is the correct type
	 *
	 * @param   VirtueMartCart  $cart            The cart object.
	 * @param   array           $cart_prices     An array with product prices.
	 * @param   int             &$methodCounter  Counts the number of methods.
	 *
	 * @return  null If no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
	 *
	 * @since   6.6.0
	 */
	public function onCheckAutomaticSelected(VirtueMartCart $cart, array $cart_prices = array(), &$methodCounter = 0)
	{
		return 0;
	}

	/**
	 * Set the updated cart prices including tax id necessary
	 *
	 * @param   VirtueMartCart  $cart          An instance of VirtueMartCart.
	 * @param   array           &$cart_prices  An array of cart prices.
	 * @param   JTable          $method        An instance of the payment or shipping method.
	 * @param   bool            $progressive   Deals with how the fee is calculated.
	 *
	 * @return  mixed
	 */
	public function setCartPrices(VirtueMartCart $cart, &$cart_prices, $method, $progressive = true)
	{
		$_psType = ucfirst($this->_psType);

		$calculator = calculationHelper::getInstance();

		$cart_prices[$this->_psType . 'Value'] = $calculator->roundInternal($this->getCosts($cart, $method, $cart_prices), 'salesPrice');

		if (!isset($cart_prices[$this->_psType . 'Value']))
		{
			$cart_prices[$this->_psType . 'Value'] = 0.0;
		}

		if (!isset($cart_prices[$this->_psType . 'Tax']))
		{
			$cart_prices[$this->_psType . 'Tax'] = 0.0;
		}

		if ($this->_psType == 'payment')
		{
			$cartTotalAmountOrig = $this->getCartAmount($cart_prices);

			if (!isset($method->cost_percent_total))
			{
				$method->cost_percent_total = 0.0;
			}

			if (!isset($method->cost_per_transaction))
			{
				$method->cost_per_transaction = 0.0;
			}

			if (!$progressive)
			{
				// Simple
				$cartTotalAmount = ($cartTotalAmountOrig + $method->cost_per_transaction) * (1 + ($method->cost_percent_total * 0.01));
			}
			else
			{
				// Progressive
				$cartTotalAmount = ($cartTotalAmountOrig + $method->cost_per_transaction) / (1 - ($method->cost_percent_total * 0.01));
			}

			$cart_prices[$this->_psType . 'Value'] = $cartTotalAmount - $cartTotalAmountOrig;

			if (!empty($method->cost_min_transaction) and $method->cost_min_transaction != '' and $cart_prices[$this->_psType . 'Value'] < $method->cost_min_transaction)
			{
				$cart_prices[$this->_psType . 'Value'] = $method->cost_min_transaction;
			}
		}

		if (!isset($cart_prices['salesPrice' . $_psType]))
		{
			$cart_prices['salesPrice' . $_psType] = $cart_prices[$this->_psType . 'Value'];
		}

		$taxrules = array();

		if (isset($method->tax_id) and (int) $method->tax_id === -1)
		{
		}
		else
		{
			if (!empty($method->tax_id))
			{
				$cart_prices[$this->_psType . '_calc_id'] = $method->tax_id;

				$db = JFactory::getDbo();
				$q = 'SELECT * FROM #__virtuemart_calcs WHERE `virtuemart_calc_id`="' . $method->tax_id . '" ';
				$db->setQuery($q);
				$taxrules = $db->loadAssocList();

				if (!empty($taxrules))
				{
					foreach ($taxrules as &$rule)
					{
						if (!isset($rule['subTotal']))
						{
							$rule['subTotal'] = 0;
						}

						if (!isset($rule['taxAmount']))
						{
							$rule['taxAmount'] = 0;
						}

						$rule['subTotalOld'] = $rule['subTotal'];
						$rule['taxAmountOld'] = $rule['taxAmount'];
						$rule['taxAmount'] = 0;
						$rule['subTotal'] = $cart_prices[$this->_psType . 'Value'];
						$cart_prices[$this->_psType . 'TaxPerID'][$rule['virtuemart_calc_id']] = $calculator->roundInternal($calculator->roundInternal($calculator->interpreteMathOp($rule, $rule['subTotal'])) - $rule['subTotal'], 'salesPrice');
						$cart_prices[$this->_psType . 'Tax'] += $cart_prices[$this->_psType . 'TaxPerID'][$rule['virtuemart_calc_id']];
					}
				}
			}
			else
			{
				$taxrules = array_merge($cart->cartData['VatTax'], $cart->cartData['taxRulesBill']);
				$cartdiscountBeforeTax = $calculator->roundInternal($calculator->cartRuleCalculation($cart->cartData['DBTaxRulesBill'], $cart->cartPrices['salesPrice']));

				if (!empty($taxrules))
				{
					foreach ($taxrules as &$rule)
					{
						// Quick n dirty
						if (!isset($rule['calc_kind']))
						{
							$rule = (array) VmModel::getModel('calc')
								->getCalc($rule['virtuemart_calc_id']);
						}

						if (!isset($rule['subTotal']))
						{
							$rule['subTotal'] = 0;
						}

						if (!isset($rule['taxAmount']))
						{
							$rule['taxAmount'] = 0;
						}

						if (!isset($rule['DBTax']))
						{
							$rule['DBTax'] = 0;
						}

						if (!isset($rule['percentage']) && $rule['subTotal'] < $cart->cartPrices['salesPrice'])
						{
							$rule['percentage'] = ($rule['subTotal'] + $rule['DBTax']) / ($cart->cartPrices['salesPrice'] + $cartdiscountBeforeTax);
						}
						else
						{
							if (!isset($rule['percentage']))
							{
								$rule['percentage'] = 1;
							}
						}

						$rule['subTotalOld'] = $rule['subTotal'];
						$rule['subTotal'] = 0;
						$rule['taxAmountOld'] = $rule['taxAmount'];
						$rule['taxAmount'] = 0;
					}

					foreach ($taxrules as &$rule)
					{
						$rule['subTotal'] = $cart_prices[$this->_psType . 'Value'] * $rule['percentage'];

						if (!isset($cart_prices[$this->_psType . 'Tax']))
						{
							$cart_prices[$this->_psType . 'Tax'] = 0.0;
						}

						$cart_prices[$this->_psType . 'TaxPerID'][$rule['virtuemart_calc_id']] = $calculator->roundInternal($calculator->roundInternal($calculator->interpreteMathOp($rule, $rule['subTotal'])) - $rule['subTotal'], 'salesPrice');
						$cart_prices[$this->_psType . 'Tax'] += $cart_prices[$this->_psType . 'TaxPerID'][$rule['virtuemart_calc_id']];

					}
				}
			}
		}

		if (empty($method->cost_per_transaction))
		{
			$method->cost_per_transaction = 0.0;
		}

		if (empty($method->cost_min_transaction))
		{
			$method->cost_min_transaction = 0.0;
		}

		if (empty($method->cost_percent_total))
		{
			$method->cost_percent_total = 0.0;
		}

		if (count($taxrules) > 0)
		{
			$cart_prices['salesPrice' . $_psType] = $calculator->roundInternal($calculator->executeCalculation($taxrules, $cart_prices[$this->_psType . 'Value'], true, false), 'salesPrice');
			reset($taxrules);

			foreach ($taxrules as &$rule)
			{
				if (!isset($cart_prices[$this->_psType . '_calc_id']) or !is_array($cart_prices[$this->_psType . '_calc_id']))
				{
					$cart_prices[$this->_psType . '_calc_id'] = array();
				}

				$cart_prices[$this->_psType . '_calc_id'][] = $rule['virtuemart_calc_id'];

				if (isset($rule['subTotalOld']))
				{
					$rule['subTotal'] += $rule['subTotalOld'];
				}

				if (isset($rule['taxAmountOld']))
				{
					$rule['taxAmount'] += $rule['taxAmountOld'];
				}
			}
		}
		else
		{
			$cart_prices['salesPrice' . $_psType] = $cart_prices[$this->_psType . 'Value'];
			$cart_prices[$this->_psType . 'Tax'] = 0;
			$cart_prices[$this->_psType . '_calc_id'] = 0;
		}

		return $cart_prices['salesPrice' . $_psType];
	}

	/**
	 * Fill the array with all plugins found with this plugin for the current vendor.
	 *
	 * @param   int  $vendorId  The ID of the vendor.
	 *
	 * @return  True when plugins(s) was (were) found for this vendor, false otherwise
	 *
	 * @since   6.6.0
	 */
	protected function getPluginMethods($vendorId)
	{
		if (!class_exists('VirtueMartModelUser'))
		{
			require_once VMPATH_ADMIN . '/models/user.php';
		}

		$usermodel = VmModel::getModel('user');
		$user = $usermodel->getUser();
		$user->shopper_groups = (array) $user->shopper_groups;

		$db = JFactory::getDbo();

		if (empty($vendorId))
		{
			$vendorId = 1;
		}

		$select = 'SELECT i.*, ';

		$extPlgTable = '#__extensions';
		$extField1 = 'extension_id';
		$extField2 = 'element';

		$select .= 'j.`' . $extField1 . '`,j.`name`, j.`type`, j.`element`, j.`folder`, j.`client_id`, j.`enabled`, j.`access`, j.`protected`, j.`manifest_cache`,
			j.`params`, j.`custom_data`, j.`system_data`, j.`checked_out`, j.`checked_out_time`, j.`state`,  s.virtuemart_shoppergroup_id ';

		if (!VmConfig::$vmlang)
		{
			VmConfig::setdbLanguageTag();
		}

		$joins = array();

		if (VmConfig::$defaultLang != VmConfig::$vmlang and Vmconfig::$langCount > 1)
		{
			$langFields = array($this->_psType . '_name', $this->_psType . '_desc');

			$useJLback = false;

			if (VmConfig::$defaultLang != VmConfig::$jDefLang)
			{
				$joins[] = ' LEFT JOIN `#__virtuemart_' . $this->_psType . '_' . VmConfig::$jDefLang . '` as ljd';
				$useJLback = true;
			}

			foreach ($langFields as $langField)
			{
				$expr2 = 'ld.' . $langField;

				if ($useJLback)
				{
					$expr2 = 'IFNULL(ld.' . $langField . ',ljd.' . $langField . ')';
				}

				$select .= ', IFNULL(l.' . $langField . ',' . $expr2 . ') as ' . $langField . '';
			}

			$joins[] = ' LEFT JOIN `#__virtuemart_' . $this->_psType . 'methods_' . VmConfig::$defaultLang . '` as ld using (`virtuemart_' . $this->_psType . 'method_id`)';
			$joins[] = ' LEFT JOIN `#__virtuemart_' . $this->_psType . 'methods_' . VmConfig::$vmlang . '` as l using (`virtuemart_' . $this->_psType . 'method_id`)';
		}
		else
		{
			$select .= ', l.* ';
			$joins[] = ' LEFT JOIN `#__virtuemart_' . $this->_psType . 'methods_' . VmConfig::$vmlang . '` as l using (`virtuemart_' . $this->_psType . 'method_id`)';
		}

		$q = $select . ' FROM   `#__virtuemart_' . $this->_psType . 'methods' . '` as i ';

		$joins[] = ' LEFT JOIN `' . $extPlgTable . '` as j ON j.`' . $extField1 . '` =  i.`' . $this->_psType . '_jplugin_id` ';
		$joins[] = ' LEFT OUTER JOIN `#__virtuemart_' . $this->_psType . 'method_shoppergroups` AS s ON i.`virtuemart_' . $this->_psType . 'method_id` = s.`virtuemart_' . $this->_psType . 'method_id` ';

		$q .= implode(' ' . "\n", $joins);
		$q .= ' WHERE i.`published` = "1" AND j.`' . $extField2 . '` = "' . $this->_name . '"
	    						AND  (i.`virtuemart_vendor_id` = "' . $vendorId . '" OR i.`virtuemart_vendor_id` = "0" OR i.`shared` = "1")
	    						AND  (';

		foreach ($user->shopper_groups as $groups)
		{
			$q .= ' s.`virtuemart_shoppergroup_id`= "' . (int) $groups . '" OR';
		}

		$q .= ' (s.`virtuemart_shoppergroup_id`) IS NULL ) GROUP BY i.`virtuemart_' . $this->_psType . 'method_id` ORDER BY i.`ordering`';

		$db->setQuery($q);

		$this->methods = $db->loadObjectList();

		if ($this->methods)
		{
			foreach ($this->methods as $method)
			{
				VmTable::bindParameterable($method, $this->_xParams, $this->_varsToPushParam);
			}
		}
		else
		{
			if ($this->methods === false)
			{
				vmError('Error reading getPluginMethods ' . $q);
			}
		}

		return count($this->methods);
	}

	/**
	 * Get the total weight for the order, based on which the proper shipping rate
	 * can be selected.
	 *
	 * @param   VirtueMartCart  $cart            An instance of VirtueMartCart.
	 * @param   string          $to_weight_unit  The weight unit.
	 *
	 * @return  float  Total weight for the order
	 *
	 * @since   6.6.0
	 */
	protected function getOrderWeight(VirtueMartCart $cart, $to_weight_unit)
	{
		static $weight = array();

		if (!array_key_exists($cart->products[0]->virtuemart_product_id, $weight))
		{
			$weight[$cart->products[0]->virtuemart_product_id] = 0.0;
		}

		if ($weight[$cart->products[0]->virtuemart_product_id] === 0.0 && count($cart->products) > 0)
		{
			foreach ($cart->products as $product)
			{
				$weight[$cart->products[0]->virtuemart_product_id] += (
					ShopFunctions::convertWeightUnit($product->product_weight, $product->product_weight_uom, $to_weight_unit) * $product->quantity
				);
			}
		}

		return $weight[$cart->products[0]->virtuemart_product_id];
	}

	/**
	 * Calculate the price (value, tax_id) of the selected method.
	 * It is called by the calculator.
	 * This function does NOT to be reimplemented. If not reimplemented, then the default values from this function are taken.
	 *
	 * @param   VirtueMartCart  $cart               An instance of VirtueMartCart.
	 * @param   array           &$cart_prices       An array with product prices.
	 * @param   array           &$cart_prices_name  An array with product prices and plugin names.
	 *
	 * @return null If the method was not selected, false if the shipping rate is not valid any more, true otherwise
	 *
	 * @since  6.6.0
	 */
	public function onSelectedCalculatePrice(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name)
	{
		$idName = $this->_idName;

		if (!($method = $this->selectedThisByMethodId($cart->$idName)))
		{
			// Another method was selected, do nothing
			return null;
		}

		if (!$method = $this->getVmPluginMethod($cart->$idName) or empty($method->$idName))
		{
			return null;
		}

		$cart_prices_name = '';
		$cart_prices['cost'] = 0;

		if (!$this->checkConditions($cart, $method, $cart_prices))
		{
			return false;
		}

		$cart_prices_name = $this->renderPluginName($method);

		$this->setCartPrices($cart, $cart_prices, $method);

		return true;
	}

	/**
	 * Render the plugin name.
	 *
	 * @param   object  $plugin  The plugin to render the name for.
	 *
	 * @return  string  The plugin name with HTML markup.
	 *
	 * @since   6.6.0
	 */
	protected function renderPluginName($plugin)
	{
		$return = '';
		$plugin_name = $this->_psType . '_name';
		$plugin_desc = $this->_psType . '_desc';
		$description = '';
		$logosFieldName = $this->_psType . '_logos';
		$logos = $plugin->$logosFieldName;

		if (!empty($logos))
		{
			$return = $this->displayLogos($logos) . ' ';
		}

		if (!empty($plugin->$plugin_desc))
		{
			$description = '<span class="' . $this->_type . '_description">' . $plugin->$plugin_desc . '</span>';
		}

		$pluginName = $return . '<span class="' . $this->_type . '_name">' . $plugin->$plugin_name . '</span>' . $description;

		return $pluginName;
	}

	/**
	 * Displays the logos of a VirtueMart plugin
	 *
	 * @param   array  $logo_list  The list of logos to display.
	 *
	 * @return  string HTML markup with logos
	 *
	 * @since   6.6.0
	 */
	protected function displayLogos($logo_list)
	{
		$img = '';

		if (!(empty($logo_list)))
		{
			$url = JURI::root() . 'images/stories/virtuemart/' . $this->_psType . '/';

			if (!is_array($logo_list))
			{
				$logo_list = (array) $logo_list;
			}

			foreach ($logo_list as $logo)
			{
				if (!empty($logo))
				{
					$alt_text = substr($logo, 0, strpos($logo, '.'));
					$img .= '<span class="vmCart' . ucfirst($this->_psType) . 'Logo" >' .
						'<img align="middle" src="' . $url . $logo . '"  alt="' . $alt_text . '" /></span> ';
				}
			}
		}

		return $img;
	}

	/**
	 * Convert the amounts from comma to period.
	 *
	 * @param   object  &$method  The method to replace the amount for.
	 *
	 * @return  void.
	 *
	 * @since   6.6.0
	 */
	public function convert_condition_amount(&$method)
	{
		$method->min_amount = (float) str_replace(',', '.', $method->min_amount);
		$method->max_amount = (float) str_replace(',', '.', $method->max_amount);
	}

	/**
	 * Get the cart amount for checking conditions if the payment conditions are fulfilled.
	 *
	 * @param   array  $cart_prices  Array with cart product prices.
	 *
	 * @return  float  The cart amount
	 *
	 * @since   6.6.0
	 */
	public function getCartAmount($cart_prices)
	{
		if (empty($cart_prices['salesPrice']))
		{
			$cart_prices['salesPrice'] = 0.0;
		}

		$cartPrice = !empty($cart_prices['withTax']) ? $cart_prices['withTax'] : $cart_prices['salesPrice'];

		if (empty($cart_prices['salesPriceShipment']))
		{
			$cart_prices['salesPriceShipment'] = 0.0;
		}

		if (empty($cart_prices['salesPriceCoupon']))
		{
			$cart_prices['salesPriceCoupon'] = 0.0;
		}

		$amount = $cartPrice + $cart_prices['salesPriceShipment'] + $cart_prices['salesPriceCoupon'];

		if ($amount <= 0)
		{
			$amount = 0;
		}

		return $amount;
	}

	/**
	 * Get the shipment/payment costs.
	 *
	 * @param   VirtueMartCart  $cart         An instance of the VirtueMartCart object.
	 * @param   object          $method       The payment/shipment object.
	 * @param   array           $cart_prices  An array with product prices.
	 *
	 * @return  float  The costs.
	 *
	 * @since   6.6.0
	 */
	public function getCosts(VirtueMartCart $cart, $method, $cart_prices)
	{
		if (!isset($method->cost_percent_total))
		{
			$method->cost_percent_total = 0.0;
		}

		if (preg_match('/%$/', $method->cost_percent_total))
		{
			$method->cost_percent_total = substr($method->cost_percent_total, 0, -1);
		}
		else
		{
			if (empty($method->cost_percent_total))
			{
				$method->cost_percent_total = 0;
			}
		}

		$cartPrice = !empty($cart->cartPrices['withTax']) ? $cart->cartPrices['withTax'] : $cart->cartPrices['salesPrice'];

		if (!isset($method->cost_per_transaction))
		{
			$method->cost_per_transaction = 0.0;
		}

		$costs = $method->cost_per_transaction + $cartPrice * $method->cost_percent_total * 0.01;

		if (!empty($method->cost_min_transaction) and $method->cost_min_transaction != '' and $costs < $method->cost_min_transaction)
		{
			return $method->cost_min_transaction;
		}
		else
		{
			return $costs;
		}
	}

	/**
	 * Convert currency to vendor format.
	 *
	 * @param   object  &$method  The plugin method.
	 *
	 * @return  void
	 *
	 * @since   6.6.3
	 */
	public function convertToVendorCurrency(&$method)
	{
		return null;
	}
}
