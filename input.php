<?php
require_once 'dbconfig.php';
include ('changeGroupingNum.php');
// Inialize session
session_start();					//must be called on every page that wants to use variables stored in session

function display_client($client_id){
	echo "<div width = 80%>";
	echo "<table border = '1' cellpadding='2' >";
	$sql = "SELECT *
			FROM client
			WHERE client_id = $client_id";
			
	//display col names
	$result = mysql_query($sql);
	if (!$result) {
		die('Query1x failed: ' . mysql_error());
	}
	echo "<tr>";
	$i = 0;
	while ($i < mysql_num_fields($result)) {
		
		$meta = mysql_fetch_field($result, $i);
		if (!$meta) {
			echo "No information available<br />\n";
		}
		echo "<th>".$meta->name."</th>";
		$i++;
	}		
	echo "</tr>";		
	$result = mysql_query($sql) or die ('Query2 failed:'. mysql_error());
	$row = mysql_fetch_array($result,  MYSQL_NUM);
	echo "<tr>";
	$i = 0;
	while ($i < mysql_num_fields($result)) {
		echo "<td>".$row{$i}."</td>";
		$i++;
	} 
	echo "</tr>";
	echo "</table>";
	echo "</div>";
}

for ($round=0;$exists == 0;$round++){
	if (isset($_POST["array$round"])){
	$input_id_array[$round] = $_POST["array$round"];				//in order to send arr or elements from page to page...is how it is done.  move input array from login proc to--->whatever
	}

	else{
	$exists = 1;
	}
}


	//-----MUST EVENTUALLY BUILD IN SUPPORT FOR MULTIPLE FORMS.....use the form_id column in the grouping_names table
	if($_SESSION['minGroupingNum'] == null){
				$sql1 = 'SELECT MIN(grouping_id) FROM grouping_names';
				$result =   mysql_query($sql1) or die ('Query min failed:'. mysql_error());
				$row = mysql_fetch_row($result);
				$minGroupingNum = $row[0];
	}
	if ($_SESSION['maxGroupingNum'] == null){
				$sql1 = 'SELECT MAX(grouping_id) FROM grouping_names';
				$result =   mysql_query($sql1) or die ('Query max failed:'. mysql_error());
				$row = mysql_fetch_row($result);
				$maxGroupingNum = $row[0];
	}

				
	if(!empty($_POST['previous']))
	{
			if($_SESSION['photo'] == "true"){
                $_SESSION['grouping_num'] = decrementGroupingNum($_SESSION['grouping_num'], $_SESSION['minGroupingNum'], $_SESSION['maxGroupingNum']);
                $_SESSION['photo'] = "false";
            }
            else{
                $_SESSION['grouping_num'] = decrementGroupingNum($_SESSION['grouping_num'], $_SESSION['minGroupingNum'], $_SESSION['maxGroupingNum']);	
			}
            //echo'<p>ELSE - $_SESSION['.'"grouping_num"'.'] = ' .$grouping_num. '; </p>';
            

			if( $_SESSION['grouping_num'] == 4 )
            {
            	header('Location: upload_photo.php?msg="Choose a file to upload"');
            }
            else
            {
            	header('Location: survey.php');
            }
				
	}
	else if(!empty($_POST['next'])){
        
            if($_SESSION['photo'] == "true"){
				$_SESSION['grouping_num'] = incremetGroupingNum($_SESSION['grouping_num'], $_SESSION['minGroupingNum'], $_SESSION['maxGroupingNum']);
                $_SESSION['photo'] = "false";
            }
            else{
				$_SESSION['grouping_num'] = incremetGroupingNum($_SESSION['grouping_num'], $_SESSION['minGroupingNum'], $_SESSION['maxGroupingNum']);
				
                //the example of inserting data with variable from HTML form
                //input.php
                if($_SESSION['old_ios'] != true){
                    mysql_connect(HOST,USERNAME,PASSWORD);//database connection
                    mysql_select_db(DB_NAME);

                    $client_column[] = array();

                    echo '<html><body><table><tr>';
                    
                    $sql = 'SELECT client_id FROM client  WHERE client_id = ' . $_SESSION['clientID'] . ' AND survey_complete = 0';						//if client is not yet eneted into database, it will return 0 rows...therefore we insert...if client is in db and not done then we update.
                    $result =   mysql_query($sql) or die ('Query5 failed:'. mysql_error().'<br/>sql: '.$sql.'<br/>');
                    
                    if(!$result){
                        echo "sql error: " .mysql.error()."<br/>sql: $sql<br/>";
                    }
                    else{
                        //UPDATE
                        //'UPDATE client SET survey_complete='1' WHERE client_id ='$_SESSION['clientID']'/
                        $sqlSetString = '';
                        $i = 1;
                        for($index = 1; $index < count($input_id_array) ; $index++) {								//the zero index has some php garabage encoding or something...our first real entry is at spot 1
                            //echo 'question_id is '.$input_id_array[$index].'<br/>';
                            $sql = 'SELECT column_name, table_name   FROM map_question_id_to_client_column_name
                                    WHERE question_id = '.$input_id_array[$index].'';						//this is to get input responses 1 to (end-1).
                            $result = mysql_query($sql) or die ('Query3 failed:'. mysql_error());
                            $row = mysql_fetch_array($result,MYSQLI_ASSOC);
                            
                            //Multivalued checkboxes
                            if($row{table_name} != NULL){
                                //$_SESSION['test_val'] =  mysql_real_escape_string($_POST[$input_id_array[$i]]);
                                
                                var_dump($_POST);
                                var_dump($input_id_array);
                                var_dump($_POST[89]);
                                foreach($_POST[$input_id_array[$i]] as $key => $val){
                                        $q = "INSERT INTO ".$row{table_name}.
                                              "(client_id, response) VALUES
                                               (".$_SESSION['clientID'].", $val);";
                                        $r = mysql_query($q);
                                        if(!$r){
                                            //probably inserted a duplicate
                                            //todo: create log file and record error for max robustness
                                            continue;
                                        }
                                }
                                continue;
                            }
                            else {
                                //Single response question
                                //echo "$index is a $key containing $value \n";
                                $sqlSetString .= mysql_real_escape_string($row{column_name}) . '=';
                                if(!is_numeric($_POST[$input_id_array[$index]])){
                                    $sqlSetString .= '\'' . mysql_real_escape_string($_POST[$input_id_array[$index]]) . '\', ';
                                }
                                else{
                                    $sqlSetString .= '' . mysql_real_escape_string($_POST[$input_id_array[$index]]) . ', ';
                                }
                                $i = $i + 1;
                                //echo  '<br/>'.$sqlSetString.'<br/>';
                                }
                                //echo '</br> $sqlSetString = ' . $sqlSetString . '</br>';                            
                        }
                        //remove the last 2 chars ', ' from $sqlSetString...should be done like this and not just process the last on specially (like in input.php)...because might have a multivalue checkbox as the last question to be processed...current input.php might have a problem with that scenario
                        $sqlSetString = substr_replace($sqlSetString, "", -2);
                        
                        //---Update the client---
                        $sql1 = 'UPDATE client SET ' . $sqlSetString . ' WHERE client_id =' .$_SESSION['clientID'];
                        $result1 =   mysql_query($sql1) or die ('Query7 failed:'. $sql1 . ' '. mysql_error());
                                                                    
                        mysql_free_result($result);
                        mysql_free_result($result1);
                    }
                }
            }   //--END else for handling all other 'next' questions request that is not for photo upload page

            if( $_SESSION['grouping_num'] == 4 )
            {
            	header('Location: upload_photo.php?msg="Choose a file to upload"');
            }
            else
            {
            	header('Location: survey.php');
            }
    }
	else if(!empty($_POST['reset'])) {

			//clear out all columns of current client data except client_id
			mysql_connect(HOST,USERNAME,PASSWORD);//database connection
            mysql_select_db(DB_NAME);
			$sql = 'DELETE FROM client WHERE client_id = ' . $_SESSION['clientID'];				//client_id is the primary key
						echo $sql .' <br />';
						
			$result =   mysql_query($sql) or die ('Query reset failed:'. mysql_error().'<br/>sql: '.$sql.'<br/>');
			

			$row = mysql_fetch_array($result, MYSQLI_ASSOC);
			
			
			//reset vars and so that reload will start at begenning of survey
		    $_SESSION['clientID'] = null;						//NOTE...this will cause 'gaps' in the primary key of the clients table in the database
		    $_SESSION['grouping_num'] = null;
            $_SESSION['photo'] = "false";
            
			header("Location: consent.php");
           
	}

	//mysql_close($link);
	
	
	

	
?>
