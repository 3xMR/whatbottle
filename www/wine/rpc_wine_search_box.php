<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
//require_once("$root/functions/function.php");


require_once("$root/classes/class.wine_search.php");
require_once("$root/classes/class.db.php");
require_once("$root/classes/class.region.php");


$q = strtolower($_REQUEST['term']);

if (!$q) {
    return false;
}

$var_result = array();

$term = mysql_escape_string(trim($q));

//get list of wines
$search_obj = new wine_search();
$type = "wines";
$group = "tblWine.wine_id";
$order = "tblWine.wine ASC";

$results = $search_obj -> search($type, $term, $wine_id=false, $winetype_id=false, $country_id=false, $region_id=false, $subregion_id=false,
                    $producer_id=false, $merchant_id=false, $acquire_id=false, $group, $order, $limit=false);



if(sizeof($results)>0){
    
    foreach($results as $key => $row){
        // While there are results loop through them
        
        $value = stripslashes($row['wine']." (".$row['producer'].")");
        $key  = $row['wine_id'];
        $category = 'wine';
    
        //echo "$value|$key|$category\n";
        $var_row['label'] = $value;
        $var_row['value'] = $key;
        $var_row['category'] = 'Wine';
        
        array_push($var_result, $var_row);

    }
    
}


//get countries
$obj_country = new country();
$where = "country LIKE '%$term%' ";
$var_country = $obj_country -> get($where);

if($var_country){
     foreach($var_country as $row){
    
        $var_row['label'] = $row['country'];
        $var_row['value'] =  $row['country_id'];
        $var_row['category'] = 'Country';
        
        array_push($var_result, $var_row);

    }
    
}



//get regions

$obj_region = new region();
$where = "region LIKE '%$term%' ";
//$where = " region_id = 1 ";
$var_region = $obj_region ->get_extended($where);

if($var_region){
     foreach($var_region as $row){

        $var_row['label'] = $row['region'].", ".$row['country'];
        $var_row['value'] =  $row['region_id'];
        $var_row['category'] = 'Region';
        
        array_push($var_result, $var_row);

    }
}



//get subregions
$column = $group = $sort = $limit = null;
$obj_subregion = new subregion();
$where = "subregion LIKE '%$term%' ";
$var_subregion = $obj_subregion -> get_extended($where);

if($var_subregion){
     foreach($var_subregion as $row){

        $var_row['label'] = $row['subregion'].", ".$row['region'].", ".$row['country'];
        $var_row['value'] =  $row['subregion_id'];
        $var_row['category'] = 'Subregion';
        
        array_push($var_result, $var_row);

    }
    
}


//get merchants
$column = $group = $sort = $limit = null;
$obj_merchant = new merchant();
$where = "merchant LIKE '%$term%' ";
$var_merchant = $obj_merchant -> get($where);

if($var_merchant){
     foreach($var_merchant as $row){

        $var_row['label'] = $row['merchant'];
        $var_row['value'] =  $row['merchant_id'];
        $var_row['category'] = 'Merchant';
        
        array_push($var_result, $var_row);

    }
    
}

echo json_encode($var_result);

?>