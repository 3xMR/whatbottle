<?php
//start php session
session_start();

/*
 * Basket functions
 *
 */


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
    
    $json_string = $_REQUEST['vintage_array'];
    $var_basket = $_SESSION['var_basket'];
    //$var_vintage = json_decode($json_string);
    $var_vintage = $_REQUEST['vintage_array'];
    $not_found = 0;
    $found = 0;
    
    if(count($var_vintage) < 1 || empty($var_basket) ){ //nothing to remove OR basket is empty
        $var_result['success'] = true;
        $var_result['msg'] = 'vintage array was empty or basket was empty';
        $var_result['basket_count'] = count($var_basket);
        return $var_result;
    }
    
    foreach($var_vintage as $vintage){
        $vintage_id = $vintage;
        $array_key = array_search($vintage_id, $var_basket); //is vintage_id in basket
        if ($array_key !== false){ //vintage is in basket
            unset($var_basket[$array_key]); //remove from array
            $found =+ 1;
        }else{
            $not_found =+ 1; //count vintages not found
        }
    }
    
    $_SESSION['var_basket'] = $var_basket; //update session
    
    $var_result['success'] = true;
    $var_result['msg'] = "vintage_id = $vintage_id, array_key = $array_key, vintages removed = $found, vintages not found = $not_found";
    $var_result['basket_count'] = count($var_basket);
    return $var_result;

}


function add(){
    
    $vintage_id = $_REQUEST['vintage_id'];
    $var_basket = $_SESSION['var_basket'];
    
    if(!$var_basket){
        $var_basket = array();
    }

    if(!$vintage_id){ //no vintage_id provided
        $var_result['success'] = false;
        $var_result['msg'] = 'no vintage_id provided';
        return $var_result;
    }
    
    $array_key = array_search($vintage_id,$var_basket); //is vintage_id already in basket
    
    if($array_key===false){ //vintage not already added
       
        array_push($var_basket, $vintage_id);//push new value to array
        $_SESSION['var_basket'] = $var_basket; //update session
        
        $var_result['success'] = true;
        $var_result['msg'] = "vintage added to basket vintage_id: $vintage_id";
        $var_result['basket_count'] = count($var_basket);
    }else{
        $var_result['success'] = true;
        $var_result['msg'] = "vintage already added vintage_id: $vintage_id";
        $var_result['basket_count'] = count($var_basket);
    }

    return $var_result;

}
