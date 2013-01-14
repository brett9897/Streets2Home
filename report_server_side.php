<?php
require_once 'dbconfig.php';
	//// Inialize session
	//session_start();
	//
	//// Check, if username session is NOT set then this page will jump to login page
	//if (!isset($_SESSION['username'])) {
	//header('Location: index.php');
	//}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
	  <title>Homeless Shelter Occupancy</title>
	  <link href="style.css" rel="stylesheet" type="text/css" />
	  <link href="screen.css" rel="stylesheet" type="text/css" />
	  
		<link href="demo_page.css" rel="stylesheet" type="text/css" />
		<link href="demo_table.css" rel="stylesheet" type="text/css" />
 
		<link href="shCore.css" rel="stylesheet" type="text/css" />
		<!--jQuery UI stuff-->
	    <link href="css/overcast/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
	   	<script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
	    <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
	    <script src="js/button.js" text="text/javascript"></script>

		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		

		<script type="text/javascript" language="javascript" src="jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#example').dataTable( {
					"sScrollX": "100%",
					"bProcessing": true,
					"bServerSide": true,
					"sAjaxSource": "report_server_processing.php"
				} );
			} );
		</script>
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    		<script type="text/javascript">
     		 google.load('visualization', '1', {packages: ['corechart']});
   		 </script>
   		 <script type="text/javascript">
    		  function drawVisualization() {
      		  // Create and populate the data table.
      		  var data = google.visualization.arrayToDataTable([
         	 ['Vulnerability Index', 'Number of Clients'],
       		   <?php
                    include ('dbconfig.php');
                               //-------------------Connect To Database-------------------
                    $link   =   mysql_connect(HOST,USERNAME,PASSWORD) or die ('Could not connect :'.  mysql_error());
                    mysql_select_db(DB_NAME) or die( "Unable to select database");
                    $sql = "SELECT vi, count(vi) AS count
                            FROM client
                            GROUP BY vi";
                    $rt = mysql_query($sql) or die('Error msg: '.mysql_error().'<br/>
                                sql: '.$sql.'<br/');
                    $number = mysql_num_rows($sql);
                    $i = 1;
                    while($r=mysql_fetch_array($rt, MYSQLI_ASSOC))
                    {
                        if($i != $number){
                            echo '[ '.$r['vi'].', '.$r['count'].'],';
                        }
                        else{
                            echo '[ '.$r['vi'].', '.$r['count'].']';
                        }
                        $i++;
                    }
                    mysql_close($link);
                ?>
        	]);
      
        		// Create and draw the visualization.
        		new google.visualization.ColumnChart(document.getElementById('visualization')).
            		draw(data,
                		 {title:"Number of Clients by Vulnerability Index",
                 	 width:800, height:400,
                  	hAxis: {title: "Vulnerability Index"}}
          	  );
      		}
      

      		google.setOnLoadCallback(drawVisualization);
    		</script>
	</head>
	
	<body>
		
<?php
	include('header.php');
	include('dbconfig.php');
	//populate listbox with used questions in the database

	//-------------------Connect To Database-------------------
	$link   =   mysql_connect(localhost, USERNAME, PASSWORD) or die ('Could not connect :'.  mysql_error());
	mysql_select_db(DB_NAME) or die( "Unable to select database");
	
	//-------------------Get the tip and show it in the this page from database-------------------
	$sql1 = 'SELECT tips FROM tips_table WHERE page_name="report_server_side.php"';
	$result1 = mysql_query($sql1) or die ( 'Query1 failed: ' . mysql_error() );
	$row1 = mysql_fetch_array($result1, MYSQLI_ASSOC);
	mysql_close($link);
	
	$tips = trim($row1{'tips'});
	if( $tips != null && $tips != "" )
	{
	    echo '<div id="tips">
	            <strong>Tips</strong>
	            <br><br>
	            <p>' . $tips .'</p>
	          </div>';
	}
?>
	
	
		<div id="dt_example">
			<div id="container">
				
				<div id="dynamic">
					<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
						<thead>
							<tr>
								
								
								<?php
									session_start();
										include('dbconfig.php');
									
										
									if(  isSet($_POST['useDefaultTable'])     ||   (!isSet($_SESSION['defaultColumns']) && !isSet($_SESSION['aColumns0'])) ){
									//if(  isSet($_POST['useDefaultTable'])     ||   (!isSet($_SESSION['defaultColumns']) &&  $_SESSION['aColumns0']==null) ){
										$_SESSION['aColumns0'] = null;
										//-----------USER CLICKED 'DEFAULT TABLE' BUTTON-----------
										//-----------------------OR--------------------------------
										//----USER IS COMMING TO PAGE FOR FIRST TIME AND WILL USE DEFAULT COLUMS--------------
										//------retrieve default columns from database and store them--------
										
										//-------------------Connect To Database-------------------
										$link   =   mysql_connect(localhost, USERNAME, PASSWORD) or die ('Could not connect :'.  mysql_error());
										mysql_select_db(DB_NAME) or die( "Unable to select database");
										
										//-------------------Get the tip and show it in the this page from database-------------------
										$sql1 = 'SELECT client_attributes FROM default_report_table_columns';
										$result1 = mysql_query($sql1) or die ( 'Query3 failed: ' . mysql_error() );
										mysql_close($link);
										
										while( $row1 = mysql_fetch_array($result1, MYSQLI_ASSOC) ){
											$chosenFilters[] = $row1{'client_attributes'};
										}
										$chosenFilters[] = "details_link";
										$_SESSION['defaultColumns'] = $chosenFilters;
									}
									else if( $_POST['chosenFilters'] != null){		
										if( isSet($_POST['useDefaultTable']) ){
											$_SESSION['aColumns0'] = null;
										}
										else{
											$_SESSION['defaultColumns'] = null;
										}
										//-----------USER IS COMING TO PAGE FROM SELECTING CUSTOM REPORT COLUMNS-------------
										
											//get the custom columns from post

												$_SESSION['aColumns0'] = $_POST['chosenFilters'];
												$chosenFilters=$_POST['chosenFilters'];
												$chosenFilters[] = "details_link";
												$_SESSION['aColumns0'] = $chosenFilters;
												/* //working tested long method
													$recArr2 = $_POST['chosenFilters'];
													$aColumns0 = array();
													foreach($recArr2 as $item){
														$aColumns0[] = $item;
														echo $item."<br>\n";
													}
													$_SESSION['aColumns0'] = $aColumns0;
												*/
												//var_dump($_POST);
												
												//----------check if user wanted to update the default report list-------------
												if( isSet($_POST['setDefault']) ){
													//-------------------Connect To Database-------------------
													$link2   =   mysql_connect(localhost, USERNAME, PASSWORD) or die ('Could not connect :'.  mysql_error());
													mysql_select_db(DB_NAME) or die( "Unable to select database");
													//---update the default report list
													$sql2 = 'DELETE FROM default_report_table_columns';
													$result2 = mysql_query($sql2) or die ('Query2 failed: ' . mysql_error());
													mysql_close($link2);
													
													//----WILL USE PDO FOR OPTIMIZATION-------
													try {
														$dbh = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
														$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

														// this is for INSERT ONLY
														$sql = "INSERT INTO default_report_table_columns (client_attributes) VALUES (:attribute)";
														$query = $dbh->prepare($sql);											//compile the sql stuff  (do it once only)
														for( $i=0; $i<count($_SESSION['aColumns0'])-1; $i++){
															$query->bindParam(":attribute", $_SESSION['aColumns0'][$i], PDO::PARAM_STR, 128);			//		 -the param values.the type and legths will prevent against sql injection
															$query->execute();	
														}
														// will return T if success and F if not happen (eg. already exists in db)
													}
													catch(PDOException $e)
													{
														echo ("Error: " . $e->getMessage());
													}																						
												}//end default table updates		
									} //end check for buiding custom table
									else if ( $_SESSION['defaultColumns'] != null ){
										//-----------USER  IS RETURNING TO PAGE WITH A CUSTOM or DEFAULT REPORT ALREADY SELCTED---------
										
										$_SESSION['defaultColumns'] = null;
										$chosenFilters = $_SESSION['aColumns0'];
									}
									else if ( $_SESSION['aColumns0'] != null ){
										//-----------USER IS RETURNING TO PAGE WITH A CUSTOM or DEFAULT REPORT ALREADY SELCTED---------
										
										$_SESSION['defaultColumns'] = null;
										$chosenFilters = $_SESSION['aColumns0'];
									}


	//----------BUILD THE REPORT TABLE-----------
									//generates the custom report table's columns
									foreach ($chosenFilters as $item)
										//echo $item."<br>\n";
										echo '<th >'.$item.'</th>';
								?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="5" class="dataTables_empty">Loading data from server</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<?php
									//making custom table footers
										foreach ($chosenFilters as $item){
												//echo $item."<br>\n";
												echo '<th >'.$item.'</th>';
                                        }
								?>
							</tr>
						</tfoot>
					</table>
				</div>
            <?php
                if($_SESSION{'user_type_num'} == 2){
					
					// create buttons for defualt table, custom table, and download CSV file.
					echo '<table width="100%">';
						echo '<tr>'; 
							echo '<td align="center">'; 
								echo '<form method="post" action="report_server_side.php">
									<input type="submit" name="useDefaultTable" value="Default Report" align="center">
									</form>';
							echo '</td>';
							echo '<td align="center">'; 
								echo '<form method="post" action="download_csv.php">
									<input type="submit" value="Download the CSV File" align="center">
									</form>';
							echo '</td>';
							echo '<td align="center">'; 
								echo '<form method="post" action="report_config.php">
									<input type="submit" value="Customize Report" align="center">
									</form>';
							echo '</td>';
						echo '</tr>';
					echo '</table>';

                }
            ?>           
                <div class="spacer"></div>
                <div id="visualization" style="width: 600px; height: 400px;"></div>  
		
                <div id="dt_example">
                    <div id="container">
                        
                        <div id="dynamic">
                            
                    </div>
                </div>
			</div>
		</div>
	</body>
</html>
