<?php

/*
 * Check for duplicate producer
 *
 */
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

$producer = $_REQUEST['producer_name'];
$producer_id = $_REQUEST['producer_id'];


if($producer_id>0){
    $sql_id = " AND producer_id <> $producer_id ";
} else {
    $sql_id = null;
}

$obj = new producer();
$where = sprintf("producer = '%s' $sql_id ",mysql_real_escape_string($producer));
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