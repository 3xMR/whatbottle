<?php
/*
 * Release: 2.0
 * Released: TBC
 * Notes: Major release to make much more orientated for iPad
 *  New look, cleaner more square
 *  will render much larger on iPad better for touch
 *  search autocomplete now supports categories making it easier to search 
 *  Acquistions as slide out
 * 
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

            .vintage_details h3{
                color:#B5ADAD;
                font-weight: normal;
                margin-bottom:7px;
                margin-top:5px;
            }
            
            .vintage_details h4{
                margin-left:5px;
                margin-bottom:7px;

            }
            

        </style>

    </head>

<body>

    <div id="dialog-rate" class="hidden" title="Rate Vintage">
        <p>
            <?php
               //quality rating
                echo "<h1 style=\"margin-bottom:15px;\" >Rate Vintage</h1>";
                echo "<div style=\" width:200px; padding-left:5px; float:left; margin-bottom:10px; \" >";
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
                echo "<div style=\" width:200px; padding-left:5px; float:left; margin-left:20px; margin-bottom:10px;\" >";
                echo "<h3 style=\"margin-bottom:10px;\" class=\"block\" >Value</h3>";
                echo "<div class=\"rating\" style=\"width:192px; height:32x; display:block;\" >";
                    echo "<input name=\"note_value\" type=\"radio\" value=\"1\" class=\"auto-submit-pound \" title=\"Poor\" />";
                    echo "<input name=\"note_value\" type=\"radio\" value=\"2\" class=\"auto-submit-pound \" title=\"OK\" />";
                    echo "<input name=\"note_value\" type=\"radio\" value=\"3\" class=\"auto-submit-pound \" title=\"Good\" />";
                    echo "<input name=\"note_value\" type=\"radio\" value=\"4\" class=\"auto-submit-pound \" title=\"Very Good\" />";
                    echo "<input name=\"note_value\" type=\"radio\" value=\"5\" class=\"auto-submit-pound \" title=\"Excellent\" />";
                echo "</div>";
            echo "</div>";
            ?>

            <input type="text" class="hidden" id="note_quality" />
            <input type="text" class="hidden" id="note_value" />
        </p>
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
                    echo "<img class=\"btn_reset_search search_button click\" id=\"btn_wine_search_reset\" style=\"float:right; margin-right:7px;\" src=\"/images/delete_grey_flat_32.png\" height=\"16px\" />";
                    echo "<input type=\"hidden\" class=\"search_input\" name=\"wine_id\" id=\"wine_id\" >";
                echo "</div>";
                //buttons
                echo "<div class=\"con_search_buttons vertical-centre\" >";
                    echo "<img id=\"btn_toggle_search_adv\" class=\"search_button click\" src=\"/images/show_grey_flat_24.png\" height=\"21px\" width=\"21px\"/>";
                echo "</div>"; //con_search_buttons
                
                echo "<div style=\"float:right; margin-right:15px;\">";
                    if(is_authed()){
                        echo "<img class=\"click\" style=\"margin-right:20px;\" id=\"btn_add_wine\" name=\"btn_add_wine\" src=\"/images/add_wine_flat_grey_64.png\" height=\"30px\" >";
                    }
                    echo "<img class=\"click\" id=\"btn_show_acquire\" name=\"btn_show_acquire\" src=\"/images/shopping_cart_flat_grey_32.png\" height=\"30px\" >";
                echo "</div>";

        echo "</div>"; //search_container

        echo "<div class=\"clear\" ></div>";

        echo "<div class=\"search_advanced\" id=\"search\">";
        
        echo "<div id=\"con_search_column_1_2\" style=\"float:left; width:auto; margin:10px;\" >";
            
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
                            echo ("<option value=".$key.">".$item);
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
                        echo ("<option value=".$key.">".$item);
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
                        echo ("<option value=".$key.">".$item);
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
                            echo ("<option value=".$key.">".$item);
                        }
                echo "</select>";
            echo "</div>";

        echo "</div>"; //con_search_column_1_2
            
        echo "<div id=\"con_search_column_2_2\" style=\"float:left; width:auto; margin:10px; \" >";
                
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
                        echo ("<option value=".$key.">".$item);
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
                        echo ("<option value=".$key.">".$item);
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
                        //return list of award orgs
                        $item = $var_result['subregion'];
                        $key = $var_result['subregion_id'];
                        echo ("<option value=".$key.">".$item);
                    }
                echo "</select>";
            echo "</div>";

        echo "</div>"; //con_search_column_2_2

        echo "<div class=\"float_left clear\" style=\"margin-left:5px;\" >";
            echo "<input type=\"button\" name=\"btnReset\" class=\"btn_reset_search big_button\" value=\"Clear\">";
            echo "<input type=\"button\" name=\"btn_adv_submit\" value=\"Search\" id=\"btn_adv_submit\" class=\"btn_search_wine big_button\" >";
        echo "</div>";

        echo "<div class=\"clear\" ></div>";
            
        echo "<hr/>";

        echo "</div>"; //search_advanced

        echo "<div class=\"clear\" ></div>";

        //wines
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
        $admin_option = "<div>Admin<img style=\"float:right; margin-top:2px;\" src=\"/images/arrow_next_black.svg\" height=\"21px\" /></div>";
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
    <?php echo $admin_option ?>
    <div class="ui-menu-item-last">Reference Data<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>

<div id='wine_menu' class="pop_up" style="width:200px; display:none;">
    <div class="ui-menu-item-first ui-menu-header">Choose an action</div>
    <div>Add Vintage<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <!--<div id="btn_google_search" >Google Search<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>-->
    <div class="ui-menu-item-last">Edit Wine<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>


<?php require_once("$root/includes/script_libraries.inc.php");//include all script libraries ?>
<script type="text/javascript" src="/libraries/jquery.ui.listBox.js"></script>
<script type="text/javascript" src="/libraries/jquery.activity-indicator-1.0.0.min.js"></script>
<script type="text/javascript">

//Note: Popup blocker will prevent anything other than a direct user action from opening new window

//TODO: Persist accordian positions - so they are shown when returning
//TODO: Add pagination, sortby and number of items on page as option
//TODO: search by rating
//TODO: free text search on notes

//TODO: Double-click on image to open image_manager page as overlay
//TODO: Show value in note summary
//TODO: If login is magnus add admin option to top level menu - to access admin functions
//TODO: Centre label images
//TODO: Click Whatbottle text to go to /index.php


$(document).ready(function(){
    
    var this_page = "/index.php";

    //page control object
    var obj_page = new page_control({
        save_function: function(){
            //insert here
        },
        page_url: this_page, //set page url
        no_dirty: true
    });

    var bln_expand_all_vintages = false;

    $(document).mouseup(function (e)
    { //closes right panel when clicking outside of panel
        var container = $("#panel_right");

        if (!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.hide("slide", { direction: "right" }, 400);
        }
    });

    //set onload behaviour
    $('#search').hide();
    $('.vintages_panel').hide();
    $('.vintage_details').hide();
    load_wines_html();
    
    //recent acquistions listbox
    $("#con_acquisitions").listBox({
        title: "Acquisitions",
        width: 300,
        height: 300,
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
            console.log('clickFilterClear');
            $('#acquire_id').val(0); //set acquire dropdown value
            $("#con_acquisitions").listBox("clearSelected");//clear selected item from listBox
            $("#con_acquisitions").listBox("refresh"); //refresh acquisition listBox
            search_wines();
        }

    });
    
    $(document).on("click","#btn_test",function(){
        console.log('scroll...');
    });
    

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
        load_wines_html(); //load wine list
        //$("#con_acquisitions").listBox("refresh"); //refresh acquisition listBox
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
                //TODO: put search criteria to session so that rpcWineSearchResults.html picks them up when loading
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
        $('#con_rpc_wine_search_results_html').activity(); //show spinner    
        $('#con_rpc_wine_search_results_html').load('/wine/rpc_wine_search_results_html.php', function(){ //load results
           toggle_vintages(); //run following code once refreshed
           //TODO:if only one result is returned expand it fully
       });
    }


    function toggle_vintage_panel(wine_id){
        //toggle vintage accordian panel - open or close
        
        $panel = "#vintages_panel_"+wine_id;

        $($panel).slideToggle("medium",function(){
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
    }


    function toggle_vintage_details_panel(vintage_id){
        //toggle vintage details panel - open or close
        $panel = "#vintage_details_"+vintage_id;
        $($panel).slideToggle("medium");
        arrow_id = '#arrow_indicator_vintage_'+vintage_id;
        $(arrow_id).toggleClass('arrow_down');
    }


    function quick_note(vintage_id, object){
        //display dialog-box to add quick note or launch full note
        
        //reset quick note values
        $('#note_quality').val(0);
        $('.auto-submit-star').rating('drain');
        $('#note_value').val(0);
        $('.auto-submit-pound').rating_pound('drain');
        
        $("#dialog-rate").dialog({
            modal: true,
            width: 'auto',
            buttons: {
                OK: function() { //save tasting note
                    $(this).dialog('close'); //close dialog
                    quality_rating = $('#note_quality').val();
                    value_rating = $('#note_value').val();
                    console.log("save quick_note vintage_id="+vintage_id+" quality ="+quality_rating+" value_rating="+value_rating);

                    $.post("/vintage/rpc_notes.php", {
                        action: 'put_db',
                        vintage_id: vintage_id,
                        note_quality: quality_rating,
                        note_value: value_rating,
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
                    console.log("open full note vintage_id="+vintage_id+" quality="+quality_rating+ " value= "+value_rating);
                    open_note(note_id, vintage_id, quality_rating, value_rating);
                    $(this).dialog('close');
                },
                Cancel: function(){
                    $(this).dialog('close');

                }
            },
            dialogClass: "clean-dialog",
            position: { my: "right top", at: "left bottom", of: object }
        });
    }


    function search_wines(ignore_text){
        //seach wines and update results
        //console.log('function: search_wines');
        
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
                        //console.log('search wine SUCCESS');
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
        
        console.log('seacrh_trigger');
        
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
        reset_search();
    });


    function reset_search(ignore_search_text){
        //reset all search fields
        //console.log('function reset_search');

        var count = $('.search_input').length;

        //remove any highlight formats
        $(".search_input").removeClass('highlight_input');
        
        //clear acquisition listBox selected items
        $("#con_acquisitions").listBox("refresh",true); //refresh acquisition listBox - true clears selected item

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
                search_wines();
            }
        });

     }



    $('#btn_toggle_search_adv').click(function(){
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
            console.log('collapse all vintages');
            $(".vintages_panel").slideUp('medium');
            $("#btn_expand_vintages").attr("src", "/images/next_grey_flat_24.png");
            $(".wine_panel_toggle").removeClass('arrow_down');
            $(".vintage_expanded_indicator").removeClass('arrow_down');
        };

        //if only one result - expand it
        if($('.wine_accordian').size()==1){
            //only one wine result returned - expand it
            $('.vintages_panel').each(function(index) {
                var wine_id = ($(this).attr('id').replace("vintages_panel_", ""))*1;
                toggle_vintage_panel(wine_id);
            });
        }

    }


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
    

    //*****Events******

    
    $('#search_text').keyup(function(){ //reset search if all search text deleted
        if($(this).val()==""){
            reset_search();
        }
    });
    

    $(document).on('click','.vintage_panel_toggle',function(){
        var vintage_id = ($(this).closest(".vintage_accordian").attr('id').replace("vintage_accordian_", ""));
        console.log('vintage_id: '+vintage_id);
        toggle_vintage_details_panel(vintage_id);
    });



    $(document).on('click','.btn_edit_vintage',function(){
        //open vintage to edit
        var id = $(this).attr('id');//get vintage_id
        var vintage_id = (id.replace("edit_", ""))*1;
        console.log('edit vintage = '+vintage_id);
        //open vintage page to edit
        edit_vintage(vintage_id);
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
    
    
    function open_note(note_id, vintage_id, quality, value){
        //open note
        
        //create note object for data object
        var data = ({
            quality_rating: quality,
            value_rating: value
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
            rtn_url:        "/vintage/vintage.php",
            page_action:    'leave',
            dst_type:       "image",
            dst_action:     "open",
            parent_id:      vintage_id,
            child:          true
        });
        
    }
    
    
    $(document).on('click','.image_con',function(){
        //open selected image
        var vintage_id = ($(this).closest(".vintage_accordian").attr('id').replace("vintage_accordian_", ""))*1;
        console.log('open image vintage_id='+vintage_id);
        
        open_image(vintage_id);

    });
    

    $(document).on('click','.note_link',function(){
        //open selected tasting note
        var note_id = $(this).attr('id');
        var vintage_id = ($(this).closest(".vintage_accordian").attr('id').replace("vintage_accordian_", ""))*1;
        console.log('open note_id='+note_id+' vintage_id='+vintage_id);
        
        open_note(note_id, vintage_id);

    });
    
    
    $(document).on('click','.acquire_link',function(){
        //open selected acquisition
        var acquire_id = $(this).attr('id');
        var vintage_id = ($(this).closest(".vintage_accordian").attr('id').replace("vintage_accordian_", ""))*1;
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
    

    $(document).on('click','.btn_add_tasting_note',function(){
        //add new note event
        var vintage_id = ($(this).attr('id').replace("add_note_", ""))*1;
        quick_note(vintage_id, this); //open quick note form, pass this context for dialog position
        console.log("add new tasting note vintage_id="+vintage_id);
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
    

    function add_wine_old(){
        //add new wine
        console.log('fnc: add_wine');

        $.post("/wine/rpc_wine_db.php", {
            wine_id: null, //new wine
            status: 1, //new
            action: 'add_wine'
        }, function(data){
            if(data.success){
                console.log('wine form session set SUCCESS');
                //open wine_form
                child_url = "/wine/wine.php";
                parent_url = this_page;
                //page_flow_set(child_url,parent_url,true);
            }else{
                console.log('wine form session set FAILED');
                alert('critical error trying to open wine form');
            }
        }, "json");

    }
    

    function open_full_note_old(vintage_id, quality_rating, value_rating){
        //open tasting note page
        console.log('open full note');
        $.post("/vintage/rpc_notes.php", {
            action: 'new_note',
            vintage_id: vintage_id,
            quality_rating: quality_rating,
            value_rating: value_rating
        }, function(data){
            if(data.success){
                //redirect to tasting note page
                //open wine_form
                child_url = "/vintage/tasting_note.php?vintage_id="+vintage_id;
                parent_url = this_page;
                //page_flow_set(child_url,parent_url,true);

            } else {
                console.log('Open full note RPC failed error = '+data.error);
            }
        }, "json");

    }

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
        toggle_vintage_panel($(this).attr('id'));
    });


    $('.btn_slide_vintage_details').click(function(){
        //toggle vintage details panel
        toggle_vintage_detail_panel($(this).attr('id'));
    });


    $(document).on('click','.wine_panel_toggle',function(){
        //toggle vintage panel

        var wine_id = ($(this).closest(".wine_accordian").attr('id').replace("wine_accordian_", ""));
        //close all child vintage detail panels
        $(this).closest(".wine_accordian").next('.vintages_panel').children('.vintage_accordian').children('.vintage_details').hide();
        //change all vintage expand / collapse indicators to closed (right arrow)
        $(this).closest(".wine_accordian").next('.vintages_panel').children('.vintage_accordian').find('.vintage_expanded_indicator').removeClass('arrow_down').addClass('arrow_right');
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

