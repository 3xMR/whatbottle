<?php
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

//grape listBox contents
$obj = new grape();

$where = null;
$columns = null;
$group = null;
$sort = "colour, grape ASC";
$rst = $obj->get($where, $columns, $group, $sort);


foreach($rst as $row){

    $index = $row['grape_id'];
    $value = $row['grape'];
    $colour = $row['colour'];
    
    if($colour=='white'){
        $colour = "lightyellow";
    }

    //Level 1 item
    echo "<div class=\"listBox_row click\" id=\"$index\" >";
        
        echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value\" >"; //hidden input allows jquery to recover value

        //colour
        echo "<div style=\"float:left; width:7px; margin-right:10px; height:16px; text-align:left; border:solid 1px lightgray; background-color:$colour;\" >";
            echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$colour\" >";
        echo "</div>";  
        
        //name
        //echo "<div style=\"float:left; width:auto; background-color:pink; text-align:left; margin-left:5px;\" >";
            echo "<p>$value</p>";
        //echo "</div>";
        
        echo "<div class=\"clear\"></div>";

            
    echo "</div>";


} //foreach


?>
