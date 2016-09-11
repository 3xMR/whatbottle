<?php

/*
 * Check for duplicate grape
 *
 */
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


$grape_id = $_REQUEST['grape_id'];
$grape_name = $_REQUEST['grape_name'];
$grape_colour = $_REQUEST['grape_colour'];

if($grape_name){
    
    if($grape_id > 0){
        $sql_id = " AND grape_id <> $grape_id ";
    } else {
        $sql_id = null;
    }
    
    $obj = new grape();
    $where = sprintf("grape = '%s' AND colour = '$grape_colour' $sql_id ",mysql_real_escape_string($grape_name));
    $count = $obj -> row_count($where);

    if($count > 0){
        //duplicate
        $response = false;
    } else {
        //unique
        $response = true;
    } 
    
} else {
    $response = false;
}


//encode result
echo json_encode($response);


?>