<?php
/*
 * Acquisition RPC functions
 *
 */
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


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
    
} else {
    $var_result['success'] = false;    
    $var_result['error'] = "'rpc_action' and 'action' missing - nothing to do";
    echo json_encode($var_result);
}

function exists($find, $array){
    $result = false;

    if(!empty($array)){
        //recursive search
        foreach($array as $key => $vintage){
            $vintage_id = $vintage['vintage_id'];

            if ($find == $vintage_id ){
                $result = $key;
            }
        }
    } 

    return $result;
}


function save_to_session(){
    //save acquisition to session

    $var_acquire_post = $_REQUEST['var_acquire'];
    $date = $var_acquire_post[1];
    //$mysql_date = date_uk_to_us($date);

    //save main acquire data to session
    $_SESSION['var_acquire']['acquire_id'] = $var_acquire_post[0];
    $_SESSION['var_acquire']['acquire_date'] = $var_acquire_post[1];
    $_SESSION['var_acquire']['merchant_id'] = $var_acquire_post[2];
    $_SESSION['var_acquire']['acquire_type_id'] = $var_acquire_post[3];
    $_SESSION['var_acquire']['acquire_notes'] = $var_acquire_post[4];
    $_SESSION['var_acquire']['user_id'] = $_SESSION['user_id'];
    
    //save vintages
    if(empty($_REQUEST['var_vintages']) || $_REQUEST['var_vintages']=='none'){
        //no vintages to save
    } else {

        $var_vintages_post = $_REQUEST['var_vintages'];

        foreach($var_vintages_post as $var_vintage_post){
            $vintage_has_acquire_id = $var_vintage_post[0];
            //$vintage_id = $var_vintage_post[0];
            $var_vintage = array(
                'vintage_id' => $var_vintage_post[1],
                'vintage_label' => stripslashes($var_vintage_post[2]),
                'qty' => $var_vintage_post[3],
                'unit_price' => $var_vintage_post[4],
                'discount_percentage' =>  $var_vintage_post[5],
                'total_discount' =>  $var_vintage_post[6],
                'discounted_price' =>  $var_vintage_post[7],
                'total_price' =>  $var_vintage_post[8],
                'user_id' => $_SESSION['user_id'],
                'db_action' => $var_vintage_post[9]
            );

            $_SESSION['var_acquire']['var_acquire_vintages'][$vintage_has_acquire_id] = $var_vintage;
        }
        
    }
    
    $var_result['success'] = true;
    return $var_result;
    
}



function get_from_db(){
    //get acquisition details from database

    //clear existing session data
    unset($_SESSION['var_acquire']);

    $acquire_id = $_REQUEST['acquire_id'];

    if($acquire_id > 0 ){

        //get acquire details
        $acquire_obj = new acquire;
        $where = "acquire_id = $acquire_id";
        $var_acquire = $acquire_obj -> get($where);

        if($var_acquire){
            //load acquire details in to session
            $_SESSION['var_acquire']['acquire_id'] = $var_acquire[0]['acquire_id'];
            $_SESSION['var_acquire']['acquire_date'] = $var_acquire[0]['acquire_date'];
            $_SESSION['var_acquire']['merchant_id'] = $var_acquire[0]['merchant_id'];
            $_SESSION['var_acquire']['acquire_type_id'] = $var_acquire[0]['acquire_type_id'];
            $_SESSION['var_acquire']['acquire_notes'] = $var_acquire[0]['acquire_notes'];

            //load vintage details
            $vintages_obj = new vintage_has_acquire;
            $where = "acquire_id = $acquire_id";
            $var_vintages = $vintages_obj -> get($where);

            if (!empty($var_vintages)){

                foreach($var_vintages as $vintage){

                    $vintage_has_acquire_id = $vintage['vintage_has_acquire_id'];
                    $vintage_id = $vintage['vintage_id'];
                    $vintage_label_obj = new vintage($vintage_id);
                    $vintage_label = $vintage_label_obj -> vintage_label();

                    $var_vintage = array(
                        'vintage_id' => $vintage_id,
                        'vintage_label' => $vintage_label,
                        'qty' => $vintage['qty'],
                        'unit_price' => $vintage['unit_price'],
                        'discounted_price' => $vintage['discounted_price'],
                        'discount_percentage' =>  $vintage['discount_percentage'],
                        'total_discount' =>  $vintage['total_discount'],
                        'total_price' => $vintage['total_price'],
                        'db_action' => 'update'
                    );

                    //$_SESSION['var_acquire']['var_acquire_vintages'][$vintage_id] = $var_vintage;
                    $_SESSION['var_acquire']['var_acquire_vintages'][$vintage_has_acquire_id] = $var_vintage;

                }
            } else {
                //add false statement here if acquisition MUST have vintages

            }

            $var_result['success'] = true;
            return $var_result;

        } else {
            //no matching acquire record returned
            $var_result['success'] = false;
            $var_result['error'] = 'no acquisition details found for acquire_id='+$acquire_id;
            return $var_result;
        }

    } else if($acquire_id == 0) {
        
        //acquire_id provided indicates an Add record
        $var_result['success'] = true;
        return $var_result;
        
    } else {
        
        //acquire_id provided indicates an Add record
        $var_result['success'] = false;
        $var_result['error'] = 'no acquire_id provided';
        return $var_result;
        
    }
    

}



function save_to_db(){
    //save acquire session details to db

    //reset flags
    $failed = 0;
    $saved_ok = 0;
    $result = false;
    
    //save acquire to session first
    if(!save_to_session()){
        $var_result['success'] = false;
        $var_result['error'] = "Failed to save acquisition to session";
        return $var_result;
    }
    
    $acquire_obj = new acquire;
    $acquire_id = $_SESSION['var_acquire']['acquire_id'];

    //check mandatory data is provided
    if(!$_SESSION['var_acquire']['acquire_date'] > 0 && !$_SESSION['var_acquire']['merchant_id'] > 0){
        //insufficient data to proceed with save
        $var_result['success'] = false;
        $var_result['error'] = "Acquire Record - mandatory data not provided\n Date=".$_SESSION['var_acquire']['acquire_date']." nerchant_id=".$_SESSION['var_acquire']['merchant_id'];
        return $var_result;
    }
    
    //convert date before sending to db
    $date = $_SESSION['var_acquire']['acquire_date'];
    $mysql_date = date("Y-m-d", strtotime($date));
    $_SESSION['var_acquire']['acquire_date'] = $mysql_date;
 
    if($acquire_id>0){ //already exists so update db
        $where = " acquire_id = $acquire_id ";
        $result = $acquire_obj -> update($_SESSION['var_acquire'],$where);

        if(!$result){
            //update failed
            $var_result['success'] = false;
            $var_result['error'] = "save acquire failed ".$acquire_obj->get_sql_error();
            return $var_result;
        }

    } else { //new record so insert to db
        $result = $acquire_obj -> insert($_SESSION['var_acquire']);

        if($result){
            $acquire_id = $result;
            $var_result['acquire_id'] = $acquire_id;
        } else {
            //insert failed
            $var_result['success'] = false;
            $var_result['error']= $acquire_obj->get_sql_error();
            return $var_result;
        }
    }


    //SAVE VINTAGES
    if($result){ //acquire saved OK
        //determine which vintages have been added or deleted and mark accordingly

        $var_vintages = $_SESSION['var_acquire']['var_acquire_vintages'];

        if(!empty($var_vintages)){
            foreach($var_vintages as $vintage_has_acquire_id => $vintage){
               //reset error
               $error = null;

               $vintage_id = $vintage['vintage_id'];
               //add acquire_id to array
               $vintage['acquire_id'] = $acquire_id;
               //save to db
               $vintage_obj = new vintage_has_acquire;
               //determine action
               $db_action = $vintage['db_action'];

               if ($db_action=='update'){
                   //update existing record
                   log_write("UPDATE vintage record $vintage_id $acquire_id",1,'save_to_db');
                   $where = " vintage_has_acquire_id = $vintage_has_acquire_id ";
                   $result = $vintage_obj -> update($vintage, $where);

                   if($result==false){
                       //error
                       $var_result['success'] = false;
                       $var_result['error'] = $vintage_obj -> get_sql_error();
                       return $var_result;
                   }

               } else if ($db_action=='insert') {
                   //insert new record
                   log_write("INSERT vintage record vintage_id=$vintage_id acquire_id=$acquire_id vintage_has_acquire_id=$vintage_has_acquire_id",1,'save_to_db');
 
                   $result = $vintage_obj -> insert($vintage);
                   //change action
                   if($result){
                        $vintage['db_action'] = 'update';
                        //remove temp array first
                        unset($_SESSION['var_acquire']['var_acquire_vintages'][$vintage_has_acquire_id]);
                        //insert new array object
                        $_SESSION['var_acquire']['var_acquire_vintages'][$result] = $vintage;

                   } else {
                       //insert failed
                        $error = $vintage_obj -> get_sql_error();
                        $vintages_error = $vintages_error." vintage_id:$vintage_id error:$error \n";
                        $var_result['success'] = false;
                        $var_result['error'] = $vintages_error;
                        return $var_result;
                   }

               } else if ($db_action=='delete') {
                   //delete record
                   log_write("DELETE vintage record $vintage_id $acquire_id",1,'save_to_db');
                   $where = "vintage_has_acquire_id = $vintage_has_acquire_id ";
                   $result = $vintage_obj -> delete($where);
                   //remove from array
                   unset($_SESSION['var_acquire']['var_acquire_vintages'][$vintage_has_acquire_id]);
               } else {
                   //no action provided
                   $result = false;
               }

               log_write("SQL action result = $result",1,'save_to_db');


               if ($result>0){
                   $saved_ok = $saved_ok + 1;
               } else {
                   $failed = $failed + 1;
               }


            } //end foreach

            //upload modified array to session
            log_write("upload modified array to session",1,'save_to_db');
            //$_SESSION['var_acquire']['var_acquire_vintages'] = $var_vintages;


            if($failed>0){
                log_write("saved acquire - failed to save $failed vintages",1,'save_to_db');
                $var_result['success'] = false;
                $var_result['error'] = "failed to save $failed vintage records to db \n $vintages_error";
            } else {
                log_write("success - saved acquire & vintages",1,'save_to_db');
                $var_result['success'] = true;
                $var_result['msg'] = "Saved acquire & vintages successfully to DB";
            }

        } else {
            //no vintage records to save
            log_write("saved acquire - no vintages to save",1,'save_to_db');
            $var_result['success'] = true;
            $var_result['msg'] = "Saved Acquire successfully to DB - but no vintages to save";
        }
        
    } else {
       //failed to save acquire record
       log_write("failed to save acquire or vintage records",1,'save_to_db');
       $var_result['success'] = false;
       $var_result['error'] = "Failed to save acquire record \n error = $error";
    }

    return $var_result;
}


function remove_vintage_from_session($acquire_has_vintage_id){
    //remove vintage from session

    if($acquire_has_vintage_id>=0){
        $var_vintages = $_SESSION['var_acquire']['var_acquire_vintages'];
        if(array_key_exists($acquire_has_vintage_id, $var_vintages )){

            //determine action
            if($var_vintages[$acquire_has_vintage_id]['db_action']=='insert'){
                //non-committed record - remove from array only
                unset($_SESSION['var_acquire']['var_acquire_vintages'][$acquire_has_vintage_id]);
                $result = true;
            } else {
                //committed record - mark for deletion
                $_SESSION['var_acquire']['var_acquire_vintages'][$acquire_has_vintage_id]['db_action']='delete';
                $result = true;
            }

        } else {
            //key not found in array
            $result = false;
        }

    } else {
        //no vintage_id provided
        $result = false;
    }

    return $result;
}


function add_vintage_from_basket($vintage_id){
    //determine whether to add vintage

    if($vintage_id>0){

        //add vintage to array
        $vintage_obj = new vintage($vintage_id);
        $vintage_label = $vintage_obj -> vintage_label();
        //determine whether to set discount to 100%
        if($_REQUEST['acquire_type_id']>1){

            $discount_percentage = 100;
        } else {
            $discount_percentage = 0;
        }

        $var_vintage = array(
            'vintage_id' => $vintage_id,
            'vintage_label' => $vintage_label,
            'discount_percentage' => $discount_percentage,
            'db_action' => 'insert'
            );

        //note: no vintage_has_acquire_id available as it has not been added to db yet - will increment from previous array object
        $_SESSION['var_acquire']['var_acquire_vintages'][] = $var_vintage;

        //clear basket
        unset($_SESSION['var_basket']);

        $result = true;
    } else {
        //add failed - no key provided
        $result = false;
    }

    return $result;
}



function delete_acquire_from_db(){
    //delete all acquisition details from database

    $acquire_id = $_REQUEST['acquire_id'];

    if($acquire_id > 0 ){
        //delete all vintage_has_acquisition records with this acquire_id
        $where = " acquire_id = $acquire_id ";
        $vintage_has_acquire_obj = new vintage_has_acquire;
        $vintage_result = $vintage_has_acquire_obj -> delete($where);
        if($vintage_result){ //vintage records deletion successful
            $where = " acquire_id = $acquire_id ";
            $acquire_obj = new acquire;
            $acquire_result = $acquire_obj -> delete($where); //delete master acquire record
            if($acquire_result){ //master acquire deletion successful
                $var_result['success'] = true;
                $var_result['msg'] = 'acquire and associated vintages deleted';
                return $var_result;
            }else{
                $sql_error = $acquire_obj -> get_sql_error();
                $var_result['success'] = false;
                $var_result['error'] = "deleted acquisition but not vintages, delete_acquire_from_db aborted db_error: $sql_error";
                return $var_result;
            }
        }else{
            $sql_error = $vintage_has_acquire_obj -> get_sql_error();
            $var_result['success'] = false;
            $var_result['error'] = "failed to deleted acquisition vintages, delete_acquire_from_db aborted db_error: $sql_error";
            return $var_result;
        }

    } else {
        //no acquire_id provided
        $var_result['success'] = false;
        $var_result['error'] = 'acquire_id missing delete_acquire_from_db aborted';
        return $var_result;
    }

}


//***end functions****


if($_REQUEST['action']=='unset_session'){
    //remove array
    unset($_SESSION['var_acquire']);
    $var_result['success'] = true;
    return $var_result;
}


function add_basket(){
    //add all vintages from basket
    $var_basket = $_SESSION['var_basket'];

    if(empty($var_basket)){
        //no vintages in basket
        $var_result['success']=true;
        $var_result['records']=0;
        $var_result['msg'] = "basket is empty";
        return $var_result;
    }

    foreach($var_basket as $key => $value){
        //add vintage to acquistion
        if(add_vintage_from_basket($value)){
            $var_result['success']=true;
            $var_result['records']=$var_result['records']+1;
        } else {
            $var_result['success']=false;
            $var_result['error'] = "failed to add vintage_id = $key from basket";
        }
    }
    
    return $var_result;

}

function remove_vintage(){
    //remove vintage / mark for deletion from session
    
    if($_REQUEST['key']>=0){
        //log_write("key provided",1,"action='remove_vintage'");
        $result = remove_vintage_from_session($_REQUEST['key']);
        //log_write("remove_vintage_from_session result = $result",1,"action='remove_vintage'");
        if($result){
            $var_result['success']=true;
            $var_result['records']=1;
            return $var_result;
        } else {
            $var_result['success']=false;
            $var_result['error']='invalid key provided'.$_REQUEST['key'];
            return $var_result;
        }
    }
}

function delete_from_db(){
    //delete acquisition from db
    $var_result = delete_acquire_from_db();
    return $var_result;
}



?>
