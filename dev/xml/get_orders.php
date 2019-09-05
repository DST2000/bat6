<?
require_once('tools.inc.php');

	$xml_out = "<root>";
		
		$oid = null;
		$total_price = 0;
		$total_price_netto = 0;
		$spec_code = $addr = '';
		$order_result = mysql_query($order_sql = "
		SELECT o.*, op.prod_id as prod_id, op.name as prod_name, op.price, op.quantity, op.category_name, c.id as client_id, c.name as client_name, c.inn
		FROM `order` o 
		left join `clients` c on c.id = user_id
		inner join `order_products` op on op.order_id = o.id
		where o.uploaded is null
		order by o.status desc, o.id desc, o.CreatedOn");
		
		while($order = mysql_fetch_assoc($order_result)){
			
			
			if ($oid != $order['id'] ){
				if ($oid !== null){
					$xml_out .= "</products>";
					$xml_out .= "<addr>$addr </addr>";
					$xml_out .= "<spec_code>$spec_code </spec_code>";
					$xml_out .= "<total_price>$total_price</total_price>";
					$xml_out .= "<total_price_netto>$total_price_netto</total_price_netto>";
					$xml_out .= "</order>";
					$total_price = 0;
					$total_price_netto = 0;
				}
				
				$oid = $order['id'];
				$spec_code =$order['spec_code'];
				$addr =$order['addr'];

				// печатаем шапку
				$xml_out .= "<order id='$oid' CreatedOn='{$order['CreatedOn']}' >";
				$xml_out .= "<client>";
					$xml_out .= "<client_id>{$order['client_id']}</client_id>";
					$xml_out .= "<client_name>{$order['client_name']}</client_name>";
					$xml_out .= "<inn>{$order['inn']}</inn>";
				$xml_out .= "</client>";
				$xml_out .= "<products>";
			}
			
			$total_price += $order['price']*$order['quantity'];
			$total_price_netto += $order['price'] / 1.2 * $order['quantity'];
			$xml_out .= "<product>";
				$xml_out .= "<product_id>{$order['prod_id']}</product_id>";
				$xml_out .= "<product_name>{$order['prod_name']}</product_name>";
				$xml_out .= "<quantity>{$order['quantity']}</quantity>";
				$xml_out .= "<price>{$order['price']}</price>";
				$xml_out .= "<price_netto>".($order['price'] / 1.2)."</price_netto>";
			$xml_out .= "</product>";
		}
		if (mysql_num_rows($order_result) > 0){
			$xml_out .= "</products>";
			$xml_out .= "<addr>$addr </addr>";
			$xml_out .= "<spec_code>$spec_code </spec_code>";
			$xml_out .= "<total_price>$total_price</total_price>";
			$xml_out .= "<total_price_netto>$total_price_netto</total_price_netto>";
			$xml_out .= "</order>";
		}
	$xml_out .= "</root>";
	
	$order_upd = mysql_query($order_upd = "UPDATE `order` set `uploaded` = NOW() where `uploaded` is null");
	
	header("Content-Type: text/xml");
	echo $xml_out;
		?>

			
			
