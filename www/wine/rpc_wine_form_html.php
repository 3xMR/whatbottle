<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
//require_once("$root/classes/class.db.php");


echo "<div style=\"float:left; background-color:none; \" class=\"con_form_fields\" >";

?>

    <form action="\wine\wine.php" method="post" id="frm_wine" name="frm_wine" autocomplete="off">

        <div class="hidden">
            <input type="hidden" value="<?php echo $_SESSION['var_wine_temp']['wine_id']; ?>" name="wine_id" id="wine_id"><br/>
            <input type="hidden" value="<?php echo $_SESSION['var_wine_temp']['is_dirty'];?>" name="is_dirty" id="is_dirty"><br/>
            <input type="hidden" value="<?php echo $_SESSION['var_wine_temp']['status'];?>" name="status" id="status"><br/>
        </div>    

        <div class="input-main-label" >
            <p>Wine Name</p>
        </div>
        
        <div class="input-main">
            <input type="text" value="<?php echo $_SESSION['var_wine_temp']['wine'];?>" name="wine" id="wine_name" autocomplete="off" >
        </div>
        
        
        <div class="input-main-label" >
            <p>Type</p>
        </div>

        <div class="input-main">
            <?php
               //echo "<div class=\"input_select\">";
                   echo "<select name=\"winetype_id\" id=\"winetype_id\"  >";
                       $session_winetype_id = $_SESSION['var_wine_temp']['winetype_id'];
                       $obj = new winetype();
                       $sort = "winetype ASC";
                       $var_results = $obj -> get();
                       echo "<optgroup class=\"styled-option\" >";
                       foreach($var_results as $var_result){
                           //return list of award orgs
                           $item = $var_result['winetype'];
                           $key = $var_result['winetype_id'];
                           if($session_winetype_id == $key){
                               //add item and mark as selected
                               echo "<option selected=\"selected\" value=\"$key\">$item";
                           }else{
                               //add item to list
                               echo "<option value=\"$key\">$item";
                           }
                       }
                       echo "</optgroup>";
                   echo "</select>";
               //echo "</div>";
            ?>   
        </div>

        
        <div class="input-main-label" >
            <p>Producer</p>
        </div>

        <div class="input-main" style="position:relative;" >
            <select id="select_producer">
                <?php
                    $session_producer_id = $_SESSION['var_wine_temp']['producer_id'];
                    $obj = new producer();
                    $sort = "producer ASC";
                    $var_results = $obj ->get(false,false,false,$sort);
                    foreach($var_results as $var_result){
                        //return list
                        $item = $var_result['producer'];
                        $key = $var_result['producer_id'];
                        if($session_producer_id == $key){
                            //add item and mark as selected
                            echo "<option selected=\"selected\" value=\"$key\">$item";
                        }else{
                            //add item to list
                            echo "<option value=\"$key\">$item";
                        }
                    }
                ?>
            </select>
            <input type="text" name="producer"  style="width:270px; position:absolute; top:0; left:0; border-top-right-radius:0px; border-bottom-right-radius:0px;" value="<?php echo $_SESSION['var_wine_temp']['producer'];?>" id="producer" />
            <img style="margin-top:3px;" class="click control" id="btn_add_producer" alt="show all producers" src="/images/plus_grey_flat_32.png"  />
            <input type="hidden" class="debug" name="producer_id" value="<?php echo $_SESSION['var_wine_temp']['producer_id'];?>" id="producer_id" />
        </div>

        
        <div class="input-main-label" >
            <p>Country</p>
        </div>
        
        <div class="input-main" style="position:relative;"  >
            <select id="select_country" name="country_select" >
                <?php
                    $session_country_id = $_SESSION['var_wine_temp']['country_id'];
                    $obj = new country();
                    $sort = "country ASC";
                    $var_results = $obj ->get(false,false,false,$sort);
                    //add initial option
                    echo "<option value=\"-1\">Select...";
                    foreach($var_results as $var_result){
                        //return list
                        $item = $var_result['country'];
                        $key = $var_result['country_id'];
                        if($session_country_id == $key){
                            //add item and mark as selected
                            echo "<option selected=\"selected\" value=\"$key\">$item";
                        }else{
                            //add item to list
                            echo "<option value=\"$key\">$item";
                        }
                    }
                ?>
            </select>
            <input type="text" name="country"  style="width:270px; position:absolute; top:0; left:0; border-top-right-radius:0px; border-bottom-right-radius:0px;" value="<?php echo $_SESSION['var_wine_temp']['country'];?>" id="country" />
            <input type="hidden" name="country_id" class="debug" value="<?php echo $_SESSION['var_wine_temp']['country_id'];?>" id="country_id" />
            <img class="click control" alt="add_country" style="margin-top:3px;" id="btn_add_country" src="/images/plus_grey_flat_32.png" >
        </div>


        <div class="input-main-label" >
            <p>Region</p>
        </div>

        <div class="input-main" style="position:relative;" >
            <select id="select_region">
                <?php
                $session_region_id = $_SESSION['var_wine_temp']['region_id'];
                $obj = new region();
                $sort = "region ASC";
                $var_results = $obj ->get(false,false,false,$sort);
                //add initial option
                echo "<option value=\"-1\">Select...";
                foreach($var_results as $var_result){
                    //return list
                    $item = $var_result['region'];
                    $key = $var_result['region_id'];
                    if($session_region_id == $key){
                        //add item and mark as selected
                        echo "<option selected=\"selected\" value=\"$key\">$item";
                    }else{
                        //add item to list
                        echo "<option value=\"$key\">$item";
                    }
                }
                ?>
            </select>
            <input type="text" style="width:270px; position:absolute; top:0; left:0; border-top-right-radius:0px; border-bottom-right-radius:0px;" name="region" value="<?php echo $_SESSION['var_wine_temp']['region'];?>" id="region" />
            <input type="hidden" value="<?php echo $_SESSION['var_wine_temp']['region_id'];?>" name="region_id" id="region_id" />
            <img class="click control" alt="add_region" style="margin-top:3px;" id="btn_add_region" src="/images/plus_grey_flat_32.png" >
        </div>
            

        <div class="input-main-label" >
            <p>Subregion</p>
        </div>

        <div class="input-main" style="position:relative;" >
            <select id="select_subregion" name="select_subregion">
                <?php
                    $session_subregion_id = $_SESSION['var_wine_temp']['subregion_id'];
                    $obj = new subregion();
                    $sort = "subregion ASC";
                    $var_results = $obj -> get(false,false,false,$sort);
                    //add initial option
                    echo "<option value=\"-1\">Select...";
                    foreach($var_results as $var_result){
                        //return list
                        $item = $var_result['subregion'];
                        $key = $var_result['subregion_id'];
                        if($session_subregion_id == $key){
                            //add item and mark as selected
                            echo "<option selected=\"selected\" value=\"$key\">$item";
                        }else{
                            //add item to list
                            echo "<option value=\"$key\">$item";
                        }
                    }
                ?>
            </select>
            <input type="text" style="width:270px; position:absolute; top:0; left:0; border-top-right-radius:0px; border-bottom-right-radius:0px;" name="subregion"  value="<?php echo $_SESSION['var_wine_temp']['subregion'];?>" id="subregion" />
            <input type="hidden" value="<?php echo $_SESSION['var_wine_temp']['subregion_id'];?>" name="subregion_id" id="subregion_id" />
            <img class="click control" style="margin-top:3px;" alt="add_subregion" id="btn_add_subregion" src="/images/plus_grey_flat_32.png"  >
        </div>
  
      

        <?php

echo "</form>";

echo "</div>"; //con_form_fields






?>
