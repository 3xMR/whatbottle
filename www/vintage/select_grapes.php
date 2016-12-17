<?php

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

echo "<html>";
echo "<head>";

require_once("$root/includes/standard_html_head.inc.php");
//include style sheets
require_once("$root/includes/css.inc.php");
//page title
echo "<title>Select Grapes</title>";
//include all script libraries
require_once("$root/includes/script_libraries.inc.php");


?>

<script type="text/javascript">

$(document).ready(function(){

    //_______Globals______
    var this_page = "/vintage/select_grapes.php";
   
    
    //______Initialise_____
    $('#filter').focus();
    
    
    //show ajax activity
    $('#process_indicator')
    .hide()  // hide it initially
    .ajaxStart(function() {
        $(this).show();
    })
    .ajaxStop(function() {
        $(this).hide();
    });


    var obj_page = new page_control({
        save_function: function(){
            return save_page();
        },
        page_url: this_page, //set page url
        no_dirty: true,
        pop_up: true //return_url remains unchanged
    });
    
    
    function save_to_session(callback){
        //save values to session
        var def = new jQuery.Deferred();
        
        //create json array from input values
        var var_fields = $(".checkbox_percent").serializeArray();
        var json_field = JSON.stringify(var_fields); //convert to json array
        //console.log(json_field);
        
        //save - put array of input fields to session
        $.post("/vintage/rpc_vintage.php", {
            action: 'put_grapes_session',
            json_field: json_field
        }, function(data){
            if(data.success){
                console.log('Save grapes to session successful '+data.msg);
                obj_page.set_is_dirty(false); //clear dirty flag
                //callback
                if(jQuery.isFunction(callback)){
                    callback(); //execute callback function
                }
                def.resolve(true);      
            } else {
                var msg = 'Save Grapes to session failed with error: '+data.error;
                console.log(msg);
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
        
              
    }
    
    
    function save_page(callback){
        //save page
        
        var def = new jQuery.Deferred();
   
        console.log('save_page...');
        var total_percent = check_total_percentage();
        
        if(total_percent > 100){ //validate
            var msg = 'Save failed because total percentage ('+total_percent+'%) is greater than 100%';
            console.log(msg);
            //display error message
            $(".con_button_bar").notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false
                }
            );
            def.reject(msg);
        };
        
        //continue with save
        
        if(!obj_page.is_dirty()){ //page not dirty so nothing to save
            //close page
            obj_page.leave_page({
                page_action: 'close'
            });

            def.resolve(true);
            return;
        }
        
        $.when(save_to_session()).then(function(){
            console.log('save_to_session promise returned');
            //display message
            msg = "Save Successful";
            $(".con_button_bar").notify(msg,{
                position: "top left",
                style: "msg",
                className: "success",
                arrowShow: false
                }
            );
            
            //timeout used to delay callback and allow success msg to be shown
            var timeoutID = window.setTimeout(delayedFinish,750);
            function delayedFinish(){
                if(typeof callback === 'function'){
                    callback();
                };
                
                //close page
                obj_page.leave_page({
                    page_action: 'close'
                });
                
                def.resolve(true);
            }
            
        });
        
        return def.promise();
    }
    

    
    
    function check_total_percentage(){
        
        var total=0;
  
        $("input:checkbox:checked").each(function(){
            id = '#'+ $(this).val();
            total += parseInt($(this).parent().parent().find(id).val());
        });
        
        if(total > 100){
            return total;
        }else{
            return false;
        }
        
    }
    
    
    function add_grape(){
        //show add grape dialog
        save_to_session(function(){
            open_add_grape_dialog();
        });
    }
    
    
    function open_add_grape_dialog(){
        $( "#dialog_grape" ).dialog("open");
    }
    
    
    function scroll_to_row(id){
        //scroll to specified row
        $("#con_listBox_grape").listBox('scrollToRow', id, true);
    }


    //_______Actions & Events_______

    $(document).on('click',':checkbox', function() {
        //set focus on percent input when check box checked
        console.log('hello this='+this);
        if($(this).prop('checked')){
            id = '#'+ $(this).val();
            console.log(id);
            $(this).parent().parent().find(id).attr('disabled',false);
            $(this).parent().parent().find(id).focus();
            
        }else{
            id = '#'+ $(this).val();
            $(this).parent().parent().find(id).val("");
            $(this).parent().parent().find(id).attr('disabled', true);
        }
    });


    $(".btn_close").click(function(){
        //close page - not used after moving to just OK button
        obj_page.leave_page({
            page_action: 'close'
        });
        
    });


    $(".btn_save").click(function(){
        //save page
        save_page(function(){
            obj_page.leave_page({
               page_action: 'close' //save page and then close
            });
        });
        
    });
    
    
    $("#con_listBox_grape").listBox({
        title: "Grapes",
        width: 500,
        height: 500,
        listContent: '/vintage/rpc_listBox_grape_html.php',
        showFilter: true,
        showBorder: true,
        showRoundedCorners: false,
        showTitle: false,
        clickAdd: function(event, data){
            add_grape();
        },
        clickFilterClear: function(event, data){
            console.log('clickFilterClear event triggered');
            save_to_session(function(){
               $("#con_listBox_grape").listBox('refresh'); 
            });
        }
        
    });
    
    
    $( "#dialog_grape" ).dialog({
        autoOpen: false,
        height: 270,
        width: 345,
        modal: true,
        dialogClass: "clean-dialog",
        position: { my: "left bottom", at: "right top", of: '#con_listBox_grape_btn_add' },
        buttons: {
                "Save": function() {
                    //get values
                    var value = $('#grape_text').val();
                    var id = $("#grape_id").val();
                    var colour = $("#grape_colour").val();
                    console.log('validate frm_grape value='+value+" id="+id+" colour="+colour);
                    
                    //validate form
                    var bln_valid = false;
                    bln_valid = $("#frm_grape").valid();
                    
                    if(bln_valid==false){
                        console.log('failed validation');
                    } else {
                        //add to db and close form
                        console.log('passed validation');

                        $.post("/admin/rpc_ref_data.php", {
                                action: 'save_grape_db',
                                value: value,
                                id: id,
                                colour: colour
                            }, function(data){
                                if(data.success){
                                    console.log('saved to db SUCCESS');
                                    //load html content
                                    id = data.id;
                                    console.log('db returned id='+id);
                                    
                                    $("#con_listBox_grape").listBox('refresh',id); //refresh html
                                    
                                    msg = "Add Grape successful";
                                    $(".con_button_bar").notify(msg,{
                                        position: "top left",
                                        style: "msg",
                                        className: "success",
                                        arrowShow: false
                                        }
                                    );
                                    
                                    //close dialog
                                    $("#dialog_grape").dialog('close');

                                }else{
                                    //display error message
                                    msg = "Add Grape failed - "+data.error;
                                    $(".ui-dialog-buttonpane").notify(msg,{
                                        position: "top left",
                                        style: "msg",
                                        className: "warning",
                                        arrowShow: false
                                        }
                                    );
                                }
                            }, "json");

                        
                    }

                },

                Cancel: function() {
                        $( this ).dialog( "close" );
                }
        },
        close: function() {
               console.log('close_function');
               $('#grape_text').val("");
        }
    });


});

</script>
</head>
<body>
    
    
    <div id="dialog_grape" class="hidden" title="Add Grape">
        <form id="frm_grape" name="frm_grape">
            <h2 style="margin-bottom:20px;"> Add Grape</h2>
            <div class="input-main-label" >
                <p>Grape Name</p>
            </div>
            <div class="input-main" >
                <input style="height:29px;" type="text" class="input-main" id="grape_text" ></input>
            </div>
            <div class="input-main-label" >
                <p>Grape Colour</p>
            </div>
            <div class="input-main" >
                <select name="grape_colour" id="grape_colour" >
                    <option value="Red">Red
                    <option value="White">White
                </select>
            </div>
            <br/>
        </form>
    </div>

<?php

require_once("$root/includes/standard_dialogs.inc.php");

//page
echo "<div class=\"page_container\">";
    
    //header
    require_once("$root/includes/nav/topheader.php");

    //form
    echo "<div class=\"con_single_form rounded\" >";
    
        //main heading & ajax process indicator
        echo "<div class=\"con_title_bar\" style=\"float:left;\" >";
            echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:15px; \" >";
                echo "<div style=\"float:left; width:58px;\" >";
                    echo "<img src=\"/images/grapes_flat_grey_64.png\" height=\"48px\" width=\"48px\" >";
                echo "</div>";
                echo "<div style=\"width:auto; float:left; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >Select Grapes</h1>";
                echo "</div>";
                echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left; height:50px;\"  >";
                    echo "<img id=\"process_indicator\" src=\"/images/ajax-loader.gif\" height=\"24px\" width=\"24px\" />";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
            echo "</div>";
        echo "</div>"; //con_title_bar
     
        
        echo "<div style=\"float:left\" id=\"con_listBox_grape\"></div>";
            //grapes listbox container - filled by plugin from /vintages/rpc_listBox_grape_html.php
        echo "<div class=\"clear\" ></div>";

        
        //button footer
        echo "<div class=\"con_button_bar\" >";

            if( is_authed() ){
                echo "<input type=\"button\" id=\"btn_save\" class=\"btn_save\" value=\"OK\" />";
            }        
            //echo "<input type=\"button\" id=\"btn_close\" class=\"btn_close\" value=\"Close\" class=\"btn_close\" />";
        
        echo "</div>";

        
        echo "<div class=\"clear\" ></div>";
    
    echo "</div>"; //single_form

echo "</div>"; //page_container
?>
    
</body>
</html>