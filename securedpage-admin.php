<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

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
	</head>
	<body>
<?php
include('header.php');
// Check, if username session is NOT set then this page will jump to login page
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
}
?>
		<div id="wrapper" align="center">
			<ul> Welcome! Please select Survey to begin.</ul>
		</div>
	</body>
</html>

