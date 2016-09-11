<?php
/* 
 * Called by Load method on edit_awards.php

 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


//display added awards
$var_awards = $_SESSION['var_awards_temp'];
if(!empty($var_awards)){
    foreach($var_awards as $var_award){

        $award_name = $var_award['award'];
        $org = $var_award['award_org'];
        $award_id = $var_award['award_id'];

        echo "<div style=\"background-color:#3B3839; width:auto; height:35px; padding-left:10px; margin-bottom:10px; margin-right:10px;\" class=\"vertical-centre\" id=\"award_$award_id\" >";
            echo "<h3 style=\"float:left; font-weight:normal; color:white; \">$org - $award_name</h3>";
            echo "<img style=\"float:right; padding-right:10px; \" class=\"btn_remove click\" id=\"$award_id\" src=\"/images/delete_grey_flat_32.png\" height=\"18px\" width=\"18px\" \>";
        echo "</div>";

    }
}

?>
