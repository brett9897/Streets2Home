<?php
	// Inialize session
	session_start();
	//only allow administrators to this page
	if($_SESSION['user_type_num'] != 2){
		header('Location: index.php');
	}

	// Check, if username session is NOT set then this page will jump to login page
	if (!isset($_SESSION['username'])) {
	header('Location: index.php');
	}


	if($_POST['cancel'] != null){
			//user wants to cancel the update process and return back to the client details
			header('Location: admin_options.php');
		}
	else {
		require_once 'dbconfig.php';	
		
		//-------------------Connect To Database-------------------
		$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
		mysql_select_db(DB_NAME) or die( "Unable to select database");
		//if(!empty($_POST['tipsArray'])){
		if($_POST['tipsArray'] != null){
			$tipsArray = $_POST['tipsArray'];										//tipsArray in $_POST is indexed by page_name and not numbers...e.g. tipsArray[edit_tips.php] and not tipsArray[1]
			
			$sql = 'SELECT * FROM tips_table';		//get all the page name values
			$results = mysql_query($sql) or die ('Query0 failed: ' . mysql_error());
			
			while( $row = mysql_fetch_array($results, MYSQL_ASSOC) ){
				//set all the 
				//$sql1 = 'UPDATE tips_table SET tips="'. my_real_escape_string($tipsArray['\''. $row{'page_name'} . '\'']) .' WHERE page_name="'. $row{'page_name'} .'"';
				$sql1 = 'UPDATE tips_table SET tips="'. $tipsArray[$row{'page_name'} ] . '" WHERE page_name="'. $row{'page_name'} .'"';
				echo ' sql1 = ' . $sql1 .'</br>';
				$results1 = mysql_query($sql1) or die ('Query1 failed: ' . mysql_error());
				//echo ' ---- '. $tipsArray[ $row{'page_name'}  ] .'</br>';			//this will echo out all the textarea entries that were on edit_tip.php page														
				//echo $row{'page_name'} .'</br>';
			}
			
			mysql_free_result($results);
			mysql_free_result($results1);
			mysql_close($link);
		}
		
			header('Location: admin_options.php');
	}

?>
