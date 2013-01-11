<?php
require_once 'dbconfig.php';
//This file is for generating n numbers of random clients into the client
//table for testing purposes.
function generate_clients($n){
	$sql = "SHOW columns FROM client";
	$result = mysql_query($sql) or die(mysql_error());
	for($i = 0; $i < $n; $i++){
		while($row = mysql_fetch_array($result, MYSQLI_ASSOC){
			if($row{field} == "client_id"){
				continue;
			}
			$range_result = mysql_query( " SELECT MAX(`client_id`) AS max_id , MIN(`client_id`) AS min_id FROM `client` ");
			$range_row = mysql_fetch_object( $range_result );
			$random = mt_rand( $range_row->min_id , $range_row->max_id );
			$result = mysql_query( " SELECT ".$row{field}." FROM `client` WHERE `id` >= $random LIMIT 0,1 ");
			if(!$result){
				$i--;
				continue;
			}
			 
		}
	}
}
?>
