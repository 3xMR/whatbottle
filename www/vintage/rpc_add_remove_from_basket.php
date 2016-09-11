<?php
//start php session
session_start();

/*
 * add or remove vintage from basket session
 *
 */

$vintage_id = $_REQUEST['key'];
$action = $_REQUEST['action'];
$var_basket = $_SESSION['var_basket'];

if(!empty($var_basket)){
    $array_key = array_search($vintage_id,$var_basket);
}


if($action == "remove"){

    if($vintage_id>0){
        //save details to session
        if(!empty($var_basket)){

            //ammend array
            if ($array_key===false){
                //value not found
            } else {
                unset($var_basket[$array_key]);
            }

            //add ammended array back
            $_SESSION['var_basket'] = $var_basket;
        }

    }

}

if($action == "add"){
    echo "<br/>process add as action";
    if($vintage_id>0){
        //save details to session

        if(empty($var_basket) || $array_key===false){
            //add value to array
             $_SESSION['var_basket'][]=$vintage_id;
        }

    }

}


if($action == "empty"){
    //empty basket

    unset($_SESSION['var_basket']);

}


if($action == "empty"){
    //empty basket

    unset($_SESSION['var_basket']);

}

?>
