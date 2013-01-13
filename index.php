<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Homeless Shelter Occupancy</title>
  
  <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
  <meta content="utf-8" http-equiv="encoding">
  
  <link href="style.css" rel="stylesheet" type="text/css" />
  <link href="screen.css" rel="stylesheet<br>" type="text/css" />
  <!--jQuery UI stuff-->
  <link href="css/overcast/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
  <script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
  <script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
  <script src="js/button.js" text="text/javascript"></script>
</head>
<body>
<div id="page">
	<div id="header"style="height:100px;">
		<h1><a href="/">Homeless Housing Occupancy Project</a></h1>
		<p class="description">A Cooperation between Georgia Tech and the United Way of Metropolitan Atlanta</p>
	</div>

	<hr />
<div class="content">

<div id="primary" style="width:70%;">
<p style="color: green"></p>
<B><Font Color="red"></Font></B>
<div id="BlankLine" style ="max-height:30px;height:30px;min-height:30px;"><br></div>
<form id="login" method="post" action="loginproc.php"> 

    <h1>Log in to <strong>Streets to Home 2.0</strong> </h1>
    <br/><br/>
    
    <div>
    	<label for="login_username"><b>Username</b></label> <br/>
    	<input type="text" name="username" id="login_username" class="field required" title="Please provide your username" />
    </div>			

    <div>
    	<label for="login_password"><b>Password</b></label> <br/>
    	<input type="password" name="password" id="login_password" class="field required" title="Password is required" />
    </div>			
    
    <!--<p class="forgot"><a href="#">Forgot your password?</a></p>-->
    			
    <div class="submit">
        <button type="submit">Log in</button>   
        
       <!-- <label>
        	<input type="checkbox" name="remember" id="login_remember" value="yes" />
            Remember my login on this computer
        </label>-->   
    </div>
    
  
</form>	

<br>
<br>

</div> <!-- #primary -->

<hr />

<div class="clear"></div>

</div> <!-- .content -->

</div> <!-- Close Page -->

<p id="footer">

</p>
<?php
//// Inialize session
session_start();
session_destroy();

?>

</div>

</body>

</html>
