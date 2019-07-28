<?php
/* 
 * 
 * Branch R5_PDO_Update
 * Release 4.4
 * Released: 28-07-2019
 * Notes:
 * Added show children in filtered location listBox
 * Fixed widths of admin listboxes and removed border of tabs
 * Added PDO database connection used in User class (not transitioned other classes yet)
 * Add User class with all user related methods for login, password change
 * PDO public database credentials moved to config folder above public_html
 * Added Settings page with password change function
 * Added Settings to main menu
 * Fixed basket count notification so it is self initialising
 * Increased image max upload size to 10MB and moved rpc_image_uploader.php to /vintage
 * Added home button link to whatbottle logo
 * Created template.php page
 * Fixed Chrome interpreting location fields as addresses
 * Fixed From/To on Vintage page being populated with 0 when returning from child page
 * Fixed listBoxes on Admin page; filters have accent-folding, fixed reset, fixed widths
 * 
 * 
 * Release 4.32.2
 * Released 23.02.2019
 * Fixed login fields not being in a form and autocomplete settings for username and password fields
 * 
 * Release 4.32.1
 * Released: 04.03.2018
 * Notes:
 * Unsaved changes dialog appears on close without changes, caused by autofill on login form
 * Label images not appearing on index page when they have been added and are visible on vintage page
 * Add/Edit Image button showing on Index.php even when not authed
 * Minor formating changes post major release
 * Fixed search bug, now resets all search filters when entering new text in autocomplete
 * Fixed available override dialog box
 * Reworked Image Manager so it now supports editing images directly from home page
 * and so that it works on mobile
 * 
 * 
 * Release 4.3
 * Released: 25.02.2018
 * Notes:
 * Major responsive design overhaul
 * Index, wine, vintage, acquisition and all dialogs now responsive design enabled
 * Quick notes now include general notes text box and vintage price show within dialog
 * Add vintage button removed from new wine page
 * Wine and Vintage pages simplified to just focus on add functions
 * All text and many sizes changed to 'em' to make more responsive
 * Pagination rows is now set by screen size
 * 
 * 
 * Release 4.2
 * Released: 31.08.2017
 * Notes:
 * Added first stage of responsive design formatting

 * 
 * Release 4.1
 * Released: 26.08.2017
 * Notes:
 * Fixed validation on To/From fields on Vintage
 * To/From fields set to select text on iPad to ease modification
 * Re-arranged field layout on Vintage to look better
 * Change colour of login button on login form and advanced search buttons so they are all blue
 * Reset available value to zero for all vintages in db
 * Search box reset buttons show only when results are filtered
 * Fixed vintage details not expanding when only one wine is filtered
 * Fixed panels remaining open after search reset
 * Fixed multiple autocomplete based searches are aggregated rather than reset each time
 * 
 * 
 * Release: 4.0
 * Released: 19.08.2017
 * Notes: 
 *      Addition of 'available' concept to allowing tracking stock
 *      Addition of 'Drinking To & From' dates to tblVintage and Vintage form
 *      Changes to class_wine_search.php to support searching for Available and Drinking concepts
 *      Modified wine search to remove text from search field
 *      Validations changed for Wine and Vintage forms
 * 
 * Backlog:
 * TODO: Put wine and vintage count on reporting page
 * TODO: free text search on notes
 * TODO: Double-click on image to open image_manager page as overlay
 * TODO: centre align vertically vintage year with stars and pound signs
 * 
 * php.ini = /Applications/MAMP/bin/php/php5.6.2/conf/php.ini
 *
 */
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

echo "<!DOCTYPE html>";
echo "<html>";
    echo "<head>";
    
    require_once("$root/includes/standard_html_head.inc.php");
    echo "<title>What Bottle?</title>";
    require_once("$root/includes/css.inc.php");//include style sheets 
    
?>

        <style>
            
            /*** Autocomplete ***/
            
            .ui-autocomplete-category {
              font-weight: bold;
              padding: .2em .4em;
              margin: .8em 0 .2em;
              line-height: 1.5;
            }

            .ui-autocomplete-loading {
                background: white url("/images/ui-anim_basic_16x16.gif") right center no-repeat;
            }

            .ui-autocomplete { 
                position: absolute; 
                cursor: default;
                z-index:30 !important;
                max-height: 100%;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
                font-size: 16px;
                margin: .8em 0 .2em;
                line-height: 1.5;
            }

            
            .con_pagination *{
                margin-left:10px;
                margin-right:10px;
                float:right;
            }
            

            
            
            /*** Main Form Button Bar ***/
            
            .con_button_bar{
                float:left;
                clear:left;
                margin-top:15px;
                padding-top:15px;
                border-top: solid 1px darkgray;
                /*background-color:orange;*/
                width: 100%;
            }


            

        </style>

    </head>

<body>

    <div id="dialog-rate" class="hidden" title="Rate Vintage">
        <p>
            <?php
               //quality rating
                echo "<h1 style=\"margin-bottom:15px;\" >Tasting Note</h1>";
                echo "<div style=\" width:200px; padding-left:5px; float:left; margin-right:20px; margin-bottom:10px; \" >";
                    echo "<h3 style=\"margin-bottom:10px;\" class=\"block\" >Quality</h3>";
                    echo "<div class=\"rating\" style=\"width:192px; height:32px; display:block;\" >";

                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"1\" class=\"auto-submit-star {split:2}\" title=\"Undrinkable\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"2\" class=\"auto-submit-star {split:2}\" title=\"Terrible\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"3\" class=\"auto-submit-star {split:2}\" title=\"Very Poor\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"4\" class=\"auto-submit-star {split:2}\" title=\"Poor\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"5\" class=\"auto-submit-star {split:2}\" title=\"OK\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"6\" class=\"auto-submit-star {split:2}\" title=\"Reasonable\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"7\" class=\"auto-submit-star {split:2}\" title=\"Good\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"8\" class=\"auto-submit-star {split:2}\" title=\"Very Good\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"9\" class=\"auto-submit-star {split:2}\" title=\"Excellent\" />";
                        echo "<input name=\"note_quality\"  type=\"radio\" value=\"10\" class=\"auto-submit-star {split:2}\" title=\"Outstanding\" />";
                    echo "</div>";
                echo "</div>";

                //value rating
                echo "<div style=\" width:200px; padding-left:5px; float:left; margin-bottom:10px;\" >";
                    echo "<div style=\"background-color:yellow; width:100%;\" >";
                        echo "<h3 style=\"display:inline-block; float:left; margin-bottom:10px;\" style=\"background-color:pink;\" >Value</h3>";
                        echo "<div class=\"block\" style=\"margin-left:10px; padding-top:0.125em; float:left; font-size:0.8em; color:lightgray;\" ><p id=\"quick_price\">hello</p></div>";
                    echo "</div>";
                    echo "<div class=\"rating \" style=\"width:192px; height:32x; display:block; clear:left;\" >";
                        echo "<input name=\"note_value\" type=\"radio\" value=\"1\" class=\"auto-submit-pound \" title=\"Poor\" />";
                        echo "<input name=\"note_value\" type=\"radio\" value=\"2\" class=\"auto-submit-pound \" title=\"OK\" />";
                        echo "<input name=\"note_value\" type=\"radio\" value=\"3\" class=\"auto-submit-pound \" title=\"Good\" />";
                        echo "<input name=\"note_value\" type=\"radio\" value=\"4\" class=\"auto-submit-pound \" title=\"Very Good\" />";
                        echo "<input name=\"note_value\" type=\"radio\" value=\"5\" class=\"auto-submit-pound \" title=\"Excellent\" />";
                    echo "</div>";
                echo "</div>"; //value rating
                
                //General Note
                echo "<div style=\"float:left; clear-left; width:100%; padding-left:5px; margin-top:10px; margin-bottom:10px;\" >";
                    echo "<h3 style=\"margin-bottom:10px;\" class=\"block\" >General Notes</h3>";
                    echo "<div class=\"input-main\" style=\"width:100%;\" >";
                        echo "<textarea id=\"note_general\" style=\"width:98%; height:5em;\" value=\" \" class=\"\" ></textarea>";
                    echo "</div>";
                echo "</div>";
                

                
            
            ?>

            <input type="text" class="hidden" id="note_quality" />
            <input type="text" class="hidden" id="note_value" />
            <input type="text" class="hidden" id="note_general" />
        </p>
    </div>
    
    
    <div id="dialog-override" class="hidden" title="Override Available Bottles">
        <div id="con_dialog_override" style="height:100px; width:100%; ">
            <?php
          
                echo "<h1 id=\"override_dialog_title\" style=\"margin-bottom:15px; width:100%; text-align:center;\" >Set Available Bottles</h1>";
                
                echo "<div class=\"input-main vertical-centre bottom-spacer\" style=\"height:50px; width:123px; margin-left:auto; margin-right:auto;\" >";
                    echo "<img src=\"/images/minus_grey_flat_32.png\" style=\"float:left; width:24px; height:24px; margin-right:15px; margin-left:0px; \" id=\"decrement_override\" class=\"ignore_dirty\" />";  
                    echo "<input type=\"number\" step=\"0\" min=\"-1\" max=\"5\" id=\"available_bottles\" disabled=\"disabled\" style=\"float:left; text-align:center; width:35px; height:35px; font-size:1.5em;\" >";
                    echo "<img src=\"/images/plus_grey_flat_32.png\" style=\"float:left; width:24px; height:24px; margin-left:15px; \" id=\"increment_override\" class=\"ignore_dirty\" />";  
                echo "</div>";
                echo "<div class=\"clear\" ></div>";
            ?>
        </div>  
        
        <input type="number" id="available_override" name="available_override" class="hidden">
        <input type="number" id="net_available" name="net_available" class="hidden">
        <input type="number" id="available_max" name="available_max" class="hidden">
        <input type="number" id="vintage_id" name="vintage_id" class="hidden">
    </div>





<?php

    if(isset($_POST['search_text'])){
        $search_text = $_POST['search_text'];
    }else{
        $search_text = "";
    }


echo "<div class=\"page_container\">";

    require_once("$root/includes/nav/topheader.php"); //header
    
    echo "<div style=\"margin-top:40px;\" class=\"wine_results\">";

        echo "<div class=\"search_container\" >";
                //expand accordian
                echo "<div class=\"vertical-centre\" style=\"height:30px; float:left; margin-left:9px; margin-right:15px;\" >";
                    echo "<img id=\"btn_expand_vintages\" class=\"search_button click\" src=\"/images/next_grey_flat_24.png\" height=\"18px\" />";
                echo "</div>"; 
                //search box
                echo "<div class=\"search_box vertical-centre\" >";
                    echo "<input name=\"search_text\" class=\"search_input\" id=\"search_text\" value=\"$search_text\" placeholder=\"search wines...\"  />";
                    echo "<img class=\"btn_reset_search search_button click\" id=\"btn_wine_search_reset\" style=\"float:right; margin-right:7px; \" src=\"/images/delete_grey_flat_32.png\" height=\"16px\" />";
                    echo "<input type=\"hidden\" class=\"search_input\" name=\"wine_id\" id=\"wine_id\" >";
                echo "</div>";
                //buttons
                echo "<div class=\"con_search_buttons vertical-centre\" >";
                    echo "<img id=\"btn_toggle_search_adv\" class=\"search_button toggle_search_adv click\" src=\"/images/show_grey_flat_24.png\" height=\"21px\" width=\"21px\"/>";
                echo "</div>"; //con_search_buttons
                
                echo "<div class=\"hide_small_screen\" style=\"float:right; margin-right:15px;\">";
                    if(is_authed()){
                        echo "<img class=\"click\" style=\"margin-right:30px;\" id=\"btn_add_wine\" name=\"btn_add_wine\" src=\"/images/add_wine_flat_grey_64.png\" height=\"30px\" >";
                    }
                    echo "<img class=\"click\" id=\"btn_show_acquire\" name=\"btn_show_acquire\" src=\"/images/shopping_cart_flat_grey_32.png\" height=\"30px\" >";
                echo "</div>";

        echo "</div>"; //search_container

        echo "<div class=\"clear\" ></div>";

        echo "<div class=\"search_advanced\" id=\"search\" style=\"padding-top:10px;\" >";
        
        echo "<div id=\"con_search_column_1_2\" style=\"float:left; width:auto; margin: 0px 10px 0px 10px; \" >";
            
            echo "<div class=\"input-main\">";
                echo "<h3>Wine Type:</h3>";
                echo "<select class=\"search_input\" name=\"winetype_id\" id=\"winetype_id\">";
                    echo "<option value=\"0\">Any";
                    $obj = new winetype();
                    $sort = "winetype ASC";
                    $var_results = $obj -> get();
                    if($var_results){
                        foreach($var_results as $var_result){
                            //return list of award orgs
                            $item = $var_result['winetype'];
                            $key = $var_result['winetype_id'];
                            $winetype_selected = $_SESSION['var_wine_search_criteria']['winetype_id'] == $key ? "selected" : null; //persist search selection
                            echo ("<option value=".$key." $winetype_selected>".$item);
                        }
                    }
                echo "</select>";
            echo "</div>";

            echo "<div class=\"input-main\">";
                echo "<h3>Producer:</h3>";
                echo "<select class=\"search_input\" name=\"producer_id\" id=\"producer_id\">";
                    echo "<option value=\"0\">Any";
                    $obj = new producer();
                    $sort = "producer ASC";
                    $var_results = $obj -> get($where=false, $columns=false, $group=false, $sort='producer ASC', $limit=false);

                    foreach($var_results as $var_result){
                        //return list of award orgs
                        $item = $var_result['producer'];
                        $key = $var_result['producer_id'];
                        $producer_selected = $_SESSION['var_wine_search_criteria']['producer_id'] == $key ? "selected" : null; //persist search selection
                        echo ("<option value=".$key." $producer_selected>".$item);
                    }

                echo "</select>";
            echo "</div>";

            echo "<div class=\"input-main\">";
                echo "<h3>Merchant:</h3>";
                echo "<select class=\"search_input\" name=\"merchant_id\" id=\"merchant_id\">";
                    echo "<option value=\"0\">Any";
                    $obj = new merchant();
                    $var_merchants = $obj -> get($where=false, $columns=false, $group=false, $sort='merchant ASC', $limit=false);
                    foreach($var_merchants as $var_merchant){
                        //return list of award orgs
                        $item = $var_merchant['merchant'];
                        $key = $var_merchant['merchant_id'];
                        $merchant_selected = $_SESSION['var_wine_search_criteria']['merchant_id'] == $key ? "selected" : null; //persist search selection
                        echo ("<option value=".$key." $merchant_selected>".$item);
                    }
                echo "</select>";
            echo "</div>";

            echo "<div class=\"input-main\">";
                echo "<h3>Acquisition:</h3>";
                echo "<select class=\"search_input\" name=\"acquire_id\" id=\"acquire_id\">";
                    echo "<option value=\"0\">Any";
                    $obj = new acquire();
                    $sort = "acquire_date DESC";
                    $var_results = $obj -> get_extended(false,false,false,$sort);
                        foreach($var_results as $var_result){
                            //return list of award orgs
                            $merchant = $var_result['merchant'];
                            $acquire_date = $var_result['acquire_date'];
                            $item = $merchant." ".$acquire_date;
                            $key = $var_result['acquire_id'];
                            $acquire_selected = $_SESSION['var_wine_search_criteria']['acquire_id'] == $key ? "selected" : null; //persist search selection
                            echo ("<option value=".$key." $acquire_selected>".$item);
                        }
                echo "</select>";
            echo "</div>";
            
            echo "<div class=\"input-main\">";
                echo "<h3>Drinking Guide:</h3>";
                $guide_selected = $_SESSION['var_wine_search_criteria']['guide_selected'] ? "selected" : null; //persist search selection
                $this_year = date("Y");
                $next_year = $this_year + 1;
                $two_years = $next_year + 1;
                $three_years = $two_years + 1;
                $four_years = $three_years + 1;
                $five_years = $four_years + 1;
                $ten_years = $this_year + 10;
                $fifteen_years = $this_year + 15;
                $twenty_years = $this_year + 20;
                echo "<select class=\"search_input\" name=\"drinking_guide\" id=\"drinking_guide\">";
                    echo "<option value=\"0\">Any";
                    echo "<option value=\"$this_year\">Now ($this_year)";
                    echo "<option value=\"$next_year\">Next Year ($next_year)";
                    echo "<option value=\"$two_years\">Next 2 Years ($two_years)";
                    echo "<option value=\"$three_years\">Next 3 Years ($three_years)";
                    echo "<option value=\"$four_years\">Next 4 Years ($four_years)";
                    echo "<option value=\"$five_years\">Next 5 Years ($five_years)";
                    echo "<option value=\"$ten_years\">Next 10 Years ($ten_years)";
                    echo "<option value=\"$fifteen_years\">Next 15 Years ($fifteen_years)";
                    echo "<option value=\"$twenty_years\">Next 20 Years ($twenty_years)";
                echo "</select>";
            echo "</div>";

        echo "</div>"; //con_search_column_1_2
            
        echo "<div id=\"con_search_column_2_2\" style=\"float:left; width:auto; margin: 0px 10px 0px 10px;\" >";
                
            echo "<div class=\"input-main\" >";
                echo "<h3>Country:</h3>";
                echo "<select class=\"search_input\" name=\"country_id\" id=\"country_id\">";
                    echo "<option value=\"0\">Any";
                    $obj = new country();
                    $sort = "country ASC";
                    $var_results = $obj -> get($where=false, $columns=false, $group=false, $sort='country ASC', $limit=false);
                    foreach($var_results as $var_result){
                        //return list of award orgs
                        $item = $var_result['country'];
                        $key = $var_result['country_id'];
                        $country_selected = $_SESSION['var_wine_search_criteria']['country_id'] == $key ? "selected" : null; //persist search selection
                        echo ("<option value=".$key." $country_selected>".$item);
                    }
                echo "</select>";
            echo "</div>";
        

            echo "<div class=\"input-main\">";
                echo "<h3>Region:</h3>";
                echo "<select class=\"search_input\" name=\"region_id\" id=\"region_id\">";
                    echo "<option value=\"0\">Any";
                    $obj = new region();
                    $sort = "region ASC";
                    $var_results = $obj -> get($where=false, $columns=false, $group=false, $sort='region ASC', $limit=false);
                    foreach($var_results as $var_result){
                        //return list of award orgs
                        $item = $var_result['region'];
                        $key = $var_result['region_id'];
                        $region_selected = $_SESSION['var_wine_search_criteria']['region_id'] == $key ? "selected" : null; //persist search selection
                        echo ("<option value=".$key." $region_selected>".$item);
                    }
                echo "</select>";
            echo "</div>";

            echo "<div class=\"input-main\">";
                echo "<h3>Sub-Region:</h3>";
                echo "<select class=\"search_input\" name=\"subregion_id\" id=\"subregion_id\">";
                    echo "<option value=\"0\">Any";
                    $obj = new subregion();
                    $sort = "subregion ASC";
                    $var_results = $obj -> get($where=false, $columns=false, $group=false, $sort='subregion ASC', $limit=false);
                    foreach($var_results as $var_result){
                        $item = $var_result['subregion'];
                        $key = $var_result['subregion_id'];
                        $subregion_selected = $_SESSION['var_wine_search_criteria']['subregion_id'] == $key ? "selected" : null; //persist search selection
                        echo ("<option value=".$key." $subregion_selected>".$item);
                    }
                echo "</select>";
            echo "</div>";
            
            echo "<div class=\"input-main\">";
                echo "<h3>Vintage Quality:</h3>";
                $quality_selected = $_SESSION['var_wine_search_criteria']['vintage_quality'] ? "selected" : null; //persist search selection
                echo "<select class=\"search_input\" name=\"vintage_quality\" id=\"vintage_quality\">";
                    echo "<option value=\"0\">Any";
                    echo "<option value=\"2\"> 1";
                    echo "<option value=\"3\"> 1.5";
                    echo "<option value=\"4\"> 2";
                    echo "<option value=\"5\"> 2.5";
                    echo "<option value=\"6\"> 3";
                    echo "<option value=\"7\"> 3.5";
                    echo "<option value=\"8\"> 4";
                    echo "<option value=\"9\"> 4.5";
                    echo "<option value=\"10\"> 5";
                echo "</select>";
            echo "</div>";
            
            
            echo "<div class=\"input-main\">";
                echo "<h3>Available Bottles:</h3>";
                $available_selected = $_SESSION['var_wine_search_criteria']['available'] ? "selected" : null; //persist search selection
                echo "<select class=\"search_input\" name=\"available\" id=\"available\">";
                    echo "<option value=\"0\">Any";
                    echo "<option value=\"1\" $available_selected>Available";
                echo "</select>";
            echo "</div>";
            
        echo "</div>"; //con_search_column_2_2

        echo "<div class=\"float_left clear\" style=\"margin-left:5px;\" >";
            echo "<input type=\"button\" name=\"btnReset\" class=\"btn_reset_search big_button\" value=\"Clear\">";
            echo "<input type=\"button\" name=\"btn_adv_submit\" value=\"Search\" id=\"btn_adv_submit\" class=\"btn_search_wine big_button\" >";
            echo "<input type=\"button\" name=\"btn_adv_show_less\" value=\"Show Less\" id=\"btn_adv_submit\" class=\"btn_search_wine big_button toggle_search_adv\" >";
        echo "</div>";

        echo "<div class=\"clear\" ></div>";
            
        echo "<hr/>";

        echo "</div>"; //search_advanced

        echo "<div class=\"clear\" ></div>";

        //wines - search results
        echo "<div id=\"con_rpc_wine_search_results_html\" style=\"text-align:left;\" >";
            //show wines search results - rpc_wine_search_results_html.php
        echo "</div>";

        echo "<div class=\"clear\" ></div>";

    echo "</div>"; //wine_results

    //Right Slide Out Panel - Acquisitions
    echo "<div id=\"panel_right\" class=\"panel_right\" >";

        echo "<div style=\"float:left;\" id=\"con_acquisitions\" >";
            //populated by /acquire/rpc_acquire_listBox_html.php
        echo "</div>";

    echo "</div>"; //acquisition_container

    echo "<div class=\"clear\" ></div>";

echo "</div>"; //page_container


if(is_authed()){
    if($_SESSION['username'] == "magnus@mcdonaldruden.co.uk"){
        //disabled
        //$admin_option = "<div>Admin<img style=\"float:right; margin-top:2px;\" src=\"/images/arrow_next_black.svg\" height=\"21px\" /></div>";
    }else{
        $admin_option = "";
    }
}

//common dialogs
require_once("$root/includes/standard_dialogs.inc.php");

?>
    
<!-- Pop-up Menus-->
<div id='main_menu' class="pop_up" style="width:225px; display:none; position:fixed; z-index:35;">
    <div class="ui-menu-item-first">Show Acquisitions<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Wine<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Acquisition<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>Reference Data<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <?php echo $admin_option ?>
    <div class="ui-menu-item-last">Settings<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>

<div id='wine_menu' class="pop_up" style="width:200px; display:none;">
    <div class="ui-menu-item-first ui-menu-header">Wine actions</div>
    <div>Add Vintage<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <!--<div id="btn_google_search" >Google Search<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>-->
    <div class="ui-menu-item-last">Edit Wine<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>


<div id='vintage_menu' class="pop_up" style="width:200px; display:none;">
    <div class="ui-menu-item-first ui-menu-header">Vintage actions</div>
    <div>Edit Vintage<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>Add to Basket<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div class="ui-menu-item-last">Tasting Note<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>



<?php require_once("$root/includes/script_libraries.inc.php");//include all script libraries ?>
<script type="text/javascript" src="/libraries/jquery.ui.listBox.js"></script>
<script type="text/javascript" src="/libraries/jquery.activity-indicator-1.0.0.min.js"></script>
<script type="text/javascript">

//Note: Popup blocker will prevent anything other than a direct user action from opening new window

$(document).ready(function(){
    
    var this_page = "/index.php";
    var bln_expand_all_vintages = false;

    //page control object
    var obj_page = new page_control({
        save_function: function(){
            //insert here
        },
        page_url: this_page, //set page url
        no_dirty: true
    });

    $(document).mouseup(function (e)
    { //closes right panel when clicking outside of panel
        var container = $("#panel_right");

        if (!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.hide("slide", { direction: "right" }, 400);
        }
    });
    
    var resizeTimer;
    var windowHeight = $(window).height();
    $(window).resize(function(){
        //reload wines so that height is adjusted to window size
        if($(window).height() != windowHeight){ //prevents IOS random scroll resize
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() { //wait until window stops resizing
                // Run code here, resizing has "stopped"
                var height = $(window).height();
                console.log("height = "+height);
                load_wines_html();
                //Change height of acquisitions listbox
                var acquireHeight = height - 65;
                $("#con_acquisitions").listBox("setHeight",acquireHeight); //refresh acquisition listBox
            }, 250);
            windowHeight = $(window).height(); //reset windows height checker
        }
    });
    

    //recent acquistions listbox
    $("#con_acquisitions").listBox({
        title: "Acquisitions",
        width: 330,
        height: $(window).height() - 65,
        showFilter: true,
        showBorder: true,
        showShadow: false,
        showRoundedCorners: false,
        addClass: 'listBox_acquisition_theme',
        listContent: '/acquire/rpc_acquire_listBox_html.php',
        clickAdd: function(event, data){
            add_acquisition(data);
        },
        clickSelected: function(event, data){
            console.log('acquisition selected data:');
            console.log(data);
            $('#acquire_id').val(data.listBox_id); //set acquire dropdown value
        },
        clickEdit: function(event, data){         
            open_acquisition(data.listBox_id);
        },
        clickFilter: function(event, data){
            search_wines();
        },
        clickFilterClear: function(event, data){
            $('#acquire_id').val(0); //set acquire dropdown value
            $("#con_acquisitions").listBox("clearSelected");//clear selected item from listBox
            $("#con_acquisitions").listBox("refresh",true); //refresh acquisition listBox
            //reset_search();
            search_wines();
        },
        clickTitle: function(event, data){
            $("#panel_right").hide("slide", { direction: "right" }, 300);
        }

    });
    
    //set onload behaviour
    $('#search').hide(); //hide advanced search features
    $('.vintages_panel').hide();
    $('.vintage_details').hide();
    load_wines_html();
    

    //set focus on search box
    var isiPad = navigator.userAgent.match(/iPad/i) != null;
    if(!isiPad){
        //setting focus on ipad causes keyboard to show, even if selecting item in listbox
        $('#search_text').focus();
    }

    //initialise basket
    initialise_basket();


    //_______FUNCTIONS_______

    function spinner(){
        //display process spinner
        var opts = {
            lines: 13, // The number of lines to draw
            length: 7, // The length of each line
            width: 4, // The line thickness
            radius: 10, // The radius of the inner circle
            rotate: 0, // The rotation offset
            //color: '#48DD00', // #rgb or #rrggbb
            speed: 1, // Rounds per second
            trail: 60, // Afterglow percentage
            //shadow: false, // Whether to render a shadow
            //hwaccel: false, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            //zIndex: 2e9, // The z-index (defaults to 2000000000)
            top: '250px', // Top position relative to parent in px
            left: 'auto' // Left position relative to parent in px
            };

            var target = document.getElementById('con_rpc_wine_search_results_html');
            //var spinner = new Spinner(opts).spin(target);
    }


    function refresh(){ //refresh html elements
        //refresh page
        store_open_panels();
        load_wines_html();//load wine list
        $("#con_acquisitions").listBox("refresh"); //refresh acquisition listBox
    }
    

    $.widget( "custom.catcomplete", $.ui.autocomplete, {
        _create: function() {
          this._super();
          this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
        },
        _renderMenu: function( ul, items ) {
          var that = this,
            currentCategory = "";
          $.each( items, function( index, item ) {
            var li;
            if ( item.category != currentCategory ) {
              ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
              currentCategory = item.category;
            }
            li = that._renderItemData( ul, item );
            if ( item.category ) {
              li.attr( "aria-label", item.category + " : " + item.label );
            }
          });
        }
    });


    $( "#search_text" ).catcomplete({
            source: "wine/rpcWineSearchAutocomplete.php",
            select: function(event,ui){
                console.log(ui.item);
                //update autocomplete with label
                console.log(ui.item.label);
                $('#search_text').val(ui.item.label); //update with label
                //$('#wineSearchAutocompleteValue').val(ui.item.value);
                $('#search_selected_id').val(ui.item.value);
                $('#search_category').val(ui.item.category);
                //reset autocomplete set fields
                $('#search_text').val(''); //remove search text  
                $('#wine_id').val(0);
                $('#country_id').val(0);
                $('#region_id').val(0);
                $('#subregion_id').val(0);
                $('#producer_id').val(0);
                $('#merchant_id').val(0);
                //put_search_to_session(); 
                switch(ui.item.category){
                    case 'Wine':
                        $('#wine_id').val(ui.item.value);
                        break;
                    case 'Country':
                        $('#country_id').val(ui.item.value);
                        break;
                    case 'Region':
                        $('#region_id').val(ui.item.value);
                        break;
                    case 'Subregion':
                        $('#subregion_id').val(ui.item.value);
                        break;
                    case 'Producer':
                        $('#producer_id').val(ui.item.value);
                        break;
                    case 'Merchant':
                        $('#merchant_id').val(ui.item.value);
                        break;
                }
                search_wines(true);
                return false; //cancel event to prevent value overriding the label

            },
            open: function(){
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) { //resolves two click issue in IOS
                    $('.ui-autocomplete').off('menufocus hover mouseover');
                }
            }
    });


    function load_wines_html(){ //load wines
        var viewportHeight = $(window).height(); //pass height to php view Load POST to set number of rows
        $('#con_rpc_wine_search_results_html').activity(); //show spinner    
        $('#con_rpc_wine_search_results_html').load('/wine/rpc_wine_search_results_html.php', {viewportHeight: viewportHeight},function(){ //load results
           //toggle_vintages(); //run following code once refreshed
           //set_open_panels();
           set_reset_button(); //show or hide reset button based on search results
           //TODO:if only one result is returned expand it fully
    
            if($('.wine_accordian').size()==1){
                //only one wine result returned - expand it
                console.log('only one wine - so expand it');
                $('.vintages_panel').each(function(index) {
                    var wine_id = ($(this).attr('id').replace("vintages_panel_", ""))*1;
                    toggle_vintage_panel(wine_id, 'medium', function(){
                        show_all_vintage_details(wine_id); //call back to open all vintage details as well
                    });
                    
                });
            }
            
        });
        
    };
    
    
    function set_reset_button(){
        /* php on rpc_wine_search_results_html.php checks if search paramter array is empty and sets hidden input field
         * to True if the results are filtered
         * This function checks that field after the page has loaded and hides or shows the reset button
         */
        
        var filtered = $('#search_filter_status').val();
        
        if (filtered === 'true') {
            $('.btn_reset_search').show();
            $('#con_acquisitions_btn_clear_filter').show(); //acquisition listBox
        }else{
            $('.btn_reset_search').hide();
            //$('#con_acquisitions_btn_clear_filter').hide(); //acquisition listBox
        }
        
    }

    function toggle_vintage_panel(wine_id, duration, callback){
        //toggle vintage accordian panel to show vintages under a wine - open/close
        console.log('toggle_vintage_panel wine_id='+wine_id);
        //hide all children (vintage_details panels)
        $('#wine_accordian_'+wine_id).next('.vintages_panel').children('.vintage_accordian').children('.vintage_details').hide();
        //change all vintage expand / collapse indicators to closed (right arrow)
        $('#wine_accordian_'+wine_id).next('.vintages_panel').children('.vintage_accordian').find('.vintage_expanded_indicator').removeClass('arrow_down').removeClass('arrow_right').addClass('arrow_right');
        $panel = "#vintages_panel_"+wine_id;
        $($panel).slideToggle(duration,function(){
            //callback function
            if($($panel).is(":visible")){
                console.log($panel + ' :visible'); 
             }else{
                 console.log($panel + ' :hidden');
             }
        });
        
        //swivel open close arrow
        arrow_id = '#arrow_indicator_'+wine_id;
        $(arrow_id).toggleClass('arrow_down');
        
        if(typeof callback === 'function'){
            callback();
        }
    }


    function toggle_vintage_details_panel(vintage_id,duration){
        //toggle vintage details panel - open or close
        console.log('toggle_vintage_details_panel');
        $panel = "#vintage_details_"+vintage_id;
        $($panel).slideToggle(duration);
        arrow_id = '#arrow_indicator_vintage_'+vintage_id;
        $(arrow_id).toggleClass('arrow_down');
    }
    
    
    function show_all_vintage_details(wine_id,duration){
        //show all vintage details for a given wine_id
        console.log('show all vintage details...');
        $wine = "#vintages_panel_"+wine_id;
        $($wine).find(".vintage_accordian").each(function(index, element){
            var id = $(element).attr('id');
            var vintage_id = id.replace("vintage_accordian_", "");
            console.log('call toggle_vintage_details_panel : '+vintage_id);
            toggle_vintage_details_panel(vintage_id);
        });

    }
    
    
    function override_available_bottles(vintage_id, object){
        //display dialog to change available override
        console.log('override_available_bottle vintage_id = '+vintage_id);
        get_override_data(vintage_id,object);
    }
    
    
    function get_vintage_price(vintage_id){
        //get full price, and price paid from last acquisiton for vintage
        
        //post to rpc_vintage to get prices from last acquisition for vintage
        //on success return values as array arrPrice
                
        $.post("/acquire/rpc_acquire_db.php", {
            action: 'get_acquisition_details',
            vintage_id: vintage_id
        }, function(data){
            if(data.success){
                //success
                console.log('get_acquisition_details success');
                console.log(data.data);
                var string = "";
                var pricePaid = data.data[0]['unit_price'];
                var priceDisc = data.data[0]['discounted_price'];
                if(pricePaid == priceDisc){
                    string = "£"+pricePaid;
                }else{
                    string = "£"+priceDisc+ " / £"+pricePaid;
                }
                $('#quick_price').text(string);
            } else {
                console.log('get_acquisition_details failed = '+data.error);
                $('#quick_price').val('');
            }
        }, "json");

    }


    function quick_note(vintage_id, object, arrPrice){
        //display dialog-box to add quick note or launch full note
        
        //reset quick note values
        $('#note_quality').val(0);
        $('.auto-submit-star').rating('drain');
        $('#note_value').val(0);
        $('.auto-submit-pound').rating_pound('drain');
        $('#note_general').val("");
        $('#quick_price').text("");
        
        //TODO add arrPrice values to dialog, show nothing if values are not present
        
        get_vintage_price(vintage_id);
        
        //determine screen size
        var windowWidth = $(window).width();
        if(windowWidth > 500){
            dialogWidth = 470;
            positionMy = "right top";
            positionAt = "left bottom";
            positionOf = object;
        } else {
            dialogWidth = windowWidth;
            positionMy = "right top+20px";
            positionAt = "right bottom";
            positionOf = "#top_nav";
        }   
        
        $("#dialog-rate").dialog({
            modal: true,
            width: dialogWidth,
            buttons: {
                OK: function() { //save tasting note
                    $(this).dialog('close'); //close dialog
                    quality_rating = $('#note_quality').val();
                    value_rating = $('#note_value').val();
                    note_general = $('#note_general').val();
                    console.log("save quick_note vintage_id="+vintage_id+" quality = "+quality_rating+" value_rating = "+value_rating+" note_general = "+note_general);

                    $.post("/vintage/rpc_notes.php", {
                        action: 'put_db',
                        vintage_id: vintage_id,
                        note_quality: quality_rating,
                        note_value: value_rating,
                        note_general: note_general,
                        note_type: 'quick'
                    }, function(data){
                        if(data.success){
                            //save was successful
                            //console.log('quick_note save was successful');
                            jQuery.noticeAdd({
                                text: 'Quick Note saved successfully.',
                                stay: false
                            });
                            //reload html
                            refresh();

                        } else {
                            console.log('quick_note saved failed - error = '+data.error);
                            jQuery.noticeAdd({
                                text: "Error saving Quick Note",
                                stay: false
                            });
                        }
                    }, "json");

                },
                Full: function(){
                    //open full note
                    note_id = 0; //new note
                    quality_rating = $('#note_quality').val();
                    value_rating = $('#note_value').val();
                    note_general = $('#note_general').val();
                    console.log("open full note vintage_id="+vintage_id+" quality="+quality_rating+ " value= "+value_rating);
                    open_note(note_id, vintage_id, quality_rating, value_rating, note_general);
                    $(this).dialog('close');
                },
                Cancel: function(){
                    $(this).dialog('close');

                }
            },
            dialogClass: "clean-dialog",
            position: { 
                my: positionMy,
                at: positionAt, 
                of: positionOf
            }
        });
    
    
    }
    


    function search_wines(ignore_text){
        //seach wines and update results
        console.log('function: search_wines');
        
        spinner();//start spinner
        
        var var_search = {};
        var count = $('.search_input').length; //get number of elements
        
        $(".search_input").each(function(index){
            var name = $(this).attr('id');
            var_search[name] = $(this).val();
            if(index==count-1){//loop completed
                if(ignore_text){ //remove text from search fields
                    var_search['search_text'] = "";
                }
                console.log(var_search);
                
                $.post("/wine/rpc_wine_db.php", {
                    action: 'put_search_to_session',
                    var_search: var_search
                }, function(data){
                    if(data.success){
                        //new search so set pagination to page 1 - pagination function will refresh html
                        pagination("index_page_pagination", 1);

                    } else {
                        console.log('error putting search to session='+data.error);
                    }

                }, "json");

            }
        });
    }
    
    
    
//_______ACTIONS and EVENTS_______
    
    $('.btn_search_wine').click(function(){
        //submit search for wine
        search_wines();
    });


    $(".search_input").change(function(e){
        //trigger search update
        search_wines();
    });
    
    
    function search_trigger(element){
        //use as trigger to update search results when a search input is
        //updated
        
        //FIX: class.wine_search is filtered on details stored in session, because text box is cleared after search but session 
        //isnt then the subsequent serach is an AND serach based on saved session.
        // If the session is cleared after the page refreshes and loads results then navigating away from the page would result in the
        // filtered list being lost, and the serach being repeated
        // 
        
        console.log('search_trigger');
        
        if($(element).val()>0){
            //set background color
            $(element).removeClass('highlight_input').addClass('highlight_input');
        } else {
            $(element).removeClass('highlight_input');
        }

        //clear search_text and wine_id if advanced criteria used
        if($(element).attr('id')=="search_text" || $(element).attr('id')=="wine_id"){
            //do nothing as text has changed
            //console.log ('search_text has changed');
        } else {
            //clear text
            //console.log ('advanced inputs have changed');
            $("#search_text").val("");
            $("#wine_id").val("0");
        }

        //update search and html
        search_wines();
    }


    $('.btn_reset_search').click(function(){
        //submit search for wines
        console.log('btn_reset_search');
        reset_search();
        close_all_panels();
    });


    function reset_search(ignore_search_text){
        //reset all search fields
        console.log('function reset_search');

        var count = $('.search_input').length;

        //remove any highlight formats
        $(".search_input").removeClass('highlight_input');
        
        //clear acquisition listBox selected items
        $("#con_acquisitions").listBox("refresh", true); //refresh acquisition listBox - true clears selected item

        $(".search_input").each(function(index){
            if($(this).attr('id')=="search_text" || $(this).attr('id')=="wine_id"){
                if(ignore_search_text){
                    //do not clear search text
                } else {
                    $(this).val("");
                }
            }else{
                $(this).val("0");
            }

            if(index==count-1){
                //run code - iterations complete
                search_wines(); //update search html
            }
        });

     }



    $('.toggle_search_adv').click(function(){
       //search panel slide
        $('#search').slideToggle("medium");
    });


    $('#btn_expand_vintages').click(function(){
        //expand or collapse all wines to show vintages
        //console.log('btn_expand_vintages clicked');

        if(bln_expand_all_vintages){
            bln_expand_all_vintages = false;
        } else {
            bln_expand_all_vintages = true;
        }

        toggle_vintages();

    });


    function close_all_panels(){
        //close all panels - effective reset
        console.log('close_all_panels...');
        $(".vintages_panel").slideUp('fast'); //close vintages_panel - hide list of vintages
        $(".vintage_details").slideUp('fast'); //close vintage_details panels - hide vintage details
        $("#btn_expand_vintages").attr("src", "/images/next_grey_flat_24.png"); 
        $(".wine_panel_toggle").removeClass('arrow_down'); //reset
        $(".vintage_expanded_indicator").removeClass('arrow_down'); //reset vintage details indicator
    }
    
    function toggle_vintages(){
        //show or hide all vintages for wines

        console.log('function toggle_vintages');
        if(bln_expand_all_vintages){
            console.log('expand all vintages');
            $(".vintage_details").hide();
            $(".vintages_panel").slideDown('medium');
            $("#btn_expand_vintages").attr("src", "/images/down_flat_grey_24.png");
            $(".wine_panel_toggle").removeClass('arrow_down').addClass('arrow_down');
            $(".vintage_expanded_indicator").removeClass('arrow_down');
        }else{
            close_all_panels();
        };

        //if only one result - expand it
        if($('.wine_accordian').size()==1){
            //only one wine result returned - expand it
            console.log('only 1 result returned so expand it');
            $('.vintages_panel').each(function(index) {
                var wine_id = ($(this).attr('id').replace("vintages_panel_", ""))*1;
                toggle_vintage_panel(wine_id);
            });
        }

    }
    
    
    var vintage_menu = $("#vintage_menu").menu({
       items: "> :not(.ui-menu-header)",
       select: function( event, ui ) {
            menu_select({ //pass selcted object to menu function
                selected_item: ui.item[0].textContent,
                menu_id: $(this).attr('id'),
                origin_id: $(this).data('origin_id')
            });
        }
    }).hide();
    

    $(document).on('click','.vintage_menu',function() {
        // Make use of the general purpose show and position operations
        // open and place the menu where we want.

        //***set menu name here***
        var this_menu = vintage_menu;
        
        //put unique id into menu data field so the menu can be linked back to the initiating item
        var id = $(this).attr('id').replace("vintage_menu_","");
        this_menu.data('origin_id',id);
        
        //close all other menus before opening this one
        var this_menu_id = this_menu.attr('id');
        $(".ui-menu:not(#"+this_menu_id+")" ).hide();

        //show menu
        this_menu.show().position({
              my: "right top",
              at: "middle middle",
              of: this
        });

        //set event to close when clicking anywhere on wondow       
        $(document).on( "click", function() {
              this_menu.hide();
        });

        //handle touching outside div on iPad
        $(document).on('touchstart', function (event) {
            if (!$(event.target).closest(this_menu).length) {
                this_menu.hide();
            }
        });

        // Make sure to return false here or the click registration
        // above gets invoked.
        return false;
    });


    var wine_menu = $("#wine_menu").menu({
       items: "> :not(.ui-menu-header)",
       select: function( event, ui ) {
            menu_select({ //pass selcted object to menu function
                selected_item: ui.item[0].textContent,
                menu_id: $(this).attr('id'),
                origin_id: $(this).data('origin_id')
            });
        }
    }).hide();
    

    $(document).on('click','.wine_menu',function() {
        // Make use of the general purpose show and position operations
        // open and place the menu where we want.

        //***set menu name here***
        var this_menu = wine_menu;
        
        //put unique id into menu data field so the menu can be linked back to the initiating item
        var id = $(this).attr('id').replace("wine_menu_","");
        this_menu.data('origin_id',id);
        
        //close all other menus before opening this one
        var this_menu_id = this_menu.attr('id');
        $(".ui-menu:not(#"+this_menu_id+")" ).hide();

        //show menu
        this_menu.show().position({
              my: "right top",
              at: "middle middle",
              of: this
        });

        //set event to close when clicking anywhere on wondow       
        $(document).on( "click", function() {
              this_menu.hide();
        });

        //handle touching outside div on iPad
        $(document).on('touchstart', function (event) {
            if (!$(event.target).closest(this_menu).length) {
                this_menu.hide();
            }
        });

        // Make sure to return false here or the click registration
        // above gets invoked.
        return false;
    });
    
    
    //main menu pop-up
    var main_menu = $("#main_menu").menu({
       items: "> :not(.ui-menu-header)",
       select: function( event, ui ) {
            menu_select({ //pass selcted object to menu function
                selected_item: ui.item[0].textContent,
                menu_id: $(this).attr('id')
            });
        }
    }).hide();


    $('#btn_main_menu').click(function(e) {
        // Make use of the general purpose show and position operations
        // open and place the menu where we want.

        $('.ui-menu:not(#main_menu)' ).hide();

        main_menu.show().position({
              my: "left top",
              at: "left bottom",
              of: this
        });

        $(document).on( "click", function() {
              main_menu.hide();
        });

        //handle touching outside div on iPad
        $(document).on('touchstart', function (event) {
            if (!$(event.target).closest(main_menu).length) {
                main_menu.hide();
            }
        });
        
        (e).stopPropagation();
        return false;
    });
    
    
    function menu_select(selected_object){
        var selected_item = selected_object['selected_item'];
        var menu_id = selected_object['menu_id'];
        var origin_id = selected_object['origin_id'];
        console.log(origin_id);

        console.log('Menu_Select Function. Item: '+selected_item + ' Menu: '+menu_id + ' origin_id: '+origin_id);
        
        switch(menu_id){
            case 'main_menu':
                switch(selected_item){
                    case 'New Wine':
                        add_wine();
                        break;
                    case 'New Acquisition':
                        add_acquisition();
                        break;
                    case 'Show Acquisitions':
                        $('#panel_right').toggle("slide", { direction: "right" }, 500);
                        break;
                    case 'Reference Data':
                        open_reference_data();
                        break;
                    case 'Wines':
                        open_wines();
                        break;
                    case 'Settings':
                        open_settings();
                        break;
                    default:
                        console.log('selected_item not recognised: '+selected_item);
                }
                break;
   
            case 'wine_menu':
                switch(selected_item){
                    case 'Add Vintage':
                        add_vintage(origin_id);
                        break;
                    case 'Edit Wine':
                        edit_wine(origin_id);
                        break;
                    case 'Google Search':
                        get_google_search_term_wine(origin_id);
                        break;
                    default:
                        console.log('selected_item not recognised: '+selected_item);
                }
                break;
    
            case 'vintage_menu':
                switch(selected_item){
                    case 'Edit Vintage':
                        edit_vintage(origin_id);
                        break;
                    case 'Add to Basket':
                        add_vintage_basket(origin_id);
                        break;
                    case 'Tasting Note':
                        quick_note(origin_id);
                        break;
                    default:
                        console.log('selected_item not recognised: '+selected_item);
                }
                break;
                
            default:
                console.log("menu_id not recognised: "+menu_id);
                
        }
        
    }
    
    
    $('#btn_show_acquire').click(function(){
        //show acquisition panel
        $('#panel_right').toggle("slide", { direction: "right" }, 500);
    });
    
    
    function open_reference_data(){
        obj_page.leave_page({
            dst_url: "/admin/index_admin.php",
            rtn_url: this_page,
            dst_action: 'open',
            page_action: 'leave'
        });
    }
    
    function open_settings(){
        obj_page.leave_page({
            dst_url: "/user/settings.php",
            rtn_url: this_page,
            dst_action: 'open',
            page_action: 'leave'
        });
        
    }
    
    
    function open_wines(){
        //open Wines page
        obj_page.leave_page({
            dst_url: "/index.php",
            dst_action: 'open',
            page_action: 'leave'
        });
    }
    
    
    function get_override_data(vintage_id,object){
        //get override data for vintage and save to dialog
        //var override_max, override_min, available_bottles;
        
        console.log('get_override_data vintage_id = '+vintage_id);
        
        $('#con_dialog_override :input').val(''); //clear dialog inputs

        $.post("/vintage/rpc_vintage.php", {
            action: 'get_vintage_available_override_details',
            vintage_id: vintage_id
        }, function(data){
            if(data.success){
                console.log('get_override_data success');
                console.log(data);
                //$('#available_override').val(data.details.override);
                //$('#override_max').val(data.details.override_max);
                //$('#override_min').val(data.details.override_min);
                $('#available_bottles').val(data.details.available_bottles);
                //$('#available_max').val(data.details.available_max);
                $('#available_max').val(data.details.gross_available_bottle_count);
                $('#net_available').val(data.details.net_available_bottle_count);
                $('#vintage_id').val(vintage_id);
                $('#available_bottles_text').text(data.details.available_bottles+' Available Bottles');
                show_override_available_dialog(object); //open dialog
            } else {
                console.log('error with get_override_data error ='+data.error);
            }

        }, "json");
        
    }
    
    
    function put_available_override_data(optional_override){
        //put override data for vintage to rpc to save to db

        //get data
        if(optional_override >= 0){
            override = optional_override;
        }else{
            override = parseInt($('#available_override').val());
        }
        
        vintage_id = $('#vintage_id').val();
        console.log('put_override_data vintage_id = '+vintage_id+' override = '+override);

        $.post("/vintage/rpc_vintage.php", {
            action: 'put_vintage_available_override_details',
            vintage_id: vintage_id,
            override: override
        }, function(data){
            if(data.success){
                console.log('put_vintage_available_override_details success');
                console.log(data);
                refresh();
            } else {
                console.log('error with put_available_override_data. error ='+data.error);
            }

        }, "json");
        
    }
    
    
    function adjust_available_override(increment){
        //increment or decrement available override value on override-dialog
        console.log('adjust_available_override value = '+increment);
        //var override_value = 1 * $('#available_override').val();
        var available_max = 1 * $('#available_max').val();
        var net_available = 1 * $('#net_available').val();
        var available_bottles = 1 * $('#available_bottles').val();

        if( (increment > 0 && available_bottles < available_max) || (increment < 0 && available_bottles > 0) ) {
            available_bottles = available_bottles + increment;
            //available_bottles = available_bottles - increment;
            console.log('available_bottles = '+available_bottles);
        }
        
        //calculate override
        var available_override = net_available - available_bottles;
        
        console.log('calculated override = '+available_override);
        $('#available_override').val(available_override);
        $('#available_bottles').val(available_bottles);
        $('#available_bottles_text').text(available_bottles+' Available Bottles');
    }
    
    
    function show_override_available_dialog(object){
        
        //determine screen size
        var windowWidth = $(window).width();
        if(windowWidth > 500){
            dialogWidth = 320;
            positionMy = "left top";
            positionAt = "right bottom";
            positionOf = object;
        } else {
            dialogWidth = 320;
            positionMy = "centre top+20px";
            positionAt = "center bottom";
            positionOf = "#top_nav";
        } 
        
        $("#dialog-override").dialog({
            modal: true,
            width: dialogWidth,
            buttons: {
                OK: function() {
                    put_available_override_data();
                    store_open_panels(); //test
                    $(this).dialog('close'); //close dialog

                },
                Auto: function(){
                    put_available_override_data(0); //set override value to zero
                    $(this).dialog('close');
                },
                Cancel: function(){
                    $(this).dialog('close');
                }
     
            },
            dialogClass: "clean-dialog",
            position: { my: positionMy, at: positionAt, of: positionOf }
        });
  
    }
    
    
    function store_open_panels(){
        //function to get the id of all panels which are open
        //class = arrow_down
        //arrow_indicator_nnnn = wine
        //arrow_indicator_vintage_nnnn = vintage
        
        console.log('get_open_panels');
        var arrOpenObjects = $('.arrow_down').map(function(){return this.id;}).get();
        console.log(arrOpenObjects);
        
        jsonOpenObjects = JSON.stringify(arrOpenObjects);
        
        //save to local storage
        localStorage.setItem('openPanels',jsonOpenObjects);
        console.log(JSON.parse(localStorage.getItem('openPanels')));
    }
   
    
    function set_open_panels(){
        //function recalls persisted open panel data from localStorage
        //and opens panels that were previously open
        arrOpenPanels = JSON.parse(localStorage.getItem('openPanels'));
        console.log('open panels recovered from local storage');
        console.log(arrOpenPanels);
 
        for (var i=0; i < arrOpenPanels.length; i++) {
            var id = arrOpenPanels[i].replace("arrow_indicator_", "");
            if(id.search("vintage") >= 0){
                //vintage panel - remove extra text
                id = id.replace("vintage_", "");
                toggle_vintage_details_panel(id,'fast');
            }else{
                //wine panel
                toggle_vintage_panel(id,"fast");
            }
        }
        
    }
 
 
  


    //*****Events******

    $(document).on('click','#increment_override',function(){
        console.log('increment override...');
        adjust_available_override(1);
    });
    
    
    $(document).on('click','#decrement_override',function(){
        console.log('increment override...');
        adjust_available_override(-1);
    });

    
    $('#search_text').keyup(function(){ //reset search if all search text deleted
        if($(this).val()==""){
            reset_search();
        }
    });
    

    $(document).on('click','.vintage_panel_toggle',function(){
        var vintage_id = ($(this).closest(".vintage_accordian").attr('id').replace("vintage_accordian_", ""));
        console.log('vintage_id: '+vintage_id);
        if($(this).hasClass('.ignore_vintage_panel_toggle')){
            console.log('event cancelled by .ignore_vintage_panel_toggle');
            return false;
        }
        toggle_vintage_details_panel(vintage_id);
    });



    $(document).on('click','.btn_edit_vintage',function(e){
        //open vintage to edit
        var id = $(this).attr('id');//get vintage_id
        var vintage_id = (id.replace("edit_", ""))*1;
        console.log('edit vintage = '+vintage_id);
        //open vintage page to edit
        edit_vintage(vintage_id);
        e.stopPropagation()
    });
    
    
    function edit_vintage(vintage_id){
        //open vintage page to edit
        
        if(vintage_id){
            obj_page.leave_page(
            {
            dst_url:        "/vintage/vintage.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "vintage",
            dst_action:     "edit",
            object_id:      vintage_id
            });
        }
    };
    
    
    function open_note(note_id, vintage_id, quality, value, note_general){
        //open note
        
        //create note object for data object
        var data = ({
            quality_rating: quality,
            value_rating: value,
            note_general: note_general
        });
        
        obj_page.leave_page({
            dst_url:        "/vintage/tasting_note.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "note",
            dst_action:     "open",
            object_id:      note_id,
            parent_id:      vintage_id,
            data:           data
        });
       
    };
    
    
    function open_image(vintage_id){
        //open image_manager for given image
       
       //TODO: disabled for now - enable through adding (.image_con) to image container, image_manager needs to identify that image has been opened from Index and not Vintage_
       //and save image back to db and session - db commit is currently through vintage.php
        obj_page.leave_page({
            dst_url:        "/vintage/select_image.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "image", //loading vintage details will load image details to session
            dst_action:     "open",
            object_id:      vintage_id,
            parent_id:      vintage_id,
            child:          true
        });

    }
    
    
    $(document).on('click','.image_con',function(){
        //open selected image
        var vintage_id = ($(this).closest(".vintage_accordian").attr('id').replace("vintage_accordian_", ""))*1;
        var vintage_id = $(this).data('vintage_id');
        console.log('open image vintage_id='+vintage_id);
        
        open_image(vintage_id);

    });
    

    $(document).on('click','.note_link',function(){
        //open selected tasting note
        var note_id = $(this).attr('id');
        var vintage_id = $(this).data('vintage_id');
        console.log('open note_id='+note_id+' vintage_id='+vintage_id);
        
        open_note(note_id, vintage_id);

    });
    
    
    $(document).on('click','.acquire_link',function(){
        //open selected acquisition
        var acquire_id = $(this).attr('id');
        var vintage_id = $(this).data('vintage_id');
        console.log('open acquire_id='+acquire_id+' vintage_id='+vintage_id);
        
        open_acquisition(acquire_id, vintage_id);

    });


    $(document).on('click','.add_vintage_btn',function(){
        //edit vintage
        console.log('btn_add_vintage...');

        var id = $(this).attr('id');
        var wine_id = (id.replace("add_vintage_", ""))*1;
        console.log('selected wine_id='+wine_id);

        //put wine_id to session and open vintage to edit
        $.post("/vintage/rpc_vintage.php", {
            action: 'get_vintage_from_db',
            wine_id: wine_id,
            status: 1
        }, function(data){
            if(data.success){
                //redirect to vintage page
                child_url = "/vintage/vintage.php";
                parent_url = this_page;
                //page_flow_set(child_url,parent_url,true);
            } else {
                console.log('error opening creating new ='+data.error);
            }

        }, "json");

    });


    $('.wine_results').dblclick(function(){
        //collapse all vintage panels
        console.log('collapse all vintage panels');

        $('.vintages_panel').hide();

    });


    $('#btn_add_wine').click(function(){
        //add new wine
        add_wine();
    });
    
    
    $("#panel_right_btn_close").click(function(){
        //close right sliding panel
        $('#panel_right').toggle("slide", { direction: "right" }, 500); 
    });
    

    $(document).on('click','.btn_add_tasting_note',function(e){
        //add new note event
        var vintage_id = ($(this).attr('id').replace("add_note_", ""))*1;
        quick_note(vintage_id, this); //open quick note form, pass this context for dialog position
        console.log("add new tasting note vintage_id="+vintage_id);
        e.stopPropagation();
    });
    
    
    $(document).on('click','.btn_edit_image',function(){
        //add new note event
        var vintage_id = ($(this).attr('id').replace("edit_image_", ""))*1;
        console.log("edit_image vintage_id = "+vintage_id);
        open_image(vintage_id, this); //pass 'this' context for dialog position
        
    });


    $(document).on('click','.btn_edit_override',function(){
        //add new note event
        var vintage_id = ($(this).attr('id').replace("override_", ""))*1;
        console.log("override_available_bottles vintage_id = "+vintage_id);
        override_available_bottles(vintage_id, this); //open quick note form, pass this context for dialog position
        
    });
    

    $(document).on('click','.edit_wine_btn',function(){
        //open wine to edit

        var id = $(this).attr('id');
        var wine_id = (id.replace("edit_wine_", ""))*1;
        console.log('btn_edit_wine id='+wine_id);
        
        //open wine page to edit
        edit_wine(wine_id);

    });
    
    
    function get_google_search_term_wine(wine_id){
        //get search text to google for given wine_id
        
        console.log('wine_id = '+wine_id);
        
        if(!wine_id){
            console.log("no wine_id provided");
            return false;
        };
        
        $.post("/wine/rpc_wine_db.php", {
            action: 'google_search_term_wine',
            wine_id: wine_id
        }, function(data){
            if(data.success){
                console.log('get_google_search_term_wine succesful');
                console.log('gterm = '+data.term);
                google_pre = "https://www.google.com/search?q=";
                search_term = data.term;
                google_post = "&source=lnms&tbm=isch";
                href = google_pre + search_term + google_post;
                window.open(href,'_blank');
                
                google_search_wine(data.term);
            } else {
                console.log('Error getting google search term. Error='+data.error);
            }

        }, "json");
        
    };


    function google_search_wine(term){
       //open new window with google search
       console.log('google search');
       
       google_pre = "https://www.google.com/search?q=";
       search_term = term;
       google_post = "&source=lnms&tbm=isch";
       href = google_pre + search_term + google_post;
       window.open(href,'_blank');
       
    }


    function add_wine(){
        //Add new wine
        
        obj_page.leave_page({
            dst_url:        "/wine/wine.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "wine",
            object_id:      null,
            dst_action:     "add"
        });
       
    };


    function edit_wine(wine_id){
        //open wine page to edit

        if(wine_id){
            obj_page.leave_page({
                dst_url:        "/wine/wine.php",
                rtn_url:        this_page,
                page_action:    'leave',
                dst_type:       "wine",
                dst_action:     "edit",
                object_id:      wine_id
            });
        } else {
            console.log("error - no wine_id provided");
        };
        
    };
    

    function add_vintage(wine_id){
        //Add new vintage to wine
        
        obj_page.leave_page({
            dst_url:        "/vintage/vintage.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "vintage",
            parent_id:      wine_id,
            object_id:      0,
            dst_action:     "add"
        });
       
    };
    
    
    function add_acquisition(){
        //add new acquisition
        
        obj_page.leave_page({
            dst_url:        "/acquire/acquisition.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "acquisition",
            object_id:      0,
            dst_action:     "add"
        });
       
    };
    
    
    function open_acquisition(acquire_id,vintage_id){
        //open existing acquisition
        
        obj_page.leave_page({
            dst_url:        "/acquire/acquisition.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "acquisition",
            object_id:      acquire_id,
            dst_action:     "open"
        });
       
    };
    

    $(document).on('click','.add_vintage_btn',function(){
        //add vintage button
        console.log('btn_add_vintage...');

        var id = $(this).attr('id');
        var wine_id = (id.replace("add_vintage_", ""))*1;
        console.log('selected wine_id='+wine_id);
        
        if(wine_id){
            add_vintage(wine_id);
        };

    });
    

    //vintage accordian panel slide
    $('.btn_slide_vintages').click(function(){
        //toggle vintages panel
        console.log('.btn_slide_vintages');
        toggle_vintage_panel($(this).attr('id'));
    });


    $('.btn_slide_vintage_details').click(function(){
        //toggle vintage details panel
        toggle_vintage_detail_panel($(this).attr('id'));
    });


    $(document).on('click','.wine_panel_toggle',function(){
        //toggle vintage panel
        console.log('click .wine_panel_toggle');
        var wine_id = ($(this).closest(".wine_accordian").attr('id').replace("wine_accordian_", ""));
        toggle_vintage_panel(wine_id);
        
    });


    var pagination_id = "index_page_pagination";

    $(document).on('click','#btn_go',function(){
        //go to page number
        var page_num = ($("#page_num").val())*1;
        console.log('page_num='+page_num);
        if(typeof page_num == 'number'){
            console.log('page_num='+page_num);
            pagination(pagination_id, page_num);
        }
    });

    $(document).on('click','#btn_next',function(){
        //go to next page
        console.log("click next");
        pagination(pagination_id, 'next');
    });

    $(document).on('click','#btn_prev',function(){
        //go to next page
        pagination(pagination_id, 'prev');
    });

    $(document).on('click','#btn_first',function(){
        //go to next page
        pagination(pagination_id, 'first');
    });

    $(document).on('click', '#btn_last',function(){
        //go to next page
        pagination(pagination_id, 'last');
    });



    function pagination(id, command){
        //set server side details to control pagination
        //actions = next, prev, first, last, page

        if(id && command){
            $.post("/vintage/rpc_vintage.php", {
                action: 'pagination',
                id: id,
                command: command
            }, function(data){
                if(data.success){
                    console.log("pagination success");
                    $("#page_num").val(data.page_num);
                    refresh();
                } else {
                    console.log("pagination failed");
                }
            }, "json");

        }else{
            console.log('pagination id or command missing');
        }

    }


    //star rating
    $('.auto-submit-star').rating({
        callback: function(value, link){
            name = $(this).attr("name");
            if(!value>0){
                value = 0;
            }
            $("#"+name).val(value);
            console.log(name+" = "+value);
        }
    });


    //star rating
    $('.auto-submit-pound').rating_pound({
        callback: function(value, link){
            name = $(this).attr("name");
            if(!value>0){
                value = 0;
            }
            $("#"+name).val(value);
            console.log(name+" = "+value);
        }
    });



    $(document).on('scroll','page_container', function(){
        console.log("scroll...");
    });

}); //document.ready

</script>
</body>
</html>

