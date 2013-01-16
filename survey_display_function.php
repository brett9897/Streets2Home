<?php
require_once 'dbconfig.php';
function display_question($id, $text, $data_type, $row){
	switch($data_type){
		case 1:
			display_textbox($id, $text,$row{'required'});
			break;
		case 2:
			display_date_box($id, $text);
			break;
		case 3:
			display_dropdown($id, $text);
			break;
		case 4:
			display_checkbox($row, $id, $text);
			break;
		case 5:
			display_label($text);
			break;
		case 6:
			display_radio_buttons($id, $text);
			break;
		case 7:
			header("Location: upload_photo.php?msg=$text");
			break;
		case 8:
			display_SSN_box($id, $text, $row{'required'});
			break;
	}
}


function display_textbox($id, $text, $is_required){

    $auto_val = "";

    if($text == "Interviewer's Name"){
        $auto_val = get_user_real_name();
    }

    if($is_required){
        echo '<td text-align="right">'.$text.'</td>';
        echo '<td  text-align="left"><input type="text" name="'.$id.'" id="'.$id.'" value="'.$auto_val.'" required></td>';
    }
    else{
        echo '<td text-align="right">'.$text.'</td>';
        echo '<td  text-align="left"><input type="text" name="'.$id.'" id="'.$id.'" value="'.$auto_val.'"></td>';
    }
}


function display_date_box($id, $text){
    $auto_val = "";
    if($text == "Date of Interview"){
        $auto_val = get_current_date();
    }
	echo '<td text-align="right">'.$text.'</td>';
	echo '<td text-align="left"><input type="text" class="date_picker"
	            name="'.$id.'"
				id = "'.$id.'"
	            pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))"
	            value="'.$auto_val.'" required>';
	echo 'Format: YYYY-MM-DD</td>';
}


function display_dropdown($id, $text){
	echo '<td text-align="right">'.$text.'</td>';
	//We need to get the dropdown option from the database
	$sql1 = 'SELECT response_table FROM form_questions WHERE question_id = '.$id.'';
	$result1 =   mysql_query($sql1) or die ('Query1a failed:'. mysql_error());
	$row1 = mysql_fetch_array($result1,MYSQLI_ASSOC);
	$sql2 = 'SELECT * FROM '.$row1{'response_table'}.'';
	$result2 =   mysql_query($sql2) or die ('Query2 display_dropdown failed:'. mysql_error());
    $meta = mysql_fetch_field($result2);
		if (!$meta) {
			echo "No meta available<br />\n";
		}
		$primary_column = $meta->name;
	echo '<td>';
	echo '<select name="'.$id.'" id="'.$id.'">';
	while($row2 = mysql_fetch_array($result2,MYSQLI_ASSOC)){
		echo '<option value="'.$row2{$primary_column}.'">
				'.$row2{'response'}.'</option>';
	}
	echo '</select>';
	echo '</td>';
}


function display_radio_buttons($id, $text){
	echo '<td text-align="right">'.$text.'</td>';
	//We need to get the checkbox option from the database
	$sql1 = 'SELECT response_table, response_column FROM form_questions WHERE question_id = '.$id.'';
	//echo "<br/>sql1 : $sql1<br/>";
	$result1 =   mysql_query($sql1) or die ('Query1b failed:'. mysql_error());
	$row1 = mysql_fetch_array($result1,MYSQLI_ASSOC);
	$sql2 = 'SELECT * FROM '.$row1{'response_table'}.'';
	//echo "<br/>sql2 : $sql2<br/>";
	$result2 =   mysql_query($sql2) or die ('Query2 display_checkbox failed:'. mysql_error());
	$meta = mysql_fetch_field($result2);
		if (!$meta) {
			echo "No meta available<br />\n";
		}
		$primary_column = $meta->name;
		//echo '<br/>primary col is '.$primary_column.'<br/>';
	echo '<td>';
	while($row2 = mysql_fetch_array($result2,MYSQLI_ASSOC)){
		
		/*get primary key column from response table*/
		$sql3 = 'SELECT * FROM '.$row1{'response_table'}.'';
		//echo "<br/>sql3 : $sql3<br/>";
		$result3 = mysql_query($sql3) or die ('Query3 failed:'. mysql_error());
		//echo '<br/>num fields is  '.mysql_num_fields($result3).'<br/>';				
		$sql4 = 'SELECT * FROM '.$row1{'response_table'}.'
				WHERE '.$row1{'response_column'}.' = \''.addslashes($row2{$row1{'response_column'}}).'\'';
		//echo "<br/>sql 4 : $sql4<br/>";
		$result4 = mysql_query($sql4) or die ('Query4 failed:'. mysql_error());
		$row4 = mysql_fetch_array($result4,MYSQLI_ASSOC);
		echo '<input type="radio" name="'.$id.'" id="'.$id.'"
				value="'.$row2[$primary_column].'">
				'.$row2{'response'}.'</input>';
	}
	echo '</td>';
}

function display_checkbox($row, $id, $text){
	echo '<td style="vertical-align:text-top"">'.$text.'</td>';
	//We need to get the checkbox option from the database
	$sql1 = 'SELECT response_table FROM form_questions WHERE question_id = '.$id.'';
	//echo "<br/>sql1 : $sql1<br/>";
	$result1 =   mysql_query($sql1) or die ('Query1c failed:'. mysql_error());
	$row1 = mysql_fetch_array($result1,MYSQLI_ASSOC);
	$sql2 = 'SELECT * FROM '.$row1{'response_table'}.'';
	//echo "<br/>sql2 : $sql2<br/>";
	$result2 =   mysql_query($sql2) or die ('Query2 display_checkbox failed:'. mysql_error());
	$meta = mysql_fetch_field($result2);
		if (!$meta) {
			echo "No meta available<br />\n";
		}
		$primary_column = $meta->name;
		//echo '<br/>primary col is '.$primary_column.'<br/>';
	echo '<td>';
	while($row2 = mysql_fetch_array($result2,MYSQLI_ASSOC)){
		
		/*get primary key column from response table*/
		$sql3 = 'SELECT * FROM '.$row1{'response_table'}.'';
		//echo "<br/>sql3 : $sql3<br/>";
		$result3 = mysql_query($sql3) or die ('Query3 failed:'. mysql_error());
		//echo '<br/>num fields is  '.mysql_num_fields($result3).'<br/>';				
		$sql4 = 'SELECT * FROM '.$row1{'response_table'}.'
				WHERE response = \''.addslashes($row2{'response'}).'\'';
		//echo "<br/>sql 4 : $sql4<br/>";
		$result4 = mysql_query($sql4) or die ('Query4 failed:'. mysql_error());
		$row4 = mysql_fetch_array($result4,MYSQLI_ASSOC);
		echo '<input type="checkbox" name="'.$id.'[]" id="'.$id.'"
				value="'.$row2[$primary_column].'">
				'.$row2{'response'}.'</input>';
        echo '<br/>';
	}
	echo '</td>';
}


function display_label($text){
	echo '<td colspan="2">'.$text.'</td>';
}

function display_SSN_box($id, $text, $is_required){

	$auto_val = "";

    if($text == "Interviewer's Name"){
        $auto_val = get_user_real_name();
    }

    if($is_required){
        echo '<td text-align="right">'.$text.'</td>';
        echo '<td  text-align="left"><input type="text" name="'.$id.'" id="'.$id.'" value="'.$auto_val.'" required>';
        echo 'Format: ###-##-####</td>';
    }
    else{
        echo '<td text-align="right">'.$text.'</td>';
        echo '<td  text-align="left"><input type="text" name="'.$id.'" id="'.$id.'" value="'.$auto_val.'">';
        echo 'Format: ###-##-####</td>';
    }
}

function display_grouping_name($group_id){
	$sql = "SELECT grouping_name FROM grouping_names WHERE grouping_id = $group_id";
	$result =   mysql_query($sql) or die ('Query failed:'. mysql_error()."<br/>sql:$sql<br/>");
	$row = mysql_fetch_array($result,MYSQLI_ASSOC);
	echo '<h2>'.$row{'grouping_name'}.'</h2>';
}

function get_user_real_name(){
    $sql = "SELECT first_name, last_name
            FROM user
            WHERE username = '". $_SESSION['username']."'";
    $result = mysql_query($sql) or die("sql: $sql<br/>");
    if($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
        return $row{'first_name'}." ".$row{'last_name'};
    }
    return "";
}

function get_current_date(){
    $date = date('Y-m-d', time());
    return $date;
}

function get_start_grouping_num(){
    $sql = "SELECT grouping_id
            FROM form_questions
            WHERE is_used = 1
            ORDER BY grouping_id";
    $result = mysql_query($sql) or die("sql: $sql<br/>");
    $row = mysql_fetch_array($result, MYSQLI_ASSOC);
    return $row{'grouping_id'};
    mysql_free_result($result);
}
?>			
