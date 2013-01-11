<?php
session_start();
echo '<div id="page">
<div id="header" style="height:100px;">		
    <div style="width:60%;position:absolute;top:-25px;">
    <h1><a href="#">Homelessness Vulnerability<br/>Tracking System</a></h1>
    <p class="description" style="font-size: 0.8em;">A Cooperation between Georgia Tech and the United Way of Metropolitan Atlanta</p>
    </div>
    <div style="position:absolute;width:40%;top:0px;left:60%"> 
    <span class="logged_in_user"><B>Currently logged in as: '.$_SESSION['username'].'<Font Color="Yellow"><?php echo $_SESSION[\'LOGIN\']; ?></Font> | <a href="logout.php"><Font Color="Yellow">Signout</Font></a></B></span>	
    </div>
    <div>
    <br/>	
    </div>
            
</div>
<hr />
<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
?>
