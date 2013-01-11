<?php
session_start();
include ('dbconfig.php');
include('SimpleImage.php');

mysql_connect(HOST, USERNAME, PASSWORD);
$conn = mysql_select_db(DB_NAME);

$target_path = "uploads/";

if(!$_SESSION['client_photo']){
    $target_path = $target_path . renameImage();
    $_SESSION['client_photo'] = $target_path;
}
else{
    $target_path = $_SESSION['client_photo'];
}

$query = "UPDATE client ";
$query .= "SET personal_photo = ('$target_path') ";
$query .= "WHERE client_id = ".$_SESSION['clientID'];
$results = mysql_query($query) or die('Error msg: '.mysql_error().'<br/>
            sql: '.$query.'<br/>');

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    $msg = "The file ".  $_SESSION['client_photo']. 
    " has been uploaded";
    $image = new SimpleImage();
    $image->load($target_path);
    $image->resize(600,800);
    $image->save($target_path);
} else{
    $msg = "There was an error uploading the file, please try again!";
    $msg .= $_FILES['uploadedfile']['error'];
}
if(strcmp($_SESSION['change_photo'], "true") == 0){
    $_SESSION['skip_cp'] = 1;
    $_SESSION['change_photo'] = "false";
    header ( 'Location: change_photo.php');
}
else{
    header("Location: upload_photo.php?msg=$msg");
}

function renameImage(){ 
    mysql_connect(HOST, USERNAME, PASSWORD);
    $conn = mysql_select_db(DB_NAME);
    
    $sql = "SELECT first_name, last_name, client_id
            FROM client
            WHERE client_id = ".$_SESSION['clientID'];
    $res = mysql_query($sql) or die('Error msg: '.mysql_error().'<br/>
                    sql: '.$sql.'<br/>');
    if($row = mysql_fetch_array($res, MYSQLI_ASSOC)){
        $_SESSION['successphoto'] = 1;
        return $row{first_name}.'_'.$row{last_name}.'_'.$row{client_id};
    }
    else{
        echo "There was an error while fetching the row.<br/>";
    }
}
?>
