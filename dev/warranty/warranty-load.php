<?php
//
/*
{"key_a":["package1", "package2", "package3"],"191033183":["Дата 2, Товар 2, Заключение 123", "Дата 3, Товар 3, Заключение 125", "Дата 4, Товар 4, Заключение 125"],"key_c":3,"175106060":"Дата 5, Товар 5, Заключение 55","e":5}
*/
//
// {"key1": ["package1", "package2", "package3"]},
// {"key2": ["package1", "package2", "package3", "package4"]}
//

//.warranty >span {
//    display: flex;
//}


// User

$user = JFactory::getUser(); 

//$search_id = $user->username;
//print_r($username);
//echo '</br>';

$search_id = "";
if ($user->guest) { 
//	echo "форма для запроса номера телефона";
//	echo '</br>';
	$input = JFactory::getApplication()->input;
	$post_array = $input->getArray($_POST);
	//print_r ($post_array[phonenumber]);
	
	//$search_id_check = htmlspecialchars($_POST['phonenumber']);
	$search_id_check = htmlspecialchars($post_array[phonenumber]);
	//$search_id_check = '178588888';
	if (strlen($search_id_check) == 9) {
		echo '<p>Для получения информации о товаре, отданном на диагностику, введите Ваш номер телефона, который Вы сообщили нам в качестве контактного в сервисном центре. Заполните девятизначный номер телефона (без +375) с кодом оператора. Например 175106060.</p>';
		$search_id = $search_id_check;
	} elseif (strlen($search_id_check) != 9) {
		echo '<p>Для получения информации о товаре, отданном на диагностику, введите Ваш номер телефона, который Вы сообщили нам в качестве контактного в сервисном центре. Заполните девятизначный номер телефона (без +375) с кодом оператора. Например 175106060.</p>';
	}
	//<form action="<?php echo JRoute::_('index.php?option=com_content&view=article&id=59'); /" method="post">
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_content&view=article&id=59'); ?>" method="post">
	<p>Введите номер телефона (175106060): <input type="number" name="phonenumber" placeholder="номер телефона" value="<?php echo $search_id_check; ?>" required="required" /></p>
	<p><input type="submit" /></p>
	</form>
	<?
	//$search_id = "2915458456";
	//$search_id = htmlspecialchars($_POST['phonenumber']);
		
}
elseif (!$user->guest) {
	$search_id = $user->username;
}
if (strlen($search_id) !== 9) {
	//echo 'no key';
	//$arraywarranty = array();
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
} //else


//echo print_r($arraywarranty);
echo '<div class="warranty">';
foreach ($arraywarranty as $arraywarrantyelement) {
	echo '<span class="warrantyElement">'.$arraywarrantyelement.'</span>';
}
echo '</div>';

?>