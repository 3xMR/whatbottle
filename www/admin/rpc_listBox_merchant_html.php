<?php

header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


//get list of level_1 items
$obj_1 = new merchant();
$where = null;
$columns = null;
$group = null;
$sort = "merchant ASC";
$rst_1 = $obj_1->get($where, $columns, $group, $sort);


foreach($rst_1 as $key => $row){

    $index_1 = $row['merchant_id'];
    $value_1 = $row['merchant'];


    //Level 1 item
    echo "<div class=\"listBox_row click\" id=\"$index_1\" >";
        echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value_1\" >"; //hidden input allows jquery to recover value
        echo "<p>$value_1</p>";
    echo "</div>";

} //foreach

?>