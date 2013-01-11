<?php
include('dbconfig.php');

// Inialize session
session_start();
//session_destroy();

// Include database connection settings
//include('config.inc');
              // Your database password. If your database has no password, leave it empty.

// Let's connect to host
$conn = mysql_connect(HOST, USERNAME, PASSWORD) or DIE('Connection to host is failed, perhaps the service is down!');
// Select the database
mysql_select_db(DB_NAME) or DIE('Database name is not available!');


// Retrieve username and password from database according to user's input
//$sql = "SELECT * FROM user WHERE (username = '" . mysql_real_escape_string($_POST['username']) . "') and (password = '" . mysql_real_escape_string(md5($_POST['password'])) . "')";
$sql = "SELECT username, user_type_num FROM user WHERE (username = '" . mysql_real_escape_string($_POST['username']) . "') and (password = '" . md5(mysql_real_escape_string($_POST['password'])) . "')";
$result = mysql_query($sql) or die ('mysql_query() failed: ' . mysql_error());
$row = mysql_fetch_array($result, MYSQLI_ASSOC);			//give the associative array from the results  you want.
	echo '1-first_name is ' . $row{first_name};
$num_rows = mysql_num_rows($result);

// Check username and password match
if (mysql_num_rows($result) == 1) {
	// Set username session variable
	$_SESSION['username'] = $_POST['username'];
	$_SESSION['user_type_num'] = $row{user_type_num};
	//echo 'first_name is ' . $row{first_name};
    
    update_last_login($_SESSION['username']);   
	
	if ($_SESSION['user_type_num'] == 2) {
		// Jump to secured admin page
		header('Location: securedpage-admin.php');
	}
	else if ($_SESSION['user_type_num'] == 1){
		header('Location: securedpage-user.php');
	}
}
else {
	// Jump to login page
	header('Location: index.php');
}

function update_last_login($username){
    $sql = "UPDATE user
            SET last_login = (SELECT NOW())
            WHERE username = '$username'";
    mysql_query($sql) or die('last login failed because '.mysql_error().'<br/>sql: '.$sql.'<br/>');
}

?>
