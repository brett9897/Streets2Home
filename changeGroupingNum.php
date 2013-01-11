<?php
session_start();
include ('dbconfig.php');

//-------------------PDO SQL OPTIMIZATION	
	function incremetGroupingNum($groupingNum, $minGroupingNum, $maxGroupingNum){
		//need to go past $maxGroupingNum to know when the survey is over and display completed screen instead. (do NOT want opposite for decrementGroupingNum)
		//if($groupingNum < $maxGroupingNum){
            $groupingNum += 1;
        //}
		//echo 'groupingNum: ' . $groupingNum . '<br/>';
		
		//----WILL USE PDO FOR OPTIMIZATION-------
		try {
			$dbh = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "SELECT * FROM form_questions WHERE is_used = 1 AND grouping_id =:grouping_num";
			
			$query = $dbh->prepare($sql);											//compile the sql stuff  (do it once only)
			//for( $i=$_SESSION['grouping_num']; $i <= $_SESSION['maxGroupingNum']; $i++){
			$query->bindParam(":grouping_num", $groupingNum, PDO::PARAM_INT,4);
			$query->execute();
			//$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$res = $query->fetchAll();
			while( count($res)==0    &&   $groupingNum <= $maxGroupingNum ) {
				$groupingNum += 1;
				$query->bindParam(":grouping_num", $groupingNum, PDO::PARAM_INT,4);			//		 -the param values.the type and legths will prevent against sql injection
				$query->execute();	
				//$res = query->fetchAll(PDO::FETCH_ASSOC);
				$res = $query->fetchAll();
			}
			/*			
            if(count($res)==0 && $groupingNum >= $minGroupingNum) {  //--needed incase starting survey not at first group...prevents the groupingNum from going to a groupNum value with no questions used
               $groupingNum = decrementGroupingNum($groupingNum, $minGroupingNum, $maxGroupingNum);
            }
           * */
			// will return T if success and F if not happen (eg. already exists in db)
		}
		catch(PDOException $e)
		{
			echo ("Error: " . $e->getMessage());
		}
		//echo 'groupingNum: ' .  $groupingNum . '<br/>';
		return $groupingNum;
	}
	
	
	function decrementGroupingNum($groupingNum, $minGroupingNum, $maxGroupingNum){
        if($groupingNum > $minGroupingNum){
            $groupingNum -= 1;
        }
		//echo 'groupingNum: ' . $groupingNum . '<br/>';
		
		//----WILL USE PDO FOR OPTIMIZATION-------
		try {
			$dbh = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "SELECT * FROM form_questions WHERE is_used = 1 AND grouping_id =:grouping_num";
			
			$query = $dbh->prepare($sql);											//compile the sql stuff  (do it once only)
			//for( $i=$_SESSION['grouping_num']; $i <= $_SESSION['maxGroupingNum']; $i++){
			$query->bindParam(":grouping_num", $groupingNum, PDO::PARAM_INT,4);
			$query->execute();
			//$res = $query->fetchAll(PDO::FETCH_ASSOC);
			$res = $query->fetchAll();
			while( count($res)==0    &&   $groupingNum >= $minGroupingNum ) {
				$groupingNum -= 1;
				$query->bindParam(":grouping_num", $groupingNum, PDO::PARAM_INT,4);			//		 -the param values.the type and legths will prevent against sql injection
				$query->execute();	
				//$res = query->fetchAll(PDO::FETCH_ASSOC);
				$res = $query->fetchAll();
			}
            if(count($res)==0 && $groupingNum <= $minGroupingNum) {  //--needed incase starting survey not at first group...prevents the groupingNum from going to a groupNum value with no questions used
               $groupingNum = incremetGroupingNum($groupingNum, $minGroupingNum, $maxGroupingNum);
            }
			// will return T if success and F if not happen (eg. already exists in db)
		}
		catch(PDOException $e)
		{
			echo ("Error: " . $e->getMessage());
		}
		return $groupingNum;
	}





?>

