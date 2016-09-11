<?php
//Returns country name and key for given region or subregion key


$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/classes/class.db.php");
require_once("$root/classes/class.region.php");
require_once("$root/classes/class.subregion.php");


$keyField = $_POST['keyField'];
$keyValue = $_POST['keyValue'];

if ($keyValue>0){

    switch ($keyField){

        case 'region':
           $region_obj = new region();
           $var_region = $region_obj -> get_extended("region_id = $keyValue");
           if($var_region){
               $var_result['success'] = True;
               $var_result['country_id'] = $var_region[0]['country_id'];
               $var_result['country'] = $var_region[0]['country'];
           }else{
               $var_result['success'] = false;
               $var_result['error'] = 'no country returned for given region_id';
           }
        break;

        case 'subregion':
           $obj = new subregion();
           $var_subregion = $obj -> get_extended("subregion_id = $keyValue");
           if($var_subregion){
               $var_result['success'] = True;
               $var_result['country_id'] = $var_subregion[0]['country_id'];
               $var_result['country'] = $var_subregion[0]['country'];
               $var_result['region_id'] = $var_subregion[0]['region_id'];
               $var_result['region'] = $var_subregion[0]['region'];
           }else{
               $var_result['success'] = false;
               $var_result['error'] = 'no region returned for given subregion_id';
           }
        break;
    }//switch
} else {
    $var_result['success'] = false;
    $var_result['error'] = 'no keyValue provided';
}


//output result
echo json_encode($var_result);


?>
