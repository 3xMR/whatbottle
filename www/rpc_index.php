<?php
/*
 * Index RPC functions
 *
 */


$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


if($_REQUEST['action']){
    //convert action to function call
    $fnc = $_REQUEST['action'];
    $var_result = call_user_func($fnc);
}


function set_wine_form_session(){
    //set wine form session variables based on values passed
    //status = '1'create '2'read '3'write '4'delete

    If(!$_REQUEST['wine_id'] || $_REQUEST['status']==1){
        //$wine_id = $_REQUEST['wine_id'];
        $status = $_REQUEST['status'];
        //unset old session data
        unset($_SESSION['var_wine_temp']);
        //create new session
        //$_SESSION['var_wine_temp']['wine_id'] = $wine_id;
        $_SESSION['var_wine_temp']['status'] = $status;
        $var_return['success']=true;
    } else {
        $var_return['success']=false;
        $var_return['error']='invalid parameters passed';
    }

return $var_return;

}


function get_wine_session_from_db(){
    //get wine session data from db


    If($_REQUEST['wine_id'] > 0 || $_REQUEST['status']=='saved'){
        //load existing wine
        $wine_id = $_REQUEST['wine_id'];
        $wine_form_status = $_REQUEST['wine_form_status']; //'saved'
        
        //load details from db
        $obj_wine = new wine();
        $where = "wine_id = $wine_id";
        $var_record = $obj_wine -> get($where);
        
        if($var_record){
            //put details to session
            $_SESSION['var_wine_temp'] = $var_record[0];
            $_SESSION['var_wine_temp']['is_dirty'] = false;
            $_SESSION['var_wine_temp']['status'] = 'saved';
            
        }else{
            $var_return['success']=false;
            $var_return['error']="failed to get record from db. wine_id=$wine_id";
            return $var_return;
        }
        
        //legacy
        $_SESSION['wine_form']['wine_id'] = $wine_id;
        $_SESSION['wine_form']['status'] = $wine_form_status;
        $var_return['success']=true;
        return $var_return;
        
    } else {
        //no parameters provided
        $var_return['success'] = false;
        $var_return['error']='no parameters passed';
        return $var_return;
    }

}



if($_REQUEST['action']=='set_wine_form_session'){
    //set wine form session variables

    log_write("proc start",1,"set_wine_form_session'");
    $var_result = set_wine_form_session();
    log_write("get_from_db",1,"action='get_from_db'");

}


//output result
echo json_encode($var_result);

?>
