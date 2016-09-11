<?php
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

 

 
 
        
            //get list of level_1 items
            $obj_1 = new country();
            $sort_1 = "country ASC";
            $rst_1 = $obj_1 ->get($where=false, $columns=false, $group=false, $sort_1);


            foreach($rst_1 as $row){

                $index_1 = $row['country_id'];
                $value_1 = $row['country'];
                $flag_file = $row['flag_image'];
                $image = "/images/flags/".$flag_file;

                //get list of level_2 items
                $obj_2 = new region();
                $where = "country_id = $index_1";
                $order = " region ASC ";
                $rst_2 = $obj_2 -> get($where,$columns,$group,$order);
                
                //Level 1 item
                echo "<div class=\"listBox_row country click\" id=\"$index_1\" >";
                    //hidden input allows jquery to recover value
                    echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value_1\" >";
                    echo "<input type=\"hidden\" class=\"listBox_value\" value=\"country\" >";
                    echo "<input type=\"hidden\" class=\"listBox_value\" id=\"flag_image\" value=\"$flag_file\" >";
                    
                    echo "<table class=\"listbox_table\">";
                        echo "<tr>";
                            echo "<td style=\"width:24px; text-align:left; vertical-align:middle;\" >";
                                if($rst_2){
                                    echo "<div class=\"listBox_expand listBox_status\" id=\"arrow_parent_$index_1\"></div>";
                                }else{
                                    echo "<div style=\"width:32px;\" ></div>";
                                }
                            echo "</td>";

                            echo "<td style=\"width:32px; text-align:center; vertical-align:middle;  padding-top:1px;\"  >";
                                echo "<img src=\"$image\" width=\"24px\" height=\"24px\" />";
                            echo "</td>";

                            echo "<td style=\"width=auto; vertical-align:middle;\" >";
                                echo "<p class=\"listBox_row_text\">$value_1</p>";
                            echo "</td>";

                        echo "</tr>";
                    echo "</table>";
                
                echo "</div>"; //level_1



                if($rst_2){
                    //has children
                    echo "<div style=\"\" class=\"child_con country\" id=\"$index_1\" >";
                        echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$index_1\" >";

                        foreach ($rst_2 as $row ){
                            $index_2 = $row['region_id'];
                            $value_2 = $row['region'];

                            //get list of level_3 items
                            $obj_3 = new subregion();
                            $where = "region_id = $index_2";
                            $order = " subregion ASC ";
                            $rst_3 = $obj_3 -> get($where,false,false,$order);

                            //display level_2

                            echo "<div class=\"listBox_row region click\" id=\"$index_2\" >";
                                //hidden input allows jquery to recover value
                                echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value_2\" >";
                                echo "<input type=\"hidden\" class=\"listBox_value\" value=\"region\" >";

                                echo "<table class=\"listbox_table\" id=\"child_table\">";
                                    echo "<tr>";
                                        echo "<td  style=\"width:40px; vertical-align:middle;\" >";
           
                                        echo "</td>";

                                        echo "<td style=\"width:30px; text-align:right; vertical-align:middle;\" >";
                                            if($rst_3){
                                                echo "<div class=\"listBox_expand listBox_status\" id=\"arrow_parent_$index_2\"></div>";
                                            }else{
                                                echo "<div style=\"width:30x;\" >&nbsp;</div>";
                                            }
                                        echo "</td>";

                                        echo "<td style=\"width:auto; vertical-align:middle;\"  >";
                                            echo "<p class=\"listBox_row_text\">$value_2</p>";
                                        echo "</td>";

                                    echo "</tr>";
                                echo "</table>";

                            echo "</div>"; //row
                    
                        
                            if($rst_3){
                                echo "<div style=\"\" class=\"child_con region\" id=\"$index_2\" >";
                                    echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$index_2\" >";
                                    echo "<input type=\"hidden\" class=\"listBox_value\" value=\"region\" >";


                                    foreach ($rst_3 as $row ){
                                        $index_3 = $row['subregion_id'];
                                        $value_3 = $row['subregion'];

                                        //display level_3

                                        echo "<div class=\"listBox_row subRegion click\" id=\"$index_3\" >";
                                            //hidden input allows jquery to recover value
                                            echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$value_3\" >";
                                            echo "<input type=\"hidden\" class=\"listBox_value\" value=\"subRegion\" >";

                                            echo "<table class=\"listbox_table\" id=\"baby_table\">";
                                                echo "<tr>";
                                                    echo "<td  style=\"width:90px; vertical-align:middle;\" >";
                                                        echo "&nbsp;";//spacer cell
                                                    echo "</td>";
                                                    echo "<td style=\"width:auto; border: solid 0px black; vertical-align:middle;\" >";
                                                        echo "<p class=\"listBox_row_text\">$value_3</p>";
                                                    echo "</td>";

                                                echo "</tr>";
                                            echo "</table>";

                                        echo "</div>"; //level_3

                                    }//foreach

                                echo "</div>"; //con_region
                            }    
       
                } //foreach rst_2
                    
                echo "</div>"; //con_level_1 - contains regions

            } //end_if rst_2
                
                
        } //foreach parent

    echo "</div>"; //level_1_con

  


?>
