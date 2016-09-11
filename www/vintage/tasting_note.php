<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

echo "<!DOCTYPE html><html>";
echo "<head>";
    require_once("$root/includes/standard_html_head.inc.php");
    echo "<title>Tasting Note</title>";
    require_once("$root/includes/css.inc.php");
    require_once("$root/includes/script_libraries.inc.php");//include all script libraries
echo "</head>";

?>

<body>

<div id="dialog-delete" class="hidden" title="Delete Note">
        <div style="float:left; display:inline-block; margin-top:5px; margin-bottom:10px;">
            <img src="/images/warning_flat_orange_32.png" width="32px" height="32px">
        </div>
        <div style="float:left; margin-left:15px; margin-bottom:10px; width:320px; line-height:150%;">
            <h2>Are you sure you want to delete this Tasting Note?</h2>
            <p style="color:grey; margin-top:10px;">All data will be lost</p>
        </div>
</div>
    
    
<div id="unsaved-note" class="hidden" title="Unsaved Changes">
    
    <div style="float:left; display:inline-block; margin-top:20px; margin-bottom:0px;">
        <img src="/images/warning_flat_orange_32.png" width="32px" height="32px">
    </div>
    <div style="float:left; margin-top:20px; margin-left:15px; margin-bottom:0px; width:320px; line-height:150%;">
        <h2>You have unsaved changes!</h2>
        <p style="color:grey; margin-top:10px;">Changes will be lost if you continue</p>
    </div>    
</div>
   
    

<?php
    
    //get vintage label
    $vintage_id = $_SESSION['var_note']['vintage_id'];
    if($vintage_id){
        $vintage_obj = new vintage($vintage_id);
        $vintage_label = $vintage_obj -> vintage_label();
    }else{
        $vintage_label = "";
    }

    //standard html dialog boxes
    require_once("$root/includes/standard_dialogs.inc.php");

    //capture REQUESTS
    echo "<input type=\"hidden\" id=\"request_vintage_id\" value=\"$vintage_id\"/>";

    require_once("$root/includes/standard_dialogs.inc.php");
    
    //html page
    echo "<div class=\"page_container\">";
        require_once("$root/includes/nav/topheader.php"); //header
        
        echo "<div class=\"con_single_form rounded\">";
        
            //Title bar
            echo "<div class=\"con_title_bar\" id=\"con_title_bar\" >";
                //wine name
                echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px;\" >";
                    echo "<div style=\"float:left; width:58px;\" >";
                        echo "<img src=\"/images/tasting_flat_grey_64.png\" height=\"48px\" width=\"48px\" >";
                    echo "</div>";
                    echo "<div style=\"width:auto; float:left; padding-top:5px;\" >";
                        echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >Tasting Note</h1>";
                        echo "<h3 style=\"color:darkgrey;\">$vintage_label</h3>";
                    echo "</div>";
                    echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left; height:50px;\"  >";
                        echo "<img id=\"process_indicator\" src=\"/images/ajax-loader.gif\" height=\"24px\" width=\"24px\" />";
                    echo "</div>";
                    echo "<div class=\"clear\"></div>";
                echo "</div>";
            echo "</div>"; //con_title_bar
            
            echo "<div id=\"tasting_note_form_content\" >"; //tasting_note
                //filled by jquery load method - rpc_note_form_html.php
                echo "<div class=\"static-rating\" style=\"width:24 px; float:left;\"></div>";
            echo "</div>"; //tasting_note_form_content
            
            echo "<div class=\"con_button_bar\" >"; //buttons
                if(is_authed()){
                    echo "<input type=\"button\" id=\"btn_save\" name=\"btn_save\" value=\"Save\" $disabled\>";
                    echo "<input type=\"button\" id=\"btn_new\" name=\"btn_new\" value=\"New\" $disabled\>";
                    echo "<input type=\"button\" id=\"btn_delete\" name=\"btn_delete\" value=\"Delete\" $disabled\>";
                }
                echo "<input type=\"button\" class=\"btn_close\" id=\"btn_close\" name=\"btn_close\" value=\"Close\" \>";
            echo "</div>"; //bottom_row
            echo "<div class=\"clear\" ></div>";
        
        echo "</div>"; //con_single_form     
    echo "</div>"; //page_container
    

?>
    
<!-- Pop-up Menus-->
<div id='main_menu' class="pop_up" style="width:225px; display:none; position:fixed; z-index:35;">
    <div class="ui-menu-item-first">New Note<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Wine<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Acquisition<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
     <div>Wines<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div class="ui-menu-item-last">Reference Data<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>

</body>

<script type="text/javascript">
    
$(document).ready(function(){

    //TODO: Add Main Menu navigation to Tasting Note
    
    var this_page = "/vintage/tasting_note.php";
    
    var obj_page = new page_control({
        save_function: function(){
            return save_page();
        },
        page_url: this_page //set page url
    });
    

//***functions***

    function get_note_db(key){
        //get note from db and put to session
        var vintage_id = $('#vintage_id').val();
        console.log('get_db vintage_id='+vintage_id+' note_id='+key);
        
        $.post("/vintage/rpc_notes.php", {
            note_id: key,//note_id
            vintage_id: vintage_id,
            rpc_action: 'get_from_db'
        }, function(data){
            if(data.success){
                //refresh form
                refresh_note_form();
            } else {
                console.log('get_db error returned = '+data.error);
            }
        }, "json");
        
    }
    

    function save_page(callback){
        //get note data from form and put to DB (save record)
        
        var def = new $.Deferred();
        
        if(!$("#frm_note").valid()){
            var msg = 'Validation failed';
            console.log(msg);
            $(".con_button_bar").notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false
                }
            );
            def.reject(msg);
        }
        
        var note_id  = $('#note_id').val();
        var vintage_id  = $('#vintage_id').val();
        var note_date  = $('#note_date').val();
        var note_quality  = $('#note_quality').val();
        var note_value  = $('#note_value').val();
        var note_appearance  = $('#note_appearance').val();
        var note_aroma = $('#note_aroma').val();
        var note_taste  = $('#note_taste').val();
        var note_general  = $('#note_general').val();
        var sweetness_id = $('#sweetness_id').val();
        var fullness_id  = $('#fullness_id').val();
        console.log('put_db');

        $.post("/vintage/rpc_notes.php", {
            note_id: note_id,
            vintage_id: vintage_id,
            note_date: note_date,
            note_quality: note_quality,
            note_value: note_value,
            note_appearance: note_appearance,
            note_aroma: note_aroma,
            note_taste: note_taste,
            note_general: note_general,
            sweetness_id: sweetness_id,
            fullness_id: fullness_id,
            action: 'put_db'
        }, function(data){
            if(data.success){
                console.log('saved to db - '+data.msg);
                if(data.note_id){ //update note_id field on insert
                    $('#note_id').val(data.note_id);
                    $("#btn_delete").removeAttr('disabled');//enable delete button
                }
                obj_page.set_is_dirty(false); //update is_dirty
                //refresh_all_notes(); //refresh all notes listbox

                //display success message
                $(".con_button_bar").notify("Save Successful",{
                    position: "top left",
                    style: "msg",
                    className: "success",
                    arrowShow: false
                    }
                );
                
                //callback
                if(typeof callback === 'function'){
                    callback();
                }
                
                def.resolve(true);
                
            } else {
                //save failed
                var msg = 'Save Note failed with error: '+data.error;
                //display error message
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "error",
                    arrowShow: false
                    }
                );
                def.reject(msg);
            }
            
        }, "json");
        
        return def.promise();
    }


    function delete_note_db(note_id, url){
        //delete note from from db
        console.log('delete_db note_id='+note_id);
        
        $.post("/vintage/rpc_notes.php", {
            note_id: note_id,
            rpc_action: 'delete_db'
        }, function(data){
            if(data.success){
                var msg = "Tasting Note deleted successfully";
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "success",
                    arrowShow: false
                    }
                );
                if(data.note_id>0){
                    var note_id = data.note_id;
                    url = "/vintage/tasting_note.php?note_id="+data.note_id;
                    get_db(note_id,url);
                } else {
                    page_flow_return(this_page);
                }
            } else {
                //delete failed
                var msg = "Failed to delete tasting Note error: "+data.error;
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "error",
                    arrowShow: false
                    }
                );
                console.log(msg);
                console.log(data);
            }

        }, "json");
    }


    function old_refresh_all_notes(){
        var vintage_id = $('#vintage_id').val();
        $('#all_notes_content').load('/vintage/rpc_all_notes_html.php', {vintage_id: vintage_id}, function(){
           //run following code once refreshed
       });
    }


    function refresh_note_form(){
        var request_vintage_id = $('#request_vintage_id').val();
        $('#tasting_note_form_content').load('/vintage/rpc_note_form_html.php',{request_vintage_id: request_vintage_id}, function(){
           //run following code once refreshed
           all_notes_listBox();
       });
    }
    
    
    function all_notes_listBox(){
        $("#con_all_notes").listBox({
            title: "Tasting Notes",
            width: 315,
            height: 200,
            showFilter: false,
            showBorder: true,
            showShadow: false,
            showRoundedCorners: false,
            addClass: 'listBox_flat_theme',
            listContent: '/vintage/rpc_all_notes_html.php',
            clickAdd: function(event, data){

            },
            clickSelected: function(event, data){
                note_id = data.listBox_id;
                console.log('Note selected in listBox note_id: '+note_id);
                open_note(note_id);
            }
        });
    }
    
    
    function delete_note(){
        note_id = $('#note_id').val();
        if(!note_id>0){
            return false;
        }
        
        $("#dialog-delete").dialog({
            modal: true,
            width: '410px',
            buttons: {
                OK: function() {
                    //delete
                    $(this).dialog('close');
                    obj_page.set_is_dirty(false);
                    console.log('Delete '+note_id);
                    delete_note_db(note_id);
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
                    case 'New Note':
                        new_note();;
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
    
    
    function open_wines(){
        //open Wines page
        obj_page.leave_page({
            dst_url: "/index.php",
            dst_action: 'open',
            page_action: 'leave'
        });
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
    
    
    function open_reference_data(){
        //open ref data page
        obj_page.leave_page({
            dst_url: "/admin/index_admin.php",
            rtn_url: this_page,
            dst_action: 'open',
            page_action: 'leave'
        });
    }

//***Initialise page***


    //refresh html
    refresh_note_form();
    

    //load basket
    initialise_basket();


    //show ajax activity
    $('#process_indicator')
    .hide()  // hide it initially
    .ajaxStart(function() {
        $(this).show();
    })
    .ajaxStop(function() {
        $(this).hide();
    });


    //detect changes - set to dirty
    $(document).on('click', ':checkbox',function(){
        obj_page.set_is_dirty(true);
    });

    $(document).on('click','.auto-submit-star,.rating-cancel',function(data){
        if(!$(this).hasClass('star-rating-readonly')){ //ignore in read-only mode
           obj_page.set_is_dirty(true); 
        }
    });
    
    $(document).on('click','.auto-submit-pound,.rating-cancel',function(){
        if(!$(this).hasClass('pound-rating-readonly')){ //ignore in read-only mode
            obj_page.set_is_dirty(true);
        }
    });

    //date picker
    $(document).on('click','#note_date', function(){
        obj_page.set_is_dirty(true);
    });




//***form buttons***

    $(document).on('click','#btn_close',function(){
        //function in page_control
        obj_page.close_page();
    });


    $(document).on('click','#btn_save',function(){
        ///save button - checks validation before calling database save functions
        save_page();
    });


    $(document).on('click','#btn_delete',function(){
        //delete note from db
        delete_note();
    });


    $(document).on('click','#btn_new',function(){
        //open new note for current vintage
        console.log('open new note');
        new_note();
    });


    function new_note(){
        
        if(!obj_page.is_dirty()){
            console.log('new_note - page not dirty');
            get_note_db();
            return true;
        }
        
        console.log('page is_dirty - prompt user to save current note');
        $("#unsaved-note").dialog({
            width: "410px",
            modal: true,
            dialogClass: "clean-dialog",
            position: { my: "center bottom", at: "center top", of: '.btn_close' },
            buttons: {
                Save: function() {
                    save_page(function(){
                        get_note_db();
                    });
                    $(this).dialog('close');
                },
                Continue: function() {
                    obj_page.set_is_dirty(false);
                    get_note_db();
                    $(this).dialog('close');
                }
            }
        });
        
    }
    
    
    function open_note(note_id){
        
        if(!obj_page.is_dirty()){
            console.log('open_note - page not dirty');
            get_note_db(note_id);
            return true;
        };
             
        console.log('open_note page is_dirty');
        $("#unsaved-note").dialog({
            width: "410px",
            modal: true,
            dialogClass: "clean-dialog",
            position: { my: "center bottom", at: "center top", of: ".btn_close" },
            buttons: {
                Save: function() {
                    //remain on page
                    save_page(function(){
                        get_note_db(note_id);
                    });
                    $(this).dialog('close');
                },
                Continue: function() {
                    //leave page and discard changes
                    obj_page.set_is_dirty(false);//continue
                    get_note_db(note_id);
                    $(this).dialog('close');
                }
            }
        });
   
    }
 
    
    

//***form controls***

    
    $(document).on('click','.note_link',function(){
        //open note from 'Other Notes List', put to session
        var key = $(this).attr('id');
  
    });




});

</script>
</html>
