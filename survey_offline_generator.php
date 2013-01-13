<?php 
session_start();

include ('update_manifest.php');

function generateOfflineSurveyType($surveyType){
	if($surveyType == 'normal'){
		$user_type_num = $_SESSION['user_type_num']; 			//2 is administrator signifier
		$_SESSION['user_type_num'] = null;
		generateOfflineSurvey();
		$_SESSION['user_type_num'] = $user_type_num;
	}
	else if($surveyType == 'admin'){
		generateOfflineSurvey();
	}
}



function generateOfflineSurvey(){	
    update_manifest();
   $_SESSION['grouping_num'] = null;								//prevents possible loading of page when user would be on the 'completed survey' page if they were using online version
   
	//---PHP USER MUST HAVE WRITE PERMISSIONS ON SERVER OR ELSE IT WILL FAIL!!!!
	//---this will start making a buffer of all output html stuff...at end of file it will be saved to a static html page.
	//---offline web app does not work with 'include (......php)' commands.  This is why it will be saved to a static html file.
	ob_start(); 

echo '
	 <!DOCTYPE html>
		<html manifest="s2h_survey_offline.manifest">
				<head>
				  <title>Homeless Shelter Occupancy</title>
				  <link href="style.css" rel="stylesheet" type="text/css" />
				  <link href="screen.css" rel="stylesheet" type="text/css" />
						
					<!-- Javascript event handler to force the offline web app to \'reload\' the cached page when an updaded version has been downloaded. -->	
						<script type="text/javascript">
							window.applicationCache.addEventListener(\'updateready\', function(){
								window.applicationCache.swapCache();
								window.location.reload(false);									<!-- false param tells it to reload and use cache data...true tells it to reload with data from server....(ignores true if using application cache -->
							}, false);	
							
							window.applicationCache.addEventListener(\'cached\', function(){
								window.location.reload(false);									<!-- false param tells it to reload and use cache data...true tells it to reload with data from server....(ignores true if using application cache -->
							}, false);	
						</script>
							  
				  <!-- JQUERY STUFF -->
		            <link href="css/overcast/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
		            <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
		            <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
		            <script src="js/button.js" text="text/javascript"></script>
		            <script src="js/survey/survey.js" text="text/javascript"></script>  			
					<script>
						$(function(){

								//------CHECK FOR LOCAL STORAGE CAPABILITY--------
									$(document).ready(function() {
									  // Handler for .ready() called.
									  //window.alert("localDatabaseCompatable function running now!");
										if(typeof(localStorage) == \'undefined\') {
											//Check if the browser has local storage. 
											window.alert("Your internet broswer does not support local storage.  Please upgrade your browser to the most recent version to use the offline survey.");
											return false; 
										} 
										else if (typeof(window.applicationCache) == \'undefined\') {
													window.alert("Your internet broswer does not support offline webb apps.  Please upgrade your browser to the most recent version to use the offline survey.");
													return false; 
										}
									});
								
								
									//---------SAVE SURVEY DATA function------------								
									$(\'#surveyForm\').submit(function(e){
									   //STOPS the form from submitting...THUS the page will not reload on submit...is problematic for offline web app.
									   //...we are doing this be able to use the HTML5 native form validation
									   e.preventDefault(); 	
									   
									   //---ensure we have a starting value for client id set...
										if(localStorage.getItem(\'currentClientId\') == null){
											localStorage.setItem(\'currentClientId\', 0 );
											var currentClientIdTemp = parseInt(0);
										} else {
											var currentClientIdTemp = parseInt(localStorage.getItem(\'currentClientId\'));					//must do parseInt because browser local storage is as strings for everything
										}
										//---collect survey data---
										$currentClientData = $("#surveyForm").serialize();
										//console.log($currentClientData);
										
										//---Save current client\'s survey data localy with unique name---
										localStorage.setItem(\'S2H-LocalClientSurvey-\' + currentClientIdTemp, $currentClientData);
										
										//---Update count of clients surveyed locally---
										currentClientIdTemp +=  parseInt(1);																//force javascript to treat it as int and not strings
										localStorage.setItem(\'currentClientId\',  currentClientIdTemp);
										
										//---Alert user of successful save.  Using alert window because when user is offline, the page can\'t run the php to generate the success page.
										alert(\'Survey has been saved.  You may now start another survey.\');
										
										//---Clear the survey (this is triggered by intput type button...it does not cause the page to reload so we me clear it here
										document.getElementById(\'surveyForm\').reset();				

										return false;
									});
					});					
				</script>
									
';		//end echo out of start of html page

										//only allow administrators to have this function
										if($_SESSION['user_type_num'] == 2){			
											echo "					
										<script>							<!---this has to be echoed out seperately ... it breaks otherwise...-->
											$(function(){
												//---------SYNC TO DATABASE function------------
													$('#sync').click(function () {
														//---find out how many client survey must be synced---
														if(localStorage.getItem('currentClientId') == null){
															localStorage.setItem('currentClientId', 0 );
															var currentClientIdTemp = parseInt(0);
															alert('There are no saved client surveys to sync do the database!');
															return;
														} else {
															var currentClientIdTemp = parseInt(localStorage.getItem('currentClientId'));					//must do parseInt because browser local storage is as strings for everything
														}
														
														for( var clientId=0; clientId < currentClientIdTemp; clientId++){
															var clientData = localStorage.getItem('S2H-LocalClientSurvey-' + clientId);
															//alert('ClientId'+clientId+' = ' + clientData);
															var client = {\"client\" : clientData};
															var text = $.ajax ({
																type: \"POST\",
																url: \"sync-server.php\",
																data: client
																//success: function(text){
																//	  $('#entries').html(text); 
																//}
																});    
															//alert('text = ' + text);
															console.log(text);									
														}
														alert('All saved client surveys have been synced to the database!');
													});	
													
													
												//---------DELETE LOCAL SAVED CLIENT SURVEYS function------------
													$('#delete').click(function () {
														if( confirm('Delete Localy Saved Clients?') ){
															var clientCount = null;
															//---find out how many client surveys there are to delete---
															if(localStorage.getItem('currentClientId') != null){
																var currentClientIdTemp = parseInt(localStorage.getItem('currentClientId'));  //must do parseInt because browser local storage is as strings for everything
															} else {
																alert('There are no saved client surveys to delete!');
																return;
															}
															
															for( var clientId=0; clientId < currentClientIdTemp; clientId++){
																var clientDataKey = 'S2H-LocalClientSurvey-' + clientId;
																if ( localStorage.getItem(clientDataKey) != null ){
																	localStorage.removeItem(clientDataKey);
																	//alert('removed client' + clientId);
																}
															}
															
															localStorage.removeItem('currentClientId');
															alert('All saved client surveys have been deleted!');
														}
													});	
												});
											</script>							<!---this has to be echoed out seperately ... it breaks otherwise...-->
												
												";
										}
								
echo "
							
				  <!--<script>jQuery.noConflict();</script>-->		
				  
					  
				</head>
				<body>
";		//---end echo out of end of the start of the html page
				

	include ('header.php');
	include ('changeGroupingNum.php');
	include ('dbconfig.php');          
	include ('compute_vi.php');
	include ('survey_display_function.php');

	//---NOTE:  this page can NOT compute the VI and set it.   It must be done via admin in the update weights because it calls the compute/update vi for all clients.

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
			
					echo '<div id="Tips" style="width:30%;position:relative;left:3%;top:3%;background:#B4CFEC;border: 1px solid #000000;padding: 10 10 10 10">
							<B>Tips</B>
							<br><br>
							<p>' . $row1{'tips'} .'</p>
							</div>';
					
					

					//-------------------SET CLIENT ID and QUESTION GROUPING NUMBER-------------------
					//$_SESSION['clientID'] = $_POST['username'];
					if($_SESSION['clientID'] == null ){
						$sql0 = 'INSERT INTO client (client_id) VALUES(-1)';
						$result0 =   mysql_query($sql0) or die ('Query0 failed:'. mysql_error());					
						$_SESSION['clientID'] = mysql_insert_id();																//mysql_insert_id() is on a per connection basis --> no race condition with other concurrent surveyors
						$sql1 = 'UPDATE client SET details_link=\'<a href="client_details.php?client_id='.$_SESSION['clientID'].'">Details</a>\' WHERE client_id='.$_SESSION['clientID'];				//when inserting varchar into db must have apostrophies around it...and need to escape special chars...eg.  for ' to go into db need to do \'
						$result1 =   mysql_query($sql1) or die ('Query1x failed:'. mysql_error());
						
						
						//$_SESSION['grouping_num'] = 1;
						//$grouping_num = $_SESSION['grouping_num'];	
						//echo'<p>CLIENTID -- $_SESSION['.'"grouping_num"'.'] = 1; </p>';
					}
					
					//-----MUST EVENTUALLY BUILD IN SUPPORT FOR MULTIPLE FORMS.....use the form_id column in the grouping_names table
					if($_SESSION['minGroupingNum'] == null){
								$sql1 = 'SELECT MIN(grouping_id) FROM grouping_names';
								$result =  mysql_query($sql1) or die ('Query min failed:'. mysql_error());
								$row = mysql_fetch_row($result);
								$_SESSION['minGroupingNum'] = $row[0];
					}
					if ($_SESSION['maxGroupingNum'] == null){
								$sql1 = 'SELECT MAX(grouping_id) FROM grouping_names';
								$result =   mysql_query($sql1) or die ('Query max failed:'. mysql_error());
								$row = mysql_fetch_row($result);
								$_SESSION['maxGroupingNum']= $row[0];
					}
					
					if($_SESSION['grouping_num'] == null ){														//need this check only if session has not been destroyed
							$_SESSION['grouping_num'] = 0;
							$_SESSION['grouping_num'] = incremetGroupingNum($_SESSION['grouping_num'], $_SESSION['maxGroupingNum'] );			//this is done incase the survey will not start/use the first group of questions
					}
					$grouping_num = $_SESSION['grouping_num'];					//this value could be set by a redirect to this page:  survey.php-->input.php-->survey.php		both survey.php and input.php modify $_SESSION['grouping_num']

					
					//echo 'grouping_num: ' . $grouping_num . '<br/>';
					//echo '<p>'.$_SESSION['clientID'].'</p>';
					echo '<div id="wrapper">';
					
						echo '<form id="surveyForm" method="post">';				// THIS MUST BE OUTSIDE OF THE TABLE...otherwise javascript has problems finding it's elements sometimes...
						echo '<table width="100%">';
							echo '<tr><td width="50%" text-align="right"></td><td width="50%" text-align="left"></td></tr>';
							while( $_SESSION['grouping_num'] <= $_SESSION['maxGroupingNum'] ) {							
									//-----GET ALL THE QUESTIONS and PUT THEM ON ONE PAGE FOR OFFLINE VERSION-------
									//-------------------SQL Get Questions-------------------
									$query  =   "SELECT * FROM form_questions WHERE is_used = 1 AND grouping_id = ".$_SESSION['grouping_num']."  ORDER BY `grouping_id`, `question_order_num`";
									//$query  =   "SELECT * FROM form_questions WHERE is_used = 1 AND grouping_id = 1 ORDER BY `grouping_id`, `question_order_num`";
									//echo $query;
									$result =   mysql_query($query) or die ('Query failed:'. mysql_error());
									echo "";
									
									//----echo out the questions into a table form
										$grouping_name_displayed = false;
										while ($row = mysql_fetch_array($result,MYSQLI_ASSOC)){														//$row becomes the next question row entry in returned results from the table.
											echo '<tr>';
												//----this must be inside a <td></td> or else all the titles will end up in one groupd above all the quetsions
												if(!$grouping_name_displayed){
													echo '<td colspan="2">';
														echo '<br/>';
														display_grouping_name($row{'grouping_id'});
														$grouping_name_displayed = true;
													echo '</td>';
												}
											echo '</tr>';
											echo '<tr>';
												if($row{'question_response_type'} != 5){
													//this is a label, so don't add to input array
													array_push($input_id_array, $row{'question_id'});
												}
												echo display_question($row{'question_id'},$row{'question_text'}, $row{'question_response_type'}, $row);
											echo '</tr>';
										}
										$_SESSION['grouping_num'] = incremetGroupingNum($_SESSION['grouping_num'], $_SESSION['maxGroupingNum'] );
										
										//-------FORCE SKIPPING OF PHOTO----DOES NOT CURRENLTY WORK IN ONE PAGE OFFLINE VERSION------------
										if(	$_SESSION['grouping_num'] == 4 ){
											$_SESSION['grouping_num'] = incremetGroupingNum($_SESSION['grouping_num'], $_SESSION['maxGroupingNum'] );
										}
							}
						// -------------------Page one (stick it in a form):------------------


						// calls the input.php file...will have to redirect to this page and select the next grouping of questions
						
					
						//if(count($input_id_array) > 1){						//rudamentry way of detecting if there are no questions to ask...must be changed to accomodate entire grouping sections being skipped by admin				
						if($grouping_num <= $_SESSION['maxGroupingNum']) {
							//echo 'count($input_id_array) = ' . count($input_id_array);
								echo '<tr>';
									echo '<td align="left"><input type="button" onclick="if(confirm(\'Reset the entire survey?\')){document.getElementById(\'surveyForm\').reset();} " value="Reset Survey"></td>';
									//echo '<td align="right"><button id="saveSurvey"> Save Client\'s Survey</button></td>'; 		//appends data to URL
									echo '<td align="right"><button id="save"> Save Client\'s Survey</button></td>'; 		//appends data to URL
									//echo '<td align="right"><input type="button" id="saveSurvey" value="Save Client\'s Survey"></td>';			//using an input type="button" will keep the page from submitting and trying to reload...causes problems for offline web app. (could do event.preventDefault(); on a jquery submit event cacther, but this is simpler)
								echo '</tr>';
						}
						else {
							//---FORCE RELOAD OF PAGE FOR OFFLINE...WILL ALWAYS HAVE THE CURRENT SURVEY DISPLAYED
							//---PREVENTS CACHING/SAVING OF 'completed survey page' THAT IS MADE FROM PHP 
							//echo '<p>Thank you for completing this survey.  It has automatically been saved to our database.</p>';
							$_SESSION['clientID'] = null;
							$_SESSION['grouping_num'] = null;
							header('Location: survey_offline.php');
							
							//echo '<tr><td></td><td align="right"><input type="submit" name="submit" value="Submit"></td></tr>'; 
							}
						echo '</form>';
					echo '</table>';
						
						
					
									
						//echo '<form method="post" action="input.php" onsubmit="return confirm(\'Reset the entire survey?\')" >';		
						echo '<form method="post">';		
							echo '<table width="100%">'; 
								echo '<tr><td width="33%"></td><td width="33%"></td><td width="33%"></td></tr>';			//column layout set up
								//echo '$_SESSION[\'grouping_num\'] = ' . $_SESSION['grouping_num'];
								if( $_SESSION['grouping_num'] == null){
									echo '<tr>';
									echo '<td></td>';
									echo '<td  align="center"><button id="newSurvey"> Start New Survey </button></td>'; 
									echo '<td></td>';
								echo '</tr>';
								}					
								//only allow administrators to have this function
								if($_SESSION['user_type_num'] == 2){
									echo '<tr>';
										echo '<td></td>';
										//echo '<td  align="center"><button id="sync"> Sync Saved Surveys With Database </button></td>'; 
										echo '<td  align="center"><input type="button" id="sync" value="Sync Saved Surveys With Database"> </td>'; 
										echo '<td></td>';
									echo '</tr>';								
									echo '<tr>';
										echo '<td></td>';
										//----using onClick will prevent the from from being submitted and the keep the page from reloading---
										//echo '<td  align="center"><button id="delete"> Delete Saved Surveys </button></td>'; 
										echo '<td  align="center"><input type="button" id="delete" value="Delete Saved Surveys"></td>'; 
										echo '<td></td>';
									echo '</tr>';								
								}
							echo '</table>';
						echo '</form>';
					echo '</div>';
					
					//mysql_free_result($result); 
echo "				
			</body>
	</html>
";

	//---THIS IS WHERE THE OUTPUT HTML IS SAVED AS A STATIC WEB PAGE....THE STATIC WEB PAGE IS THE PAGE THAT SHOULD BE CACHED AND USED OFFLINE!!!!

	$stringHTML = ob_get_contents(); 
		
	$fileP = fopen( "survey_offline_static.html", "w+") or die	("--- " . print_r(error_get_last()) . " ---unable to open offline web application static HTML file");			//option 'w+' will open the file for read/write, clear the contents, and place file pointer at start of file.
	//echo '</br>$fileP = ' . $fileP;
	
	//echo '$stringHTML =    ' . $stringHTML;
   
	$writeSuccess = fwrite($fileP, $stringHTML);
	$closeSuccess = fclose($fileP);
	
	if( $writeSuccess == false  ||  $closeSuccess == false ){
		die	("Failed to update offline web application manifest file");
	}


}		//---end function generateOfflineSurvey()

	
?>
