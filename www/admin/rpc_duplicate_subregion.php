<?php

/*
 * Check for duplicate region
 *
 */
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

$subregion_name = $_REQUEST['subregion_name'];
$subregion_id = $_REQUEST['subregion_id'];
$region_id = $_REQUEST['region_id'];

if($subregion_id>0){
    $sql_id = " AND subregion_id <> $subregion_id ";
} else {
    $sql_id = null;
}

$obj = new subregion();
$where = sprintf("subregion = '%s' AND region_id = $region_id $sql_id  ",mysql_real_escape_string($subregion_name));
$count = $obj -> row_count($where);

if($count > 0){ //duplicate
   $response = false;
} else { //unique
    $response = true;
}

//encode result
echo json_encode($response);

?>