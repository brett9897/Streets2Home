<?php
require_once 'dbconfig.php';
// Inialize session
session_start();

// Check, if username session is NOT set then this page will jump to login page
if (!isset($_SESSION['username'])) {
header('Location: index.php');
}
?>






<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
	<head>
	  <title>Homeless Shelter Occupancy</title>
	  <link href="style.css" rel="stylesheet" type="text/css" />
	  <link href="screen.css" rel="stylesheet" type="text/css" />
	  <!--jQuery UI stuff-->
      <link href="css/overcast/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
      <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
      <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
      <script src="js/button.js" text="text/javascript"></script>
	</head>
	<body onload="javascript:setOffsets()">
		
<?php
include('header.php');
include ('compute_vi.php');
	echo "<br/>";
	echo '<form method="post" action="adjust_vi.php">';	
	echo "<table>";	
	
	display_vi_info();
	echo '<tr><td></td><td align="right"><input type="submit" name="submit" value="Update Weights"></td></tr>';
	echo "</table>";
	echo '</form>';	
	
	function display_vi_info(){
	//-------------------Connect To Database-------------------
	$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
	mysql_select_db(DB_NAME) or die( "Unable to select database");
	
	//-------------------Get the tip and show it in the this page from database-------------------
	$sql1 = 'SELECT tips FROM tips_table WHERE page_name="adjust_vi.php"';
	$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
	$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);

	$tips = trim($row1{'tips'});
	if( $tips != null && $tips != "" )
	{
	    echo '<div id="tips">
	            <strong>Tips</strong>
	            <br><br>
	            <p>' . $tips .'</p>
	          </div>';
	}
	
	//ECHO "ABOUT TO ENTER POST<BR/>";
	if(isset($_POST['submit']))
	{
		//ECHO "ENTERED POST<BR/>";			
		foreach( $_POST as $vi_num => $updated_weight ) {
			//echo "<br/>vi num is $vi_num<br/>";
			//echo "<br/>new weight is $updated_weight<br/>";
			if($vi_num == 'submit'){
				continue;
			}
			$sql = "UPDATE vulnerability_conf
					SET weight = $updated_weight
					WHERE vi_num = $vi_num";
			$result = mysql_query($sql) or die('Update failed<br/>
					Error Message: '.mysql_error().'<br/>
					Failed SQL Statement: '.$sql.'<br/>');
		}
       $mtime = microtime();
       $mtime = explode(" ",$mtime);
       $mtime = $mtime[1] + $mtime[0];
       $starttime = $mtime; 
		updateClientViScores();
        $mtime = microtime();
       $mtime = explode(" ",$mtime);
       $mtime = $mtime[1] + $mtime[0];
       $endtime = $mtime;
       $totaltime = ($endtime - $starttime);
       //echo "This update took ".$totaltime." seconds";
		//echo "got here";
        
        $myFile = "timing.txt";
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh, $totaltime);
        fclose($fh);
        
		header( 'Location: vi_success.php' ) ;
	}
	
	$sql = "SELECT vi_num, description, weight
			FROM vulnerability_conf";
	$result = mysql_query($sql) or die('Query1 in display_vi_info failed<br/>
			Error Message: '.mysql_error().'<br/>
			Failed SQL Statement: '.$sql.'<br/>');
	echo "<br/><br/>";
	echo "<th><h3>Description of Vulnerability</h3></th><th><h3></he>Weight</h3></th>";
	while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
		echo "<tr>";
		echo "<td>".$row{description}."</td>";
		echo "<td><input type='text' name='".$row{vi_num}."' 
				id='".$row{vi_num}."' value='".$row{weight}."'
				size='3'></input></td>";
		echo "</tr>";
	}
}

?>

		</div>
		<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>
		

		
		
	</body>
</html>
