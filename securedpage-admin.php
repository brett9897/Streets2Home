<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

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
	</head>
	<body onload="javascript:setOffsets()">
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

