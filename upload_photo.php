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
include ('header.php');
include ('dbconfig.php');
include ('survey_display_function.php');

//Detect special conditions devices
$iPod = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$iPad = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android= stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$webOS= stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

//do something with this information
if( ($iPad || $iPhone) &&  (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone OS 6_0') == false && strpos($_SERVER['HTTP_USER_AGENT'], 'OS 6_0') == false)){
        
        $_SESSION['old_ios'] = true;
        mysql_connect(HOST, USERNAME, PASSWORD);
        $conn = mysql_select_db(DB_NAME);
        display_grouping_name($_SESSION['grouping_num']);
        $_SESSION['photo'] = "true";
        echo '<div id="wrapper">';
        echo '<form method="post" action="input.php">';
        echo '<table width="100%">';        
        echo '<tr><td colspan="2">To use the upload photo feature, you must 
        upgrade to iOS6 which is the latest iPad/iPhone version.  <a href="http://howto.cnet.com/8301-11310_39-57516080-285/how-to-install-ios-6/">Click here</a> for upgrade directions.</td></tr>';
        echo '<tr>';
        echo '<td align="left"><input type="submit" name="previous" value="Previous Questions"></td>'; 
        echo '<td align="right"><input type="submit" name="next" value="Continue"></td></tr>'; 
        echo '</tr>';
        echo '</table>';
        echo '</form>';
        echo "</div>";
        
        mysql_close($conn);
}
/**else if($iPad){
        //were an iPad -- do something here
}else if($Android){
        //were an Android device -- do something here
}else if($webOS){
        //were a webOS device -- do something here
}
* Just here in case we want to do something with these other devices later.
* */
else{
    $conn = mysql_connect(HOST, USERNAME, PASSWORD);
    mysql_select_db(DB_NAME);
    display_grouping_name($_SESSION['grouping_num']);

    $_SESSION['photo'] = "true";
    echo '<div id="wrapper">';

    echo '<table width="100%">';
    
    echo '<form enctype="multipart/form-data" action="uploader.php" method="POST">';
    echo '<tr>';
    //echo "<td colspan='2'>".$_GET['text']."</td>";
    echo '</tr>';
    echo '<tr align="center">';
    echo '<td colspan="2">';
    echo '<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
            Choose a file to upload: <input name="uploadedfile" type="file" accept="image/*"/>';
    echo '</td>';
    echo '</tr>';
    echo '<tr><td><br/><br/></td></tr>';
    echo '<tr><td colspan="2" align="center">';
    echo    '<input type="submit" value="Upload File" />';
    echo '</td></tr>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
    //echo "photo success is " . $_SESSION['successphoto'];
//if($_SESSION['successphoto']){
    $msg = $_GET['msg'];
    $_SESSION['successphoto'] = 0;
    echo "<tr><td><br/><br/><td></td></td></tr>";
    echo "<tr align='center'><td colspan='2'>Photo Preview</td></tr>";
    $sql1 = "SELECT personal_photo, first_name
             FROM client
             WHERE client_id = ".$_SESSION['clientID'];
    $res1 = mysql_query($sql1) or die('Error msg: ' .mysql_error().'<br/>sql: '.$sql1.'<br/>');
    $row1 = mysql_fetch_array($res1, MYSQLI_ASSOC);
    //echo $_SESSION['clientID'];
    //header("Content-Type: image/jpg");
    echo '<tr align="center"><td colspan="2"><img src="'.$row1{'personal_photo'}.'"/></td></tr>';
//}
    echo '<form method="post" action="input.php">';   
    echo '<tr>';
    echo '<td align="left"><input type="submit" name="previous" value="Previous Questions"></td>'; 
    echo '<td align="right"><input type="submit" name="next" value="Save and Continue"></td></tr>'; 
    echo '</tr>';
    echo '</table>';
          
    echo "</div>";
    
    mysql_close($conn);
}

?>

</body>
</html>
