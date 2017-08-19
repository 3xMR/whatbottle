<?php
//start php session
session_start();

/*
 * Save current Vintage details to session
 *
 */


$root = $_SERVER['DOCUMENT_ROOT'];
//$new_root = rtrim($root, '/\\');
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



function debug($text){
    //debug output

    global $new_root, $log_path;
    $debug = false;
    $write_to_file = true;
    $file_name = "log_rpc_vintage.txt";

    if($debug){
        if($write_to_file){
            //debug output to file

            $log_file = $new_root.$log_path.$file_name;

            if(file_exists($log_file)){
                //open new file in w rite mode
                $mode = "a";
            } else {
                $mode = "w";
            }

            $fh = fopen($log_file, $mode) or die("can't open log file");

            $stringData = "> $text \n";
            fwrite($fh, $stringData);
            fclose($fh);

        }else{
            //write to screen
            echo "> $text<br/>";
        }
    }

}

function pagination(){
    //set pagination page number to session

    if($_REQUEST['id']){
        $id = $_REQUEST['id'];
    }else{
        //no id
        $var_result['success']=false;
        $var_result['page_num']=$current_page;
        $var_result['error']="no pagination id provided";
        return $var_result;
    }

    if($_REQUEST['command']){
        $action = $_REQUEST['command'];
    }else{
        //no action
        $var_result['success']=false;
        $var_result['page_num']=$current_page;
        $var_result['error']="no page action provided";
        return $var_result;
    }


    $current_page = $_SESSION[$id]['current_page'];
    $num_pages = $_SESSION[$id]['num_pages'];

    if(is_numeric($action)){
        //is a number so set page num

        if($action > $num_pages){
            $page_num = $num_pages;
        }else if($action < 1){
            $page_num = 1;
        }else{
            $page_num = $action;
        }


    }else if($action == "next"){
        //move to next page

        if(($current_page + 1)< $num_pages){
            $page_num = $current_page + 1;
        } else {
            $page_num = $num_pages;
        }


    }else if($action == "prev"){
        //prev page

        if(($page_num - 1)>0){
           $page_num = $current_page - 1;
        }else{
            $page_num = 1;
        }

    }else if($action == "first"){

        $page_num = 1;

    }else if($action == "last"){

        $page_num = $num_pages;

    }else{
        //no recognised action
        $var_result['success']=false;
        $var_result['page_num']=$current_page;
        $var_result['error']="no page action recognised";
        return $var_result;
    }

    $_SESSION[$id]['current_page'] = $page_num;
    $var_result['success']=true;
    $var_result['page_num']=$page_num;
    return $var_result;

}

function get_vintage_associations(){
    //return association counts for vintage
    $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];
    $index = $vintage_id;
    $var_result = array();

    if($index){
        $obj = new vintage($index);
        $result = $obj->get_all();
        if($result){
            $var_result['success'] = true;
            $var_result['note_count'] = $obj->note_count;
            $var_result['acquisition_count'] = $obj->acquisition_count;
            $var_result['award_count'] = $obj->award_count;
            $var_result['grape_count'] = $obj->grape_count;
        }else{
            $var_result['success'] = false;
            $var_result['error'] = "get_vintage_association - no results returned for index=$index";
        }
    } else {
        $var_result['success'] = false;
        $var_result['error'] = "get_vintage_association - no index provided";
    }

    return $var_result;

}


function delete_vintage(){
    //delete vintage and associated details from database
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }

    $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];
    $index = $vintage_id;
    $var_result = array();

    if($index>0){
        //check if assoc with acquisitions
        $obj_db = new vintage_has_acquire();
        $where = "vintage_id =$index";
        $count = $obj_db -> row_count($where);
        if($count){
            //associated with acquisitions
            $var_result['success']=false;
            $var_result['error']="vintage deleted failed as this vintage is associated with $count acquisitions";
            return $var_result;
        }

        //delete vintage - association deletions are handled by class
        $obj_vintage = new vintage($vintage_id);
        $result = $obj_vintage -> delete_vintage();

        if($result){
            unset($_SESSION['var_vintage_temp']); //clear session
            $var_result['success']=true;
            
        } else {
            $var_result['success']=false;
            $var_result['error']="failed to delete vintage";
        }

    } else {
        //no index provided
        $var_result['success']=false;
        $var_result['error']="vintage deleted failed as no vintage_id provided";

    }

    return $var_result;

}


function put_to_session(){
    //put vintage details to session

    $var_result = array();
    
    if($_REQUEST['json_values']){
        
        $json = stripslashes($_REQUEST['json_values']);
        $var_values = json_decode($json,true);
        foreach($var_values as $value){
            $_SESSION['var_vintage_temp'][$value['name']] = $value['value'];
        }
        
        $var_result['success']=true;
    }else{
        $var_result['success'] = false;
        $var_result['error'] = "json_values array missing";
    }

    return $var_result;
}



function get_vintage_from_db(){
    //get vintage details from db and load to session

    $vintage_id = $_REQUEST['vintage_id'];
    if(isset($_REQUEST['wine_id'])){$wine_id = $_REQUEST['wine_id'];}
    $status = $_REQUEST['status'];
    $var_result = array();

    if($vintage_id){

        //clear existing session
        unset($_SESSION['var_vintage_temp']);

        //load vintage details and put to session
        $vintage = new vintage($vintage_id);
        $vintage_details = $vintage -> get_all();
        $_SESSION['var_vintage_temp'] = $vintage_details;

        //create image array
        if ($_SESSION['var_vintage_temp']['image1']>""){

            //set session data
            $var_images['saved']['name'] = $_SESSION['var_vintage_temp']['image1'];
            $var_images['saved']['status'] = 'saved';

            $var_images['temp']['name'] = $_SESSION['var_vintage_temp']['image1'];
            $var_images['temp']['status'] = 'saved';

            //put to session
            $_SESSION['var_vintage_temp']['var_images'] = $var_images;

        } else {
            //no image to copy - clear session data
            unset($_SESSION['var_vintage_temp']['var_images']);
        }

        //create record or original file_name
            //$_SESSION['var_vintage_temp']['image1_old'] = $_SESSION['var_vintage_temp']['image1'];


         //set status
        if(empty($_REQUEST['status'])){
            $_SESSION['var_vintage_temp']['status'] = 3; //read-only
        } else {
            $_SESSION['var_vintage_temp']['status'] = $_REQUEST['status'];
        }

        //set is_dirty to false
        $_SESSION['var_vintage_temp']['is_dirty'] = false;

        $var_result['success']=true;

    } else if($wine_id && $status == 1){
        //new vintage on existing wine

        //clear old session data
        unset($_SESSION['var_vintage_temp']);

        //get details from wine

        $obj_wine = new wine($wine_id);
        $var_wine = $obj_wine -> get_extended();
        $_SESSION['var_vintage_temp'] = $var_wine[0];
        $_SESSION['var_vintage_temp']['status'] = 1; //add new

        //get previous vintage details if they exist and populate new vintage
        $obj_vintage = new vintage();
        $where = "wine_id = $wine_id";
        $group = null;
        $sort = "vintage_id DESC";
        $limit = 1;

        $var_vintage = $obj_vintage -> get($where, $columns=false, $group, $sort, $limit);

        if($var_vintage){

            //copy details
            if($var_vintage[0]['vintage_id']){
                //set clone_id
                $_SESSION['var_vintage_temp']['vintage_clone_id'] = $var_vintage[0]['vintage_id'];

                //alcohol
                $_SESSION['var_vintage_temp']['alcohol'] = $var_vintage[0]['alcohol'];

                //image
                $_SESSION['var_vintage_temp']['image1'] = $var_vintage[0]['image1'];

                $var_images['saved']['name'] = $_SESSION['var_vintage_temp']['image1'];
                $var_images['saved']['status'] = 'saved';

                $var_images['temp']['name'] = $_SESSION['var_vintage_temp']['image1'];
                $var_images['temp']['status'] = 'saved';

                //put to session
                $_SESSION['var_vintage_temp']['var_images'] = $var_images;

                //grapes
                $obj = new vintage($var_vintage[0]['vintage_id']);
                $var_grapes = $obj -> get_grapes();
                if($var_grapes){
                     $_SESSION['var_vintage_temp']['var_grapes'] = $var_grapes;
                }

            }

        } else {

             $_SESSION['var_vintage_temp']['vintage_clone_id'] = 'none found';
             $var_result['success']=true;
             $var_result['msg']='no vintage_id found to clone';
        }

        //set is_dirty to false
        $_SESSION['var_vintage_temp']['is_dirty'] = false;

        $var_result['success']=true;

    } else {
        //no details provided
        $var_result['success']=false;
        $var_result['error']='no wine_id or vintage_id provided';
    }

    return $var_result;

}


function unset_session(){
    //unset session variable
    if($_REQUEST['session_name']>""){
        unset($_SESSION[$_REQUEST['session_name']]);
        $var_result['success']=true;
        return $var_result;
    } else {
        $var_result['success']=false;
        $var_result['error']='no session name provided';
        return $var_result;
    }
}


function clear_vintage_session(){
    //unset session variable

    unset($_SESSION['var_vintage_temp']);
    $var_result['success']=true;
    return $var_result;

}


function unset_vintage_temp(){
    //unset var_vintage_temp and discard changes

    //delete tmp vintage if different to image1
    if($_SESSION['var_vintage_temp']['image1_old'] > "" &&
            $_SESSION['var_vintage_temp']['image1'] <> $_SESSION['var_vintage_temp']['image1_old']){
        //delete image1
        $src_file_name = $_SESSION['var_vintage_temp']['image1'];
        $src_full_path = $root.$label_path.$src_file_name;
        //delete tmp file
        fclose($src_full_path);
        unlink($src_full_path);
       unset($_SESSION['var_vintage_temp']['image1']);
    }

    unset($_SESSION['var_vintage_temp']);
    $var_result['success']=true;
    return $var_result;

}



function set_vintage_form_session(){
    //set wine form session variables based on values passed
    //by jquery

    //vintage_form_status = '1'create '2'read '3'write '4'delete

    if($_REQUEST['status']==1 && $_REQUEST['wine_id']>0){
        //create new vintage

        //clear old session data
        unset($_SESSION['var_vintage_temp']);
       // unset($_SESSION['vintage_form']['vintage_id']);
        $_SESSION['var_vintage_temp']['wine_id'] = $_REQUEST['wine_id'];
        $_SESSION['var_vintage_temp']['status'] = 1; //add new
        $_SESSION['var_vintage_temp']['init_page'] = $_REQUEST['init_page'];
        $var_return['success']=true;

    } else if($_REQUEST['vintage_id']>0){
        //clear existing session
        unset($_SESSION['var_vintage_temp']);
        $_SESSION['vintage_form']['vintage_id'] = $_REQUEST['vintage_id'];
        $_SESSION['vintage_form']['status'] = $_REQUEST['status'];
        $_SESSION['var_vintage_temp']['init_page'] = $_REQUEST['init_page'];
        $var_return['success']=true;

    } else {
        $var_return['success']=false;
        $var_return['error']='invalid parameters passed';
    }

    $var_result = $var_return;
    return $var_result;
}



function get_vintage_session(){
    //get vintage details from session and return to page

    $var_result = $_SESSION['var_vintage_temp'];
    $var_result['success'] = true;
    return $var_result;
}


function get_vintage_available_override_details(){
    //get vintage details from session and return to page

    if( !($vintage_id = filter_input(INPUT_POST, "vintage_id", FILTER_SANITIZE_NUMBER_INT) ) ){
        //no vintage_id provided can't continue
        $var_result['success'] = false;
        $var_result['error'] = 'no vintage_id provided';
        return $var_result;
    }
    
    $objVintage = new vintage($vintage_id);
    if( !($varAvailable = $objVintage->get_available_bottle_count() ) ){
        $var_result['success'] = false;
        $var_result['error'] = 'get_available_bottle_count returned false error = '.$objVintage->get_last_error();
        return $var_result;
    }
    
    $var_result['success'] = true;
    $var_result['details'] = $varAvailable;
    return $var_result;
    
}


function put_vintage_available_override_details(){
    //put available override details to db
    
    if(!is_authed()){
        $var_result['success'] = false;
        $var_result['error'] = 'Not logged in';
        return $var_result;
    }
    
    if( !($vintage_id = filter_input(INPUT_POST, "vintage_id", FILTER_SANITIZE_NUMBER_INT) ) ){
        //no vintage_id provided can't continue
        $var_result['success'] = false;
        $var_result['error'] = 'no vintage_id provided';
        return $var_result;
    }
    
    $override_value = 1 * filter_input(INPUT_POST, "override", FILTER_SANITIZE_NUMBER_INT);
    
    $objVintage = new vintage($vintage_id);
    
    if( !($objVintage->set_available_override($override_value)) ){
        $var_result['success'] = false;
        $var_result['error'] = 'set_available_override returned false. error = '.$objVintage->get_last_error();
        return $var_result;
    }
    
    $var_result['success'] = true;
    return $var_result;
    
}


function put_vintage_to_db(){
    //save vintage to db
    
    if(!is_authed()){ //abort if not authorised
        $var_result['success'] = false;
        $var_result['error'] = 'You must logon to save changes';
        return $var_result;
    }
    
    //retrieve values sent from form
    $json = stripslashes($_REQUEST['json_values']);
    $var_values = json_decode($json,true);
    foreach($var_values as $value){
        $_SESSION['var_vintage_temp'][$value['name']] = $value['value'];
    }

    //form values captured in array
    $var = $_SESSION['var_vintage_temp'];

    //new or existing?
    if($var['vintage_id']){
        //existing vintage
        $bln_existing = true;

    } else {

        //new vintage validation
        if(empty($var['wine_id'])){
            //no vintage_id or wine_id provided
            $var_result['success'] = false;
            $var_result['error'] = 'critical: no wine_id provided for new vintage commit_to_db aborted';
            return $var_result;
        }

        //wine_id provided but no vintage_id continue with validation as new
    }

    //validate mandatory data is present; wine_id, year
    if($var['wine_id']>0 && $var['year']>1000 && $var['year']<9999){
        // ok to continue

        //confirm year is not a duplicate
        $obj_vintage =  new vintage();
        $where = "wine_id = ".$var['wine_id']." AND year = ".$var['year'];
        $var_vintage = $obj_vintage -> get($where);
        if($var_vintage){
            $found_vintage_id = $var_vintage[0]['vintage_id'];
            if($var['vintage_id']!=$found_vintage_id){
                //will create duplicate year
                $var_result['success'] = false;
                $var_result['error'] = "will create duplicate vintage year for this wine if saved vintage_id=$found_vintage_id";
                //terminate function
                return $var_result;
            }
        }

    } else {
        //mandatory data is missing
        $var_result['success'] = false;
        $var_result['error'] = 'critical: mandatory data; wine_id or year are missing - commit_to_db aborted';
        //terminate function
        return $var_result;
    }


    //commit to db

    $var['user_id'] = $_SESSION['user_id']; //set user_id

    if($bln_existing): //perform db update
        $obj_vintage = new vintage();
        $vintage_id = $var['vintage_id'];
        $where = "vintage_id = ".$vintage_id;
        $result = $obj_vintage -> update($var,$where);

        if($result==false){ //update failed
            $var_result['success'] = false;
            $var_result['error'] = "db update failed";
            return $var_result;
        }else{
            //update successful
            $var_result['db_action'] = "update";
            $var_result['vintage_id'] = $vintage_id;
        }

    else:
        //perform db insert
        $obj_vintage = new vintage();
        $where = "vintage_id = ".$var['vintage_id'];
        $result = $obj_vintage -> insert($var);
        if($result<>false):
            //insert successful
            $vintage_id = $result;
            //update session
            $_SESSION['var_vintage_temp']['vintage_id'] = $vintage_id;
            $var_result['db_action'] = 'insert';
            $var_result['vintage_id'] = $result;

        else:
            //insert failed
            $sql_error = $obj_vintage ->get_sql_error();
            $var_result['success'] = false;
            $var_result['error'] = "db insert failed sql_error: $sql_error";
            return $var_result;
        endif;

    endif;


    //save image
    $save_image_result = save_image();
    if($save_image_result['success']==false){
        //saving image failed
        $var_result = $save_image_result;
        return $var_result;
    }


    //save grape details
    $grape_result = put_grapes_db($vintage_id);
    
    if($grape_result['success']===false){
        //grape save failed
        $var_result['success'] = false;
        $var_result['error'] = $grape_result['error'];
        return $var_result;
    }

    //save award details
    $var_awards = $_SESSION['var_vintage_temp']['var_awards'];

    if(is_array($var_awards)){
        //delete existing awards first
        $obj_awards = new vintage_has_award();
        $where = "vintage_id = $vintage_id";
        $obj_awards -> delete($where);

        //save awards
        foreach($var_awards as $award){
            $obj_award = new vintage_has_award();
            $award['vintage_id'] = $vintage_id;
            $award['user_id'] = $_SESSION['user_id'];
            $obj_award-> insert($award);
            //TODO: capture errors
        }
    }

    //reset is_dirty
    $_SESSION['var_vintage_temp']['is_dirty']= false;
    
    //add to basket if new
    if($bln_existing != true){
        //add to basket
        
    }
    
    $var_result['success'] = true;
    $var_result['msg'] = 'vintage saved successfully';
    return $var_result;
}




//_____GRAPES_____
//

function put_grapes_session(){
    //put grapes array to session

    $var = stripslashes($_REQUEST['json_field']);
    $var_grapes = json_decode($var,true); //convert to php array
    $var_grapes_session = $_SESSION['var_vintage_temp']['var_grapes'];
    $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];
    
    $_SESSION['var_vintage_temp']['is_dirty']=true;
       
    function compare_arrays($var_grapes, $var_grapes_session){
        /* Compare uploaded array with array in session to determine if it has changed
         * and set is_dirty if it has
         */
        
        //$_SESSION['var_vintage_temp']['json_grape_array_count']=count($var_grapes);
        //$_SESSION['var_vintage_temp']['session_grape_array_count']=count($var_grapes_session);
        
        if(count($var_grapes) != count($var_grapes_session)){
            //something has changed - no need to process further
            $_SESSION['var_vintage_temp']['is_dirty'] = true; //set is_dirty
            return true;
        }
        
        if(empty($var_grapes)){
            return true;
        }
         
        foreach($var_grapes as $var_grape){
            $bln_grape_match = false; //reset
            $grape_id = $var_grape['name'];
            $percent = $var_grape['value'];

            foreach($var_grapes_session as $var_grape_session){
                if($var_grape_session['grape_id'] == $grape_id){
                    $bln_grape_match = true;//match found
                    if($var_grape_session['percent'] != $percent){ //percent has changed
                        $_SESSION['var_vintage_temp']['is_dirty'] = true; //set is_dirty
                    }
                    break 1; //stop foreach loop as grape was found
                } else {
                    $bln_grape_match = false;
                }
            } //foreach session grapes

            if($bln_grape_match == false){
                //grape not found in uploaded data so must be deleted
                $_SESSION['var_vintage_temp']['is_dirty'] = true; //set is_dirty
            }

        } //foreach uploaded grapes
            
        
        
    } //function compare arrays
    
    compare_arrays($var_grapes, $var_grapes_session); //Compare arrays to determine changes
    
    unset($_SESSION['var_vintage_temp']['var_grapes']); //remove existing session details

    if(!empty($var_grapes)){ //grape array provided - save to session
             
        $var_new_session = array();
        
        foreach($var_grapes as $var_grape){
            $obj = new grape();
            $where = 'grape_id = '.$var_grape['name'];
            $columns = "grape_id, grape, colour ";
            $var_grape_db = $obj -> get($where, $columns);
            $var_grape_db[0]['percent'] =  $var_grape['value'];
            $var_grape_db[0]['vintage_id'] =  $vintage_id;
            array_push($var_new_session, $var_grape_db[0]);
        }
        
        $_SESSION['var_vintage_temp']['var_grapes'] = $var_new_session;

        $var_result['success'] = true;
        $var_result['msg'] = "$n grapes successfully saved to session";
        return $var_result;
            
    } else {
        //no grape data to save but existing was deleted so mark as dirty
        $_SESSION['var_vintage_temp']['is_dirty'] = true; //set is_dirty
        $var_result['success'] = true;
        $var_result['msg'] = "all grapes removed successfully from session";
        return $var_result;
    }
    
    
}


function put_grapes_db($vintage_id){
    //save grapes to db
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }
    
    $var_grapes = $_SESSION['var_vintage_temp']['var_grapes'];
    
    if(!$vintage_id){ //no vintage_id cannot continue
        $var_result['success'] = false;
        $var_result['error'] = 'no vintage_id cannot commit to db aborted';
        return $var_result; 
    }
    
    //delete existing grapes from vintage_has_grapes table
    $obj_grapes = new vintage_has_grape();
    $where = "vintage_id = $vintage_id";
    $obj_grapes -> delete($where);

    if(!$obj_grapes){
        //delete operation failed - do not continue
        $var_result['success'] = false;
        $var_result['error'] = 'db delete of grapes failed - grapes not saved';
        return $var_result;
    }

    if(is_array($var_grapes)){ //save grapes
        
        foreach($var_grapes as $grape){
            $obj_grape = new vintage_has_grape();
            $grape['vintage_id'] = $vintage_id;
            $grape['user_id'] = $_SESSION['user_id'];
            $obj_grape -> add($grape);
            
            if($obj_grape === false){
                $var_result['success'] = false;
                $var_result['error'] = 'failed to add grape to db';
                return $var_result;
            }
        }
        
        $var_result['success'] = true;
        return $var_result;
       
    } 
    
}


//____AWARDS_____

function put_temp_awards(){
    //put award temp details from vintage temp session
        
        $_SESSION['var_awards_temp'] = $_SESSION['var_vintage_temp']['var_awards'];
        
        $var_result['success']=true;
        return $var_result;
}


function put_awards_session(){
    //transfer temp awards selection into vintage temp session

    $_SESSION['var_vintage_temp']['var_awards'] = $_SESSION['var_awards_temp'];
    unset($_SESSION['var_awards_temp']);

    $var_result['success'] = true;
    return $var_result;
}




//
//_____IMAGE______
//

function ImageCreateFromBMP($filename){


   if (! $f1 = fopen($filename,"rb")) return FALSE;


   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
   if ($FILE['file_type'] != 19778) return FALSE;

 //2 : Chargement des ent�tes BMP
   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] = 4-(4*$BMP['decal']);
   if ($BMP['decal'] == 4) $BMP['decal'] = 0;

 //3 : Chargement des couleurs de la palette
   $PALETTE = array();
   if ($BMP['colors'] < 16777216)
   {
    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
   }

 //4 : Cr�ation de l'image
   $IMG = fread($f1,$BMP['size_bitmap']);
   $VIDE = chr(0);

   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
   $P = 0;
   $Y = $BMP['height']-1;
   while ($Y >= 0)
   {
    $X=0;
    while ($X < $BMP['width'])
    {
     if ($BMP['bits_per_pixel'] == 24)
        $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
     elseif ($BMP['bits_per_pixel'] == 16)
     {
        $COLOR = unpack("n",substr($IMG,$P,2));
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 8)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 4)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
        if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     elseif ($BMP['bits_per_pixel'] == 1)
     {
        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
        if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
        elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
        elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
        elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
        elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
        elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
        elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
        elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
        $COLOR[1] = $PALETTE[$COLOR[1]+1];
     }
     else
        return FALSE;
     imagesetpixel($res,$X,$Y,$COLOR[1]);
     $X++;
     $P += $BMP['bytes_per_pixel'];
    }
    $Y--;
    $P+=$BMP['decal'];
   }

 //Fermeture du fichier
   fclose($f1);

 return $res;
}


function reduce_image($img, $quality){
    // Open the original image.

    global $new_root, $label_path;

    $orig_path = $new_root.$label_path;
    $new_path = $new_root."/images/temp/";
    $img = $_REQUEST['image_name'];
    $ext = pathinfo($img, PATHINFO_EXTENSION);
    $file_name = pathinfo($img, PATHINFO_FILENAME);
    $quality = 95;
    $target_size = 400;


    if(!file_exists($orig_path.$img)){
        $var_result['success']=FALSE;
        $var_result['error']="original image not found path=$orig_path$img";
        return $var_result;
    }

    //load image to memory - identify file type
    if($ext=='jpg' || $ext=="jpeg"){
        $source = imagecreatefromjpeg($orig_path.$img);
    } else if($ext=="png"){
        $source = imagecreatefrompng($orig_path.$img);
    } else if($ext=="gif"){
        $source = imagecreatefromgif($orig_path.$img);
    } else if($ext=="bmp"){
        $source = imagecreatefromBMP($orig_path.$img);
    } else {
        $var_result['success']=FALSE;
        $var_result['error']="image type not supported ext=$ext";
        return $var_result;
    }

    //get details from original
    list($orig_width, $orig_height) = getimagesize($orig_path.$img);

    //determine new dims
    if ($orig_width > $orig_height){
        $percentage = ($target_size / $orig_width);
    } else {
        $percentage = ($target_size / $orig_height);
    }

    //gets the new value and applies the percentage, then rounds the value
    $targ_width = round($orig_width * $percentage);
    $targ_height = round($orig_height * $percentage);


    // Resample the image.
    $temp_image = imagecreatetruecolor($targ_width, $targ_height);
    if(imagecopyresized($temp_image, $source, 0, 0, 0, 0, $targ_width, $targ_height, $orig_width, $orig_height)){
        //success
    }else{
        $var_result['success']=false;
        $var_result['error'] = "failed to imagecopyresize original image";
        return $var_result;
    }

    // Create the new file name.
    $image_name = $file_name.".".$ext;

    // Save the image.
    if(imagejpeg($temp_image, "$new_path/$image_name", $quality)){
        //success
        $var_result['success']=true;
    }else{
        $var_result['success']=false;
        $var_result['error'] = "failed to create new image $new_path/$image_name";
    }

    // Clean up.
    imagedestroy($source);
    imagedestroy($temp_image);
    return $var_result;
}



function image_rename($old_image, $new_image){
    //rename and or move image

    global $new_root;

    $old_image = $new_root.$old_image;
    $new_image = $new_root.$new_image;
    return rename($old_image, $new_image);

}



function image_unique_name($image_name, $ext=FALSE){
    //function to rename image file prefixes

    if($image_name){

        //extract extension
        if(!$ext){
            //no overide provided - so use file extension
            $ext = pathinfo($image_name, PATHINFO_EXTENSION);
        }

        //generate unique_id
        $session_id = $_REQUEST["PHPSESSID"];
        // Get both seconds and microseconds parts of the time
        list($usec, $sec) = explode(" ",microtime());

        // Fudge the time we just got to create two 16 bit words
        $usec = (integer) ($usec * 65536);
        $sec = ((integer) $sec) & 0xFFFF;

        $uid = $session_id.'_'.$sec.$usec;

        return $uid.".".$ext;

    } else {
        //no name to process
        return false;
    }
    
}

function bulk_delete(){
    //delete files in bulk - image_admin

    debug("***** fnc: bulk_delete ".date('Y-m-d H:i:s')."*****");

    $json_array = $_REQUEST['json_field'];
    $dir = $_REQUEST['dir'];

    if($json_array && $dir){

        $var_array = $_REQUEST['json_field'];
        $var_array = stripslashes($var_array);
        $var_files = json_decode($var_array,true);

        if(empty($var_files)){
            //no input data provided
            $var_result['success'] = false;
            $var_result['error'] = 'no input array provided';
            return $var_result;
        }else{
            foreach ($var_files as $var_file) {
                debug("delete file >> name = ".$var_file['name']." value = ".$var_file['value']);
                $file = $dir.$var_file['value'];
                delete_image($file);
            }

            $var_result['success'] = true;
            return $var_result;
        }
    } else {
        $var_result['success'] = false;
        $var_result['error'] = 'no input data provided';
    }

}


function image_correct_records(){
    //delete files in bulk - image_admin

    debug("***** fnc: correct_records ".date('Y-m-d H:i:s')."*****");

    $json_array = $_REQUEST['json_field'];

    if($json_array){

        $var_array = $_REQUEST['json_field'];
        $var_array = stripslashes($var_array);
        $var_records = json_decode($var_array,true);

        if(empty($var_records)){
            //no input data provided
            $var_result['success'] = false;
            $var_result['error'] = 'no input array provided';
            return $var_result;
        }else{
            foreach ($var_records as $var_record) {
                debug("db update image1 = null >> vintage_id = ".$var_record['value']);
                $vintage_obj = new vintage();
                $where = "vintage_id = ".$var_record['value'];
                $input_array['image1'] = null;
                $update_result = $vintage_obj ->update($input_array, $where);
                if($update_result){
                    $var_result['success'] = true;
                }else{
                    $var_result['success'] = false;
                    $var_result['error'] = "failed to correct record = ".$var_record['value'];
                    return $var_result;
                }
            }

            return $var_result;
        }
    } else {
        $var_result['success'] = false;
        $var_result['error'] = 'no input data provided';
    }

}


function delete_image($image_name){
    //delete image - image name must include relative path
    
    if(!is_authed()){ //check if user is authorised
        $var_result['success'] = false;
        $var_result['error'] = "You must login to use this application";
        return $var_result;
    }

    $root = $_SERVER['DOCUMENT_ROOT'];
    $site_root = rtrim($root, '/\\');

    //add root to provide absolute path
    $image_name = $site_root.$image_name;
    //delete file

    if(file_exists($image_name)){
        if(unlink($image_name)){
            return true;
        } else {
            return false;
        }
    }else{
        return false;
    }

}


function save_image_to_vintage(){
    //on close of image manager, update Vintage session with image
    
    global $new_root, $label_path, $label_upload_path;
    
    $image_name = $_SESSION['var_vintage_temp']['var_images']['edit']['name'];
    $image_status = $_SESSION['var_vintage_temp']['var_images']['edit']['status'];
    $saved_image = $_SESSION['var_vintage_temp']['var_images']['temp']['name'];
    
    if($image_name && $image_status <> 'deleted'){ //check file name exists
        $edit_image_path = $new_root.$label_upload_path.$image_name;
        if(!file_exists($edit_image_path)){ //check file exists
            $var_result['success'] = false;
            $var_result['error']= "image file not found save cannot continue file: $image_name";
            return $var_result;
        }
    } else if($image_status == 'deleted'){
        //clear image name so nothing displays when returning to vintage
        $image_name = null;
    } else {
        $var_result['success'] = true;
        $var_result['msg']= "no image name to save to var_vintage_temp OR image deleted file: $image_name";
        return $var_result;
    }
    
    if($image_status == 'saved'){ //nothing has changed - no need to copy image
        //check original file exists in label location before deleting edit version
        if($saved_image){
            $saved_image_path = $new_root.$label_path.$image_name;
            if(!file_exists($saved_image_path)){ //check file exists
                $var_result['success'] = false;
                $var_result['error']= "saved (original) image file not found save cannot continue file: $saved_image";
                return $var_result;
            }
        }
        
        //original file intact - can now safely delete edit image file
        if(!unlink($edit_image_path)){
            $var_result['success'] = false;
            $var_result['error']= "failed to delete original image file: $image_name";
            return $var_result;
        } 
    }
    
    //update vintage session to point at new image
    $_SESSION['var_vintage_temp']['var_images']['temp']['name'] = $image_name;
    $_SESSION['var_vintage_temp']['var_images']['temp']['status'] = $image_status;
    $_SESSION['var_vintage_temp']['is_dirty'] = true; //set as dirty
    
    $var_result['success'] = true;
    $var_result['msg']= "update var_vintage_temp to point at image file: $image_name";
    return $var_result;
    
}


function image_has_assoc($image_name, $vintage_id){
     //true if image name is associated with other vintages
    
    if(!$image_name || !$vintage_id){
        return -1;
    }
    
    $vintage_obj = new vintage();
    $where = "image1 = '$image_name' AND vintage_id <> $vintage_id";
    $vintage_count = $vintage_obj -> row_count($where);

    if($vintage_count){
        //associated vintages identified
        return $vintage_count;
    }else{
        //no associated vintages
        return 0;
    }
            
}


function delete_image_saved($image_saved_name){
    //delete saved_image

    global $new_root, $label_path, $label_upload_path;
    $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];

    if(!$image_saved_name){ //determine if vintage already has a saved image
        return true; //nothing to delete
    }

    if(image_has_assoc($image_saved_name, $vintage_id)>0){ //check for associations
        return true; //image associated with other vintages - do not delete
    }

    //check saved file exists
    $image_saved_path = $new_root.$label_path.$image_saved_name;
    if(file_exists($image_saved_path)){ //file exists
        if(unlink($image_saved_path)){ //delete saved image file
            return true;
        }else{
            return false; //delete failed
        }
    }else{
        //file does not exist - clear session reference
        unset( $_SESSION['var_vintage_temp']['var_images']['saved']);
        return true;
    }

}


function save_image(){
    //save image to db

    global $new_root, $label_path, $label_upload_path;

    $image_saved_name = $_SESSION['var_vintage_temp']['var_images']['saved']['name'];
    $image_saved_status = $_SESSION['var_vintage_temp']['var_images']['saved']['status'];
    $image_edit_name = $_SESSION['var_vintage_temp']['var_images']['edit']['name'];
    $image_edit_status = $_SESSION['var_vintage_temp']['var_images']['edit']['status'];
    $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];

    
    //determine if any action is required
    if($image_edit_status == 'saved' || (!$image_edit_name)){ //image_edit already saved
        //nothing to do
        $var_result['success'] = true;
        $var_result['msg'] = "image_edit already saved - or nothing to save";
        return $var_result;
    }
    
    if($image_edit_status == 'deleted'){
        //delete saved image - checking for associations with other vintages first
        
        if(delete_image_saved($image_saved_name)){ //delete saved image if not associated with other vintages
            //remove image name from vintage record
            $vintage_obj = new vintage();
            $where = "vintage_id = $vintage_id";
            $input_array['image1'] = null;
            $update_result = $vintage_obj ->update($input_array, $where);

            if($update_result){
                //clear session
                unset($_SESSION['var_vintage_temp']['var_images']['edit']);
                unset($_SESSION['var_vintage_temp']['var_images']['temp']);
                unset($_SESSION['var_vintage_temp']['var_images']['saved']);
                $var_result['success'] = true;
                $var_result['msg'] = "image deleted and db updated";
                return $var_result;
                
            }else{
                $sql_error = $vintage_obj -> get_sql_error();
                $var_result['success'] = false;
                $var_result['error'] = "update db after deleting saved_image failed with sql_error: $sql_error";
                return $var_result;
            }
        } 
        
    } //deleted
    
    if($image_edit_status == 'new'){
        //Move new image from upload folder to label folder - check for other vintage associations - and then
        //delete old saved image
        
        //check to see if saved name and edit name are identical - if so nothing has changed
        if($image_saved_name == $image_edit_name){
            //no need to move file or update db - just delete edit file and update session
            $result = delete_image_edit($image_edit_name); //delete edit_image
            if($result['success'] == false){
                $var_result = $result;
                return $var_result;
            }
            //update session
            $_SESSION['var_vintage_temp']['var_images']['saved']['status'] = 'saved';
            
            $var_result['success'] = true;
            $var_result['msg'] = "image_edit was unchanged and delete was successful";
            return $var_result;

        } else {
            //create new name for new image file
            $image_new_name = image_unique_name($image_edit_name);
            $src_image_path = $new_root.$label_upload_path.$image_edit_name;
            $dst_image_path = $new_root.$label_path.$image_new_name;

            //move new edit image to label folder
            if(!rename($src_image_path, $dst_image_path)){
                $error = error_get_last();
                $var_result['success'] = false;
                $var_result['error'] = "save_image failed to move 'new' file to label folder error: $error src: $src_image_path dst: $dst_image_path";
                return $var_result;
            }

            //delete old saved image and update db
            if(delete_image_saved($image_saved_name)){
                //remove image name from vintage record
                $vintage_obj = new vintage();
                $where = "vintage_id = $vintage_id";
                $input_array['image1'] = $image_new_name;
                $update_result = $vintage_obj ->update($input_array, $where);

                if($update_result){
                    //update session
                    $_SESSION['var_vintage_temp']['var_images']['saved']['name'] = $image_new_name;
                    $_SESSION['var_vintage_temp']['var_images']['saved']['status'] = 'saved';
                    unset($_SESSION['var_vintage_temp']['var_images']['edit']);
                    
                    $var_result['success'] = true;
                    $var_result['msg'] = "edit_image saved as save_image and db update successful";
                    return $var_result;
                }else{
                    $sql_error = $vintage_obj -> get_sql_error();
                    $var_result['success'] = false;
                    $var_result['error'] = "edit_image saved as save_image but db update failed with sql_error: $sql_error";
                    return $var_result;
                }
            }
  
        }
        
    }//new

} //save_image



function rotate_image(){
    //rotate edit image

    global $new_root, $label_path, $label_upload_path;
    $var_edit = $_SESSION['var_vintage_temp']['var_images']['edit'];
    //$var_temp = $_SESSION['var_vintage_temp']['var_images']['temp'];
    $var_saved = $_SESSION['var_vintage_temp']['var_images']['saved'];
    

    if(!$var_edit['name']){ //edit image name not set
        $var_result['success']=false;
        $var_result['error']="nothing set in session edit parameter";
        return $var_result;
    }

    //identify where image is located
    if($var_edit['name']==$var_saved['name']){
        //image is in saved location
        $image_name = $new_root.$label_path.$var_edit['name'];
    } else {
        //new image - so use upload location
        $image_name = $new_root.$label_upload_path.$var_edit['name'];
    }

    //identify file type from ext
    $ext = pathinfo($var_edit['name'], PATHINFO_EXTENSION);
  
    if(!file_exists($image_name)){ //check file exists
        $var_result['success']=false;
        $var_result['error']="Image file not found. Path = ".$image_name;
        return $var_result;
    }
        
    //rotate angle
    $degrees = filter_input(INPUT_POST, 'degrees', FILTER_SANITIZE_NUMBER_INT);
    if(!$degrees){ //if not set default to 90
        $var_result['success']=false;
        $var_result['error']="No rotate angle provided degree = ".$degrees;
        return $var_result;
    }
 
    //load image to memory - identify file type
    if($ext=='jpg' || $ext=="jpeg" || $ext=="JPG"){
        $source = imagecreatefromjpeg($image_name);
    } else if($ext=="png"){
        $source = imagecreatefrompng($image_name);
    } else if($ext=="gif"){
        $source = imagecreatefromgif($image_name);
    } else if($ext=="bmp"){
        $source = imagecreatefromBMP($image_name);
    } else {
        $var_result['success']=FALSE;
        $var_result['error']="image type not supported. ext = $ext";
        return $var_result;
    }

    //create new name for rotated image
    $new_name = image_unique_name($var_edit['name'],"jpg");

    // Rotate
    $rotate = imagerotate($source, $degrees, 0);
    if($rotate == false){
        $var_result['success']=false;
        $var_result['error']="imagerotate php function returned false";
        return $var_result;
    }

    // Output
    $new_image = $new_root.$label_upload_path.$new_name;

    if(imagejpeg($rotate, $new_image, 100) == false){
        //creating jpeg image of rotated image failed
        $var_result['success']=false;
        $var_result['error']="imagejpeg php function failed to create jpeg. returned false  file:".$new_name;
        return $var_result;
    }
    

    //update session and delete old file
    $result = put_image_session($new_name, 'new');
    if($result['success']==false){ //function failed pass through error
        return $result;
    }

    $var_result['success']=true;
    $var_result['msg']="rotated image succeessful file: ".$new_name;

    return $var_result;

}


function crop_image(){
    //image_manager: crop image

    global $new_root, $label_path, $label_upload_path;
    $var_edit = $_SESSION['var_vintage_temp']['var_images']['edit'];
    $var_temp = $_SESSION['var_vintage_temp']['var_images']['temp'];
    $var_saved = $_SESSION['var_vintage_temp']['var_images']['saved'];

    $edit_name = $var_edit['name'];

     //identify where image is located
    if($var_edit['name']==$var_saved['name']){
        //image is in saved location
        $image_name = $new_root.$label_path.$var_edit['name'];
    } else {
        //new image - so use upload location
        $image_name = $new_root.$label_upload_path.$var_edit['name'];
    }

     //dimensions
    $jpeg_quality = 100;
    //$disp_w = 200; //width of image box
    $disp_w = $_REQUEST['image_width'];
    $src = $image_name;
    $var_size = getimagesize($src);
    $ext = pathinfo($edit_name, PATHINFO_EXTENSION);
    $orig_w = $var_size[0];
    $orig_h = $var_size[1];
    $ratio = $orig_w/$disp_w;
    //$ratio = 1;


    $targ_w = ($_REQUEST['w'])*$ratio;
    $targ_h = ($_REQUEST['h'])*$ratio;
    $src_x = ($_REQUEST['x'])*$ratio;
    $src_y = ($_REQUEST['y'])*$ratio;
    $src_w = ($_REQUEST['w'])*$ratio;
    $src_h = ($_REQUEST['h'])*$ratio;
    
    $var_data = explode(',',"$targ_w,$targ_h,$src_x,$src_y,$src_w,$src_h,$disp_w");

    //check file exists
    if(file_exists($image_name)){  //crop image
        //load image to memory - identify file type
        if($ext=='jpg' || $ext=="jpeg"){
            $source = imagecreatefromjpeg($image_name);
        } else if($ext=="png"){
            $source = imagecreatefrompng($image_name);
        } else if($ext=="gif"){
            $source = imagecreatefromgif($image_name);
        } else if($ext=="bmp"){
            $source = imagecreatefromwbmp($image_name);
        } else {
            $var_result['success']=FALSE;
            $var_result['error']="image type not supported ext=$ext";
        }

        //create new name for cropped image
        $new_name = image_unique_name($var_edit['name'],"jpg");

        //creat new blank image
        if(function_exists('imagecreatetruecolor')){
            $dst =  imagecreatetruecolor($targ_w,$targ_h);
        }else{
            $var_result['success']=FALSE;
            $var_result['error']="imagecreatetruecolor failed";
            return $var_result;
        }

        //crop image
        if(!imagecopyresampled($dst,$source,0,0,$src_x,$src_y,$targ_w,$targ_h,$src_w,$src_h)){
            //crop failed
            $var_result['success']=FALSE;
            $var_result['error']="imagecopyresampled failed";
            return $var_result;
        }

        // Output
        $new_image = $new_root.$label_upload_path.$new_name;

        if(imagejpeg($dst, $new_image, $jpeg_quality)){ //image created successfully - saved as jpeg
            
            //update edit details
            $result = put_image_session($new_name, 'new');
            if($result['success']==false){ //function failed pass through error
                return $result;
            }
            
            $var_result['success']=true;
            $var_result['msg']="image cropped and image saved. file: ".$new_name;
            $var_result['data'] = $var_data;

        }else{
            //failed to crop
            $var_result['success']=false;
            $var_result['error']="crop image failed to save to file. file: ".$new_name;
            return $var_result;
        }

    }else{
        $var_result['success']=false;
        $var_result['error']="file not found. file: ".$image_name;
        return $var_result;
    }

    return $var_result;

}



function delete_image_edit(){
    //delete edit image

    global $new_root, $label_path, $label_upload_path;

    $edit_name = $_SESSION['var_vintage_temp']['var_images']['edit']['name'];

    if(!$edit_name){
        //no editname provided - so nothing to delete
        $var_result['success']= TRUE;
        $var_result['msg']='no edit image name provided - so nothing to delete';
        return $var_result;
    }
    
    $edit_image = $new_root.$label_upload_path.$edit_name; //construct full image path to upload/edit location
    
    if(!file_exists($edit_image)){ //check file exists
        //file not found - delete failed
        unset($_SESSION['var_vintage_temp']['var_images']['edit']);//clear session details
        $var_result['success']= true;
        $var_result['error']="file could not be found - nothing to delete. file=".$edit_image;
        return $var_result;
    }
    
    //check file is not used by any other vintage
        //look up image name in db excluding this vintage 
        //if it exists then remove ref from this vintage in db
        //exit without deleting
   
    if(!unlink($edit_image)){ //delete file
        $var_result['success']= FALSE;
        $var_result['error']="file delete failed. file=".$edit_image;
        return $var_result;
    }
    
    //delete was successful
    if($_REQUEST['edit_status'] == 'deleted'){
        //if file has been saved to label location it will need to be deleted on vintage close (temp image)
        $saved_image = $new_root.$label_path.$edit_name; 
        if(file_exists($saved_image)){
            //update session so that file will be deleted on vintage close
            $_SESSION['var_vintage_temp']['var_images']['edit']['status'] = 'deleted';
            $var_result['success']= TRUE;
            $var_result['msg']='edit image deleted - vintage session set to delete successfully';
            return $var_result;
        }
    }

    unset($_SESSION['var_vintage_temp']['var_images']['edit']);//clear session details

    $var_result['success']= TRUE;
    $var_result['msg']= "edit image delete successful";
    return $var_result;
 

}


function check_image_files($correct){
    //check if images exists and if correct is true - correct

    //not used

    debug("*****check_image_file*****",true);

    global $new_root, $label_path, $label_upload_path;
    $var_images = $_SESSION['var_vintage_temp']['var_images'];

    $saved_name = $var_images['saved']['name'];
    $saved_image = $new_root.$label_path.$saved_name;
    if(file_exists($saved_image)){
        //nothing to do
        $debug .= "saved_image file found";
    }else{
        //saved image file does not exist
        if($correct){
            //remove from vintage record and session
        }
    }

}

function get_image(){
    //get image details from session

    global $new_root, $label_path, $label_upload_path;

    $var_images = $_SESSION['var_vintage_temp']['var_images'];

    if(!$var_images){ //session is empty
        $var_result['success'] = true;
        $var_result['edit_image'] = null;
        $var_result['msg'] = 'no images details in session to return';
        return $var_result;
    }
        
    $var_result['saved_name'] = $var_images['saved']['name']; //saved in db
    $var_result['saved_status'] = $var_images['saved']['status']; //saved in db

    $var_result['temp_name'] = $var_images['temp']['name']; //vintage page - before commit to db
    $var_result['temp_status'] = $var_images['temp']['status']; //vintage page - before commit to db

    $var_result['edit_name'] = $var_images['edit']['name']; //edit page
    $var_result['edit_status'] = $var_images['edit']['status']; //edit page
        
        //Check if edit_name image exists and if not clear session if status not deleted
        //TODO: handle deleted images
        //TODO: handle if image is not found clear session
        
    $edit_name = $var_images['edit']['name'];
    $filename = $new_root.$label_upload_path.$edit_name;

    if(file_exists($filename)){ //filename exists get details to return to page
        $src = $filename;
        $var_size = getimagesize($src);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $data['orig_w'] = $var_size[0];
        $data['orig_h'] = $var_size[1];
        $data['ext'] = $ext;
        
        //return values
        $var_result['success'] = true;
        $var_result['msg'] = "edit_image file found";
        $var_result['data'] = $data;
        return $var_result;
        
    }else{ //edit filename does not exist
        if($var_images['edit']['status'] <> 'deleted'){ //not deleted so it must be missing unset session
            unset($_SESSION['var_vintage_temp']['var_images']['edit']);
            $var_result['success'] = true;
            $var_result['msg'] = "edit_image file not found so session cleared";
            return $var_result;
        }else{
            $var_result['success'] = true;
            $var_result['msg'] = "no edit_image found";
            return $var_result;
        }
    }

}



function delete_image_temp(){
    //delete temp image - when closing vintage form

    //get image details from session
    $var_saved = $_SESSION['var_vintage_temp']['var_images']['saved'];
    $var_temp = $_SESSION['var_vintage_temp']['var_images']['temp'];

    if ($var_saved['name']==$var_temp['name']){
        //temp name is same as saved name then do not delete image it is associated
        //with vintage

        //clear session details
        unset($_SESSION['var_vintage_temp']['var_images']);

        $var_result['success']=true;
        $var_result['msg']='temp image name and saved image name match - no delete required';
        return $var_result;

    }else{
        //temp image is new or modifed and not saved so can be deleted
        //as it is new it will be in upload location

        //provide root location
        $new_root = rtrim($root, '/\\');

        //construct full image path
        $temp_image = $new_root.$label_upload_path.$var_temp['name'];

        //check file exists
        if(file_exists($temp_image)){
            //delete file
            if(unlink($temp_image)){

                //clear session details
                unset($_SESSION['var_vintage_temp']['var_images']);

                $var_result['success']=true;
                $var_result['msg']="temp image deleted OK. file=".$temp_image;
                return $var_result;
            }else{
                //delete failed
                $var_result['success']=false;
                $var_result['error']="temp image deleted FAILED. file=".$temp_image;
                return $var_result;
            }

        } else {
            //file not found - delete failed
            $var_result['success']=TRUE;
            $var_result['msg']="file could not be found - delete not required. file=".$temp_image;
            return $var_result;
        }

    }

}


function image_edit_to_tmp(){

    $_SESSION['var_vintage_temp']['image1_tmp'] = $_SESSION['var_vintage_temp']['image1'];
    $var_result['success'] = true;
    return $var_result;

}


function put_image_session($image_name=null, $image_status=null){
    //put newly uploaded edit image to session and delete previous edit image if applicable
    
    global $label_path, $label_upload_path;
    
    $root = rtrim($_SERVER['DOCUMENT_ROOT'],'/\\');
    
    if(!$image_name){ //no parameters provided retrieve from POST
        $image_name = $_REQUEST['edit_name'];
        $image_status = $_REQUEST['edit_status'];
    }
    
    if(!$image_name){ //no parameter and no post name provided
        $var_result['success'] = false;
        $var_result['error'] = "put_image_session: No image_name provided cannot continue file: $image_name";
        return $var_result;
    } 
    
    //set variables
    $var_edit = $_SESSION['var_vintage_temp']['var_images']['edit']; //get current edit image details
    $prev_edit_name = $var_edit['name'];
    $new_edit_name = $image_name;
    $new_edit_status = $image_status;
    $new_edit_path = $root.$label_upload_path.$new_edit_name;
    $prev_edit_path = $root.$label_upload_path.$prev_edit_name;
    
    //check new file has been uploaded/exists
    if(!file_exists($new_edit_path)){ //new file exists - delete old file
        $var_result['success'] = false;
        $var_result['error'] = "New edit_image file not found, put_image_session aborted filename: $new_edit_path";
        return $var_result; 
    }
    
    //delete prev_image
    if($prev_edit_name && file_exists($prev_edit_path)){ //check prev_image exists before attempting delete
       if(!unlink($prev_edit_path)){ //delete prev_image
           $var_result['success'] = false;
           $var_result['error'] = "Deleteing prev_image file failed, put_image_session aborted prev_image filename: $prev_edit_path";
           return $var_result; 
       }else{
           $del_msg = "Deleted prev_image: $prev_edit_name. ";
       }
    }
    
    //update session
    $_SESSION['var_vintage_temp']['var_images']['edit']['name'] = $new_edit_name;
    $_SESSION['var_vintage_temp']['var_images']['edit']['status'] = $new_edit_status;
    
    //confirm success
    $var_result['success'] = true;
    $var_result['msg'] = "$del_msg Updated session with new_image: $new_edit_name";
    return $var_result; 
     
}


function update_edit_image($edit_name=false, $edit_status=false){
    //handle updating of edit image

    global $label_path, $label_upload_path;

    //$var_temp = $_SESSION['var_vintage_temp']['var_images']['temp'];
    $var_edit = $_SESSION['var_vintage_temp']['var_images']['edit'];


    if($_REQUEST['edit_name']){ //set edit_name from post - overide parameter
        $edit_name = $_REQUEST['edit_name'];
        $edit_status = $_REQUEST['edit_status'];
    }
    
    if(!$edit_name){ //no name provided cannot continue
        $var_result['success'] = false;
        $var_result['error'] = " No filename name provided cannot continue";
        return $var_result;
    }

    $prev_edit_name = $var_edit['name'];
    $new_edit_name = $edit_name;
    $new_edit_status = $edit_status;

    //$old_temp_name = $_SESSION['var_vintage_temp']['var_images']['temp']['name'];
    //$old_temp_status = $_SESSION['var_vintage_temp']['var_images']['temp']['status'];

    if($new_edit_name <> $prev_edit_name){ //check something has changed
        //if($prev_edit_name == $var_temp['name']){
            //previous edit image is current temp so do not delete
            //$bln_delete_prev = false;
        //} else {
            //previous edit image is different to temp - has not been saved - can be deleted
            //$bln_delete_prev = true;
            //delete prev edit image
            $prev_edit_image = $label_upload_path.$prev_edit_name;
            if(!delete_image($prev_edit_image)){
                $var_result['success'] = false;
                $var_result['error'] = "failed to delete previous edit image: $prev_edit_image";
                return $var_result;
            }
        //}

        //update session
        if($new_edit_name){
            //update session
            $_SESSION['var_vintage_temp']['var_images']['edit']['name'] = $new_edit_name;
            $_SESSION['var_vintage_temp']['var_images']['edit']['status'] = $new_edit_status;
            $var_result['success'] = true;
            $var_result['msg'] = 'Session updated with new image name';
        } else {
            //clear session
            unset($_SESSION['var_vintage_temp']['var_images']['edit']);
            $var_result['success'] = true;
            $var_result['msg'] = 'No new_edit_name so session cleared';
        }

    }else{
        //edit image has not been changed - nothing to do
        $var_result['success'] = TRUE;
        $var_result['msg'] .= " > edit image not changed - no action taken";
        return $var_result;
    }


    //save edit image to temp
    if($_REQUEST['save_edit']){
        //delete old temp/edit image
        if($old_temp_name && ($old_temp_status == 'new') && ($old_temp_name <> $new_edit_name)){
            //delete old edit image
            $old_temp_image = $label_upload_path.$old_temp_name;
            if(delete_image($old_temp_image)){
                //delete successful
                $var_result['msg'] .= "Deleted old_temp_image successful image: $old_temp_name";
                $var_result['success'] = true;
            }
        } else {
            $var_result['success'] = true;
            $var_result['msg'] = "temp image empty or not new or still used - not deleted file: $old_temp_name status: $old_temp_status";
        }

        //set is_dirty session variable to true for vintage
        $_SESSION['var_vintage_temp']['is_dirty'] = true;

    }

    return $var_result;

}





function image_copy_to_upload($image_name){
    //copy image to upload location
    //and set as edit image
    
    global $new_root, $label_path, $label_upload_path;
    
    $image_name = ($image_name ? $image_name : $_REQUEST['image_name']);

    if(!$image_name){ //no image file to copy
        $var_result['success'] = false;
        $var_result['error'] = 'image_name parameter is empty - nothing to do';
        return $var_result;
    }
    
    //delete previous edit image before copying new one
    $var_response = delete_image_edit();
    if($var_response['success']==false){
        $var_result['success'] = false;
        $var_result['error'] = 'failed to delete existing edit image. error returned: '.$var_response['error'];
        return $var_result;
    }

    //check file exists and copy to upload folder for editing
    $src_path = $new_root.$label_path.$image_name;
    $dst_path = $new_root.$label_upload_path.$image_name;
    
    if(file_exists($src_path)){ //file exists copy image
        if (!copy($src_path, $dst_path)) {
            $var_result['success'] = false;
            $var_result['error'] = 'failed to copy image file to upload folder';
            return $var_result;
        } 
    }else{ //CHECK THIS file does not exist clear session to prevent errors 
        unset($_SESSION['var_vintage_temp']['var_images']['edit']['name']);
        unset($_SESSION['var_vintage_temp']['var_images']['edit']['status']);
        $var_result['success'] = true;
        $var_result['msg'] = "image file does not exist - cleared from session file: $image_name";
        return $var_result;
    }
    
    
    //update session to point at file
    $_SESSION['var_vintage_temp']['var_images']['edit']['name'] = $image_name;
    $_SESSION['var_vintage_temp']['var_images']['edit']['status'] = 'new';
    
    $var_result['success'] = true;
    $var_result['msg'] = 'image copied ready to edit';
    return $var_result;

    
}




function put_image_vintage(){
    //put image details to session - before opening edit page
    
    global $new_root, $label_path, $label_upload_path;
    $var_images = $_SESSION['var_vintage_temp']['var_images'];
    $image_name = $var_images['temp']['name'];

    if(!$image_name){ //no image file to copy
        $var_result['success'] = true;
        $var_result['msg'] = 'vintage has no temp image to copy - nothing to do';
        return $var_result;
    }

    //check file exists and copy to upload folder for editing
    $src_path = $new_root.$label_path.$image_name;
    $dst_path = $new_root.$label_upload_path.$image_name;
    
    if(file_exists($src_path)){ //file exists copy image
        if (!copy($src_path, $dst_path)) {
            $var_result['success'] = false;
            $var_result['error'] = 'failed to copy image file to upload folder';
            return $var_result;
        } 
    }else{ //file does not exist clear session to prevent errors
        unset($_SESSION['var_vintage_temp']['var_images']['temp']['name']);
        unset($_SESSION['var_vintage_temp']['var_images']['temp']['status']);
        $var_result['success'] = true;
        $var_result['msg'] = "image file does not exist - cleared from session file: $image_name";
        return $var_result;
    }
    
    
    //update session to point at file
    $_SESSION['var_vintage_temp']['var_images']['edit']['name'] = $image_name;
    $_SESSION['var_vintage_temp']['var_images']['edit']['status'] = 'saved';
    
    $var_result['success'] = true;
    $var_result['msg'] = 'image copied ready to edit';
    return $var_result;

    
}






//_____OUTPUT RESULT_____

//echo json_encode($var_result);

?>
