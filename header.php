<?php
session_start();
if($_SESSION['user_type_num'] == 2){
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
    <ul class="menu"><li><a href="survey.php">Survey</a></li>
    <li><a href="survey_offline_static.html">Offline Survey</a></li>
    <li><a href="report_server_side.php">Reports</a></li>
    <li></li><li><a href="users.php">Users</a></li>
    <li><a href="admin_options.php">Admin</a></li>
    <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
    <li><a href="#"><i>Help</i></a></li>
    <li><a href="javascript:toggle_feedback_form()"><i>Feedback?</i></a></li>
    </ul>	
    </div>
            
</div>
<hr />
<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
}
else{
    
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
    <ul class="menu"><li><a href="survey.php">Survey</a></li>
    <li><a href="survey_offline_static.html">Offline Survey</a></li>
    <li><a href="report_server_side.php">Reports</a></li>
    <!--<li></li><li><a href="users.php">Users</a></li>-->
    <!--no admin option for user-->
    <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li><li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
    <li><a href="#"><i>Help</i></a></li>
    <li><a href="javascript:toggle_feedback_form()"><i>Feedback?</i></a></li>
    </ul>	
    </div>
            
</div>
<hr />
<div id="BlankLine" style ="max-height:20px;height:20px;min-height:20px;"></div>';
}
?>
