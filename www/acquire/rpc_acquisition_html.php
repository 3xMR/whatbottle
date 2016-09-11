<?php
/* 
 * Called by Load method on basket panel

 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


//disable fields if not authed
if(is_authed()){
     $disabled = null;
}else{
    $disabled = 'disabled';
}
    
if(!empty($_SESSION['var_acquire']['var_acquire_vintages'])){
    //vintages exist present in table html
    $var_vintages = $_SESSION['var_acquire']['var_acquire_vintages'];
    //start table
    echo "<table class=\"acquisition\" id=\"table_acquisition\">";
        //echo "<colgroup></colgroup>";
        //echo "<colgroup style=\"background-color:lightblue; text-align:center;\"></colgroup>";
        //echo "<colgroup span=\"5\" style=\"background-color:pink; text-align:right;\" ></colgroup>";
        //echo "<colgroup style=\"background-color:lightgreen; width:45px;\" ></colgroup>";

        echo "<thead>";
            echo "<tr id=\"heading_row\" >";
                echo "<th>Vintage</th>";
                echo "<th>Qty</th>";
                echo "<th>Price</th>";
                echo "<th>Disc. %</th>";
                echo "<th>Disc. £</th>";
                echo "<th>Paid</th>";
                echo "<th>Total</th>";
                if(is_authed()){
                    echo "<th style=\"width:30px;\"></th>";
                }    
            echo "</tr>";
        echo "</thead>";
        
        //footer
         echo "<tfoot>";
            echo "<tr id=\"total_row\">";
                echo "<td id=\"vintage_cell\" style=\"width:350px;\" >";
                    echo "&nbsp;";
                echo "</td>";
                echo "<td id=\"qty_total_cell\" >";
                    echo "<input type=\"number\" step=\"0\" class=\"table_input readonly\" style=\"width:35px; text-align:center; border:none;\" name=\"qty_total\" id=\"qty_total\" READONLY $disabled/>";
                echo "</td>";
                echo "<td id=\"full_price_total_cell\" >";
                    echo "&nbsp;";
                echo "</td>";
                echo "<td id=\"discount_percent_cell\" >";
                    echo "&nbsp;";
                echo "</td>";
                echo "<td id=\"discount_cell\" >";
                    echo "&nbsp;";
                echo "</td>";
                echo "<td id=\"price_paid_cell\" >";
                    echo "&nbsp;";
                echo "</td>";
                echo "<td id=\"total_price_paid_cell\" >";
                    echo "<input type=\"number\" step=\"0\" class=\"table_input readonly\" name=\"total_price_paid_total\" id=\"total_price_paid_total\" READONLY $disabled/>";
                echo "</td>";
                if(is_authed()){
                    echo "<td id=\"remove_cell\" >";
                        echo "&nbsp;";
                    echo "</td>";
                }
                
            echo "</tr>";
        echo "</tfoot>";
        
     echo "<tbody>";
    //load vintages as rows
    foreach($var_vintages as $vintage_has_acquire_id => $vintage){
        if($vintage['db_action']<>'delete'){
            //get values for form
            $id = $vintage_has_acquire_id;
            $vintage_id = $vintage['vintage_id'];
            $vintage_label = $vintage['vintage_label'];
            $qty = $vintage['qty'];
            if($qty > 1){  } else { $qty=1; };
            $unit_price = $vintage['unit_price'];
            if($unit_price > 0){ }else{$unit_price = '0';}
            $discounted_price = $vintage['discounted_price'];
            if($discounted_price > 0){ }else{$discounted_price = '0';}
            $discount_percentage = $vintage['discount_percentage'];
            if($discount_percentage > 0){ }else{$discount_percentage  = '0';}
            $db_action = $vintage['db_action'];
            
            //set colour for new rows
            if($db_action=='insert'){
                $colour = "whitesmoke";
            }else{
                $colour = "white";
            }
            
            echo "<tr class=\"row\" style=\"background-color:$colour;\" id=$id >";
                echo "<td style=\"text-align:left; padding-left:5px; padding-right:5px;\" id=\"vintage_label_$id\" >";
                    echo "<span class=\"vintage_click click\">$vintage_label</span>";
                    echo "<input type=\"hidden\" name=\"vintage_id\" id=\"vintage_id_$id\" value=\"$vintage_id\" />";
                    echo "<input type=\"hidden\" name=\"link_index\" class=\"link_index\" value=\"$vintage_id\" />";
                    echo "<input type=\"hidden\" name=\"db_action\" id=\"db_action_$id\" value=\"$db_action\" />";
                    echo "<input type=\"hidden\" name=\"vintage_has_acquire_id\" id=\"vintage_has_acquire_id_$id\" value=\"$id\" />";
                echo "</td>";
                echo "<td id=\"qty_cell\" >";
                    echo "<input type=\"number\" step=\"0\" class=\"qty table_input\" style=\"width:35px; text-align:center; border:none;\" name=\"$id:_qty\" id=\"qty_$id\" value=\"$qty\" $disabled/>";
                echo "</td>";
                echo "<td id=\"full_price_cell\" >";
                    echo "<input type=\"number\" step=\"0\" class=\"full_price table_input\" name=\"$id:_full_price\" id=\"full_price_$id\" value=\"$unit_price\" $disabled/>";
                echo "</td>";
                echo "<td id=\"discount_percent_cell\" >";
                    echo "<input type=\"number\" step=\"0\" class=\"discount_percent table_input\" name=\"$id:_discount_percent\" id=\"discount_percent_$id\" value=\"$discount_percentage\" $disabled/>";
                echo "</td>";
                echo "<td id=\"discount_cell\" >";
                    echo "<input type=\"number\" step=\"0\" class=\"discount table_input\" name=\"$id:_discount\" id=\"discount_$id\" value=\"0\" $disabled/>";
                echo "</td>";
                echo "<td id=\"price_paid_cell\" >";
                    echo "<input type=\"number\" step=\"0\" class=\"price_paid table_input\" name=\"$id:_price_paid\" id=\"price_paid_$id\" value=\"0.00\" tabindex=\"-1\" READONLY $disabled/>";
                echo "</td>";
                echo "<td id=\"total_price_paid_cell\" >";
                    echo "<input  type=\"number\" step=\"0\" class=\"total_price_paid table_input readonly\" name=\"$id:_total_price_paid\" id=\"total_price_paid_$id\" value=\"0.00\" tabindex=\"-1\" READONLY $disabled/>";
                echo "</td>";
                if(is_authed()){
                    //only show delete button if authed
                    echo "<td style=\"text-align:center; vertical-align:middle;\" class=\"\" id=\"remove_cell\" >";
                        echo "<img class=\"btn_remove_row click\" id=\"remove_$id\" src=\"/images/more_grey_flat_24.png\" height=\"18px\" width=\"18px\"/>";
                    echo "</td>";
                }
           echo "</tr>";
        } //end if <> delete
    } //end foreach

        echo "</tbody>";
    echo "</table>";

} else {
    //var_vintages does not exist
    echo "</br>";
    echo "<p>No Vintages here yet..</p>";
    //echo "<hr>";
}

        
?>
