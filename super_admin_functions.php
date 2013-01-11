<?php
require_once 'dbconfig.php';
include('compute_vi.php');
	$username   =   USERNAME;
	$password   =   PASSWORD;
	$database   =   DB_NAME;
	$input_id_array[] = array();
	//End Variables
if (extension_loaded('gd')) { // return true if the extension’s loaded.
    echo 'Installed.';
} else {
    if (dl('gd.so')) { // dl() loads php extensions on the fly.
        echo 'Installed.';
    } else {
        echo 'Not installed.';
    }
}

	//-------------------Connect To Database-------------------
	$link   =   mysql_connect(localhost,$username,$password) or die ('Could not connect :'.  mysql_error());
	mysql_select_db($database) or die( "Unable to select database");
	$sql = 'SELECT client_id from client';
    //$sql = "UPDATE client SET personal_photo = 'uploads/buzz.gif'";
	$result = mysql_query($sql) or die('Error Msg:'.mysql_error().'<br/>sql: '.$sql.'<br/>');
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		/**$sql2 = 'update client
            set details_link = \'<a href="client_details.php?client_id='.$row{client_id}.'">Details</a>\'
            where client_id = '.$row{client_id}.';';
		**/
        $sql2 = "delete from client where first_name = '';";
        $result2 = mysql_query($sql2) or die('Error Msg:'.mysql_error().'<br/>sql: '.$sql2.'<br/>');
        
        //updateRandomClientVI($row{client_id});
}
?>
