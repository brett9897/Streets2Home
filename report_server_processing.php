<?php
    include('dbconfig.php');
	session_start();
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	
		if( isSet($_SESSION['aColumns0']) ){					//this was set inside the client side php file for the main view.		
			$aColumns = $_SESSION['aColumns0'];
		} else if( isSet($_SESSION['defaultColumns']) ){
			//default table columns
			//$aColumns = array( 'first_name', 'last_name', 'date_of_birth', 'VI', 'details_link' );
			$aColumns = $_SESSION['defaultColumns'];
			/*
				//------retrieve default columns from database and store them--------
				
				//-------------------Connect To Database-------------------
				$link   =   mysql_connect(localhost, USERNAME, PASSWORD) or die ('Could not connect :'.  mysql_error());
				mysql_select_db(DB_NAME) or die( "Unable to select database");
				
				//-------------------Get the tip and show it in the this page from database-------------------
				$sql1 = 'SELECT client_attributes FROM default_report_table_columns';
				$result1 = mysql_query($sql1) or die ( 'Query3 failed: ' . mysql_error() );

				
				while( $row1 = mysql_fetch_array($result1, MYSQLI_ASSOC) ){
					$aColumns[] = $row1{'client_attributes'};
				}
				$chosenFilters[] = "details_link";
				$_SESSION['defaultColumns'] = $chosenFilters;
				
				mysql_free_result($result1);
				mysql_close($link);						
				* 
				* */
		}
		/*
		 * //THIS WILL CAUSE THE 'JSON FORMATTING ERROR' if turned on...use only for debuging this specific page script
		echo '-------------<br/>';
		foreach($aColumns as $item){
			echo $item."<br>\n";
		}
		echo '-------------<br/>';
		*/
		
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "client_id";
	
	/* DB table to use */
	$sTable = "client";
	
	/* Database connection information */
	$gaSql['user']       = USERNAME;
	$gaSql['password']   = PASSWORD;
	$gaSql['db']         = DB_NAME;
	$gaSql['server']     = HOST;

	
	
	/* REMOVE THIS LINE (it just includes my SQL connection user/pass) */
	//include( $_SERVER['DOCUMENT_ROOT']."/datatables/mysql.php" );
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * Local functions
	 */
	function fatal_error ( $sErrorMessage = '' )
	{
		header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
		die( $sErrorMessage );
	}

	
	/* 
	 * MySQL connection
	 */
	if ( ! $gaSql['link'] = mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
	{
		fatal_error( 'Could not open connection to server' );
	}

	if ( ! mysql_select_db( $gaSql['db'], $gaSql['link'] ) )
	{
		fatal_error( 'Could not select database ' );
	}

	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
			intval( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
				 	mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
			{
				$sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
			}
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	$logfile = "mylog.txt";     //open this file for debugging only
    $fp = fopen($logfile, "w");
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
		";
    fwrite($fp, "sql: $sQuery\naColumns: ".str_replace(" , ", " ", implode(", ", $aColumns))."\n");
    $csv_output = str_replace(" , ", " ", implode(", ", $aColumns))."\n";
    fclose($fp);
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "version" )
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
                $csv_output .= $aRow[ $aColumns[$i] ]=="0" ? '-' : $aRow[ $aColumns[$i] ].", ";
			}
			//else if ( $aColumns[$i] != 'details_link')
			//{
			//	/*link output*/
			//	$row[] = $aRow[ $aColumns[$i] ];
			//	// echo '<a href="'.$row['link'].">... 
			//}
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
                $csv_output .= $aRow[ $aColumns[$i] ].", ";
			}
		}
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>
