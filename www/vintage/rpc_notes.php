<?php
/* 
 * rpc functions and procedures for
 * tasting notes
 * 
 * NOTE: still anumber of action based functions at end of script
 * 
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


$var_result['success'] = false;

$note_id = $_REQUEST['key'];
$vintage_id = $_REQUEST['vintage_id'];


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
        $var_result['error'] = "function not found fnc = $fnc";
        $var_result['success'] = false;
        echo json_encode($var_result);
    }

}


function update_rating($vintage_id, $rating){

   if(!$vintage_id > 0){
        $var_result['success'] = false;
        $var_result['error'] = "no vintage_id provided";
        return $var_result;
   }
   
   if(empty($rating)){
       //value is zero or empty do not include in average or update
        $var_result['success'] = true;
        $var_result['msg'] = "rating value is zero or empty";
        return $var_result;
   }

   if($rating=='value'){
       $note_field = "note_value";
       $vintage_field = "vintage_value";
   } else {
       $note_field = "note_quality";
       $vintage_field = "vintage_quality";
   }

   //average quality rating for all notes and update vintage record
   $note_obj = new tasting_note();
   $where = " vintage_id = $vintage_id AND $note_field > 0 ";
   $columns = " AVG($note_field) ";
   $rst = $note_obj -> get($where, $columns, $group=false, $sort=false, $limit=false);

   if($rst){

       $result = $rst[0]["AVG($note_field)"];
   }

   if($result > 0){
       $rating = round($result,0);
   } else {
       $rating = 0;
   }

   //update vintage
   $vintage_obj = new vintage();
   $where = " vintage_id = $vintage_id";
   $set = " $vintage_field = $rating, modified = Now() ";
   $result = $vintage_obj -> update_custom($set, $where);

}

function get_from_db(){
    //get tasting note details from database
    
    $note_id = $_REQUEST['note_id'];
    $vintage_id = $_REQUEST['vintage_id'];
    
    if($note_id > 0 ){
        //open existing note
        $class_obj = new tasting_note;
        $records = $class_obj -> get(" note_id = $note_id ");
        $record_count = count($records);
        if ($record_count==1){
            
            $_SESSION['var_note'] = $records[0];

            //convert date from mysql formay
            $mysql_date = $_SESSION['var_note']['note_date'];
            if($mysql_date>0){
                $_SESSION['var_note']['note_date'] = date_us_to_uk($mysql_date,'d-M-Y');
            }

            $var_result['success'] = true;
            return $var_result;

        } else {
            //error - records returned did not equal 1
            $var_result("error - $record_count records were returned - should be only 1");
            $var_result['success'] = false;
            return $var_result;
        }
        
    } else if($vintage_id>0){

        return add_note(); //return result from add_note function

    } else { //note_id not provided
        $var_result['error'] = 'No note_id provided';
        $var_result['success'] = false;
        return $var_result;
    }

    return $var_result;
}


function put_db(){
    //save to db record
    
    //save post to session before saving to db
    $value = implode(",",$_REQUEST);
 
    if($_REQUEST['note_type']=='quick'){
        //insert server date
        $_REQUEST['note_date'] = date("d-M-Y");
    }

    $_SESSION['var_note']['note_id'] = $_REQUEST['note_id'];
    $_SESSION['var_note']['vintage_id'] = $_REQUEST['vintage_id'];
    $_SESSION['var_note']['note_date'] = $_REQUEST['note_date'];
    $_SESSION['var_note']['note_quality'] = $_REQUEST['note_quality'];
    $_SESSION['var_note']['note_value'] = $_REQUEST['note_value'];
    $_SESSION['var_note']['note_appearance'] = $_REQUEST['note_appearance'];
    $_SESSION['var_note']['note_aroma'] = $_REQUEST['note_aroma'];
    $_SESSION['var_note']['note_taste'] = $_REQUEST['note_taste'];
    $_SESSION['var_note']['note_general'] = $_REQUEST['note_general'];
    $_SESSION['var_note']['sweetness_id'] = $_REQUEST['sweetness_id'];
    $_SESSION['var_note']['fullness_id'] = $_REQUEST['fullness_id'];

    //get note_id
    $note_id = $_SESSION['var_note']['note_id'];

    $class_obj = new tasting_note;

    //check mandatory data is provided
    if($_SESSION['var_note']['note_date'] <= 0 || $_SESSION['var_note']['vintage_id'] <= 0){
        //mandatory data not provided
         $var_return['success']=false;
         $var_return['error']= "db save abandoned due to incomplete data";
         return $var_return;
    } 

    $_SESSION['var_note']['user_id'] = $_SESSION['user_id']; //set user_id

    //transfer array to prevent converting date back and forwards
    $var_note = $_SESSION['var_note'];

    //convert date to mysql format
    $date = $_SESSION['var_note']['note_date'];
    $date = date_uk_to_us ($date,'Y-m-d');
    $var_note['note_date'] = $date;

    if($note_id>0){ //existing vintage so update records    
        $result = $class_obj -> update($var_note," note_id = $note_id ");
        if($result){
            $var_return['success']=true;
            $var_return['msg']="Note saved to DB as UPDATE";
        } else {
            $var_return['success']=false;
            $var_return['error']="Note DB UPDATE failed";
        }

    } else { //new record save as INSERT
        $_SESSION['var_wine_temp']['var_note_dump'] = $var_note;
        $result = $class_obj -> insert($var_note);

        if($result>0){
           //put new note_id to session
            $_SESSION['var_note']['note_id'] = $result;
            $var_return['note_id']=$result;
            $var_return['success']=true;
            $var_return['msg']="Note saved to DB as INSERT";
        } else {
            $var_return['success']=false;
            $var_return['error']="Note DB INSERT failed";
        }

    }
    

    //update average ratings
    if($var_return['success']){
        //update quality and value averages
        $vintage_id = $_POST['vintage_id'];
        update_rating($vintage_id,'quality');
        update_rating($vintage_id, 'value');
    }

    return $var_return;

}


function delete_db($note_id, $vintage_id){
    //delete record from db
    
    $note_id = $_REQUEST['note_id'];
    $vintage_id = $_REQUEST['vintage_id'];
   

    //get vintage_id for note
    $class_obj = new tasting_note;
    $where = "note_id = $note_id";
    $result = $class_obj -> get($where);
    
    if($result){
        $vintage_id = $result[0]['vintage_id'];
    }
    
    log_write("vintage_id = $vintage_id",1,'delete_db');
    $class_obj = $where = $result = null;
    
    
    //delete note
    $class_obj = new tasting_note;
    if($note_id>0){
        
        //delete note from db
        $where = "note_id = $note_id";
        $result  = $class_obj -> delete($where);
        
        if($result){ //delete successful
            
            //clear session
            unset($_SESSION['var_note']);
            $class_obj = $where = $result = null;
            $var_return['success'] = true;

            //get previous note
            $class_obj = new tasting_note;
            $where = "vintage_id = $vintage_id";
            $sort = " created DESC ";
            $limit = " 1 ";
            $result = $class_obj -> get($where, $columns=false, $group=false, $sort, $limit);

            if($result){
                $note_id = $result[0]['note_id'];
                $var_return['note_id'] = $note_id;
            } else {
                $var_return['note_id'] = 0;
            }
            
            $var_return['success'] = true;
            
        } else {
            //delete from db failed
            $var_return['error'] = "error deleting note_id=$note_id from db";
            $var_return['success'] = false;
            return $var_return;            
        }
        
    } else {
        log_write("ERROR - no note_id provided",3,'delete_db');
        $var_return['error'] = "no note_id provided";
        $var_return['success'] = false;
        return $var_return;
    }

     //update average ratings
    if($var_return['success']){
        //update quality and value averages
        log_write("update average ratings vintage_id=$vintage_id",1,'delete_db');
        update_rating($vintage_id,'quality');
        update_rating($vintage_id, 'value');
    }

    return $var_return;

}


function add_note(){
    //add new note to existing vintage
    
    //clear existing session
    unset($_SESSION['var_note']);
    
    //update with new details
    $vintage_id  = $_REQUEST['vintage_id'];
    $quality_rating = $_REQUEST['quality_rating'];
    $value_rating = $_REQUEST['value_rating'];

    if($vintage_id){
        //update session with new details
        $_SESSION['var_note']['vintage_id'] = $vintage_id;
        $_SESSION['var_note']['note_quality'] = $quality_rating;
        $_SESSION['var_note']['note_value'] = $value_rating;

        $var_result['success'] = true;
        return $var_result;
    } else {
        //no vintage_id provided - return error
        $var_result['success'] = false;
        $var_result['error'] = "no vintage_id provided";
        return $var_result;
    }
    
}

//***end functions****


if($_REQUEST['action']=='unset_session'){
    //remove array
    unset($_SESSION['var_note']);
    $var_result['success'] = true;
}

if($_REQUEST['action']=='save_to_session'){
    //save vintage details to session
    save_acquire_to_session();
    save_vintages_to_session();
    $var_result['success'] = true;
    $var_result['close'] = false;
}

if($_REQUEST['action']=='save_to_db'){
    //save acquisition to session
    save_acquire_to_session();
    save_vintages_to_session();
    $var_result = save_to_db();
    //print_r($var_result);
    $var_result['close'] = true;
}

if($_REQUEST['action']=='get_db'){
    //deprecated - get tasting note from db
    if(get_db($note_id, $vintage_id)){
        //$var_result['success'] = true;
    }    
}



if($_REQUEST['action']=='delete'){
    //deprecated - delete tasting note from db
    //$var_result = delete_db($note_id);
}

if($_REQUEST['action']=='open_tasting_note'){
    //open tasting note

    //clear old session data
    unset($_SESSION['var_note']);
    
    $var_result = get_db($_REQUEST['note_id']);
}


?>
