 <!DOCTYPE html>
    <html>
			<head>
			  <title>Homeless Shelter Occupancy</title>
			  <link href="style.css" rel="stylesheet" type="text/css" />
			  <link href="screen.css" rel="stylesheet" type="text/css" />
			  <link href="facebox.css" rel="stylesheet" type="text/css" />
			  <script src="/javascripts/jquery.js" type="text/javascript"></script>
			  <!--<script>jQuery.noConflict();</script>-->
			  <script src="/javascripts/prototype.js" type="text/javascript"></script>
			  <script src="/javascripts/effects.js" type="text/javascript"></script>
			  <script src="/javascripts/dragdrop.js" type="text/javascript"></script>
			  <script src="/javascripts/controls.js" type="text/javascript"></script>
			  <script src="/javascripts/application.js" type="text/javascript"></script>
			  <script src="/javascripts/feedback.js" type="text/javascript"></script>
			  <script src="/javascripts/facebox.js" type="text/javascript"></script>
			</head>
			<body onload="javascript:setOffsets()">
			

<?php
include('mini_header.php');


	function getFieldNames(){
		$sql = "SELECT field
				FROM registration_fields";
		$result = mysql_query($sql) or die('<br/>query1 failed in getFieldNames
			<br/>Error Message: '.mysql_error().'<br/>Failed SQL: '.$sql.'<br/>');
		$field_string = "";
		for($i = 1; $i <= mysql_num_rows($result); $i++){
			$row = mysql_fetch_array($result, MYSQLI_ASSOC);
			if($i != mysql_num_rows($result)){
				$field_string .= $row{field}.', ';
			}
			else{
				$field_string .= $row{field};
			}
		}
		return $field_string;
	}
	
	function getFieldValues($postVars){
		$sql = "SELECT field
				FROM registration_fields";
		$result = mysql_query($sql) or die('<br/>query1 failed in getFieldValues
			<br/>Error Message: '.mysql_error().'<br/>Failed SQL: '.$sql.'<br/>');
		$field_vals = "";
		for($i = 1; $i <= mysql_num_rows($result); $i++){
			$row = mysql_fetch_array($result, MYSQLI_ASSOC);
			if($i != mysql_num_rows($result)){
				if($row{field} != "password"){
					$field_vals .= '\''.$postVars{$row{field}}.'\', ';
				}
				else{
					$field_vals .= '\''.md5($postVars{$row{field}}).'\', ';
				}
			}
			else{
				if($row{field} != "password"){
					$field_vals .= '\''.$postVars{$row{field}}.'\'';
				}
				else{
					$field_vals .= '\''.md5($postVars{$row{field}}).'\'';
				}
			}
		}
		return $field_vals;
	}

	require_once 'dbconfig.php';

	$connection = mysql_pconnect(HOST,USERNAME,PASSWORD);
	mysql_select_db(DB_NAME,$connection);
	
	$success = 0;//false
	
	if(isset($_POST['submit']))
	{	
		if($_POST['password'] == $_POST['pass_confirmation']){
			$user = $_POST[username];
			$check = "SELECT username FROM user where username=$user";
			$finally = mysql_query($check);
			$field_names = getFieldNames();
			$field_values = getFieldValues($_POST);
			$query = "INSERT INTO user($field_names) VALUES($field_values)";
			$result = mysql_query($query) or $error = mysql_error($connection);
			if (!empty($error)){
				echo '<span style="color:red">Username already exists.</span>';
			}
			else			{
				// the query ran successfully
				echo "Registration successful!";
				$success = 1;
				echo '<input type="button" value="Login"
					onClick="location.href=\'index.php\';">';
			}
			
		}
		else{
			echo "<span style='color:red'>Passwords do not match</span>";
		}
	}
	if(!$success){
	echo '<h1 style="color:black">New User Registration</h1>';
	echo '<br/>';

	echo '<form method="post" action="registration.php">';	
	echo '<table>';
	$sql = "SELECT field, label
			FROM registration_fields";
	$result = mysql_query($sql) or die('<br/>query1 failed from registration.php
		<br/>Error Message: '.mysql_error().'<br/>Failed SQL: '.$sql.'<br/>');
	while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
		echo '<tr>';
		echo '<td>'.$row{label}.'</td>';
		if($row{field} != "password"){
			echo '<td><input type="text" id="'.$row{field}.'" name="'.$row{field}.'"/></td>';
		}
		else{
			echo '<td><input type="password" id="'.$row{field}.'" name="'.$row{field}.'"/></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>Confirm '.$row{label}.'</td>';
			echo '<td><input type="password" id="pass_confirmation" name="pass_confirmation"/></td>';
		}
		echo '</tr>';
	}
	echo '<tr><td></td><td align="right"><input type="submit" name="submit" value="Submit"></td></tr>'; 
	echo '</table>';
}
	mysql_close($connection);
?>
        </body>
</html>

