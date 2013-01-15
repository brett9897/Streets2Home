<!DOCTYPE html>
    <html>
			<head>
			  <title>Modify Survey Administration Page</title>
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
session_start();
include('dbconfig.php');
include ('header.php');
include ('survey_offline_generator.php');
    
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
    //---make changes so that correct offline survey can be generated.
   $_SESSION['grouping_num'] = null;								//reset, becase user should have to restart survey if the survey has been modified.
   echo '<script> alert(\'Update Complete!  You may now generate the desired type of offline survey (administrative OR normal user).\');</script>';
}
else if( isset($_POST['surveyOfflineAdmin']) ){
	generateOfflineSurveyType('admin');
	$_SESSION['surveyOfflineSet'] = 'Admin';
	header('Location: modify_survey.php');				//necessary because generateOfflineSurveyType() will actually echo out the offline survey page to the page because this page posts to itself.  By reloading page, it 'never' shows up.
}
else if( isset($_POST['surveyOfflineNormal']) ){
	generateOfflineSurveyType('normal');
	$_SESSION['surveyOfflineSet'] = 'Normal';    
	header('Location: modify_survey.php');				//necessary because generateOfflineSurveyType() will actually echo out the offline survey page to the page because this page posts to itself.  By reloading page, it 'never' shows up.
}
else if( isset($_SESSION['surveyOfflineSet']) ) {
	echo '<script> alert(\'"Offline Survey - ' . $_SESSION['surveyOfflineSet'] . '" has been generated and is ready to use!\');</script>';
	$_SESSION['surveyOfflineSet'] = null;
}

?>
<div id="wrapping" class="mod_surv">
    <div id="side_nav" class="mod_surv">
        <a href="adjust_vi.php">Vulnerability Score Adjustment</a><br/><br/>
        <a href="modify_survey.php">Modify Survey</a><br/><br/>
        <a href="#">Modify Language</a><br/><br/>
        <a href="edit_tips.php">Edit Tips</a><br/><br/>
    </div>
<?php
echo '<div id="wrapper" class="mod_surv">';

//-------------------Get the tip and show it in the this page from database-------------------
$sql1 = 'SELECT tips FROM tips_table WHERE page_name="modify_survey.php"';
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

echo '<h3>Filter Groups</h3><br/><br/>';
echo '<table>';
echo '<form method="post" action="modify_survey.php">';
echo display_group_selection();
echo '<tr><td align="center" colspan="2"><input type="submit" name="submit" value="Update Survey"></td></tr>';
echo '<tr>
		<td align="left"><input type="submit" name="surveyOfflineAdmin" value="Generate Offline Survey - Admin"></td>
		<td align="right"><input type="submit" name="surveyOfflineNormal" value="Generate Offline Survey - Normal"></td>
	  </tr>';
echo '</form>';
echo '</table>';
echo '</div>';
echo '</div>';
echo '<div class="clear"></div>';
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
    $sql = "UPDATE grouping_names
            SET is_used = 1
            WHERE grouping_id = $id;";
    $result = mysql_query($sql) or die('Error2 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    
    mysql_free_result($result);
}

/*mark_all_group_questions_used will turn on EVERY QUESTION in the given grouping id*/
function mark_all_group_questions_used($id){
	$sql = "UPDATE form_questions
            SET is_used = 1
            WHERE grouping_id = $id;";
    $result = mysql_query($sql) or die('Error1 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    mysql_free_result($result);
}

function clear_all_groups(){  
    $sql = "UPDATE grouping_names
            SET is_used = 0;";
    $result = mysql_query($sql) or die('Error4 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
    
    mysql_free_result($result);
}

function clear_all_question(){
	$sql = "UPDATE form_questions
            SET is_used = 0;";
    $result = mysql_query($sql) or die('Error3 Msg: '.mysql_error().'<br/>SQL: '.$sql.'<br/>');
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
