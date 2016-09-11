<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

/*
 * add or remove award from session
 *
 */

$award_id = $_REQUEST['key'];
$action = $_REQUEST['action'];
$var_awards = $_SESSION['var_awards_temp'];



if($action == "remove"){
    //remove award from var_awards_temp
    
    if($award_id>0){
        //identify array_key from award_id
        if(!empty($var_awards)){
            
            foreach($var_awards as $key => $array){ //find award_id to return array_id
                if($array['award_id']==$award_id){
                    $array_key = $key;
                    unset ($_SESSION['var_awards_temp'][$array_key]); //remove award_id from session
                    $_SESSION['var_vintage_temp']['is_dirty'] = true; //set is_dirty
                    
                    $var_result['success'] = true;
                    $var_result['array_key']=$array_key;                   
                }else{
                    //failed - no array_key identified
                    $var_result['success'] = false;
                    $var_result['error'] = "no array Key identified award_id=$award_id";
                }
            } //foreach

        } else {
            //failed - no award_id provided
            $var_result['success'] = false;
            $var_result['error'] = 'var_awards_temp is empty';
        }

    } else {

        //failed - no award_id provided
        $var_result['success'] = false;
        $var_result['error'] = 'no award_id provided';
    }

}



if($action == "add"){
    //add award to var_awards_temp

    if($award_id>0){
        //save details to session

        $obj_award = new award();
        $where = " award_id = $award_id ";
        $columns = " award_id, award_org, award";
        $var_array = $obj_award -> get_extended($where,$columns);

        if($var_array){

            $_SESSION['var_awards_temp'][$award_id]=$var_array[0];
            $_SESSION['var_vintage_temp']['is_dirty'] = true; //set is_dirty

            $var_result['success'] = true;

        }else{

            //sql failed
            $sql_error = $obj_award ->get_sql_error();
            $var_result['success'] = false;
            $var_result['error'] = "sql_error = $sql_error";

        }


    } else {
        //failed - no award_id provided
        $var_result['success'] = false;
        $var_result['error'] = 'no award_id provided';
    }

}


//_____OUTPUT RESULT_____

echo json_encode($var_result);

?>
