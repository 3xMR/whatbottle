<?php

//Setup links based on login
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/classes/class.db.php");
require_once("$root/functions/function_user.php");

//links
$wine = "/index.php";
$acquire ="/acquire/index.php";
$storage ="/storage/index.php";
$ref ="/admin/index_admin.php";
$friends ="/friends/index.php";

if(is_authed()){
    $authed = true;
    $user_image = "/images/user_simple_grey.png";
}else{
    $authed = false;
    $user_image = "/images/user_simple_green.png";
}

//get basket count
//$basket_count = count($_SESSION['var_basket']);
$basket_count = isset($array['var_basket']) ? $array['var_basket'] : '';
if($basket_count > 0){
    $display = 'inline';
}else{
    $display = 'none';
}

if($environment == "dev"){
    $color = 'background-color:darkred;';
    $title = 'Test';
}else{
    $title = 'Whatbottle?';
    $color = 'background-color:#5292BE;';
}

//Display Nav Bar
echo "<div id=\"top_nav\" class=\"vertical-centre\" style=\"$color\" >";
    //burger menu
    echo "<img class=\"click btn_main_menu\" style=\"float:left; margin-left:15px;\" id=\"btn_main_menu\" src=\"/images/burger_menu_grey.png\" height=\"27px\" width=\"27px\" />";

    //title
    echo "<div class=\"click\" style=\"float:left; color:lightgray; font-size:1.25em; margin-left:25px;\" id=\"top_nav_nav\" >";
        echo "<p>$title</p>";
    echo "</div>"; //div_top_nav

    //right side menu buttons
    echo "<img style=\"float:right; margin-right:20px;\" class=\"click btn_login\" authed_data=\"$authed\" id=\"btn_login_top_nav\" src=\"$user_image\" height=\"24px\" width=\"24px\" />";
        echo "<div id='noti_container' style=\"float:right; margin-right:20px;\" >";
            echo "<img class=\"click btn_basket\" id=\"btn_basket_top_nav\" src=\"/images/basket_simple_grey.png\" height=\"24px\" width=\"24px\" />";
            echo "<div id=\"noti_bubble\" style=\"display:$display;\" >";
                echo "<p id=\"noti_bubble_count\" >$basket_count</p>";
            echo "</div>";
        echo "</div>";


    //clear
    echo "<div class=\"clear\" ></div>";

echo "</div>"; //top_nav
