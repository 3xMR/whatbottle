<?php
//start php session
session_start();

/*
 * 
 *
 */


$root = $_SERVER['DOCUMENT_ROOT'];
//$new_root = rtrim($root, '/\\');
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


//$wine_id = $_REQUEST['wine_id'];
//$vintage_id = $_REQUEST['vintage_id'];
//$year = $_REQUEST['year'];
//$status = $_REQUEST['status'];
//$action = $_REQUEST['action'];

if(isset($_REQUEST['src_page'])){
    $src_page = $_REQUEST['src_page'];
}

if(isset($_REQUEST['dst_page'])){
    $dst_page  = $_REQUEST['dst_page'];
}

if(isset($_REQUEST['this_page'])){
    $this_page  = $_REQUEST['this_page'];
}

if($_REQUEST['rpc_action']){
    //convert action to function call
    $fnc = $_REQUEST['rpc_action'];
    if(is_callable($fnc)){
        //call action as function
        $var_result = call_user_func($fnc);
        echo json_encode($var_result);
    }else{
        echo null;
    }

}

function test_deferred(){
    //function to test deferred jquery capability
    
    $test_id = $_REQUEST['test_id'];
    sleep($test_id);
    $var_result['success'] = false;
    $var_result['fnc'] = $_REQUEST['fnc'];
    $var_result['my_name'] = "Magnus";
    $var_result['value'] = $test_id*10;
    return $var_result;
    
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


function test_function(){
    

    $var_result['success'] = true;
    $var_result['value'] = 23456;
    return $var_result;
}


function page_flow_set(){
    //put page flow details to session
    
    //initialise
    $var_result = array();
    global $src_page, $dst_page, $this_page;
    
    //update session array with src/parent(return) page for dst/child page
    if($src_page && $dst_page){
        //clean-up dst page to remove post details
        $end_pos = strpos($dst_page,"?");
        if($end_pos > 0){
            //trim post request details
            $dst_page = substr($dst_page, 0, $end_pos);
        }

        $_SESSION['var_page_flow'][$dst_page] = $src_page;
        
        $var_result['dst_page'] = $dst_page;
        
        //get src_page for this_page
        $return_page = $_SESSION['var_page_flow'][$this_page];
        
        if ($return_page){
            $var_result['return_page'] = $return_page;
        } else {
            $var_result['return_page_error'] = "no return page found";
        }

        $var_result['success'] = true;
        return $var_result;
        
    } else {
        $var_result['success'] = false;
        $var_result['error'] = "src or dst page not provided src_page=$src_page and dst_page=$dst_page";
        return $var_result;
    }

}


function page_flow_return(){
    //get page flow details from session

    //initialise
    $var_result = array();
    global $src_page, $dst_page, $this_page;
    
    if($this_page){
        $src_page = $_SESSION['var_page_flow'][$this_page];
        if ($src_page){
            $var_result['success'] = true;
            $var_result['src_url'] = $src_page;
            return $var_result;
        } else {
            $var_result['success'] = false;
            $var_result['error'] = 'no src page returned';
            return $var_result;
        }
    }else{
        $var_result['success'] = false;
        $var_result['error'] = 'no page provided';
        return $var_result;
    }

}


function get_vintage_from_db(){
    //get vintage details from db and load to session
    
    $vintage_id = $_REQUEST['vintage_id'];
    if(isset($_REQUEST['wine_id'])){
        $wine_id = $_REQUEST['wine_id']; 
    }
    
    $action = $_REQUEST['action'];
    $status = $_REQUEST['status'];
    
    //check if page_action is 'add'
    if($_REQUEST['dst_action']='add'){
        //set staus to 1 (add)
        $status = 1;
        $action = 'add';
    }   
    
    $var_result = array();

    if($vintage_id){

        //clear existing session
        unset($_SESSION['var_vintage_temp']);

        //load vintage details and put to session
        $vintage = new vintage($vintage_id);
        $vintage_details = $vintage -> get_all();
        
        if($vintage_details){
            $_SESSION['var_vintage_temp'] = $vintage_details;
        }else{
            //error getting vintage details
            $sql_error = $vintage -> get_sql_error();
            $var_result['success']=false;
            $var_result['error']="error getting vintage details from db. sql_error = $sql_error";
            return $var_result;
        }
        

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

         //set status
        if(empty($_REQUEST['status'])){
            $_SESSION['var_vintage_temp']['status'] = 3; //read-only
        } else {
            $_SESSION['var_vintage_temp']['status'] = $_REQUEST['status'];
        }

        //set is_dirty to false
        $_SESSION['var_vintage_temp']['is_dirty'] = false;

        $var_result['success']=true;
        $var_result['name']="get_vintage_from_db";

    } else if($wine_id && $action == 'add'){
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


function commit_vintage_to_db(){
    //save vintage to db

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
            $var_result['error'] = 'no wine_id provided for new vintage save request';
            //terminate function
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
        $var_result['error'] = 'mandatory data; wine_id or year are missing';
        //terminate function
        return $var_result;
    }


    //commit to db

    //save vintage details

    //set user_id
    $var['user_id'] = $_SESSION['user_id'];

    if($bln_existing):
        //perform db update

        $obj_vintage = new vintage();
        $vintage_id = $var['vintage_id'];
        $where = "vintage_id = ".$vintage_id;
        log_write('save_vintage',1,"update record");
        $result = $obj_vintage -> update($var,$where);

        if($result==false){
            //update failed
            $var_result['success'] = false;
            $var_result['error'] = "db update failed";
            return $var_result;
        }else{
            //update successful
            $var_result['db_action'] = "update";
            //continue...
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
            $var_result['success'] = false;
            $var_result['error'] = 'db insert failed';
            return $var_result;
        endif;

    endif;


    //save image
    if(save_image()==false){
        //saving image failed
        $var_result['success'] = false;
        $var_result['error'] = 'save_image() returned false';
        return $var_result;
    }


    //save grape details
    $var_grapes = $_SESSION['var_vintage_temp']['var_grapes'];

    if(is_array($var_grapes)){
        log_write('save_vintage',1,"var_grapes array identified");
        //delete existing grapes from vintage_has_grapes table
        $obj_grapes = new vintage_has_grape();
        $where = "vintage_id = $vintage_id";
        $obj_grapes -> delete($where);

        if($obj_grapes === false){
            //delete operation failed - do not continue
            $var_result['success'] = 'false';
            $var_result['error'] = 'db delete of grapes failed - grapes not saved';
        } else {
            //save grapes
            foreach($var_grapes as $grape){
                $obj_grape = new vintage_has_grape();
                $grape['vintage_id'] = $vintage_id;
                $grape['user_id'] = $_SESSION['user_id'];
                $obj_grape -> add($grape);
                //TODO: capture error on saving grapes
            }
        }
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

    //save note details
    $var_result['success'] = true;
    //reset is_dirty
    $_SESSION['var_vintage_temp']['is_dirty']= false;
    return $var_result;
}




//_____GRAPES_____
//

function save_selected_grapes(){
    //put grapes array to session

    log_write("save_grapes",1,"save_selected_grape");
    $var = $_REQUEST['json_field'];
    $var = stripslashes($var);
    log_write($var,1,"save_selected_grape");
    $var_grapes = json_decode($var,true);
    log_write($var_grapes,1,"save_selected_grape");

    if(empty($var)){
        //no input data provided
        $var_result['success'] = false;
        $var_result['error'] = 'no input array provided';
        return $var_result;

    } else {

        if(!empty($var_grapes)){
            //grape array provided - save to session

            //clear old values
            $n=0;
            unset($_SESSION['var_vintage_temp']['var_grapes']);
            foreach($var_grapes as $var_grape){
                $obj = new grape();
                $where = 'grape_id = '.$var_grape['name'];
                $var_grape_db = $obj -> get($where);
                $var_grape_db[0]['percent'] =  $var_grape['value'];
                $_SESSION['var_vintage_temp']['var_grapes'][]= $var_grape_db[0];
                $n = $n + 1;
                log_write('grape_id = '.$var_grape['name'].' percentage = '.$var_grape['value'],1,"rpc_vintage.php");
            }

            //set is_dirty
            $_SESSION['var_vintage_temp']['is_dirty'] = true;

            $var_result['success'] = true;
            return $var_result;

        } else {
            //no array provided - clear session array

        }
    }
}




//____AWARDS_____

function put_temp_awards(){
    //put award temp details from vintage temp session
        $_SESSION['var_awards_temp'] = $_SESSION['var_vintage_temp']['var_awards'];
        $var_result['success']=true;
        return $var_result;
}


function save_selected_awards(){
    //transfer temp awards selction into vintage temp session

    $_SESSION['var_vintage_temp']['var_awards'] = $_SESSION['var_awards_temp'];
    unset($_SESSION['var_awards_temp']);


    //set is_dirty
    //TODO: compare sessions and set is_dirty only if changed
    $_SESSION['var_vintage_temp']['is_dirty'] = true;

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
        debug("old_image=$old_image");
    $new_image = $new_root.$new_image;
        debug("new_image=$new_image");

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

    $root = $_SERVER['DOCUMENT_ROOT'];
    $new_root = rtrim($root, '/\\');

    //add root to provide absolute path
    $image_name = $new_root.$image_name;
    //delete file

    if(file_exists($image_name)){
        if(unlink($image_name)){
            debug("delete_image successful file=$image_name");
            return true;
        } else {
            debug("delete_image unlink failed");
            return false;
        }
    }else{
        debug("delete_image unsuccessful - cannot find file=$image_name");
        return false;
    }

}


function save_image(){
    //save image to db

    debug("***** fnc: save_image ".date('Y-m-d H:i:s')."*****");
    debug("temp_name=".$_SESSION['var_vintage_temp']['var_images']['temp']['name']);

    global $new_root, $label_path, $label_upload_path;
    $var_temp = $_SESSION['var_vintage_temp']['var_images']['temp'];
    $var_saved = $_SESSION['var_vintage_temp']['var_images']['saved'];
    //$var_edit = $_SESSION['var_vintage_temp']['var_images']['edit'];

    if($var_temp['name'] <> $var_saved['name']){
        //temp image is different to saved image
        debug("temp image name is different to saved image name");

        //get details from session
        $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];
        $saved_name = $var_saved['name'];
        //$saved_ext = pathinfo($saved_name, PATHINFO_EXTENSION);
            debug("saved_name=$saved_name");
            debug("this vintage_id=$vintage_id");
            //debug("saved_ext=$saved_ext");
        $saved_image = $label_path.$saved_name;

        //determine if vintage already has an image
        if($saved_name){
            //vintage already has saved image - check for associations before deleting

            //is this image associated with any other vintages
            $vintage_obj = new vintage();
            $where = "image1 = '$saved_name' AND vintage_id <> $vintage_id";
            $vintage_count = $vintage_obj -> row_count($where);

            if($vintage_count){
                //associated vintages identified
                debug("image is associated with other vintages - do NOT delete");
                $bln_delete_saved = false;
            } else {
                debug("image is not associated with any other vintages - OK to delete");
                $bln_delete_saved = true;
            }

            //check saved file exists
            if(file_exists($new_root.$label_path.$var_saved['name'])){
                $saved_image_exists = true;
                debug("saved image does exist");
            } else {
                //file does not exist - clear session reference
                $saved_image_exists = false;
                debug("saved image does NOT exists - clear session");
                unset( $_SESSION['var_vintage_temp']['var_images']['saved']);
            }

        }

        //determine what to do with the temp image for this vintage
        if($var_temp['name']){
            //move new/modified image to saved location and rename
            $temp_name = $var_temp['name'];
            $temp_image = $label_upload_path.$temp_name;

            //construct new name for saved image
            $unique_name = image_unique_name($temp_name);
                debug("new_name=$unique_name");
            $new_image = $label_path.$unique_name;

            //rename temp image to new location
            if(image_rename($temp_image, $new_image)){

                    debug("rename temp image successful");

                //update session
                $_SESSION['var_vintage_temp']['var_images']['temp']['name']=$unique_name;
                $_SESSION['var_vintage_temp']['var_images']['temp']['status']='saved';

                $_SESSION['var_vintage_temp']['var_images']['saved']['name']=$unique_name;
                $_SESSION['var_vintage_temp']['var_images']['saved']['status']='saved';

                //update vintage record with new name
                $vintage_obj = new vintage();
                $where = "vintage_id = $vintage_id";
                    debug("where=$where");
                $input_array['image1'] = $unique_name;
                $update_result = $vintage_obj -> update($input_array, $where);

                if($update_result){
                    //update successful
                        debug("update vintage with new image successful");
                        $var_return = true;
                    //delete old saved image if not associated and it exists
                    if($bln_delete_saved && $saved_image_exists){
                        //delete old saved image
                        if(delete_image($saved_image)){
                            //delete image successful
                            debug("deleted old saved image file=$saved_image");
                            //no further action required - return function
                            return TRUE;
                        }else{
                            debug("delete old saved image FAILED file=$saved_image");
                            //problem with deleting the old saved image - return function
                            return false;
                        }
                    }

                }else{
                    //update failed - return function
                    debug("db update with new saved image failed");
                    return false;
                }


            }else{
                debug("rename and move of temp image to save location failed");
                return false;
            }


        }else{
            //Delete
            //temp name is different to saved image but temp is empty so delete saved and update vintage record
            debug("temp name is different to saved image but temp is empty so must be a delete action");

            if($bln_delete_saved && $saved_image_exists){
                //delete saved image if not associated with other vintages
                if(delete_image($saved_image)){
                    //delete image successful
                    debug("saved image deleted. file=$saved_image");
                }else{
                    debug("saved image delete FAILED. file=$saved_image");
                    return false;
                }
            }

            //clear session
            unset($_SESSION['var_vintage_temp']['var_images']['saved']);

            //remove image name from vintage record
            $vintage_obj = new vintage();
            $where = "vintage_id = $vintage_id";
            $input_array['image1'] = null;
            $update_result = $vintage_obj ->update($input_array, $where);

            if($update_result){
                debug("vintage update after delete successful");
                return true;
            }else{
                debug("update of vintage record failed");
                return false;
            }

        }


    } else {
        //temp has not been modified
        debug("temp not modified nothing to do");
        return true;
    }

    if($var_return == true){
        return true;
    } else{
        return false;
    }


}


function rotate_image($degrees){
    //rotate edit image 90 deg

    global $new_root, $label_path, $label_upload_path;
    $var_edit = $_SESSION['var_vintage_temp']['var_images']['edit'];
    $var_temp = $_SESSION['var_vintage_temp']['var_images']['temp'];
    $var_saved = $_SESSION['var_vintage_temp']['var_images']['saved'];


    if($_REQUEST['degrees']){
        $degrees = $_REQUEST['degrees'];
    }


    if($var_edit['name']){

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

        //check file exists
        if(file_exists($image_name)){
            //rotate image

            if($_REQUEST['degrees']){
                $degrees = $_REQUEST['degrees'];
            }else{
                $degrees = 90;
            }

            //load image to memory - identify file type
            if($ext=='jpg' || $ext=="jpeg"){
                $source = imagecreatefromjpeg($image_name);
            } else if($ext=="png"){
                $source = imagecreatefrompng($image_name);
            } else if($ext=="gif"){
                $source = imagecreatefromgif($image_name);
            } else if($ext=="bmp"){
                $source = imagecreatefromBMP($image_name);
            } else {
                $var_result['success']=FALSE;
                $var_result['error']="image type not supported ext=$ext";
            }

            //create new name for rotated image
            $new_name = image_unique_name($var_edit['name'],"jpg");

            // Rotate
            $rotate = imagerotate($source, $degrees, 0);

            // Output
            $new_image = $new_root.$label_upload_path.$new_name;

            if(imagejpeg($rotate, $new_image, 100)){
                //image created successfully - saved as jpeg

                //update edit
                update_edit_image($new_name);

                $var_result['success']=true;
                $var_result['msg']="rotated image saved. file".$new_name;

            }else{
                //creating rotated image failed
                $var_result['success']=false;
                $var_result['error']="rotated image failed to save to file. file:".$new_name;
            }

        }else{
            $var_result['success']=false;
            $var_result['error']="file not found. file:".$image_name;
        }
    }else{
        $var_result['success']=false;
        $var_result['error']="no edit name provided";
    }


    return $var_result;

}


function crop_image(){

    debug("***** fnc: crop_image *****");

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
    $disp_w = 300; //width of image box
    $src = $image_name;
    $var_size = getimagesize($src);
    $ext = pathinfo($edit_name, PATHINFO_EXTENSION);
    $orig_w = $var_size[0];
    $orig_h = $var_size[1];
    $ratio = $orig_w/$disp_w;
    //$ratio = 1;

    debug("orig_w=$orig_w orig_h=$orig_h ratio=$ratio");

    $targ_w = ($_REQUEST['w'])*$ratio;
    $targ_h = ($_REQUEST['h'])*$ratio;
    $src_x = ($_REQUEST['x'])*$ratio;
    $src_y = ($_REQUEST['y'])*$ratio;
    $src_w = ($_REQUEST['w'])*$ratio;
    $src_h = ($_REQUEST['h'])*$ratio;

    debug("targ_w=$targ_w targ_h=$targ_h src_x=$src_x src_y=$src_y");

    //check file exists
    if(file_exists($image_name)){
        //crop image

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

        if(imagejpeg($dst, $new_image, $jpeg_quality)){
            //image created successfully - saved as jpeg

            //update edit details
            update_edit_image($new_name);

            $var_result['success']=true;
            $var_result['msg']="image cropped and image saved. file".$new_name;

        }else{
            //creating rotated image failed
            $var_result['success']=false;
            $var_result['error']="crop image failed to save to file. file:".$new_name;
            return $var_result;
        }

    }else{
        $var_result['success']=false;
        $var_result['error']="file not found. file:".$image_name;
        return $var_result;
    }

    return $var_result;

}



function delete_image_edit(){
    //delete edit image

    global $new_root, $label_upload_path;
    $var_result = "";

    $var_edit = $_SESSION['var_vintage_temp']['var_images']['edit'];
    $var_temp = $_SESSION['var_vintage_temp']['var_images']['temp'];


    if ($var_temp['name']==$var_edit['name']){
        //edit name is same as temp name then do not delete image it will be required
        //by vintage form on close - delete handled by vintage form

        //clear session details
        unset($_SESSION['var_vintage_temp']['var_images']['edit']);

        $var_result['success']=true;
        $var_result['msg']='temp image name and edit image name match - no delete required';
        return $var_result;

    }else{
        //edit image is new or modifed and not saved to temp so can be deleted
        //as it is new it will be in upload location


        if($var_edit['name']){
            //construct full image path
            $edit_image = $new_root.$label_upload_path.$var_edit['name'];

            //check file exists
            if(file_exists($edit_image)){

                    //delete file
                    if(unlink($edit_image)){
                        //delete was successful

                        //clear session details
                        unset($_SESSION['var_vintage_temp']['var_images']['edit']);

                        $var_result['success']= TRUE;
                        $var_result['msg']='edit image deleted successfully';
                        return $var_result;

                    }else{
                        $var_result['success']= FALSE;
                        $var_result['error']="file delete failed. file=".$edit_image;
                        return $var_result;
                    }

            } else {
                //file not found - delete failed
                $var_result['success']= FALSE;
                $var_result['error']="file could not be found - delete failed. file=".$edit_image;
                return $var_result;
            }
        } else{
            //no editname provided - so nothing to delete
            $var_result['success']= TRUE;
            $var_result['msg']='no edit image name provided - so nothing to delete';
            return $var_result;
        }
    }

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

    if($var_images){
        $var_result['saved_name'] = $var_images['saved']['name']; //saved in db
        $var_result['saved_status'] = $var_images['saved']['status']; //saved in db

        $var_result['temp_name'] = $var_images['temp']['name']; //vintage page - before commit to db
        $var_result['temp_status'] = $var_images['temp']['status']; //vintage page - before commit to db

        $var_result['edit_name'] = $var_images['edit']['name']; //edit page
        $var_result['edit_status'] = $var_images['edit']['status']; //edit page

        //return values
        $var_result['success'] = TRUE;
        $var_result['msg']= 'details returned to page';


        //identify where image is located
        if($var_images['edit']['status']=='saved'){
            //image is in saved location
            $edit_image = $new_root.$label_path.$var_images['edit']['name'];
        } else {
            //new image - so use upload location
            $edit_image = $new_root.$label_upload_path.$var_images['edit']['name'];
        }

        //confirm edit image exists
        if(file_exists($edit_image)){
            //file exists
        }else{
            //set session to empty
            $var_result['edit_name'] = NULL;
            $var_result['edit_status'] = NULL;
        }


    } else {
        //session is empty - check vintage_id

        $var_result['success'] = TRUE;
        $var_result['edit_image'] = null;
        $var_result['msg'] = 'no images details in session to return';
    }

    return $var_result;
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


function update_edit_image($edit_name=false, $edit_status=false){
    //handle updating of edit image

    debug("***** fnc: update_edit_image *****");


    global $label_path, $label_upload_path, $_REQUEST;

    $var_temp = $_SESSION['var_vintage_temp']['var_images']['temp'];
    $var_edit = $_SESSION['var_vintage_temp']['var_images']['edit'];


    if($_REQUEST['edit_name']){
        //set edit_name - overide parameter
        $edit_name = $_REQUEST['edit_name'];
        $edit_status = $_REQUEST['edit_status'];
    }

    $prev_edit_name = $var_edit['name'];
    $new_edit_name = $edit_name;
    $new_edit_status = $edit_status;

    $old_temp_name = $_SESSION['var_vintage_temp']['var_images']['temp']['name'];
    $old_temp_status = $_SESSION['var_vintage_temp']['var_images']['temp']['status'];

        debug("prev_image_name=".$prev_edit_name);
        debug("new_edit_name=".$new_edit_name);
        debug("old_temp_name=".$old_temp_name);

    if($new_edit_name <> $prev_edit_name){
        //edit image has been changed
            debug("new edit name is different to pre edit name");

        if($prev_edit_name == $var_temp['name']){
            //prev edit image is current temp so do not delete
            $bln_delete_prev = FALSE;
                debug("temp name is same as prev edit name - set bln_delete to false");
        } else {
            //pre edit image is different to temp - has not been saved - can be deleted
                debug("temp name is different to prev edit name - set bln_delete to true");
            $bln_delete_prev = TRUE;
        }


        //update session
        if($new_edit_name){
            //update session
                debug("new edit name provided - session updated");
            $_SESSION['var_vintage_temp']['var_images']['edit']['name'] = $new_edit_name;
            $_SESSION['var_vintage_temp']['var_images']['edit']['status'] = 'new';

            $var_result['success'] = true;

        } else {
            //clear session
            unset($_SESSION['var_vintage_temp']['var_images']['edit']);
            $var_result['success'] = true;
        }


        if($bln_delete_prev){
            //delete prev edit image
                debug("flag set to delete previous edit image - delete file=$prev_edit_name");
            $prev_edit_image = $label_upload_path.$prev_edit_name;
            if(delete_image($prev_edit_image)){
                //delete successful
                    debug("delete prev image successful");
                    $var_result['success'] = true;
            }else{
                    debug("delete prev image failed");
                $var_result['success'] = FALSE;
                $var_result['error'] = "failed to delete prev edit image";
                return $var_result;
            }
        }


    }else{
        //edit image has not been changed - nothing to do
            debug("edit image has not changed - no action taken");
        $var_result['success'] = TRUE;
        $var_result['msg'] .= " > edit image not changed - no action taken";
    }


    //save edit image to temp
    if($_REQUEST['save_edit']){
        //save edit name to temp name
        debug("save_edit action");

        if($new_edit_name){
            //update session
                debug("saved edit to temp - update session");
            $_SESSION['var_vintage_temp']['var_images']['temp']['name'] = $new_edit_name;
            $_SESSION['var_vintage_temp']['var_images']['temp']['status'] = $new_edit_status;
            $var_result['saved']=TRUE;
        } else {
            //clear session - temp
                debug("clear temp session");
            unset($_SESSION['var_vintage_temp']['var_images']['temp']);
            $var_result['saved']=TRUE;
        }

        //delete old temp/edit image
        if($old_temp_name && ($old_temp_status == 'new') && ($old_temp_name <> $new_edit_name)){
            //delete old edit image
                debug("save_edit:  prev temp image status is new so OK to delete");
                $old_temp_image = $label_upload_path.$old_temp_name;
            if(delete_image($old_temp_image)){
                //delete successful
                debug("save_edit: delete old temp image successful file=$old_temp_name");
                $var_result['msg'] .= " > deleted old_temp_image=$old_temp_name";
                $var_result['success'] = TRUE;
            }
        } else {
            debug("save_edit:  temp image empty or not new or still used - not deleted file=$old_temp_name status=$old_temp_status");
            $var_result['success'] = TRUE;
            $var_result['msg'] .= " >> save_edit:  temp image empty or not new or still used - not deleted file=$old_temp_name status=$old_temp_status";
        }

        //set is_dirty session variable to true for vintage
        $_SESSION['var_vintage_temp']['is_dirty'] = true;


    }else{
        debug("saved action not set");
    }

    return $var_result;

}





function put_image_vintage(){
    //put image details to session - before opening edit page

    $var_images = $_SESSION['var_vintage_temp']['var_images'];

    if($var_images['temp']['name']){
        //image reference - update session
        $var_images['edit']['name'] = $var_images['temp']['name'];
        $var_images['edit']['status'] = $var_images['temp']['status'];
        $_SESSION['var_vintage_temp']['var_images'] = $var_images;
    }else{
        $var_result['success'] = true;
        $var_result['msg'] = 'no temp image to copy to edit image';
        return $var_result;
    }

    //$var_result['success'] = true;
    return $var_result;
}


?>

