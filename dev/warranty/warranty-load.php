<?php

$user = JFactory::getUser(); 



$search_id = "";
if ($user->guest) { 
	$input = JFactory::getApplication()->input;
	$post_array = $input->getArray($_POST);

	$search_id_check = htmlspecialchars($post_array[phonenumber]);

	if (strlen($search_id_check) == 9) {
		echo '<h3>Для розничных покупателей:</h3>';
		echo '<p>Для получения информации о товаре, отданном на диагностику, введите Ваш номер телефона, который Вы сообщили нам в качестве контактного в сервисном центре. Заполните девятизначный номер телефона (без +375) с кодом оператора. Например для городсого номера в Минске <b>175106060</b>, МТС <b>333514747</b>, А1 <b>293514747</b>.</p>';
		echo '<div>Бланк заявления для физических лиц в формате <a href='.'"'.'media/docs/zayavleniye_fiz_lic.pdf'.'"'.' target='.'"'.'_blank'.'"'.' rel='.'"'.'noopener noreferrer'.'"'.'> .pdf</a> , <a href='.'"'.'media/docs/zayavleniye_fiz_lic.doc'.'"'.' target='.'"'.'_blank'.'"'.' rel='.'"'.'noopener noreferrer'.'"'.'> .doc</a></div>';
		
		$search_id = $search_id_check;
	} elseif (strlen($search_id_check) != 9) {
		echo '<h3>Для розничных покупателей:</h3>';
		echo '<p>Для получения информации о товаре, отданном на диагностику, введите Ваш номер телефона, который Вы сообщили нам в качестве контактного в сервисном центре. Заполните девятизначный номер телефона (без +375) с кодом оператора. Например для городского номера в Минске <b>175106060</b>, МТС <b>333514747</b>, А1 <b>293514747</b>.</p>';
		echo '<div>Бланк заявления для физических лиц в формате <a href='.'"'.'media/docs/zayavleniye_fiz_lic.pdf'.'"'.' target='.'"'.'_blank'.'"'.' rel='.'"'.'noopener noreferrer'.'"'.'> .pdf</a> , <a href='.'"'.'media/docs/zayavleniye_fiz_lic.doc'.'"'.' target='.'"'.'_blank'.'"'.' rel='.'"'.'noopener noreferrer'.'"'.'> .doc</a></div>';
	}
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_content&view=article&id=59'); ?>" method="post">
	<p><b>Введите номер телефона</b> (175106060): <input type="number" class="btn btn-default btn-lg active" name="phonenumber" placeholder="НОМЕР ТЕЛЕФОНА" value="<?php echo $search_id_check; ?>" required="required" /> <i class="fas fa-phone"></i></p>
	<p><input type="submit" /></p>
	</form>
	<?

		
}
elseif (!$user->guest) {
	$search_id = $user->username;
}
if (strlen($search_id) !== 9) {
	//echo 'no key';
}
elseif (strlen($search_id) == 9) {
///

$module = JModuleHelper::getModule("mod_flexi_customcode","warranty");

$params = new JRegistry($module->params);
$paramscode = $params ['code_area'];

$arraywarranty = array();
$array = json_decode($paramscode, true);

foreach ($array as $key =>$value){
	if ($search_id == $key) {
		if (!is_array($value)) {
			$value = (array)$value;
		}
		
		foreach ($value as $arraydata) {
			$arraywarranty[] = $arraydata;
		}
	}
}

if (count($arraywarranty) == 0){
	echo '<p>По вашему заросу нет данных для отображения</p>';
}	
} 

echo '<div class="warranty">';
foreach ($arraywarranty as $arraywarrantyelement) {
	echo '<span class="warrantyElement alert-success">'.$arraywarrantyelement.'</span>';
}
echo '</div>';

?>