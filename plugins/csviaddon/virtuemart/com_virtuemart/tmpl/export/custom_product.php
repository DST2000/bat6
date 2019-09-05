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

/** @var JForm $form */
$form = $this->forms->custom_product;

// Set the layout
$layoutPath = JPATH_ADMINISTRATOR . '/components/com_csvi/layouts';
$layout     = new JLayoutFile('csvi.templatefield', $layoutPath);
$data       = array('form' => $form);
?>
<div class="span6">
	<h3><?php echo JText::_('COM_CSVI_EXPORT_CUSTOM_PRODUCT'); ?></h3>
	<?php
	$fields = array(
		'language',
		'exportsef',
		'cachesefurls',
		'emptysefcache',
		'producturl_suffix',
		'vm_itemid',
		'picture_limit',
		'featured',
		'category_separator',
		'product_categories',
		'publish_state_categories',
		'incl_subcategory',
		'parent_only',
		'child_only',
		'custom_title',
	);

	foreach ($fields as $index => $field)
	{
		$data['field'] = $field;
		echo $layout->render($data);
	}
	?>

	<div class="control-group">
		<label class="control-label <?php echo $form->getField('productskufilter', 'jform')->labelClass; ?>" for="<?php echo $form->getField('productskufilter', 'jform')->id; ?>">
			<?php echo JText::_('COM_CSVI_' . $form->getField('productskufilter', 'jform')->id . '_LABEL'); ?>
		</label>
		<div class="controls">
			<?php echo $form->getInput('incl_productskufilter', 'jform'); ?>
			<span class="help-block">
				<?php echo JText::_('COM_CSVI_' . $form->getField('incl_productskufilter', 'jform')->id . '_DESC'); ?>
			</span>
			<span class="cron-block">
				<?php echo str_replace('jform_', 'form.', $form->getField('incl_productskufilter', 'jform')->id); ?>
			</span>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<?php echo $form->getInput('productskufilter', 'jform'); ?>
			<span class="help-block">
				<?php echo JText::_('COM_CSVI_' . $form->getField('productskufilter', 'jform')->id . '_DESC'); ?>
			</span>
			<span class="cron-block">
				<?php echo str_replace('jform_', 'form.', $form->getField('productskufilter', 'jform')->id); ?>
			</span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label <?php echo $form->getField('stocklevelstart', 'jform')->labelClass; ?>" for="<?php echo $form->getField('stocklevelstart', 'jform')->id; ?>">
			<?php echo JText::_('COM_CSVI_' . $form->getField('stocklevelstart', 'jform')->id . '_LABEL'); ?>
		</label>
		<div class="controls">
			<?php echo $form->getInput('stocklevelstart', 'jform'); ?>
			<?php echo $form->getInput('stocklevelend', 'jform'); ?>
			<span class="help-block">
				<?php echo JText::_('COM_CSVI_' . $form->getField('stocklevelstart', 'jform')->id . '_DESC'); ?>
			</span>
			<span class="cron-block">
				<?php echo str_replace('jform_', 'form.', $form->getField('stocklevelstart', 'jform')->id); ?>
				/
				<?php echo str_replace('jform_', 'form.', $form->getField('stocklevelend', 'jform')->id); ?>
			</span>
		</div>
	</div>

	<?php
	$data['field'] = 'shopper_groups';
	echo $layout->render($data);
	$data['field'] = 'manufacturers';
	echo $layout->render($data);
	?>
</div>
<div class="span6">
	<h3><?php echo JText::_('COM_CSVI_PRICE_OPTIONS'); ?></h3>
	<?php
	$data['field'] = 'shopper_group_price';
	echo $layout->render($data);
	?>

	<div class="control-group">
		<label class="control-label <?php echo $form->getField('pricefrom', 'jform')->labelClass; ?>" for="<?php echo $form->getField('pricefrom', 'jform')->id; ?>">
			<?php echo JText::_('COM_CSVI_' . $form->getField('pricefrom', 'jform')->id . '_LABEL'); ?>
		</label>
		<div class="controls">
			<?php echo $form->getInput('priceoperator', 'jform'); ?>
			<?php echo $form->getInput('pricefrom', 'jform'); ?>
			<?php echo $form->getInput('priceto', 'jform'); ?>
			<span class="help-block">
				<?php echo JText::_('COM_CSVI_' . $form->getField('pricefrom', 'jform')->id . '_DESC'); ?>
			</span>
			<span class="cron-block">
				<?php echo str_replace('jform_', 'form.', $form->getField('priceoperator', 'jform')->id); ?>
				/
				<?php echo str_replace('jform_', 'form.', $form->getField('pricefrom', 'jform')->id); ?>
				/
				<?php echo str_replace('jform_', 'form.', $form->getField('priceto', 'jform')->id); ?>
			</span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label <?php echo $form->getField('price_quantity_start', 'jform')->labelClass; ?>" for="<?php echo $form->getField('price_quantity_start', 'jform')->id; ?>">
			<?php echo JText::_('COM_CSVI_' . $form->getField('price_quantity_start', 'jform')->id . '_LABEL'); ?>
		</label>
		<div class="controls">
			<?php echo $form->getInput('price_quantity_start', 'jform'); ?>
			<?php echo $form->getInput('price_quantity_end', 'jform'); ?>
			<span class="help-block">
				<?php echo JText::_('COM_CSVI_' . $form->getField('price_quantity_start', 'jform')->id . '_DESC'); ?>
			</span>
			<span class="cron-block">
				<?php echo str_replace('jform_', 'form.', $form->getField('price_quantity_start', 'jform')->id); ?>
				/
				<?php echo str_replace('jform_', 'form.', $form->getField('price_quantity_end', 'jform')->id); ?>
			</span>
		</div>
	</div>

	<?php
	$data['field'] = 'targetcurrency';
	echo $layout->render($data);
	?>
	<div class="advancedUser">
		<?php
		$data['field'] = 'force_shopper_group_price';
		echo $layout->render($data);
		?>
	</div>
</div>
