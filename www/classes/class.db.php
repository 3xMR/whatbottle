<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/classes/MyPDO.php"); //database connection class

//include table classes
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
require_once("$root/classes/User.php");
require_once("$root/classes/class.vintage_has_acquire.php");
require_once("$root/classes/class.vintage_has_award.php");
require_once("$root/classes/class.vintage_has_grape.php");
require_once("$root/classes/class.vintage.php");
require_once("$root/classes/class.wine.php");
require_once("$root/classes/class.winetype.php");
require_once("$root/classes/class.list_has_vintage.php");



class db {

    /**
     * Base database access class
     * inherit into table classes
     * 27-02-2021 - upgraded to PDO
     *
     * Methods:
     * Insert - create new record, input array, returns index or array of errors
     * Update - update record, input array, WHERE statement or uses index, returns # of updated
     *      records or false
     * Delete - deletes record, input index, returns true or false
     * Get - returns record(s), input WHERE or uses index if provided
     *  assoc. array on success
     *  Null if no matching record
     *  false on error - use get_sql_error() to retrieve error message
     * 
     *
     */

private $sql_error = null;
private $last_error = null;
public $count = null;
protected $db;


function __construct() {
    //construct function
    $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
}

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


function update ($input_array, $where=false){
    //UPDATE existing record in db

    $value = null;
    $error_detail = null;
    $assocUpdateArray = [];
    $required_missing = false;
    $this->sql_error = null;
    $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
    
    if(!isset($this->table)||$this->table==''){ //check db table is set
        $this->sql_error = "class.db:update() UPDATE operation was not possible because Table is missing";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }
    
    if(!isset($input_array)){ //check input_array provided
        $this->sql_error = "class.db:update() UPDATE operation was not possible because input_array is missing";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }

    if(isset($where)){ //check where statement provided
        $where = str_ireplace('WHERE', '', $where); //remove 'where' non-case sensitive, because we add it later
    } else{
        $this->sql_error = "class.db:update() UPDATE operation was not possible because WHERE statement is missing";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }
    
    
    foreach ($this->fieldlist as $field => $var_field){ //process input_array to create sql statement

        $include = true; //add all fields by default

        $find_key = $var_field['map'];
        $found = array_key_exists($find_key, $input_array); //return field mapping if key exists
        
        if($found){
            $value = $input_array[$find_key]; //get value
        }else{
            $value = null; //no value found, set to null
        }
        
        if(isset($var_field['primary_key']) && empty($where)){ //no WHERE clause provided so default to primary key update
            $where = "$field = $value";
            $include = false; //do not include in sql update statement
        } 
             
        if(isset($var_field['primary_key'])){ //remove for updates
            $include = false;
            continue; //skip to next field;
        }
        
        if(isset($var_field['autonumber'])){ //remove as this is an auto_number field
            $include = false;
            continue; //skip to next field
        }
        
        if($field=='created'){ //ignore - don't change created date for record
            $include = false;
            continue;
        }
        
        if(!$found && isset($var_field['override'])){
            $default = $var_field['default']; //set to default value as this is required but has not been found
            $value = $default;
        }
             
        if($field=='modified'){ //ensure timestamp is applied for modified and created
            $value = date('Y-m-d H:i:s');
            $include = true;
        }
        
        if(!$found && isset($var_field['required']) && $var_field['required']==true){ //required field is missing do not insert this record
            $required_missing = true;
            $error_detail .= " Required field missing: $field ";
        }
        
        if(isset($var_field['primary_key']) && empty($where)){ //no WHERE clause provided so default to primary key update
            $where = "$field = $value";
            //do not include pk value in sql update statement
            $include = false;
        } 

        //write to array for PDO insert
        if($include){
            $assocUpdateArray[$field] = $value; //add value to array
            $set .= "$field = :$field, "; //add column to set sql statement    
        }
        
    } //end foreach
    
    
    if($required_missing == true){ //abort insert
        $this->sql_error = "class.db:update() UPDATE operation was not possible because one or more mandatory fields are missing. $error_detail";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }
    
    $setSql = rtrim($set, ', ');
  
    $sql = "UPDATE $this->table SET $setSql WHERE $where;";

    //PDO UPDATE statement
    $stmt = $this->db->prepare($sql);
    $stmt->execute($assocUpdateArray);

    
    if(!$stmt){ //PDO update failed
        $this->sql_error = "class.db:update() UPDATE operation failed when calling PDO execute";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }
    
    return $stmt;

}


function insert($input_array){
    //insert new record into db
    
    //initialise
    $value = null;
    $error_detail = null;
    $assocInputArray = [];
    $required_missing = false;
    $column = '';
    $valueName = '';
    $this->sql_error = null;
    $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
    
    if(!isset($this->table)||$this->table==''){
        $this->sql_error = "class.db:insert() INSERT operation was not possible because Table is missing for class: ";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }
    
    foreach ($this->fieldlist as $field => $var_field){

        $include = true;

        $find_key = $var_field['map'];
        $found = array_key_exists($find_key, $input_array); //return field mapping if key exists
        
        if($found){
            $value = $input_array[$find_key]; //get value
        }else{
            $value = null; //no value found, reset
        }
        
        if(isset($var_field['primary_key'])){ //remove for inserts as this will be an auto_number field in db
            $include = false;
            continue; //skip to next field;
        }
        
        if(isset($var_field['autonumber'])){ //remove as this is an auto_number field
            $include = false;
            continue; //skip to next field
        }
        
        if(!$found && isset($var_field['override'])){
            $default = $var_field['default']; //set to default value as this is required but has not been found
            $value = $default;
        }
        
        if(!$found && isset($var_field['datatype']) ){ //not found and not required so set value to Null or empty string
            if($var_field['datatype']=='string' ){
                $value = ''; 
            } else {
                $value = null; //was 'Null'
            }
        }
             
        if($field=='modified' || $field=='created'){ //ensure timestamp is applied for modified and created
            $value = date('Y-m-d H:i:s');
            $include = true;
        }
        
        if(!$found && isset($var_field['required']) && $var_field['required']==true){ //required field is missing do not insert this record
            $required_missing = true;
            $error_detail .= " Required field missing: $field ";
        }

        //write to array for PDO insert
        If($include){
            $assocInputArray[$field] = $value;
            $column .= "$field, ";
            $valueName .= ":$field, ";
        }
        
    } //end foreach

    //print_r($assocInputArray);

    if($required_missing == true){ //abort insert
        $this->sql_error = "class.db:insert() Insert operation was not possible because one or more mandatory fields are missing. $error_detail";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }
       
    $columnSql = "(".rtrim($column, ', ').")";
    $valueNameSql = "(".rtrim($valueName, ', ').")";
    //print_r($assocInputArray);
    
    $sql = "INSERT INTO $this->table $columnSql VALUES $valueNameSql;";
    //print $sql;
    
    
    //PDO INSERT statement
    $stmt = $this->db->prepare($sql);
    $stmt->execute($assocInputArray);
    $id = $this->db->lastInsertId();
    return $id;

    
} //end insert



function get($where=false, $columns=false, $group=false, $sort=false, $limit=false, $input_array=null){
    //execute GET query against db
    //$input_array -> associative array of input values
    
    $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
    $this->sql_error = null; //reset sql_error
    
     if(!isset($this->table)||$this->table==''){
        $this->sql_error = "class.db:insert() INSERT operation was not possible because Table is missing for class: ";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }
    
    if(!$where){
        //return all records
        $where = null;
    }else{
        $where = "WHERE $where";
    }

    if(!$columns){
        $columns = '*';
    } else {
        $columns = $columns;
    }

    if(!$group){
        $group = null;
    } else {
        $group = " GROUP BY $group";
    }

    if(!$sort){
        $sort = null;
    } else {
        $sort = " ORDER BY $sort";
    }
      
    if(!$limit){
        $limit = null;
    } else {
        $limit = " LIMIT $limit";
    }
    
    $query = "SELECT $columns FROM $this->table $where $group $sort $limit";
    
    //print $query."<br>";
 
    $stmt = $this->db->prepare($query);
    if(isset($input_array)){
        $stmt->execute($input_array);
    }else{
        $stmt->execute();
    }
    $rst = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all records in recordset as assoc. array
        //$rstRow = $stmt->fetch(PDO::FETCH_ASSOC); //fetch next row as associated array
    $this->count = count($rst); //count results in array

    if($this->count < 1){
        return null; //no records to return
    }

    return $rst; //return dataset

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

    $query = "SELECT count(*) FROM $this->table $where";
    //print $query;
    
    $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    if($stmt){
        $count = $stmt->fetchColumn();
    }
    if(!$count){ return false; }
  
    return $this->count = $count;
}


function delete($where){
    //delete records that match the where statement
    
    $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
    
    if(!$where){//no where statement provided
        $this->sql_error = "class.db:delete() no WHERE statement provided - cannot continue. table class:".$this->table;
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
    }
    
    $where = " WHERE $where";
    $query = "DELETE FROM $this->table $where";
    //print $query;
    $stmt = $this->db->prepare($query);
    $result = $stmt->execute();
 
    if(!$result){
        $this->sql_error = "class.db:delete() PDO execution failed - cannot continue. table class:".$this->table;
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }
    
    $count = $stmt->rowCount(); //determine if any rows were affected - Delete will return true even if no records were changed
    if($count>0){
        return true;
    }else{
        $this->sql_error = "class.db:delete() no records deleted, likely no matching record to delete";
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }

}
    

function update_custom($set, $where){
    //allow update of a set of columns only
    
    $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
  
    if(!$where){//no where statement provided
        $this->sql_error = "class.db:update_custom() no WHERE statement provided - cannot continue. table class:".$this->table;
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
    }
    
    if(!$set){//no where statement provided
        $this->sql_error = "class.db:update_custom() no columns provided - cannot continue. table class:".$this->table;
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
    }
    
    
    $query = "UPDATE $this->table SET $set WHERE $where";
    //print $query;
    $stmt = $this->db->prepare($query);
    $result = $stmt->execute();
 
    if(!$result){
        $this->sql_error = "class.db:update_custom() PDO execution failed - cannot continue. table class:".$this->table;
        $this->last_error = $this->sql_error; //set last_error property in table class so it can be retrieved on error
        return false;
    }

    return $result;

}


} //end class
