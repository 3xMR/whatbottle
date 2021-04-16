<?php
/*
 * Add new wine page
 * August 2019
 * Redesigned to making adding wines even easier
 * Step 1. Find or add new Producer
 * Step 2. Select from existing wines or add a new wine
 * Step 3. Select Region from a new easy pick dialog
 * 
 * rpc_wine_form_html.php - this page loads the html
 * rpc_wine_from_producer_html.php - this page shows the wines for the selected producer
 * autocomplete_results.php - autocomplete for Producer
 */
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";

require_once("$root/includes/standard_html_head.inc.php"); 
require_once("$root/includes/css.inc.php");
echo "<title>What Bottle?</title>"; //page title

require_once("$root/includes/script_libraries.inc.php"); //include all script libraries

If(isset($_SESSION['var_wine_temp']['status'])){
    //determine status and set title accordingly - form is reset on rpc_wine_form_html.php
    $status = $_SESSION['var_wine_temp']['status'];
    if($status == 1){
        $title_text = "Add New Wine";
    }else{
        $title_text = "Edit Wine Details";
    }
}

?>

</head>

<body>

    <div id="dialog-producer" class="hidden" title="Add Producer?">	
        <h2 style="margin-bottom:15px;"> Add New Producer</h2>
        <div class="input-main-label">
            <p>Producer</p>
        </div>
        <div class="input-main" >
            <input type="text" style="width:100%;" id="add_producer"></input>
        </div>
    </div>
    
    
    <div id="dialog-country" class="hidden" title="Add Edit Country">
        <h2 style="margin-bottom:15px;" id="country_dialog_title" > Add Country</h2>
        <div class="input-main-label">
            <p>Country Name</p>
        </div>
        <div class="input-main">
            <input type="text" name="country_text" id="country_text" autocomplete="new-location" style="width:100%;"/>
            <input type="hidden" id="add_country_id" />
        </div>
        <div class="input-main-label">
            <p>Flag Image</p>
        </div>
        <div class="input-main">
            <input type="text" name="flag_file" id="flag_file" autocomplete="off" style="width:100%;"/>
            <input type="hidden" id="flag_file" />
        </div>
        <div class="clear" ></div>
    </div>
    
   
    <div id="dialog-region" class="hidden" title="Add Edit Region">
        <h2 style="margin-bottom:15px;" id="region_dialog_title" >Add Region</h2>
        <div class="input-main-label">
            <p>Region</p>
        </div>
        <div class="input-main">
            <input type="text" name="region_text" id="region_text" autocomplete="off" style="width:100%;"/>
            <input type="hidden" id="add_region_id" />
            <input type="hidden" id="region_country_id" />
        </div>
        <div class="clear" ></div>
    </div>
   
    
    <div id="dialog-subregion" class="hidden" title="Add Edit Subregion">
        <h2 style="margin-bottom:15px;" id="subregion_dialog_title" >Add Subregion</h2>
        <div class="input-main-label">
            <p>Subregion</p>
        </div>
        <div class="input-main">
            <input type="text" name="subregion_text" id="subregion_text" autocomplete="off" style="width:100%;"/>
            <input type="hidden" id="add_subregion_id" />
            <input type="hidden" id="subregion_region_id" />
        </div>
        <div class="clear" ></div>
    </div>
    
    
    <div id="dialog-unique-wine" class="hidden" title="Warning - Duplicate Wine">
        <p>
            <span class="ui-icon ui-icon-alert" style="float:left; margin:0 10px 10px 0;"></span>
            Duplicate Wine - A wine with this name, wine type and producer already exists!
        </p>
        </br>
        <p>
            <b>OK</b> - to continue and create a duplicate wine<br/>
            <b>Cancel</b> - to return to page and make changes
        </p>
    </div>
    
    
    <div id="dialog-location-select" class="hidden" title="Select Region">
            <div id="con_listBox_location" ></div>
    </div>


    <div class="hidden"><!--hidden fields-->
        <span>wine_id: </span><input type="text"name="wine_id" id="wine_id" value="<?php echo $_SESSION['var_wine_temp']['wine_id']; ?>" ><br/>
        <span>is_dirty: </span><input type="text" name="is_dirty" id="is_dirty" value="<?php echo $_SESSION['var_wine_temp']['is_dirty'];?>" ><br/>
        <span>status: </span><input type="text" name="status" id="status" value="<?php echo $_SESSION['var_wine_temp']['status'];?>" ><br/> <!--determines add, edit, delete -->
        <input type="text" name="country_id" id="country_id" value="<?php echo $_SESSION['var_wine_temp']['country_id'];?>" /><br/>
        <input type="text" name="region_id" id="region_id" value="<?php echo $_SESSION['var_wine_temp']['region_id'];?>" /><br/>
        <input type="text" name="subregion_id" id="subregion_id" value="<?php echo $_SESSION['var_wine_temp']['subregion_id'];?>" /><br/>
    </div>    
<?php


//page
echo "<div class=\"page_container\">";

    //header
    require_once("$root/includes/nav/topheader.php");


    //wine_form
    echo "<div class=\"con_single_form\" >";
        echo "<div class=\"con_title_bar\" >";  //title bar
            //wine name
            echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px;\" >";
                echo "<div style=\"float:left; width:3em;\" >";
                    echo "<img style=\"width:2.5em; height:2.5em;\" src=\"/images/wine_flat_grey_64.png\"  >";
                echo "</div>";
                echo "<div style=\"width:auto; float:left; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >$title_text</h1>";
                echo "</div>";
                echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left; height:2em;\"  >";
                    echo "<img id=\"process_indicator\" src=\"/images/ajax-loader.gif\" height=\"24px\" width=\"24px\" />";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
            echo "</div>";

        echo "</div>"; //con_title_bar

        //Column 1
        echo "<div class=\"rwd-con-whole\" id=\"wine_form_content\" >";
            //filled by jquery load method - rpc_wine_form_html.php
        echo "</div>";


        //Button Bar
        echo "<div class=\"con_button_bar\" >";
            if(is_authed()){
                echo "<input type=\"button\" name=\"btn_save\"  id=\"btn_save\" value=\"Save\" class=\"submit form_input\" />";
                echo "<input type=\"button\" name=\"btn_edit\" id=\"btn_edit\" value=\"Edit\" />";
                echo "<input type=\"button\" name=\"btn_delete\" id=\"btn_delete\" value=\"Delete\" style=\"display:none\" />";
            }
            echo "<input type=\"button\" name=\"btn_close\" id=\"btn_close\" value=\"Close\" class=\"btn_close form_input\" />";

        echo "</div>";

        //clear page_container
        echo "<div class=\"clear\"></div>";

    echo "</div>"; //con_single_form


echo "</div>"; //page_container

//common dialogs
require_once("$root/includes/standard_dialogs.inc.php");

?>

        
<div id='main_menu' class="pop_up" style="width:200px; display:none; position:fixed; z-index:30;">
    <div class="ui-menu-item-first" >New Wine<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Vintage<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>New Acquisition<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>Wines<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div class="ui-menu-item-last">Reference Data<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>
    

</body>

<script type="text/javascript">

$(document).ready(function(){
    
    //____Global variables____
    var objLocation = null;
    var this_page = "/wine/wine.php";


    //____Initialise Page_____

    var obj_page = new page_control({
        save_function: function(){
            return save_page(true);
        },
        page_url: this_page //set page url
    });

    get_wine_session(); //get form status, calls load_wine_html, which calls initialise page
    
    function load_wine_html(){
       //load vintage html from remote script
       $('#wine_form_content').load('/wine/rpc_wine_form_html.php', function(){
            console.log('rpc_wine_form_html.php loaded');
            initialise_page(); //initialise page and set control status
            
       });
    }
    
    
    function initialise_page(){
        /* function to intialise page as html load means form
        *  will load after parent page has completed loading
        */
       console.log('initialise page...');
        $('#process_indicator') //show ajax activity
        .hide()  // hide it initially
        .ajaxStart(function() {
            $(this).show();
        })
        .ajaxStop(function() {
            $(this).hide();
        });

        set_autocomplete();
        set_validation();

        initialise_listBox_location(); //select location pop-up
        $('#producer').focus(); //set initial focus
        update_page_status(); //update control states
        
    
    }
    

    //____functions____
    
    function putWineServer(save_to_db){
        //put wine record to server
        //save_db (boolean) - true to commit data to db as well as session
        
        var deferred = $.Deferred(); //promise required for page_control
        
        //serialize form data
        var var_fields = $(":input").serializeArray();
        var json_array = JSON.stringify(var_fields);
        console.log("serialize form data");
        console.log(json_array);
        console.log('save_to_db = '+save_to_db);
        
        //post data to server and when completed return promise
        $.when(post_data(save_to_db)).then(function(data){ 
            console.log("post_data to server returned resolved: "+data);
            deferred.resolve(true); 
        }, function(data){//promise rejected
            deferred.reject(data);
            console.log("post_data to server returned rejected: "+data);
        });


        function post_data(save_to_db){
            
            var def_post_data = $.Deferred();

            $.post("/wine/rpc_wine_db.php", {
                    action: 'put_wine_session',
                    json_array: json_array,
                    save_db: save_to_db
                }, function(data){
                    console.log(data);
                    if(data.success){
                        console.log('put_wine_session OK db save_type = '+data.save_type);
                        
                        if(data.save_type == 'session'){ //session update - no need to process beyond here
                           console.log('put_wine_to_session - put to session successfully');
                           def_post_data.resolve(data);
                        }

                        if(data.save_type == 'db update' || data.save_type == 'db insert' ){
                            
                            $('#wine_id').val(data.wine_id); //update wine_id
                            $('#status').val('saved');//update status
                            obj_page.set_is_dirty(false);//reset is_dirty
                            
                            //display success message
                            $(".con_button_bar").notify("Save Successful",{
                                position: "top left",
                                style: "msg",
                                className: "success",
                                arrowShow: false
                                }
                            );

                            //go straight to add new vintage for new wines
                            if(data.save_type==='db insert'){
                                //new wine - checkbox selected so create new vintage
                                console.log('add vintage - redirect to vintage page');
                                var rtn_url = "/index.php"; //set rtn url to index rather than this_page
                                setTimeout(function(){ //timeout to allow success msg to be seen
                                    add_vintage(rtn_url);  //add new vintage function
                                },500);
                            } 
                            
                            def_post_data.resolve(true);

                        } 

                    } else {
                        console.log('put_wine_session failed with error = '+data.error);
                        def_post_data.reject(data);
                        
                        //display message
                        $(".con_button_bar").notify(data.error,{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false
                            }
                        );

                    }//data success

                }, "json");
                
                return def_post_data.promise();
                
            }; //post_data
            
            return deferred.promise();
            
    }; //putWineServer

    
    function producerSelected(){
        //function called once producer is selected
        console.log('producerSelected');
        var producer_id = $("#producer_id").val();
        if(producer_id > 0){ //get count of wines for producer, and update wine session so that producer_id is updated
            console.log('producerSelected producer_id: '+producer_id);
            $.when( getWinesForProducerCount(producer_id), putWineServer() ).then(
                function(count){
                    if(count>0){
                        $( "#con_wines_from_producer" ).load( "/wine/rpc_wine_from_producer_html.php" ); //load html into div
                        $('#con_wines_from_producer').show('medium'); //show div
                        console.log('producerSelected(): show wines for producer');
                    }else{
                        console.log('hide wines for producer');
                        $('#con_wines_from_producer').hide('fast'); 
                    }
                }
            );  
        }else{
            //no producer selected - hide wines div
            $('#con_wines_from_producer').hide('fast'); 
        }
        
    }
             
 
    function getWinesForProducerCount(producer_id){
       //return count of wines for given producer_id
       
       var def = $.Deferred();
       
       console.log('getWinesForProducerCount producer_id:'+producer_id);
       
       $.post("/wine/rpc_wine_db.php",{
            producer_id: producer_id,
            action: 'get_wine_count_for_producer'
            }, function(data) {
                if(data.success){
                    console.log('getWinesForProducerCount successful');
                    console.log('wine count = '+data.count);
                    def.resolve(data.count);
                } else {
                    console.log('getWinesForProducerCount failed');
                    def.reject();
                }
        }, "json");
       
       return def.promise();
   }


//   function get_wine_db(){
//        //get wine details from DB
//
//        $.post("/wine/rpc_wine_db.php",{
//            wine_id: window.wine_id,
//            action: 'get_from_db'
//
//            }, function(data) {
//                if(data.success){
//                    console.log('get_from_db successful');
//
//                    console.log(data.country);
//                    console.log(data.country_id);
//                    $("#country").val(data.country);
//                    $("#country_id").val(data.country_id);
//
//                    //fill region
//                    if(data.region_id){
//                        $("#region").val(data.region);
//                        $("#region_id").val(data.region_id);
//                    }
//
//                } else {
//                    console.log('rpc_wine_db returned FAILED');
//                    return;
//                }
//        }, "json");
//
//    }


    function load_select_options(select,key_name,value_name,json_array){
        //take json array and load select

        var $select = $("#"+select);//get the element for the select
        $select.empty();// Clear the old options
        
        if(json_array){//test if array is empty
            console.log('populate select with options');
            options = json_array;
            //append initial selection prompt
            $select.append("<option value='-1'>Select...</option>");
            for (index = 0; index < options.length; ++index) {
            option = options[index];
            $select.append("<option value='"+option[key_name]+"'>"+option[value_name]+"</option>");
            }
        }else{
            console.log('json_array is empty no options to populate select');
            $select.append("<option value='-1'>- No matches -</option>");
        }
    }


    function clear_vintage_session(url){
        //clear var_temp_vintage session from memory
        //and redirect to provided URL

        console.log('clear_vintage_session');
        console.log('redirect url='+url);

        $.post("/vintage/rpc_vintage.php", {
            action: 'clear_vintage_session'
            },
            function(data){
                if(data.success){
                    console.log('clear_vintage_session successful');
                    //redirect to provided url
                    if(url>""){
                      console.log('redirecting to...'+url);
                      window.location = (url);
                    }
                }

        }, "json");
    }


    function get_wine_session(callback){
        //get session variables for form
        console.log('get_wine_session()');
        $.post("/wine/rpc_wine_db.php", {
            action: 'get_wine_session'
        }, function(data){
            if(data.success){
                console.log('get_wine_session OK');
                console.log('wine_id='+data.wine_id);
                console.log('form_status='+data.form_status);
                
                load_wine_html();//load html content
                $('#status').val(data.status);//set global variables
                console.log('status = '+$('#status').val());
                wine_id = data.wine_id;
                //$('#wine_id').val(wine_id).trigger('change');
               
                if(typeof callback === 'function'){  //call callback if provided
                    console.log('call callback');
                    callback();
                };

            }else{
                var msg = 'critical error trying to retrieve wine details from session';
                alert(msg);
            }
        }, "json");
        
    }


    function update_page_status(status){
        //enable or disable controls based on status

        //1-new/add, 2-write,3-read,4-delete

        if(!status){
            //get status from field
            var status = $("#status").val();
        }

        console.log("fnc: update_page_status status="+status);

        if(status==1){ //add/new
           console.log('new wine form');
           $("#con_listBox_vintages").listBox("configStatus","add"); //disable listbox
           $(':input').removeAttr('disabled');
           $('.control').show();
           //active
           $('#btn_save,#btn_close,.btn_close,.btn_save').removeAttr('disabled').show();
           //inactive
           $('#btn_edit,#btn_delete').attr('disabled', true).hide();
           $("#status").val(status); //set form status
        }

        if(status==2){ //edit
           console.log("form status = 2");
           $("#con_listBox_vintages").listBox("configStatus","edit"); //set status of listbox
           $(':input').removeAttr('disabled');
           $('.control').show();
           //active
           $('#btn_save, #btn_close, #btn_delete').show();
           //inactive
           $('#btn_edit').attr('disabled', true).hide();
            //set focus
            $('#producer').focus();
            $("#status").val(status); //set form status
        }

        if(status==3 || status=='saved'){ //read-only
           console.log("form status = 3 read only");
           $("#con_listBox_vintages").listBox("configStatus","read");  //set status of listbox
           $(':input').attr('disabled', true);
           $('.control').hide();
           //active
           $('#btn_edit, #btn_close, #btn_debug').removeAttr('disabled').show();
           //inactive
           $('#btn_save, #btn_delete').hide();
           $("#status").val(status); //set form status
        }

        if(status==4){
           if($("#status").val() == 4){
               //delete has already been processed close page
               obj_page.close_page(); //close_page
           }
           console.log('redirect after deleting');
           $(':input').not(':button').val('').attr('disabled', true);
           $('.control').hide();
           //active
           $('#btn_close').removeAttr('disabled');
           //inactive
           $('#btn_save, #btn_delete, btn_edit').attr('disabled', true);
           $("#status").val(4); //set form status 
           setTimeout(function(){
               //location.reload(); //reload to clear form - will be closed by lines above
               obj_page.close_page();
           },500);
        }

    }
    
    
    function num_vintages(delete_wine){
        //checks for vintages before deleting if none added

        console.log('function: num_vintages');
        console.log('delete_wine= '+delete_wine);
         if($("#wine_id").val()>0){

            var wine_id = $("#wine_id").val();
            console.log('wine_id= '+wine_id);

            $.post("/wine/rpc_wine_db.php",{
                wine_id: wine_id,
                action: 'vintage_count'

                }, function(data) {
                    if(data.success){
                        console.log('rpc vintage_count successful');
                        console.log('vintage_count= '+data.vintage_count);

                       //if delete_wine is true - continue to delete wine function
                       if(delete_wine==true && data.vintage_count==0){
                           console.log('delete');
                           
                            $("#dialog_confirm_delete_text").text("Are you sure you want to Delete this Wine?");

                            $("#dialog-delete").dialog({
                                modal: true,
                                width: "410px",
                                buttons: {
                                    Delete: function() {
                                        //continue with save
                                        $(this).dialog('close');
                                        //call function to create new producer
                                        console.log('user confirmed to delete');
                                        //delete wine
                                            $.post("/wine/rpc_wine_db.php",{
                                                wine_id: wine_id,
                                                action: 'delete_wine'
                                                }, function(data) {
                                                    if(data.success){
                                                        console.log('rpc delete_vintage SUCCESS');
                                                        update_page_status(4); //clear form and reload to redirect
                                                    } else {
                                                        var msg = ('rpc delete_vintage failed with error: '+data.error);
                                                        $(".con_button_bar").notify(msg,{
                                                             position: "top left",
                                                             style: "msg",
                                                             className: "error",
                                                             arrowShow: false
                                                             }
                                                         );
                                                        console.log(msg);
                                                        return;
                                                    }
                                            }, "json");
                                    },
                                    Cancel: function() {
                                        //remain on page
                                        $(this).dialog('close');
                                        $('#wine_name').focus();
                                        console.log('user declined to delete wine');
                                    }
                                },
                                dialogClass: "clean-dialog",
                                position: { my: "left bottom", at: "right top", of: '#btn_delete' }
                            });


                       } else {
                            var msg = "Cannot delete this Wine as it has one or more associated Vintages";
                            $(".con_button_bar").notify(msg,{
                                 position: "top left",
                                 style: "msg",
                                 className: "warning",
                                 arrowShow: false
                                 }
                            );
                        }

                       //return data.vintage_count;
                    } else {
                        console.log('vintage_count - returned FAILED');
                        return;
                    }
            }, "json");

         } else {
            alert ("critical_error: wine_id not found");
         }
    }
    
    
    function open_vintage(index){
        //open selected vintage
        
        if(index){
            //call leave_page function
            obj_page.leave_page({
                dst_url: "/vintage/vintage.php",
                rtn_url:   this_page,
                dst_type: 'vintage',
                dst_action: 'open',
                object_id: index,
                page_action: 'leave'
            });
            
        }else{
            console.log('fn:open_vintage - no index provided');
        }
        
    }
    
    
    function add_vintage(wine_id, rtn_url){
        //add new vintage to wine
        
        if(wine_id > 0){
            //new wine - use this wine_id
            suppress_warning = true;
        }else{
           //existing wine
           var wine_id = $("#wine_id").val();
           suppress_warning = false;
        }
        
        console.log('selected wine_id='+wine_id);
        
        if(rtn_url){
            return_url = rtn_url;
        }else{
            return_url = this_page;
        }
        
        if(wine_id){
            obj_page.leave_page(
            {
                page_action: 'leave',
                dst_url: "/vintage/vintage.php",
                rtn_url: "/index.php",
                dst_type: 'vintage',
                dst_action: 'add',
                parent_id: wine_id,
                no_dirty: suppress_warning,
                
                put_dst_details: function()
                {
                    //put wine_id to session and add new vintage
                    $.post("/vintage/rpc_vintage.php", {
                        action: 'get_vintage_from_db',
                        wine_id: wine_id,
                        status: 1
                    }, function(data){

                        if(data.success){

                        } else {
                            console.log('fn:add_vintage - get_vintage_from_db failed. error='+data.error);
                        }

                    }, "json");
 
                }
            });
            
        }else{
            var msg = "Save Wine before adding a Vintage";
            console.log(msg);
            $(".con_button_bar").notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false
                }
           );
        } 
        
    }



    //____Actions & Events____


    //$(document).on('focus',":input",function() { //highlight input when it has focus
    //    //highlight active input field
    //    console.log('focus on '+$(this).attr('id'));
    //    $(":input").removeClass("highlight_input");
//
   //     if($(this).attr('disabled') !== true){
    //       $(this).removeClass("highlight_input").addClass("highlight_input");
    //    };

    //});
    

    $("#frm_wine").submit(function(){
        //save button - save wine to database after validation

        console.log('frm_wine submitted');
        //validate form
        console.log('validate form');
        $("#frm_wine").validate();

        if($("#frm_wine").valid()){
            console.log('form validation OK - continue to save');
            save_wine_to_db(false);
        }else{
            console.log('form validation FAILED');
        }

        return false;
    });


    
    function validate_page(){
        //validate page
        var def = $.Deferred();

        if( !$("#frm_wine").valid() ){ //validation failed do NOT continue
            console.log('frm_wine failed validation');
            $('#unsaved').dialog('close'); //close save_dialog if it is open to allow form validation to be corrected      
            def.reject('frm_wine failed validation');
        }else{
           console.log('frm_wine validated OK'); 
           def.resolve('frm_wine validated OK');
        };
        
        return def.promise();
    }
    

    function save_page(save_db){
        //function to save page
        //save_db (bool) - true will save to session and db, false to session only
        
        console.log('save_page function called save_db parameter: '+save_db);
        var def = $.Deferred();
         
        validate_page().done(function(response){
            console.log('Page validation successful. response = '+response);
          
            putWineServer(save_db).then(function(){ //put wine to server
                console.log("putWineServer successful");
                update_page_status(3);
                def.resolve(true);
            }).fail(function(response){ //save to db failed
                console.log("putWineServer failed: "+response);
                def.reject(false);
            });
                
        }).fail(function(response){
            console.log('Page validation failed. response = '+response);
        });
            
        return def.promise();
        
    }; //save_page
    
    
    /*** Events ***/
    
    $(document).on('click',"#btn_save",function(){
        //save wine
        console.log('btn_save...');
        save_page(true); //call save_page function

    });


    $(document).on('click','.btn_close',function(){
        //button class to close page
        obj_page.close_page();
    });


    $(document).on('click','#btn_delete',function(){
        //delete wine - after confirming it doesn't have valid vintages'
        console.log('delete wine');
        if(num_vintages(true)>0){
            console.log('Do NOT delete');
        } else {
            console.log('OK to delete');
        }

    });


    $(document).on('click','#btn_edit',function(){
        //edit button - enable write mode - form status = 3
        console.log('edit wine');

        update_page_status(2); //write

    });


    $(document).on('click',"#btn_add_producer",function(){
        console.log('show add producer dialog');
        add_producer();
    });


    $(document).on('click',".btn_add_vintage",function(event){
        var wine_id = $(this).data('wine_id');
        console.log('add new vintage to wine: '+wine_id);
        add_vintage(wine_id);
    });


    $(document).on('change',"#select_producer",function(){
        var text = $(this).find('option:selected').text();
        var index = $(this).find('option:selected').val();
        $("#producer_id").val(index);
        $("#producer").val(text);
        $("#select_producer").val(index);
        if(index > 0){ //producer selected
            producerSelected();
        }
    });


    $(document).on('change',"#select_country",function(){
        //handle country being selected
        var text = $(this).find('option:selected').text();
        var index = $(this).find('option:selected').val();
        fill_country_region('country',index,text);
    });


    $(document).on('change',"#select_region",function(){
        var text = $(this).find('option:selected').text();
        var index = $(this).find('option:selected').val();
        fill_country_region('region',index,text);
    });


    $(document).on('change',"#select_subregion",function(){
        var text = $(this).find('option:selected').text();
        var index = $(this).find('option:selected').val();
        fill_country_region('subregion',index,text);
    });
   
   
    $("#wine_id").on('change',function(){
        var wine_id = $('#wine_id').val();
        console.log('wine_id changed '+ wine_id);
        if(wine_id > 0){ //wine_id present
            console.log('wine_id present');
        }else{
            console.log('no wine_id present');
        }
    });




    //____Form Validation____

    function set_validation(){
        //validation - handled as function so that it can be called after html has loaded

        $("#frm_wine").validate({
            rules: {
                producer: {required: true},
                wine: {required: true},
                winetype_id: {number: true, required: true},
                location: {required: true},
                region_id: {number: true, required: true}
            },
            messages: {
                producer: {
                    required: "Producer is required"
                },
                wine: {
                    required: "Wine Name is required"
                },
                type: {
                    required: "Wine type is required"
                },
                location: {
                    required: "Region is required"
                }
            }, //messages
            //errorLabelContainer: $('#error_labels'),
            errorPlacement: function(error, element){}, //prevent error messages being displayed
            invalidHandler: function(event, validator){
                //validation failed
                console.log('Validation failed');
                var errorMsg = validator.errorList;
                var errorMsgCombined = "";
                for(var key in errorMsg){
                    errorMsgCombined = errorMsgCombined + errorMsg[key]['message'] + "\n";
                }

                $(".con_button_bar").notify(errorMsgCombined,{
                    position: "top left",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 3000
                });
                
            }
        });
    }
    


    //____AutoComplete Fields____
    
    

    
    function set_autocomplete(){
        //autocomplete _ handled as function so that it can be called after html has loaded
        console.log('set form autocomplete settings');
        var start = new Date().getTime();
        
        $("#producer").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "/wine/autocomplete_results.php",
                    dataType: "json",
                    data: {
                        term : request.term,
                        category : 'producer'
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            autoFocus:true,
            select: function( event, ui ) {
                console.log("Selected: " + ui.item.value + " id: " + ui.item.id + " input was " + this.value );
                console.log(ui);
                if(ui.item.id){
                    $("#producer_id").val(ui.item.id);
                    $("#select_producer").val(ui.item.id);
                    $("#producer").val(ui.item.value);
                    obj_page.set_is_dirty(true);
                    producerSelected();
                }
            },
            open: function(){
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) { //resolves two click issue in IOS
                    $('.ui-autocomplete').off('menufocus hover mouseover');
                }
            },
            change: function(e, ui) {
                console.log('Producer autocomplete change event');
                if (!ui.item) { //no match event clear producer
                    $(this).val("");
                    $("#producer_id").val(""); //clear producer_id
                    producerSelected();
                }
            },
            search: function(e, ui) {
                console.log('Producer autocomplete search event');
                if (!ui.item) { //no match event clear producer
                    $("#producer_id").val(""); //clear producer_id
                    producerSelected();
                }
            }
        });
    }
        



//_____Dialogs & Forms______

    function add_producer(){
        //open dialog and then add producer to db

        $.when( add_producer_dialog() ).then(
            function(status){ //done
                console.log('add_producer_dialog() - Done');
                console.log(status);
                //add_producer_db(status.producer);
            },
            function(status){ //rejected
                console.log('add_producer_dialog() - Rejected');
                $("#producer").focus();
            }
        );

    };
    
    
    function add_producer_dialog(){
        //open dialog to add new producer
        
        var def = $.Deferred();
        
        //dynamically determine dialog size
        var windowWidth = $(window).width();
        if(windowWidth > 500){
            dialogWidth = 370;
            dialogHeight = 225;
            positionMy = "left top";
            positionAt = "right top";
            positionOf = '#btn_add_producer'
        } else {
            dialogWidth = windowWidth-20;
            dialogHeight = 200;
            positionMy = "center top+20px";
            positionAt = "centre bottom";
            positionOf = "#top_nav";
        }  
        
        if( $("#add_producer").val() === $( "#producer" ).val() ){
            var automated_add = true; //set flag if triggered automatically on blur event
            console.log('automated_add');
        };
        
        $("#dialog-producer").dialog({
            modal: true,
            width: dialogWidth,
            height: dialogHeight,
            buttons: {
                OK: function() {
                    console.log('add_producer_dialog - response: OK'); 
                    var_response = {
                        button: 'OK',
                        producer: $("#add_producer").val()
                    };
                    add_producer_db($("#add_producer").val());
                    def.resolve(var_response);
                },
                Cancel: function() {
                    console.log('add_producer_dialog - response: Cancel');
                    var_response = {
                        button: 'Cancel'
                    };
//                    if(automated_add){ //user cancelled an automated add so clear all fields
//                        $("#producer").val(''); //clear values
//                        $("#producer_id").val('');
//                    }
                    $("#add_producer" ).val("");
                    $(this).dialog('close');
                    def.reject(var_response);
                }
            },
            dialogClass: "clean-dialog",
            position: { my: positionMy, at: positionAt, of: positionOf }
        });
        
        return def.promise();
        
    }
    

    function add_producer_db(producer, callback){
        //add producer to database
        console.log('add_producer_db');
        //var producer_name = $('#producer').val();
        
        if(!$.trim(producer)){
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


        $.post("/admin/rpc_ref_data.php", {
            value: producer,
            //action: 'add_producer_db'
            action: 'save_producer_db'
        }, function(data){
            if(data.success){
                console.log('add_producer_db successful id: '+data.id);
                $("#producer_id").val(data.id); //set producer values
                $("#producer").val(producer);
                
                 //reset and close dialog
                $( "#add_producer" ).val("");
                $( "#dialog-producer" ).dialog( "close" );
                
                $('#con_wines_from_producer').hide('fast',function(){ //new wine - close wine list div before showing notification otherwise it it orphaned
                    var msg = ('Add Producer Successful');
                    $(".con_button_bar").notify(msg,{
                        position: "top left",
                        style: "msg",   
                        className: "success",
                        arrowShow: false
                    });
                }); 
               
                if(typeof(callback) === "function"){
                    console.log('add_producer_db callback');
                    callback();
                }

            }else{
                $( "#add_producer" ).val(""); //reset dialog

                var msg = (data.error);
                $(".ui-dialog-buttonset").notify(msg,{
                   position: "top right",
                   style: "msg",
                   className: "warning",
                   arrowShow: false
                   }
               );
            }
        }, "json");
    }
    

    function add_new_country(){
        //open dialog to add new country
        console.log('add_new_country');
        $('#add_country_id').val(-1); //set hidden id field to neg to identify as new
        $('#country_text').val(null);
        $('#flag_file').val(null);
        //open dialog
        $("#dialog-country").dialog( "open" ); //open dialog
        $('#country_text').focus();
    }
    
    
    function edit_country(index, data){
        console.log("edit country index="+index);
        if(index <= 0){
            console.log('edit_county - incomplete parameters');
        }
        var country = data.listBox_values[0];
        var flag_file = data.listBox_values[2];
        $('#add_country_id').val(index);//fill form fields
        $('#country_text').val(country);
        $('#flag_file').val(flag_file);
        $("#country_dialog_title" ).text( "Edit Country" ); //update dialog title
        $("#dialog-country").dialog( "open" ); //open dialog
        $('#country_text').focus();

    }
    

    $( "#dialog-country" ).dialog({
        autoOpen: false,
        height: 310,
        width: 325,
        modal: true,
        buttons: {
                OK: function() {
                    var country_text = $('#country_text').val();
                    var country_flag = $('#flag_file').val();
                    var country_id = $('#add_country_id').val();
                    add_country_to_db(country_text, country_flag, country_id, function(){
                        $('#dialog-country').dialog( "close" );
                    });
                },
                Cancel: function() {
                        $(this).dialog( "close" );
                }
        },
        dialogClass: "clean-dialog",
        position: { my: "center bottom", at: "center top", of: '#con_listBox_location_footer' },
        close: function() {
            $('#country_text').val('');
        }
    });
    
    
    function add_country_to_db(country, flag, country_id, callback){
        //add country to db
        console.log('add_country_to_db country = '+country);
        
        if(!$.trim(country)){
            msg = "Country name cannot be blank";
            $('#dialog-country').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
            return false;
        } 
        
        $.post("/admin/rpc_ref_data.php",{
            action: 'save_country_db',
            country_text: country,
            country_flag: flag,
            country_id: country_id
            }, function(data) {
                if(data.success){
                    console.log('add_country_to_db successful');
                    $('#country').val(country);//update form with newly added value
                    $('#country_id').val(data.country_id);
                    var msg = ('Save country successful');
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",   
                        className: "success",
                        arrowShow: false
                        }
                    );
            
                    $("#con_listBox_location").listBox('refresh',data.country_id); //refresh listbox and show new item
                    
                    if($.isFunction(callback)){
                        callback();
                    }
                    
                } else {
                    var msg = data.error;
                    console.log(msg);
                    $('#dialog-country').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
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
    
    
    $("#dialog-country").keydown(function (event) { //set default enter behaviour
        console.log("return key keydown event detected");
        if (event.keyCode == 13) {
            $(this).parent()
                   .find("button:eq(1)").trigger("click");
            return false;
        }
    });
    
    
    
    $('#con_listBox_location').keydown(function(e){
        console.log("keydown event detected");
        if (e.keyCode == 39) {      
            $(".move:focus").next().focus();
            $(this).closest('div').next().find('.listBox_row').first().focus();

        }
        if (e.keyCode == 37) {      
            $(".move:focus").prev().focus();

        }
    });
    

    function add_new_region(parent_id){
        //open dialog to add new country
        console.log('add_new_region function');
        $('#region_country_id').val(parent_id); //set parent_id
        $('#region_text').val(''); //clear form
        $('#add_region_id').val(''); //clear form
        $("#region_dialog_title" ).text( "Add Region" ); //update dialog title
        $("#dialog-region").dialog( "open" ); //open dialog
        $('#region_text').focus(); //set focus on text field
    }
    
    
    function edit_region(index, value, parent_index){
        //edit region
        console.log('edit_region index='+index);
        if(index <= 0){ 
            return false;
            console.log('edit_region - incomplete parameters');
        }
        $('#add_region_id').val(index);
        $('#region_text').val(value);
        $('#region_country_id').val(parent_index);
        $("#region_dialog_title" ).text( "Edit Region" ); //update dialog title
        $("#dialog-region").dialog( "open" );
        $('#region_text').focus();
    };
  

    $( "#dialog-region" ).dialog({
            autoOpen: false,
            height: 225,
            width: 325,
            modal: true,
            buttons: {
                    OK: function() {
                        var region_id = $('#add_region_id').val();
                        var region_text = $('#region_text').val();
                        var country_id = $('#region_country_id').val();
                        add_region_to_db(region_text, region_id, country_id, function(){
                            $('#dialog-region').dialog( "close" );
                        });
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            },
            dialogClass: "clean-dialog",
            position: { my: "left bottom", at: "left top", of: '#con_listBox_location_btn_add' },
            close: function() {
                $('#region_text').val('');
                $('#dialog-region').removeData('id');
                $('#dialog-region').removeData('parent_id');
            }
    });
    
    
    $("#dialog-region").keydown(function (event) { //set default enter behaviour
        if (event.keyCode == 13) {
            $(this).parent()
                   .find("button:eq(1)").trigger("click");
            return false;
        }
    });
    

    function add_region_to_db(region, region_id, country_id, callback){
        //add region to  db
        console.log('add_region_to_db region = '+region);
        
        if(!$.trim(region)){
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
        
        $.post("/admin/rpc_ref_data.php",{
            action: 'save_region_db',
            region_text: region,
            region_id: region_id,
            country_id: country_id
            }, function(data) {
                if(data.success){
                    console.log('add_region_to_db successful');
                    $('#region').val(region);//update form with newly added value
                    $('#region_id').val(data.region_id);
                    var msg = ('Save region successful');
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",   
                        className: "success",
                        arrowShow: false
                        }
                    );
            
                    $("#con_listBox_location").listBox('refresh',data.region_id); //refresh listbox and show new item
                    
                    if($.isFunction(callback)){
                        callback();
                    }
                } else {
                    var msg = data.error;
                    console.log(msg);
                    $('#dialog-region').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
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


    function add_new_subregion(id, parent_id){
        //open dialog to add new subregion
        console.log('add_new_subregion function');
        $('#subregion_text').val(''); //clear form text
        $('#subregion_region_id').val(id); //set region_id on dialog hidden field
        $("#subregion_dialog_title" ).text( "Add Subregion" ); //update dialog title
        $('#dialog-subregion').dialog( "open" ); //open dialog
        $('#subregion_text').focus(); //set focus
    }
    
    
    function edit_subregion(index, value, parent_index){
        console.log('edit_subregion='+index);
        if(index <= 0){
            console.log('nothing selected');
            return false;
        }
        $('#add_subregion_id').val(index); //fill dialog form fields
        $('#subregion_text').val(value);
        $('#subregion_region_id').val(parent_index);
        $("#subregion_dialog_title" ).text( "Edit Subregion" ); //update dialog title
        $("#dialog-subregion").dialog( "open" );
        $('#subregion_text').focus();

    };

    $( "#dialog-subregion" ).dialog({
            autoOpen: false,
            height: 225,
            width: 325,
            modal: true,
            buttons: {
                    OK: function() {
                        subregion_text = $('#subregion_text').val();
                        subregion_id = $('#add_subregion_id').val();
                        region_id = $('#subregion_region_id').val();
                        add_subregion_to_db(subregion_text, region_id, subregion_id, function(){
                            $('#dialog-subregion').dialog( "close" );
                        });
                    },
                    Close: function() {
                        $('#dialog-subregion').dialog( "close" );
                    }
            },
            dialogClass: "clean-dialog",
            position: { my: "left bottom", at: "right top", of: '#con_listBox_location_btn_add' },
            close: function() {
                $('#subregion_text').val(''); //clear dialog form fields
                $('#add_subregion_id').val('');
                $('#subregion_region_id').val('');
            }
            
    });
    
    
    $("#dialog-subregion").keydown(function (event) { //set default enter behaviour
        if (event.keyCode === 13) {
            $(this).parent()
                   .find("button:eq(1)").trigger("click");
            return false;
        }
    });


    function add_subregion_to_db(subregion_text, region_id, subregion_id, callback){
        //add subregion to  db

        console.log('add_subregion_to_db subregion: '+subregion_text+' region_id: '+region_id);
        
        if(!$.trim(subregion_text)){
            msg = "Subregion name cannot be blank";
            $('#dialog-subregion').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
            return false;
        }
        
        $.post("/admin/rpc_ref_data.php",{
            action: 'save_subregion_db',
            subregion_text: subregion_text,
            region_id: region_id,
            subregion_id: subregion_id
            }, function(data) {
                if(data.success){   
                    var msg = ('Save subregion successful');
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{ //show message on location dialog because subregion dialog has closed
                        position: "top left",
                        style: "msg",   
                        className: "success",
                        arrowShow: false
                        }
                    );
                    
                    $("#con_listBox_location").listBox('refresh',data.subregion_id); //refresh listbox and show new item
            
                    if($.isFunction(callback)){
                        callback();
                    }
         
                } else {
                    var msg = data.error;
                    console.log(msg);
                    $('#dialog-subregion').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "warning",
                        arrowShow: false,
                        autoHideDelay: 3000
                        }
                    );
                 
                }
        }, "json");

    };
    




    $("#frm_add_subregion").validate({
        rules:
        {
            new_subregion:
            {
                required: true,
                remote: {
                    url: "/admin/rpc_duplicate_subregion.php",
                    type: "post",
                    data: {
                        region_id: function(){
                            return $('#region_id').val();
                        }
                    }

                }

            }
       },//rules
       messages:
       {
            new_subregion:
            {
                required: "Enter name of Subregion",
                remote: "Subregion already added"
            }
      },
      errorPlacement: function(error, element) {
                error.insertAfter($('#error_message_subregion'));
            }
    });


    $("#dialog-form-add-subregion").keydown(function (event) { //set default enter behaviour
        if (event.keyCode == 13) {
            $(this).parent()
                   .find("button:eq(0)").trigger("click");
            return false;
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



    
    function initialise_listBox_location(){
        
        //determine size based on window dims
        var width = $(window).width();
        var height = $(window).height();
        console.log('window.width: '+width);
        console.log('window.length: '+height);
        if(width<450){
            width = '90%';
        } else{
            width = '450px';
        }
        
        //setup region listBox
        $("#con_listBox_location").listBox({
            title: "Region",
            width: '100%',
            height: $(window).height() - 250,
            listContent: '/admin/rpc_listBox_location_html.php',
            showTitle: false,
            showFilter: true,
            showBorder: false,
            showShadow: false,
            showRoundedCorners: false,
            addClass: 'listBox_large_theme',
            clickAdd: function(event, data){
                add_location(data);
            },
            clickRemove: function(event, data){
                console.log('clickRemove Event');
                delete_location(data);
            },
            clickEdit: function(event, data){
                console.log('clickEdit Event');
                edit_location(data);
            },
            clickSelected: function(event, data){
                console.log('clickSelected Event');
                objLocation = data; //persist data in global object
                updateListBoxLocation(data);
            },
            clickFilterClear: function(event, data){
                console.log('clickFilterClear Event');
                updateListBoxLocation(data);
            }
        });
    }
    
    
    function add_location(data){
        //add_location type selector function
        //determine what type of record is being added then call specific function
        
        var id = data.listBox_id;
        var parent_id = data.listBox_parent_id;
        var level = data.listBox_level;
        
        console.log("add_location data: id="+id+" parent_id="+parent_id+" parent_type="+level);
              
        if(typeof level === 'undefined'){
            level = 0;//add country
        } 
        
        var el = $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first'); //find first buttonpane to prevent notification on both dialogs
        
        //determine 'what to add
        switch(level){
            case 1:
                console.log('add Region');
                add_new_region(id);
                break;
            case 2:
                console.log('add Subregion');
                add_new_subregion(id);
                break;
            case 3:
                msg = 'Cannot add child to subregion';
                console.log(msg);
                $(el).notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "warning",
                    arrowShow: false,
                    autoHideDelay: 3000
                    }
                );       
                break;
            default:
                console.log('add Country');
                add_new_country();
        }
    
    }
    
    
    function edit_location(data){

        var id = data.listBox_id;
        var parent_id = data.listBox_parent_id;
        var level = data.listBox_level;
        var value = data.listBox_values[0];
        
        if(typeof level === 'undefined'){
            level = 0; //nothing selected
        }
        
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
        
    }
    
    
    function delete_location(data){
        //listbox delete location    
        console.log('delete_location');
        console.log(data);
        
        if(jQuery.isEmptyObject(data)){ //check for a selected object
            var msg = "Nothing selected to delete";
            var el = $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first'); //find first buttonpane to prevent notification on both dialogs
            console.log(msg);
            $(el).notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 2000
                }
            );
            return false;
        }
        
        var level = data.listBox_level;
        var id = data.listBox_id;
        var value = data.listBox_values[0];
        
        if(typeof level === 'undefined'){
            level = 0;//nothing selected
        }
        
        
        if($("#con_listBox_location").listBox("hasChildren")){ //check if row has children
            var msg = "Location cannot be deleted whilst it contains other locations";
            var el = $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first'); //find first buttonpane to prevent notification on both dialogs
            console.log(msg);
            $(el).notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );                  
            return false; 
        }

        switch(level){ //determine 'what to delete'
            case 1:
            console.log('delete Country');
            delete_country(id, data);
            break;
            case 2:
            console.log('delete Region');
            delete_region(id, data);
            break;
            case 3:
            console.log('delete Subregion');
            delete_subregion(id, data);
            break;
            default:
            console.log('nothing selected');
        }
        
    }
    
    
    function delete_country(index, event_data){
        //delete country with provided index
        
        if(index <= 0){
            console.log('delete_country - no index provided to delete');
            return false;
        }
        
        $.post("/admin/rpc_ref_data.php", {
                action: 'delete_country',
                country_id: index
            }, function(data){
                if(data.success){
                    $('#'+event_data.listBox_row_id).remove(); //hide deleted element, avoids the need to Refresh
                    hideLocationDialogButtonOK(); //prevent selection of deleted item by removing OK button
                    cleanUpLocationDelete(1,index); //remove location details on wine form if they included this country
                    var msg = "Delete country successful";
                    console.log(msg);
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "success",
                        arrowShow: false,
                        autoHideDelay: 3000
                        }
                    );

                }else if(data.error == 'has_children'){
                    var msg = 'Country is associated with one or more wines';
                    console.log(msg);
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "warning",
                        arrowShow: false,
                        autoHideDelay: 3000
                        }
                    );

                } else { //some other error
                    var msg = data.error;
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
  
    }
    
    
    function delete_region(index, event_data){
        //delete region with provided index
        var msg;
        
        if(index <= 0){
            console.log('delete_region - no index provided to delete');
            return false;
        }
        
        $.post("/admin/rpc_ref_data.php", {
                action: 'delete_region',
                region_id: index
            }, function(data){
                if(data.success){
                    $('#'+event_data.listBox_row_id).remove(); //hide deleted element, avoids the need to Refresh
                    hideLocationDialogButtonOK() //prevent selection of deleted item by removing OK button
                    cleanUpLocationDelete(2, index) //clean up wine form on deletion
                    msg = "Delete region successful";
                    console.log(msg);
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "success",
                        arrowShow: false,
                        autoHideDelay: 3000
                        }
                    );

                }else if(data.error == 'has_children'){
                    var msg = 'Region is associated with one or more wines';
                    console.log(msg);
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "warning",
                        arrowShow: false,
                        autoHideDelay: 3000
                        }
                    );

                } else { //some other error
                    var msg = data.error;
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
  
    }

        
    function delete_subregion(index, event_data){
    //delete subregion with provided index
        
        if(index <= 0){
            console.log('delete_subregion - no index provided to delete');
            return false;
        }
        
        $.post("/admin/rpc_ref_data.php", {
                action: 'delete_subregion',
                subregion_id: index
            }, function(data){
                if(data.success){
                    $('#'+event_data.listBox_row_id).hide(); //hide deleted element, avoids the need to Refresh
                    hideLocationDialogButtonOK(); //prevent selection of deleted item by removing OK button
                    cleanUpLocationDelete(3, index) //clean up wine form on deletion
                    var msg = "Delete subregion successful";
                    console.log(msg);
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "success",
                        arrowShow: false,
                        autoHideDelay: 3000
                        }
                    );

                }else if(data.error == 'has_children'){
                    var msg = 'Subregion is associated with one or more wines';
                    console.log(msg);
                    $('#dialog-location-select').siblings('.ui-dialog-buttonpane:first').notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "warning",
                        arrowShow: false,
                        autoHideDelay: 3000
                        }
                    );

                } else { //some other error
                    var msg = data.error;
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
  
    }

    
    function updateListBoxLocation(data){
        console.log('updateListBoxLocation');
        //Set status of buttons
        console.log(data);
        if(data.listBox_level >= 2){
            console.log('valid Region selected - enable OK button');
            showLocationDialogButtonOK();
        }else{
            hideLocationDialogButtonOK();
        }
        
    }
    
    
    function updateRegion(){
        //update country, region, subregion after listBox selection
        
        var data = objLocation; //global object from listBox selection
        var category = data.listBox_values[1];
        console.log('updateRegion()');
        
        if(category == 'region'){
            console.log('region selected');
            //update region input with id, lookup country
            var regionName = data.listBox_values[0];
            var regionIndex = data.listBox_values[2];
            var countryName = data.listBox_values[3];
            var countryIndex = data.listBox_values[4];
            var locationName = regionName + ", " + countryName;
 
            objLocation = data; //set global object
            console.log('country_id ='+countryIndex);
            console.log('region_id ='+regionIndex);
            
            $('#location').val(locationName);
            $("#country_id").val(countryIndex);
            $("#region_id").val(regionIndex);
            $('#is_dirty').val(1); //flag form as dirty
       
        }
                    
        if(category == 'subRegion'){
            console.log('subregion selected')
            var subregionName = data.listBox_values[0];
            var subregionIndex = data.listBox_values[2];
            var countryName = data.listBox_values[3];
            var countryIndex = data.listBox_values[4];
            var regionName = data.listBox_values[5];
            var regionIndex = data.listBox_values[6];
            var locationName = subregionName + ", " + regionName + ", " + countryName;

            objLocation = data; //set global object
            
            $('#location').val(locationName);
            $("#country_id").val(countryIndex);
            $("#region_id").val(regionIndex);
            $("#subregion_id").val(subregionIndex);
            $('#is_dirty').val(1); //flag form as dirty
        }
        
    }

    
    $(document).on('click','#location', function(){
        showLocationDialog();
    });


    function showLocationDialog(){
        
        objLocation = null; //reset location object
        
        var width = $(window).width();
        var height = $(window).height();
        if(width<415){ //iPhone 8 Plus
            width = '90%';
        } else{
            width = '415px'; //all other bigger devices
        }
        
        $("#dialog-location-select").dialog({
            modal: true,
            width: width,
            buttons: {
                Close: function() {
                    //lose changes
                    $(this).dialog('close');
                }
            },
            dialogClass: "clean-dialog",
            position: { my: "left top", at: "left top", of: '#btn_main_menu' }
        });
        
        
        
    }
    
    
    function showLocationDialogButtonOK(){
        
        console.log('showLocationDialogButtonOK');
        
        $("#dialog-location-select").dialog( "option", "buttons", 
          
            {
                OK: function() {
                    //save changes
                    disabled: true;
                    updateRegion(); //check data collected from the location listBox and update form
                    $(this).dialog('close');

                },
                Close: function() {
                    //lose changes
                    $(this).dialog('close');
                }
            }
          
        );
    }
    
    
    function hideLocationDialogButtonOK(){
        
        console.log('hideLocationDialogBUttonOK');
        
        $("#dialog-location-select").dialog( "option", "buttons", 
          
            {
                Close: function() {
                    //lose changes
                    $(this).dialog('close');
                }
            }
          
        );
    }
    
    
    $(document).on('click','#btn_clear_location', function(){
        clearLocation();
        if($('#status').val()>1){ //location has been removed for an existing wine so mark form as dirty
            $('#is_dirty').val(1);
        }
    });
    
    
    function clearLocation(){
        //clear all location details
        $("#location").val('');

        $("#select_country").val('');
        $("#country_id").val('');
        $("#select_region").val('');
        $("#region_id").val('');
        $("#select_subregion").val('');
        $("#subregion_id").val('');
        
    }
    
    
    function cleanUpLocationDelete(level, index){
        //clear location details on wine form if they include a deleted index
       switch(level){
            case 1: //country
                if($('#country_id').val() === index){
                    clearLocation();
                }
                break;
            case 2: //region
                if($('#region_id').val() === index){
                    clearLocation();
                }
                break;
            case 3:
                if($('#subregion_id').val() === index){
                    clearLocation();
                }    
                break;
        }
        
    }
    
    
    $(document).on('click','.wine_panel_toggle',function(){
        //toggle vintage panel
        console.log('click .wine_panel_toggle');
        var wine_id = ($(this).closest(".wine_accordian").attr('id').replace("wine_accordian_", ""));
        toggle_vintage_panel(wine_id);
        
    });
    
    
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
    
    
    $(document).on('click','.vintage_panel_toggle',function(){
        var vintage_id = ($(this).closest(".vintage_accordian").attr('id').replace("vintage_accordian_", ""));
        console.log('vintage_id: '+vintage_id);
        if($(this).hasClass('.ignore_vintage_panel_toggle')){
            console.log('event cancelled by .ignore_vintage_panel_toggle');
            return false;
        }
        toggle_vintage_details_panel(vintage_id);
    });
    
    
    function toggle_vintage_details_panel(vintage_id,duration){
        //toggle vintage details panel - open or close
        console.log('toggle_vintage_details_panel');
        $panel = "#vintage_details_"+vintage_id;
        $($panel).slideToggle(duration);
        arrow_id = '#arrow_indicator_vintage_'+vintage_id;
        $(arrow_id).toggleClass('arrow_down');
    }
    
    
   
});
</script>
</html