<!DOCTYPE html>
<html>
<head>
    <title>Preview Question Group of Survey</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
    <link href="screen.css" rel="stylesheet" type="text/css" />
</head>
<?php
include('dbconfig.php');
include ('header.php');
include('survey_display_function.php');

//-------------------Connect To Database-------------------
$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
mysql_select_db(DB_NAME) or die( "Unable to select database");

//only allow administrators to this page
if($_SESSION['user_type_num'] != 2){
    header('Location: index.php');
}

//-------------------Get the tip and show it in the this page from database-------------------
$sql1 = 'SELECT tips FROM tips_table WHERE page_name="question_grouping_preview.php"';
$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);

echo '<div id="Tips" style="width:30%;position:relative;left:3%;top:3%;background:#B4CFEC;border: 1px solid #000000;padding: 10 10 10 10">
        <B>Tips</B>
        <br><br>
        <p>' . $row1{'tips'} .'</p>
        </div>';
echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
//--------------End Tips Section------------------

if(empty($_GET['grouping_id'])){
    if(isset($_POST)){
        if(!empty($_POST['edit'])){
            header("Location: modify_grouping.php?grouping_id=".$_SESSION['modified_grouping_id']);
        }
        else if(!empty($_POST['accept'])){
            header("Location: modify_survey.php");
        }
    }
    else{
        echo "Error:  No GET or POST detected.<br/>";
        die;
    }
}
else{
    //grouping is set, store it into session variable for use in the header function above
    $_SESSION['modified_grouping_id'] = $_GET['grouping_id'];
}

echo '<p style="text-align: center"><span style="color: red;font-size: 14">Preview Only</span></p>';

display_grouping_name($_GET['grouping_id']);

$query  =   "SELECT *
             FROM form_questions
             WHERE is_used = 1 AND
                   grouping_id = ".$_GET['grouping_id']."
             ORDER BY `question_order_num`";

$result =   mysql_query($query) or die ('Query failed:'. mysql_error());

echo '<div id="wrapper">';
echo '<table >';
echo '<tr><td style="width:50%;text-align="right"></td><td style="width:50%;text-align="left"></td></tr>';

while ($row = mysql_fetch_array($result,MYSQLI_ASSOC)){

 echo '<tr>';
 echo display_question($row{'question_id'}, $row{'question_text'}, $row{'question_response_type'}, $row);
 echo '</tr>';

}

echo '</table>';
echo '</div>';

echo '<div>';
echo '<form method="post" action="question_grouping_preview.php">';
echo '<table style="width: 100%">';
echo '<tr style="height: 12px"></tr><tr>';
echo '<td style="text-align: left"><input type="submit" name="edit" value="Edit"></td>';
echo '<td style="text-align: right"><input type="submit" name="accept" value="Accept Changes"></td></tr>';
echo '</tr>';
echo '</table>';
echo '</form>';
echo '</div>';


?>
</body
</html>