<?php
/* 
 * Include to show recent acquisitions
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


if($_SESSION['var_wine_search_criteria']['merchant_id']>0){
    //filter acquisitions by search criteria
    $where = "tblAcquire.merchant_id = ".$_SESSION['var_wine_search_criteria']['merchant_id'];
} else {
    $where = false;
}

$acquire_obj = new acquire;
$sort = "acquire_date DESC";
$var_acquires = $acquire_obj -> get_extended($where,false,false,$sort);

if(is_array($var_acquires)){
    foreach($var_acquires as $acquire){
        $id = $acquire['acquire_id'];
        $acquire_date = date_us_to_uk($acquire['acquire_date']);
        $acquire_merchant = $acquire['merchant'];

        echo "<div class=\"listBox_row click\" id=\"$id\" >";
            echo "<div class=\"open_acquisition\" id=\"open_acquisition_$id\" style=\"float:left; width:auto;\">";
                echo "<div style=\"float:left; width:190px; \" >";
                    echo "<p>$acquire_merchant</p>";
                echo "</div>";
                
                echo "<div style=\"float:left; text-align:right; width:90px; \" >";
                    echo "<p>$acquire_date</p>";
                echo "</div>";
            echo "</div>";
            
            echo "<div style=\"float:right; width: 40px;\">";
                 echo "<div style=\"\" >";
                    echo "<img style=\"display:block; margin-left:12.5px; margin-top:4px; \" src=\"/images/filter_flat_grey_24.png\" height=\"15px\" width=\"15px\" id=\"acquire_filter_$id\" class=\"click acquire_filter\" data-acquire_id=\"$id\" />";
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"clear\" ></div>";
        echo "</div>";
        
    }
}

