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
              <script src="js/survey/survey.js" text="text/javascript"></script>
			</head>
			
<?php

include ('header.php');
include ('changeGroupingNum.php');
include ('dbconfig.php');          
include ('compute_vi.php');
include ('survey_display_function.php');
// Inialize session
session_start();				//must be called on every page that wants to use variables stored in session




//---------------------------------------------------------------------------------------------------------------
//----------------------------------------------------------PAGE START-------------------------------------------
//---------------------------------------------------------------------------------------------------------------
//-------------------Start Variables-----------------------
$username   =   USERNAME;
$password   =   PASSWORD;
$database   =   DB_NAME;
$input_id_array[] = array();
//End Variables


//-------------------Connect To Database-------------------
$link   =   mysql_connect(HOST,$username,$password) or die ('Could not connect :'.  mysql_error());
mysql_select_db($database) or die( "Unable to select database");

//-------------------Get the tip and show it in the this page from database-------------------
$sql1 = 'SELECT tips FROM tips_table WHERE page_name="survey.php"';
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

//-------------------SET CLIENT ID and QUESTION GROUPING NUMBER-------------------
//$_SESSION['clientID'] = $_POST['username'];
if($_SESSION['clientID'] == null ){
    $sql0 = 'INSERT INTO client (client_id) VALUES(-1)';										//this is overridden by server b/c client_id is the auto incrementing 'index' of that table
    $result0 =   mysql_query($sql0) or die ('Query0 failed:'. mysql_error());					
    $_SESSION['clientID'] = mysql_insert_id();																//mysql_insert_id() is on a per connection basis --> no race condition with other concurrent surveyors
    $sql1 = 'UPDATE client SET details_link=\'<a href="client_details.php?client_id='.$_SESSION['clientID'].'">Details</a>\' WHERE client_id='.$_SESSION['clientID'];				//when inserting varchar into db must have apostrophies around it...and need to escape special chars...eg.  for ' to go into db need to do \'
    $result1 =   mysql_query($sql1) or die ('Query1x failed:'. mysql_error());
    
    
    //$_SESSION['grouping_num'] = 1;
    //$grouping_num = $_SESSION['grouping_num'];	
    //echo'<p>CLIENTID -- $_SESSION['.'"grouping_num"'.'] = 1; </p>';
}



//-----MUST EVENTUALLY BUILD IN SUPPORT FOR MULTIPLE FORMS.....use the form_id column in the grouping_names table
if($_SESSION['minGroupingNum'] == null || $_SESSION['minGroupingNum'] > get_start_grouping_num()){
            $_SESSION['minGroupingNum'] = get_start_grouping_num();
}
if ($_SESSION['maxGroupingNum'] == null){
            $_SESSION['maxGroupingNum']= get_last_grouping_num();
}

if($_SESSION['grouping_num'] == null ){														//need this check only if session has not been destroyed
        $_SESSION['grouping_num'] = 0;
        $_SESSION['grouping_num'] = incremetGroupingNum($_SESSION['grouping_num'], $_SESSION['minGroupingNum'], $_SESSION['maxGroupingNum']);			//this is done incase the survey will not start/use the first group of questions
    //echo'<p>IF - GROUPING_NUM -- $_SESSION['.'"grouping_num"'.'] = ' .$grouping_num. '; </p>';
}
$grouping_num = $_SESSION['grouping_num'];					//this value could be set by a redirect to this page:  survey.php-->input.php-->survey.php		both survey.php and input.php modify $_SESSION['grouping_num'] 
//echo 'grouping_num: ' . $grouping_num . '<br/>';
//echo '<p>'.$_SESSION['clientID'].'</p>';

//-------------------SQL Get Questions-------------------
$query  =   "SELECT * FROM form_questions WHERE is_used = 1 AND grouping_id = ".$_SESSION['grouping_num']."  ORDER BY `grouping_id`, `question_order_num`";
//$query  =   "SELECT * FROM form_questions WHERE is_used = 1 AND grouping_id = 1 ORDER BY `grouping_id`, `question_order_num`";
//echo $query;
$result =   mysql_query($query) or die ('Query failed:'. mysql_error());
echo "";
echo '<div id="wrapper">';
echo '<table width="100%">';
echo '<form method="post" action="input.php">';																		// calls the input.php file...will have to redirect to this page and select the next grouping of questions
//Get results
//$grouping_num = $_SESSION['grouping_num'];
$grouping_name_displayed = false;
while ($row = mysql_fetch_array($result,MYSQLI_ASSOC))														//$row becomes the next question row entry in returned results from the table.
    {
        //if($row{'grouping_id'} != $grouping_num){
            echo '</table>';
            if(!$grouping_name_displayed){
                display_grouping_name($row{'grouping_id'});
                $grouping_name_displayed = true;
            }
            //$grouping_num = $row{'grouping_id'};
            echo '<table width="100%">';
            echo '<tr><td width="50%" text-align="right"></td><td width="50%" text-align="left"></td></tr>';
        //}
        echo '<tr>';
        
        
        if($row{'question_response_type'} != 5){
            //this is a label, so don't add to input array
            array_push($input_id_array, $row{'question_id'});
        }
        echo display_question($row{'question_id'},
            $row{'question_text'}, $row{'question_response_type'}, $row);
        echo '</tr>';
    }
    
    
// -------------------Page one (stick it in a form):-------------------
for ($round=0;$round<count($input_id_array);$round++){
    //echo "blah blah blah";
    echo "<input type=hidden name='array$round' value='$input_id_array[$round]'>";
    //echo "<input name='array$round' value='$input_id_array[$round]'>";
    //echo "------blah------";
}

//if(count($input_id_array) > 1){						//rudamentry way of detecting if there are no questions to ask...must be changed to accomodate entire grouping sections being skipped by admin				
if($grouping_num <= $_SESSION['maxGroupingNum']) {
    //echo 'count($input_id_array) = ' . count($input_id_array);
    if( $grouping_num == $_SESSION['minGroupingNum'])
    {
        echo '<tr>';
            echo'<td></td>';
            echo '<td align="right"><input type="submit" name="next" value="Save and Continue"></td></tr>'; 
        echo '</tr>';
    }
    else
    {
        echo '<tr>';
            echo'<td align="left"><input type="submit" name="previous" value="Previous Questions" formnovalidate></td>'; 
            echo '<td align="right"><input type="submit" name="next" value="Save and Continue"></td></tr>'; 
        echo '</tr>';
    }
}
else {
    //compute vi and insert it into the db
    computeVI($_SESSION['clientID']);
    //display_client($client_id);
    //updateClient($client_id);
    //mysql_free_result($result2);
    //echo 'count($input_id_array) = ' . count($input_id_array);
    echo '<p>Thank you for completing this survey.  It has automatically been saved to our database.</p>';
    $_SESSION['clientID'] = null;
    $_SESSION['grouping_num'] = null;
	echo '<tr>';
		echo '<td></td>';
		echo '<td  align="center"><button id="newSurvey"> Start New Survey </button></td>'; 
		echo '<td></td>';
	echo '</tr>';
    
    //echo '<tr><td></td><td align="right"><input type="submit" name="submit" value="Submit"></td></tr>'; 
    }
echo '</table>';
echo '</form>';


if( $_SESSION['grouping_num'] > 1){
    echo '<form method="post" action="input.php" onsubmit="return confirm(\'Reset the entire survey?\')" >';		
        echo '<table width="100%">'; 
            echo '<tr><td width="33%"></td><td width="33%"></td><td width="33%"></td></tr>';			//column layout set up
            echo '<tr>';
                echo '<td></td>';
                echo '<td align="center"><input type="submit" name="reset" value="Reset Entire Survey"></td>'; 
                echo '<td></td>';
            echo '</tr>';	
        echo '</table>';
    echo '</form>';
}
echo '</div>';

//mysql_free_result($result); 

function get_last_grouping_num(){
    $sql = "SELECT grouping_id
            FROM form_questions
            WHERE is_used = 1
            ORDER BY grouping_id DESC";
    $result = mysql_query($sql) or die("sql: $sql<br/>");
    $row = mysql_fetch_array($result, MYSQLI_ASSOC);
    return $row{'grouping_id'};
    mysql_free_result($result);
}
            ?>
        </body>
</html>
