<?php
	session_start();
	$_SESSION['clientID'] = null;
	
include ('dbconfig.php');          
include ('compute_vi.php');

	//------Get the data sent in POST-------
		$unserializedData = array();	
		$client = $_POST['client'];
		parse_str($client,$unserializedData);
		print_r($unserializedData);
		//echo $unserializedData[72][0];	
		

	//-------------------Start Variables-----------------------
		$username   =   USERNAME;
		$password   =   PASSWORD;
		$database   =   DB_NAME;

	//-------------------Connect To Database-------------------
		$link   =   mysql_connect(HOST,$username,$password) or die ('Could not connect :'.  mysql_error());
		mysql_select_db($database) or die( "Unable to select database");
		
	//-------------------SET CLIENT ID-------------------
		if($_SESSION['clientID'] == null ){
			$sql0 = 'INSERT INTO client (client_id) VALUES(-1)';												//this is overridden by server b/c client_id is the auto incrementing 'index' of that table
			$result0 =   mysql_query($sql0) or die ('Query0 failed:'. mysql_error());					
			$_SESSION['clientID'] = mysql_insert_id();																//mysql_insert_id() is on a per connection basis --> no race condition with other concurrent surveyors
			$sql1 = 'UPDATE client SET details_link=\'<a href="client_details.php?client_id='.$_SESSION['clientID'].'">Details</a>\' WHERE client_id='.$_SESSION['clientID'];				//when inserting varchar into db must have apostrophies around it...and need to escape special chars...eg.  for ' to go into db need to do \'
			$result1 =   mysql_query($sql1) or die ('Query1 failed:'. mysql_error());
		}

		//$_SESSION['clientID'] = 150;
		
		$sql = 'SELECT client_id FROM client  WHERE client_id = ' . $_SESSION['clientID'] . ' AND survey_complete = 0';						//if client is not yet eneted into database, it will return 0 rows...therefore we insert...if client is in db and not done then we update.
		$result =   mysql_query($sql) or die ('Query5 failed:'. mysql_error().'<br/>sql: '.$sql.'<br/>');
			

		if(!$result){
			echo "sql error: " .mysql.error()."<br/>sql: $sql<br/>";
		}
		
	//-------------------Create the UPDATE string for the database------------------
		else{
			$sqlSetString = '';
			
			//------Get the indexes and values from associative array when not knowing the key/value pairs------
			//---arrays that come in as ##[] from the name or id attribute in the html page will be truncated to ##...(what we want for updating question values)...
			$index = 0;
			foreach($unserializedData as $key => $value)							//itterate through data entries in array $unserializedData
			{
				$sql = 'SELECT column_name, table_name
							FROM map_question_id_to_client_column_name
							WHERE question_id = ' . $key . '';
						
				$result = mysql_query($sql) or die ('Query3 failed:'. mysql_error());
				$row = mysql_fetch_array($result,MYSQLI_ASSOC);
						
				//Multivalue checkbox group response        
				if($row{table_name} != NULL){
					foreach($value as $k => $v){
						//echo "$index is a $key containing $value [$k] = $v \n";
						$q = "INSERT INTO ".$row{table_name}.
								  "(client_id, response) VALUES
								   (".$_SESSION['clientID'].", $v);";
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
					if(!is_numeric($value)){
						$sqlSetString .= '\'' . mysql_real_escape_string($value) . '\', ';
					}
					else{
						$sqlSetString .= '' . mysql_real_escape_string($value) . ', ';
					}
				}
				$index++;
			}
			
			//remove the last 2 chars ', ' from $sqlSetString...should be done like this and not just process the last on specially (like in input.php)...because might have a multivalue checkbox as the last question to be processed...current input.php might have a problem with that scenario
			$sqlSetString = substr_replace($sqlSetString, "", -2);
			
			//---Update the client---
			$sql1 = 'UPDATE client SET ' . $sqlSetString . ' WHERE client_id =' .$_SESSION['clientID'];
			$result1 =   mysql_query($sql1) or die ('Query7 failed:'. mysql_error());
														
			mysql_free_result($result);
			mysql_free_result($result1);
	}
	
	computeVI($_SESSION['clientID']);
	//display_client($client_id);
	//updateClient($client_id);
	$_SESSION['clientID'] = null;
                        
?>
