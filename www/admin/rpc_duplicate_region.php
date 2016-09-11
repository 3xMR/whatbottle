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

$region_name = $_REQUEST['region_name'];
$region_id = $_REQUEST['region_id'];
$country_id = $_REQUEST['country_id'];

if($region_id>0){
    $sql_id = " AND region_id <> $region_id ";
} else {
    $sql_id = null;
}

$obj = new region();
$where = sprintf("region = '%s' AND country_id = $country_id $sql_id  ",mysql_real_escape_string($region_name));
$count = $obj -> row_count($where);

if($count > 0){ //duplicate
    $response = false;
} else {
    //unique
    $response = true;
}

//encode result
echo json_encode($response);

?>