<?php
    if( isset($_POST['consent']) )
    {
      header("Location: survey.php");
    }
    else
    {
?>


 <!DOCTYPE html>
    <html>
			<head>
			  <title>Homeless Shelter Occupancy</title>
			  <link href="style.css" rel="stylesheet" type="text/css" />
			  <link href="screen.css" rel="stylesheet" type="text/css" />
              <!--jQuery UI stuff-->
              <link href="css/overcast/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
              <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
              <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
              <script src="js/button.js" text="text/javascript"></script>
              <script src="js/survey/survey.js" text="text/javascript"></script>
			</head>
		  <body>	
<?php

include ('header.php');

?>
  <div>
    <h2>Consent for Interview</h2>
    <form action="consent.php" method="POST">
      <div class="question">Do you consent to being interviewed?</div>
      <div class="response"><input type="checkbox" name="consent" value="yes" required/></div>
      <div class="submit_buttons"><input type="submit" value="Continue" /></div>
    </form>
  </div>
</div>
</body>
</html>
<?php
    }
?>