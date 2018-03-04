<?php

//Creates search results for main wine autocomplete search input
//inludes results from multiple categories and returns
// value, label, category

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/classes/class.wine_search.php");
require_once("$root/classes/class.db.php");
require_once("$root/classes/class.region.php");


$q = strtolower($_REQUEST['term']);

if (!$q) {
    return false;
}


$var_result = array();

$term = mysql_escape_string(trim($q));

$search_obj = new wine_search();

if(strlen($term)==1){
    unset($_SESSION['var_wine_search_criteria']); //single character means new search, so clear old search criteria held in session
}

$_SESSION['var_wine_search_criteria']['search_text'] = $term; //put search text to session
$varSearchParam = $_SESSION['var_wine_search_criteria']; //get search parameters from session
$varSearchParam['search_text'] = $term; //update parameters
$varSearchParam['type'] = "wines"; //update parameters
$varSearchParam['group'] = "tblWine.wine_id";
$varSearchParam['order'] = "tblWine.wine ASC";

$results = $search_obj -> search($varSearchParam); //search


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

//get producers
$obj_producer = new producer();
$where = "producer LIKE '%$term%' ";
$var_producer = $obj_producer -> get($where);

if($var_producer){
     foreach($var_producer as $row){
    
        $var_row['label'] = $row['producer'];
        $var_row['value'] =  $row['producer_id'];
        $var_row['category'] = 'Producer';
        
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