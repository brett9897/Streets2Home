 <!DOCTYPE html>
    <html>
        <head>
			<meta charset="utf-8" />
			<title>jQuery UI Datepicker - Default functionality</title>
			<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
			<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
			<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
			<link rel="stylesheet" href="/resources/demos/style.css" />
			<script>
			$(function() {
				$( "#datepicker" ).datepicker();
			});
			</script>		
			<style type='text/css'>
				#wrapper {
					width:950px;
					height:auto;
					padding: 13px;
					margin-right: auto;
					margin-left: auto;
					background-color: #fff;
				}
			</style>
		</head>
        <body bgcolor="#e1e1e1">
            <div id="wrapper">
                <center><font fact="Andalus" size="5">Test Form</font></center>
                <br />
            </div>

            <?php
            
				function display_question($id, $text, $data_type, $row){
					switch($data_type){
						case 1:
							display_textbox($id, $text);
							break;
						case 2:
							display_date_time($id, $text);
							break;
						case 3:
							display_dropdown($row, $id, $text);
							break;
						case 4:
							display_checkbox($row, $id, $text);
							break;
						case 5:
							display_label($text);
							break;
					}
				}
				function display_textbox($id, $text){
					echo '<td width="50%" text-align="right">'.$text.'</td>';
					echo '<td><input type="text" name="'.$id.'" id="'.$id.'"></td>';
				}
				
				function display_date_time($id, $text){
					echo '<td>'.$text.'</td>';
					echo '<td><input type="text" name="'.$id.'" id="datepicker" size="25">';
				}
				
				function display_dropdown($row, $id, $text){
					echo '<td>'.$text.'</td>';
					//We need to get the dropdown option from the database
					$sql1 = 'SELECT response_table, response_column FROM form_questions WHERE question_id = '.$id.'';
					$result1 =   mysql_query($sql1) or die ('Query1 failed:'. mysql_error());
					$row1 = mysql_fetch_array($result1,MYSQLI_ASSOC);
					$sql2 = 'SELECT '.$row1{response_column}.' FROM '.$row1{response_table}.'';
					$result2 =   mysql_query($sql2) or die ('Query2 failed:'. mysql_error());
					echo '<td>';
					echo '<select name="'.$id.'" id="'.$id.'">';
					while($row2 = mysql_fetch_array($result2,MYSQLI_ASSOC)){
						echo '<option value="'.$row2{$row1{response_column}}.'">
								'.$row2{$row1{response_column}}.'</option>';
					}
					echo '</select>';
					echo '</td>';
				}
				
				function display_checkbox($row, $id, $text){
					echo '</tr>';	//close tag here to create another row for the choices
					echo '<td colspan="2">'.$text.'</td>';
					echo '<tr>';	//new row for check boxes
					//We need to get the checkbox option from the database
					$sql1 = 'SELECT response_table, response_column FROM form_questions WHERE question_id = '.$id.'';
					//echo "<br/>sql1 : $sql1<br/>";
					$result1 =   mysql_query($sql1) or die ('Query1 failed:'. mysql_error());
					$row1 = mysql_fetch_array($result1,MYSQLI_ASSOC);
					$sql2 = 'SELECT * FROM '.$row1{response_table}.'';
					//echo "<br/>sql2 : $sql2<br/>";
					$result2 =   mysql_query($sql2) or die ('Query2 failed:'. mysql_error());
					$meta = mysql_fetch_field($result2);
						if (!$meta) {
							echo "No meta available<br />\n";
						}
						$primary_column = $meta->name;
						//echo '<br/>primary col is '.$primary_column.'<br/>';
					echo '<td colspan="2">';
					while($row2 = mysql_fetch_array($result2,MYSQLI_ASSOC)){
						
						/*get primary key column from response table*/
						$sql3 = 'SELECT * FROM '.$row1{response_table}.'';
						//echo "<br/>sql3 : $sql3<br/>";
						$result3 = mysql_query($sql3) or die ('Query3 failed:'. mysql_error());
						//echo '<br/>num fields is  '.mysql_num_fields($result3).'<br/>';				
						$sql4 = 'SELECT * FROM '.$row1{response_table}.'
								WHERE '.$row1{response_column}.' = \''.addslashes($row2{$row1{response_column}}).'\'';
						//echo "<br/>sql 4 : $sql4<br/>";
						$result4 = mysql_query($sql4) or die ('Query4 failed:'. mysql_error());
						$row4 = mysql_fetch_array($result4,MYSQLI_ASSOC);
						echo '<input type="checkbox" name="'.$id.'" id="'.$id.'"
								value="'.$row2[$primary_column].'">
								'.$row2{$row1{response_column}}.'</input>';
					}
					echo '</td>';
				}
				
				function display_label($text){
					echo '<td width="50%" text-align="right">'.$text.'</td>';
				}
				
				function display_grouping_name($group_id){
					$sql = "SELECT grouping_name FROM grouping_names WHERE grouping_id = $group_id";
					$result =   mysql_query($sql) or die ('Query failed:'. mysql_error());
					$row = mysql_fetch_array($result,MYSQLI_ASSOC);
					echo '<h2>'.$row{grouping_name}.'</h2>';
				}
				
                $input_id_array[] = array();
                //End Variables

                //Connect To Database
                $link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
                mysql_select_db(DB_NAME) or die( "Unable to select database");

                //SQL Get Questions
                $query  =   "SELECT * FROM form_questions WHERE is_used = 1 ORDER BY `grouping_id`, `question_order_num`";
                $result =   mysql_query($query) or die ('Query failed:'. mysql_error());
                echo '<div id="wrapper">';
                echo '<table width="90%">';
                echo '<form method="post" action="input.php">';
                //Get results
                $grouping_num = 0;
                while ($row = mysql_fetch_array($result,MYSQLI_ASSOC))
                    {
						if($row{'grouping_id'} != $grouping_num){
							echo '</table>';
							display_grouping_name($row{'grouping_id'});
							$grouping_num = $row{'grouping_id'};
							echo '<table width="90%">';
						}
						echo '<tr>';
						if($row{'question_response_type'} != 5){
							//this is a label, so don't add to input array
							array_push($input_id_array, $row{'question_id'});
						}
                        echo display_question($row{'question_id'},
							$row{'question_text'}, $row{'question_response_type'});
						echo '</tr>';
                    }
                // Page one (stick it in a form):
				for ($round=0;$round<count($input_id_array);$round++){
				echo "<input type=hidden name='array$round' value='$input_id_array[$round]'>";
				}
                echo '<tr><td></td><td align="right"><input type="submit"
						name="submit" value="Sent"></td></tr>';
                
                echo '</div>';
                
                mysql_free_result($result); 
                mysql_close($link);
            ?>
        </body>
</html>
