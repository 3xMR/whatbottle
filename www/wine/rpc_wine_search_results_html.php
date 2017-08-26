<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");
//require_once("$root/classes/class.timer.php");
require_once("$root/classes/class.wine_search.php");
//$timer = new Timer();


//search variables
//$wine_id = $_SESSION['var_wine_search_criteria']['wine_id'];
//$search_text = $_SESSION['var_wine_search_criteria']['search_text'];
//$producer_id = $_SESSION['var_wine_search_criteria']['producer_id'];
//$winetype_id = $_SESSION['var_wine_search_criteria']['winetype_id'];
//$country_id = $_SESSION['var_wine_search_criteria']['country_id'];
//$region_id = $_SESSION['var_wine_search_criteria']['region_id'];
//$subregion_id = $_SESSION['var_wine_search_criteria']['subregion_id'];
//$Award = $_SESSION['var_wine_search_criteria']['check_has_award'];
//$Note = $_SESSION['var_wine_search_criteria']['check_has_note'];
//$merchant_id = $_SESSION['var_wine_search_criteria']['merchant_id'];
//$acquire_id = $_SESSION['var_wine_search_criteria']['acquire_id'];
//$available = $_SESSION['var_wine_search_criteria']['available'];



//get list of wines
$search_obj = new wine_search();

$varSearchParam = $_SESSION['var_wine_search_criteria']; //get search parameters from session

$results_filtered = array_sum($varSearchParam) > 0 ? 'true' : 'false'; //used to flag to JS function 'set_reset_button' on index.php that results are filtered and show reset button
echo "<input type=\"hidden\" id=\"search_filter_status\" value=\"$results_filtered\" >";

$varSearchParam['type'] = "wines"; //update parameters
$varSearchParam['group'] = "tblWine.wine_id";
$varSearchParam['order'] = "last_modified DESC";
$resSearch = $search_obj -> search($varSearchParam); //search

if($_SESSION['index_page_pagination']['current_page']){
    $page_num = $_SESSION['index_page_pagination']['current_page'];
}else{
    $page_num = $_SESSION['index_page_pagination']['current_page'] = 1;
}

//get only one page of wine records
$results = $search_obj ->get_wines($page_num);
$arr_page = $search_obj ->get_page_numbers();
$str_pagination =  " $page_num of ".$arr_page['num_pages']." ";

if($results){
    //step through wine results and render

    foreach($results as $key => $row){
       //wine details
        $wine_id = $row['wine_id'];
        $wine_name = $row['wine'];
        $producer = $row['producer'];
        $wine_type = $row['winetype_id'];
        $full_name = $wine_name." (".$producer.")";
        $flag = "/images/flags/".$row['flag_image'];
        $country = $row['country'];
        $region = $row['region'];
        $subregion = $row['subregion'];
        $location = "$country, $region".($subregion ? ", $subregion" : ""); 

        $rst_vintages = $search_obj ->get_vintages($wine_id);
        $vintage_count = count($rst_vintages);

        //display wine
        echo "<div class=\"wine_accordian\" id=\"wine_accordian_$wine_id\" >";
            echo "<table class=\"wine\" id=\"wine_table\">";
                echo "<tr>";
                    //expand indicator
                    echo "<td valign=middle >";
                        if($vintage_count>0){
                           echo "<div class=\"wine_panel_toggle click arrow_right\" id=\"arrow_indicator_$wine_id\" style=\"width:30px; height:30px; margin-left:10px; background-color:\" ></div>";
                        }else{
                            echo "<div style=\"width:30px; height:30px; margin-left:10px;\" ></div>";
                        }
                    echo "</td>";

                    //wine type indicator
                    if($wine_type==1 || $wine_type==8){
                        $colour = "#C92F24";
                    } else {
                        $colour = "whitesmoke";
                    }

                    echo "<td style=\"background-color:$colour;\" class=\"wine_panel_toggle click\" >";
                        echo "<div style=\"width:15px;\"></div>";
                    echo "</td>";

                    //country flag
                    echo "<td width=60 align=center class=\"wine_panel_toggle click\" >";
                        echo "<img src=\"$flag\" width=\"28px\" height=\"28px\" />";
                    echo "</td>";

                    //wine name
                    echo "<td width=800 align=left  class=\"wine_panel_toggle click\" >";
                        echo "<p style=\"color:#363A36; font-size:18px; padding-top:3px; padding-bottom:3px;\">$full_name</p>";
                        echo "<p style=\"color:darkgray; font-size:14px; padding-bottom:3px;\">$location</p>";
                    echo "</td>";

                    echo "<td valign=middle >";
                        if (is_authed()){
                            echo "<div class=\"wine_menu click\" id=\"wine_menu_$wine_id\" style=\"float:right; padding-left:10px; padding-right:15px;\">";
                                echo "<img value=\"$wine_id\"  src=\"/images/show_grey_flat_32.png\" width=\"24px\" height=\"24px\" >";
                            echo "</div>";
                        }
                    echo "</td>";
                    
                echo "</tr>";
            echo "</table>";
        echo "</div>"; //wine_accordian

        //*** Vintage Details ***
        $rst_vintages = $search_obj ->get_vintages($wine_id);

        echo "<div class=\"vintages_panel hidden\" id=\"vintages_panel_$wine_id\" >";

            foreach ($rst_vintages as $key => $rowVintage ){
                
                //print_r($rowVintage);
                $vintage_id = $rowVintage['vintage_id'];
                $year = $rowVintage['year'] == 0 ? "n/a" : $rowVintage['year'];

                //ratings
                $vintage_quality = $rowVintage['vintage_quality'];
                $quality_width = ($vintage_quality*10)."px";
                $vintage_value = $rowVintage['vintage_value'];
                $value_width = ($vintage_value*18)."px";
                
                
                //display vintage
                echo "<div class=\"vintage_accordian\" id=\"vintage_accordian_$vintage_id\" >";
                    echo "<table class=\"vintage\" id=\"vintage_accordian_table\">";
                        echo "<tr>";
                            echo "<td style=\"vertical-align:middle;\" class=\"year vintage_panel_toggle\" >";
                                echo "<div style=\"width=24px; float:left; padding-left:10px; \" >";
                                    echo "<div class=\"click vintage_expanded_indicator arrow_right\" id=\"arrow_indicator_vintage_$vintage_id\" style=\"width:30px; height:30px; margin-left:10px; \" ></div>";
                                echo "</div>";
                            echo "</td>";
                            echo "<td style=\"vertical-align:middle;\" class=\"year vintage_panel_toggle\" \" >";
                                echo "<h2 style=\"padding-right:20px;\" >$year</h2>";
                            echo "</td>";
                            echo "<td style=\"vertical-align:middle;\"  class=\"rating vintage_panel_toggle\" id=\"vintage_bar_$vintage_id\">";
                                //ratings
                                echo "<div class=\"vertical-centre\" style=\"height:22px; width:200px; margin:0px; padding:0px;\" >";
                                echo "<div class=\"quality-static-rating-medium\" style=\"width:$quality_width; float:left;\" ></div>";
                                echo "<div class=\"value-static-rating-medium\" style=\"width:$value_width; float:left; margin-left:10px;\"></div>";
                                echo "<div>";
                            echo "</td>";
                            echo "<td class=\"spacer vintage_panel_toggle\" id=\"vintage_bar_$vintage_id\">";
                                echo "&nbsp";
                            echo "</td>";
                            

                            //vintage buttons
                            echo "<td class=\"actions\" >";
                                echo "<input type=\"image\" class=\"btn_edit_vintage\" value=\"$vintage_id\" id=\"edit_$vintage_id\" name=\"btn_edit_vintage\" src=\"/images/edit_flat_grey_24.png\" width=\"18\" height=\"18\">";
                            echo "</td>";
                            echo "<td class=\"actions\">";
                               echo "<input type=\"image\" class=\"btn_add_to_basket\" id=\"$vintage_id\" name=\"btn_add_to_basket\" src=\"/images/basket_flat_grey_24.png\" width=\"18\" height=\"18\">";
                            echo "</td>";
                            echo "<td class=\"actions\" >";
                                if (is_authed()){
                                    echo "<img value=\"$vintage_id\" class=\"btn_add_tasting_note click\" id=\"add_note_$vintage_id\" src=\"/images/notes_flat_grey_24.png\" width=\"18px\" height=\"18px\" >";
                                }
                            echo "</td>";
          
                    echo "</tr>";
                echo "</table>";

                echo "<div class=\"vintage_details\" id=\"vintage_details_$vintage_id\" >";

                    echo "<div class=\"left_column\" style=\"width:20%; \" >";

                        //Label Image
                        echo "<div class=\"wordwrap\" style=\"padding-top:10px; \" >";

                            $file_name = $rowVintage['image1'];
                            $new_root = rtrim($root, '/\\');

                            if($file_name > ""){
                                //TODO: resolve issue with trailing slash difference between .local and .com

                                $image_path = $label_path.$file_name;
                                if(file_exists($new_root.$image_path)){
                                    echo "<img style=\"display:block; margin-left:auto; margin-right:auto;\" ".fnImageResize($image_path,$new_root,160)." />";
                                } else {
                                    echo "<br/>file NOT found";
                                }
                            }else{
                                //no image file provided show default
                                //TODO: click to add image
                                $image_path = "/images/winebottle.jpg";
                                $img = fnImageResize($image_path, $new_root, 160);
                                echo "<img style=\"display:block; margin-left:auto; margin-right:auto;\" $img />";
                            }

                        echo "</div>";

                    echo "</div>"; //left_column

                    echo "<div class=\"centre_column\" style=\"width:40%; padding-left:20px; border-left:dashed 1px lightgray;\" >";

                         //Wine Type
                        echo "<h3>Type</h3>";
                        echo "<p class=\"text_2\" >".$rowVintage['winetype']."</p>";
                        
                        //Drinking Guide
                        if(!empty($rowVintage['drink_year_to'])){
                           echo "<h3>Drinking Guide</h3>";
                           $drink_text = null;
                           if(!empty($rowVintage['drink_year_from'])){
                               $drink_text = $rowVintage['drink_year_from']." - ";
                           }
                           $drink_text .= $rowVintage['drink_year_to'];
                           echo "<p class=\"text_2\" >$drink_text</p>";
                        }

                        //Grapes
                        echo "<h3 style=\"margin-top:10px;\">Grapes</h3>";
                        $grapes_obj = new vintage_has_grape;
                        $where = " vintage_id = ".$vintage_id;
                        $var_grapes = $grapes_obj -> get_extended($where);

                        if(!empty($var_grapes)){
                            foreach ($var_grapes as $var_grape){
                                echo "<p class=\"text_2\">";
                                    echo $var_grape['grape'];
                                    if($var_grape['percent']>0){
                                        echo " (".$var_grape['percent']."%)";
                                    }
                                echo "</p>";
                            }
                        } else {
                            //no grapes added
                            echo "<p class=\"text_2\"> - </p>";
                        }
                      

                        //Alcohol
                        echo "<h3 style=\"margin-top:10px;\">Alcohol</h3>";
                        $str_alcohol = $rowVintage['alcohol'] > 0 ?  $rowVintage['alcohol']."%" : " - ";
                        echo "<p class=\"text_2\">$str_alcohol</p>";
                
                        
                        //Awards
                        $awards_obj = new vintage_has_award();
                        $where = "vintage_id = $vintage_id";
                        $var_awards = $awards_obj -> get_extended($where);
                        
                        echo "<h3 style=\"margin-top:10px;\">Awards:</h3>";
                        if(!empty($var_awards)){
                            foreach ($var_awards as $key => $var_award){
                                echo "<p class=\"text_2\" >".$var_award['award_org']." - ".$var_award['award']."</p>";
                            }
                        } else{
                            echo "<p class=\"text_2\"> - </p>"; //no awards to display
                        }
                        
                        //Comments
                        echo "<h3 style=\"margin-top:10px;\">Comments</h3>";
                        $str_comments = !empty( $rowVintage['vintage_notes'] ) ? $rowVintage['vintage_notes'] : " - ";
                        echo "<p class=\"text_2\">$str_comments</p>";
                        

                    echo "</div>"; //centre_column


                    echo "<div class=\"right_column\" style=\"width:40%; text-align:left; \" >";
                        
                        //Tasting Notes
                        echo "<h3>Tasting Notes</h3>";
                        $obj = new tasting_note();
                        $where = "vintage_id = $vintage_id";
                        $sort = "note_date DESC";
                        $var_notes = $obj -> get($where, $columns=false, $group=false, $sort, $limit=false);

                        if($var_notes){
                            foreach($var_notes as $note){
                                //return list of award orgs
                                $note_id = $note['note_id'];
                                $note_value = $note['note_value'];
                                $value_width = $note_value*16;
                                $value_width .= "px";
                                $note_quality = $note['note_quality'];
                                $quality_width = $note_quality*8;
                                $quality_width .= "px";
                                $note_date_db = $note['note_date'];
                                $note_date = $note_date_db > 0 ? date_us_to_uk($note_date_db,'d-M-Y') : null; //convert date
                                
                                echo "<div class=\"note_link link ignore_dirty\" id=\"$note_id\" style=\"float:left; width:300px; margin-bottom:5px; cursor:pointer; \">";
                                    echo "<div class=\"vertical-centre\" style=\"height:18px;\" >";
                                        echo "<div style=\"width:85px; float:left;\"><p class=\"text_2\" style=\"float:left\" >$note_date</p></div>";
                                        echo "<div class=\"quality-static-rating-small\" style=\"float:left; margin-left:15px; width:$quality_width; \" ></div>";
                                        echo "<div class=\"value-static-rating-small\" style=\"float:left; margin-left:5px; width:$value_width; \" ></div>";
                                    echo "</div>";
                                echo "</div>";
                                echo "<div class=\"clear\" ></div>";
                            }
                        } else {
                            echo "<p class=\"text_2\"> - </p>";
                        }

                        //Acquisitions
                        echo "<div id=\"con_acquisitions_vintage\" >";
                            echo "<h3 style=\"margin-top:5px; \" >Acquisitions</h3>";
                            $acquire_obj = new vintage_has_acquire();
                            $where = "vintage_id = $vintage_id";
                            $columns = "";
                            $group = "";
                            $sort = "acquire_date DESC";
                            $limit = "";
                            $var_acquires = $acquire_obj -> get_extended($where,$columns,$group,$sort,$limit);

                            if($var_acquires){
                                foreach($var_acquires as $acquire){
                                    $acquire_id = $acquire['acquire_id'];
                                    $acquire_merchant = $acquire['merchant'];
                                    $acquire_date = date_us_to_uk($acquire['acquire_date'],'d-M-Y');
                                    $acquire_qty = $acquire['qty'];
                                    $discounted_price = number_format($acquire['discounted_price'],2);
                                    $unit_price = number_format($acquire['unit_price'],2);

                                    echo "<div class=\"acquire_link link ignore_dirty\" id=\"$acquire_id\" style=\"float:left; width:325px; padding-bottom:5px; margin-bottom:5px; border-bottom:1px dashed lightgray;\" >";

                                        echo "<div style=\"float:left; width:75%; color:#606060;\" >";
                                            echo "<p>$acquire_merchant</p>";
                                        echo "</div>";
                                        echo "<div style=\"float:right; text-align:right; width:25%;\" >";
                                            echo "<p>$acquire_date</p>";
                                        echo "</div>";

                                        echo "<div class=\"clear\" ></div>";

                                        echo "<div style=\"margin-top:7px; \">"; //second row

                                            echo "<div style=\"float:left; width:33.3%; \" >";
                                                echo "<p style=\"font-size:80%; display:inline; color:#B5ADAD;\" >Quantity:</p>";
                                                echo "<p style=\"font-size:80%; display:inline; \" > $acquire_qty</p>";
                                            echo "</div>";

                                            echo "<div style=\"float:left; width:33.3%; \" >";
                                                echo "<p style=\"font-size:80%; color:#B5ADAD; display:inline;\" >Price Paid:</p>";
                                                echo "<p style=\"font-size:80%; display:inline;\" > £ $discounted_price</p>";
                                            echo "</div>";

                                            echo "<div style=\"float:left; text-align:right;  width:33.3%; \" >";
                                                echo "<p style=\"font-size:80%; color:#B5ADAD; display:inline;\" >Full Price:</p>";
                                                echo "<p style=\"font-size:80%; display:inline;\" > £ $unit_price</p>";
                                            echo "</div>";                                        

                                        echo "</div>"; //second_row

                                        echo "<div class=\"clear\" ></div>";

                                    echo "</div>"; //acquire_link
                                } //foreach acquisition
                                
                                //Available details
                                echo "<div id=\"con_available_$vintage_id\" style=\"float:left; width:100%;\" >";
                                    echo "<div class=\"vertical-centre input-main-label\" style=\"margin-top:5px;\" >";
                                        echo "<p style=\"color:#B5ADAD;\" >Available Bottles</p>";
                                        echo "<input type=\"image\" class=\"btn_edit_override\" style=\"float:left; margin-left:10px;\" value=\"$vintage_id\" id=\"override_$vintage_id\" name=\"btn_edit_override\" src=\"/images/edit_flat_grey_24.png\" width=\"16px\" height=\"16px\" >";
                                    echo "</div>";
                                    echo "<div class=\"clear\"></div>";
                                    $obj_vintage = new vintage($vintage_id);
                                    
                                    $acquisition_bottle_count = $obj_vintage ->get_acquisition_bottle_count();
                                    if($acquisition_bottle_count){
                                        echo "<div style=\"float:left; width:33.3%; \" >";
                                            echo "<p style=\"color:#B5ADAD; display:inline;\" >Acquired: </p>";
                                            echo "<p style=\"display:inline;\" >$acquisition_bottle_count</p>";
                                        echo "</div>";   
                                    }
                                    
                                    $available_bottle_count = $obj_vintage ->get_available_bottle_count();
                                    if(is_array($available_bottle_count)){
                                        echo "<div style=\"float:left; width:33.3%; \" >";
                                            echo "<p style=\"font-size:100%; color:#B5ADAD; display:inline;\" >Available: </p>";
                                            echo "<p style=\"font-size:100%; display:inline;\" >".$available_bottle_count['available_bottles']." </p>";
                                        echo "</div>";
                                    }
                                    echo "<div class=\"clear\"></div>";
                                echo "</div>";
  
                                
                            } else {
                                echo "<p class=\"text_2\"> - </p>"; //no acquisitions
                            }
                        
                            echo "<div class=\"clear\" ></div>";
                            
                        echo "</div>"; //con_acquisitions_vintage

                    echo "</div>"; //right_column


                    echo "<div class=\"clear\"></div>"; //clear all three columns

                echo "</div>"; //vintage_details

            echo "</div>"; //vintage_accordian

            }

        echo "</div>"; //vintages_panel

        echo "<div class=\"clear\" ></div>";
    } //next (wine)

}else{
    //no results
    echo "<p style=\"margin-top:10px; margin-left:10px;\" >No Results Found</p>";
}

//if($arr_page['num_pages']>1){
    echo "<div style=\" width:100%; height:30px; padding-bottom:5px; padding-top:5px; border-bottom:solid 1px gray; \" >";
        //pagination
        echo "<div class=\"vertical-centre con_pagination\" id=\"index_pagination\" style=\"height:30px; width:250px; padding-right:10px; float:right; \" >";
            echo "<img class=\"click\" id=\"btn_last\" src=\"/images/last_grey_flat_24.png\" height=\"18px\" width=\"18px\" />";
            echo "<img class=\"click\" id=\"btn_next\" src=\"/images/next_grey_flat_24.png\" height=\"18px\" width=\"18px\" />";
            echo "<span style=\"color:darkgray; font-size:14px;\" >$str_pagination</span>";
            echo "<img class=\"click\" id=\"btn_prev\" src=\"/images/previous_grey_flat_24.png\" height=\"18px\" width=\"18px\" />";
            echo "<img class=\"click\" id=\"btn_first\" src=\"/images/first_grey_flat_24.png\" height=\"18px\" width=\"18px\" />";
        echo "</div>";
    echo "</div>";
//}



?>
