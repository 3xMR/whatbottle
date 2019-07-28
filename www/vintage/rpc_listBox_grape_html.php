<?php
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");



//get grape details from session
if (isset($_SESSION['var_vintage_temp'])){
    
    //get grape details from session and create string of selected ids
    $var_grapes = $_SESSION['var_vintage_temp']['var_grapes'];
    
    foreach($var_grapes as $key => $grape){
        $grape_id = $grape['grape_id'];
        $var_selected_grapes[] = $grape_id;       
    }
    
    if($var_selected_grapes){
        $str_selected_grapes = implode(",", $var_selected_grapes) ?: 0; //set to zero if empty
    }else{
        $str_selected_grapes = 0;
    }
    
}else{
    echo "No vintage details found in SESSION";
}


//get grape rst from db - order so that selected grapes appear at top of list
$obj = new grape();
$where = null;
$columns = " grape_id, grape, colour, IF(grape_id IN ($str_selected_grapes),'true','false') as selected ";
$group = null;
$sort = " selected DESC, colour, grape ASC ";
$rst = $obj->get($where, $columns, $group, $sort);

if(!$rst){
    $db_error = $obj->get_sql_error();
    echo $db_error;
}


foreach($rst as $row){
    
    $checked = "";
    $disabled = "disabled=\"true\"";
    $percent = "";
    $index_1 = $row['grape_id'];

    //populate current grape details
    if(isset($var_grapes)){
        foreach($var_grapes as $vin_grape){
            if ($vin_grape['grape_id']==$index_1){
                $checked = "checked=\"yes\"";
                $disabled = "";
                $percent = $vin_grape['percent'];
            }
        }
    }
    
    $index = $row['grape_id'];
    $value = $row['grape'];
    $colour = $row['colour'];
    
    if($colour=='white'){
        $colour = "lightyellow";
    }

    //Level 1 item
    echo "<div class=\"listBox_row vertical-centre click\" id=\"$index\" style=\"height:24px;\" >";
        //hidden input allows jquery to recover value
        echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value\" >";

        echo "<div style=\"float:left; width:20px; height:100%; text-align:left; border:solid 1px lightgray; background-color:$colour;\">";
            echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$colour\" >";
        echo "</div>";
        
        echo "<div style=\"float:left; width:auto; margin-left:5px;\" >";
            echo "<p>$value</p>";
        echo "</div>";
        
        echo "<div style=\"float:right; margin-right:5px;\" >";
            echo "<input type=\"number\" step=\"0\" max=\"100\" style=\"width:50px; text-align:right; border:1px solid #D0D0D0; padding:3px; font-size:16px;\" min=\"1\" max=\"100\" maxlength=\"5\" value=\"$percent\" class=\"checkbox_percent\"  $disabled id=\"$index_1\" name=\"$index_1\" <input\>";
        echo "</div>";
        
        echo "<div style=\"float:right; margin-right:20px;\" >";
            echo "<input $checked type=\"checkbox\" class=\"checkbox_grape\" id=\"grape_$index_1\" name=\"grape_id_$index_1\" value=\"$index_1\">";
        echo "</div>";
        
        echo "<div class=\"clear\"></div>";
        
    echo "</div>";


} //foreach


?>
