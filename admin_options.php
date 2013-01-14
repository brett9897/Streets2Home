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
include('dbconfig.php');

//only allow administrators to this page
if($_SESSION['user_type_num'] != 2){
    header('Location: index.php');
}

// Check, if username session is NOT set then this page will jump to login page
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
}

	//-------------------Connect To Database-------------------
	$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
	mysql_select_db(DB_NAME) or die( "Unable to select database");

    //-------------------Get the tip and show it in the this page from database-------------------
	$sql1 = 'SELECT tips FROM tips_table WHERE page_name="admin_options.php"';
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
  
	echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
?>
                   
					 <h3> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Administrative Options:</h3>
					 <br><br>
                	<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="adjust_vi.php">Vulnerability Score Adjustment</a></p><br/>
                	<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="modify_survey.php">Modify Survey</a></p><br/>
                	<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#">Modify Language</a></p><br/>
                	<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="edit_tips.php">Edit Tips</a></p><br/>
                	
                	<br><br><br><br><br><br><br><br><br><br><br>
                </div>
                <div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>
                
               
                
                
        </body>
</html>
