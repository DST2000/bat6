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
					<a title="Banner P7024 (70 Ah) Asia, p" href="/catalog/akkumulators/banner/banner-p7024-70-ah-asia-p">
						<img src="/images/virtuemart/product/resized/banner-p7024-70-ah-asia-p-1_0x90.jpg" alt="banner-p7024-70-ah-asia-p-1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/banner/banner-p7024-70-ah-asia-p">Banner P7024 (70 Ah) Asia, p</a></h2></td>
				<td><span class="stock-level">2шт. </span></td>
				<td>231.83				</td>
				<td>137,22</td>
				<td>
					<span class="product-size">260-174-200</span>					</td>
				<td>
				600				</td>
			</tr>
	
	<tr>
				<td>
					<a title="Energizer plus 560413 (60 Ah) ASIA" href="/catalog/akkumulators/energizer/energizer-plus-560413-60-ah-asia">
						<img src="/images/virtuemart/product/resized/energizer-plus-560413-60-ah-asia-1_0x90.jpg" alt="energizer plus 560413 60 ah asia 1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/energizer/energizer-plus-560413-60-ah-asia">Energizer plus 560413 (60 Ah) ASIA</a></h2></td>
				<td><span class="stock-level">1шт. </span></td>
				<td>130.9				</td>
				<td>86,94</td>
				<td>
					<span class="product-size">232-173-225</span>					</td>
				<td>
				510				</td>	
			</tr>

	<tr>
				<td>
					<a title="Varta Blue Dyn (Asia) 560411 (60 Ah)р" href="/catalog/akkumulators/varta/varta-blue-dyn-asia-560411-60-ah-r">
						<img src="/images/virtuemart/product/resized/varta-blue-dyn-asia-560411-60-ah-r-1_0x90.jpg" alt="varta blue dyn asia 560411 60 ah r 1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-blue-dyn-asia-560411-60-ah-r">Varta Blue Dyn (Asia) 560411 (60 Ah)р</a></h2></td>
				<td><span class="stock-level">1шт. </span></td>
				<td>189.54				</td>
				<td>114,13</td>
				<td>
					<span class="product-size">232-173-225</span>					</td>
				<td>
				540				</td>
			</tr>

	<tr>
				<td>
					<a title="Varta Blue Dyn (Asia) 565420 (65 Ah) р" href="/catalog/akkumulators/varta/varta-blue-dyn-asia-565420-65-ah-r">
						<img src="/images/virtuemart/product/resized/varta-blue-dyn-asia-565420-65-ah-r-1_0x90.jpg" alt="varta blue dyn asia 565420 65 ah r 1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-blue-dyn-asia-565420-65-ah-r">Varta Blue Dyn (Asia) 565420 (65 Ah) р</a></h2></td>
				<td><span class="stock-level">2шт. </span></td>
				<td>226.3				</td>
				<td>152,65</td>
				<td>
					<span class="product-size">232-173-225</span>					</td>
				<td>
				570				</td>	
			</tr>

	<tr>
				<td>
					<a title="Varta Blue Dyn (Asia) 575413 (75 Ah) р" href="/catalog/akkumulators/varta/varta-blue-dyn-asia-575413-75-ah-r">
						<img src="/images/virtuemart/product/resized/varta-blue-dyn-asia-575413-75-ah-r-1_0x90.jpg" alt="varta blue dyn asia 575413 75 ah r 1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-blue-dyn-asia-575413-75-ah-r">Varta Blue Dyn (Asia) 575413 (75 Ah) р</a></h2></td>
				<td><span class="stock-level">1шт. </span></td>
				<td>238.27				</td>
				<td>160,98</td>
				<td>
					<span class="product-size">261-175-220</span>					</td>
				<td>
				680				</td>	
			</tr>

	<tr>
				<td>
					<a title="Varta Blue Dyn 574013 (74 Ah) рус" href="/catalog/akkumulators/varta/varta-blue-dyn-574013-74-ah-rus-2">
						<img src="/images/virtuemart/product/resized/varta-blue-dyn-574013-74-ah-rus-2-1_0x90.jpg" alt="varta blue dyn 574013 74 ah rus 2 1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-blue-dyn-574013-74-ah-rus-2">Varta Blue Dyn 574013 (74 Ah) рус</a></h2></td>
				<td><span class="stock-level">1шт. </span></td>
				<td>235.7				</td>
				<td>159,51</td>
				<td>
					<span class="product-size">278-175-190</span>					</td>
				<td>
				680				</td>	
			</tr>
	
	<tr>
				<td>
					
						<img class="browseProductImage-horison" src="/components/com_virtuemart/assets/images/vmgeneral/noimage.gif" alt="Не настроено изображение ">					
				</td>
				<td><h2 class="horizon-name">Varta Funstart 530034 (30 Ah)</h2></td>
				<td><span class="stock-level">1 шт. </span></td>
				<td>				</td>
				<td>65,50</td>
				<td>
					<span class="product-size"></span>					</td>
				<td>
								</td>
				<td>
					
				</td>	
			</tr>
	
	<tr>
				<td>
					<a title="Varta POWERSPORTS 008011 (8 Ah 6V)" href="/catalog/akkumulators/varta/varta-powersports-008011-8-ah-6v-2">
						<img src="/images/virtuemart/product/resized/varta-powersports-008011-8-ah-6v-2-1_0x90.jpg" alt="varta-powersports-008011-8-ah-6v-2-1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-008011-8-ah-6v-2">Varta POWERSPORTS 008011 (8 Ah 6V)</a></h2></td>
				<td><span class="stock-level">2шт. </span></td>
				<td>77.9				</td>
				<td>48,78</td>
				<td>
					<span class="product-size">91-83-161</span>					</td>
				<td>
				40				</td>	
			</tr>
		
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
					<a title="Varta POWERSPORTS 511013 (11 Ah)" href="/catalog/akkumulators/varta/varta-powersports-511013-11-ah-2">
						<img src="/images/virtuemart/product/resized/varta-powersports-511013-11-ah-2-1_0x90.jpg" alt="varta powersports 511013 11 ah 2 1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-511013-11-ah-2">Varta POWERSPORTS 511013 (11 Ah)</a></h2></td>
				<td><span class="stock-level">2шт. </span></td>
				<td>84.9				</td>
				<td>59,95</td>
				<td>
					<span class="product-size">136-91-146</span>					</td>
				<td>
				90				</td>	
			</tr>
	
	<tr>
				<td>
					<a title="Varta POWERSPORTS AGM 503014 (3 Ah)" href="/catalog/akkumulators/varta/varta-powersports-agm-503014-3-ah-2">
						<img src="/components/com_virtuemart/assets/images/vmgeneral/noimage.gif" alt="Не настроено изображение " class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-agm-503014-3-ah-2">Varta POWERSPORTS AGM 503014 (3 Ah)</a></h2></td>
				<td><span class="stock-level">4шт. </span></td>
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
				<td><span class="stock-level">7шт. </span></td>
				<td>87.72				</td>
				<td>43,87</td>
				<td>
					<span class="product-size">114-49-86</span>					</td>
				<td>
				30				</td>	
			</tr>
	
	<tr>
				<td>
					<a title="Varta POWERSPORTS AGM 508901 (8 Ah)" href="/catalog/akkumulators/varta/varta-powersports-agm-508901-8-ah-2">
						<img src="/images/virtuemart/product/resized/varta-powersports-agm-508901-8-ah-2-1_0x90.jpg" alt="varta powersports agm 508901 8 ah 2 1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-agm-508901-8-ah-2">Varta POWERSPORTS AGM 508901 (8 Ah)</a></h2></td>
				<td><span class="stock-level">1шт. </span></td>
				<td>131.68				</td>
				<td>87,16</td>
				<td>
					<span class="product-size">150-87-93</span>					</td>
				<td>
				150				</td>	
			</tr>

	<tr>
				<td>
					<a title="Varta POWERSPORTS AGM 509901 (9 Ah)" href="/catalog/akkumulators/varta/varta-powersports-agm-509901-9-ah-2">
						<img src="/images/virtuemart/product/resized/varta-powersports-agm-509901-9-ah-2-1_0x90.jpg" alt="varta-powersports-agm-509901-9-ah-2-1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-agm-509901-9-ah-2">Varta POWERSPORTS AGM 509901 (9 Ah)</a></h2></td>
				<td><span class="stock-level">2шт. </span></td>
				<td>155.64				</td>
				<td>100,44</td>
				<td>
					<span class="product-size">150-87-110</span>					</td>
				<td>
				200				</td>	
			</tr>
	
	<tr>
				<td>
					<a title="Varta POWERSPORTS AGM 509902 (8 Ah)" href="/catalog/akkumulators/varta/varta-powersports-agm-509902-8-ah-2">
						<img src="/images/virtuemart/product/resized/varta-powersports-agm-509902-8-ah-2-1_0x90.jpg" alt="varta-powersports-agm-509902-8-ah-2-1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-agm-509902-8-ah-2">Varta POWERSPORTS AGM 509902 (8 Ah)</a></h2></td>
				<td><span class="stock-level">1шт. </span></td>
				<td>131.01				</td>
				<td>87,74</td>
				<td>
					<span class="product-size">149-70-105</span>					</td>
				<td>
				115				</td>
			</tr>
	
	<tr>
				<td>
					<a title="Varta POWERSPORTS AGM 512903 (12 Ah)" href="/catalog/akkumulators/varta/varta-powersports-agm-512903-12-ah-2">
						<img src="/images/virtuemart/product/resized/varta-powersports-agm-512903-12-ah-2-1_0x90.jpg" alt="varta powersports agm 512903 12 ah 2 1" class="browseProductImage-horison">					</a>
				</td>
				<td><h2 class="horizon-name"><a href="/catalog/akkumulators/varta/varta-powersports-agm-512903-12-ah-2">Varta POWERSPORTS AGM 512903 (12 Ah)</a></h2></td>
				<td><span class="stock-level">1шт. </span></td>
				<td>160.37				</td>
				<td>84,18</td>
				<td>
					<span class="product-size">152-70-150</span>					</td>
				<td>
				190				</td>
			</tr>
		
	
</tbody>	
	
</table>