<?php
/* 
 * Wine RPC functions
 * 
 */
//prevent php warnings messing up json return
ini_set( "display_errors", 0); 

$root = $_SERVER['DOCUMENT_ROOT'];
$new_root = rtrim($root, '/\\');
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");
require_once("$root/classes/MyPDO.php");
require_once("$root/classes/Producer.php");


$rpc_action = (filter_input(INPUT_POST, 'rpc_action', FILTER_SANITIZE_STRING) > "") ? filter_input(INPUT_POST, 'rpc_action', FILTER_SANITIZE_STRING) : filter_input(INPUT_GET, 'rpc_action', FILTER_SANITIZE_STRING);
$action = (filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) > "") ? filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) : filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if($rpc_action || $action){
    //convert action to function call
    if($rpc_action){
        $fnc = $rpc_action;
    }else{
        $fnc = $action;
    }
    
    if(is_callable($fnc)){
        //call action as function
        $var_result = call_user_func($fnc);
        echo json_encode($var_result);
    }else{
        $page = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING);
        $var_result['error'] = "function [$fnc] not found on server page $page";
        $var_result['success'] = false;
        echo json_encode($var_result);
    }
    
}


function debug($text){
    //debug output
    
    global $new_root, $log_path;
    $debug = false;
    $write_to_file = true;
    $file_name = "log_rpc_wine_db.txt";
    
    if($debug){
        if($write_to_file){
            //debug output to file

            $log_file = $new_root.$log_path.$file_name;

            if(file_exists($log_file)){
                //open new file in w rite mode
                $mode = "a";
            } else {
                $mode = "w";
            }

            $fh = fopen($log_file, $mode) or die("can't open log file");

            $stringData = "> $text \n";
            fwrite($fh, $stringData);
            fclose($fh);
            
        }else{
            //write to screen
            echo "> $text<br/>";
        }   
    }
    
}


function google_search_term_wine(){
    //return text for google search based on wine_id
    $wine_id = $_REQUEST['wine_id'];
    
    if(!($wine_id>0)){
        $var_result['success'] = false;
        $var_result['error'] = 'No wine_id provided';
        return $var_result;
    }
    
    $obj_wine = new wine();
    $where = "wine_id = $wine_id";
    $result = $obj_wine ->get_extended($where);

    if($result>0){
        $row = $result[0];
        $wine = $row['wine'];
        $producer = $row['producer'];
        $str = urlencode("$producer $wine");
        
        $var_result['success'] = true;
        $var_result['term'] = $str;
        return $var_result;
    } else {
        $var_result['success'] = false;
        $var_result['error'] = 'No results returned by DB';
        return $var_result;
    }

}
    
    


function exists_wine($wine_id){
    //returns true if given wine_id already exists

    if($wine_id>0){
        $obj_wine = new wine();
        $where = "wine_id = $wine_id";
        $result = $obj_wine -> row_count($where);
        log_write("row_count=".$result,1,"exists_wine");
        if($result>0){
            return true;
        } else {
            return false;
        }

    }

    return false;
}


function get_from_db(){
    //get wine from db and put to session
    $wine_id = $_REQUEST['wine_id'];
    
    if($_REQUEST['dst_action']=='add'){
        //clear session variables - ready for new wine
        unset($_SESSION['var_wine_temp']);
        //create new session with status of 1 (add)
        $_SESSION['var_wine_temp']['status'] = 1;
        $var_result['success']=true;
        return $var_result;
    }
    
    if($wine_id){
        $obj_wine = new wine();
        $where = "wine_id = $wine_id";
        $result = $obj_wine -> get_extended($where);
        if($result){
            //put record in session
            $_SESSION['var_wine_temp'] = $result[0];
            $_SESSION['var_wine_temp']['status'] = 3; //read
            $_SESSION['var_wine_temp']['is_dirty'] = false;
            $var_result['success']=true;
            return $var_result;
        } else {
            $var_result['success']=false;
            $var_result['error']="no record found in wine_id=$wine_id";
            return $var_result;
        }
    }else{
        $var_result['success']=false;
        $var_result['error']="no wine_id provided";
        return $var_result;  
    }

};


function add_wine(){
    //add new wine
    //status = '1'new '2'write '3'read '4'delete
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }    

    If($_REQUEST['status']==1){
        $status = $_REQUEST['status'];
        //unset old session data
        unset($_SESSION['var_wine_temp']);
        //create new session
        $_SESSION['var_wine_temp']['status'] = $status;
        $var_return['success']=true;
    } else {
        $var_return['success']=false;
        $var_return['error']='invalid parameters passed';
    }

return $var_return;

};


function put_wine_session(){
    //put data from wine form to session
    
    if(!$_REQUEST['json_array']){
        $var_result['success']=false;
        $var_result['error']='no json_array provided';   
        return $var_result;
    }

    $json_array = stripslashes($_REQUEST['json_array']);
    $var_assoc = json_decode($json_array,true);
    
    if(!is_array($var_assoc)){
        $var_result['success'] = false;
        $var_result['msg']='not an array';
        return $var_result;
    }
    
    unset($_SESSION['var_wine_temp']); //clear session before setting new values
    
    foreach($var_assoc as $field){ //put data into session
        $_SESSION['var_wine_temp'][$field['name']] = $field['value'];
    }
    

    $_SESSION['var_wine_temp']['user_id'] = $_SESSION['user_id']; //update user_id
    
    //Session updated - now save session to db

    if($_REQUEST['save_db']==true){ //continue with commit to db
        $var_wine = $_SESSION['var_wine_temp'];
        $var_result = save_to_db($var_wine); //pass details to save function
    } else {
        //save to session only
        $var_result['success'] = true; //do not return yet - as it may be a save_db
        $var_result['msg']='save to session successful';
        $var_result['save_type'] = 'session';
    }
    
    return $var_result;
    
};


function save_to_db($var_wine){
    //save wine session detail to db
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }    
    
    $valid = true;
    
    if(!$var_wine['wine']>""){
        $error_msg = "wine_name not provided";
        $valid = false;
    }

    if(!$var_wine['producer_id']>0){
        $error_msg .= " >> producer_id not provided";
        $valid = false;
    }

    if(!$var_wine['winetype_id']>0){
        $error_msg .= " >> winetype_id not provided";
        $valid = false;
    }

    if(!$var_wine['country_id']>0){
        $error_msg .= " >> country_id not provided";
        $valid = false;
    }

    if(!$var_wine['region_id']>0 ){
        $error_msg = " >> region_id not provided";
        $valid = false;
    }

    if($valid==false){
        //one or more mandatory fields missing - cannot continue with save
        $var_result['success']=false;
        $var_result['error']=$error_msg;
        return $var_result;
    }
    
    //start db update
    
    $wine_id = $var_wine['wine_id'];
    
    if($wine_id>0){ //has wine_id so is existing and should be updated
 
        if(exists_wine($wine_id)==false){ //check wine does actually exists to prevent updating a deleted wine - stale session
            
            //trying to update a deleted wine - abort
            $var_result['success']=false;
            $var_result['error']='trying to update a deleted wine - stale session data?';
            return $var_result;
        }

        //existing wine - perform SQL update
        $wine_obj = new wine();
        $where = "wine_id = $wine_id";
        $result = $wine_obj -> update($var_wine, $where);

        if($result){
            //db update OK
            $_SESSION['var_wine_temp']['status'] = 'saved';
            $_SESSION['var_wine_temp']['is_dirty'] = false;
            $var_result['success']=true;
            $var_result['save_type']='db update';
            $var_result['wine_id']= $wine_id;
            return $var_result;
        } else {
            //update failed
            $sql_error = $wine_obj ->get_sql_error();
            $var_result['success']=false;
            $var_result['error']= "db update failed - sql_error=$sql_error";
            $var_result['save_type']='db update';
            return $var_result;
        }
        
    } else {
        
        //new wine - perform SQL insert
        
        //double check these details do not already exists
        //note: have removed ability to add a duplicate - close the below with if statement $bln_allow_duplicate
        $where = "wine = \"".$wine_name."\" AND winetype_id = $winetype_id AND producer_id = $producer_id ";
        $wine_exists_obj = new wine();
        $var_wine_result = $wine_exists_obj -> get($where);

        if($var_wine_result){
            //match found - wine with these values already exists
            $var_result['success']=false;
            $var_result['error']='db save aborted - a wine with this name, producer and winetype already exists!';
            return $var_result;
        }


        //insert wine
        $wine_obj = new wine();
        $result = $wine_obj -> insert($var_wine);

        if($result){
            $_SESSION['var_wine_temp']['status'] = 'saved';
            $_SESSION['var_wine_temp']['wine_id'] = $result;
            $_SESSION['var_wine_temp']['is_dirty'] = false;
            $var_result['success']=true;
            $var_result['wine_id']=$result;
            $var_result['save_type']='db insert';
            return $var_result;
        } else {
            $sql_error = $wine_obj ->get_sql_error();
            $var_result['success']=false;
            $var_result['error']="db insert failed sql_error=$sql_error";
            $var_result['save_type']='db insert';
            return $var_result;
        }
    }
    
}


function save_to_db_old(){
    //save wine to database

    //error codes
    //10 - duplicate wine
    //20 - mandatory fields missing
    //30 - database save error
    //40 - invalid wine_id - update cannot continue

    log_write("start",1,"save_to_db");
    log_write("field values to follow:",1,"save_to_db");
    $wine_id = $_REQUEST['wine_id'];
    $wine_name = $_REQUEST['wine_name'];
    //$year = $_REQUEST['year'];
    $winetype_id = $_REQUEST['winetype_id'];
    $producer = $_REQUEST['producer'];
    $producer_id = $_REQUEST['producer_id'];
    $country = $_REQUEST['country'];
    $country_id = $_REQUEST['country_id'];
    $region = $_REQUEST['region_id'];
    $region_id = $_REQUEST['region_id'];
    $subregion = $_REQUEST['subregion'];
    $subregion_id = $_REQUEST['subregion_id'];
    $override_duplicate = $_REQUEST['override_duplicate'];

    //validate mandatory fields
    $valid = true;
    
    if(!$wine_name>""){
        $error_msg = "wine_name not provided <br/>";
        $valid = false;
    }

    if(!$producer_id>0){
        $error_msg = $error_msg."producer_id not provided <br/>";
        $valid = false;
    }

    if(!$winetype_id>0){
        $error_msg = $error_msg."winetype_id not provided <br/>";
        $valid = false;
    }

    if(!$country_id>0){
        $error_msg = $error_msg."country_id not provided <br/>";
        $valid = false;
    }

    if(!$region_id>0 ){
        $error_msg = $error_msg."region_id not provided <br/>";
        $valid = false;
    }

    if($valid==false){
        //one or more mandatory fields missing - cannot continue with save
        log_write("FAILED mandatory fields missing",3,"save_to_db");
        $var_result['success']=false;
        $var_result['error']='20';
        $var_result['error_msg']=$error_msg;
        return $var_result;
    }

    //create save array
    $var_wine['wine_id'] = $_REQUEST['wine_id'];
    $var_wine['wine'] = $_REQUEST['wine_name'];
    $var_wine['year'] = $_REQUEST['year'];
    $var_wine['winetype_id'] = $_REQUEST['winetype_id'];
    $var_wine['producer'] = $_REQUEST['producer'];
    $var_wine['producer_id'] = $_REQUEST['producer_id'];
    $var_wine['country'] = $_REQUEST['country'];
    $var_wine['country_id'] = $_REQUEST['country_id'];
    $var_wine['region'] = $_REQUEST['region'];
    $var_wine['region_id'] = $_REQUEST['region_id'];
    $var_wine['subregion'] = $_REQUEST['subregion'];
    $var_wine['subregion_id'] = $_REQUEST['subregion_id'];
    $var_wine['user_id'] = $_SESSION['user_id'];


    //save wine
    log_write("save wine to db",1,"save_to_db");

    $wine_obj = new wine();

    if($wine_id>0){
        //confirm wine_id exists in db (protect against clicking back in browser
        //after wine has been deleted
        if (exists_wine($_REQUEST['wine_id'])==false){
            //trying to update a delete wine - abort
            $var_result['success']=false;
            $var_result['error']=40;
            return $var_result;
        }

        //existing wine - perform SQL update
        log_write("perform UPDATE",1,"save_to_db");
        $where = "wine_id = $wine_id";
        $result = $wine_obj -> update($var_wine, $where);
        log_write("UPDATE result: $result",1,"save_to_db");

        if($result){
            log_write("UPDATE successful",1,"save_to_db");
            $var_result['success']=true;
            $var_result['save_type']='db update';
            $var_result['wine_id']=$wine_id;
            return $var_result;
        } else {
            //update failed
            log_write("UPDATE failed",3,"save_to_db");
            $var_result['success']=false;
            $var_result['error']=30;
            $var_result['save_type']='db update';
            return $var_result;
        }
        
    } else {
        //new wine - perform SQL insert
        log_write("perform insert",1,"save_to_db");
        log_write("override_duplicate=$override_duplicate",1,"save_to_db");
        //check if wine is unique
        if($override_duplicate == 'false'){
            log_write("check for duplicate",1,"save_to_db");
            $where = "wine = \"".$wine_name."\" AND winetype_id = $winetype_id AND producer_id = $producer_id";
            log_write("where statement: $where",1,"save_to_db");
            $obj = new wine();
            $var_wine_result = $obj -> get($where);
            log_write("query returned: $var_wine_result",1,"save_to_db");

            if($var_wine_result){
                //match found - wine with these values already exists
                log_write("match found - return error code 10",1,"save_to_db");
                $var_result['success']=false;
                $var_result['error']=10;
                $var_result['error_msg']='A wine with this name, producer and winetype already exists!';
                return $var_result;
            }

            log_write("no match found - continue with save",1,"save_to_db");

        }

        //insert wine
        $result = $wine_obj -> insert($var_wine);
        log_write("save result: $result",1,"save_to_db");

        //save successful?
        if($result>0){
            log_write("wine saved successfully",1,"save_to_db");
            $var_result['success']=true;
            $var_result['wine_id']=$result;
            $var_result['save_type']='db insert';
        } else {
            log_write("ERROR: wine save unsuccessfull",3,"save_to_db");
            $var_result['success']=false;
            $var_result['error']=30;
        }
    }
    
    return $var_result;
}


function add_producer_db($producer_name){
    //Add new producer to db
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }

    
    if(!$producer_name){
        $producer_name = $_REQUEST['value']; //retrieve producer_name from POST
    }
    
    if(!$producer_name){ //producer_name empty
        $var_result['success']=false;
        $var_result['error']="Producer name cannot be blank.";
        return $var_result;
    }
    
    //check if producer already exists
    $producer_obj = new producer();
    $var_producer = $producer_obj -> get("producer='$producer_name'");

    if($var_producer[0]['producer_id']>0){
        //producer already exists

        $var_result['success']=false;
        $var_result['error']="Producer name '$producer_name' already exists";
        return $var_result;
    } else {
        //producer does not exist - add producer
  
        //create array
        $var_producer['producer'] = $producer_name;
        $var_producer['user_id'] = $_SESSION['user_id'];
        
        //insert new producer to db
        $producer_obj = new producer();
        $key = $producer_obj -> insert($var_producer);

        if($key>0){
            $var_result['success']=true;
            $var_result['producer_id']=$key;
            return $var_result;
        }else{
            $mysql_error = mysql_error();
            $var_result['success']=false;
            $var_result['error']= "Problem inserting producer '$producer_name' into db. mysql error = $mysql_error";
            return $var_result;
        }
    }
}


function vintage_count(){
    //count vintages in wine

    $wine_id = $_REQUEST['wine_id'];

    if($wine_id>0){
        $obj = new vintage();
        $where = "wine_id = $wine_id";
        $columns = "Count(*)";
        $result = $obj -> get($where, $columns);
        $count = $result[0]['Count(*)'];
        log_write("result = $count",1,"action='vintage_has'");
        $var_result['success'] = true;
        $var_result['vintage_count'] = $count;
        return $var_result;
    } else {
        $var_result['success'] = false;
        $var_result['error'] = 20;
        return $var_result;
    }
    
}


function delete_wine(){
    //delete wine
    
    $wine_id = $_REQUEST['wine_id'];

    if($wine_id>0){    
        //has wine got vintages
        $var_return = vintage_count();
        $num_vintages = $var_return['vintage_count'];
        
        if($num_vintages==0){
            //delete wine
            $obj = new wine();
            $where = "wine_id = $wine_id";
            $result = $obj -> delete($where); //delete wine

            if($result){ //delete was successful
                $var_result['success'] = true;
                unset($_SESSION['var_wine_temp']); //clear session
                $_SESSION['var_wine_temp']['status'] = 4; //set session status to deleted for reload of wine page
                return $var_result;
            } else {
                //delete failed
                $sql_error = $obj->get_sql_error();
                $var_result['success'] = false;
                $var_result['error'] = "Wine delete failed with sql_error: $sql_error";
                return $var_result;
            }
            
        } else {
            //wine has vintages return an error
            $var_result['success'] = false;
            $var_result['error'] = "Wine has $num_vintages vintages associated which must be deleted first";
            return $var_result;
        }
    } else {
        $var_result['success'] = false;
        $var_result['error'] = 'no wine_id provided delete aborted';
        return $var_result;
    }
    
    
};


function get_wine_session(){
    //get wine details from session
    
    $var_wine = $_SESSION['var_wine_temp'];
    
    if($var_wine){
        $var_result['success'] = true;       
        $var_result['wine_id'] = $var_wine['wine_id'];
        $var_result['status'] = $var_wine['status'];       
        $var_result['var_wine'] = $var_wine; //return var_wine session as json array
        return $var_result;
        
    }else{
        $var_result['success'] = false;
        $var_result['error'] = 'var_wine_temp session is empty';
        return $var_result;   
    }
 

};




function get_regions(){
    //return list of regions for given country to populate select options on wine form
    $index = $_REQUEST['country_id'];
    
    if($index>0){
        //get list of regions for country index from db
        $obj = new region();
        $columns = "region_id, region";
        $sort = "region ASC";
        $where = "country_id=".$index;
        $var_array = $obj ->get($where, $columns,false,$sort);
        $row_count = $obj ->count();
       
    } else {
        //no index provided return all results
        $obj = new region();
        $columns = "region_id, region";
        $sort = "region ASC";
        //$where = "country_id=".$index;
        $where = null;
        $var_array = $obj ->get($where, $columns,false,$sort);   
    }
    
    $row_count = $obj->count();
    
    if(!$var_array && $row_count===null){
        //error running query
        $var_result['success'] = false;
        $var_result['error'] = "error returning regions for country_id=$index error: ".$obj->get_sql_error();
    }elseif($row_count==0){
        $var_result['success'] = true;
        $var_result['msg'] = 'No records returned';
        $var_result['json_array'] = null;
    }else{
        $var_result['success'] = true;
        $var_result['json_array'] = $var_array;
    }

    return $var_result;
    
};


function get_subregions(){
    //return list of subregions for given region to populate select options on wine form
    $index = $_REQUEST['region_id'];
    $where = $index > 0 ? "region_id = ".$index : null;

    //get list of subregions from db
    $obj = new subregion();
    $columns = "subregion_id, subregion";
    $sort = "subregion ASC";
    $var_array = $obj ->get($where,$columns,false,$sort);
    $row_count = $obj->count();
    
    if(!$var_array && $row_count===null){
        //error running query
        $var_result['success'] = false;
        $var_result['error'] = "error returning subregions for region_id=$index sql_error: ".$obj->get_sql_error();
    }elseif($row_count==0){
        $var_result['success'] = true;
        $var_result['msg'] = 'No records returned';
        $var_result['json_array'] = null;
    }else{
        $var_result['success'] = true;
        $var_result['json_array'] = $var_array;
    }
    
    return $var_result;

};




function put_search_to_session(){

    if($_REQUEST['var_search']){
        $_SESSION['var_wine_search_criteria'] = $_REQUEST['var_search'];
        $var_result['success'] = true;
    } else {
        $var_result['success']=false;
        $var_result['error']='no search criteria provided';
    }
    
    return $var_result;

};


function set_form_status(){

    log_write("proc start",1,"set_form_status'");
    if($_REQUEST['status']>0){
        $_SESSION['wine_form']['status'] = $_REQUEST['status'];
        $_SESSION['wine_form']['wine_id'] = $_REQUEST['wine_id'];
        $var_result['success'] = true;
        $var_result['status'] = $_SESSION['wine_form']['status'];
    } else {
        $var_result['success']=false;
        $var_result['error']='no status value provided';
    }
    
    return $var_result;    

};


function get_country_id(){
    //return id for country text

    if($_REQUEST['text']){
        $obj = new country();
        $where = " country = '".$_REQUEST['text']."'";
        $result = $obj -> get($where);
        
        if($result){
            //match found
            $id =  $result[0]['country_id'];
            $var_result['success']= true;
            $var_result['id']= $id;
            
        } else {
            $var_result['success']= false;
            $var_result['error']= 'no match found';
        }
        
    } else {
        //no text provided
        $var_result['success'] = false;
        $var_result['error'] = 'no text provided';
    }
    
    return $var_result;

};

function get_region_for_subregion(){
    //return region id and label for a given subregion id
    
    if(!$_REQUEST['id']){
         //no text provided
        $var_result['success'] = false;
        $var_result['error'] = 'no id provided';
        return $var_result;
    }
    
    $obj = new subregion(); //get region id for subregion id
    $where = " subregion_id = '".$_REQUEST['id']."'";
    $result = $obj -> get($where);

    if(!$result){
        //no matching results
        $sql_error = $obj->get_sql_error();
        $var_result['success']= false;
        $var_result['error']= "db results false error: $sql_error";
        return $var_result;
    }
    
    $region_id = $result[0]['region_id'];

    $obj_region = new region(); //get region details for region_id
    $where = " region_id = '".$region_id."'";
    $rst_region = $obj_region -> get($where); 
    
    if(!$rst_region){
        //no matching results
        $sql_error = $obj_region->get_sql_error();
        $var_result['success']= false;
        $var_result['error']= "db results false error: $sql_error";
        return $var_result;
    }
    
    //match found
    $var_result['region_id'] =  $rst_region[0]['region_id'];
    $var_result['region']=  $rst_region[0]['region'];
    $var_result['country_id'] = $rst_region[0]['country_id'];
    
    //get country details as well
    $var_country = get_country_for_region($var_result['region_id']);
    if($var_country['success']){
        $var_result['country'] = $var_country['country'];
    }

    $var_result['success']= true;
    return $var_result;
 
}


function get_country_for_region($region_id){
    //return region id and label for a given subregion id
    
    $region_id = ($region_id > 0 ? $region_id : $_REQUEST['id']);
    
    if(!$region_id){
         //no text provided
        $var_result['success'] = false;
        $var_result['error'] = 'no region id provided';
        return $var_result;
    }
    
    $obj_region = new region(); //get country id for region id
    $where = " region_id = '".$region_id."'";
    $rst_region = $obj_region -> get($where);

    if(!$rst_region){
        //no matching results
        $sql_error = $obj_region->get_sql_error();
        $var_result['success']= false;
        $var_result['error']= "db results false error: $sql_error";
        return $var_result;
    }
    
    $country_id = $rst_region[0]['country_id'];

    $obj_country = new country(); //get country details for country_id
    $where = " country_id = '".$country_id."'";
    $rst_country = $obj_country -> get($where); 
    
    if(!$rst_country){
        //no matching results
        $sql_error = $obj_country->get_sql_error();
        $var_result['success']= false;
        $var_result['error']= "db results false error: $sql_error";
        return $var_result;
    }
    
    //match found
    $var_result['country_id'] =  $rst_country[0]['country_id'];
    $var_result['country']=  $rst_country[0]['country'];

    $var_result['success']= true;
    return $var_result;
 
}

function get_wine_count_for_producer(){
    //return count of wines associated with a producer
    
    
    $producer_id = (filter_input(INPUT_POST, 'producer_id', FILTER_SANITIZE_STRING) > "") ? filter_input(INPUT_POST, 'producer_id', FILTER_SANITIZE_STRING) : filter_input(INPUT_GET, 'producer_id', FILTER_SANITIZE_STRING);
    
    if(empty($producer_id) || !is_numeric($producer_id) ){
        $var_result['success']= false;
        $var_result['error']= "no producer_id provided";
        return $var_result;
    }
    
    $producerObj = new wbProducer();
    $count = $producerObj->getWineCount($producer_id);
    if($count < 0){
        //sql error
        $var_result['success']= false;
        $var_result['error']= $producerObj->lastErrorMessage;
        return $var_result;
    }
    
    $var_result['success']= true;
    $var_result['count']= $count;
    return $var_result;
    
}

?>
