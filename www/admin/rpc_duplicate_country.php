<?php

/*
 * Check for duplicate country
 *
 */
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

$country_name = $_REQUEST['country_text'];
$country_id = $_REQUEST['country_id'];

$obj = new country();
if($country_id>0){
    $sql_id = " AND country_id <> $country_id ";
} else {
    $sql_id = null;
}

$where = sprintf("country = '%s' $sql_id ",mysql_real_escape_string($country_name));
$count = $obj -> row_count($where);

if($count > 0){
    //duplicate
    $response = false;
} else {
    //unique
    $response = true;
}

//encode result
echo json_encode($response);

?>

