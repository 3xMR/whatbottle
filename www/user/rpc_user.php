<?php
/* 
 * user functions
 * 
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/User.php"); //include user class


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


function changePassword(){
    //change password
        
    $password = (filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) > "") ? filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) : filter_input(INPUT_GET, 'password', FILTER_SANITIZE_STRING);
    $passwordNew = filter_input(INPUT_POST, 'passwordNew', FILTER_SANITIZE_STRING) >" " ? filter_input(INPUT_POST, 'passwordNew', FILTER_SANITIZE_STRING) : filter_input(INPUT_GET, 'passwordNew', FILTER_SANITIZE_STRING);
    $passwordNewConfirm = filter_input(INPUT_POST, 'passwordNewConfirm', FILTER_SANITIZE_STRING) > "" ? filter_input(INPUT_POST, 'passwordNewConfirm', FILTER_SANITIZE_STRING) : filter_input(INPUT_GET, 'passwordNewConfirm', FILTER_SANITIZE_STRING);

    
    $userObj = new UserObj;
    $result = $userObj->changePasswordAuthed($password, $passwordNew, $passwordNewConfirm);
    
    if($result==true){
        $var_result['success'] = true;
        return $var_result;
    }
    
    $var_result['success'] = false;
    $var_result['error'] = $userObj->lastErrorMessage;
    return $var_result;
    
}


function login(){
    //login user
    
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $remember = filter_input(INPUT_POST, 'remember', FILTER_SANITIZE_STRING);
    
    $userObj = new UserObj;
    $result = $userObj->userLogin($username, $password, $remember);
    
    if($result==true){
        $var_result['success'] = true;
        return $var_result;
    }
    
    $var_result['success'] = false;
    $var_result['username'] = $username;
    $var_result['error'] = $userObj->lastErrorMessage;
    return $var_result;
    
}


function logout(){
    //logout user 
    
    $userObj = new UserObj;
    $result = $userObj->userLogout();
    
    if($result==true){
        $var_result['success'] = true;
        return $var_result;
    }
    
    $var_result['success'] = false;
    $var_result['error'] = $userObj->lastErrorMessage;
    return $var_result;
    
}

?>
