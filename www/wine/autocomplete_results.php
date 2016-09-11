<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/classes/class.db.php");
require_once("$root/classes/class.producer.php");

$term = trim(strip_tags($_GET['term']));      
$term = mysql_real_escape_string($term);

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


$rst = mysql_query($query);


if(mysql_num_rows($rst)>0) {
    
    while($row = mysql_fetch_array($rst)){// While there are results loop through them
        $record['value'] = htmlspecialchars(stripslashes($row[$value_field]));
        $record['id'] = $row[$key_field];
        $records[] = $record; //build recordset
    }
    
    echo json_encode($records);

}

?>