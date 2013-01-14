<?php
session_start();
require_once 'dbconfig.php';
	//header('Content-type: text/html; charset=utf-8');
	
	//---------security check----------
		if (!isset($_SESSION['username'])) {
			session_destroy();
			header('Location: index.php');
		}

		if($_SESSION['user_type_num'] != 2){
			//unauthorized access...destroy all session vars and redirect to login screen.
			session_destroy();
			header('Location: index.php');
		}
	
	$client_id = $_GET['client_id'];
	$_SESSION['client_id'] = $client_id;
	$_SESSION['clientID'] = $client_id;
	
	//echo 'client_id = '.$client_id;

	//-------------------Connect To Database-------------------
	$link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
	//mysqli_select_db($link, $database) or die( "Unable to select database");
	mysql_select_db(DB_NAME) or die( "Unable to select database");



	$sql = 'SELECT * FROM client WHERE client_id = ' . $client_id;						//if client is not yet eneted into database, it will return 0 rows...therefore we insert...if client is in db and not done then we update.
	//$result =   mysqli_query($sql) or die ('Query0 failed:'. mysql_error());
	$result =   mysql_query($sql) or die ('Query0 failed:'. mysql_error());
	
	$numOfCols = mysql_num_fields($result); 
	//echo 'numOfCols = '. $numOfCols . '</br>';
?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<head>
			  <title>Client Details</title>
			  <link href="style.css" rel="stylesheet" type="text/css" />
			  <link href="screen.css" rel="stylesheet" type="text/css" />
			  <!--jQuery UI stuff-->
              <link href="css/overcast/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
              <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
              <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
              <script src="js/button.js" text="text/javascript"></script>
              <script src="js/survey/survey.js" text="text/javascript"></script>


			  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
		</head>

		<body onload="javascript:setOffsets()">
<?php

session_start();
include('header.php');

    //-------------------Get the tip and show it in the this page from database-------------------
	$sql1 = 'SELECT tips FROM tips_table WHERE page_name="client_details.php"';
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
	echo '<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
	
		//echo '<body>';
   echo '<div align="center" id="wrapper">';
   
	echo '<form method="post" name="return" action="report_server_side.php">';
		echo'<input type="submit" name="return" value="Return to Report"></td>'; 
		//echo'<input type="button" name="return" value="Return To Report" onclick="location.href=report_server_side.php">'; 
	echo '</form>';
	
        echo '<table border="0" width="80%">';
            $row = mysql_fetch_array($result,MYSQLI_ASSOC);	
            
            $sql_photo = "SELECT personal_photo, first_name
             FROM client
             WHERE client_id = ".$client_id;
            $photo = mysql_query($sql_photo) or die('Error msg: ' .mysql_error().'<br/>sql: '.$sql_photo.'<br/>');
            $photo_row = mysql_fetch_array($photo, MYSQLI_ASSOC);
            if($photo_row){
                if($_SESSION['user_type_num'] == 2){
                                echo '<tr class="odd"><td align="center"> <a href="client_details_edit.php?column_name=personal_photo">Change Photo</a></td></tr>';			//sending an edit link into table	
                            }
                echo '<tr class="even"><td colspan="2"><img width="600px" height="800px" src="'.$photo_row{'personal_photo'}.'"/></td></tr>';
            }
            echo '<tr><td width="50%" text-align="right"></td><td width="50%" text-align="left"></td></tr>';					//sets up the table column spacing layout.
            

                        
				$prevBgColor  = "even";
                for($col=0; $col<$numOfCols; $col++)
                {                    
                    //echo 'col = ' .$col;
                    //$colname = mysqli_fetch_field_direct($result, $colNum);													//getting the name of the column
                    $colName = mysql_field_name($result, $col);	
                    if($colName == "details_link" || $colName == "personal_photo" || $colName == "survey_complete"){
						//skip these columns
                        continue;
                    }
                    if($colName == "form_id"){
                        //display multivalued fields
                        $q = "SELECT response_table, values_table 
                                FROM form_questions 
                                WHERE form_id = ".$colName." 
                                    AND is_multi_valued = 1";
                        $rs = mysql_query($q) or die('error msg: '.mysql_error());
                        $firstTime = true;
                        while($r = mysql_fetch_array($rs, MYSQLI_ASSOC)){
		                    //Must use this set up because when skipping entries such as client_id, personal_photo, or survey_complete, you will end up with 2 consecutive rows with same color if you do if( $col%2 == 0_{ even;} else {odd;}
							if( $prevBgColor == "even"){
								echo '<tr class="odd">';
								$prevBgColor = "odd";
							}
							else {
								echo '<tr class="even">';
								$prevBgColor = "even";						
							}
                            //echo "response_table: ".$r{response_table}."<br/>";
                            //echo "values_table: ".$r{values_table}."<br/>";
                            $q1 = "SELECT response
                                   FROM ".$r{values_table}."
                                   WHERE client_id = ".$_SESSION['client_id'];
                            $rs1 = mysql_query($q1) or die('error msg(q1): '.mysql_error());
                            
                            
                            //get the column_name descriptions for ease of reading
                            $sql2 = 'SELECT Description from map_question_id_to_client_column_name WHERE table_name=\''. $r{values_table} .'\'';				//gets the corresponding column name description from the database
                            $result2 =  mysql_query($sql2) or die ('Query1 failed:'. mysql_error());
                            $row2 = mysql_fetch_array($result2, MYSQLI_ASSOC);  
                            
                            if(mysql_num_rows($rs1) == 0){
                                echo '<tr>';
                                    echo '<td align="left">' . $row2{'Description'} .'</td><td></td>';
                                echo '</tr>';
                            }
                            else{
                              while($r1 = mysql_fetch_array($rs1, MYSQLI_ASSOC)){
                                
                                echo '<tr>';
                                    if( $firstTime ){
                                        echo '<td align="left">' . $row2{'Description'} .'</td>';                                   
                                    }               
                                    else {
                                        echo '<td align="left"></td>'; 
                                    }
                                    echo '<td align="right">'.translate_response_num($r{response_table}, $r1{response}). '</td>';
                                    //admin check to allow admin to edit a client's details	
                                    if( $firstTime ){	
                                        if($_SESSION['user_type_num'] == 2){
                                            echo '<td align="right"> <a href="client_details_edit.php?column_name='.$colName.'">Edit</a></td>';			//sending an edit link into table	
                                            $firstTime = false; 
                                        }
                                        else {
                                           echo '<td align="right"> </td>td>'; 
                                        }
                                    }
                                    else {
                                        echo '<td align="right"> </td>';
                                    }
                                echo '</tr>';
                              }
                              $firstTime = false;
                          }
                        }
                        mysql_free_result($rs);
                        continue;
                    }
                    
                    

                    //------------------FOR ALL OTHER ROWS THAT ARE NOT CALLED "form_id"----------------------
						//Must use this set up because when skipping entries such as client_id, personal_photo, or survey_complete, you will end up with 2 consecutive rows with same color if you do if( $col%2 == 0_{ even;} else {odd;}
						if( $prevBgColor == "even"){
							echo '<tr class="odd">';
							$prevBgColor = "odd";
						}
						else {
							echo '<tr class="even">';
							$prevBgColor = "even";						
						}
                    
                        //get the column_name descriptions for ease of reading
                        $sql = 'SELECT Description, question_id from map_question_id_to_client_column_name WHERE column_name=\''. $colName .'\'';				//gets the corresponding column name description from the database
                        $result1 =  mysql_query($sql) or die ('Query1 failed:'. mysql_error());
                        $row2 = mysql_fetch_array($result1, MYSQLI_ASSOC);
                        
                        if($row2{question_id}){
                            $table_name = get_table_name($row2{question_id});
                        }
                        
                        if( $row2{'Description'} != null){
                            //for printing out column descriptons that ARE questions (E.G. first name) 
                            //THESE ARE EDITABLE
                            echo '<td align="left">' . $row2{'Description'} .'</td>';
                            if(!$table_name){
                                echo '<td align="center">'.$row{$colName}. '</td>';
                            }
                            else{
                                echo '<td align="center">'.translate_response_num($table_name, $row{$colName}). '</td>';
                            }
                            if($_SESSION['user_type_num'] == 2){
                                echo '<td align="right"> <a href="client_details_edit.php?column_name='.$colName.'">Edit</a></td>';			//sending an edit link into table	
                            }
                        }
                        else {
                            //for printing out columns names that ARE NOT questions (E.G. client_id and VI)
                            //THESE ARE NOT EDITABLE...TO MAKE SOMETHING NOT EDITABLE DO NOT GIVE IT A DESCRIPTION IN map_question_id_to_client_column_name TABLE.
                            echo '<td align="left">' . $colName .'</td>';					
                            if(!$table_name){
                                echo '<td align="center">'.$row{$colName}. '</td>';
                            }
                            else{
                                echo '<td align="center">'.translate_response_num($table_name, $row{$colName}). '</td>';
                            }														//sending column response into table
                            echo '<td> </td>';				//for visual row coloring only
                        }
                        
                    echo '</tr>';
                    $colNum += 1;
                }
        echo'</table>';
    echo '</div>';
	
		
	
	mysql_free_result($result);
	mysql_free_result($result1);
	mysql_close($link);
    
function translate_response_num($table, $num){
    //echo "table name: $table<br/>";
    if(!$num)return;
    $sql = "SELECT response
            FROM $table
            WHERE num = $num";
    $res = mysql_query($sql) or die("Error in translation function: ".mysql_error()."<br/>sql: $sql<br/>");
    $row = mysql_fetch_array($res, MYSQLI_ASSOC);
    return $row{response};
}

function get_table_name($qid){
    $sql = "SELECT response_table
            FROM form_questions
            WHERE question_id = $qid";
    $res = mysql_query($sql) or die("Error in gettablename function: ".mysql_error()."<br/>sql: $sql<br/>");
    $row = mysql_fetch_array($res, MYSQLI_ASSOC);
    return $row{response_table};
}

?>

			</div>
			<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>
			<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>

	</body>
	</html>
