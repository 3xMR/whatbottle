<?php
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

//display list of vintage for given wine_id - ignore vintage_id if provided
 
//wine_id from session
if($_SESSION['var_wine_temp']['wine_id']>0){
    //wine_id provided
    $wine_id = $_SESSION['var_wine_temp']['wine_id'];
}

$obj = new vintage();
$sort = "Year ASC";
$where = "tblVintage.wine_id = '$wine_id'";
$rst = $obj->get_extended($where, $columns=false, $group=false, $sort);

if($rst){
    foreach($rst as $row){

        $index = $row['vintage_id'];
        $value = $row['year'];
        
        $quality_width = ($row['vintage_quality']*8)."px";
        $value_width = ($row['vintage_value']*16)."px";

        echo "<div class=\"listBox_row click\" id=\"$index\" >";
            //hidden input allows jquery to recover value
            echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value\" >";

            echo "<div class=\"vertical-centre\" style=\"height:20px;\" >";
                echo "<p style=\"float:left;\">$value</p>";
                echo "<div class=\"quality-static-rating-small\" style=\"float:left; margin-left:10px; width:$quality_width;\" ></div>";
                echo "<div class=\"value-static-rating-small\" style=\"float:left; margin-left:5px; width:$value_width;\"></div>";
            echo "</div>";
 

        echo "</div>";

    }
} else {
    
    $sql_error = $obj ->get_sql_error();
    if($sql_error){
        echo $sql_error;
    }else{
        echo "<p style=\"margin-top:5px; margin-left:5px; font-size:inherit;\">empty<p>";
    }
    
}







?>
