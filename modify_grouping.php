<!DOCTYPE html>
    <html>
			<head>
			  <title>Modify Survey Administration Page</title>
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

//Handle the submitted changes in this block
if(isset($_POST['submit'])){

    $update_str = "UPDATE form_questions SET ";

    foreach($_POST as $key => $val){

        $qid = strtok($key, "_");
        $attr = strtok(null);

        if(strcmp($update_str, "") == 0){
            $update_str = "UPDATE form_questions SET ";
        }

        switch($attr){

            case "order":
                $update_str .= "question_order_num = $val, ";
                break;

            case "text":
                $update_str .= "question_text = '".addslashes($val)."', ";
                break;

            case "type":
                $update_str .= "question_response_type = $val, ";
                break;

            case "used":
                $update_str .= "is_used = $val ";
                $update_str .= "WHERE question_id = $qid";
                if(!execute_sql($update_str)){
                    die("Execute_sql function failed.<br/>");
                }
                $update_str = "";
                break;

            default:
                //do nothing

        }

    }
    
    $_SESSION['grouping_num'] = get_start_grouping_num();
    
    //go to preview page to see changes.
    header("Location: question_grouping_preview.php?grouping_id=".$_SESSION['grouping_id']."");
}




//-------------------Get the tip and show it in the this page from database-------------------
$sql1 = 'SELECT tips FROM tips_table WHERE page_name="modify_grouping.php"';
$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);

echo '<div id="Tips" style="width:30%;position:relative;left:3%;top:3%;background:#B4CFEC;border: 1px solid #000000;padding: 10 10 10 10">
        <B>Tips</B>
        <br><br>
        <p>' . $row1{'tips'} .'</p>
        </div>';
echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
//--------------End Tips Section------------------


if($_GET['grouping_id'] < 0){
    echo "no grouping id.";
    die;
}

$grouping_id = $_GET['grouping_id'];
$_SESSION['grouping_id'] = $grouping_id;

display_grouping_name($grouping_id);

$query  =   "SELECT * 
             FROM form_questions 
             WHERE is_used = 1 AND 
             grouping_id = ".$_GET['grouping_id']."  ORDER BY `question_order_num`";

$result =   mysql_query($query) or die ('Query1 failed:'. mysql_error());

echo '<form method="post" action="modify_grouping.php">';
echo '<table>';
echo '<tr>';
echo '<th>Order Number</th>';
echo '<th>Question Text</th>';
echo '<th>Response Type</th>';
echo '<th>Currently Used</th>';
echo '</tr>';

while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
    
    echo '<tr>';
    echo '<td style="vertical-align:top"><input type="text" value="'.$row{'question_order_num'}.'"
            name="'.$row{'question_id'}.'_order" 
            id="'.$row{'question_id'}.'_order"
            size="2"
            vertical-align="top"
            required></td>';
    echo '<td style="vertical-align:top"><textarea cols="40" rows="5" wrap="hard"
            name="'.$row{'question_id'}.'_text" 
            id="'.$row{'question_id'}.'_text">'.$row{'question_text'}.'</textarea></td>';
    echo '<td valign="top">'.display_question_response_type_dropdown($row{'question_id'}, $row{'question_response_type'}).'</td>';
    echo '<td valign="top">'.display_currently_used_dropbox($row{'question_id'}, $row{'is_used'}).'</td>';
    echo '</tr>';
    
}
echo '<tr><td colspan="4" style="text-align: right"><input type="submit" name="submit" value="Submit Changes"></td></tr>';
echo '</table>';
echo '</form>';

//Functions start here

function display_currently_used_dropbox($question_id, $is_used){

    $retVal = '<select name="'.$question_id.'_used"
                       id="'.$question_id.'_used">';
    if($is_used){
        $retVal .= '<option selected="selected" value="1">Yes</option>';
        $retVal .= '<option value="0">No</option>';
    }
    else{
        $retVal .= '<option value="1">Yes</option>';
        $retVal .= '<option selected="selected" value="0">No</option>';
    }

    $retVal .= "</select>";

    return $retVal;

}

function display_question_response_type_dropdown($question_id, $type_id){
    
    $sql = "SELECT num, description
            FROM map_question_response_type";
            
    $res = mysql_query($sql) or die('Query failed in translate_question_response_type
                function.<br/>sql: '.$sql.'<br/>Error: '.mysql_error().'<br/>');

    $retVal = '<select name="'.$question_id.'_type"
                       id="'.$question_id.'_type">';
    while($row = mysql_fetch_array($res, MYSQLI_ASSOC)){
        if($type_id == $row{'num'}){
            $retVal .= '<option selected="selected" value="'.$row{'num'}.'">'.$row{'description'}.'</option>';
        }
        else{
            $retVal .= '<option value="'.$row{'num'}.'">'.$row{'description'}.'</option>';
        }
    }

    $retVal .= "</select>";

    return $retVal;
    
}

function execute_sql($sql){
    $res = mysql_query($sql) or die("sql: $sql <br/>Error: ".mysql_error()."<br/>");
    return $res;
}

?>
</body
</html>
