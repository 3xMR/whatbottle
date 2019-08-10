<?php
/*
 * Site details
 * Screen Size optimisation: 1280x760
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
require_once("$root/includes/css.inc.php");
echo "<title>What Bottle?</title>"; //page title

//include all script libraries
require_once("$root/includes/script_libraries.inc.php");
?>

</head>

<body>

    <div id="dialog-producer" class="hidden" title="Add Producer?">	
        <h2 style="margin-bottom:15px;"> Add New Producer</h2>
        <div class="input-main-label">
            <p>Producer</p>
        </div>
        <div class="input-main">
            <input type="text" style="width:100%;" id="add_producer"></input>
        </div>
        <br/>
    </div>
    
    <div id="dialog-country" class="hidden" title="Add Country?">	
        <h2 style="margin-bottom:15px;"> Add New Country</h2>
        <div class="input-main-label">
            <p>Country</p>
        </div>
        <div class="input-main">
            <input type="text" id="add_country" ></input>
        </div>
        <br/>
    </div>
    
    <div id="dialog-region" class="hidden" title="Add Region?">	
        <h2 style="margin-bottom:15px;"> Add New Region</h2>
        <div class="input-main-label">
            <p>Region</p>
        </div>
        <div class="input-main">
            <input type="text" style="width:100%;" id="add_region" ></input>
        </div>
        <br/>
    </div>
    
    <div id="dialog-subregion" class="hidden" title="Add Subregion?">	
        <h2 style="margin-bottom:15px;"> Add New Subregion</h2>
        <div class="input-main-label">
            <p>Subregion</p>
        </div>
        <div class="input-main">
            <input type="text" style="width:100%;" id="add_subregion" ></input>
        </div>
        <br/>
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


<?php


//page
echo "<div class=\"page_container\">";

    //header
    require_once("$root/includes/nav/topheader.php");


    //wine_form
    echo "<div class=\"con_single_form\" >";
        echo "<input type=\"hidden\" name=\"status\" id=\"status\"/>"; //hidden field to store form editStatus

        //Title bar
        echo "<div class=\"con_title_bar\" >";
            //wine name
            echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px;\" >";
                echo "<div style=\"float:left; width:3em;\" >";
                    echo "<img style=\"width:2.5em; height:2.5em;\" src=\"/images/wine_flat_grey_64.png\"  >";
                echo "</div>";
                echo "<div style=\"width:auto; float:left; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >Wine Details</h1>";
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

        //Column 2
        //echo "<div class=\"con_column_2_2\" >";
        //    echo "<div style=\"float:left; margin-top:5px; margin-left:10px;\" id=\"con_listBox_vintages\" >";
        //        // vintages/rpc_listBox_vintages_html.php
         //   echo "</div>"; //vintages listBox
        //echo "</div>";

        
        echo "<div id=\"error_labels\" class=\"clear\" style=\"padding:10px; display:none;\" >";
            //empty
        echo "</div>";

        //Button Bar
        echo "<div class=\"con_button_bar\" >";
            if(is_authed()){
                echo "<input type=\"button\" name=\"btn_save\"  id=\"btn_save\" value=\"Save\" class=\"submit form_input\" />";
                echo "<input type=\"button\" name=\"btn_edit\" id=\"btn_edit\" value=\"Edit\" />";
                echo "<input type=\"button\" name=\"btn_delete\" id=\"btn_delete\" value=\"Delete\" />";
            }
            echo "<input type=\"button\" name=\"btn_close\" id=\"btn_close\" value=\"Close\" class=\"btn_close form_input\" />";

            //if($_SESSION['var_wine_temp']['status']==1){ //add vintage check box
            //    echo "<input type=\"checkbox\" style=\"margin-left:20px;\" value=\"Add Vintage\" name=\"add_vintage\" id=\"chk_add_vintage\" checked>";
            //    echo "<label for=\"add_vintage\"> Add Vintage</label>";
            //}

        echo "</div>";


        //clear page_container
        echo "<div class=\"clear\"></div>";

        //echo "</div>"; //con_single_form_inner
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
    
    //FIX: Merchant autocomplete results is using escape character for '&' in Marks & Spencer

    //____Global variables____

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
            console.log('html loaded');
            initialise_page(); //initialise page and set control status
       });
    }
    
    
    function initialise_page(){
        /* function to intialise page as html load means form
        *  will load after parent page has completed loading
        */

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
        initialise_dropdown();
        loadVintageListbox($('#status').val()); //load vintage listbox
        update_page_status(); //update control states
        $('#wine_name').focus(); //set initial focus
    
    }
    

    //____functions____

   
    function initialise_dropdown(){
        //populate dropdown boxes

        load_region_select($("#country_id").val()); //populate region select
        load_subregion_select($("#region_id").val()); //populate subregion select

    }
    
    
    function loadVintageListbox(status){ //setup vintages listBox
      //run as function to allow page status to de defined first and then
      //load listBox knowing editStaus
        
        $("#con_listBox_vintages").listBox({
            title: "Vintages",
            width: 250,
            height: 250,
            scrollTo: true,
            showTitle: true,
            showFooter: true,
            showBorder: true,
            showRoundedCorners: false,
            addClass: 'listBox_flat_theme',
            listContent: '/vintage/rpc_listBox_vintages_html.php',
            editStatus: status, //add(1),edit(2),read(3)
            clickAdd: function(event, data){
                add_vintage();
            },
            clickEdit: function(event, data){
                var index = data.listBox_id;
                open_vintage(index);
            }

        });
        
    }


    function putWineServer(save_db){
        //put wine record to server
        //save_db (boolean) - true to commit data to db as well as session
        
        var deferred = $.Deferred(); //promise required for page_control
        
        //serialize form data
        var var_fields = $(":input").serializeArray();
        var json_array = JSON.stringify(var_fields);
        console.log("serialize form data");
        console.log(json_array);
        
        //post data to server and when completed return promise
        $.when(post_data(save_db)).then(function(data){ 
            //promise returned resolve
            console.log("post_data to server returned resolved");
            console.log(data);
            deferred.resolve(true);
            
        }, function(data){
            //promise returned reject
            deferred.reject(data);
            console.log("post_data to server returned reject");
            console.log(data);
        });


        function post_data(save_db){
            
            var def_post_data = $.Deferred();

            $.post("/wine/rpc_wine_db.php", {
                    action: 'put_wine_session',
                    json_array: json_array,
                    save_db: save_db
                }, function(data){
                    console.log(data);
                    if(data.success){
                        console.log('put_wine_session OK');

                        if(data.save_type){
                            console.log('db save_type = '+data.save_type);
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
                            //if(data.save_type==='db insert' && $("#chk_add_vintage:checked").val() !== undefined){
                            if(data.save_type==='db insert'){
                                //new wine - checkbox selected so create new vintage
                                console.log('add vintage - redirect to vintage page');
                                var rtn_url = "/index.php"; //set rtn url to index rather than this_page
                                setTimeout(function(){ //timeout to allow success msg to be seen
                                    add_vintage(rtn_url);  //add new vintage function
                                },500);
                            } 
                            
                            def_post_data.resolve(true);

                        } else {
                            //no save_type returned from server side function
                            console.log('put_wine_to_session no save_type returned');
                            def_post_data.reject(data);
                        }

                    } else {
                        console.log('put_wine_session failed with error = '+data.error);
                        def_post_data.reject(data);
                        
                        //display message
                        $(".con_button_bar").notify("Save Failed with error: "+data.error,{
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



    function check_producer(){
        //Check if producer is new and prompt user before adding
        console.log('check_producer...');
        
        var def = $.Deferred();
        
        var producer_name = $('#producer').val();
        
        if( producer_name === "" ){ 
            $('#producer_id').val("");//field is empty - clear key
            console.log('no producer value entered');
            def.resolve();
        }else{
            $.when( producer_duplicate_check(producer_name) ).then(function(data){
                console.log('producer_duplicate_check data: '+data);
                def.resolve();
            });
        }
        
        return def.promise();

    }; 
    
    
    function producer_duplicate_check(producer_name){

            var def = $.Deferred();
            console.log('producer_duplicate_db = '+producer_name);

            if(producer_name == ""){
                var msg = 'no producer_name provided - aborted!';
                console.log(msg);
                var response = {
                    success: false,
                    error: msg
                };
                def.reject(msg);
            }

            //producer_name = encodeURIComponent(producer_name);

            $.post("/vintage/rpc_validate.php", {
                field: 'producer', 
                value: producer_name
            },
            function(xml) {
                
                if( $("status",xml).text() === "True" ){ //if status is true - then producer is already added
                    //producer name exists - set key_country
                    //key_producer = $("producer_key",xml).text();
                    //$('#producer_id').val(key_producer);
                    console.log('producer value matches db entry');

                    def.resolve(xml);

                } else { //new producer - clear key field
                    console.log('Identified as new producer');
                    $('#producer_id').val(""); //clear id field to remove any remnants
                    $("#add_producer").val(producer_name);
                    add_producer();
                }
                
          }, "xml");

        return def.promise();
    };
         
 
        


   function get_wine_db(){
        //get wine details from DB

        $.post("/wine/rpc_wine_db.php",{
            wine_id: window.wine_id,
            action: 'get_from_db'

            }, function(data) {
                if(data.success){
                    console.log('get_from_db successful');

                    console.log(data.country);
                    console.log(data.country_id);
                    $("#country").val(data.country);
                    $("#country_id").val(data.country_id);

                    //fill region
                    if(data.region_id){
                        $("#region").val(data.region);
                        $("#region_id").val(data.region_id);
                    }

                } else {
                    console.log('rpc_fill_country returned FAILED');
                    return;
                }
        }, "json");

    }


    function fill_country_region(field,index,text){
    //auto populate country and region fields

        console.log('fill_country_region - field='+field+' index='+index+' text='+text);
        obj_page.set_is_dirty(true);

        if(field==='country'){
            //clear region and subregion
            console.log('country changed - clear region and subregion');
            $("#region").val('');
            $("#region_id").val('');
            $("#subregion").val('');
            $("#subregion_id").val('');

            //populate fields
            if(index>0){
                $("#country").val(text);
                $("#country_id").val(index);
                $("#select_country").val(index);
            }else{
                //index suggests no valid selection - clear all fields
                $("#country").val('');
                $("#country_id").val('');
                $("#select_country").val(-1);
            }

            //update region select list
            load_region_select(index);

        }
        
        if (field==='region'){
            //clear subregion
            console.log('region changed - clear subregion');
            $("#subregion").val('');
            $("#subregion_id").val('');
            
            //get country values and populate
            $.post("/wine/rpc_wine_db.php",{
                action: 'get_country_for_region',
                id: index
                }, function(data) {
                    if(data.success){
                        console.log('get_country_for_region successful');
                        console.log(data);
                        
                        //fill country details
                        $("#country").val(data.country);
                        $("#country_id").val(data.country_id);
                        $("#select_country").val(data.country_id);
                     
                         //fill region
                        $("#region").val(text);
                        $("#region_id").val(index);
                        $("#select_region").val(index);
                        
                        //refresh dropdowns
                        load_region_select(data.country_id);
                        load_subregion_select(index);

                    } else {
                        var msg = data.error;
                        console.log(msg);
                        return;
                    }
                }, "json");

        }
        
        if (field==="subregion"){
            
            //get country and region details  
            $.post("/wine/rpc_wine_db.php",{
                action: 'get_region_for_subregion',
                id: index
                }, function(data) {
                    if(data.success){
                        console.log('get_region_for_subregion successful');
                        console.log(data);
                        //fill country details
                        $("#country").val(data.country);
                        $("#country_id").val(data.country_id);
                        $("#select_country").val(data.country_id);
                        //fill region details
                        $("#region").val(data.region);
                        $("#region_id").val(data.region_id);
                        $("#select_region").val(data.region_id);
                        //fill subregions
                        $("#subregion").val(text);
                        $("#subregion_id").val(index);
                        $("#select_subregion").val(index);
                        //refresh dropdowns
                        load_region_select(data.country_id);
                        load_subregion_select(data.region_id);
                    } else {
                        var msg = data.error;
                        console.log(msg);
                    }
                }, "json");
                


            }
        
    }


    function load_region_select(country_id){
        //populate region select control
        console.log('function: load_region_select');

        $.post("/wine/rpc_wine_db.php", {
            action: 'get_regions',
            country_id: country_id
            }, function(data){
                if(data.success){
                    console.log('get Region data successful');
                    //update region select
                    load_select_options('select_region','region_id','region',data.json_array,true);

                    //call callback if provided
                    if(typeof callback === 'function'){
                        console.log('Call callback');
                        callback();
                    };

                }else{
                    console.log('get region select fill failed with error='+data.error);
                }
        }, "json");
    }


    function load_subregion_select(region_id){
    //populate region select control
        console.log('function: load_subregion_select');

        $.post("/wine/rpc_wine_db.php", {
            action: 'get_subregions',
            region_id: region_id
            }, function(data){
                if(data.success){
                    console.log('get subregion data successful');
                    //update subregion select
                    load_select_options('select_subregion','subregion_id','subregion',data.json_array,true);

                    //call callback if provided
                    if(typeof callback === 'function'){
                        console.log('Call callback');
                        callback();
                    };

                }else{
                    console.log('load_subregion_select failed with error = '+data.error);
                }
        }, "json");

    }


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
            $('#wine_name').focus();
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
    
    
    function add_vintage(rtn_url){
        //add new vintage to open wine
       
        var wine_id = $("#wine_id").val();
        
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
                rtn_url: return_url,
                dst_type: 'vintage',
                dst_action: 'add',
                parent_id: wine_id,
                
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


    $(document).on('focus',":input",function() { //highlight input when it has focus
        //highlight active input field
        console.log('focus on '+$(this).attr('id'));
        $(":input").removeClass("highlight_input");

        if($(this).attr('disabled') !== true){
           $(this).removeClass("highlight_input").addClass("highlight_input");
        };

    });
    

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



    $(document).on('blur','#producer',function(){
        //check if producer is new on blur
        console.log('producer - blur event');
        //delay added to allow time to select autocomplete before checking
        setTimeout(function(){
            ( !$.trim( $('#producer').val() ) ) ? false : check_producer();
            //check_producer();
        }, 500);
    });
    
    
    function validate_page(){
        //validate page
        var def = $.Deferred();

        if( !$("#frm_wine").valid() ){ //validation failed do NOT continue
            console.log('frm_wine failed validation');
            $('#unsaved').dialog('close'); //close save_dialog if it is open to allow form validation to be corrected
            //display message
            $(".con_button_bar").notify("Validation Failed",{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 1000
                }
            );
            
            def.reject('frm_wine failed validation');
        }else{
           console.log('frm_wine validated OK'); 
           def.resolve();
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
            //check to see if Producer needs to be added
            check_producer()
                .then(function(){ //check producer is not new
                    console.log("check_producer - promise resolved");
                    putWineServer(save_db).then(function(){ //put wine to server
                        console.log("putWineServer successful");
                        update_page_status(3);
                        def.resolve(true);
                    });
                },function(){
                    var msg = "check_producer - promise rejected";
                    console.log(msg);
                    var response = {
                        status: false,
                        error: msg
                    };
                    def.reject(false);
            }); //check_producer
            
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


    $(document).on('click',"#btn_add_country",function(){
        console.log('add country click event');
        add_new_country();
    });


     $(document).on('click',"#btn_add_region",function(){
        console.log('add region');
        if($('#country').val()>""){
            console.log('open add_region dialog');
            add_new_region();
        }else{
            msg = 'Please select a Country before attempting to add a new Region';
            $(".con_button_bar").notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
        }

    });


    $(document).on('click',"#btn_add_subregion",function(){
        console.log('add subregion');
        if($('#region_id').val() > 0){
            console.log('open add_subregion dialog');
            add_new_subregion();
        }else{
            msg = 'Please select a Region before attempting to add a new Subregion';
            $(".con_button_bar").notify(msg,{
                position: "top left",
                style: "msg",
                className: "warning",
                arrowShow: false,
                autoHideDelay: 3000
                }
            );
        }

    });


    $(document).on('change',"#select_producer",function(){
        var text = $(this).find('option:selected').text();
        var index = $(this).find('option:selected').val();
        $("#producer_id").val(index);
        $("#producer").val(text);
        $("#select_producer").val(index);
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

        //console.log('set form validation');

        $("#frm_wine").validate({
            rules: {
                year: {number: true, range: [1800,9999]},
                winetype_id: {number: true, required: true},
                country: {required: true},
                region: {required: true},
                producer: {required: true},
                wine: {required: true},
                country_id: {
                    number: true,
                    required: function(element){
                        return ($("#input_country").val()=="False");
                    }
                },
                producer_id: {
                    number: true,
                    required: function(element){
                        return ($("#input_producer").val()=="False");
                    }
                }
            },
            messages: {

                type: {
                    required: "Wine type is required<br/>"
                },
                producer: {
                    required: "Producer is required</br>"
                },
                country: {
                    required: "Country is required</br>"
                },
                region: {
                    required: "Region is required</br>"
                },
                wine: {
                    required: "Wine name is required</br>"
                },
                key_country: "Add country to List",
                year: "4 digits"
            }, //messages
            errorLabelContainer: $('#error_labels')
        });
    }

    //____AutoComplete Fields____
    
    

    
    function set_autocomplete(){
        //autocomplete _ handled as function so that it can be called after html has loaded
        console.log('set form autocomplete settings');
        var start = new Date().getTime();
        
        $("#producer").autocomplete({
            //source: "/wine/autocomplete_results.php",
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
            select: function( event, ui ) {
                console.log("Selected: " + ui.item.value + " aka " + ui.item.id + " input was " + this.value );
                console.log(ui);
                if(ui.item.id){
                    $("#producer_id").val(ui.item.id);
                    $("#select_producer").val(ui.item.id);
                    obj_page.set_is_dirty(true);
                }
            },
            open: function(){
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) { //resolves two click issue in IOS
                    $('.ui-autocomplete').off('menufocus hover mouseover');
                }
            }
        });
        
        
        $("#country").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "/wine/autocomplete_results.php",
                    dataType: "json",
                    data: {
                        term : request.term,
                        category : 'country'
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            select: function( event, ui ) {
                console.log("Selected: " + ui.item.value + " aka " + ui.item.id + " input was " + this.value );
                console.log(ui);
                if(ui.item.id){
                    $("#country_id").val(ui.item.id);
                    $("#select_country").val(ui.item.id);
                    clear_region();
                    obj_page.set_is_dirty(true);
                }
            },
            change: function(e, ui) {
                if (!ui.item) { //no match event
                    $(this).val("");
                    $("#country_id").val(""); //clear country_id
                    clear_region();
                }
            },
            response: function(e, ui) {
                if (ui.content.length == 0) {
                    $(this).val("");
                    clear_region();
                }
            },
            open: function(){
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) { //resolves two click issue in IOS
                    $('.ui-autocomplete').off('menufocus hover mouseover');
                }
            }
        }).on("keydown", function(e) {
            if (e.keyCode == 27) {
                $(this).val(""); //clear value on escape
                clear_region();
            }
        }).on("keyup", function(e){
            if($('#country').val()==""){ //clear values when deleted
                clear_region();
            }
        });
        
        
        function clear_region(){
            //clear region and subregion values
            console.log("reset region and subregion values"); 
            $("#region").val(''); //clear region values
            $("#region_id").val(''); 
            $("#subregion").val(''); //clear region values
            $("#subregion_id").val('');
            var country_id = $("#country_id").val();
            load_region_select(country_id);
            load_subregion_select();
        }
        
        
        $("#region").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "/wine/autocomplete_results.php",
                    dataType: "json",
                    data: {
                        term : request.term,
                        category : 'region',
                        country_id: $('#country_id').val()
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            select: function( event, ui ) {
                console.log("Selected: " + ui.item.value + " aka " + ui.item.id + " input was " + this.value );
                console.log(ui);
                if(ui.item.id){
                    fill_country_region('region', ui.item.id, ui.item.label);
                }
            },
            change: function(e, ui) {
                if (!ui.item) { //no match
                    $(this).val("");
                    $("#region_id").val('');
                    clear_subregion();
                }
            },
            response: function(e, ui) {
                if (ui.content.length == 0) {
                    $(this).val("");
                    clear_subregion();
                }
            },
            open: function(){
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) { //resolves two click issue in IOS
                    $('.ui-autocomplete').off('menufocus hover mouseover');
                }
            }
        }).on("keydown", function(e) {
            if (e.keyCode == 27) {
                $(this).val(""); //clear value on escape
                clear_subregion();
            }
        }).on("keyup", function(e){
            console.log("#region val= "+$('#region').val());
            if($('#region').val()==""){
                console.log('Region value is empty');
                clear_subregion();
            }
        });
        
        
        $("#subregion").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "/wine/autocomplete_results.php",
                    dataType: "json",
                    data: {
                        term : request.term,
                        category : 'subregion',
                        region_id: $('#region_id').val()
                    },
                    success: function(data) {
                        console.log('subregion_ac data:');
                        console.log(data);
                        response(data);
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            select: function( event, ui ) {
                console.log("Selected: " + ui.item.value + " aka " + ui.item.id + " input was " + this.value );
                console.log(ui);
                if(ui.item.id){
                    fill_country_region('subregion', ui.item.id, ui.item.label);
                }
            },
            change: function(e, ui) {
                if (!ui.item) {
                    $(this).val("");
                }
            },
            response: function(e, ui) {
                if (ui.content.length == 0) {
                    $(this).val("");
                }
            },
            open: function(){
                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) { //resolves two click issue in IOS
                    $('.ui-autocomplete').off('menufocus hover mouseover');
                }
            }
        }).on("keydown", function(e) {
            if (e.keyCode == 27) {
                $(this).val(""); //clear value on escape
            }
        });
        
        
        function clear_subregion(){
            $("#subregion").val(''); //clear region values
            $("#subregion_id").val('');
            var region_id = $("#region_id").val();
            load_subregion_select(region_id);
        }
        
        

        
        var end = new Date().getTime();
        var time = end - start;
        console.log("time for autocomplete = "+time);
        
    } //set_autocomplete







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
        
        //determine screen size
        var windowWidth = $(window).width();
        if(windowWidth > 500){
            dialogWidth = 370;
            positionMy = "left bottom";
            positionAt = "right top";
            positionOf = '#btn_add_producer'
        } else {
            dialogWidth = windowWidth;
            positionMy = "right top+20px";
            positionAt = "right bottom";
            positionOf = "#top_nav";
        }  
        
        if( $("#add_producer").val() === $( "#producer" ).val() ){
            var automated_add = true; //set flag if triggered automatically on blur event
            console.log('automated_add');
        };
        
        $("#dialog-producer").dialog({
            modal: true,
            width: dialogWidth,
            height:205,
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
                    if(automated_add){ //user cancelled an automated add so clear all fields
                        $("#producer").val(''); //clear values
                        $("#producer_id").val('');
                    }
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

        $.post("/wine/rpc_wine_db.php", {
            value: producer,
            action: 'add_producer_db'
        }, function(data){
            if(data.success){
                console.log('add_producer_db successful id: '+data.producer_id);
                //set producer value
                $("#producer_id").val(data.producer_id);
                $("#producer").val(producer);
                $("#producer").focus();
                
                //reset and close dialog
                $( "#add_producer" ).val("");
                $( "#dialog-producer" ).dialog( "close" );
                
                var msg = ('Add Producer Successful');
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",   
                    className: "success",
                    arrowShow: false
                    }
                );

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
        $( "#dialog-country" ).dialog( "open" );//open dialog
        $('#new_country').focus();
    }


    $( "#dialog-country" ).dialog({
            //requires jquery-ui plug-in
            autoOpen: false,
            modal: true,
            width: 365,
            height:205,
            buttons: {
                    "OK": function() {
                        var new_country = $('#add_country').val();
                        console.log(new_country);
                        add_country_to_db(new_country, function(){
                            $('#dialog-country').dialog( "close" );
                        });
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
            },
            dialogClass: "clean-dialog",
            position: { my: "left bottom", at: "right top", of: '#btn_add_country' },
            close: function() {
                   $('#add_country').val( "" );
            }
    });
    
    
    
    $("#dialog-country").keydown(function (event) { //set default enter behaviour
        console.log("return key keydown event detected");
        if (event.keyCode == 13) {
            $(this).parent()
                   .find("button:eq(1)").trigger("click");
            return false;
        }
    });


    function add_country_to_db(country, callback){
        //add country to  db
        console.log('add_country_to_db');
        
        if(!$.trim(country)){
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

        $.post("/admin/rpc_ref_data.php",{
            action: 'save_country_db',
            country_text: country

            }, function(data) {
                if(data.success){
                    console.log('rpc add_country_to_db SUCCESS');
                    //update form with newly added country
                    $('#country').val(country);
                    $('#country_id').val(data.country_id);
                    //clear region and subregion as country is new
                    $('#region').val('');
                    $('#region_id').val('');
                    $('#subregion').val('');
                    $('#subregion_id').val('');
                    var msg = ('Add Country Successful');
                    $(".con_button_bar").notify(msg,{
                        position: "top left",
                        style: "msg",   
                        className: "success",
                        arrowShow: false
                        }
                    );
                    if($.isFunction(callback)){
                        callback();
                    }
                } else {
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



    function add_new_region(){
        //open dialog to add new country
        console.log('add_new_region function');
        $( "#dialog-region" ).dialog( "open" ); //open dialog
        $('#add_region').focus();
    }


    $( "#dialog-region" ).dialog({
            autoOpen: false,
            height: 205,
            width: 365,
            modal: true,
            buttons: {
                    "OK": function() {
                        var new_region = $('#add_region').val();
                        add_region_to_db(new_region, function(){
                            $('#dialog-region').dialog( "close" );
                        });
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            },
            dialogClass: "clean-dialog",
            position: { my: "left bottom", at: "right top", of: '#btn_add_region' },
            close: function() {
                   $('#add_region').val( "" )
            }
    });
    
    
    $("#dialog-region").keydown(function (event) { //set default enter behaviour
        if (event.keyCode == 13) {
            $(this).parent()
                   .find("button:eq(1)").trigger("click");
            return false;
        }
    });
    

    function add_region_to_db(region, callback){
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
        var country_id = $('#country_id').val();
        
        $.post("/admin/rpc_ref_data.php",{
            action: 'add_region',
            region: region,
            country_id: country_id
            }, function(data) {
                if(data.success){
                    console.log('rpc add_region_to_db SUCCESS');
                    //update form with newly added value
                    $('#region').val(region);
                    $('#region_id').val(data.region_id);
                    var msg = ('Add Region Successful');
                    $(".con_button_bar").notify(msg,{
                        position: "top left",
                        style: "msg",   
                        className: "success",
                        arrowShow: false
                        }
                    );
                    if($.isFunction(callback)){
                        callback();
                    }
                } else {
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


    function add_new_subregion(){
        //open dialog to add new country
        console.log('add_new_subregion function');
        $( "#dialog-subregion" ).dialog( "open" );//open dialog
        $('#add_subregion').focus();
    }


    $( "#dialog-subregion" ).dialog({
        //requires jquery-ui plug-in
            autoOpen: false,
            height: 205,
            width: 365,
            modal: true,
            buttons: {
                    "OK": function() {
                        var new_subregion = $('#add_subregion').val();
                        add_subregion_to_db(new_subregion, function(){
                            $('#dialog-subregion').dialog( "close" );
                        });
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            },
            dialogClass: "clean-dialog",
            position: { my: "left bottom", at: "right top", of: '#btn_add_subregion' },
            close: function() {
                   $('#add_subregion').val( "" );
            }
    });
    
    
    $("#dialog-subregion").keydown(function (event) { //set default enter behaviour
        if (event.keyCode == 13) {
            $(this).parent()
                   .find("button:eq(1)").trigger("click");
            return false;
        }
    });


    function add_subregion_to_db(subregion, callback){
        //add subregion to  db
        console.log('add_subregion_to_db');
        
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
        
        var region_id = $('#region_id').val();
        $.post("/admin/rpc_ref_data.php",{
            action: 'add_subregion',
            subregion: subregion,
            region_id: region_id
            }, function(data) {
                if(data.success){
                    console.log('rpc add_subregion_to_db SUCCESS');
                    //update form with newly added value
                    $('#subregion').val(subregion);
                    $('#subregion_id').val(data.subregion_id);
                    var msg = ('Add Subregion Successful');
                    $(".con_button_bar").notify(msg,{
                        position: "top left",
                        style: "msg",   
                        className: "success",
                        arrowShow: false
                        }
                    );
                    if($.isFunction(callback)){
                        callback();
                    }
                } else {
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


});
</script>
</html