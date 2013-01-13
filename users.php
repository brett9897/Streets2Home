 <!DOCTYPE html>
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
			<body>


<?php
include('header.php');
require_once 'dbconfig.php';          
// Inialize session
session_start();					//must be called on every page that wants to use variables stored in session

display_users();

function display_users(){
    //-------------------Connect To Database-------------------
    $link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
    mysql_select_db(DB_NAME) or die( "Unable to select database");
    
    //-------------------Get the tip and show it in the this page from database-------------------
	$sql1 = 'SELECT tips FROM tips_table WHERE page_name="users.php"';
	$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
	$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);

	echo '<div id="Tips" style="width:30%;position:relative;left:3%;top:3%;background:#B4CFEC;border: 1px solid #000000;padding: 10 10 10 10">
			<B>Tips</B>
			<br><br>
			<p>' . $row1{'tips'} .'</p>
			</div>';
	echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
    
    echo '<div align="center" id="wrapper">';
    echo "<table>";
    echo "<th>Username</th><th>First Name</th><th>Last Name</th><th>Phone</th>";
    echo "<th>Email</th><th>Type</th><th>Last Login</th>";
    
    $query = "SELECT username, first_name, last_name, phone, email, type_name, last_login
              FROM user NATURAL JOIN user_type
              WHERE user_type_num = type_num";
    $result = mysql_query($query) or die('Error msg: '.mysql_error().'<br/>'.
                    'sql: '.$query.'<br/>');
    while( $row = mysql_fetch_array($result, MYSQLI_ASSOC) ){
        echo "<tr>";
        //if ( mysql_num_fields($row) != 0 ){
            foreach( $row as $col_name => $val ){
                echo "<td>".$val."</td>";
            }
        /*}
        else{
            echo "This row has no fields.<br/>";
        }*/
    }
    
    echo "</table>";
    echo "<br/><br/>";
    echo '<form action="registration.php" method="post">
            <input type="submit" name="adduser" value="Add New User"/>
            </form>';
    echo '</div>';
}

?>
        </body>
</html>
