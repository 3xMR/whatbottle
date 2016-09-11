<?php
/* 
 * Called by Load method on basket panel

 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


echo "<div id=\"login_form\">";
    echo "<div style=\"color:gray; margin-bottom:10px; \">";
        if(is_authed()){
            echo "<span>".$_SESSION['firstname']." ". $_SESSION['lastname']."</span>";
        } 
    echo "</div>";
    echo "<div style=\"\" >";
        echo "<span>email</span>";
    echo "</div>";
    echo "<div style=\"padding-top:5px;\" >";
        echo "<input type=\"text\" size=\"26\" value=\"\" name=\"username\" id=\"username\" />";
    echo "<div>";
    
    echo "<div style=\"padding-top:10px\" >";
        echo "<span>password</span>";
    echo "</div>";
    echo "<div style=\"padding-top:5px;\" >";
        echo "<input type=\"password\" size=\"26\" name=\"password\" id=\"password\" />";
    echo "<div>";
    
    //echo "<div class=\"clear\" ></div>";
    
    echo "<div style=\"float:left; clear:left; padding-top:10px;\" >";


        
     
        echo "</hr>";
        echo "<input type=\"button\" name=\"btn_login\" id=\"btn_login\" value=\"Login\" />";
        echo "<input type=\"checkbox\" name=\"remember\" id=\"remember\" value=\"remember me\" />";
        echo "<label for=\"remember\">remember me</label> ";

        if(is_authed() ){
            echo "<h4 class=\"inline\"><a href=\"/user/login.php?logout=yes\">logout</a></h4>";
        }
     echo "</div>";
    
    echo "<div class=\"clear\" ></div>";
    echo "<div style=\"width:190px; margin-top:10px; font-size:10pt; padding:10px; background-color:red; color:black; display:none; \" id=\"login_msg\">";
        echo "<span id=\"login_msg_txt\">Incorrect username or password</span>";
    echo "</div>";
 echo "</div>";
 
 ?>