<?php

header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");

/* Class wine_search
 *  
 *  searches for wines based on provided search criteria and stores in array
 *  search criteria
 *      wine (name) OR wine_id
 *      winetype_id
 *      producer OR producer_id
 *      merchant_id
 *      
 *      
 * 
 */


class wine_search {

    private $select, $from, $where, $order, $group;
    private $page_rows = 20;
    private $num_pages;
    private $current_page = 1;

    function search($type=false, $name=false, $wine_id=false, $winetype_id=false, $country_id=0, $region_id=0, $subregion_id=0,
                    $producer_id=0, $merchant_id=0, $acquire_id=0, $group=false, $order=false, $limit=false){
        
        //TODO: The last vintage of a wine will set the last_modified date so it will NOT always be set to the newest date
        
        //initialise variables
        $data_array = array();
        $select = "SELECT
                tblWine.wine_id,
                tblWine.wine,
                tblWine.fkClassification,
                tblWine.winetype_id,
                tblWine.producer_id,
                tblWine.country_id,
                tblWine.region_id,
                tblWine.subregion_id,
                tblWine.modified,
                tblVintage.vintage_id,
                tblVintage.year,
                tblVintage.vintage_notes,
                tblVintage.image1,
                tblVintage.vintage_quality,
                tblVintage.vintage_value,
                tblVintage.alcohol,
                tblVintage.closure_id,
                (CASE WHEN MAX(tblVintage.modified) > tblWine.modified THEN MAX(tblVintage.modified) ELSE tblWine.modified END ) as last_modified,
                tblProducer.producer,
                tblCountry.country,tblCountry.flag_image,
                tblRegion.region,
                tblSubRegion.subregion";

        $from = " FROM tblWine LEFT JOIN tblVintage ON tblWine.wine_id = tblVintage.wine_id
                  LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id
                  LEFT JOIN tblCountry ON tblWine.country_id = tblCountry.country_id
                  LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id
                  LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id ";
        

        //winetype
        if($winetype_id>0){
                $select .= ",tlkpWineType.winetype";
                $from .= "LEFT JOIN tlkpWineType ON tblWine.winetype_id = tlkpWineType.winetype_id ";
                $where .= "tblWine.winetype_id = $winetype_id ";
        }

        //country
        if($country_id>0){
            $where .= "AND tblWine.country_id = $country_id ";
        }

        //region
        if($region_id>0){
            $select .= ",tblRegion.region";
            $where .= "AND tblWine.region_id = $region_id ";
            //$from .= "LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id ";
        }

        //subregion
        if($subregion_id>0){
            $select .= ",tblSubRegion.subregion";
            $where .= "AND tblWine.subregion_id = $subregion_id ";
            //$from .= "LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id ";
        }


        //wine_id & name search
        if($wine_id>0){
            //if wine_id is provided don't search by text
            $where .= "AND tblWine.wine_id = $wine_id ";
        } else if($name){
            //look for wine name only or wine name or producer
            if($producer_id>0){
                //producer_id provided do don't include producer in text search
                $where .= "AND (tblWine.wine LIKE '%$name%') ";
            }else{
                //search for text in wine and producer
                $where .= "AND (tblWine.wine LIKE '%$name%' OR tblProducer.producer LIKE '%$name%') ";
            }
        }else{
            //no id and no name provided - add no sql

        }

        //Producer
        if($producer_id>0){
            $where .= "AND tblWine.producer_id = $producer_id ";
        }

        //Merchant or Acquire
        if($acquire_id>0){
            $where .= "AND tblVintage.vintage_id IN
            (SELECT DISTINCT tblVintage.vintage_id FROM tblAcquire
            LEFT JOIN trelVintageHasAcquire ON tblAcquire.acquire_id = trelVintageHasAcquire.acquire_id
            LEFT JOIN tblVintage ON trelVintageHasAcquire.vintage_id = tblVintage.vintage_id
            WHERE tblAcquire.acquire_id = $acquire_id) ";
        } else if($merchant_id>0){
            $where .= "AND tblVintage.vintage_id IN
            (SELECT DISTINCT tblVintage.vintage_id FROM tblAcquire
            LEFT JOIN trelVintageHasAcquire ON tblAcquire.acquire_id = trelVintageHasAcquire.acquire_id
            LEFT JOIN tblVintage ON trelVintageHasAcquire.vintage_id = tblVintage.vintage_id
            WHERE tblAcquire.merchant_id = $merchant_id) ";
        }


        if($order>""){
            $sql_order = "ORDER BY $order ";
        } else {
            $sql_order = null;
        }


        if($limit > 0){
            $sql_limit = $limit;
        } else {
            //set pagination limit
            //$sql_limit = "LIMIT 0,25 ";
        }


        if($group > ""){
            $sql_group = "GROUP BY $group ";
        } else {
            $sql_group = null;
        }


        //remove AND from start of where statement
        $where = ltrim($where,"AND ");

        if($where){
            $where = " WHERE ".$where;
        }


        //construct sql query
        $this -> where = $where;
        $this -> from = $from;
        $this -> select = $select;
        $this -> order = $sql_order;
        $this -> group = $sql_group;


        $query = $select.$from.$where.$sql_group.$sql_order.$sql_limit;

        //run query
        $result = mysql_query($query) or die(mysql_error());

        if($result){
            //search results successful
            $this -> search_initialised = true;
            //$debug -> debug("mysql query results returned OK");
            
            $num_rows = mysql_num_rows($result);
            $page_rows = $this -> page_rows;
            $num_pages = $num_rows/$page_rows;
            //$debug -> debug("num_pages = $num_pages");
            if($num_pages>0){
                $this -> num_pages = ceil($num_pages);
            }else{
                $this -> num_pages = 1;
            }
            //update session
            //$_SESSION['index_page_pagination']['current_page'] = 1;
            $_SESSION['index_page_pagination']['num_pages'] = ceil($num_pages);

            //put results into standard array
            while ($row = mysql_fetch_assoc($result)) {
                $data_array[] = $row;
            }

            //return search results
            //$debug ->debug_write();
            return $data_array;

        }else{
            //search failed
            return false;
        }

    }


    public function get_wines($page_num){
        //return wines from initialised search

        if($this -> search_initialised){

            //construct query
            $select = $this -> select;
            $from = $this -> from;
            $where = $this -> where;
            $group = $this -> group;
            $order = $this -> order;
            $num_pages = $this -> num_pages;
            $page_rows = $this -> page_rows;

            //calculate pagination
            if($page_num < 1){
                $page_num = 1;
            }else if($page_num > $num_pages){
                //no page requested set to page 1
                $page_num = $num_pages;
            }

            $this -> current_page = $page_num;

            //determine limits
            $start_row = ($page_num - 1) * $page_rows;
            $limit = "LIMIT $start_row, $page_rows";

            $query = $select.$from.$where.$group.$order.$limit;

            //run query
            $result = mysql_query($query) or die(mysql_error());

            if($result){
                //put results into standard array

                while ($row = mysql_fetch_assoc($result)) {
                    $data_array[] = $row;
                }

                //return search results
                return $data_array;

            }else{
                //search failed
                return false;
            }
        }else{
            return false;
        }//initialised

    }


    public function get_page_numbers(){
        //return number of pages for results

        $_arr_pages = array();

        if($this -> num_pages > 0){
            $_arr_pages['num_pages'] = $this -> num_pages;
            $_arr_pages['current_page'] = $this -> current_page;
            return $_arr_pages;
        }else{
            return false;
        }

    }

    public function return_vintages(){
        //return vintages after initial wine search
        if($this -> search_initialised){
            //return vintage results using initialised search settings
            $query = "SELECT * FROM tblVintage
            LEFT JOIN tblWine ON tblWine.wine_id = tblVintage.wine_id ".$where;

            $result = mysql_query($query) or die(mysql_error());

            if($result){
                //search results successful
                $this -> search_initialised = true;

                //put results into standard array
                while ($row = mysql_fetch_assoc($result)) {
                    $data_array[] = $row;
                }

                //return search results
                return $data_array;

            }else{
                //search failed
                return false;
            }

        }else{
            return false;
        }
    }


}

?>
