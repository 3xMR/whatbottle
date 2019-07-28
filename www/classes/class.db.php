<?php

//include table classes

//header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];

require_once("$root/classes/class.debug.php");

require_once("$root/classes/class.acquire.php");
require_once("$root/classes/class.acquire_type.php");
require_once("$root/classes/class.available_override.php");
require_once("$root/classes/class.award.php");
require_once("$root/classes/class.award_org.php");
require_once("$root/classes/class.country.php");
require_once("$root/classes/class.fullness.php");
require_once("$root/classes/class.grape.php");
require_once("$root/classes/class.merchant.php");
require_once("$root/classes/class.producer.php");
require_once("$root/classes/class.region.php");
require_once("$root/classes/class.subregion.php");
require_once("$root/classes/class.sweetness.php");
require_once("$root/classes/class.tasting_note.php");
require_once("$root/classes/class.user.php");
require_once("$root/classes/class.vintage.php");
require_once("$root/classes/class.vintage_has_acquire.php");
require_once("$root/classes/class.vintage_has_award.php");
require_once("$root/classes/class.vintage_has_grape.php");
require_once("$root/classes/class.vintage.php");
require_once("$root/classes/class.wine.php");
require_once("$root/classes/class.winetype.php");

class db {

    /**
     * Base database access class
     * inherit into table classes
     *
     * Methods:
     * Insert - create new record, input array, returns index or array of errors
     * Update - update record, input array, WHERE statement or uses index, returns # of updated
     *      records or false
     * Delete - deletes record, input index, returns true or false
     * Get - returns record(s), input WHERE or uses index if provided, returns array or false
     *
     * TODO: Add validation rules to fieldlist and create base validation class
     *
     */

private $sql_error = null;

private $count = null;

private $debug = false; //display debug comments

function get_table(){
   return $this->table;
}

function get_fieldlist(){
   return $this->fieldlist;
}

function get_sql_error(){
    //get last mysql error
    return $this->sql_error;
}

function set_sql_error($sql_error){
    //get last mysql error
    $this->sql_error = $sql_error;
}

function unstrip_array($array){
    foreach($array as $index ->$val){

            $array[$index] = stripslashes($val);

    }
    return $array;
}

function update($input_array, $where=false){
    //UPDATE existing record in table
    
    //initialise
    $set = null;
    
    //reset sql_error
    $this->sql_error = null;
    $required_missing = false;

    foreach ($this->fieldlist as $field => $var_field){

        $find_key = $var_field['map'];

        //return field mapping if key exists
        $found = array_key_exists($find_key, $input_array);
        if ($found){
            $value = $input_array[$find_key];

        }

        $include = true; //include all fields in UPDATE by default
        
        if(!$found ){
            //field not found, ignore unless override provided
            if(isset($var_field['override___'])){
                //****this section has been disabled****
                //
                //set to default value
                if($var_field['map']=='modified'){
                    $default = $var_field['default'];
                    $value = $default;
                    //include in sql statement
                    $include = true;
                } else {
                    //do NOT include in sql
                    $include = false;
                }
            } else {
                //do NOT include in sql
                $include = false;
            }
        } else {
            //determine WHERE clause to use
            if(isset($var_field['primary_key']) && empty($where)){
                //no WHERE clause provided so default to primary key update
                $where = "$field = '$value'";
                //do not include pk value in sql statement
                $include = false;
            } 
        }
        
        //add to sql statement
        if ($include){
            //update strings
            if($field=='modified'){
                //exclude quotes for this field
                //$set = $set." ".$field."=".$value.",";
                $set = $set." ".$field."=NOW(),";
            } else {
                //escape strings to prevent insertion
                if(get_magic_quotes_gpc()){
                    //magic quotes is on strip slashes to prevent double escaping
                    $value = stripslashes($value);
                }
                $value = mysql_real_escape_string($value);
                $set = $set." ".$field."='".$value."',";
            }
        }

    } //end foreach

    //remove trailing comma
    $set = rtrim($set,',');

    //execute sql
    if($required_missing <> true){
    //id provided so OK to update

        //create UPDATE statement
        $query = "UPDATE $this->table SET $set WHERE $where";
        //run query
        $qry_result = mysql_query($query);

        if($qry_result){
            $result = $qry_result;
        } else {
            $this -> sql_error = mysql_error();
            $result = false;
        }
        //return inserted index
    }

    return $result;
    
} //end class_db: update


function insert($input_array){
    //insert new record into db
    
    //initialise
    $values = $value = $set = $error_detail = null;
    
    //reset sql_error
    $this->sql_error = null;
    $required_missing = false;
    
    foreach ($this->fieldlist as $field => $var_field){

        $include = true;

        $find_key = $var_field['map'];
        $found = array_key_exists($find_key, $input_array); //return field mapping if key exists
        if ($found){
            $value = $input_array[$find_key];
        }

        
        if(!$found ){
            //field not found, set to null unless override provided
            if(isset($var_field['override'])){
                //set to default value
                $default = $var_field['default'];
                $value = $default;
            } else {
                if(isset($var_field['required']) && isset($var_field['autonumber'])){
                    $required_missing = true;
                    $value ='NULL';
                    $error_detail = $error_detail." missing field=".$var_field['map'];
                } else {
                    //set an empty value
                    if(isset($var_field['datatype'])){
                        if($var_field['datatype']=='string' ){
                            $value = ''; 
                        } else {
                            $value = 'NULL';
                        }
                    }else{
                        $value = 'NULL';
                    }
                }
            }
        }

        
        //ensure timestamp is applied for modified and created
        if($field=='modified' || $field=='created'){
            $value = 'Now()';
            $include = true;
        }

        //add to sql statement
        if(isset($var_field['datatype'])){
            if($var_field['datatype'] == 'string'){

                //escape strings to prevent insertion
                if(get_magic_quotes_gpc()){
                    //magic quotes is on  - strip slashes to prevent double escaping
                    $value = stripslashes($value);
                }
                $value = mysql_real_escape_string($value);
            }
        }
        
        if (isset($var_field['autonumber'])){
            $include = false;
            $required_missing = false;
        }
        
        //write sql statement
        if ($include){
            
            $set = $set." ".$field.",";
            //remove quotes from NOW function
            if ($value=="NOW()" || $value=="Now()"){
                $values = $values."$value,";
            } else {
                $values = $values."'$value',";
            }
            
        }
        
    } //end foreach

    //remove trailing comma
    $set = rtrim($set,',');
    $values = rtrim($values,',');

    //execute sql
    if($required_missing == false){

        //create INSERT statement
        $query = "INSERT INTO $this->table ($set) VALUES ($values)";
        
        //run query
        mysql_query("SET NAMES utf8");
        $qry_result = mysql_query($query);
        
        if($qry_result){
            $index = mysql_insert_id();
            //return new index
            return $index;
        } else {
            //sql failed
            $sql_error = mysql_error();
            //set sql error
            $this->sql_error = mysql_error(); 
            return false;
        }
        
    } else {
        $this->sql_error = "Insert operation was cancelled because one or more mandatory fields are missing. $error_detail";
    }

    
} //end insert



function get($where=false, $columns=false, $group=false, $sort=false, $limit=false){
    //retrieve record(s) from table
    
    //reset sql_error
    $this->sql_error = null;
    
    if(!$where){
        //return all records
        $where = NULL;
    }else{
        $where = "WHERE $where";
    }

    if (!$columns){
        $columns = '*';
    } else {
        $columns = $columns;
    }

    if (!$group){
        $group = NULL;
    } else {
        $group = " GROUP BY $group";
    }

    if (!$sort){
        $sort = NULL;
    } else {
        $sort = " ORDER BY $sort";
    }
      
    if (!$limit){
        $limit = NULL;
    } else {
        $limit = " LIMIT $limit";
    }
    
    //if(!$failed){
        $query = "SELECT $columns FROM $this->table $where $group $sort $limit";
        
        //set db codeset
        mysql_query("SET NAMES utf8");
 
        $result = mysql_query($query);
        
        if($result){
            $this -> count = mysql_num_rows($result); //set count to number of rows
        }else{
            $this -> count = 0;
        }

        if($result==false){ //differentiate between no rows returned and a fail
            //error getting results - return error (false)
            $this->sql_error = mysql_error(); 
            return $result;
        } else {
            //set count to number of rows
            //$this -> count = mysql_num_rows($result);

            //dump results into a standard php array
            $var_results = array();
            
            while ($row = mysql_fetch_assoc($result)) {     
                //$this->data_array[] = $row;
                $var_results[] = $row;
            } // while
            
            if(is_array($var_results)){
                 return $var_results;
            } else {
                return false;
            }
        }  

    //}
}


function count(){
    //get count of records returned in last query
    return $this->count;
}


function row_count($where){
    //get number of rows
    if($where){
        $where = " WHERE $where";
    }else{
        $where = NULL;
    }

    $query = "SELECT COUNT(*) FROM $this->table $where";
    $result = mysql_query($query) or die (mysql_error());
    if(!$result){
        return false;
    }
    $row = mysql_fetch_array($result);
    return $row['COUNT(*)'];
}


function delete($where){
    //delete records that match the where statement
    if($where){
        $where = " WHERE $where";
        $query = "DELETE FROM $this->table $where";
        $result = mysql_query($query);
    }else{
        //no where statement provided
        $result = false;
        $error = 'important';
        $msg = "no where statement provided for delete function";
    }

    return $result;
}
    

    function update_custom($set, $where){
        //allow update of a set of columns only

        $query = "UPDATE $this->table SET $set WHERE $where";

        //run query
        $qry_result = mysql_query($query);

        if($qry_result){
            return true;
        } else {
            $this -> sql_error = mysql_error();
            return false;
        }

    }


} //end class
?>
