<?php
	//// Inialize session
	//session_start();
	//
	//// Check, if username session is NOT set then this page will jump to login page
	//if (!isset($_SESSION['username'])) {
	//header('Location: index.php');
	//}
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Report List</title>
	<link href="s2h-css.css" rel="stylesheet" type="text/css" media="screen" />
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.20" />
</head>

<body>
	<div id="wrap">
	<div id="header">
	<div id="logo">
	  <h1><a href="#">Streets to Home </a></h1>
	  <h2>2.0</h2>
	</div>
	</div>
	<div id="menu-wrap">
	<div id="menu">
		<ul>
			<li class="first"><a href="reportList.php" accesskey="1" title="">Reports</a></li>
			<li><a href="survey.php" accesskey="2" title="">Enter Survey</a></li>
			<li><a href="#" accesskey="3" title="">Agencies</a></li>
			<li><a href="#" accesskey="4" title="">Contact</a></li>
			<li><a href="#" accesskey="5" title="">Help</a></li>
		</ul>
	</div>
	</div>
	
	<br/><br/><br/>

<form name="sort" method="post" action="reportList.php"> 

	<?
		$hostname = 'localhost';        // Your MySQL hostname. Usualy named as 'localhost', so you're NOT necessary to change this even this script has already online on the internet.
		$dbname   = 'StreetsToHome2'; // Your database name.
		$username = 'rharrison';             // Your database username.
		$password = '1234';                 // Your database password. If your database has no password, leave it empty.
		// Let's connect to host
		$link = mysql_connect($hostname, $username, $password) or DIE('Connection to host is failed, perhaps the service is down!');
		// Select the database
		mysql_select_db($dbname) or DIE('Database name is not available!');
	
		$sql = "SELECT * FROM client";
		$result = mysql_query($sql);
		if (!$result) {
			die('Query failed: ' . mysql_error());
		}
		/* get column names */
		$column_names[] = array();
		$i = 0;
		while ($i < mysql_num_fields($result)) {
			
			$meta = mysql_fetch_field($result, $i);
			if (!$meta) {
				echo "No information available<br />\n";
			}
			//echo "Information for column $i: column name is ".$meta->name." <br />\n";
			array_push($column_names, $meta->name);
			$i++;
		}
		mysql_free_result($result);
		mysql_close($link);
	?>
	

	<select name='sortBox'>
		<option value="first_name">First Name</option>
		<option value="last_name">Last Name</option>
		<option value="VI">Vulnerability Index</option>
		<option value="date_of_birth">DOB</option>
	</select>
	
	<div class="submit">
        <button type="submit">Sort</button>   
	</div>
</form>


	
	
<?php
	$hostname = 'localhost';        // Your MySQL hostname. Usualy named as 'localhost', so you're NOT necessary to change this even this script has already online on the internet.
	$dbname   = 'StreetsToHome2'; // Your database name.
	$username = 'rharrison';             // Your database username.
	$password = '1234';                 // Your database password. If your database has no password, leave it empty.

	// Let's connect to host
	mysql_connect($hostname, $username, $password) or DIE('Connection to host is failed, perhaps the service is down!');
	// Select the database
	mysql_select_db($dbname) or DIE('Database name is not available!');
	
	//post (back) check
	if( basename($_SERVER['REQUEST_METHOD']) == 'POST'){
	//explicit postback check
	//if( basename($_SERVER['REQUEST_METHOD']) == $_SERVER['SCRIPT_NAME']){
		$sql = "SELECT * FROM client ORDER BY " . $_POST['sortBox'] ." DESC";
	}
	else {
		$sql = "SELECT * FROM client ORDER BY VI DESC"; //WHERE (username = '" . mysql_real_escape_string($_POST['username']) . "') and (password = '" . mysql_real_escape_string($_POST['password']) . "')";
	}	
	$results = mysql_query($sql) or die ('mysql_query() failed: ' . mysql_error());
	
	echo '<table border="0">';
		echo '<tr>';		
			echo '<td width="125"><b>First Name</b></th>';
			echo '<td width="125"><b>Middle Name</b></th>';
			echo '<td width="125"><b>Last Name</b></th>';
			echo '<td width="125"><b>Nickname</b></th>';
			echo '<td width="125"><b>VI</b></th>';
			echo '<td width="125"><b>language_pref</b></th>';
			echo '<td width="125"><b>date_of_birth</b></th>';
			echo '<td width="125"><b>ethnicity</b></th>';
			/*
			echo '<td width="125"><b>time_on_streets_years</b></th>';
			echo '<td width="125"><b>time_on_streets_months</b></th>';
			echo '<td width="125"><b>times_housed_and_back</b></th>';
			echo '<td width="125"><b>frequent_sleep_spot</b></th>';
			echo '<td width="125"><b>shelter_preference_1</b></th>';
			echo '<td width="125"><b>shelter_preference_2</b></th>';
			echo '<td width="125"><b>at_risk_of_losing_housing</b></th>';
			echo '<td width="125"><b>ER_visits_3_months</b></th>';
			echo '<td width="125"><b>hospitilizations_last_year</b></th>';
			echo '<td width="125"><b>kidney_renal_dialysis</b></th>';
			echo '<td width="125"><b>liver_disease_cirrhosis</b></th>';
			echo '<td width="125"><b>HCAH</b></th>';
			echo '<td width="125"><b>hiv_aids</b></th>';
			echo '<td width="125"><b>diabetes</b></th>';
			echo '<td width="125"><b>frostbite_hypotherm_immersion_foot</b></th>';
			echo '<td width="125"><b>stroke_exhaustion</b></th>';
			echo '<td width="125"><b>emphysema</b></th>';
			echo '<td width="125"><b>asthma</b></th>';
			echo '<td width="125"><b>cancer</b></th>';
			echo '<td width="125"><b>hepatitis_c</b></th>';
			echo '<td width="125"><b>tuberculosis</b></th>';
			echo '<td width="125"><b>observe_physical</b></th>';
			echo '<td width="125"><b>abused_drugs_or_alc</b></th>';
			echo '<td width="125"><b>consumed_alc_daily_1_month</b></th>';
			echo '<td width="125"><b>injected_drugs</b></th>';
			echo '<td width="125"><b>treated_for_drug_abuse</b></th>';
			echo '<td width="125"><b>diagnosed_mental</b></th>';
			echo '<td width="125"><b>recv_treat_mental</b></th>';
			echo '<td width="125"><b>observe_mental</b></th>';
			echo '<td width="125"><b>victim_of_violence</b></th>';
			echo '<td width="125"><b>perm_disability</b></th>';
			echo '<td width="125"><b>	brain_head_trauma</b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			echo '<td width="125"><b></b></th>';
			
			echo '<td width="125"><b></b></th>';
			*/
			
		echo '</tr>';
		
		while($row = mysql_fetch_array($results, MYSQLI_ASSOC)){
			////echo stripslashes($row{first_name});
			////echo stripslashes($row{last_name});
			//echo stripslashes($row{first_name} . ' ' . $row{last_name} . ' ' . $row{nickname});
			echo '<tr>';	
				echo stripslashes('<td>' . $row{first_name} . '</td>');
				echo stripslashes('<td>' . $row{middle_name} . '</td>');
				echo stripslashes('<td>' . $row{last_name} . '</td>');
				echo stripslashes('<td>' . $row{nickname} . '</td>');
				echo stripslashes('<td>' . $row{VI} . '</td>');
				echo stripslashes('<td>' . $row{language_pref} . '</td>');
				echo stripslashes('<td>' . $row{date_of_birth} . '</td>');
				echo stripslashes('<td>' . $row{ethnicity} . '</td>');
				
				//echo stripslashes('<td>' . $row{} . '</td>');
			echo '</tr>';	
			////echo stripslashes(. ' ' . $row{last_name} . ' ' . $row{nickname});
			////echo $row{first_name} . ' ' . $row(last_name);
			//echo "<br/>";
		}
	
	echo '</table>';

?>
	
	
	
	</div>
</body>

</html>
