<?php
//start php session
session_start();

/*
 * Basket functions
 *
 */

$root = $_SERVER['DOCUMENT_ROOT'];
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


function remove(){
    //remove vintage from basket
    
//    $json_string = $_REQUEST['vintage_array'];
//    $var_basket = $_SESSION['var_basket'];
//    //$var_vintage = json_decode($json_string);
    $var_vintage = $_REQUEST['vintage_array'];
//    $not_found = 0;
//    $found = 0;
    
    $obj = new list_has_vintage();
    //$rst = $obj->add_vintage_to_list($vintage_id);
    $count = $obj->count_in_list();
    
    if(count($var_vintage) < 1 || $count <= 0 ){ //nothing to remove OR basket is empty
        $var_result['success'] = true;
        $var_result['msg'] = 'nothing to remove from basket';
        $var_result['basket_count'] = $count;
        return $var_result;
    }
    
    foreach($var_vintage as $vintage_id){
        $rst = $obj->remove_vintage_from_list(null,$vintage_id);
        if(!$rst){
            $var_result['success'] = false;
            $var_result['msg'] = "error removing vintages from list: ".$obj->get_sql_error();
            $var_result['basket_count'] = $count;
            return $var_result;
        }
    }

    $var_result['success'] = true;
    $var_result['basket_count'] = $count;
    return $var_result;

}


function add(){
    //add vintage to basket
    
    $vintage_id = $_REQUEST['vintage_id'];

    if(!$vintage_id){ //no vintage_id provided
        $var_result['success'] = false;
        $var_result['msg'] = 'no vintage_id provided cannot continue';
        return $var_result;
    }
    
    $obj = new list_has_vintage();
    $rst = $obj->add_vintage_to_list($vintage_id);
    $count = $obj->count_in_list();
    
    if($rst){
        $var_result['success'] = true;
        $var_result['msg'] = "vintage added to basket vintage_id: $vintage_id";
        $var_result['basket_count'] = $count;
    }else{
        $var_result['success'] = true;
        $var_result['msg'] = "vintage already added vintage_id: $vintage_id";
        $var_result['basket_count'] = $count;
    }

    return $var_result;

}
