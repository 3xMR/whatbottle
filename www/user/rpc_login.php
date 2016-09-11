<?php
/* 
 * login functions
 * 
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


if($_REQUEST['rpc_action'] || $_REQUEST['action']){
    //convert action to function call
    if($_REQUEST['rpc_action']){
        $fnc = $_REQUEST['rpc_action'];
    }else{
        $fnc = $_REQUEST['action'];
    }
    if(is_callable($fnc)){
        //call action as function
        $var_result = call_user_func($fnc);
        echo json_encode($var_result);
    }else{
        $var_result['error'] = "function [$fnc] not found on server page [".$_SERVER['PHP_SELF']."]";
        $var_result['success'] = false;
        echo json_encode($var_result);
    }
}else{
    $var_result['success'] = false;
    $var_result['error'] = "no rpc_action or action requested - cannot continue";
    echo json_encode($var_result); 
}


function rpc_login(){
    //login user
    
    $username = $_REQUEST['username'];
    $password = $_REQUEST['password'];
    $remember = $_REQUEST['remember'];
    
    if($username && $password ){
        //attempt login
         $result= user_login($username, $password, $remember);
         if($result){
             //login successful
            $var_result['success'] = true;
         }else{
            //login failed
            $var_result['success'] = false;
            $var_result['error'] = "username of password incorrect";
         }
        
    } else {
        //details incomplete
        $var_result['success'] = false;
        $var_result['error'] = "username of password not provided";
    }
    
    return $var_result;
    
}


function rpc_logout(){
    //login out user
    
    $result= user_logout(); //php function in function_user.php
    
    if($result){
        //logout successful
       $var_result['success'] = true;
    }else{
       //logout failed
       $var_result['success'] = false;
       $var_result['error'] = "logout failed";
    }
    
    return $var_result;
    
}


?>
