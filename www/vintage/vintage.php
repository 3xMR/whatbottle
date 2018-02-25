<?php
/*Master Vintage form for new and editing existing Vintages*/
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";

require_once("$root/includes/standard_html_head.inc.php");
require_once("$root/includes/css.inc.php");//include style sheets

echo "<title>Vintage - What Bottle?</title>";
echo "</head>";
?>

<body>

    <div id="dialog-producer" class="hidden" title="Add New Producer">
            <p>
                <span class="ui-icon ui-icon-alert" style="float:left; margin:0 10px 10px 0;"></span>
                Do you wish to add this producer as a new producer?
            </p>
            <p>
                <b>OK</b> - to add new producer<br/>
                <b>Cancel</b> - to return to page
            </p>

    </div>

    
    <div id="dialog-unique-wine" class="hidden" title="Warning - Duplicate Wine">
            <p>
                    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 10px 10px 0;"></span>
                    Duplicate Wine - A wine with this name, wine type and producer already exists!
            </p>
            <p>
                <b>OK</b> - to continue and create a duplicate wine<br/>
                <b>Cancel</b> - to return to page and make changes
            </p>

    </div>

    <div id="dialog-delete" class="hidden" title="Delete Vintage">
            <div style="float:left; display:inline-block; margin-top:5px; margin-bottom:10px;">
                <img src="/images/warning_flat_orange_32.png" width="32px" height="32px">
            </div>
            <div style="float:left; margin-left:15px; margin-bottom:10px; width:320px; line-height:150%;">
                <h2>Are you sure you want to delete this Vintage?</h2>
            </div>
            <div style="float:left;">
                <p style="color:grey;">All related data will be lost, including associated awards, tasting notes and images</p>
            </div>
    </div>

    
    <div id="dialog-has-vintages" class="hidden" title="Delete Wine">
            <p>
                    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 30px 0;"></span>
                    You must delete the Vintages for this Wine before you can delete the Wine
            </p>

    </div>

    
     <div id="dialog_select_grapes" class="hidden">
        <?php

        echo "<div style=\"width:300px;\" id=\"select_grape_container\">";
        echo "</div>";
        ?>

    </div>

<?php

require_once("$root/includes/standard_dialogs.inc.php");


//page
echo "<div class=\"page_container\">";

    //header
    require_once("$root/includes/nav/topheader.php");

    //wine_form
    echo "<div class=\"con_single_form rounded\" >";
        
        //Title bar
        echo "<div class=\"con_title_bar\" >";
            //wine name
            echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px;\" >";
                echo "<div style=\"float:left; width:48px; margin-right:10px;\" >";
                    echo "<img src=\"/images/vintage_flat_grey_64.png\" height=\"48px\" width=\"48px\" >";
                echo "</div>";
                echo "<div style=\"width:auto; float:none; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >".$_SESSION['var_vintage_temp']['wine'].", ".$_SESSION['var_vintage_temp']['producer']."</h1>";
                    echo "<h3 style=\"color:darkgrey;\">".$_SESSION['var_vintage_temp']['country'].", ".$_SESSION['var_vintage_temp']['region'];
                    if($_SESSION['var_vintage_temp']['subregion']){
                        echo ", ".$_SESSION['var_vintage_temp']['subregion'];
                    }
                    echo "</h3>";
                echo "</div>";
                echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left;\"  >";
                    echo "<img id=\"process_indicator\" src=\"/images/ajax_loader.gif\" height=\"24px\" width=\"24px\" />";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
            echo "</div>";
        echo "</div>"; //con_title_bar
        
        //Left Column
        echo "<div id=\"vintage_form_content\" >";
            //filled by jquery load method - rpc_vintage_form_html.php
        echo "</div>";
        
        //Button Bar
        echo "<div class=\"con_button_bar\" >";
            if( is_authed() ){
                echo "<input type=\"button\" id=\"btn_save\" value=\"Save\" />";
                echo "<input type=\"button\" id=\"btn_delete\" value=\"Delete\" />";
            }  
            //echo "<input type=\"button\" id=\"btn_add_note\" value=\"Add Note\" />";
            echo "<input type=\"button\" id=\"btn_close\" value=\"Close\" class=\"btn_close\" />";
        echo "</div>";

        //clear page_container
        echo "<div class=\"clear\"></div>";
    
    echo "</div>";


echo "</div>"; //page_container
    
//include all script libraries
require_once("$root/includes/script_libraries.inc.php"); ?>

<!-- Pop-up Menus-->
<div id='main_menu' class="pop_up" style="width:200px; display:none; position:fixed; z-index:35;">
    <div class="ui-menu-item-first">New Note<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Wine<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Acquisition<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>Wines<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div class="ui-menu-item-last">Reference Data<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>
    
    
<script type="text/javascript" src="/libraries/jquery.page_control_1.4.js"></script>
<script type="text/javascript">


$(document).ready(function(){



    /*Notes:
     * - jquery.page_control - allows easy pass back to parent page, handles dirty, leaving page without saving, and redirect
     * - json2.js - serialize input fields and pass back to server to save to session and commit to db - requires library
     * - common dialogs are in seperate include file
     * - jquery.notice.js provides growl type notifications
     * - use hidden field "is_dirty" and session [var_vintage_temp][is_dirty] to persist is_dirty between child forms
     * - vintage_form_status = '1'create '2'read '3'write '4'delete
     */


  
    //TODO: Make grapes select a modal form based on listbox
    //TODO: Hide Vintage listbox until page has loaded
    //TODO: Add ability to navigate to page with url for links
    //FIX: Add image on new vintage then add note, when returning to vintage image is missing on vintage page but has been saved, likely relates to clean-up script on image manager page

    



    //____Global variables____

    var form_status = 0;
    var vintage_id = 0;
    var this_page = "/vintage/vintage.php"; //self identification for page.control
 
    //page control object
    var obj_page = new page_control({
        save_function: function(){
            return save_page();  
        },
        save_session: function(data){
            return save_to_session();
        },
        page_url: this_page //set page url
        
    });
    

    //get form status
    console.log('get_vintage_form_session');

    //get vintage details from session();
    get_vintage_session();


    //____functions____

    function load_vintage_html(){
       //load content html from remote script
       $('#vintage_form_content').load('/vintage/rpc_vintage_form_html.php', function(){
           console.log('vintage form html loaded');
           initialise_page();
           refresh_all_notes();
       });
    }


    function load_grape_html(){
       //load content html from remote script
       $('#select_grape_container').load('/admin/rpc_grape_html.php', function(){
           console.log('grape html loaded');
       });
    }


    function initialise_page(){
        //special function to intialise page after container html has loaded

        //show ajax activity
        $('#process_indicator')
        .hide()  // hide it initially
        .ajaxStart(function() {
            $(this).show();
        })
        .ajaxStop(function() {
            $(this).hide();
        });
              
        //setup form validation
        set_validation();

        //update controls
        var vintage_id  = $("#vintage_id").val();
        if(vintage_id){
            //enable add note button - as vintage has been saved
            $("#btn_add_note").removeAttr('disabled').show();
        }else{
            //new vintage
            $('#year').focus();//set focus
            $("#btn_add_note").attr('disabled', true);
        }


    }


    function get_vintage_session(callback){
        //get session variables to page

        console.log('function: get_vintage_session');
        //spinner();
        
        $.post("/vintage/rpc_vintage.php", {
            action: 'get_vintage_session'
        }, function(data){
            if(data.success){
                console.log('get_vintage_session OK');
                
                if(data.wine_id == undefined || !data.wine_id > 0){
                    //no wine_id - redirect page after deleted
                    obj_page.close_page();
                }

                //load html content
                load_vintage_html();

                console.log('vintage_id='+data.vintage_id+" wine_id="+data.wine_id);
                console.log('session is_dirty='+data.is_dirty);

                if(callback){
                    //call callback if provided
                    console.log('fnc: get_vintage_session - callback');
                    callback(data);
                };

            }else{
                console.log('get_vintage_session FAILED');
                alert('critical error trying to retrieve vintage from session ');
                obj_page.close_page();
            }
        }, "json");

    }


    function save_to_session(url){
        //save to session before opening url if provided
        console.log('save_to_session');
        var wine_id = $("#wine_id").val();
        var vintage_id = $("#vintage_id").val();
        var url = url;
        
        if(wine_id>0){
            //save to session
            var var_fields = $(":input").serializeArray();
            var json_array = JSON.stringify(var_fields);
            console.log('vintage form data:');
            console.log(json_array);

            var save_session = $.post("/vintage/rpc_vintage.php", {
                json_values: json_array,
                action: 'put_to_session'
                },
                function(data){
                    if(data.success){
                        console.log('save_to_session successful');
                        //diable warning before leaving page
                        //obj_page.leave_page = true;
                        //set page flow and redirect to provided url
                        //var open_url = url;
                        //var parent_url = this_page;
                        //page_flow_set(open_url, parent_url, true);
                        //open_grapes(vintage_id);
                    }else{
                        console.log('save to session failed');
                        console.log(data);
                    }

            }, "json");
            
        } else {
            console.log('cannot save_to_session no wine_id provided');
        }
        
        
    }

    
    function serialize_form(){
        //serialize form data and return as array
        
        console.log('serialize form');
        var wine_id = $("#wine_id").val();
        var vintage_id = $("#vintage_id").val();
 
        if(wine_id>0){
            //save to session
            var var_fields = $(":input").serializeArray();
            var json_array = JSON.stringify(var_fields);
            console.log('vintage form data serialized:');
            console.log(json_array);
            return json_array;
        } else {
            console.log('cannot serialize_data as no wine_id exists');
            return false;
        }
        
    }
    
    
    function open_grapes(){
        //open grapes form
        
        //serialize form data to pass as array to page object
        var form_data = serialize_form();
        
        var vintage_id = $("#vintage_id").val();
        
        obj_page.leave_page(
        {
        dst_url:        "/vintage/select_grapes.php",
        rtn_url:        this_page,
        page_action:    'leave',
        dst_type:       "grapes",
        dst_action:     "open",
        object_id:      0,
        parent_id:      vintage_id,
        child:          true,
        form_action:    "put_to_session",
        form_dest:      "/vintage/rpc_vintage.php",
        form_data:      form_data
        });
       
    };
    
    
    function open_awards(){
        //open grapes form
        
        //serialize form data to pass as array to page object
        var form_data = serialize_form();
        
        var vintage_id = $("#vintage_id").val();
        
        obj_page.leave_page({
            dst_url:        "/vintage/select_awards.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "awards",
            dst_action:     "open",
            object_id:      0,
            parent_id:      vintage_id,
            child:          true,
            form_action:    "put_to_session",
            form_dest:      "/vintage/rpc_vintage.php",
            form_data:      form_data
        });
       
    };


    function edit_image(url){
        //edit image
        console.log('fnc: edit_image');

        $.post("/vintage/rpc_vintage.php", {
                action: 'put_image_vintage'
                },
                function(data){
                    if(data.success){
                        console.log('put_image_edit OK');
                        console.log(url);
                        obj_page.set_is_dirty(true);
                        save_to_session(url);//save to temp and then redirect to provided url
                    } else {
                        console.log('put_image_edit FAILED');
                    }

         }, "json");

    }
    
    
    function open_image_manager(){
        //open image manager
        
        var form_data = serialize_form();
        var vintage_id = $("#vintage_id").val();
        
        obj_page.leave_page({
            dst_url:        "/vintage/select_image.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "image",
            dst_action:     "open",
            parent_id:      vintage_id,
            child:          true,
            form_action:    "put_to_session",
            form_dest:      "/vintage/rpc_vintage.php",
            form_data:      form_data
        });
        
    }
    
    
    function escape (key, val) {
        if (typeof(val)!= "string") return val;
        return val
          .replace(/[\"]/g, '\\"')
          .replace(/[\\]/g, '\\\\')
          .replace(/[\/]/g, '\\/')
          .replace(/[\b]/g, '\\b')
          .replace(/[\f]/g, '\\f')
          .replace(/[\n]/g, '\\n')
          .replace(/[\r]/g, '\\r')
          .replace(/[\t]/g, '\\t')
        ; 
    }


    function save_page(callback){
        //save page function
        
        var def = new jQuery.Deferred();

        console.log('save vintage form...');
        $("#frm_vintage").validate();//validate form

        if($("#frm_vintage").valid()){
            console.log('form validation OK - continue to save');
            //serialize field data and pass server to save to db
            var var_fields = $(":input").serializeArray();
            var json_array = JSON.stringify(var_fields,escape); //escape function handles escaping characters
            console.log(json_array);

            //save to db
            $.post("/vintage/rpc_vintage.php", {
                action: 'put_vintage_to_db',
                json_values: json_array
                },
                function(data){
                    if(data.success){
                        console.log('commit to db OK');
                        console.log(data.msg);

                        //display success message
                        $(".con_button_bar").notify("Save Successful",{
                            position: "top left",
                            style: "msg",
                            className: "success",
                            arrowShow: false
                            }
                        );

                        console.log('db_action='+data.db_action+" vintage_id="+data.vintage_id);

                        //show add_note button
                        $("#btn_add_note").removeAttr('disabled').show();

                        if(data.db_action === 'insert' && data.vintage_id>0){
                            //update vintage_id
                            $('#vintage_id').val(data.vintage_id);
                            
                            //add to basket
                            console.log('add new vintage to basket');
                            add_vintage_basket(data.vintage_id); //jquery.basket.js
                            
                        }

                        //reset is_dirty
                        obj_page.set_is_dirty(false);

                        if(typeof callback === 'function'){
                            callback();
                        }
                        
                        def.resolve(true);
   

                    } else {
                        $('#vintage_id').val(data.vintage_id); //update vintage_id if provided
                        var msg = 'Failed to save Vintage error: '+data.error;
                        console.log(msg);
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false
                            }
                        );
                        
                        def.resolve(false);

                    }

         }, "json");


        }else{
            console.log('form validation FAILED');
        }
        
        return def.promise();

    };
    
    
    function refresh_all_notes(){
        
        $("#con_all_notes").listBox({
            title: "Tasting Notes",
            width: 400,
            height: 200,
            showFilter: false,
            showBorder: true,
            showShadow: false,
            showRoundedCorners: false,
            showTitle: false,
            addClass: 'listBox_flat_theme',
            listContent: '/vintage/rpc_all_notes_html.php?page=vintage',
            clickEdit: function(event, data){
                var vintage_id = $("#vintage_id").val();
                var note_id = data.listBox_id;
                console.log('Edit Note  selected in listBox id: '+note_id);
                if(vintage_id && note_id){
                    open_note(note_id,vintage_id);
                }
            },            
            clickAdd: function(event, data){
                var vintage_id = $("#vintage_id").val();
                console.log('Add Note');
                if(vintage_id){
                    add_note(vintage_id);
                }
            },
            clickSelected: function(event, data){
                note_id = data.listBox_id;
                console.log('Note selected in listBox note_id: '+note_id);
            }
        });
        
        
        $("#con_all_acquisitions").listBox({
            title: "Acquisitions",
            width: 400,
            height: 200,
            showFilter: false,
            showBorder: true,
            showShadow: false,
            showRoundedCorners: false,
            showTitle: false,
            addClass: 'listBox_flat_theme',
            listContent: '/vintage/rpc_listBox_acquisitions_html.php',
            clickSelected: function(event, data){
                acquire_id = data.listBox_id;
                console.log('Acquisition selected in listBox id: '+acquire_id);
            },
            clickEdit: function(event, data){
                acquire_id = data.listBox_id;
                console.log('Edit acquisition selected in listBox id: '+acquire_id);
                if(acquire_id){
                    open_acquisition(acquire_id);
                }
            },
            clickAdd: function(event, data){
                var vintage_id = $("#vintage_id").val();
                console.log('Add vintage to basket vintage_id: '+vintage_id);
                if(vintage_id){
                    add_vintage_basket(vintage_id);
                }
            }
        });
        
    
    };
    
    
    function open_acquisition(acquire_id){
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


 //________Actions__________


    //edit grapes
    $(document).on('click','#edit_grapes',function(){
        //open grapes form to modify grapes
        console.log('edit_grapes');
        open_grapes();
 
    });


    $(document).on('click','#btn_edit_image, #image_con',function(){
        //save to session and then open page
        open_image_manager();
    });


    //edit awards
    $(document).on('click','#btn_edit_awards',function(){
        console.log('edit awards click');
        open_awards();
    });


    $(document).on('click',"#btn_save",function(){
        console.log('btn_save...');
        save_page();
    });
    
    
    $(document).on('click','.btn_close',function(){
        //close page function - and delete edit_image
        obj_page.leave_page({
            page_action: 'close',
            before_close: function(){
                delete_edit_image();
            }
        });

    });


    $(document).on('click','#btn_delete',function(){
        //delete vintage
        var index = $("#vintage_id").val();
        get_vintage_associations(index);
        add_remove_vintage_basket(index, 'remove');//remove from basket
     });
     

    $(document).on('click','#btn_add_note',function(){
        //add new tasting note
        console.log('btn_add_note');
        add_note();

    });
    
    
    $(document).on('click', "input", function(e){    
        //highlight input when it has focus
        console.log('input has focus = '+ $(this).attr('id'));
        
        $(this).select();
        
        //select text on ios
        var myInput = document.getElementById($(this).attr('id'));
        myInput.setSelectionRange(0, 9999); //for ios
        
    });

    
    function delete_edit_image(){
        //delete edit_image
        
        console.log('delete_edit_image');
        
        //handle image deletion
        $.post("/vintage/rpc_vintage.php", {
            action: 'delete_image_edit'
            },
            function(data){
                if(data.success){
                    console.log('delete edit_image successful');
                }else{
                    console.log('delete edit_image failed error: '+data.error);
                }

        }, "json");
        
    }
    

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
    

    function deprecated_open_full_note(vintage_id, quality_rating, value_rating){
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
                page_flow_set(child_url,parent_url,true);

            } else {
                console.log('Open full note RPC failed error = '+data.error);
            }
        }, "json");

    }

    

    function delete_vintage(){
        //delete current vintage

        //prompt first
        $("#dialog-delete").dialog({
                modal: true,
                width: '410px',
                buttons: {
                    Delete: function() {
                        //delete
                        console.log('OK continue to delete vintage');
                        //close dialog
                        $(this).dialog('close');
                        //reset form
                        obj_page.set_is_dirty(false);
                        
                        //delete
                        $.post("/vintage/rpc_vintage.php", {
                            action: 'delete_vintage'
                            },
                            function(data){
                                if(data.success){
                                    console.log('delete vintage SUCCESS');

                                    jQuery.noticeAdd({
                                        text: 'Vintage deleted successfully!',
                                        stay: false
                                    });
                                    
                                    //display success message
                                    $(".con_button_bar").notify("Delete Vintage Successful",{
                                        position: "top left",
                                        style: "msg",
                                        className: "success",
                                        arrowShow: false
                                        }
                                    );
                                    
                                    $(':input').not(':button').val(''); //clear all inputs
                                    $('.form_data').text('');
                                    $(':button').not('#btn_close').attr('disabled', true);
                                    
                                    
                                    console.log('close_page');
                                    location.reload(); //reload page - will be redirected on reload by get_vintage_session

                                } else {

                                    $(".con_button_bar").notify(data.error,{
                                        position: "top left",
                                        style: "msg",
                                        className: "error",
                                        arrowShow: false
                                        }
                                    );

                                    console.log('delete vintage failed error='+data.error);
                                }
                        }, "json");


                    },
                    Cancel: function() {
                        //remain on page
                        $(this).dialog('close');
                    }
                },
                dialogClass: "clean-dialog",
                position: { my: "left bottom", at: "right top", of: '#btn_delete' }
            });
        }


     function get_vintage_associations(index){
         //retrieve count of associations for given vintage
         console.log('get_vintage_acquisitions');
          $.post("/vintage/rpc_vintage.php", {
            action: 'get_vintage_associations'
            },
            function(data){
                if(data.success){
                    console.log('get vintage associations SUCCESS');
                    console.log('notes='+data.note_count);
                    if(data.acquisition_count>0){
                        $(".con_button_bar").notify("Cannot delete vintage as it is associated with "+data.acquisition_count+" acquisitions",{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false
                            }
                        );
                        
                    } else {
                        //no errors continnue with delete
                        if(data.note_count>0){
                            //show warnings
                            $(".con_button_bar").notify("Vintage has "+data.note_count+" notes",{
                                position: "top left",
                                style: "msg",
                                className: "warning",
                                arrowShow: false
                                }
                            );
                        }

                        //continue with deletion
                        delete_vintage();
                    }
                } else {

                    console.log('get vintage associations failed error='+data.error);
                }
           }, "json");

    }


    //_______Validation_______
    
    
    
    $.validator.addMethod("greaterThan", function (value, element, param) {
        //add method to do max greater or equal to min comparison
        var $min = $(param);

        if (this.settings.onfocusout) {
            $min.off(".validate-greaterThan").on("blur.validate-greaterThan", function () {
                $(element).valid();
            });
        }
        
       
        var min = $min.val() || 0;
        var value = value || 0;
        //if (!){
        //    min = 0;
        //}else{
        //    min = $min.val();
        //}
        
         console.log('min value ='+min+' value = '+value);
        
        return parseInt(value) >= parseInt(min);
    }, "To year must be equal or greater than From year");


    function set_validation(){
        //special function to set up form validation

         $("#frm_vintage").validate({
            rules:{
                year: {
                        required: true, digits: true, range: [1000,9999],
                        remote: {
                            url: "/vintage/rpc_duplicate_year.php",
                            type: "post",
                            data: {
                                wine: function() {
                                    return $("#wine_id").val();
                                }
                            }
                        }
                 },
                alcohol: {
                    number: true, range: [0,100]
                        },
                drink_year_from: {
                    digits: true, number:true, range: [1000,9999]
                },
                drink_year_to: {
                    digits:true, number:true, range: [1000,9999],
                    greaterThan: '#drink_year_from'
                }  
            },      
            messages: {
                    year: {
                    required: "A year is required",
                    range: "Must be a 4 digit year e.g. 2005",
                    number: "Must be a 4 digit year e.g. 2005",
                    remote: "This vintage has already been added"
                    },
                    drink_year_from: {
                    number: "Must be a 4 digit year e.g. 2005",
                    range: "Must be a 4 digit year e.g. 2005",
                    },
                    drink_year_to: {
                    number: "Must be a 4 digit year e.g. 2005",
                    range: "Must be a 4 digit year e.g. 2005",
                    }
                },
            errorPlacement: function(error, element){
                //place error in new div after error div parent
                d = document.createElement('div');
                $(d).addClass('clear-left float-left error-validation').append(error);
                element.parent().after($(d));
                },
            invalidHandler: function(event, validator){
                $(".con_button_bar").notify("Validation Failed",{
                    position: "top left",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 1000
                });
                }
        });
    }


//*****Dialogs*****


    $("#dialog_select_grapes").dialog({
        autoOpen: false,
        height: 700,
        width: 375,
        modal: true,
        buttons: {
                "Save": function() {
                    //get values

                },

                Cancel: function() {
                        $( this ).dialog( "close" );
                }
        },

        close: function() {
                console.log('close_function');

        }
    });


//*** Menus ***

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
                    case 'Show Acquisitions': // index.php only
                        $('#panel_right').toggle("slide", { direction: "right" }, 500);
                        break;
                    case 'New Note': //tasting_note.php only
                        add_note();
                        break;
                    case 'New Vintage': // wine.php only
                        add_vintage();
                        break;
                    case 'Wines':
                        open_wines();
                        break;
                    case 'Reference Data':
                        open_reference_data();
                        break;
                    default:
                        console.log('selected_item not recognised: '+selected_item);
                }
                break;

            default:
                console.log("menu_id not recognised: "+menu_id);
        }

    }


    function open_wines(){
        //open Wines page
        obj_page.leave_page({
            dst_url: "/index.php",
            dst_action: 'open',
            page_action: 'leave'
        });
    }


    function open_reference_data(){
        //open ref data page
        obj_page.leave_page({
            dst_url: "/admin/index_admin.php",
            rtn_url: this_page,
            dst_action: 'open',
            page_action: 'leave'
        });
    }


    function add_wine(){
        //Add new wine
        obj_page.leave_page({
            dst_url:        "/wine/wine.php",
            rtn_url:        "/index.php",
            page_action:    'leave',
            dst_type:       "wine",
            object_id:      null,
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



    function add_note(vintage_id){
        //Add tasting note
        vintage_id = vintage_id > 0 ? vintage_id : $('#vintage_id').val();
      
        if(vintage_id<=0){
            console.log("can't add note until vintage is saved");
            $(".con_button_bar").notify("Save new Vintage before adding a note",{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false
                }
            );
            
        } else {

            //create note object for data object - not used for Add
            var data = ({
                quality_rating: null,
                value_rating: null
            });

            obj_page.leave_page({
                dst_url:        "/vintage/tasting_note.php",
                rtn_url:        this_page,
                page_action:    'leave',
                dst_type:       "note",
                dst_action:     "add",
                object_id:      0, //new
                parent_id:      vintage_id,
                data:           data
            });
        }
        
    };
    
    

}); //document.ready


</script>

</body>
</html>