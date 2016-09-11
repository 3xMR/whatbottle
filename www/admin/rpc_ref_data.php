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


function country_not_duplicate($country){
    //check for duplicate country
    $obj_country = new country();
    $country_name =  mysql_real_escape_string($country);
    $where = "country = '$country_name'";
    $count = $obj_country -> row_count($where);
    if($count > 0){ //duplicate
        $var_result['success'] = false;
        $var_result['error'] = "duplicate - data already added - count=$count";
        return $var_result;
    } else { 
        $var_result['success'] = true;
        return $var_result;
    }
}


function save_country_db(){
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }    
    
    $country_text = $_REQUEST['country_text'];
    $country_id = $_REQUEST['country_id'];
    $flag_image = $_REQUEST['country_flag'];
    
    if(empty($_REQUEST['country_text'])){ //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'Country name is empty nothing to save';
        return $var_result;
    }
    
    if($country_id>0){
        $sql_id = " AND country_id <> $country_id ";
    } else {
        $sql_id = null;
    }
    
    $obj = new country();
    $where = sprintf("country = '%s' $sql_id ",mysql_real_escape_string($country_text));
    $count = $obj -> row_count($where);
    if($count > 0){
        //duplicate
        $var_result['success'] = false;
        $var_result['error'] = "Country name already exists";
        return $var_result;
    } else {

        $var_input['country'] = $country_text;
        $var_input['flag_image'] = $flag_image;
        $var_input['user_id'] = $_SESSION['user_id'];

        if($country_id>0){
            //update
            $where = "country_id = $country_id";
            $result = $obj -> update($var_input,$where);

            if($result){
                $var_result['success'] = true;
                $var_result['db_action'] = 'update';
                $var_result['country_id'] = $country_id;
                return $var_result;
            }else{
                $sql_error = $obj ->get_sql_error();
                $var_result['success'] = false;
                $var_result['error'] = 'Save Country DB UPDATE failed with error: '+$sql_error;
                $var_result['db_action'] = 'update';
                return $var_result;
            }

        }else{ //insert
            $country_id = $obj -> insert($var_input);
            if ($country_id > 0){
                $var_result['success'] = true;
                $var_result['country_id'] = $country_id;
                $var_result['db_action'] = 'insert';
                return $var_result;
            } else {
                $sql_error = $obj -> get_sql_error();
                $var_result['success'] = false;
                $var_result['error'] = 'Save Country DB INSERT failed with error: '+$sql_error;
                $var_result['db_action'] = 'insert';
                return $var_result;
            }
        }//country_id
        
    } //count

}



function save_region_db(){
    
    $region_text = $_REQUEST['region_text'];
    $region_id = $_REQUEST['region_id'];
    $country_id = $_REQUEST['country_id'];
    
    $_SESSION['var_vintage_temp']['region_text'] = $region_text;
    
    if(!empty($_REQUEST['region_text'])){
        
        if($region_id > 0){
            $sql_id = " AND region_id <> $region_id ";
        } else {
            $sql_id = null;
        }

        //check for duplicate
        $obj_region = new region();
        $where = sprintf("region = '%s' AND country_id = $country_id $sql_id ",mysql_real_escape_string($region_text));
        $count = $obj_region -> row_count($where);

        if($count > 0){
            //duplicate
            $var_result['success'] = false;
            $var_result['error'] = "Region already exists";
            return $var_result;
        } else {
            //create input array
            $var_input['region'] = $region_text;
            $var_input['country_id'] = $country_id;
            $var_input['user_id'] = $_SESSION['user_id'];

            if($region_id>0){
                //update
                $where = "region_id = $region_id";
                $result = $obj_region -> update($var_input,$where);
                if($result){
                    $var_result['success'] = true;
                    $var_result['region_id'] = $region_id;
                    $var_result['db_action'] = 'update';
                    return $var_result;
                }else{
                    $var_result['success'] = false;
                    $var_result['error'] = 'DB update failed';
                    $var_result['db_action'] = 'update';
                    return $var_result;
                }
                
            }else{
                //insert
                if($country_id >0){ //requires valid country_id to add region
                    $region_id = $obj_region -> insert($var_input);
                    if ($region_id > 0){
                        $var_result['success'] = true;
                        $var_result['region_id'] = $region_id;
                        $var_result['db_action'] = 'insert';
                        return $var_result;
                    } else {
                        $var_result['success'] = false;
                        $var_result['error'] = 'db insert failed';
                        $var_result['db_action'] = 'insert';
                        return $var_result;
                    }
                } else {
                    //insert with no country_id
                    $var_result['success'] = false;
                    $var_result['error'] = 'db INSERT failed because no country_id provided';
                    return $var_result;
                }
            }
            
        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'Insufficient data to proceed with save Region';
        return $var_result;
    }
    
}


function save_subregion_db(){

    $subregion_text = $_REQUEST['subregion_text'];
    $subregion_id = $_REQUEST['subregion_id'];
    $region_id = $_REQUEST['region_id'];

    if(!empty($_REQUEST['subregion_text'])){
        
        if($subregion_id > 0){
            $sql_id = " AND subregion_id <> $subregion_id ";
        } else {
            $sql_id = null;
        }

        //check for duplicate
        $obj_subregion = new subregion();
        $where = sprintf("subregion = '%s' AND region_id = $region_id $sql_id ",mysql_real_escape_string($subregion_text));
        $count = $obj_subregion -> row_count($where);
        
        if($count > 0){
            //duplicate
            $var_result['success'] = false;
            $var_result['error'] = "Subregion already exists";
            return $var_result;
        } else {
            
            $var_input['subregion'] = $subregion_text;
            $var_input['region_id'] = $region_id;
            $var_input['user_id'] = $_SESSION['user_id'];

            if($subregion_id>0){ //update db
                
                $where = "subregion_id = $subregion_id";
                $result = $obj_subregion -> update($var_input,$where);
                if($result){
                    $var_result['success'] = true;
                    $var_result['subregion_id'] = $subregion_id;
                    $var_result['db_action'] = 'update';
                    return $var_result;
                }else{
                    $sql_error = $obj_subregion ->get_sql_error();
                    $var_result['success'] = false;
                    $var_result['error'] = "Save Subregion db update failed with sql_error: $sql_error";
                    $var_result['db_action'] = 'update';
                    return $var_result;
                }

            }else{ //insert db
                
                if($region_id >0){
                    $subregion_id = $obj_subregion -> insert($var_input);
                    if ($subregion_id > 0){
                        $var_result['success'] = true;
                        $var_result['subregion_id'] = $subregion_id;
                        $var_result['db_action'] = 'insert';
                        return $var_result;
                    } else {
                        $sql_error = $obj_subregion ->get_sql_error();
                        $var_result['success'] = false;
                        $var_result['error'] = "Save Subregion db insert failed with sql_error: $sql_error";
                        $var_result['db_action'] = 'insert';
                        return $var_result;
                    }
                } else {
                    //insert with no country_id
                    $var_result['success'] = false;
                    $var_result['error'] = 'db insert failed because no region_id provided';
                    return $var_result;
                }
            }

        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'Subregion name cannot be blank';
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


function add_region(){
    
    $country_id = $_REQUEST['country_id'];
    $region = $_REQUEST['region'];
    $_SESSION['var_vintage_temp']['region_text'] = $region;

    if($country_id > 0 && $region){
   
        //check for duplicate
        $obj_region = new region();
        $where = sprintf("country_id = $country_id AND region = '%s' ",mysql_real_escape_string($region));
        $count = $obj_region -> row_count($where);
        if($count>0){
            //duplicate
            $var_result['success'] = false;
            $var_result['error'] = 'Region name already exists';
            return $var_result;
        } else {
            //add to db
            $var_input['country_id'] = $country_id;
            $var_input['region'] = $region;
            $var_input['user_id'] = $_SESSION['user_id'];
            
            $region_id = $obj_region -> insert($var_input);
            
            if ($region_id > 0){
                $var_result['success'] = true;
                $var_result['region_id'] = $region_id;
                return $var_result;
            } else {
                $var_result['success'] = false;
                $var_result['error'] = 'db insert failed';
                return $var_result;
            }
            
        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no data to add';
        return $var_result;
    }
}


function add_subregion(){

    $region_id = $_REQUEST['region_id'];
    $subregion = $_REQUEST['subregion'];

    if($region_id > 0 && $subregion){
        //check for duplicate
        $obj_subregion = new subregion();
        $where = sprintf("region_id = $region_id AND subregion = '%s' ",mysql_real_escape_string($subregion));
        $count = $obj_subregion -> row_count($where);

        if($count>0){
            //duplicate
            $var_result['success'] = false;
            $var_result['error'] = 'Subregion name already exists';
            return $var_result;
        } else {

            //add to db
            $var_input['region_id'] = $region_id;
            $var_input['subregion'] = $subregion;
            $var_input['user_id'] = $_SESSION['user_id'];
            $subregion_id = $obj_subregion -> insert($var_input);

            if ($subregion_id > 0){
                $var_result['success'] = true;
                $var_result['subregion_id'] = $subregion_id;
                return $var_result;
            } else {
                $var_result['success'] = false;
                $var_result['error'] = 'db insert failed';
                return $var_result;
            }

        }

    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no data to add';
        return $var_result;
    }
}




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


function merchant_exists($merchant, $merchant_id = false){
    //check for duplicate merchant
    
    if($merchant_id > 0){ //include merchant_id in query if provided
        $sql_merchant_id = " AND merchant_id <> $merchant_id ";
    } else {
        $sql_merchant_id = null;
    }
    $obj = new merchant();
    $where = sprintf("merchant = '%s' $sql_merchant_id ",mysql_real_escape_string($merchant));
    $result = $obj ->get($where);
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

    if(!empty($_REQUEST['value'])){

        //check for duplicate
        $obj = new grape();
        $where = sprintf("grape = '%s' AND colour = '$colour' $sql_id ",mysql_real_escape_string($value));
        $count = $obj -> row_count($where);
        if($count > 0){
            //duplicate
            $var_result['success'] = false;
            $var_result['error'] = "Grape name and colour already exists";
            return $var_result;
        } else {
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

            }else{ //db insert
                if($value){
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

        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no Grape name provided';
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

    $value = $_REQUEST['value'];
    $id = $_REQUEST['id'];

    if(!empty($_REQUEST['value'])){

        //check for duplicate
        $obj = new producer();
        if($id>0){
            $sql_id = " AND producer_id <> $id ";
        } else {
            $sql_id = null;
        }
        $where = sprintf("producer = '%s' $sql_id ",mysql_real_escape_string($value));
        //$where = "producer = '".mysql_real_escape_string($value)."'";
        $result = $obj -> get($where);
        if($result){
            //duplicate
            $var_result['success'] = false;
            $var_result['error'] = "Producer name already exists";
            return $var_result;
        } else {
            //determine if insert or update
            $var_input['producer'] = $value;
            $var_input['user_id'] = $_SESSION['user_id'];

            if($id>0){
                //update
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

            }else{
                //insert
                if($value){
                    $id = $obj -> insert($var_input);
                    if ($id > 0){
                        $var_result['success'] = true;
                        $var_result['id'] = $id;
                        $var_result['db_action'] = 'insert';
                        return $var_result;
                    } else {
                        $var_result['success'] = false;
                        $var_result['error'] = "db insert failed id=$id value=$value";
                        $var_result['db_action'] = 'insert';
                        return $var_result;
                    }
                } else {
                    //insert with no text
                    $var_result['success'] = false;
                    $var_result['error'] = 'db insert failed because no text provided';
                    return $var_result;
                }
            }

        }
 
    } else {
        //no data to add
        $var_result['success'] = false;
        $var_result['error'] = 'no data to add';
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
