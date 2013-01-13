<?php

/* this page will allow the admin to modify the text that will be displayed as the a 'tip' for each page.  It is saved in the database table 'tips' */

require_once 'dbconfig.php';

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

?>






<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
        <head>
          <title>Homeless Shelter Occupancy</title>
          <link href="style.css" rel="stylesheet" type="text/css" />
          
          
        <link href="style.css" rel="stylesheet" type="text/css" />
		<link href="screen.css" rel="stylesheet" type="text/css" />
	  
		<link href="demo_page.css" rel="stylesheet" type="text/css" />	<!-- causes indention of div id container if it is inside dive it dt_example-->
		<link href="demo_table.css" rel="stylesheet" type="text/css" />
		
          
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
	include('dbconfig.php');
	
	//-------------------Connect To Database-------------------
	$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
	mysql_select_db(DB_NAME) or die( "Unable to select database");
	
	
	$sql = 'SELECT * FROM tips_table';
	$result = mysql_query($sql) or die ('Query0 failed: ' . mysql_error());

			
		//			echo '<div id="Tips" style="width:30%;position:absolute;right:25px;top:155px;background:#B4CFEC;border: 1px solid #000000;padding: 10 10 10 10">
		//				<B>Tips</B>
		//				<br><br>
		//				<p>Click on the <B>Create</B> button after filling in the details about the Agency.<BR><BR>The fields marked <B><Font Color=Red>*</Font></B> are mandatory.
		//				</div>';
				


		// get the tip to show in the this page from database
		$sql1 = 'SELECT tips FROM tips_table WHERE page_name="edit_tips.php"';
		$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
		$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);
		
		echo '<div id="Tips" style="width:30%;position:relative;left:3%;top:3%;background:#B4CFEC;border: 1px solid #000000;padding: 10 10 10 10">
				<B>Tips</B>
				<br><br>
				<p>' . $row1{'tips'} .'</p>
				</div>';
		
		echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
		
		echo '<h3> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edit Tips:</h3>';

		
		//echo '<br><br>';
		
		echo '<div id="dt_example">';
			echo '<div id="container">';
				
				//<h3> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edit Tips:</h3>
				//<br><br>				

					
				echo '<form method="post" name="edit" action="edit_tips_proc.php">';		
					echo '<table width="100%">';
						echo '<tr><td width="50%" text-align="right"></td><td width="50%" text-align="left"></td></tr>';

						while ($row = mysql_fetch_array($result,MYSQLI_ASSOC)){
							//loop through and print out desrciption of each page being edited and a text box for the tips.
							//It will be auto filled in with the current existing entry in the database	
							echo '<tr>';
								echo '<td  text-align="left"> ' . $row{'description'} . ' </td>';
								echo '<td></td>';
							echo '</tr>';
							echo '<tr>';
								echo '<td></td>';
								echo '<td  align="right" text-align="right"><textarea name="tipsArray['. $row{'page_name'} .']" rows=10 colsS=50 > ' . $row{'tips'} . ' </textarea></td>';
							echo '</tr>';								
							echo '<tr><td> </td><td> </td></tr>';
						}
						
					echo '<tr>';
							echo'<td align="left"><input type="submit" name="cancel" value="Cancel"></td>'; 
							echo '<td align="right"><input type="submit" name="save" value="Save"></td></tr>'; 
					echo '</tr>';	
					echo '</table>';
				echo '</form>';
	
	
		//echo '<td text-align="right"> SOME TEXT </td>';
		//echo '<td  text-align="left"><TEXTAREA NAME="Address" ROWS=3 COLS=30 > QUERY DB FOR EXISTING</TEXTAREA></td>';
			echo '</div>';
		echo '</div>';

	echo '</form>';


?>

                
               
                
                
        </body>
</html>
