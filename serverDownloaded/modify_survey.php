<!DOCTYPE html>
    <html>
			<head>
			  <title>Modify Survey Administration Page</title>
			  <link href="style.css" rel="stylesheet" type="text/css" />
			  <link href="screen.css" rel="stylesheet" type="text/css" />
			  <link href="facebox.css" rel="stylesheet" type="text/css" />
			</head>
			<body onload="javascript:setOffsets()">
<?php
include('dbconfig.php');
include ('header.php');
include ('update_manifest.php');

//-------------------Connect To Database-------------------
$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
mysql_select_db(DB_NAME) or die( "Unable to select database");

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
}

//only allow administrators to this page
if($_SESSION['user_type_num'] != 2){
    header('Location: index.php');
}



//-------------------Get the tip and show it in the this page from database-------------------
$sql1 = 'SELECT tips FROM tips_table WHERE page_name="modify_survey.php"';
$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);

echo '<div id="Tips" style="width:30%;position:relative;left:3%;top:3%;background:#B4CFEC;border: 1px solid #000000;padding: 10 10 10 10">
        <B>Tips</B>
        <br><br>
        <p>' . $row1{'tips'} .'</p>
        </div>';
echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';

if(isset($_POST['submit'])){
    
    clear_all_groups(); //this function will mark all grouping id's as not used
    
    //loop through selected groupings add mark them as used.
    foreach( $_POST as $id  => $notused ) {
        if($id == "submit"){
            continue;
        }
        mark_group_used($id);   //mark this grouping_id as used.
    }
    update_manifest();
}


echo '<div id="wrapper" class="mod_surv">';
echo '<h3>Filter Groups</h3><br/><br/>';
echo '<table>';
echo '<form method="post" action="modify_survey.php">';
echo display_group_selection();
echo '<tr><td align="right" colspan="2"><input type="submit" name="submit" value="Update Survey"></td></tr>';
echo '</form>';
echo '</table>';
echo '</div>';

function display_group_selection(){
    $q = "SELECT grouping_name, grouping_id
          FROM grouping_names";
    $res = mysql_query($q) or die(mysql_error());
    while($row = mysql_fetch_array($res, MYSQLI_ASSOC)){
        echo '<tr>';
        if(group_is_used($row{'grouping_id'}) == 1){
            echo '<td><input type="checkbox" checked="checked" name="'.$row{'grouping_id'}.'" id="'.$row{'grouping_id'}.'"
				value="'.$row{'grouping_id'}.'"></input></td>';
        }
        else{
            echo '<td><input type="checkbox" name="'.$row{'grouping_id'}.'" id="'.$row{'grouping_id'}.'"
				value="'.$row{'grouping_id'}.'"></input></td>';
        }
        echo '<td>'.$row{'grouping_name'}.'</td>';
        echo '<td><a href="modify_grouping.php?grouping_id='.$row{'grouping_id'}.'">edit</a></td>';
        echo '</tr>';
    }
    mysql_free_result($res);
}


function display_group_dropdown(){
    $sql = "SELECT grouping_name FROM grouping_names";
    $result = mysql_query($sql) or die('Error Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    $retval = '<select name="group" id="group">';
    while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
        $retval .= '<option value="'.$row{'grouping_name'}.'">
				'.$row{'grouping_name'}.'</option>';
    }
	$retval .- '</select>';
    mysql_free_result($result);
    return $retval;
}

function mark_group_used($id){
    
    $sql = "UPDATE form_questions
            SET is_used = 1
            WHERE grouping_id = $id;";
    $result = mysql_query($sql) or die('Error1 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    
    $sql = "UPDATE grouping_names
            SET is_used = 1
            WHERE grouping_id = $id;";
    $result = mysql_query($sql) or die('Error2 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    
    mysql_free_result($result);
}

function clear_all_groups(){
    $sql = "UPDATE form_questions
            SET is_used = 0;";
    $result = mysql_query($sql) or die('Error3 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    
    $sql = "UPDATE grouping_names
            SET is_used = 0;";
    $result = mysql_query($sql) or die('Error4 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    
    mysql_free_result($result);
}

function group_is_used($id){
    $sql = "SELECT grouping_name FROM grouping_names WHERE is_used = 1 AND grouping_id = $id;";
    $result = mysql_query($sql) or die('Error5 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    if(mysql_num_rows($result) > 0){
        return 1;
    }
    return 0;
}

?>
</body>
</html>
