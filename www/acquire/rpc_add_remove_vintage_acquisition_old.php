<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
//require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");
require_once("$root/classes/class.vintage.php");

print_r($_REQUEST);
echo "<hr>";
print_r($_SESSION);
echo "<hr>";

$vintage_id = $_REQUEST['key'];
$action = $_REQUEST['action'];
$var_vintages = $_SESSION['var_acquire']['var_acquire_vintages'];

//unset vintages session array
if($action=='unset'){
    unset($_SESSION['var_acquire']['var_acquire_vintages']);
    echo "<br/>unset var_acquire_vintages";
}

//unset entire acquisition session array
if($action=='unset_acquire'){
    unset($_SESSION['var_acquire']);
}

function exists($find, $array){
    $result = false;
    echo "<br/>look for key - $find";
    if(!empty($array)){
        //recursive search
        foreach($array as $key => $vintage){
            $vintage_id = $vintage['vintage_id'];
            echo "<br/> vintage_id = $vintage_id";
            echo "<br/> array_key = $key";
            if ($find == $vintage_id ){
                echo "<br/>Match found";
                $result = $key;
            }
        }
    } else {
         echo "<br>search failed - array is empty";
    }
    return $result;
}

function add_array($vintage_id, $array){
    echo "<br/>process add as action";
    if($vintage_id>0){
        if(exists($vintage_id, $array)===false){
            //add vintage to array
            $vintage_obj = new vintage();
            $vintage_label = $vintage_obj -> vintage_label($vintage_id);
            
            $var_vintage = array(
                'vintage_id' => $vintage_id,
                'vintage_label' => $vintage_label,
                'db_action' => 'insert'
                );
            $_SESSION['var_acquire']['var_acquire_vintages'][$vintage_id] = $var_vintage;
 
             echo "<br>Add succesful";
            
        } else {
            //already added do not add again
            echo "<br>Add failed - key already added";
        }
    } else {
        echo "<br>Add failed no key provided";
    }
}


function remove_array($vintage_id, $array){
    echo "<br/>process remove as action";
    if($vintage_id>0){
        $key = exists($vintage_id, $array);
        echo "<br/>key found= $key";
        if($key===false){
            echo "<br>Vintage not found in array";
        } else {
            //mark for deletion from array
            $_SESSION['var_acquire']['var_acquire_vintages'][$key]['db_action'] = 'delete';
            //unset($_SESSION['var_acquire']['var_acquire_vintages'][$key]);
        }
    } else {
        echo "<br>Remove failed no key provided";
    }
}

if(empty($_SESSION['var_acquire'])){
    //no acquisition array created - create empty array
    echo "<br/>no existing acquisition in memory";
}

if($action == "add"){
    //add single vintage
    add_array($vintage_id, $var_vintages);
}

if($action == "remove"){
    //remove single vintage
    remove_array($vintage_id, $var_vintages);
}

if($action == "add_basket"){
    //add all vintages from basket
    $var_basket = $_SESSION['var_basket'];
    if(!empty($var_basket)){
        foreach($var_basket as $key => $value){
            //add vintage to acquistion
            add_array($value, $var_vintages);
        }
    } else {
        echo "<br/>no basket to import";
    }
    
}

echo "<hr>";
print_r($_SESSION['var_acquire']);
?>
