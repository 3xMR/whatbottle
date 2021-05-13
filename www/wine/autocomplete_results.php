<?php
// autocomplete RPC for wine.php

$root = $_SERVER['DOCUMENT_ROOT'];

require_once("$root/classes/class.db.php");

$record = []; //empty array to create return array
$records = []; //empty array to create return array
$term = trim(strip_tags($_GET['term']));      
//$term = mysql_real_escape_string($term);

if(!isset($_GET['category'])){
    print "failed: No category provided";
    return false;
}

switch($_GET['category']){
    
    case 'producer':
        $query = "SELECT producer, producer_id
        FROM tblProducer
        WHERE producer LIKE '%$term%'
        ORDER BY producer ASC";

        $value_field = 'producer';
        $key_field = 'producer_id';

        break;
    
    case 'country':
        $query = "SELECT country, country_id
        FROM tblCountry
        WHERE country LIKE '%$term%'
        ORDER BY country ASC";

        $value_field = 'country';
        $key_field = 'country_id';
        
        break;
    
    case 'region':
        $country_id = $_GET['country_id'];
        
        if($country_id){
            $query = "SELECT region, region_id, country_id
            FROM tblRegion
            WHERE country_id = $country_id AND region LIKE '%$term%'
            ORDER BY region ASC";
        }else{
            $query = "SELECT region, region_id
            FROM tblRegion
            WHERE region LIKE '%$term%'
            ORDER BY region ASC"; 
        }
        
        $value_field = 'region';
        $key_field = 'region_id';

        
        break;
        
        
    case 'subregion':
        $region_id = $_GET['region_id'];
        
        if($region_id){
            $query = "SELECT subregion, subregion_id, region_id
            FROM tblSubRegion
            WHERE region_id = $region_id AND subregion LIKE '%$term%'
            ORDER BY subregion ASC";
        }else{
            $query = "SELECT subregion, subregion_id, region_id
            FROM tblSubRegion
            WHERE subregion LIKE '%$term%'
            ORDER BY subregion ASC";
        }
        
        $value_field = 'subregion';
        $key_field = 'subregion_id';
        
        break;

}


$db = MyPDO::instance();
$stmt = $db -> prepare($query);
$stmt -> execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all records in recordset as assoc. array

if(!$result){
    return false; //failed
}

foreach($result as $row){ //tranfer recordset array names into names the autocomplete field can handle
    //$record['value'] = htmlspecialchars(stripslashes($row[$value_field]));
    $record['value'] = $row[$value_field];
    $record['id'] = $row[$key_field];
    $records[] = $record; //push into array
}

echo json_encode($records);



?>