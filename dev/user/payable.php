<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' )or die();


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

		echo '<div class="payable">';

		foreach ( $payable as $key => $value ) {
			if ( $key == 'DocName' ) {
				echo( $value );
			} elseif ( $key == 'AccountValue' ) {
				echo( $value );
			}
			elseif ( $key == 'ExpireAccountValue' ) {
				echo( $value );
			}
		}
		echo '</div>';
	}
}
/* }/DST payable  */