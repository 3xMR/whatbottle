<?php

$root = $_SERVER['DOCUMENT_ROOT'];
//require_once("$root/includes/init.inc.php");
require_once("$root/includes/init.inc.php");

$q = strtolower($_GET["q"]);
if (!$q) return;

switch($_GET['input']){

case 'producer':
    $value = mysql_real_escape_string($_GET["q"]);
    $query = "SELECT producer, producer_id
    FROM tblProducer
    WHERE producer LIKE '%$value%'
    ORDER BY producer ASC";

    $value_field = 'producer';
    $key_field = 'producer_id';
break;

case 'country':
    $value = mysql_real_escape_string($_GET["q"]);
    $query = "SELECT country, country_id
    FROM tblCountry
    WHERE country LIKE '%$value%'
    ORDER BY country ASC";

    $value_field = 'country';
    $key_field = 'country_id';
break;

case 'region':

    $country = $_GET['country'];
    if($country>0){
        $sql_country = " country_id = $country AND ";
    } else {
        $sql_country = null;
    }
    
    $value = mysql_real_escape_string($_GET["q"]);
    $query = "SELECT region, region_id, country_id
    FROM tblRegion
    WHERE $sql_country region LIKE '%$value%'
    ORDER BY region ASC";

    $value_field = 'region';
    $key_field = 'region_id';
break;

case 'subregion':

    $region = $_GET['region'];
    if($region>0){
        $sql_region = " region_id = $region AND ";
    } else {
        $sql_region = "";
    }

    $value = mysql_real_escape_string($_GET["q"]);
    $query = "SELECT subregion, subregion_id, region_id
    FROM tblSubRegion
    WHERE $sql_region subregion LIKE '%$value%'
    ORDER BY subregion ASC
    LIMIT 15";

    $value_field = 'subregion';
    $key_field = 'subregion_id';
break;

} //switch

//run query and create html list
$rst = mysql_query($query) or die(mysql_error());

if($rst) {
    // While there are results loop through them
    while($row = mysql_fetch_array($rst)){
        $string_escaped = addslashes($row[$value_field]);
        $value = $row[$value_field];
        //$value = $string_escaped;
        $field = $_GET['field'];
        $key  = $row[$key_field];
        $parameters = "'$string_escaped','$field','$keyValue'";
        //echo "<li onClick=\"fill($parameters);\">$row[$value]</li>";

        echo "$value|$key\n";

    }
} else {
    echo 'ERROR: There was a problem with the query.';
}

?>