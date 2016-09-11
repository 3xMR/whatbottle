<?php
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


        
//get list of producers
$obj = new producer();

$where = null;
$columns = null;
$group = null;
$sort = "producer ASC";
$rst = $obj->get($where, $columns, $group, $sort);


foreach($rst as $row){

    $index = $row['producer_id'];
    $value = $row['producer'];


    echo "<div class=\"listBox_row click\" id=\"$index\" >";
        //hidden input allows jquery to recover value
        echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value\" >";

        echo "<table class=\"listbox_table\">";
            echo "<tr>";
                echo "<td>";
                    echo "<p>$value</p>";
                echo "</td>";
            echo "</tr>";
        echo "</table>";

    echo "</div>";

}





?>
