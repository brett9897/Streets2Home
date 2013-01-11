<?php
include('dbconfig_s2h1.php');

/*Test that your connection information will work to connect to
 * sh2v1's database.
 * */
 
 $conn = mysql_connect(HOST, USERNAME, PASSWORD);
 if(!$conn){
     echo "Failure to connect to s2hv1.<br/>";
     echo "Connection failure message: " . mysql_error() . "<br/>";
     die;
 }
 if(!mysql_select_db(DB_NAME)){
     echo "Failure to select db to s2hv1.<br/>";
     echo "Select DB failure message: " . mysql_error() . "<br/>";
     die;
 }
 
 $sql = "SELECT * FROM clients";
 $result = mysql_query($sql) or die("sql error: ". mysql_error() . "<br/>
    sql: $sql<br/>");
    
 echo "Success!<br/>";
 
 mysql_close($conn);
 

?>
