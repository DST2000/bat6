<?php
/*
 * @title		com_vm_products_csv
 * @version		3.1.3e
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!'); 

// redirect request if view and by task ...
$view = $_REQUEST['view'];
if ($view) {
	jimport('joomla.application.component.controller');
	$input = JFactory::getApplication()->input;
	$task = $input->get('task', '');
	if ($task && strpos($task, '.') === false)
		$input->set('task', $view.'.'.$task);
	$controller = JControllerLegacy::getInstance('vm_products_csv');
	$controller->execute($input->get('task')); // task may have been updated by JControllerLegacy::getInstance()
	$controller->redirect();
	return;
}

// manual vars ...
$manual_langs = array(
	'English (GB)' => 'en_gb',
	'Français' => 'fr_fr',
	'Slovenský' => 'sk_sk'
);
$vmlang = $default_lang = reset($manual_langs);
$is_joomla = defined('JPATH_BASE');
$file_dir = dirname(__FILE__);
$image_path = 'images/';
if (defined('JPATH_ADMINISTRATOR')) {
	require_once(JPATH_ADMINISTRATOR . '/components/com_vm_products_csv/vm_products_csvUtils.php');
	vm_products_csvUtils::addSubmenu();
	vm_products_csvUtils::addToolbar();
	$admin_url = vm_products_csvUtils::admin_url();
	if (!class_exists('VmConfig')) {
		require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
		VmConfig::loadConfig();
	}
	$vmlang = VmConfig::get('vmlang');
}
else
	$admin_url = str_replace('//', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '/', realpath($file_dir.'/../../'))).'/';

// determine manual language ...
$manual_lang = empty($_REQUEST['manual_lang']) ? $vmlang : $_REQUEST['manual_lang'];
$found_lang = '';
$lang_short = substr($manual_lang, 0, 2);
foreach ($manual_langs as $lang) {
	if ($lang == $manual_lang) {
		$found_lang = $lang;
		break;
	}
	elseif (!$found_lang && substr($lang, 0, 2) == $lang_short)
		$found_lang = $lang;
}
$manual_lang = (!$found_lang) ? $default_lang : $found_lang;

// build manual language select options ...
$manual_lang_select_options = '';
foreach ($manual_langs as $title => $lang) {
	$manual_lang_select_options .= '<option value="'.$lang.'"';
	if ($lang == $manual_lang)
		$manual_lang_select_options .= ' selected="selected"';
	$manual_lang_select_options .= '>'.$title.'</option>';
}

// get the manual lang file ...
require($file_dir.'/man/vm_products_csv_manual.'.$manual_lang.'.php');
?>
