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
session_start();
include ('header.php');
include ('dbconfig.php');
include ('survey_display_function.php');

if($_SESSION['skip_cp'] == 1){
    $_SESSION['change_photo'] = "true";
}
else{
    $_SESSION['skip_cp'] = 0;
}

if($_SESSION['user_type_num'] != 2){
    header('Location: index.php');
}

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
    mysql_connect(HOST, USERNAME, PASSWORD);
    $conn = mysql_select_db(DB_NAME);
    $sql = "SELECT grouping_id
            FROM grouping_names
            WHERE grouping_name = 'Upload Photo'";
    $r = mysql_query($sql) or die(mysql_error()."<br/>sql: $sql<br/>");
    if($row = mysql_fetch_array($r, MYSQLI_ASSOC)){
        display_grouping_name($row{grouping_id});
    }
    else{
        echo "No results from query to fine grouping_id for upload photo<br/>";
    }
;
    $_SESSION['photo'] = "true";
    echo '<div id="wrapper">';

    echo '<table width="100%">';
    
    echo '<form enctype="multipart/form-data" action="change_uploader.php" method="POST">';
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
    $msg = $_GET['msg'];
    echo "<tr><td><br/><br/><td></td></td></tr>";
    echo "<tr align='center'><td colspan='2'>Photo Preview</td></tr>";
    $sql1 = "SELECT personal_photo, first_name
             FROM client
             WHERE client_id = ".$_SESSION['client_id'];
    $res1 = mysql_query($sql1) or die('Error msg: ' .mysql_error().'<br/>sql: '.$sql1.'<br/>');
    $row1 = mysql_fetch_array($res1, MYSQLI_ASSOC);
    $_SESSION['client_photo'] = $row1{'personal_photo'};
    //echo $_SESSION['clientID'];
    //header("Content-Type: image/jpg");
    echo '<tr align="center"><td colspan="2"><img src="'.$row1{'personal_photo'}.'"/></td></tr>';
//}
    echo '<form method="post" action="client_details.php?client_id='.$_SESSION['client_id'].'">';   
    echo '<tr>';
    echo '<td></td>'; 
    echo '<td align="right"><input type="submit" name="change" value="Save New Photo"></td></tr>'; 
    echo '</tr>';
    echo '</table>';
          
    echo "</div>";
    
    mysql_close($conn);
}

?>

</body>
</html>
