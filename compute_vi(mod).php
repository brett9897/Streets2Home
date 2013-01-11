<?php

require_once 'dbconfig.php';

function updateClientViScores(){
	$sql = "SELECT client_id
			FROM client";
	$result = mysql_query($sql) or die('Failed Query:<br/>
					Error Message: '.mysql_error().'<br/>
					Failed SQL Statement: '.$sql.'<br/>');
	$vi_array = getVIweightArray();
	while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
		$new_vi = updateVIfromScores($row{client_id}, $vi_array);
		//echo "new vi is $new_vi <br/>";
		$sql1 = "UPDATE client
				SET vi = $new_vi
				WHERE client_id = ".$row{client_id};
		//echo "<br/>$sql<br/>";
		$result1 = mysql_query($sql1) or die('Update failed in updateClientViScores()
			<br/>Error Message: '.mysql_error().'<br/>SQL: '.$sql1.'');
	}
}

function getVIweightArray(){
				$sql2 = "SELECT vi_name, weight FROM vulnerability_conf";
					$result2 = mysql_query($sql2) or die('Failed Query2:<br/>
									Error Message: '.mysql_error().'<br/>
									Failed SQL Statement: '.$sql2.'<br/>');
					//echo mysql_num_rows($result2)." is num rows<br/>";
					$vi_array = array();
					while($row2 = mysql_fetch_array($result2, MYSQLI_ASSOC)){
									$vi_array[$row2{vi_name}] = $row2{weight};
									//echo "vi_name is ".$row2{vi_name}."<br/>weight is ".$row2{weight}."<br/>";
					}
					return $vi_array;
}

function computeVI($client_id){
	$vi = 0;
	$sql = "SELECT * FROM vulnerability_conf ORDER BY vi_num";
	$result = mysql_query($sql) or die('Query1 failed in function compute VI: ' . 
		mysql_error());
		$vi_array = array();
	while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
		switch($row{conf_type}){
			case 1:
			 $r1 = computeComboLogic($client_id, $row{vi_num},
								$row{thresh_hold}, $row{combo_logic});
			 $vi_array[$row{vi_num}]  = $r1;
				//echo "case1: vi_num is ".$row{vi_num}."   result is $r1<br/>";   
				break;
			case 2:
			 $r2 = computeMultiColumn($client_id, $row{vi_num}, $row{combo_logic});
				$vi_array[$row{vi_num}] = $r2;
				//echo "case2: vi_num is ".$row{vi_num}."   result is $r2<br/>";   
				break;
			case 3:								
				if(getClientAge($client_id) > $row{thresh_hold}){
					$vi_array[$row{vi_num}] = 1;
				}
				else{
					$vi_array[$row{vi_num}] = 0;
				}
				break;
			case 4:								
			 $r4 = computeSingleColumn($client_id,
					$row{question_id}, $row{thresh_hold});
				$vi_array[$row{vi_num}] = $r4;
					
				//echo "case4: vi_num is ".$row{vi_num}."   result is $r4<br/>";
				break;
		}
	}
	insertVIintoDB($client_id, $vi_array);
}

function computeSingleColumn($client_id, $question_id, $thresh_hold){
	$col_name  = getClientColumnName($question_id);
	$col_value = getClientColumnValue($client_id, $col_name);
	if($thresh_hold == 0){
		if($col_value == 1 || $col_value == 3 || strtolower($col_value) == "yes"
                || strtolower($col_value) == "under treatment"){
			return 1;
		}
		return 0;
	}
	else{
		if($col_value > $thresh_hold){
			return 1;
		}
		return 0;
	}
}

function getClientAge($client_id){
	$sql = "SELECT  date_format(now(), '%Y') - date_format(date_of_birth, '%Y') - 
	  (date_format(now(), '00-%m-%d') < date_format(date_of_birth, '00-%m-%d'))
			AS age
			FROM client 
			WHERE client_id = $client_id";
	$result = mysql_query($sql) or die('Query1 failed in function getClientAge: ' . 
		mysql_error().'failed sql: .'.$sql.'<br/>');
	$row = mysql_fetch_array($result, MYSQLI_ASSOC);
	return $row{age};
}

function computeMultiColumn($client_id, $vi_num, $combo_logic){
	$sql = "SELECT question_id FROM vi_multi_column";
	$result = mysql_query($sql) or die('Query1 failed in function computeComboLogic: ' . 
		mysql_error().'failed sql: .'.$sql.'<br/>');
	while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
		$col_name  = getClientColumnName($row{question_id});
		$col_value = getClientColumnValue($client_id, $col_name);
		if($col_value == 1 || $col_value == 3 && $combo_logic == "or"){
			return 1;
		}
		if($col_value != 1 && $col_value != 3 && $combo_logic == "and"){
			return 0;
		}
	}
	if($combo_logic == "or"){
		return 0;
	}
	if($combo_logic == "and"){
		return 1;
	}
}

function computeComboLogic($client_id, $vi_num, $thresh_hold, $combo_logic){
	//echo "params cid, vi_num, thresh, adn logic are $client_id, $vi_num, $thresh_hold, $combo_logic<br/";
	$sql = "SELECT vi_conf_num_table FROM vi_combinational_tables";
	$result = mysql_query($sql) or die('Query1 failed in function computeComboLogic: ' . 
		mysql_error());
	while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
		$sql2 = "SELECT * 
				 FROM ".$row{vi_conf_num_table};
		$result2 = mysql_query($sql2) or die('Query2 failed in function computeComboLogic: ' . 
			mysql_error());
		$local_postive  = 0;
		while($row2 = mysql_fetch_array($result2, MYSQLI_ASSOC)){
			//echo "<br/>row2_qid is ".$row2{question_id}."<br/>";
			$col_name  = getClientColumnName($row2{question_id});
			//echo "col name is $col_name<br/>";
			$col_value = getClientColumnValue($client_id, $col_name);
			/*col_value of 1 corresponds to a yes and if there is an under treatment
			 * option that will be 3*/
			 //echo "col val is $col_value<br/>";
			 if($thresh_hold == 0){
				 //echo "inside here, and local positive is $local_postive<br/>";
				//0 threshold means it is mostly like a yes,no, under treat question
				if($col_value == 1 || $col_value == 3){
					$local_postive = 1;
					break;
					//found a positive, you only need one, so break out of loop
				}
			}
			else{
				if($col_value > $thresh_hold){
					$local_postive = 1;
					break;
					//found a positive, you only need one, so break out of loop
				}
			} 
		}
		if($combo_logic == "and" && $local_postive == 0){
			//echo "combo_logic is AND and NO LOCAL positive<br/>";
			return 0;			
		}
		else if($combo_logic == "or" && $local_postive == 1){
			//echo "combo_logic is or and local positive<br/>";
			return 1;
		}
	}
	if($combo_logic == "and"){
		//echo "combo_logic is AND AND NO LOCAL negatives<br/>";
		return 1;			
	}
	else if($combo_logic == "or"){
		//echo "combo_logic is OR and NO LOCAL POSITIVES<br/>";
		return 0;
	}
	return 0;
}

function getClientColumnName($question_id){	
	//echo "question id is $question_id";
	$sql1 = 'SELECT column_name
			 FROM  map_question_id_to_client_column_name
			 WHERE question_id = '.$question_id;
	//echo "sql: $sql1<br/>";
	$result1 = mysql_query($sql1) or die('Query1 failed in function getClientColumnName: ' . 
			mysql_error().'failed sql: '.$sql1);
	$row = mysql_fetch_array($result1, MYSQLI_ASSOC);
	//echo "row col name is ".$row{column_name}."<br/>";
	return $row{column_name};
}

function getClientColumnValue($client_id, $col_name){	
	$sql = "SELECT $col_name
			FROM client
			WHERE client_id = $client_id";
	$result = mysql_query($sql) or die('Query failed in function getClientColumnValue: '.
		mysql_error().'<br/>sql: '.$sql.'<br/>');
	$row = mysql_fetch_array($result, MYSQL_NUM);
	return $row[0];
}

function insertVIintoDB($client_id, $vi_array){
				$attr_str = "(client_id, ";
				$val_str = "($client_id, ";
				$last_key = end(array_keys($vi_array));
                $set_string = "";
				foreach($vi_array as $vi_num => $value){
                    $sql = "SELECT vi_name FROM vulnerability_conf WHERE
                                                    vi_num = $vi_num";
                    $result = mysql_query($sql) or die('insertVIintoDB query1 failed:'.mysql_error().'<br/>sql: '.sql.'<br/>');
                    $row = mysql_fetch_array($result, MYSQLI_ASSOC);
                    if ($vi_num == $last_key) {
                        // last element
                        $attr_str .= $row{vi_name}.')';
                        $val_str .= $value.')';
                        $set_string .= $row{vi_name}." = ".mysql_real_escape_string($value)." ";
                    } else {
                        // not last element
                        $attr_str .= $row{vi_name}.', ';
                        $val_str .= $value.', ';
                        $set_string .= $row{vi_name}." = ".mysql_real_escape_string($value).", ";
                    } 
				}
                $sql = "";
                if(!vi_scores_exist($client_id)){
                    $sql = "INSERT INTO client_vulnerability_scores ".$attr_str." VALUES ".$val_str;
                }
                else{
                    $sql = "UPDATE client_vulnerability_scores
                            SET $set_string
                            WHERE client_id = $client_id";
                }
                $result = mysql_query($sql) or die(mysql_error().'<br/>sql: '.$sql.'<br/');
				$VI = computeVIfromScores($client_id, $vi_array);
				$sql2 = 	"UPDATE client
								 SET VI = $VI
								 WHERE client_id = $client_id";
					$result2 =   mysql_query($sql2) or die ('Query8 failed:'. mysql_error() . 
							'failed sql: '.$sql2.'<br/>');
}	

function computeVIfromScores($client_id, $vi_array){
	   $vi = 0;
	   foreach($vi_array as $vi_num => $value){
					  $sql = "SELECT weight FROM vulnerability_conf WHERE vi_num = $vi_num";
					  $result = mysql_query($sql) or die(mysql_error());
					  $row = mysql_fetch_array($result, MYSQLI_ASSOC);
					  $vi += $row{weight} * $value;
				}
				return $vi;
}

function updateVIfromScores($client_id, $vi__weight_array){
	   $vi = 0;
				$sql = "SELECT * FROM client_vulnerability_scores
												WHERE client_id = $client_id";
				$result = mysql_query($sql) or die('query in updateVIfromScores failed:'.mysql_error().'<br/>sql:'.$sql.'<br/>');
				//echo "sql: $sql<br/>";
				//var_dump($result);
				//echo "results have # columns: ".mysql_num_fields($result)."<br/>";
				if(mysql_num_rows($result)){
								foreach(mysql_fetch_array($result, MYSQLI_ASSOC) as $key => $value){
												//echo "key is $key<br/>val is $value<br/>vi array key is ".$vi_array{$key}."<br/>";
												$vi += $vi__weight_array{$key} * $value;
								}
				}
				else{
							//echo "NO results returned from query<br/>";
							computeVI($client_id);
				}
				return $vi;
}
function vi_scores_exist($client_id){
    $sql = "SELECT *
            FROM client
            WHERE client_id = $client_id";
    $r = mysql_query($sql);
    if($r)
        return true;
    return false;
}
?>			


