<?php
	session_start();
    include('compute_vi.php');
	//we have $_SESSION['client_id'] passed in from client_details.php edit link.
	//we have $_SESSION['column_to_update']		RESET IT TO NULL AFTER PROCESSING INPUT.
	//we have $_SESSION['questionID']			RESET IT TO NULL AFTER PROCESSING INPUT.
	
	//checking for admin status
		if($_SESSION['user_type_num'] != 2){
			//unauthorized access...destroy all session vars and redirect to login screen.
			session_destroy();
			header('Location: index.php');
		}
		
		if($_POST['cancel'] != null){
			//user wants to cancel the update process and return back to the client details
			$_SESSION['column_to_update'] = null;
			$_SESSION['questionID'] = null;
			header('Location: client_details.php?client_id=' . $_SESSION['client_id']);
		}
		else {
			//user wants to update an entry in the database
				//-------------------Include Connection information file-------------------
				include('dbconfig.php');
				include ('survey_display_function.php');
				//-------------------Connect To Database-------------------
				$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
				mysql_select_db(DB_NAME) or die( "Unable to select database");

				//echo '$_SESSION[\'client_id\'] =  ' . $_SESSION['client_id'] . '<br />';
				//echo '$_SESSION[\'column_to_update\'] =  ' . $_SESSION['column_to_update'] . '<br />';
				//echo '$_POST[\'$_SESSION[\'questionID\']] =  ' . $_POST[$_SESSION['questionID']] . '<br />';
				
				$sql = 'UPDATE client SET ' . $_SESSION['column_to_update'] .'=\''. $_POST[$_SESSION['questionID']] . '\' WHERE client_id =' .$_SESSION['client_id'];
				echo $sql . '<br />';
				$resultsMap =   mysql_query($sql) or die ('Query1 failed:'. mysql_error());
                
                computeVI($_SESSION['client_id']);
				
				mysql_close($link);
				
				$_SESSION['column_to_update'] = null;
				$_SESSION['questionID'] = null;
				header('Location: client_details.php?client_id=' . $_SESSION['client_id']);
		}
		
		
?>
