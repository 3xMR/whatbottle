<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");

echo "<div style=\"float:left; background-color:none; \" class=\"con_form_fields\" >";

//build location label
$location = $_SESSION['var_wine_temp']['country'].", ".$_SESSION['var_wine_temp']['region'].", ".$_SESSION['var_wine_temp']['subregion'];
$location = rtrim($location,', ');

$session_producer_id = $_SESSION['var_wine_temp']['producer_id'];
$session_producer = $_SESSION['var_wine_temp']['producer'];

?>
 

    <form action="\wine\wine.php" method="post" id="frm_wine" name="frm_wine" autocomplete="off">


        <div class="input-main-label" >
            <p>Producer</p>
        </div>

        <div class="input-main" style="position:relative;" >
            <select id="select_producer">
                <?php
                    $obj = new producer();
                    $sort = "producer ASC";
                    $var_results = $obj ->get(false,false,false,$sort);
                    foreach($var_results as $var_result){ //create dropdown select entries
                        $item = $var_result['producer'];
                        $key = $var_result['producer_id'];
                        if($session_producer_id == $key){
                            echo "<option selected=\"selected\" value=\"$key\">$item"; //add item and mark as selected
                        }else{
                           
                            echo "<option value=\"$key\">$item"; //add item to list
                        }
                    }
                ?>
            </select>
            <input type="text" name="producer"  style="width:270px; position:absolute; top:0; left:0; border-top-right-radius:0px; border-bottom-right-radius:0px;" value="<?php echo $session_producer;?>" id="producer" />
            <img style="margin-top:3px;" class="click control" id="btn_add_producer" alt="add producer" src="/images/plus_grey_flat_32.png"  />
            <input type="hidden" class="debug" name="producer_id" value="<?php echo $session_producer_id;?>" id="producer_id" />
        </div>
        
        
        <div id="con_wines_from_producer" style="display:none;">
            <!--html loaded from rpc_wine_from_producer_html.php displays wines found for selected Producer-->
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
           
                echo "<select name=\"winetype_id\" id=\"winetype_id\"  >";
                    $session_winetype_id = $_SESSION['var_wine_temp']['winetype_id'];
                    $obj = new winetype();
                    $sort = "winetype ASC";
                    $var_results = $obj -> get();
                    echo "<optgroup class=\"styled-option\" >";
                    foreach($var_results as $var_result){
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
     
            ?>   
        </div>
        
        <div class="input-main-label" >
            <p>Wine Region</p>
        </div>
        
        <div class="input-main vertical-centre">
            <input type="text" name="location" id="location" value="<?php echo $location; ?>" autocomplete="new-location" onkeydown="return false;" >
            <img style="margin-left:-32px; margin-bottom:3px; height:17px; width:17px;" class="click control" id="btn_clear_location" alt="clear location" src="/images/delete_grey_flat_32.png"  />
        </div>

        <div class="input-main" style="position:relative;"  >
            <div id="---con_listBox_location" ></div> <!--div to contain region listBox-->
        </div>



        <?php

echo "</form>";

echo "</div>"; //con_form_fields