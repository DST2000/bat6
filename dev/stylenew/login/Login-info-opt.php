<div class="row">
	<div class="col-xs-12 col-md-4">
		<button class="btn btn-block">
		<a href="/user-agreement">Пользовательское
		соглашение<br /><br />
		<sub>прочитайте и примите пользовательское соглашение</sub>
		<br />
		</a>
		</button>
<br />
	</div>
	<div class="col-xs-12 col-md-4">
		<button class="btn btn-block">
			<a href="/excel">Загрузить прайс-лист<br />
		<br />
		<sub>заполните профиль для активации загрузки</sub></a>
<br />
		</button>
<br />
	</div>
	<div class="col-xs-12 col-md-4">
		<button class="btn btn-block">
			<a href="/catalog/user">Изменить профиль<br />
		<sub>заполните профиль указав актуальный e-mail,</sub> <br /><br /><sub>номер и примите пользовательское соглашение</sub></a>
<br />
		</button>
<br />
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-4 col-md-offset-4">
		<button class="btn btn-block">
			<a href="/excelv2">Загрузить прайс-лист (Только аккумуляторы)<br />
		<br />
		<sub>заполните профиль для активации загрузки</sub></a>
<br />
		</button>
<br />
	</div>	
</div>
<h3>Уважаемые партнёры!</h3>
<p>Доводим до Вашего сведения, что со 2 марта 2020г. при имеющейся задолженности превышающей срок установленный договором, нашей автоматизированной  системой учёта будет приостановлена отгрузка товара в Ваш адрес.</p>
<p>Спасибо за понимание.</p>
<?php
//{"DocName":"№ 2-15 от 22 января 2015 г.","AccountValue":"0,75","ExpireAccountValue":"0"}

/* {/DST payable  */
$user = JFactory::getUser();
$userid = $user->get( 'id' );

$db = JFactory::getDBO();
$query = 'SELECT `payables` ' . ' FROM `#__virtuemart_userinfos` ' . ' WHERE `virtuemart_user_id` = ' . $userid;
$db->setQuery( $query );
$result1 = $db->LoadResult();
$payables_j_arr = explode( "|", $result1 );

foreach ( $payables_j_arr as $payables_j ) {
	$payables[] = json_decode( $payables_j, true );
}
if ( count( $payables ) > 0 ) {
	foreach ( $payables as $payable ) {

		echo '<div class="payable"><p class="payable">';
		if ( is_array( $payable ) ) {
			foreach ( $payable as $key => $value ) {
				if ( $key == 'DocName' ) {
					echo ("Ваша задолженность по договору $value ");
				} elseif ( $key == 'AccountValue' ) {
					echo ("составила <span class='AccountValue'> $value</span>");
				}
				elseif ( $key == 'ExpireAccountValue' ) {
					echo (", просроченная задолженность составляет <span class='ExpireAccountValue'> $value </span>");;
				}
			}
		}
		echo '</p></div>';
	}
}
/* }/DST payable  */
?>
<p>Просим Вас своевременно оплачивать возникающую задолженность.</p>
<p>Надеемся на дальнейшее плодотворное сотрудничество.</p>
<p></p>
</br>
<h3>Внимание. Акция!</h3>
<p>Предлагаем Вашему вниманию остатки уценённых аккумуляторных батарей 2015-2018 годов выпуска. Все мотоциклетные аккумуляторы новые, сухозаряженные с гарантией 6 мес.</p>
<p></p>
<table class="table table-striped">
<thead>
	<tr>
		<th scope="col">#</th>
		<th scope="col" class="product-name">Наименование</th>
		<th scope="col">Остаток</th>
		<th scope="col">Базовая цена</th>
		<th scope="col">Цена уценки</th>
		<th scope="col">Размеры</th>
		<th scope="col">Пуск. ток</th>
	</tr>
</thead>
<tbody>		
		
	<tr>
				<td>
					<a title="Varta POWERSPORTS 503012 (3 Ah)" href="/catalog/akkumulators/varta/varta-powersports-503012-3-ah-2">
						<img src="/components/com_virtuemart/assets/images/vmgeneral/noimage.gif" alt="Не настроено изображение " class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-503012-3-ah-2">Varta POWERSPORTS 503012 (3 Ah)</a></h2></td>
				<td><span class="stock-level">1шт. </span></td>
				<td>48.12				</td>
				<td>33,93</td>
				<td>
					<span class="product-size">100-58-112</span>					</td>
				<td>
				30				</td>
			</tr>
		
	<tr>
				<td>
					<a title="Varta POWERSPORTS AGM 503014 (3 Ah)" href="/catalog/akkumulators/varta/varta-powersports-agm-503014-3-ah-2">
						<img src="/components/com_virtuemart/assets/images/vmgeneral/noimage.gif" alt="Не настроено изображение " class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-agm-503014-3-ah-2">Varta POWERSPORTS AGM 503014 (3 Ah)</a></h2></td>
				<td><span class="stock-level">2шт. </span></td>
				<td>62.95				</td>
				<td>38,91</td>
				<td>
					<span class="product-size">114-71-86</span>					</td>
				<td>
				40				</td>	
			</tr>	
	
	<tr>
				<td>
					<a title="Varta POWERSPORTS AGM 503903 (2 Ah)" href="/catalog/akkumulators/varta/varta-powersports-agm-503903-2-ah-2">
						<img src="/images/virtuemart/product/resized/varta-powersports-agm-503903-2-ah-2-1_0x90.jpg" alt="varta-powersports-agm-503903-2-ah-2-1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-agm-503903-2-ah-2">Varta POWERSPORTS AGM 503903 (2 Ah)</a></h2></td>
				<td><span class="stock-level">5шт. </span></td>
				<td>87.72				</td>
				<td>43,87</td>
				<td>
					<span class="product-size">114-49-86</span>					</td>
				<td>
				30				</td>	
			</tr>
		

</tbody>	
	
</table>