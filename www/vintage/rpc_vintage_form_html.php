<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    require_once("$root/includes/init.inc.php");
    require_once("$root/functions/function.php");
    require_once("$root/classes/class.db.php");
    
    
//echo "<div class=\"con_form_fields\" >";
    
    //initialise
    $hide_form = false;
    
    //Deleted status
    if($_SESSION['var_vintage_temp']['status']==4){
        $msg = "Vintage deleted successfully";
        $hide_form = true;
    }
    
    //determine if results are filtered
    if($_SESSION['var_vintage_temp']['filtered']==true){
        echo "results are filtered";
    }
    

    if($_SESSION['var_vintage_temp']['status']>0){
        //load vintage - load vintage details into session

        $wine_id = $_SESSION['var_vintage_temp']['wine_id'];
        $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];
        $wine_name = $_SESSION['var_vintage_temp']['wine'];
        $year = $_SESSION['var_vintage_temp']['year'];
        $vintage_notes = $_SESSION['var_vintage_temp']['vintage_notes'];
        $alcohol = $_SESSION['var_vintage_temp']['alcohol'];
        $producer = $_SESSION['var_vintage_temp']['producer'];
        $country = $_SESSION['var_vintage_temp']['country'];
        $region = $_SESSION['var_vintage_temp']['region'];
        $subregion = $_SESSION['var_vintage_temp']['subregion'];
        $image_name = $_SESSION['var_vintage_temp']['var_images']['temp']['name'];
        $image_status = $_SESSION['var_vintage_temp']['var_images']['temp']['status'];
        $drink_year_from = $_SESSION['var_vintage_temp']['drink_year_from'];
        $drink_year_to = $_SESSION['var_vintage_temp']['drink_year_to'];
   
        $is_dirty = $_SESSION['var_vintage_temp']['is_dirty'];

        if( $_SESSION['var_vintage_temp']['status'] > 1){
            //existing vintage update year
            $year = $_SESSION['var_vintage_temp']['year'];
        }

    
    } else {
        //no details to get - redirect
        $redirect = True;
        echo '<br>could not retrieve information required to display vintage';

    }


   
    /*
     * ____Form_____
     *
     */

   
    //disable fields if not authed
    if(is_authed()){
         $disabled = null;
    }else{
        $disabled = "disabled='disabled'";
    }
   
    //***vintage form - required for validation ***
    echo "<form action=\"/vintage/vintage.php\" method=\"post\" id=\"frm_vintage\" name=\"frm_vintage\" autocomplete=\"false\" >";
    
    echo "<div class=\"rwd-con-50\" >";

        //hidden fields    
        echo "<input type=\"hidden\" value=\"$wine_id\" name=\"wine_id\" id=\"wine_id\" />";
        echo "<input type=\"hidden\" value=\"$vintage_id\" name=\"vintage_id\" id=\"vintage_id\" />";
        echo "<input type=\"hidden\" value=\"$is_dirty\" name=\"is_dirty\" id=\"is_dirty\" />";

        echo "<div class=\"input-main-label\" >";
            echo "<p>Vintage</p>";
        echo "</div>";
        
        echo "<div class=\"input-main bottom-spacer\" style=\"margin-left:5px;\" >";
            echo "<input type=\"number\" step=\"0\" max=\"9999\" style=\"width:4em; \" value=\"$year\" name=\"year\" id=\"year\" autocomplete=\"year\" $disabled />";
        echo "</div>";
 
        
        //Grapes
        echo "<div class=\"input-main-label vertical-centre clear-left click\"  id=\"edit_grapes\" >";
            echo "<p style=\"float:left;\" >Grapes</p>";
            echo "<img src=\"/images/edit_flat_grey_24.png\" style=\"width:1em; height:1em;\" id=\"edit_grapes_img\" class=\"click ignore_dirty\" />";
        echo "</div>";

        echo "<div class=\"input-main clear-left bottom-spacer\" >";
            $var_grapes = $_SESSION['var_vintage_temp']['var_grapes'];
            if(!empty($var_grapes)){
                foreach ($var_grapes as $var_grape){
                    //if($var_grape['percent']>0){
                        echo "<p style=\"margin-left:5px;\" >".$var_grape['grape']." (".$var_grape['percent']."%)</p></br>";
                    //} 
                }
            } else {
                //no grapes added
                echo "<p style=\"margin-left:5px;\" > - </p>";
            }  
        echo "</div>"; //form_input_input
 
        


        //Alcohol
        echo "<div class=\"vertical-centre input-main-label clear-left \" >";
            echo "<p>Alcohol</p>";
        echo "</div>";
        echo "<div class=\"vertical-centre input-main clear-left bottom-spacer\" >";
            echo "<input type=\"number\" step=\"0\" style=\"width:3.5em; float:left; margin-left:5px; text-align:right;\" value=\"$alcohol\" name=\"alcohol\" id=\"alcohol\" $disabled />";
            echo "<p>&nbsp; %</p>";
        echo "</div>";     
        
        
        //Ready To Drink
        echo "<div class=\"vertical-centre input-main-label clear-left\" >";
            echo "<p>Drinking Guide</p>";
        echo "</div>";
        echo "<div class=\"input-main vertical-centre clear-left bottom-spacer\" >";
            echo "<p>From </p><input type=\"number\" step=\"0\" style=\"width:4em; float:left; margin-left:5px; text-align:right;\" value=\"$drink_year_from\" name=\"drink_year_from\" id=\"drink_year_from\" $disabled />";
            echo "<p>&nbsp To </p><input type=\"number\" step=\"0\" style=\"width:4em; float:left; margin-left:5px; text-align:right;\" value=\"$drink_year_to\" name=\"drink_year_to\" id=\"drink_year_to\" $disabled />";
        echo "</div>";
  
        
    echo "</div>"; //column 1
    

    
    //***second column***
    echo "<div class=\"rwd-con-50\" >";
            

        
        
        //Awards
        echo "<div class=\"vertical-centre input-main-label click clear-left\" style=\"margin-top:10px;\" id=\"btn_edit_awards\"  >";
            echo "<p>Awards</p>";
            echo "<img src=\"/images/edit_flat_grey_24.png\" style=\"width:1em; height:1em;\" id=\"edit_awards\" class=\"ignore_dirty\" />";
        echo "</div>";

        echo "<div class=\"input-main clear-left bottom-spacer\" >"; 
                $var_awards = $_SESSION['var_vintage_temp']['var_awards'];
                if(!empty($var_awards)){
                    foreach ($var_awards as $award){
                        echo "<p style=\"margin-left:5px;\" >".$award['award_org']." - ".$award['award']."</p>";
                    }
                } else {
                    //no awards added
                    echo "<p style=\"margin-left:5px;\" > - </p>";
                }
        echo "</div>"; 
        
            
        //Vintage Comments
        echo "<div class=\"input-main-label clear-left\" >";
            echo "<p>Comments</p>";
        echo "</div>";

        echo "<div class=\"input-main clear-left bottom-spacer  \" >";
            echo "<textarea style=\"box-sizing:border-box; margin-left:5px; height:60px; width:98%; \" value=\"$vintage_notes\" name=\"vintage_notes\" id=\"vintage_notes\" class=\"_save_field\" $disabled >$vintage_notes</textarea>";
        echo "</div>";
        
        //image    
        echo "<div class=\"input-main-label vertical-centre clear-left click \" id=\"btn_edit_image\"  >";
            echo "<p>Image</p>";
            echo "<img src=\"/images/edit_flat_grey_24.png\" style=\"width:1em; height:1em;\" id=\"edit_image\" class=\"ignore_dirty\" />";
        echo "</div>";
    
        echo "<div class=\"image-placeholder click clear-left bottom-spacer\" id=\"image_con\" style=\"margin-left:5px; margin-top:5px;\" >";
            if($image_name){
                $new_root = rtrim($root, '/\\');
                $image_path = ($image_status=='new') ? $label_upload_path.$image_name : $label_path.$image_name;

                //set size of image to fit in placeholder
                list($source_width, $source_height, $type, $attr) = getimagesize("$new_root/$image_path");
                $target_width = 150;
                $target_height = 225;
                $height_ratio = $source_height/$target_height;
                $width_ratio = $source_width/$target_width;
                $target_ratio = ($height_ratio > $width_ratio) ? $height_ratio : $width_ratio;
                $set_height = $source_height/$target_ratio;
                $set_width = $source_width/$target_ratio;

                if(file_exists($new_root.$image_path)){
                    echo "<img src=\"$image_path\" width=\"$set_width\" height=\"$set_height\" style=\"display:block; margin-left:auto; margin-right:auto;\" />";
                } else {
                    echo "<p style=\"text-align:center; vertial-align:middle; line-height:225px; color:gray;\" >Image file NOT found</p>";
                }

            }else{
                //no image added
                echo "<p style=\"text-align:center; vertial-align:middle; line-height:225px; color:gray;\" >Click to Add Image</p>";
            }
        echo "</div>"; //image
        
            
        /*** Tasting Notes ***/
        //echo "<div class=\"input-main-label float-left clear-left\" >";
        //    echo "<p>Tasting Notes</p>";
        //echo "</div>";

        //echo "<div style=\"float:left; margin-left:5px; \" id=\"con_all_notes\"  >";
        //    //listBox populated by /vintage/rpc_all_notes_html.php
        //echo "</div>";

        /*** Acquisitions ***/
        //echo "<div class=\"input-main-label float-left clear-left top-spacer\" >";
       //     echo "<p>Acquisitions</p>";
        //echo "</div>";

        //echo "<div style=\"float:left; margin-left:5px; \" id=\"con_all_acquisitions\"  >";
            //listBox populated by
        //echo "</div>";

  
    echo "</div>"; //second column
    
    //echo "<div class=\"clear\"></div>";

    echo "</form>"; //required for validation


//push con_single_form bottom clear of all other divs
 //echo "<div class=\"clear\" ></div>";


?>

