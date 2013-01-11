<!DOCTYPE html>
    <html>
			<head>
			  <title>Homeless Shelter Occupancy</title>
			  <link href="style.css" rel="stylesheet" type="text/css" />
			  <link href="screen.css" rel="stylesheet" type="text/css" />
			  <link href="facebox.css" rel="stylesheet" type="text/css" />
			  <script src="/javascripts/jquery.js" type="text/javascript"></script>
			  <!--<script>jQuery.noConflict();</script>-->
			  <script src="/javascripts/prototype.js" type="text/javascript"></script>
			  <script src="/javascripts/effects.js" type="text/javascript"></script>
			  <script src="/javascripts/dragdrop.js" type="text/javascript"></script>
			  <script src="/javascripts/controls.js" type="text/javascript"></script>
			  <script src="/javascripts/application.js" type="text/javascript"></script>
			  <script src="/javascripts/feedback.js" type="text/javascript"></script>
			  <script src="/javascripts/facebox.js" type="text/javascript"></script>
			  
			  <!-- <script type="text/javascript" language="javascript" src="jquery-1-3.js"></script> -->
			  <script type="text/javascript" language="javascript" src="jquery-1-3.js"></script>			  
			  <!-- <script type="text/javascript" language="javascript" src="dlbScriptCrossBrowser.js"></script> -->
			  <script type="text/javascript" language="javascript" src="jQuery.dualListBox-1.3.js"></script>
			  <link href="/jquery.dualListBox-1.3/style.css" rel="stylesheet" type="text/css" />
			  
			  <script language="javascript" type="text/javascript">
				$(function() {
					$.configureBoxes();
				});
			  </script>

				<?php
					//not need to do jquery $(function()....) here because it is already inside the js file for it...NOT TRUE FOR FULL PLUGIN...ONLY TRUE FOR CORE JS FILE OF PLUGIN
				?>
				
			</head>
			
			<body onload="javascript:setOffsets()">
				<div id="page">
						<div id="header" style="height:75px;">		
							<div style="width:60%;position:absolute;top:-25px;">
							<h1><a href="#">Street to Home 2.0</a></h1>
							<p class="description" style="font-size: 0.8em;">A Cooperation between Georgia Tech and the United Way of Metropolitan Atlanta</p>
							</div>
							<div style="position:absolute;width:40%;top:0px;left:60%"> 
							<span class="logged_in_user"><B>Currently logged in as: <Font Color="Yellow"><?php echo $_SESSION['LOGIN']; ?></Font> | <a href="logout.php"><Font Color="Yellow">Signout</Font></a></B></span>	
							</div>
							<ul class="menu"><li><a href="survey.php">Survey</a></li>
							<li><a href="report_server_side.php">Reports</a></li>
							<li></li><li><a href="#">Users</a></li>
							<li><a href="#">Modify Language</a></li>
							<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
							<li><a href="admin_options.php">Admin</a></li>
							<li><a href="#"><i>Help</i></a></li>
							<li><a href="javascript:toggle_feedback_form()"><i>Feedback?</i></a></li>
							</ul>	
									
						</div>

						<hr />
						<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>
						<?php
							include('dbconfig.php');
							//populate listbox with used questions in the database
							//-------------------Start Variables-----------------------
							$username   =   USERNAME;
							$password   =   PASSWORD;
							$database   =   DB_NAME;


							//-------------------Connect To Database-------------------
							$link   =   mysql_connect(localhost,$username,$password) or die ('Could not connect :'.  mysql_error());
							mysql_select_db($database) or die( "Unable to select database");
							
							//-------------------Get the tip and show it in the this page from database-------------------
							$sql1 = 'SELECT tips FROM tips_table WHERE page_name="report_config.php"';
							$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
							$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);

							echo '<div id="Tips" style="width:30%;position:relative;left:3%;top:3%;background:#B4CFEC;border: 1px solid #000000;padding: 10 10 10 10">
									<B>Tips</B>
									<br><br>
									<p>' . $row1{'tips'} .'</p>
									</div>';
									
									
						?>

					<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>

<!--<p> BUILD IN FUNCTIONALITY FOR SELECTING QUESTIONS USED ON A PER FORM BASIS</p>-->
					<form id="dualListBox" method="post" action="report_server_side.php">
					<!--<form id="dualListBox" method="post" action="report_config.php">-->
					<!--<form id="dualListBox" method="post" action="report_server_side_Hacked_DynamicTableGeneration.php"> -->
					<!--<form id="dualListBox" method="post" action="report_server_processing_Hacked_attempt.php"> -->
						<table border="0" style="width:100%">
							<tbody>
								<tr>
									<td valign="top" align="left" style="width:50%">
											Filter: <input id="box1Filter" type="text"/>
											<button id="box1Clear" type="button">X</button><br />
											<select id="box1View" multiple="multiple" style="height:500px;width:100%;">
												<?php

													//---------------------Get all the used questions---------------
													////$sql = "SELECT * FROM form_questions WHERE is_used = 1";
													//$sql1 = "SELECT * FROM form_questions";
													//$results1 = mysql_query($sql1) or die ('Query1 failed: ' . mysql_error());

													$sql2 = "SELECT column_name 
															 FROM map_question_id_to_client_column_name NATURAL JOIN
															 form_questions
															 WHERE is_used = 1 AND column_name IS NOT NULL AND question_response_type != 5";
													$results2 = mysql_query($sql2) or die ('Query2 failed: ' . mysql_error());




													 while ($row2 = mysql_fetch_array($results2,MYSQLI_ASSOC))														//$row becomes the next question row entry in returned results from the table.
													{
														if($row2{column_name} == "language_pref"){
															echo '<option value="VI">VI</option>';
														}
														echo '<option value="' .$row2{column_name} .'">' .$row2{column_name}. '</option>';
													}
													mysql_free_result($results2);																

													//USE SQL HERE TO POPULATE SELECT BOX
													/* EXAMPLE OUTPUT: 
													* 
													* <option value="501497">AAPA - Asian American Psychological Association</option>
													* <option value="501053">Academy of Film Geeks</option>
													* <option value="500001">Accounting Association</option>
													* 
													*/
												?>
											</select>
										<span id="box1Counter" class="countLabel"></span>
										<select id="box1Storage"></select>
												
									</td>
			
									<td valign="middle" align="center">
										<button id="allTo2" type="button"> >> </button>
										<button id="to2" type="button"> > </button>
										<button id="to1" type="button"> < </button>
										<button id="allTo1" type="button"> << </button>
										
										<!--
											<button id="allTo2" type="button">&nbsp;>>&nbsp;</button>
											<button id="to2" type="button">&nbsp;>&nbsp;</button>
											<button id="to1" type="button">&nbsp;<&nbsp;</button>
											<button id="allTo1" type="button">&nbsp;<<&nbsp;</button>
									
											//<input id="allTo2" type="button" value=" >> " /><br />
											//<input id="to2" type="button" value=" > " /><br />
											//<input id="to1" type="button" value=" < "  /><br />
											//<input id="allTo1"  type="button" value=" << "  />
											
											//<input id="btnAddAll" type="button" value=" >> " onclick="addall();" /><br />
											//<input id="btnAdd" type="button" value=" > " onclick="add();" /><br />
											//<input id="btnRemove" type="button" value=" < "  onclick="remove();" /><br />
											//<input id="btnRemoveAll"type="button" value=" << "  onclick="removeall();" />
										-->
									</td>

									<td valign="top" align="left" style="width:50%">
												Filter: <input type="text" id="box2Filter" />
												<button type="button" id="box2Clear">X</button><br />
												<select id="box2View" name="chosenFilters[]" multiple="multiple" style="height:500px;width:100%;"></select><br />  <!-- by using name="somthing[]" with [], php serverside will collect this as an array when it posts -->
												<span id="box2Counter" class="countLabel"></span>
												<select id="box2Storage"></select>
									</td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td align="center">
										<input type="submit" name="setCustom" value="Generate Custom Report">
										&nbsp; &nbsp;
										<input type="submit" name="setDefault" value="Set As Default Report">
									</td> 		<!-- this is causing alighment issues for the move arrow buttons -->
								</tr>
							</tbody>
						</table>
					</form>					
				</div>			
			</body>
	</html>
