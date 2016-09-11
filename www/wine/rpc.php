<?php

$root = $_SERVER['DOCUMENT_ROOT'];
//require_once("$root/includes/init.inc.php");
require_once("$root/includes/init.inc.php");


// Is there a posted query string?
if(isset($_POST['queryString']) || $_GET['showAll']=='True') {

    $queryString = $_POST['queryString'];
    $showAll = $_GET['showAll'];

    $filter = $_GET['filter'];

    if(strlen($queryString) >0 || $_GET['showAll']=='True') {
        
        switch ($_GET['field']){
            
            case 'producer':
              
                $query = "SELECT producer_id, producer
                FROM tblProducer
                WHERE producer LIKE '%$queryString%'
                ORDER BY producer ASC
                LIMIT 15
                ";
                
                $value = 'producer';
                $key = 'producer_id';
            break; 
                
            case 'country':

                if($showAll=='True'){
                    $query = "SELECT country, country_id
                              FROM tblCountry
                              ORDER BY country ASC";
                }else{
                    $query = "SELECT country, country_id
                              FROM tblCountry
                              WHERE country LIKE '%$queryString%'
                              ORDER BY country ASC
                              LIMIT 15";
                }

                $value = 'country';
                $key = 'country_id';
            break;
                
                             
            case 'region':

                if($filter>0){
                    $sql_country = " country_id = $filter AND ";
                    $sql_country_all = "WHERE country_id =$filter ";
                } else {
                    $sql_country = "";
                    $sql_country_all = "";
                }
                
                if($showAll=='True'){
                    $query =   "SELECT region, region_id, country_id
                                FROM tblRegion
                                $sql_country_all
                                ORDER BY region ASC";
                }else{
                    $query =   "SELECT region, region_id, country_id
                                FROM tblRegion
                                WHERE $sql_country region LIKE '%$queryString%'
                                ORDER BY region ASC
                                LIMIT 15";
                }
                
                
            $value = 'region';
            $key = 'region_id';
            break;

            case 'subregion':

                if($filter>0){
                    $sql_region = " region_id = $filter AND ";
                    $sql_region_all = " WHERE region_id = $filter ";
                } else {
                    $sql_region = "";
                    $sql_region_all = "";
                }

                if($showAll=='True'){
                    $query = "SELECT subregion, subregion_id, region_id
                    FROM tblSubRegion
                    $sql_region_all
                    ORDER BY subregion ASC";
                } else {
                    $query = "SELECT subregion, subregion_id, region_id
                    FROM tblSubRegion
                    WHERE $sql_region subregion LIKE '%$queryString%'
                    ORDER BY subregion ASC
                    LIMIT 15";
                }

            $value = 'subregion';
            $key = 'subregion_id';
            break;

            case 'location':

                if($filter>0){
                    $sql_region = " region_id = $filter AND ";
                } else {
                    $sql_region = "";
                }

                $query = "SELECT subregion, subregion_id, region_id
                FROM tblSubRegion
                WHERE $sql_region subregion LIKE '%$queryString%'
                ORDER BY subregion ASC
                LIMIT 15
                ";
            $value = 'subregion';
            $key = 'subregion_id';
            break;



        }//switch
    
         //run query and create html list
        $rst = mysql_query($query) or die(mysql_error());

        if($rst) {
            // While there are results loop through them
            while($row = mysql_fetch_array($rst)){
            $string_escaped = addslashes($row[$value]);
            $field = $_GET['field'];
            $keyValue = $row[$key];
            $parameters = "'$string_escaped','$field','$keyValue'";
            echo "<li onClick=\"fill($parameters);\">$row[$value]</li>";
            }
        } else {
            echo 'ERROR: There was a problem with the query.';
        }
    
    
    
    } else {
    
    
    } // Dont do anything.
        
} // There is a queryString.


?>