<?php
defined ('_JEXEC') or die();

// Status Of Delimiter
$closeDelimiter = false;
$openTable = true;
$hiddenFields = '';

if(!empty($this->userFieldsCart['fields'])) {

	// Output: Userfields
	foreach($this->userFieldsCart['fields'] as $field) {
	?>
	<fieldset class="vm-fieldset-<?php echo str_replace('_','-',$field['name']) ?>">
		<div  class="cart <?php echo str_replace('_','-',$field['name']) ?>" title="<?php echo strip_tags($field['description']) ?>">
		<span class="cart <?php echo str_replace('_','-',$field['name']) ?>" ><?php echo $field['title'] ?></span>

		<?php
		if ($field['hidden'] == true) {
			// We collect all hidden fields
			// and output them at the end
			$hiddenFields .= $field['formcode'] . "\n";
		} else { ?>
				<?php echo $field['formcode'] ?>
			</div>
	<?php } ?>

	</fieldset>

	<?php
	}
	// Output: Hidden Fields
	echo $hiddenFields;
}

// {DST
	$user=JFactory::getUser();
		if($user->id>0){ 
		} else {
			?>
<button id="button-erip" type="button" class="btn btn-default navbar-btn">
Способы оплаты</button>

<div id="erip-info" style="display:none;">
			<h3 class="main-sells-footer-block-title">Оплату Вы можете произвести в любом банке:</h3>
			<ul class="main-sells-footer-list">
				<li class="main-sells-footer-list-i">	интернет-банкинге </li>
				<li class="main-sells-footer-list-i">	мобильном банкинге </li>
				<li class="main-sells-footer-list-i">	инфокиоске </li>
				<li class="main-sells-footer-list-i">	банкомате </li>
				<li class="main-sells-footer-list-i">	кассе и т.д. </li>
			</ul>
			<h3 class="main-sells-footer-block-title">Совершить оплату можно с использованием:</h3>
			<ul class="main-sells-footer-list">
				<li class="main-sells-footer-list-i">	наличных денежных средств </li>
				<li class="main-sells-footer-list-i">	любых электронных денег </li>
				<li class="main-sells-footer-list-i">	банковских платежных карточек VISA, Master Card, Belcard 
				<p>В случае отказа клиента от товара, денежные средства возвращаются в полном размере и без каких-либо комиссий. Возврат денежных средств осуществляется на ту карточку с которой была произведена оплата. Срок возврата денежных средств на карточку зависит от банка эмитента, выпустившего карточку.</p>
				</li>
			</ul>
			
			<li class="main-sells-footer-list-i"> <img class="payment-method-logo" src="/dev/logo/erip.jpg" alt="erip"> ЕРИП (АИС «Расчет») </li>
			
			<h3 class="main-sells-footer-block-title">ДЛЯ ПРОВЕДЕНИЯ ПЛАТЕЖА НЕОБХОДИМО:</h3>
			<ol class="main-sells-footer-list">
				<li>
					<h4>Выбрать </h4>
					<ul>
						<li>Пункт “Система “Расчет” (ЕРИП)</li>
						<li>Группа услуг</li>
						<li>Интернет магазины /сервисы</li>
						<li>A-Z</li>
						<li>B</li>
						<li>bat.by</li>
						<li>Выбрать оплачиваемую услугу</li>
					</ul>
				</li>
				<li>Для оплаты «Товара» ввести Номер заказа, затем Ваш номер телефона. <br>
Справка по телефонам +375 29 351 47 47, +375 33 351 47 47 
				</li>
				<li>Ввести сумму платежа (если не указана) </li>
				<li>Проверить корректность информации </li>
				<li>Совершить платеж</li>
			</ol>
			
			<p>Если Вы осуществляете платеж в кассе банка,</p> 
			<p>пожалуйста, сообщите кассиру о необходимости проведения платежа</p>
			<p>через систему ”Расчет“ (ЕРИП).</p>
		</div>

			<?php
		} 
	// }DST

?>