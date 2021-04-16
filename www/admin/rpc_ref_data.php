<?php
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

if(!is_authed()){ //check if user is authorised
    $var_result['success'] = false;
    $var_result['error'] = "You must login to use this application";
    echo json_encode($var_result);
    exit;
}  

if($_REQUEST['rpc_action'] || $_REQUEST['action']){ //convert action to function call
    if($_REQUEST['rpc_action']){
        $fnc = $_REQUEST['rpc_action'];
    }else{
        $fnc = $_REQUEST['action'];
    }
    if(is_callable($fnc)){ //call action as function
        $var_result = call_user_func($fnc);
        echo json_encode($var_result); //output returned results as json response
    }else{
        $var_result['error'] = "function [$fnc] not found on server page [".$_SERVER['PHP_SELF']."]";
        $var_result['success'] = false;
        echo json_encode($var_result);
    }

}else{
        $var_result['error'] = "No action provided";
        $var_result['success'] = false;
        echo json_encode($var_result);
}


//function country_not_duplicate($country){
//    //check for duplicate country
//    $obj_country = new country();
//    $country_name =  mysql_real_escape_string($country);
//    $where = "country = '$country_name'";
//    $count = $obj_country -> row_count($where);
//    if($count > 0){ //duplicate
//        $var_result['success'] = false;
//        $var_result['error'] = "duplicate - data already added - count=$count";
//        return $var_result;
//    } else { 
//        $var_result['success'] = true;
//        return $var_result;
//    }
//}


function save_country_db(){
    
    $country_text = $_REQUEST['country_text'] ?? null;
    $country_id = $_REQUEST['country_id'] ?? null;
    $flag_image = $_REQUEST['country_flag'] ?? null;
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }    
    
    if(!isset($country_text)){ //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'Country name not provided, cannot continue';
        return $var_result;
    }
    
    if($country_id>0){
        $where = " country = :country AND country_id <> :country_id ";
        $input_array = [':country' => $country_text, ':country_id' => $country_id];
    } else {
        $where = " country = :country ";
        $input_array = [':country' => $country_text];
    }
    
    $obj = new country();
    $result = $obj ->get($where,null,null,null,null,$input_array);
    
    if($result){ //duplicate
        $var_result['success'] = false;
        $var_result['error'] = "Country name already exists";
        return $var_result;
    }
    
    //complete input_array 
    $input_array['country'] = $country_text;   
    $input_array['user_id'] = $_SESSION['user_id'];
    if(isset($flag_image)){
       $input_array['flag_image'] = $flag_image; //if not set omit so that override in class.db operates
    }

    if($country_id>0){ //update db
        
        $where = "country_id = $country_id";
        $result = $obj -> update($input_array,$where);

        if($result){
            $var_result['success'] = true;
            $var_result['db_action'] = 'update';
            $var_result['country_id'] = $country_id;
            return $var_result;
        }else{
            $var_result['success'] = false;
            $var_result['error'] = 'Save Country DB UPDATE failed with error: '.$obj ->get_sql_error();
            $var_result['db_action'] = 'update';
            return $var_result;
        }
    }

    $new_country_id = $obj -> insert($input_array);
    
    if ($new_country_id > 0){
        $var_result['success'] = true;
        $var_result['country_id'] = $new_country_id;
        $var_result['db_action'] = 'insert';
        return $var_result;
    } else {
        $var_result['success'] = false;
        $var_result['error'] = 'Save Country DB INSERT failed with error: '.$obj -> get_sql_error();
        $var_result['db_action'] = 'insert';
        return $var_result;
    }

}



function save_region_db(){
    
    $region_text = $_REQUEST['region_text'] ?? null;
    $region_id = $_REQUEST['region_id'] ?? null;
    $country_id = $_REQUEST['country_id'] ?? null;
    
    $_SESSION['var_vintage_temp']['region_text'] = $region_text;
    
    if(!isset($region_text)){
        $var_result['success'] = false;
        $var_result['error'] = 'no Region name provided, cannot continue';
        return $var_result;
    }
    
    if(!isset($country_id)){
        $var_result['success'] = false;
        $var_result['error'] = 'no parent Country id provided, cannot continue';
        return $var_result;
    }
        
    if($region_id > 0){
        $where = " region = :region AND country_id = :country_id AND region_id <> :region_id ";
        $input_array = ['region' => $region_text, 'region_id' => $region_id, 'country_id' => $country_id];
    } else {
        $where = " region = :region AND country_id = :country_id ";
        $input_array = ['region' => $region_text, 'country_id' => $country_id];
    }

    //check for duplicate
    $obj = new region();
    $result = $obj ->get($where,null,null,null,null,$input_array);
    
    if($result){ //duplicate
        $var_result['success'] = false;
        $var_result['error'] = "Region name already exists";
        return $var_result;
    }
            
    $input_array['user_id'] = $_SESSION['user_id'];//add user_id to input array

    if($region_id>0){ //db update     
        $where = "region_id = $region_id";
        $result = $obj -> update($input_array,$where);
        if($result){
            $var_result['success'] = true;
            $var_result['region_id'] = $region_id;
            $var_result['db_action'] = 'update';
            return $var_result;
        }else{
            $var_result['success'] = false;
            $var_result['error'] = 'DB update failed with error: '.$obj->get_sql_error();
            $var_result['db_action'] = 'update';
            return $var_result;
        }
    }
        
    $new_region_id = $obj -> insert($input_array); //db insert
    if ($new_region_id > 0){
        $var_result['success'] = true;
        $var_result['region_id'] = $new_region_id;
        $var_result['db_action'] = 'insert';
        return $var_result;
    } else {
        $var_result['success'] = false;
        $var_result['error'] = 'db insert failed with error: '.$obj->get_sql_error();
        $var_result['db_action'] = 'insert';
        return $var_result;
    }
          
}


function save_subregion_db(){

    $subregion_text = $_REQUEST['subregion_text'] ?? null;
    $subregion_id = $_REQUEST['subregion_id'] ?? null;
    $region_id = $_REQUEST['region_id'] ?? null;
    
    if(!isset($subregion_text)){
         $var_result['success'] = false;
         $var_result['error'] = "Subregion name not provided, cannot continue";
         return $var_result;
    }
    
    if(!isset($region_id) || empty($region_id)){
         $var_result['success'] = false;
         $var_result['error'] = "Parent region id not provided, cannot continue";
         return $var_result;
    }
           
    if($subregion_id > 0){
        $where = " subregion = :subregion AND region_id = :region_id AND subregion_id <> :subregion_id ";
        $input_array = ['subregion' => $subregion_text, 'region_id' => $region_id, 'subregion_id' => $subregion_id];
    } else {
        $where = " subregion = :subregion AND region_id = :region_id ";
        $input_array = ['subregion' => $subregion_text, 'region_id' => $region_id];
    }

    //check for duplicate
    $obj = new subregion();
    $result = $obj ->get($where,null,null,null,null,$input_array);
    
    if($result){ //duplicate
        $var_result['success'] = false;
        $var_result['error'] = "Subregion name already exists";
        return $var_result;
    }
 
    $input_array['user_id'] = $_SESSION['user_id'];

    if($subregion_id>0){ //update db
        $where = "subregion_id = $subregion_id";
        $result = $obj-> update($input_array,$where);
        if($result){
            $var_result['success'] = true;
            $var_result['subregion_id'] = $subregion_id;
            $var_result['db_action'] = 'update';
            return $var_result;
        }else{
            $sql_error = $obj->get_sql_error();
            $var_result['success'] = false;
            $var_result['error'] = "save_subregion_db(): Subregion db update failed with sql_error: $sql_error";
            $var_result['db_action'] = 'update';
            return $var_result;
        }
    }

    $new_subregion_id = $obj -> insert($input_array); //db Insert
    if ($new_subregion_id > 0){
        $var_result['success'] = true;
        $var_result['subregion_id'] = $new_subregion_id;
        $var_result['db_action'] = 'insert';
        return $var_result;
    } else {
        $sql_error = $obj ->get_sql_error();
        $var_result['success'] = false;
        $var_result['error'] = "Save Subregion db insert failed with sql_error: $sql_error";
        $var_result['db_action'] = 'insert';
        return $var_result;
    }

}



function delete_country(){
    
    if(!empty($_REQUEST['country_id'])){
        //check for associated wines
        $obj_wine = new wine();
        $where = "country_id = '".$_REQUEST['country_id']."'";
        $wine_count = $obj_wine -> row_count($where);
        
        if($wine_count>0){
            //country has wines or regions associated
            $var_result['success'] = false;
            $var_result['error'] = 'has_children';
            return $var_result;
        } else {
            //delete
            $obj_country =  new country();
            $result = $obj_country ->delete($where);
            if ($result){
                $var_result['success'] = true;
                return $var_result;
            } else {
                $var_result['success'] = false;
                $var_result['error'] = 'delete failed';
                return $var_result;
            }
            
        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no country_id provided';
        return $var_result;
    }
    
}


function delete_region(){
    
    if(!empty($_REQUEST['region_id'])){
        //check for associated wines
        $obj_wine = new wine();
        $where = "region_id = '".$_REQUEST['region_id']."'";
        $count = $obj_wine -> row_count($where);
        
        if($count>0){
            //region has wines associated
            $var_result['success'] = false;
            $var_result['error'] = 'has_children';
            return $var_result;
        } else {
            //delete
            $obj_region =  new region();
            $result = $obj_region ->delete($where);
            if ($result){
                $var_result['success'] = true;
                return $var_result;
            } else {
                $var_result['success'] = false;
                $var_result['error'] = 'delete failed';
                return $var_result;
            }
            
        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no region_id provided';
        return $var_result;
    }
    
}


//function add_region(){
//    
//    $country_id = $_REQUEST['country_id'];
//    $region = $_REQUEST['region'];
//    $_SESSION['var_vintage_temp']['region_text'] = $region;
//
//    if($country_id > 0 && $region){
//   
//        //check for duplicate
//        $obj_region = new region();
//        $where = sprintf("country_id = $country_id AND region = '%s' ",mysql_real_escape_string($region));
//        $count = $obj_region -> row_count($where);
//        if($count>0){
//            //duplicate
//            $var_result['success'] = false;
//            $var_result['error'] = 'Region name already exists';
//            return $var_result;
//        } else {
//            //add to db
//            $var_input['country_id'] = $country_id;
//            $var_input['region'] = $region;
//            $var_input['user_id'] = $_SESSION['user_id'];
//            
//            $region_id = $obj_region -> insert($var_input);
//            
//            if ($region_id > 0){
//                $var_result['success'] = true;
//                $var_result['region_id'] = $region_id;
//                return $var_result;
//            } else {
//                $var_result['success'] = false;
//                $var_result['error'] = 'db insert failed';
//                return $var_result;
//            }
//            
//        }
// 
//    } else {
//        //no data to add
//        $var_result['success'] = false;
//        $var_result['error'] = 'no data to add';
//        return $var_result;
//    }
//}


//function add_subregion(){
//
//    $region_id = $_REQUEST['region_id'] ?? null;
//    $subregion = $_REQUEST['subregion'] ?? null;
//
//    if(!isset($region_id) || !isset($subregion)){ //need both region and subregion to continue
//        $var_result['success'] = false;
//        $var_result['error'] = 'Parameters incomplete, cannot continue';
//        return $var_result;
//    }
//    
//    //check for duplicate
//    $obj = new subregion();
//    $where = " region_id = :region_id AND subregion = :subregion";
//    $input_array = [':region_id' => $region_id, ':subregion' => $subregion];
// 
//    $result = $obj ->get($where,null,null,null,null,$input_array);
//    
//    if($result){ //duplicate
//        $var_result['success'] = false;
//        $var_result['error'] = "Subregion name already exists";
//        return $var_result;
//    }
//    
//    //add to db
//    $var_input['region_id'] = $region_id;
//    $var_input['subregion'] = $subregion;
//    $var_input['user_id'] = $_SESSION['user_id'];
//    $subregion_id = $obj -> insert($var_input);
//
//    if ($subregion_id > 0){
//        $var_result['success'] = true;
//        $var_result['subregion_id'] = $subregion_id;
//        return $var_result;
//    } else {
//        $var_result['success'] = false;
//        $var_result['error'] = 'db insert failed';
//        return $var_result;
//    }   
//
//}




function delete_subregion(){
    
    if(!empty($_REQUEST['subregion_id'])){
        //check for associated wines
        $obj_wine = new wine();
        $where = "subregion_id = '".$_REQUEST['subregion_id']."'";
        $count = $obj_wine -> row_count($where);
        
        if($count>0){
            //subregion has wines associated
            $var_result['success'] = false;
            $var_result['error'] = 'has_children';
            return $var_result;
        } else {
            //delete
            $obj_subregion =  new subregion();
            $result = $obj_subregion ->delete($where);
            if ($result){
                $var_result['success'] = true;
                return $var_result;
            } else {
                $var_result['success'] = false;
                $var_result['error'] = 'delete failed';
                return $var_result;
            }
            
        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no subregion_id provided';
        return $var_result;
    }
    
}


function delete_merchant(){
    
    if(!empty($_REQUEST['index'])){
        //check for associated acquisitions
        $obj_acquire = new acquire();
        $where = "merchant_id = ".$_REQUEST['index'];
        $count = $obj_acquire -> row_count($where);
        
        if($count>0){
            //associated records exist
            $var_result['success'] = false;
            $var_result['error'] = 'has_children';
            return $var_result;
        } else {
            //delete
            $obj_merchant =  new merchant();
            $result = $obj_merchant -> delete($where);
            if ($result){
                $var_result['success'] = true;
                return $var_result;
            } else {
                $sql_error = $obj -> get_sql_error();
                $var_result['success'] = false;
                $var_result['error'] = "db delete failed - sql_error: $sql_error";
                return $var_result;
            }
            
        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no merchant_id provided';
        return $var_result;
    }
    
}


function merchant_exists($merchant, $merchant_id = null){
    //check for duplicate merchant
    
    if(!isset($merchant)){
        $var_result['success'] = false;
        $var_result['error'] = 'merchant_exists(): Merchant name not provided, cannot continue';
        return $var_result;
    }

    if(isset($merchant_id)){ //include merchant_id in query if provided
        $where = " merchant = :merchant AND merchant_id <> :merchant_id ";
        $input_array = ['merchant' => $merchant, 'merchant_id' => $merchant_id];
    } else {
        $where = " merchant = :merchant ";
        $input_array = ['merchant' => $merchant];
    }
    
    $obj = new merchant();
    $result = $obj ->get($where,null,null,null,null,$input_array);
    
    if($result){ //exists
        $var_result['success'] = true;
        $var_result['msg'] = "Merchant name already exists";
        $var_result['merchant_id'] = $result['merchant_id'];
        return $var_result;
    } else { 
        $var_result['success'] = false;
        $var_result['error'] = 'Merchant name does not already exist';
        return $var_result;
    }
    
}


function save_merchant_db(){
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }

    $merchant_name = $_REQUEST['merchant'];
    $merchant_id = $_REQUEST['merchant_id'];
      
    if(!$merchant_name){ //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no Merchant name provided';
        return $var_result;
    }
    
    $result = merchant_exists($merchant_name, $merchant_id); //check for duplicate use of name
    
    if($result['success'] == true){
        $var_result['success'] = false;
        $var_result['error'] = $result['msg'];
        return $var_result;
    }  
        
    $var_input['merchant'] = $merchant_name;
    $var_input['user_id'] = $_SESSION['user_id'];
    $obj_merchant = new merchant();
   
    if($merchant_id > 0){ //update DB
        $where = "merchant_id = $merchant_id";
        $result = $obj_merchant -> update($var_input,$where);
        if($result){
            $var_result['success'] = true;
            $var_result['db_action'] = 'update';
            $var_result['merchant_id'] = $merchant_id;
            return $var_result;
        }else{ //update failed - get sql error
            $sql_error = $obj_merchant -> get_sql_error();
            $var_result['success'] = false;
            $var_result['error'] = "db update failed - sql_error = $sql_error";
            $var_result['db_action'] = 'update';
            return $var_result;
        }

    }else{ //insert DB
        $id = $obj_merchant -> insert($var_input);
        if ($id > 0){
            $var_result['success'] = true;
            $var_result['merchant_id'] = $id;
            $var_result['db_action'] = 'insert';
            return $var_result;
        } else {
            //insert failed - get sql error
            $sql_error = $obj_merchant -> get_sql_error();
            $var_result['success'] = false;
            $var_result['error'] = "db insert failed id=$id text=$merchant_name sql_error=$sql_error";
            $var_result['db_action'] = 'insert';
            return $var_result;
        }
    }

} //save_merchant_db


function save_grape_db(){

    $value = $_REQUEST['value'];
    $id = $_REQUEST['id'];
    $colour = $_REQUEST['colour'];
    
    if($id>0){
        $sql_id = " AND grape_id <> $id ";
    } else {
        $sql_id = null;
    }

    if(empty($_REQUEST['value'])){//no data to add    
        $var_result['success'] = false;
        $var_result['error'] = 'save_grape_db(): no grape name provided, cannot continue';
        return $var_result;
    }

        //check for duplicate
        $obj = new grape();
        $where = "grape = '$value' AND colour = '$colour' $sql_id ";
        $count = $obj -> row_count($where);
        
        if($count > 0){
            //duplicate
            $var_result['success'] = false;
            $var_result['error'] = "Grape name and colour already exists";
            return $var_result;
        }
        
        //determine if insert or update
        $var_input['grape'] = $value;
        $var_input['colour'] = $colour;
        $var_input['user_id'] = $_SESSION['user_id'];

        if($id>0){
            //update
            $where = "grape_id = $id";
            $result = $obj -> update($var_input,$where);
            if($result){
                $var_result['success'] = true;
                $var_result['db_action'] = 'update';
                $var_result['id'] = $id;
                return $var_result;
            }else{
                //update failed - get sql error
                $sql_error = $obj -> get_sql_error();
                $var_result['success'] = false;
                $var_result['error'] = "db update failed - sql_error=$sql_error";
                $var_result['db_action'] = 'update';
                return $var_result;
            }

        }

        if($value){ //db insert
            $id = $obj -> insert($var_input);
            if ($id > 0){
                $var_result['success'] = true;
                $var_result['id'] = $id;
                $var_result['db_action'] = 'insert';
                return $var_result;
            } else {
                //insert failed - get sql error
                $sql_error = $obj -> get_sql_error();
                $var_result['success'] = false;
                $var_result['error'] = "db insert failed - sql_error=$sql_error";
                $var_result['db_action'] = 'insert';
                return $var_result;
            }
        } else {
            //insert with no text
            $var_result['success'] = false;
            $var_result['error'] = 'db insert failed because no text provided';
            $var_result['db_action'] = 'insert';
            return $var_result;
        }
        

        
 
} 




function delete_grape(){
    
    if(!empty($_REQUEST['index'])){
        //check for associated acquisitions
        $obj_grapes = new vintage_has_grape();
        $where = "grape_id = ".$_REQUEST['index'];
        $count = $obj_grapes -> row_count($where);
        
        if($count>0){
            //associated records exist
            $var_result['success'] = false;
            $var_result['error'] = 'has_children';
            return $var_result;
        } else {
            //delete
            $obj_grape =  new grape();
            $result = $obj_grape -> delete($where);
            if ($result){
                $var_result['success'] = true;
                return $var_result;
            } else {
                $var_result['success'] = false;
                $var_result['error'] = 'delete failed';
                return $var_result;
            }
            
        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no grape_id provided';
        return $var_result;
    }
    
}


function save_producer_db(){

    $id = $_REQUEST['id'] ?? null;
    $value = $_REQUEST['value'] ?? null;
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }
    
    if(!isset($_REQUEST['value'])){//no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'save_producer_db(): no producer value provided, cannot continue';
        return $var_result;
    }
    
    //check for duplicate
    $obj = new producer();
    if($id){
        $where = " producer = :producer AND producer_id <> :producer_id ";
        $inputArray = ['producer'=>$value, 'producer_id' => $id];
    } else {
        $where = " producer = :producer ";
        $inputArray = ['producer'=>$value];
    }

    $result = $obj ->get($where,null,null,null,null,$inputArray);
    
    if($result){
        //duplicate
        $var_result['success'] = false;
        $var_result['error'] = "Producer name already exists";
        return $var_result;
    }
    
    //determine if insert or update
    $var_input['producer'] = $value;
    $var_input['user_id'] = $_SESSION['user_id'];

    if($id>0){ //db update
        $where = "producer_id = $id";
        $result = $obj -> update($var_input,$where);
        if($result){
            $var_result['success'] = true;
            $var_result['db_action'] = 'update';
            $var_result['id'] = $id;
            return $var_result;
        }else{
            $var_result['success'] = false;
            $var_result['error'] = 'db update failed';
            $var_result['db_action'] = 'update';
            return $var_result;
        }
    }
        
    //insert
    $newId = $obj -> insert($var_input);
    if ($newId > 0){
        $var_result['success'] = true;
        $var_result['id'] = $newId;
        $var_result['db_action'] = 'insert';
        return $var_result;
    } else {
        $var_result['success'] = false;
        $var_result['error'] = "db insert failed id=$newId value=$value";
        $var_result['db_action'] = 'insert';
        return $var_result;
    }

}


function delete_producer(){
    
    if(!empty($_REQUEST['index'])){
        //check for associated acquisitions
        $obj_wine = new wine();
        $where = "producer_id = ".$_REQUEST['index'];
        $count = $obj_wine -> row_count($where);
        
        if($count>0){
            //associated records exist
            $var_result['success'] = false;
            $var_result['error'] = 'has_children';
            return $var_result;
        } else {
            //delete
            $obj_producer =  new producer();
            $result = $obj_producer -> delete($where);
            if ($result){
                $var_result['success'] = true;
                return $var_result;
            } else {
                $var_result['success'] = false;
                $var_result['error'] = 'delete failed';
                return $var_result;
            }
            
        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no grape_id provided';
        return $var_result;
    }
    
}

?>
