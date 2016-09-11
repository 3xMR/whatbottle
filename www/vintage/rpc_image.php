<?php

/*
 * PHP handling of image upload & manipulati
*/

session_start();

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


$action = $_REQUEST['action'];


//Upload Image
if($_REQUEST['action'] == 'get_image'){
    //get image details from session
    
    $vintage_id = $_REQUEST['vintage_id'];
    $var_images = $_SESSION['var_vintage_temp']['var_images'];
    
    if($var_images){
        $var_result['saved_name'] = $var_images['saved']['name']; //saved in db
        $var_result['saved_status'] = $var_images['saved']['status']; //saved in db
        
        $var_result['temp_name'] = $var_images['temp']['name']; //vintage page - before commit to db
        $var_result['temp_status'] = $var_images['temp']['status']; //vintage page - before commit to db
        
        $var_result['edit_name'] = $var_images['edit']['name']; //edit page
        $var_result['edit_status'] = $var_images['edit']['status']; //edit page
        
        //file format
        
        //nnn_vintage_xx.ext
        //nnn = vintage_id or unique number for non committed images
        //xx = increment from 01 to 99 for multiple images
        //ext = file extension
        //
        //e.g. 278_vintage_01.jpg for commited vintage
        //temp_345676546378366635567738_vintage_01.jpg //vintage page
        // images/labels/uploads/345676546378366635567738_vintage_01.jpg //new image not yet associated with vintage
        // 
        // on save to vintage page which may not yet have vintage_id
        // images/labels/uploads/345676546378366635567738_vintage_01.jpg
        // 
        // on vintage save - vintage_id will be present
        // images/labels/278_vintage_01.jpg
        //
        // open vintage page
        // images/labels/278_vintage_01.jpg
        // saved = 278_vintage_01.jpg, temp = 278_vintage_01.jpg, edit = ""
        //
        // open image_manager page
        // images/labels/278_vintage_01.jpg
        // saved = 278_vintage_01.jpg, temp = 278_vintage_01.jpg, edit = 278_vintage_01.jpg
        //
        // upload new image
        // vintage_id available
        // saved = 278_vintage_01.jpg, temp = 278_vintage_01.jpg, edit = 01_278_vintage_01.jpg
        // 
        // upload new image again
        // saved = 278_vintage_01.jpg, temp = 278_vintage_01.jpg, edit = 02_278_vintage_01.jpg
        // prev = 01_278_vintage_01.jpg - check it is not ==temp and not associated with any vintages then delete
        // 
        // save to vintage
        // images/labels/uploads/02_278_vintage_01.jpg
        // saved = 278_vintage_01.jpg, temp = 02_278_vintage_01.jpg, edit = 02_278_vintage_01.jpg
        // 
        // save
        // check if 278_vintage_01.jpg is associated with other vintages - if it is then rename to first associated vintage and update references
        // if not - delete 278_vintage_01.jpg
        // move and rename images/labels/uploads/02_278_vintage_01.jpg to images/labels/278_vintage_01.jpg
        // saved = 278_vintage_01.jpg, temp = 278_vintage_01.jpg, edit = 278_vintage_01.jpg
        //
        // open to edit again
        // images/labels/278_vintage_01.jpg
        // saved = 278_vintage_01.jpg, temp = 02_278_vintage_01.jpg, edit = 02_278_vintage_01.jpg
        //
        //
        //
        //
        
        //return values 
        $var_result['success']= true;
        
    } else {
        //session is empty - check vintage_id
       
        $var_result['success']= true;
        $var_result['edit_image'] = null;
        $var_result['msg'] = 'no images details in var_images';
    }
    
    
}


if($_REQUEST['action'] == 'put_image'){
    //put_image names to session
   
    if($_REQUEST['edit_image']){
        $_SESSION['var_vintage_temp']['var_images']['edit_image'] = $_REQUEST['edit_image']; //image_manager page
    } else {
        //set it to empty
        //$var_images['edit_image'] = "";
    }
        
    if($_REQUEST['temp_image']){
        $_SESSION['var_vintage_temp']['var_images']['temp_image'] = $_REQUEST['temp_image']; //vintage page
    } else {
        //set it to empty
        //$var_images['temp_image'] = "";
    }
    
    if($_REQUEST['save_image']){
        $_SESSION['var_vintage_temp']['var_images']['save_image'] = $_REQUEST['save_image']; //vintage page
    } else {
        //set it to empty
        //$var_images['temp_image'] = "";
    }

    $var_result['success']=true;
    
}


if($_REQUEST['action'] == 'delete_image'){
    //put_image names to session
    
    //$label_path, $label_upload_path - set in init.inc.php
    
    $vintage_id = $_REQUEST['vintage_id'];
    $image_name = $_REQUEST['image_name'];
    //$image_path = $_REQUEST['image_path'];
    $image_status = $_REQUEST['image_status'];
    
    
    $var_images = $_SESSION['var_vintage_temp']['var_images'];
   
    
    
    if($image_name){
        //name provided
        
        
        //resolve trailing slash issue
        //image client path
        $new_root = rtrim($root, '/\\');
        $image_path = $label_path.$image_name;

        if(file_exists($new_root.$image_path)){
            //check image exists in location - may have to check both
   
            //determine image associations
     
        }else{
            $var_result['success'] = false;
            $var_result['error'] = 'image file not found';
        }

    } else {
        //set it to empty
        //$var_images['edit_image'] = "";
    }
        
    if($_REQUEST['temp_image']){
        $_SESSION['var_vintage_temp']['var_images']['temp_image'] = $_REQUEST['temp_image']; //vintage page
    } else {
        //set it to empty
        //$var_images['temp_image'] = "";
    }
    
    if($_REQUEST['save_image']){
        $_SESSION['var_vintage_temp']['var_images']['save_image'] = $_REQUEST['save_image']; //vintage page
    } else {
        //set it to empty
        //$var_images['temp_image'] = "";
    }

    $var_result['success']=true;
    
}


//Upload Image
if($action == 'Upload File'){
    //upload file to server location

   
    //identify file type
    //TODO: Add support for other image types
    if($_FILES['image_file']['type']=="image/jpeg"){
        $ext = ".jpg";
        $is_valid_image = true;
    }

    
    //is image file so continue
    if($is_valid_image){
         echo "<br/>=> is valid image file";

        //delete old temp file
        if ($_SESSION['var_vintage_temp']['image1_tmp'] <> $_SESSION['var_vintage_temp']['image1']){
            //current temp is new to this session and should be deleted
            delete_temp_image($root.$label_path);
        }

        $tmp_file = $_FILES['image_file']['tmp_name'];
        echo "<br/>=> uploaded temp name: $tmp_file";

        //new temp file name
        $file_name = unique_filename($ext);
        echo "<br/>=> new temp file name: $file_name";

        //file save destination
        $dest_file = $root.$label_path.$file_name;
        if($debug){echo "<br/>=> destination Path: $dest_file";};

        //move to images location
        move_uploaded_file($tmp_file, $dest_file);
        
         //save new temp name to session
        $_SESSION['var_vintage_temp']['image1_tmp'] = $file_name;
        
        echo "<br/>=> session: ";
        print_r($_SESSION);
        
        $blnShowImageForm = True;
    } else {
        //not an image file
        echo "<p class=\"error\">File is not an image file</p>";
        $blnShowImageForm = False;
    }
}


//_____OUTPUT RESULT_____

echo json_encode($var_result);

?>