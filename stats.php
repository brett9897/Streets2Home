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


		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		
		<script type="text/javascript" language="javascript" src="jquery.js"></script>
		<script type="text/javascript" language="javascript" src="jquery.dataTables.js"></script>
		<script type="text/javascript" language="javascript" src="shCore.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#example').dataTable( {
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
	
	<body onload="javascript:setOffsets()">
 	
    <?php include('header.php');?>
    
    <div id="visualization" style="width: 600px; height: 400px;"></div>
		
        
		
		<div id="dt_example">
			<div id="container">
				
				<div id="dynamic">
					
			</div>
		</div>
	</body>
</html>






