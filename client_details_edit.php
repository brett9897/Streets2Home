<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<head>
			  <title>Client Details</title>
			  <link href="style.css" rel="stylesheet" type="text/css" />
			  <link href="screen.css" rel="stylesheet" type="text/css" />
			  
			  <link href="demo_table.css" rel="stylesheet" type="text/css" />
			  <!--jQuery UI stuff-->
              <link href="css/overcast/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
              <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
              <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
              <script src="js/button.js" text="text/javascript"></script>	
			  		
			  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
		</head>

		<body onload="javascript:setOffsets()">
			<div id="page">
				<div id="header" style="height:75px;">		
					<div style="width:60%;position:absolute;top:-25px;">
					<h1><a href="#">Street to Home 2.0</a></h1>
					<p class="description" style="font-size: 0.8em;">A Cooperation between Georgia Tech and the United Way of Metropolitan Atlanta</p>
					</div>
					<div style="position:absolute;width:40%;top:0px;left:60%"> 
					<span class="logged_in_user"><B>Currently logged in as: <Font Color="Yellow"><?php echo $_SESSION['LOGIN']; ?></Font> | <a href="logout.php"><Font Color="Yellow">Signout</Font></a></B></span>	
					</div>
					<ul class="menu"><li><a href="survey.php">Survey</a></li>
					<li><a href="report_server_side.php">Reports</a></li>
					<li></li><li><a href="#">Users</a></li>
					<li><a href="#">Modify Language</a></li>
					<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
					<li><a href="admin_options.php">Admin</a></li>
					<li><a href="#"><i>Help</i></a></li>
					<li><a href="javascript:toggle_feedback_form()"><i>Feedback?</i></a></li>
					</ul>					
				</div>

				<hr />


<?php
	session_start();
	//we have $_SESSION['client_id'] passed in from client_details.php edit link.
	
	//checking for admin status
		if($_SESSION['user_type_num'] != 2){
			header('Location: index.php');
		}
	
	$colName = $_GET['column_name'];
    
    if($colName == "personal_photo"){
        header('Location: change_photo.php');       
    }
	
	//-------------------Include Connection information file-------------------
	include('dbconfig.php');
	include ('survey_display_function.php');
	//-------------------Connect To Database-------------------
	$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
	mysql_select_db(DB_NAME) or die( "Unable to select database");
	
	//-------------------Get the tip and show it in the this page from database-------------------
	$sql1 = 'SELECT tips FROM tips_table WHERE page_name="client_details.php"';
	$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
	$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);
	echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
	$tips = trim($row1{'tips'});
	if( $tips != null && $tips != "" )
	{
	    echo '<div id="tips">
	            <strong>Tips</strong>
	            <br><br>
	            <p>' . $tips .'</p>
	          </div>';
	}

	echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
	
	
	$sql = 'SELECT 
				question_id,
				question_text,
				question_response_type,
				response_table
			 FROM
				form_questions
			 NATURAL JOIN
				map_question_id_to_client_column_name
			 WHERE
				column_name=\''. $colName .'\'';
				
	$result =   mysql_query($sql) or die ('Query0 failed:'. mysql_error());		//THIS SHOULD ONLY RETURN ONE RESPONSE...AS LONG AS THERE ARE NO DUPLICATE NAMES IN THE DB ABOUT QUESTIONS.
					
	$row = mysql_fetch_array($result,MYSQLI_ASSOC);														//$row becomes the next question row entry in returned results from the table.
	
	echo '<form method="post" name="edit" action="client_detail_edit_proc.php">';		
		echo '<table width="100%">';
			echo '<tr><td width="50%" text-align="right"></td><td width="50%" text-align="left"></td></tr>';
			echo '<tr>';
						/*
						if($row{'question_response_type'} != 5){
							//this is a label, so don't add to input array
							array_push($input_id_array, $row{'question_id'});
						}
						*/
						
						echo display_question($row{'question_id'},
							$row{'question_text'}, $row{'question_response_type'}, $row);
						
			echo '</tr>';	
			echo '<tr>';
					echo'<td align="left"><input type="submit" name="cancel" value="Cancel"></td>'; 
					echo '<td align="right"><input type="submit" name="save" value="Save"></td></tr>'; 
			echo '</tr>';	
		echo '</table>';
	echo '</form>';
//question id, question text, type, and responce table, and clients prior response.

	$_SESSION['column_to_update'] = $colName;
	$_SESSION['questionID'] = $row{'question_id'};
	mysql_free_result($result);
	mysql_close($link);
?>


			</div>
			<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>
			<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>

	</body>
	</html>
