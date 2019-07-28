<?php

//header('Content-Type: text/html; charset=utf-8');
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

    private $select, $from, $where, $order, $group, $having, $search_initialised, $num_vintages;
    private $varSearchParam;
    private $page_rows = 20;
    private $num_pages;
    private $current_page = 1;
    
    public $last_error = null;

    //public function search($type=false, $name=false, $wine_id=false, $winetype_id=false, $country_id=0, $region_id=0, $subregion_id=0,
     //               $producer_id=0, $merchant_id=0, $acquire_id=0,  $group=false, $order=false, $limit=false, $available=0, $having=false, $varSearchParam){
        
    public function search($varSearchParam){
        /* Search parameters passed as associative array
         * $type=false, $search_text=false, $wine_id=false, $winetype_id=false, $country_id=0, $region_id=0, 
         * $subregion_id=0, $producer_id=0, $merchant_id=0, $acquire_id=0, $group=false, 
         * $order=false, $limit=false, $available=0, $having=false;
         * 
         * //TODO: The last vintage of a wine will set the last_modified date so it will NOT always be set to the newest date
         */
        
        //$this -> _timerStart();
       
        $this -> varSearchParam = $varSearchParam; //save parameters to class variable
        extract($varSearchParam, EXTR_SKIP); //extract array to variables
        
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
                tblVintage.drink_year_from,
                tblVintage.drink_year_to,
                (CASE WHEN MAX(tblVintage.modified) > tblWine.modified THEN MAX(tblVintage.modified) ELSE tblWine.modified END ) as last_modified,
                tblProducer.producer,
                tblCountry.country,tblCountry.flag_image,
                tblRegion.region,
                tblSubRegion.subregion ";

        $from = " FROM tblWine LEFT JOIN tblVintage ON tblWine.wine_id = tblVintage.wine_id
                  LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id
                  LEFT JOIN tblCountry ON tblWine.country_id = tblCountry.country_id
                  LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id
                  LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id ";
        
        //**** Wine Related Search Terms ****//
        
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
        }

        //wine_id & name search
        if($wine_id>0){
            //if wine_id is provided don't search by text
            $where .= "AND tblWine.wine_id = $wine_id ";
        } else if($search_text){
            //look for wine name only or wine name or producer
            if($producer_id>0){
                //producer_id provided do don't include producer in text search
                $where .= "AND (tblWine.wine LIKE '%$search_text%') ";
            }else{
                //search for text in wine and producer
                $where .= "AND (tblWine.wine LIKE '%$search_text%' OR tblProducer.producer LIKE '%$search_text%') ";
            }
        }

        //Producer
        if($producer_id>0){
            $where .= "AND tblWine.producer_id = $producer_id ";
        }

        //**** Vintage Related Search Terms ****//
        
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
        
        //Vintage Quality
        if($vintage_quality>0){
            $where .= "AND tblVintage.vintage_quality >= $vintage_quality ";
        }
        
        //Available
        if($available>0){
            $select .= ", (acquisitions.purchased - ifnull(notes.opened,0) - ifnull(override.override,0)) available ";
            $from .= "  LEFT JOIN (SELECT vintage_id, SUM(trelVintageHasAcquire.qty) purchased
                        FROM trelVintageHasAcquire GROUP BY vintage_id) AS acquisitions
                        ON acquisitions.vintage_id = tblVintage.vintage_id
                        LEFT JOIN (SELECT vintage_id, IFNULL(COUNT(tblNotes.note_id),0) opened
                        FROM tblNotes GROUP BY vintage_id) as notes
                        ON notes.vintage_id = tblVintage.vintage_id
                        LEFT JOIN (SELECT vintage_id, override
                        FROM tblAvailableOverride) as override
                        ON override.vintage_id = tblVintage.vintage_id ";
            //$group = " tblVintage.vintage_id ";
            $having = " available > 0 "; //use HAVING to filter by alias 'Available' WHERE won't work with alias
        }else{
            $having = null;
        }
        
        //Drinking Guide
        if($drinking_guide > 0){
            $where .= "AND tblVintage.drink_year_to <= $drinking_guide ";
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
        
        
        if($having > ""){
            $sql_having = "HAVING $having ";
        } else {
            $sql_having = null;
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
        $this -> having = $sql_having;

        $query = $select.$from.$where.$sql_group.$sql_having.$sql_order.$sql_limit;
        
        //echo $query;

        //run query
        $result = mysql_query($query) or die(mysql_error());

        if($result==false){
            $this-> last_error = "search mysql query returned an error";
            return false;
            
        }
        
        //search results successful
        $this -> search_initialised = true;

        $num_rows = mysql_num_rows($result);
        $page_rows = $this -> page_rows;
        $num_pages = $num_rows/$page_rows;
        
        //echo " [num_rows=$num_rows page_rows=$page_rows num_pages=$num_pages] ";

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
        //$this->_timerStop('search');
            
        //print_r($data_array);
        
        return $data_array;


    }
    
    private function _timerStart(){
       //page timer start 
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $this -> timer_start = $time;
    }
    
    private function _timerStop($script){
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        $finish = $time;
        $total_time = round(($finish - $this -> timer_start), 4);
        //echo "Script ($script) completed in '.$total_time.' seconds.";
        return true;
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
            $having = $this -> having;
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

            $query = $select.$from.$where.$group.$having.$order.$limit;
            
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

    public function get_vintages($wine_id){
        //return only vintages after initial wine search, will return all vintages if not filtered by passing wine_id
        
        if(!$this -> search_initialised){
            $this-> last_error = "search has not been initialised - use 'Search(varSearchParam)' method";
            return false; //search not initialised
        }
        
        if(!$this -> varSearchParam){
            $this-> last_error = "search parameters varSearchParam not found";
            return false; 
        }
        
        if(!$wine_id > 0){
            $this-> last_error = "No wine_id provided";
            return false; //no wine_id parameter provided
        }
        
        //print_r($this -> varSearchParam);
        //echo "</br>";
        extract($this -> varSearchParam, EXTR_SKIP); //extract array to variables

        //construct query
        $select = $this -> select;
        $from = $this -> from;
        $where = $this -> where;
        $group = $this -> group;
        $order = $this -> order;
        $having = $this -> having;
        
        $select = "SELECT tblVintage.vintage_id ";
        $from = "FROM tblVintage ";
        $where = "WHERE tblVintage.wine_id = $wine_id ";
        $group = null;
        $order = "tblVintage.year ASC ";

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
        
        //Vintage Quality
        if($vintage_quality>0){
            $where .= "AND tblVintage.vintage_quality >= $vintage_quality ";
        }
        
        
        //Available - use sub-select statements with a LEFT JOIN to calculate sums from different tables
        if($available>0){
            $select .= ", (acquisitions.purchased - ifnull(notes.opened,0) - ifnull(override.override,0)) available ";
            $from .= "  LEFT JOIN (SELECT vintage_id, SUM(trelVintageHasAcquire.qty) purchased
                        FROM trelVintageHasAcquire GROUP BY vintage_id) AS acquisitions
                        ON acquisitions.vintage_id = tblVintage.vintage_id
                        LEFT JOIN (SELECT vintage_id, IFNULL(COUNT(tblNotes.note_id),0) opened
                        FROM tblNotes GROUP BY vintage_id) as notes
                        ON notes.vintage_id = tblVintage.vintage_id
                        LEFT JOIN (SELECT vintage_id, override
                        FROM tblAvailableOverride) as override
                        ON override.vintage_id = tblVintage.vintage_id ";
            //$group = " tblVintage.vintage_id ";
            $having = " available > 0 "; //use HAVING to filter by alias 'Available' WHERE won't work with alias
        }
        
        //Drinking Guide
        if($drinking_guide > 0){
            $where .= "AND tblVintage.drink_year_to <= $drinking_guide ";
        }

        if($order>""){
            $sql_order = "ORDER BY $order ";
        } else {
            $sql_order = null;
        }

        
        if($limit > 0){
            $sql_limit = $limit;
        } //$sql_limit = "LIMIT 0,25 ";
        

        
        if($group > ""){
            $sql_group = "GROUP BY $group ";
        } else {
            $sql_group = null;
        }
        
        
        if($having > ""){
            $sql_having = "HAVING $having ";
        } else {
            $sql_having = null;
        }
        
        $query = $select.$from.$where.$sql_group.$sql_having.$sql_order.$sql_limit;
        //echo $query;
        //echo "</br>";

        //run query
        $result = mysql_query($query) or die(mysql_error());

        if(!$result){
            $this-> last_error = "my_sql query failed";
            return false; //failed
        }
        
        //count rows
        $this -> num_vintages =  mysql_num_rows($result);
        
        //vintage class object
        $obj_vintage = new vintage();
        
        //put results into standard array
        while ($row = mysql_fetch_assoc($result)) {
            //$data_array[] = $row;
            $rst_vintage = $obj_vintage->get_extended("vintage_id = ".$row['vintage_id']);
            $rst_vintages[] = $rst_vintage[0];
            
        }
        
        //print_r($rst_vintages);
        
        //return search results
        return $rst_vintages;
        
  
    }
    
    
    public function set_results_per_page($intResultsPerPage){
        //set number of results to display per page of pagination
        
        if($intResultsPerPage > 0){
            $this ->page_rows = $intResultsPerPage;
        }else{
            return false;
        }
        
    }

}

?>
