<?php

/*
 * Check for duplicate year entry
 *
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

$wine_id = $_REQUEST['wine'];
$year = $_REQUEST['year'];


if($wine_id>0 && $year>0){
    //check vintage hasn't already been added
    $obj_vintage =  new vintage();
    $where = "wine_id = $wine_id AND year = $year";
    $var_vintage = $obj_vintage -> get($where);
    if($var_vintage){
        $found_vintage_id = $var_vintage[0]['vintage_id'];
        if($_SESSION['var_vintage_temp']['vintage_id']==$found_vintage_id){
            //is update on same vintage - OK
            $response = true;
        } else {
            $response = false;
        }
    } else {
        $response = true;
    }
    
} else {
    $response = false;
}

echo json_encode($response);


?>

