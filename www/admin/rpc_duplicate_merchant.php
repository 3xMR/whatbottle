<?php

/*
 * Check for duplicate merchant
 *
 */
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

$merchant_name = $_REQUEST['merchant_name'];
$merchant_id = $_REQUEST['merchant_id'];

if($merchant_id > 0){ //include merchant_id in query if provided
    $sql_id = " AND merchant_id <> $merchant_id ";
} else {
    $sql_id = null;
}
$obj = new merchant();
$where = sprintf("merchant = '%s' $sql_id ",mysql_real_escape_string($merchant_name));
$count = $obj -> row_count($where);

if($count > 0){ //duplicate
    $response = false;
} else { //unique
    $response = true;
}

//encode result
echo json_encode($response);

?>

