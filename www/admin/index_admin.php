<?php
/*
 * Site details
 * Screen Size optimisation: 1280x760
 *
 */
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

echo "<!DOCTYPE html>";
echo "<html>";

echo "<head>";
    require_once("$root/includes/standard_html_head.inc.php");
    require_once("$root/includes/css.inc.php");
    echo "<title>Reference Data</title>";
    ?>

    <style type="text/css">




    </style>
</head>

<body>


    
    <div id="dialog_country" class="hidden" title="Add Country">
        <h2 style="margin-bottom:15px;" id="country_dialog_title" > Add Country</h2>
        <div class="input-main-label">
            <p>Country</p>
        </div>
        <div class="input-main">
            <input type="text" name="country_text" id="country_text" autocomplete="off"/>
            <input type="hidden" id="country_id" />
        </div>
        <div class="input-main-label">
            <p>Flag Image</p>
        </div>
        <div class="input-main">
            <input type="text" name="flag_file" id="flag_file" autocomplete="off"/>
            <input type="hidden" id="flag_file" />
        </div>
        <div class="clear" ></div>
    </div>
    
   
    <div id="dialog_region" class="hidden" title="Add Edit Region">
        <h2 style="margin-bottom:15px;" id="region_dialog_title" >Add Region</h2>
        <div class="input-main-label">
            <p>Region</p>
        </div>
        <div class="input-main">
            <input type="text" name="region_text" id="region_text" autocomplete="off"/>
            <input type="hidden" id="region_id" />
            <input type="hidden" id="region_country_id" />
        </div>
        <div class="clear" ></div>
    </div>
   
    
    <div id="dialog_subregion" class="hidden" title="Add Edit Subregion">
        <h2 style="margin-bottom:15px;" id="subregion_dialog_title" >Add Subregion</h2>
        <div class="input-main-label">
            <p>Subregion</p>
        </div>
        <div class="input-main">
            <input type="text" name="subregion_text" id="subregion_text" autocomplete="off"/>
            <input type="hidden" id="subregion_id" />
            <input type="hidden" id="subregion_region_id" />
        </div>
        <div class="clear" ></div>
    </div>
    
    
    <div id="dialog_merchant" class="hidden" title="Add Merchant?">
        <h2 style="margin-bottom:15px;" id="merchant_dialog_title" > Add New Merchant</h2>
        <div class="input-main-label">
            <p>Merchant</p>
        </div>
        <div class="input-main">
            <input type="text" name="merchant_text" id="merchant_text" autocomplete="off"/>
            <input type="hidden" id="merchant_id" size="32" class="text ui-widget-content ui-corner-all" />
        </div>
        <div class="clear" ></div>
    </div>
    
    
    <div id="dialog_producer" class="hidden" title="Add or Edit Producer">
        <h2 style="margin-bottom:15px;" id="producer_dialog_title" >Add Producer</h2>
        <div class="input-main-label">
            <p>Producer</p>
        </div>
        <div class="input-main">
            <input type="text" name="producer_text" id="producer_text" autocomplete="off"/>
            <input type="hidden" id="producer_id" />
        </div>
        <div class="clear" ></div>
    </div>
    
    
    <div id="dialog_grape" class="hidden" title="Add Edit Grape">
        <h2 style="margin-bottom:15px;" id="grape_dialog_title" > Add New Grape</h2>
       
        <div class="input-main-label">
            <p>Grape Name</p>
        </div>
        <div class="input-main">
           <input type="text" name="grape_text" id="grape_text" size="32" autocomplete="off"/>
           <input type="hidden" id="grape_id" />
        </div>
        <div class="input-main-label">
            <p>Colour</p>
        </div>
        <div class="input-main">
            <select name="grape_colour" id="grape_colour" style="width:100px;" >
                <option value="Red">Red
                <option value="White">White
            </select>
        </div>
        <div class="clear" ></div>
    </div>
    

    <div id="dialog_cannot_delete" class="hidden" title="Cannot Delete">
        <p>
            <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 30px 0;"></span>
            This object is associated with Wines and/or Vintages and cannot be deleted.
        </p>
    </div>
    

<div class="page_container">

    <?php require_once("$root/includes/nav/topheader.php"); ?>

    <div class="con_single_form" id="form_content" >
    
        <div id="tabs">
            <ul>
              <li><a href="#tabs-1">Locations</a></li>
              <li><a href="#tabs-2">Merchants</a></li>
              <li><a href="#tabs-3">Producers</a></li>
              <li><a href="#tabs-4">Grapes</a></li>
            </ul>
            <div id="tabs-1">
                <div style="float:left;" id="con_listBox_location" ><!- Loaded ("$root/admin/rpc_listBox_locations_html.php") -> </div>
                <div class="clear"></div>
            </div>
            <div id="tabs-2">
                <div style="float:left;" id="con_listBox_merchant" ><!- Loaded ("$root/admin/rpc_listBox_merchant_html.php") -> </div>
                <div class="clear"></div>
            </div>
            <div id="tabs-3">
                <div style="float:left;" id="con_listBox_producer" ><!- Loaded ("$root/admin/rpc_listBox_producer_html.php") -> </div>
                <div class="clear"></div>
            </div>
            <div id="tabs-4">
                <div style="float:left;" id="con_listBox_grape" ><!- Loaded ("$root/admin/rpc_listBox_grape_html.php") -> </div>
                <div class="clear"></div>
            </div>
      
        </div>
    

        <div class="con_button_bar" >
            <input type="button" id="btn_cancel" value="OK" >
            <input type="button" id="btn_test" value="Test" >
        </div>
           
        <div class="clear" ></div>
        
    </div> <!-- con_single_form -->
    
 </div> <!-- page_container -->



    
<!-- Pop-up Menus-->
<div id='main_menu' class="pop_up" style="width:225px; display:none; position:fixed; z-index:35;">
    <div class="ui-menu-item-first">Wines<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Wine<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div class="ui-menu-item-last">New Acquisition<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>

<?php
require_once("$root/includes/standard_dialogs.inc.php");
require_once("$root/includes/script_libraries.inc.php");
?>
<style>
    .ui-widget-header{
        background: none;
        border-width: 0px 0px 1px 0px;
        border-bottom-right-radius: 0px;
        border-bottom-left-radius: 0px;
    }
</style>
    
<script type="text/javascript" src="/libraries/jquery.ui.listBox.js"></script>
<script type="text/javascript">

//TODO: add notes input field to grapes and provide mechanism to view
//TODO: Add start page menu for ipad mobile devices
//TODO: Traverse up tree to open all containing elements when refreshing tree listbox
//TODO: Fix font sizes in list boxes, they are too big and vary across listboxes
//TODO: Filter box should resize to be similar height as a row in ListBox
//FIX: filter doesn't treat special characters as if they were the same without accents
//TODO: Remove test button
//TODO: Add butons to filter Grapes by colour

$(document).ready(function(){

    //page control object
    var this_page = "/admin/index_admin.php";
    var obj_page = new page_control({
        page_url: this_page, //set page url
        no_dirty: true
    });
    
    
    
    
    //*** Menus ***/
    
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
                    case 'Wines':
                        open_wines();
                        break;
                    case 'New Wine':
                        add_wine();
                        break;
                    case 'New Acquisition':
                        add_acquisition();
                        break;
                    default:
                        console.log('selected_item not recognised: '+selected_item);
                }
                break;

            default:
                console.log("menu_id not recognised: "+menu_id);
        }

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
    
    
    function open_wines(){
        //open Wines page
        obj_page.leave_page({
            dst_url: "/index.php",
            dst_action: 'open',
            page_action: 'leave'
        });
    }
    
    
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
    
    
    //_______ACTIONS and EVENTS_______
  
    
    //setup Producer listBox
    $("#con_listBox_producer").listBox({
        title: "Producer",
        width: 400,
        height: 700,
        listContent: '/admin/rpc_listBox_producer_html.php',
        showFilter: true,
        showTitle: false,
        showShadow: false,
        showRoundedCorners: false,
        addClass: 'listBox_large_theme',
        clickAdd: function(event, data){
            add_producer(data);
        },
        clickRemove: function(event, data){
            delete_producer(data.listBox_id, data.element);
        },
        clickEdit: function(event, data){
            edit_producer(data.listBox_id, data.listBox_values[0]);
        }
       
    });
        
    
    function add_producer(){
        //add producer
        console.log('add producer - open dialog');
        $('#producer_id').val(-1);
        $('#producer_text').val(null);
        //open dialog
        $("#producer_dialog_title" ).text( "Add Producer" ); //update dialog title
        $("#dialog_producer" ).dialog( "open" );
        $('#producer_text').focus();
        
    };
    

    //edit producer
    function edit_producer(index, value){
        if(index > 0){
            //fill form fields
            $('#producer_id').val(index);
            $('#producer_text').val(value);
            //open dialog
            $("#producer_dialog_title" ).text( "Edit Producer" ); //update dialog title
            $( "#dialog_producer" ).dialog( "open" );
            $('#producer_text').focus();
            
        } else {
            console.log('edit_producer - no index or value provided');
        }
        
    };
    
    
    //delete producer
    function delete_producer(index, element_id){
        //delete producer with provided index
        console.log('delete_producer='+index+' element='+element_id);
        
        if(index > 0){
            
            $.post("/admin/rpc_ref_data.php", {
                    action: 'delete_producer',
                    index: index
                }, function(data){
                    if(data.success){
                        var msg = 'Delete Producer successful';
                        console.log(msg);

                        //refresh listBox
                        $("#con_listBox_producer").listBox("refresh");
                        $("#con_listBox_producer").listBox("clearSelected");
                        
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "success",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    }else if(data.error == 'has_children'){
                        var msg = "Producer is associated with one or more Wines and cannot be deleted";
                        console.log(msg);
                        
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "warning",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    } else {
                        var msg = "Delete Producer failed with error: "+data.error;
                        console.log(msg);
                         $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                    }
               }, "json");
            
        } else {
            console.log('nothing selected')
        }
        
    };
    
    
    
    
    
    //Grape Listbox
    
    //setup grape listBox
    $("#con_listBox_grape").listBox({
        title: "Grape",
        width: 400,
        height: 700,
        listContent: '/admin/rpc_listBox_grape_html.php',
        showFilter: true,
        showTitle: false,
        showShadow: false,
        showRoundedCorners: false,
        addClass: 'listBox_large_theme',
        clickAdd: function(event, data){
            add_grape();
        },
        clickRemove: function(event, data){
            delete_grape(data.listBox_id, data.element);
        },
        clickEdit: function(event, data){
            edit_grape(data.listBox_id, data.listBox_values[0], data.listBox_values[1]);
        }
       
    });
    
    
    function add_grape(){
        console.log('open dialog - add_grape');
        //clear form validation
            //var validator = $("#frm_grape").validate();
            //validator.resetForm();
        //set hidden id field to -1 to identify as new
        $('#grape_id').val(-1);
        $('#grape_text').val(null);
        //open dialog
        $("#grape_dialog_title" ).text( "Add Grape" ); //update dialog title
        $("#dialog_grape" ).dialog( "open" );
        $('#grape_text').focus();

    };
    

    //edit grape
    function edit_grape(index, value, colour){
        if(index > 0){
            console.log("edit grape index="+index+" value="+value+' colour='+colour);
            //fill form fields
            $('#grape_id').val(index);
            $('#grape_text').val(value);
            $('#grape_colour').val(colour);
      
            //open dialog
            $("#grape_dialog_title" ).text( "Edit Grape" ); //update dialog title
            $( "#dialog_grape" ).dialog( "open" );
            $('#grape_text').focus();
            
        } else {
            console.log('edit_grape - incomplete parameters');
        }
        
    };
    
    
    function add_grape_db(){
        //add or update grape to db
        var def = $.Deferred();
        
        //get values
        var grape = $('#grape_text').val();
        var id = $("#grape_id").val();
        var colour = $("#grape_colour").val();
        console.log('grape value: '+grape+" grape_id: "+id+" colour: "+colour);

        //check is not blank
        if(!$.trim(grape)){
            msg = "Grape name cannot be blank";
            $(".ui-dialog-buttonset").notify(msg,{
                position: "top right",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
            return false;
        }
        
        //post to server
        $.post("/admin/rpc_ref_data.php", {
            action: 'save_grape_db',
            value: grape,
            id: id,
            colour: colour
        }, function(data){
            if(data.success){
                console.log('Grape save successful');
                var noun = ($("#grape_dialog_title" ).text() == "Edit Grape") ? "Update" : "Add"; //determine whether update or edit
                $("#con_listBox_grape").listBox("refresh", data.id); //refresh listbox
                msg = noun+" Grape successful";
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "success",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
        
                $("#dialog_grape").dialog( "close" ); //close dialog

            }else{
                var msg = data.error;
                console.log(msg);
                $(".ui-dialog-buttonset").notify(msg,{
                    position: "top right",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
                return;
            }
        }, "json");
    }
    
    
    
    function add_country_db(){
        //add or update country to db
        var def = $.Deferred();
        
        //get values
        var country_text = $('#country_text').val();
        var country_id = $("#country_id").val();
        var flag_file = $("#flag_file").val();
        console.log('country = '+country_text+' country_id = '+country_id + 'flag_file = '+flag_file);

        //check is not blank
        if(!$.trim(country_text)){
            msg = "Country name cannot be blank";
            $(".ui-dialog-buttonset").notify(msg,{
                position: "top right",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
            return false;
        }
        
        //post to server
        $.post("/admin/rpc_ref_data.php", {
            action: 'save_country_db',
            country_text: country_text,
            country_id: country_id,
            country_flag: flag_file
        }, function(data){
            if(data.success){
                console.log('Country save successful');
                var noun = ($("#country_dialog_title" ).text() == "Edit Country") ? "Update" : "Add"; //determine whether update or edit
                $("#con_listBox_location").listBox("refresh", data.id); //refresh listbox
                msg = noun+" Country successful";
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "success",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
        
                $("#dialog_country").dialog( "close" ); //close dialog

            }else{
                var msg = data.error;
                console.log(msg);
                $(".ui-dialog-buttonset").notify(msg,{
                    position: "top right",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
                return;
            }
        }, "json");
    }
    
    
    function add_region_db(){
        //add or update region to db

        //get values
        var region_text = $("#region_text").val();
        var region_id = $("#region_id").val();
        var country_id = $("#region_country_id").val();
        console.log('country_id = '+country_id+' region_text = '+region_text+' region_id = '+region_id);

        //check is not blank
        if(!$.trim(region_text)){
            msg = "Region name cannot be blank";
            $(".ui-dialog-buttonset").notify(msg,{
                position: "top right",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
            return false;
        }
        
        //post to server
        $.post("/admin/rpc_ref_data.php", {
            action: 'save_region_db',
            region_text: region_text,
            country_id: country_id,
            region_id: region_id
        }, function(data){
            if(data.success){
                console.log('Region save successful');
                var noun = ($("#region_dialog_title" ).text() == "Edit Region") ? "Update" : "Add"; //determine whether update or edit
                $("#con_listBox_location").listBox("refresh", data.id); //refresh listbox
                msg = noun+" Region successful";
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "success",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
        
                $("#dialog_region").dialog( "close" ); //close dialog

            }else{
                var msg = data.error;
                console.log(msg);
                $(".ui-dialog-buttonset").notify(msg,{
                    position: "top right",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
                return;
            }
        }, "json");
    }
    
    
    
    function add_subregion_db(){
        //add or update subregion to db

        //get values
        var subregion = $('#subregion_text').val();
        var subregion_id = $("#subregion_id").val();
        var region_id = $("#subregion_region_id").val();
        console.log('subregion: '+subregion+' subregion_id: '+subregion_id+' region_id:'+region_id);

        //check is not blank
        if(!$.trim(subregion)){
            msg = "Subregion name cannot be blank";
            $(".ui-dialog-buttonset").notify(msg,{
                position: "top right",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
            return false;
        }
        
        //post to server
        $.post("/admin/rpc_ref_data.php", {
            action: 'save_subregion_db',
            subregion_text: subregion,
            region_id: region_id,
            subregion_id: subregion_id
        }, function(data){
            if(data.success){
                console.log('Subregion save successful');
                var noun = ($("#subregion_dialog_title" ).text() == "Edit Subregion") ? "Update" : "Add"; //determine whether update or edit
                $("#con_listBox_location").listBox("refresh", data.id); //refresh listbox
                msg = noun+" Subregion successful";
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "success",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
        
                $("#dialog_subregion").dialog( "close" ); //close dialog

            }else{
                var msg = data.error;
                console.log(msg);
                $(".ui-dialog-buttonset").notify(msg,{
                    position: "top right",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
                return;
            }
        }, "json");
    }
    
    
    function add_producer_db(){
        //add or update pproducer to db

        //get values
        var value = $('#producer_text').val();
        var id = $("#producer_id").val();
        console.log('Producer: '+value+" id: "+id);

        //check is not blank
        if(!$.trim(value)){
            msg = "Producer name cannot be blank";
            $(".ui-dialog-buttonset").notify(msg,{
                position: "top right",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
            return false;
        }
        
        //post to server
        $.post("/admin/rpc_ref_data.php", {
            action: 'save_producer_db',
            value: value,
            id: id
        }, function(data){
            if(data.success){
                console.log('Producer save successful');
                var noun = ($("#producer_dialog_title" ).text() == "Edit Producer") ? "Update" : "Add"; //determine whether update or edit
                $("#con_listBox_producer").listBox("refresh", data.id); //refresh listbox
                msg = noun+" Producer successful";
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "success",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
        
                $("#dialog_producer").dialog( "close" ); //close dialog

            }else{
                var msg = data.error;
                console.log(msg);
                $(".ui-dialog-buttonset").notify(msg,{
                    position: "top right",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
                return;
            }
        }, "json");
    }
    
    
    //delete grape
    function delete_grape(index, element_id){
        //delete grape with provided index
        console.log('delete_grape ='+index);
        
        if(index > 0){
            
            $.post("/admin/rpc_ref_data.php", {
                    action: 'delete_grape',
                    index: index
                }, function(data){
                    if(data.success){
                        console.log('delete grape successful');
                        
                        //refresh
                        $("#con_listBox_grape").listBox("refresh");
                        $("#con_listBox_grape").listBox("clearSelected");
                        
                        msg = "Delete Grape successful";
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "success",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    }else if(data.error == 'has_children'){
                        msg = "Grape is associated with one or more Wines so cannot be deleted";
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "warning",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );  
                    } else {
                        var msg = "Delete Grape failed with error: "+data.error;
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                    }
               }, "json");
            
        } else {
            console.log('nothing selected');
        }
        
    };
    


    //Location Listbox
    
    //setup Location listBox
    $("#con_listBox_location").listBox({
        title: "Location",
        width: 400,
        height: 700,
        listContent: '/admin/rpc_listBox_location_html.php',
        showTitle: false,
        showFilter: true,
        showBorder: true,
        showShadow: false,
        showRoundedCorners: false,
        addClass: 'listBox_large_theme',
        clickAdd: function(event, data){
            add_location(data);
        },
        clickRemove: function(event, data){
            delete_location(data);
        },
        clickEdit: function(event, data){
            edit_location(data);
        }
       
    });
    
    
    $(document).on('click','#btn_cancel',function(){
        //close form
        close_page();
    });
    
    
    $(document).on('click','#btn_refresh',function(){
        //refresh producer lisbox
        $("#con_listBox_location").listBox('persist');
    });
    
    
    
    function close_page(){
        obj_page.leave_page({
            page_action: 'close'
        });
    }
    
    
    function add_location(data){
        //determine what type of record is being added
        var id = data.listBox_id;
        var parent_id = data.listBox_parent_id;
        var level = data.listBox_level;
        
        console.log("add_location data: data.id="+id+" parent_id="+parent_id+" parent_type="+level);
        
        
        if(typeof parent_type == 'undefined'){
            //add country
            parent_type = 0;
        } 
            
        //determine 'what to add
        switch(level){
            case 1:
                console.log('add Region');
                add_region(id);
                break;
            case 2:
                console.log('add Subregion');
                add_subregion(id);
                break;
            case 3:
                console.log('Cannot add child to subregion');
                break;
            default:
            console.log('add Country');
            add_country();
        }
    };


    
   
    function edit_location(data){
        //listbox edit
        var id = data.listBox_id;
        var parent_id = data.listBox_parent_id;
        var level = data.listBox_level;
        var value = data.listBox_values[0];
        
        if(typeof level == 'undefined'){
            //nothing selected
            level = 0;
        }
        
        
        //determine 'what to edit
        switch(level){
            case 1:
            console.log('edit Country');
            edit_country(id, data);
            break;
            case 2:
            console.log('edit Region');
            edit_region(id, value, parent_id);
            break;
            case 3:
            console.log('edit subRegion');
            edit_subregion(id, value, parent_id);
            break;
            default:
            //code to be executed if n is different from case 1 and 2
            console.log('nothing selected');
        }
    };
    
    
       
    function delete_location(data){
        //listbox delete location
        
        var level = data.listBox_level;
        var id = data.listBox_id;
        var value = data.listBox_values[0];
        
        if(typeof level == 'undefined'){
            level = 0;//nothing selected
        }
        
        //check if row has children
        if($("#con_listBox_location").listBox("hasChildren")){
            msg = "Location cannot be Deleted whilst is has child locations";
            console.log(msg);
            $(".con_button_bar").notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );              
            
            return false;
            
        }
        
        //determine 'what to edit
        switch(level){
            case 1:
            console.log('delete Country');
            delete_country(id, value);
            break;
            case 2:
            console.log('delete Region');
            delete_region(id, value);
            break;
            case 3:
            console.log('delete subRegion');
            delete_subregion(id, value);
            break;
            default:
            console.log('nothing selected');
        }
        
    }
    
    
  
    
    //Merchant Listbox
    
   
    //setup Merchant listBox
    $("#con_listBox_merchant").listBox({
        title: "Merchant",
        width: 400,
        height: 700,
        listContent: '/admin/rpc_listBox_merchant_html.php',
        showFilter: true,
        showTitle: false,
        showBorder: true,
        showShadow: false,
        showRoundedCorners: false,
        addClass: 'listBox_large_theme',
        clickAdd: function(event, data){
            add_merchant();
        },
        clickRemove: function(event, data){
            delete_merchant(data.listBox_id);
        },
        clickEdit: function(event, data){
            edit_merchant(data.listBox_id, data.listBox_values[0]);
        }
       
    });
    
    
    function add_merchant(){
        //add merchant
        console.log('add_merchant - open dialog');

        //set hidden id field to neg to identify as new
        $('#merchant_id').val(-1);
        $('#merchant_text').val(null);
        
        //open dialog
        $("#dialog_merchant" ).dialog( "open" );
        $('#merchant_text').focus();
        $("#merchant_dialog_title" ).text( "Add Merchant" ); //update dialog title
        
    };
    
    
    function add_merchant_db(){
        //add merchant to db
        
        var def = $.Deferred();
        
        //get values from form
        var merchant = $('#merchant_text').val();
        var id = $("#merchant_id").val();

        //check is not blank
        if(!$.trim(merchant)){
            msg = "Merchant name cannot be blank";
            $(".ui-dialog-buttonset").notify(msg,{
                position: "top right",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
            return false;
        }
        
        //post to server
        $.post("/admin/rpc_ref_data.php", {
            action: 'save_merchant_db',
            merchant: merchant,
            merchant_id: id
        }, function(data){
            if(data.success){
                console.log('Merchant save successful');
                var noun = ($("#merchant_dialog_title" ).text() == "Edit Merchant") ? "Update" : "Add"; //determine whether update or edit
                $("#con_listBox_merchant").listBox("refresh", data.id); //refresh listbox
                msg = noun+" Merchant successful";
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "success",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
        
                $("#dialog_merchant").dialog( "close" ); //close dialog

            }else{
                var msg = data.error;
                console.log(msg);
                $(".ui-dialog-buttonset").notify(msg,{
                    position: "top right",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );
            }
        }, "json");

    }
    
    
    $("#dialog_merchant").dialog({
        autoOpen: false,
        height: 205,
        width: 360,
        modal: true,
        buttons: {
                "OK": function() {
                    add_merchant_db();
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
        },
        close: function() {
               $('#merchant_text').val(""); //clear dialog
        },
        dialogClass: "clean-dialog",
        position: { my: "left bottom", at: "left top", of: '#con_listBox_merchant_footer_buttons' }  
    });


    function edit_merchant(index, value){
        //edit merchant
        if(index > 0){
            console.log("edit merchant index="+index+" value="+value);
            //fill form fields
            $('#merchant_id').val(index);
            $('#merchant_text').val(value);

            $("#dialog_merchant" ).dialog( "open" );
            $('#merchant_text').focus();
            $("#merchant_dialog_title" ).text( "Edit Merchant" ); //update dialog title

        } else {
            console.log('edit_merchant - no index or value provided');
        }
        
    };
    
    
    function delete_merchant(index){
        //delete merchant with provided index
        console.log('delete_merchant ='+index);
        
        if(index > 0){
            
            $.post("/admin/rpc_ref_data.php", {
                    action: 'delete_merchant',
                    index: index
                }, function(data){
                    if(data.success){
                        var msg = 'Delete Merchant successful';
                        console.log(msg);
                        $("#con_listBox_merchant").listBox("refresh"); //refesh listbox
                        $("#con_listBox_merchant").listBox("clearSelected");
                        
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "success",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );

                    }else if(data.error == 'has_children'){
  
                        var msg = "Merchant is associated with one or more Acquisitions so cannot be deleted";
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "warning",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                        
                    } else {
                        var msg = 'Delete Merchant failed with error: '+data.error;
                        console.log(msg);
                        jQuery.noticeAdd({
                            text: msg,
                            stay: true,
                            type: 'notice-error'
                        });
                        
                    }
               }, "json");
            
        } else {
            console.log('delete merchant - no index provided');
        }
        
    };

    


    //add country
    function add_country(){
        console.log('open dialog - add_country');
        //set hidden id field to neg to identify as new
        $('#country_id').val(-1);
        $('#country_text').val(null);
        $('#flag_file').val(null);
        //open dialog
        $("#country_dialog_title" ).text( "Add Country" ); //update dialog title
        $("#dialog_country" ).dialog( "open" );
        $('#country_text').focus();
        
    };
    
    
    //edit country
    function edit_country(index, data){
        if(index > 0){
            console.log("edit country index="+index);
            var country = data.listBox_values[0];
            var flag_file = data.listBox_values[2];
            //fill form fields
            $('#country_id').val(index);
            $('#country_text').val(country);
            $('#flag_file').val(flag_file);
            
            $("#country_dialog_title" ).text( "Edit Country" ); //update dialog title
            $("#dialog_country" ).dialog( "open" );
            $('#country_text').focus();
            
        } else {
            console.log('edit_county - incomplete parameters');
        }
        
    };
    
    
    //delete country - level_1
    function delete_country(index){
        //delete country with provided index
        console.log('delete_country ='+index);
        
        if(index > 0){
            
            $.post("/admin/rpc_ref_data.php", {
                    action: 'delete_country',
                    country_id: index
                }, function(data){
                    if(data.success){
                        var msg = "Delete Country successful";
                        console.log(msg);
                        
                        //refresh listBox
                        $("#con_listBox_location").listBox("refresh");
                        $("#con_listBox_location").listBox("clearSelected");
                        
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "success",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                
                    }else if(data.error === 'has_children'){
                        var msg = "Country cannot be deleted whilst it contains Regions";
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "warning",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    } else {
                        var msg = "Delete Country failed with error: "+data.error;
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    }
               }, "json");
            
        } else {
            console.log('nothing selected');
        }
        
    };
    
    
     //add region
    function add_region(parent_index){
        console.log('add_region parent_index='+parent_index);
        $('#region_id').val(-1);//set hidden id field to neg to identify as new
        $('#region_country_id').val(parent_index);
        $('#region_text').val();
        //open dialog
        $("#region_dialog_title" ).text( "Add Region" ); //update dialog title
        $("#dialog_region" ).dialog( "open" );
        $('#region_text').focus();
        
    };
    
 
   
    //edit region - level_2
    function edit_region(index, value, parent_index){
        console.log('edit_region index='+index);
        if(index > 0){
            //fill form fields
            $('#region_id').val(index);
            $('#region_text').val(value);
            $('#region_country_id').val(parent_index);
            
            $("#region_dialog_title" ).text( "Edit Region" ); //update dialog title
            $( "#dialog_region" ).dialog( "open" );
            $('#region_text').focus();
            
        } else {
            console.log('edit_region - incomplete parameters');
        }
        
    };
 

    //delete region - level_2
    function delete_region(index){
        //delete region with provided index
        
        if(index > 0){
            $.post("/admin/rpc_ref_data.php", {
                    action: 'delete_region',
                    region_id: index
                }, function(data){
                    if(data.success){
                        var msg = "Delete Region successful";
                        console.log(msg);
                        
                        $('#con_listBox_location').listBox("refresh");
                        $("#con_listBox_grape").listBox("clearSelected");
                        
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "success",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    }else if(data.error == 'has_children'){
                        var msg = 'Cannot delete Region because it has children';
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "warning",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    } else {
                        var msg = "Delete Region failed with error: "+data.error;
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    }
               }, "json");
            
        } else {
            console.log('delete_region - incomplete parameters');
        }
        
    };
    
    

    //add subregion - level_3
    function add_subregion(parent_index){
        console.log('add_subregion parent_index='+parent_index);
        $('#subregion_id').val(-1);//set hidden id field to neg to identify as new
        $('#subregion_region_id').val(parent_index);
        $('#subregion_text').val();
        //open dialog
        $("#subregion_dialog_title" ).text( "Add Subregion" ); //update dialog title
        $("#dialog_subregion" ).dialog( "open" );
        $('#subregion_text').focus();

    };



     //edit subregion - level_3
    function edit_subregion(index, value, parent_index){
        console.log('edit_subregion='+index);
        if(index > 0){
            //fill form fields
            $('#subregion_id').val(index);
            $('#subregion_text').val(value);
            $('#subregion_region_id').val(parent_index);

            //open dialog
            $("#subregion_dialog_title" ).text( "Edit Subregion" ); //update dialog title
            $("#dialog_subregion").dialog( "open" );
            $('#subregion_text').focus();

        } else {
            console.log('nothing selected');
        }

    };
 
    
    //delete region - level_2
    function delete_subregion(index){
        //delete region with provided index
        
        if(index > 0){
            $.post("/admin/rpc_ref_data.php", {
                    action: 'delete_subregion',
                    subregion_id: index
                }, function(data){
                    if(data.success){
                        var msg = "Delete Subregion successful";
                        console.log(msg);

                        $('#con_listBox_location').listBox("refresh");
                        $("#con_listBox_grape").listBox("clearSelected");
                       
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "success",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    }else if(data.error == 'has_children'){
                        var msg = 'Subregion is associated with one or more Wines so cannot be deleted';
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "warning",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );
                        
                    } else {
                        var msg = "Delete Subregion failed with error: "+data.error;
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false,
                            autoHideDelay: 3000
                            }
                        );

                    }
               }, "json");
            
        } else {
            console.log('delete_subregion - incomplete parameters');
        }
        
    };
    
    
    
    //_____FORMS and DIALOGS_____
    
  
    $( "#dialog_country" ).dialog({
        autoOpen: false,
        height: 275,
        width: 400,
        modal: true,
        buttons: {
            OK: function(){
                add_country_db();
            },
            Cancel: function() {
                $(this).dialog( "close" );
            }
        },
        close: function() {
               $('#country_text').val(null);
        },
        dialogClass: "clean-dialog",
        position: { my: "left bottom", at: "left top", of: '#con_listBox_location_footer_buttons' }  
    });

    
    
    $("#dialog_region").dialog({
        autoOpen: false,
        height: 205,
        width: 400,
        modal: true,
        buttons: {
            OK: function() {
                add_region_db();
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
               $('#region_text').val("");
        },
        dialogClass: "clean-dialog",
        position: { my: "left bottom", at: "left top", of: '#con_listBox_location_footer_buttons' }  
    });
    


    $("#dialog_subregion").dialog({
        autoOpen: false,
        height: 205,
        width: 400,
        modal: true,
        buttons: {
            OK: function() {
                add_subregion_db();
            },
            Cancel: function() {
                $(this).dialog( "close" );
            }
        },
        close: function() {
            $('#subregion_text').val("");
        },
        dialogClass: "clean-dialog",
        position: { my: "left bottom", at: "left top", of: '#con_listBox_location_footer_buttons' } 
    });
    
    
    $("#dialog_producer").dialog({
        autoOpen: false,
        height: 205,
        width: 400,
        modal: true,
        buttons: {
            OK: function() {
                add_producer_db();
            },
            Cancel: function() {
                $(this).dialog( "close" );
            }
        },
        close: function() {
            $('#producer_text').val("");
        },
        dialogClass: "clean-dialog",
        position: { my: "left bottom", at: "left top", of: '#con_listBox_producer_footer_buttons' } 
    });


    $("#dialog_grape").dialog({
        autoOpen: false,
        height: 265,
        width: 400,
        modal: true,
        buttons: {
                OK: function() {
                    add_grape_db();
                },

                Cancel: function() {
                    $(this).dialog( "close" );
                }
        },
        close: function() {
            $('#grape_text').val("");
        },
        dialogClass: "clean-dialog",
        position: { my: "left bottom", at: "left top", of: '#con_listBox_grape_footer_buttons' }  
    });




    //initialise
    $( "#tabs" ).tabs(); //create tabs
    
  

}); //document.ready



</script>

</body>
</html>

