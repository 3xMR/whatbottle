<?php
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

//display list of acquisitions for a given vintage_id
 
//vintage_id from session
if($_SESSION['var_vintage_temp']['vintage_id']>0){
    $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];
}

$obj = new vintage_has_acquire();
$sort = "tblAcquire.acquire_date ASC";
$where = "vintage_id = '$vintage_id'";
$rst = $obj->get_extended($where, $columns=false, $group=false, $sort);

if($rst){
    foreach($rst as $row){

        $index = $row['acquire_id'];
        $date = date_us_to_uk($row['acquire_date'],'d-M-Y');
        $merchant = $row['merchant'];
        $price = $row['unit_price'];
        $disc_price = $row['discounted_price'];
        $qty = $row['qty'];
        
        echo "<div class=\"listBox_row click\" id=\"$index\" >";
            
            echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value\" >"; //hidden input allows jquery to recover value
                
            echo "<div style=\"float:left; width:260px; color:#606060; \" >";
                echo "<p>$merchant</p>";
            echo "</div>";

            echo "<div style=\"float:right; text-align:right; width:120px;\" >";
                echo "<p>$date</p>";
            echo "</div>";

            echo "<div class=\"clear\" ></div>";

            echo "<div style=\"margin-top:7px; padding-left:10px; padding-right:5px;  \">"; //second row

                echo "<div style=\"float:left; text-align:left; width:auto; margin-right:5px; \" >";
                    echo "<p style=\"font-size:80%; color:#606060;\" >Qty:</p>";
                echo "</div>";
                echo "<div style=\"float:left; text-align:left; width:30px;\" >";
                    echo "<p style=\"font-size:80%;\" >$qty</p>";
                echo "</div>";

                echo "<div style=\"float:left; text-align:left; width:auto; margin-right:5px; \" >";
                    echo "<p style=\"font-size:80%; color:#606060;\" >Full Price:</p>";
                echo "</div>";
                echo "<div style=\"float:left; text-align:left; width:65px;\" >";
                    echo "<p style=\"font-size:80%;\" >£ $price</p>";
                echo "</div>";

                echo "<div style=\"float:left; text-align:left; width:auto; margin-right:5px; \" >";
                    echo "<p style=\"font-size:80%; color:#606060;\" >Price Paid:</p>";
                echo "</div>";                    
                echo "<div style=\"float:left; text-align:left; width:65px; \" >";
                    echo "<p style=\"font-size:80%;\" >£ $disc_price</p>";
                echo "</div>";

            echo "</div>"; 

            echo "<div class=\"clear\" ></div>";
                
        echo "</div>";

    }
} else {
    
    $sql_error = $obj ->get_sql_error();
    if($sql_error){
        echo $sql_error;
    }else{
        echo "<p style=\"margin-top:5px; margin-left:5px; font-size:inherit;\">None<p>";
    }
    
}