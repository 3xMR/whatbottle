<?php
//display wines associated with producer when adding a new wine
//used in: rpc_wine_form_html.php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");
require_once("$root/classes/class.wine_search.php");



?>

<!--<div class="hidden">
    <input type="text" value="<?php //echo $_SESSION['var_wine_temp']['wine_id']; ?>" name="wine_id" id="wine_id"><br/>
    <input type="text" value="<?php //echo $_SESSION['var_wine_temp']['is_dirty'];?>" name="is_dirty" id="is_dirty"><br/>
    <input type="text" value="<?php //echo $_SESSION['var_wine_temp']['status'];?>" name="status" id="status"><br/>
</div>    -->

   <div class="input-main-label" >
        <p>Existing Wines</p>
   </div>

<div id="con_wines_from_producer_html" style="border-top:solid gray 1px;">
        
    <?php

    $producer_id = $_SESSION['var_wine_temp']['producer_id'];
    if(!$producer_id > 0 ){
        //show code for no producer found
        echo "No producer selected";
        goto NoProducerSelected; //jump to end of script
    }

    //get wines for producer
    $search_obj = new wine_search();
    $varSearchParam['type'] = "wines"; 
    $varSearchParam['group'] = "tblWine.wine_id";
    $varSearchParam['order'] = "tblWine.created DESC";
    $varSearchParam['producer_id'] = $producer_id;
    $rst = $search_obj -> search($varSearchParam); //search

    //print_r($rst);

    if($rst){ //step through wine results and render

        foreach($rst as $key => $row){
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

                echo "<div class=\"wine_accordian\" id=\"wine_accordian_$wine_id\"  >";
                    echo "<table class=\"wine\" id=\"wine_table\">";
                        echo "<tr>";
                            //expand indicator
                            echo "<td valign=middle >";
                                if($vintage_count>0){
                                   echo "<div class=\"wine_panel_toggle click arrow_right\" id=\"arrow_indicator_$wine_id\" style=\"width:30px; height:30px; margin-left:5px; background-color:\" ></div>";
                                }else{
                                    echo "<div style=\"width:30px; height:30px; margin-left:5px;\" ></div>";
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
                            echo "<td width=60px align=center class=\"wine_panel_toggle click\" >";
                                echo "<img src=\"$flag\" width=\"28px\" height=\"28px\" />";
                            echo "</td>";

                            //wine name
                            echo "<td width=800px align=left  class=\"wine_panel_toggle click\" >";
                                //echo "<p style=\"color:#363A36; line-height:1.2em; font-size:1em; padding-top:5px; padding-bottom:3px;\">$full_name</p>";
                                echo "<h3 style=\"margin-top:3px;\" >$full_name</h3>";
                                echo "<p style=\"color:darkgray; font-size:0.8em; padding-bottom:5px;\">$location</p>";
                            echo "</td>";

                            echo "<td valign=middle >";
                                if (is_authed()){
                                //    echo "<div class=\"wine_menu click\" id=\"wine_menu_$wine_id\" style=\"float:right; padding-left:10px; padding-right:15px;\">";
                                //        echo "<img value=\"$wine_id\"  src=\"/images/show_grey_flat_32.png\" width=\"24px\" height=\"24px\" >";
                                //    echo "</div>";
                                    echo "<img class=\"btn_add_vintage click\" data-wine_id=\"$wine_id\" value=\"$wine_id\" id=\"add_vintage_$wine_id\" src=\"/images/plus_grey_flat_32.png\" width=\"20px\" height=\"20px\" style=\"margin:10px;\" >";
                                }
                            echo "</td>";

                        echo "</tr>";
                    echo "</table>";
                echo "</div>"; //wine_accordian


                //*** Vintage Details ***
                $rst_vintages = $search_obj ->get_vintages($wine_id);

                echo "<div class=\"vintages_panel hidden\" id=\"vintages_panel_$wine_id\" >";

                    if($rst_vintages){
                        foreach ($rst_vintages as $key => $rowVintage ){
                            //print_r($rowVintage);
                            $vintage_id = $rowVintage['vintage_id'];
                            $year = $rowVintage['year'] == 0 ? "n/a" : $rowVintage['year'];

                            //ratings
                            $vintage_quality = $rowVintage['vintage_quality'];
                            $quality_width = ($vintage_quality*10)."px";
                            $vintage_value = $rowVintage['vintage_value'];
                            $value_width = ($vintage_value*20)."px";


                            //vintage header bar
                            echo "<div class=\"vintage_accordian vintage_panel_toggle\" data-vintage_id=\"$vintage_id\" id=\"vintage_accordian_$vintage_id\" style=\"display:flex; flex-direction:row; align-items:center; width:100%; height:42px; background-color:#F8F8F8;\" >"; //container for vintage header row
                                echo "<div class=\"vintage_expanded_indicator click arrow_right\" data-vintage_id=\"$vintage_id\" id=\"arrow_indicator_vintage_$vintage_id\" style=\"width:16px; height:16px; margin-left:10px; background-color:;\" >";
                                echo "</div>";
                                echo "<div data-vintage_id=\"$vintage_id\" style=\" margin-left:10px; background-color:;\" >";
                                    echo "<p style=\"font-size:1.4em;\" >$year</p>";
                                echo "</div>";
                                echo "<div class=\"quality-static-rating-medium\" style=\"width:$quality_width; margin-left:10px; background-color:;\" ></div>";
                                echo "<div class=\"value-static-rating-medium\" style=\"width:$value_width; margin-left:10px; \"></div>";
                                //vintage buttons
                                echo "<div class=\"vintage_buttons __hide_small_screen\" style=\"margin-left:auto; background-color:;\" >";
                                    if (is_authed()){    
                                        //echo "<img class=\"btn_edit_vintage click \" data-vintage_id=\"$vintage_id\" value=\"$vintage_id\" id=\"edit_$vintage_id\" name=\"btn_edit_vintage\" src=\"/images/edit_flat_grey_24.png\" width=\"18\" height=\"18\" style=\"margin:10px;\" >";
                                        echo "<img class=\"btn_add_to_basket click \" data-vintage_id=\"$vintage_id\" name=\"btn_add_to_basket\" src=\"/images/basket_flat_grey_24.png\" width=\"20px\" height=\"20px\" style=\"margin:10px;\" >";
                                        //echo "<img class=\"btn_add_tasting_note click\" data-vintage_id=\"$vintage_id\" value=\"$vintage_id\" id=\"add_note_$vintage_id\" src=\"/images/add_tasting_flat_grey.png\" width=\"20px\" height=\"20px\" style=\"margin:10px;\" >";
                                    }                        
                                echo "</div>";
                                //only show 'more' button on small screen
                                //echo "<div class=\"vintage_buttons hide_medium_screen \" style=\"margin-left:auto; background-color:;\" >";
                                //    if (is_authed()){
                                //        echo "<div class=\"vintage_menu click\" id=\"vintage_menu_$vintage_id\" style=\"float:right; padding-left:10px; padding-right:15px;\">";
                                //            echo "<img value=\"$vintage_id\"  src=\"/images/show_grey_flat_32.png\" width=\"24px\" height=\"24px\" >";
                                 //       echo "</div>";
                                 //   }
                                //echo "</div>";
                            echo "</div>";

                            //vintage details container
                            echo "<div class=\"vintage_details hidden\" id=\"vintage_details_$vintage_id\"  >";

                                echo "<div class=\"left-column\" >";

                                    //Label Image
                                    $file_name = $rowVintage['image1'];
                                    $new_root = rtrim($root, '/\\');
                                    $class = (empty($file_name) ? "hide_small_screen" : null); //if no image file_name show nothing on smaller screens


                                    echo "<div class=\"vintage-image $class\" style=\"position:relative;\" >";

                                        if($file_name > ""){
                                            $image_path = $label_path.$file_name;
                                            if(file_exists($new_root.$image_path)){
                                                echo "<img style=\"display:block; margin-left:auto; margin-right:auto;\" ".fnImageResize($image_path,$new_root,150)." />";
                                            } else {
                                                echo "<br/>file not found";
                                            }
                                        }else{
                                            //no image file provided show default
                                            //echo "wine_type = $wine_type";
                                            switch($wine_type){
                                                case 1: //red
                                                    $image_path = "/images/winebottle_red.png";
                                                    break;
                                                case 2: //white
                                                    $image_path = "/images/winebottle_white.png";
                                                    break;
                                                case 3: //rose
                                                    $image_path = "/images/winebottle_rose.png";
                                                    break;
                                                case 4: //sparkling white
                                                    $image_path = "/images/winebottle_sparkling_white.png";
                                                    break;
                                                case 5: //dessert
                                                    $image_path = "/images/winebottle_white.png";
                                                    break;
                                                case 8: //sparkling red
                                                    $image_path = "/images/winebottle_red.png";
                                                    break;
                                                default:
                                                    $image_path = "/images/winebottle_red.png";
                                            }
                                            $img = fnImageResize($image_path, $new_root, 150);
                                            echo "<img style=\" display:block; margin-left:auto; margin-right:auto;\" $img />";
                                        }

                                        echo "<div class=\"$class\" style=\"position:absolute; bottom:0px; left:0px; z-index:100;\" >";
//                                            if(is_authed()){
//                                                echo "<input type=\"image\" class=\"btn_edit_image\" value=\"$vintage_id\" id=\"edit_image_$vintage_id\" name=\"btn_edit_image\" src=\"/images/plus_circle_grey_flat_32.png\" width=\"18px\" height=\"18px\" >";
//                                            }
                                        echo "</div>";

                                    echo "</div>";

                                    echo "<div class=\"clear\"></div>";

                                echo "</div>"; //left-column

                                //echo "<div class=\"centre_column\" style=\"padding-left:20px; border-left:dashed 1px lightgray;\" >";
                                echo "<div class=\"rwd-con-half\" >"; 

                                     //Wine Type
                                    echo "<div class=\"vintage-section \" >";
                                        echo "<h3>Type</h3>";
                                        echo "<p class=\"text_2\" >".$rowVintage['winetype']."</p>";
                                    echo "</div>";

                                    //Grapes
                                    $grapes_obj = new vintage_has_grape;
                                    $where = " vintage_id = ".$vintage_id;
                                    $var_grapes = $grapes_obj -> get_extended($where);

                                    if(!empty($var_grapes)){
                                        echo "<div class=\"vintage-section \" >";
                                            echo "<h3>Grapes</h3>";
                                            foreach ($var_grapes as $var_grape){
                                                echo "<p class=\"text_2\" style=\"padding-bottom:2px;\" >";
                                                    echo $var_grape['grape'];
                                                    if($var_grape['percent']>0){
                                                        echo " (".$var_grape['percent']."%)";
                                                    }
                                                echo "</p>";
                                            }
                                        echo "</div>";
                                    }


                                    //Alcohol
                                    if($rowVintage['alcohol']>0){
                                        echo "<div class=\"vintage-section \" >";
                                            echo "<h3>Alcohol</h3>";
                                            //$str_alcohol = $rowVintage['alcohol'] > 0 ?  $rowVintage['alcohol']."%" : " - ";
                                            echo "<p class=\"text_2\">".$rowVintage['alcohol']."%</p>";
                                        echo "</div>";
                                    }

                                    //Drinking Guide  
                                    if(!empty($rowVintage['drink_year_to'])){
                                       echo "<div class=\"vintage-section \" >";
                                            echo "<h3>Drinking Guide</h3>";
                                            $drink_text = null;
                                            if(!empty($rowVintage['drink_year_from'])){
                                                $drink_text = $rowVintage['drink_year_from']." - ";
                                            }
                                            $drink_text .= $rowVintage['drink_year_to'];
                                            echo "<p class=\"text_2\" >$drink_text</p>";
                                        echo "</div>";
                                    }

                                    //Awards
                                    $awards_obj = new vintage_has_award();
                                    $where = "vintage_id = $vintage_id";
                                    $var_awards = $awards_obj -> get_extended($where);

                                    if(!empty($var_awards)){
                                        echo "<div class=\"vintage-section \" >";
                                            echo "<h3>Awards:</h3>";
                                            foreach ($var_awards as $key => $var_award){
                                                echo "<p class=\"text_2\" style=\"padding-bottom:2px;\" >".$var_award['award_org']." - ".$var_award['award']."</p>";
                                            }
                                        echo "</div>";
                                    }

                                echo "</div>"; //rwd-con-half    

                                //echo "<div class=\"right_column\" style=\"text-align:left; \" >";
                                echo "<div class=\"rwd-con-half\" >";  

                                //Comments
                                if(!empty( $rowVintage['vintage_notes'] )){
                                    echo "<div class=\"vintage-section \" >";
                                        echo "<h3>Comments</h3>";
                                        //$str_comments = !empty( $rowVintage['vintage_notes'] ) ? $rowVintage['vintage_notes'] : " - ";
                                        echo "<p class=\"text_2\">".$rowVintage['vintage_notes']."</p>";
                                    echo "</div>";
                                }

                                //Tasting Notes
                                $obj = new tasting_note();
                                $where = "vintage_id = $vintage_id";
                                $sort = "note_date DESC";
                                $var_notes = $obj -> get($where, $columns=false, $group=false, $sort, $limit=false);

                                if($var_notes){
                                    echo "<div class=\"vintage-section \" >";
                                        echo "<h3>Tasting Notes</h3>";
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

                                            echo "<div class=\"note_link ignore_dirty\" id=\"$note_id\" style=\"float:left; margin-bottom:5px; \">";
                                                echo "<div class=\"vertical-centre\" style=\"height:18px;\" >";
                                                    echo "<div style=\"width:100px; float:left; white-space:nowrap; margin-right:15px; margin-top:3px;\"><p class=\"text_2\" style=\"float:left\" >$note_date</p></div>";
                                                    echo "<div class=\"quality-static-rating-small\" style=\"float:left; margin-right:15px; margin-top:3px; width:$quality_width; \" ></div>";
                                                    echo "<div class=\"value-static-rating-small\" style=\"float:left; width:$value_width; margin-top:3px;\" ></div>";
                                                echo "</div>";
                                            echo "</div>";
                                            echo "<div class=\"clear\" ></div>";
                                        }
                                        echo "</div>";
                                    }

                                    //Acquisitions
                                    echo "<div id=\"con_acquisitions_vintage\" class=\"vintage-section \" >";
                                        echo "<h3>Acquisitions</h3>";
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

                                                echo "<div class=\"acquire_link ignore_dirty\" id=\"$acquire_id\" data-vintage_id=\"$vintage_id\" style=\"float:left; width:100%; padding-bottom:5px; margin-bottom:5px; border-bottom:1px dashed lightgray;\" >";
                                                    echo "<div style=\"float:left; width:100px; text-align:left; \" >";
                                                        echo "<p>$acquire_date</p>";
                                                    echo "</div>";
                                                    echo "<div style=\"float:left; margin-left:15px;\" >";
                                                        echo "<p>$acquire_merchant</p>";
                                                    echo "</div>";

                                                    echo "<div class=\"clear\" ></div>";

                                                    echo "<div style=\"margin-top:7px; \">"; //second row
                                                        echo "<div style=\"float:left; width:auto; margin-right:15px;\" >";
                                                            echo "<p style=\"font-size:0.85em; display:inline; \" >Qty:</p>";
                                                            echo "<p class=\" \" style=\"font-size:0.85em; display:inline; \" > $acquire_qty</p>";
                                                        echo "</div>";

                                                        echo "<div style=\"float:left; text-align:right; width:auto; margin-right:15px;\" >";
                                                            echo "<p class=\" \" style=\"font-size:0.85em; display:inline;\" >Price:</p>";
                                                            echo "<p style=\"font-size:0.85em; display:inline;\" > £ $unit_price</p>";
                                                        echo "</div>";   

                                                        echo "<div style=\"float:left; width:auto; \" >";
                                                            echo "<p style=\"font-size:0.85em; display:inline;\" >Paid:</p>";
                                                            echo "<p class=\" \" style=\"font-size:0.85em; display:inline;\" > £ $discounted_price</p>";
                                                        echo "</div>";



                                                    echo "</div>"; //second_row

                                                    echo "<div class=\"clear\" ></div>";

                                                echo "</div>"; //acquire_link
                                            } //foreach acquisition

                                            echo "<div class=\"clear\" ></div>";

                                            //Available details
                                            echo "<div id=\"con_available_$vintage_id\" >";
                                                echo "<div class=\"vertical-centre input-main-label\" style=\"margin-bottom:0px;\" >";
                                                    echo "<h3 style=\"display:inline-block; float:left;\" >Available Bottles</h3>";
//                                                    if(is_authed()){
//                                                        echo "<input type=\"image\" class=\"btn_edit_override\" style=\"float:left; margin-left:10px; \" value=\"$vintage_id\" id=\"override_$vintage_id\" name=\"btn_edit_override\" src=\"/images/edit_flat_grey_24.png\" width=\"16px\" height=\"16px\" >";
//                                                    }
                                                echo "</div>";
                                                //echo "<div class=\"clear\"></div>";
                                                $obj_vintage = new vintage($vintage_id);
                                                $acquisition_bottle_count = $obj_vintage ->get_acquisition_bottle_count();
                                                if($acquisition_bottle_count){
                                                    $available_bottle_count = $obj_vintage ->get_available_bottle_count();
                                                    echo "<div style=\"float:left; clear:left; \" >";
                                                        echo "<p> Available: ".$available_bottle_count['available_bottles']."&nbsp; &nbsp; Acquired: ". $acquisition_bottle_count."</p>";
                                                    echo "</div>";
                                                }
                                                echo "<div class=\"clear\"></div>";
                                            echo "</div>";


                                        } else {
                                            echo "<p> - </p>"; //no acquisitions
                                        }

                                        echo "<div class=\"clear\" ></div>";

                                    echo "</div>"; //con_acquisitions_vintage

                                echo "</div>"; //rwd-con-half


                                echo "<div class=\"clear\"></div>"; //clear all three columns

                            //echo "</div>"; //vintage_details

                        echo "</div>"; //vintage_accordian

                        }

                } //closure - if rst_vintages

            echo "</div>"; //vintages_panel

            echo "<div class=\"clear\" ></div>";






        } //foreach wine

    }


    NoProducerSelected: //jump to label to execute if no producer selected
    //end script
    ?>

</div> <!--con_wines-->