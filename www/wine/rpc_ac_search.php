<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/classes/class.wine_search.php");


$q = strtolower($_REQUEST['q']);

if (!$q) {
    return;
}

$name = mysql_escape_string(trim($q));

//get list of wines
$search_obj = new wine_search();
$type = "wines";
$group = "tblWine.wine_id";
$order = "tblWine.wine ASC";

$results = $search_obj -> search($type, $name, $wine_id=false, $winetype_id=false, $country_id=false, $region_id=false, $subregion_id=false,
                    $producer_id=false, $merchant_id=false, $acquire_id=false, $group, $order, $limit=false);

if(sizeof($results)>0){
    
    foreach($results as $key => $row){
        // While there are results loop through them
        
        $value = stripslashes($row['wine']." (".$row['producer'].")");
        $key  = $row['wine_id'];
        $category = 'wine';
    
        echo "$value|$key|$category\n";

    }

}

?>