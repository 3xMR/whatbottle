<?php
/*Master Acquisition form*/

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";

require_once("$root/includes/standard_html_head.inc.php"); //include style sheets
require_once("$root/includes/css.inc.php");
echo "<title>Acquisition - What Bottle?</title>";//page title
?>

    <div id="dialog-form-add-merchant" class="hidden" title="Add Merchant?">	
        <h2 style="margin-bottom:15px;"> Add New Merchant</h2>
        <div class="input-main-label">
            <p>Merchant</p>
        </div>
        <div class="input-main">
            <input type="text" name="new_merchant" id="new_merchant" autocomplete="off"/>
            <input type="hidden" id="new_merchant_id" size="32" class="text ui-widget-content ui-corner-all" />
        </div>
        <br/>
    </div>


<?php

//disable fields if not authed
if(is_authed()){
     $disabled = null;
}else{
    $disabled = "disabled='disabled'";
}

echo "<div class=\"page_container\" >";
    require_once("$root/includes/nav/topheader.php"); //header

echo "<div class=\"con_single_form\" >";
    
    //Title bar
    echo "<div class=\"con_title_bar\" >";
        $acquisition_id = $_SESSION['var_vintage_temp']['acquisition_id'];
        $acquire_id = $_SESSION['var_acquire']['acquire_id'];
        echo "<input type=\"hidden\" name=\"acquire_id\" id=\"acquire_id\" value=\"$acquire_id\" />";
        
        echo "<div class=\"vertical-centre\" style=\"height:30px; border-bottom: solid 1px darkgray; background-color:; padding-bottom:0px; margin-top:0px; margin-bottom:0px;\" >";
            echo "<div style=\"float:left; width:2.5em;\" >";
                echo "<img src=\"/images/shopping_cart_512.png\" style=\"display:block; height:25px; width:25px; margin-bottom:5px; \" >";
            echo "</div>";
            echo "<div style=\"float:left; \" >";
                echo "<h1 class=\"inline\" >Acquisition</h1>";
            echo "</div>";
            echo "<div style=\"padding-left:15px; float:left;\"  >";
                echo "<img id=\"process_indicator\" src=\"/images/ajax-loader.gif\" style=\"height:1.6em; width:1.6em;\" />";
            echo "</div>";
            echo "<div class=\"clear\"></div>";
        echo "</div>";
    echo "</div>"; //con_title_bar

    echo "<form action=\"$url\" method=\"post\" name=\"form\" id=\"form\" >";
    
    //column_1
    echo "<div class=\"rwd-con-50\" style=\"background-color:; margin-top:10px; min-width:358px;\" >";

            echo "<div class=\"con_form_input\" >";
                //Date
                echo "<div class=\"input-main-inline vertical-centre\" >";
                    echo "<label style=\"width:5em;\">Date</label>";

                    $mysql_date = $_SESSION['var_acquire']['acquire_date'];
                    if($mysql_date>0){
                        $acquire_date = date_us_to_uk($mysql_date,'d-M-Y');
                    } else {
                        $acquire_date = null;
                    }

                    echo "<input type=\"text\" class=\"date\" style=\"width:120px;\" name=\"acquire_date\" id=\"acquire_date\" value=\"$acquire_date\" $disabled/>";
                echo "</div>";
            echo "</div>"; //con_form_input

            //Type
            echo "<div class=\"con_form_input\" >";
                echo "<div class=\"input-main-inline vertical-centre\" >";
                    echo "<label style=\"width:5em;\" >Type</label>";
                    echo "<select class=\"acquire_type\" style=\"width:230px;\" name=\"acquire_type_id\" id=\"acquire_type_id\" $disabled>";
                        echo "<option value=\"0\">Select an acquisition type...";
                        $obj_acquire = new acquire_type();
                        $var_results = $obj_acquire -> get($where, $columns, $group, $sort, $limit);
                        foreach($var_results as $var_result){
                            //return list of merchants
                            $item = $var_result['acquire_type'];
                            $key = $var_result['acquire_type_id'];
                            if($_SESSION['var_acquire']['acquire_type_id']){
                                $selected_acquire_type = $_SESSION['var_acquire']['acquire_type_id'];
                            }else{
                                $selected_acquire_type = 1; //default to Acquisition
                            }
                            if($key==$selected_acquire_type){
                                echo ("<option selected value=".$key.">".$item);
                            } else {
                                echo ("<option value=".$key.">".$item);  
                            }

                        }
                    echo "</select>";   
                echo "</div>";
            echo "</div>"; 


            //Merchant
            //echo "<div class=\"con_form_input\" >";
                echo "<div class=\"input-main-inline vertical-centre\" >";
                    echo "<label style=\"width:5em;\" >Merchant</label>";
                    echo "<select class=\"merchant\" style=\"width:230px;\" name=\"merchant_id\" id=\"merchant_id\" $disabled>";
                        echo "<option value=\"\">Select a Merchant...";
                        $obj = new merchant();
                        $sort = "merchant ASC";
                        $var_results = $obj -> get($where, $columns, $group, $sort, $limit);
                        foreach($var_results as $var_result){
                            //return list of merchants
                            $item = $var_result['merchant'];
                            $key = $var_result['merchant_id'];
                            $selected_merchant = $_SESSION['var_acquire']['merchant_id'];

                            if($key==$selected_merchant){
                                echo ("<option selected value=".$key.">".$item);
                            } else {
                            echo ("<option value=".$key.">".$item);  
                            }
                        }
                    echo "</select>";
                    echo "<img style=\"margin-left:15px;\" src=\"\images\plus_grey_flat_32.png\" height=\"18px\" width=\"18px\" id=\"btn_add_merchant\" class=\"click input_image\" />";
                echo "</div>";
            //echo "</div>";
        
        echo "</div>"; //column_1_2
        
    echo "<div class=\"rwd-con-50\" style=\"background-color:; margin-top:10px;\" >";

        //Notes
        //echo "<div class=\"con_form_input\" >";
            echo "<div class=\"input-main-label\" >";
                echo "<label>Notes</label>";
            echo "</div>"; 
            
            echo "<div class=\"input-main\" >";
                $acquire_notes = $_SESSION['var_acquire']['acquire_notes'];
                echo "<textarea style=\"height:65px; width:100%;\"  value=\"$acquire_notes\" name=\"acquire_notes\" id=\"acquire_notes\" $disabled >$acquire_notes</textarea>";
            echo "</div>";
            
        //echo "</div>"; 
        
    echo "</div>";
    
    echo "<div class=\"rwd-con-100\" style=\"background-color:; margin-top:0px;\" >";

        //Vintages
    
        echo "<div class=\"input-main-label\" style=\"width:100%; margin-bottom:10px;\" >";
            echo "<label>Vintages</label>";
        echo "</div>"; //form_input_title

        //echo "<div style=\"clear:both;\" >";
            echo "<div style=\"clear:both; overflow-y:auto; height:100px;\" id=\"acquistion_content\" >";
                //filled with jquery load method - /acquire/rpc_acquisition_html.php
            echo "</div>";
        //echo "</div>"; 
             
   echo "</div>"; //rwd-con-100           
            
        echo "<div class=\"clear\" ></div>";
    
        echo "<div style=\"margin-top:10px; margin-left:20px;\" >";
            echo "<ul id=\"error_labels\" style=\"list-style-type:circle; color:red; \" ></ul>";
        echo "</div>";

        //form buttons
        echo "<div class=\"con_button_bar\" >";
            if(is_authed()){
                echo "<input type=\"button\" name=\"btn_save\" id=\"btn_save\"  value=\"Save\" \>";
                echo "<input type=\"button\" name=\"btn_delete\" id=\"btn_delete\" class=\"hide_small_screen\" value=\"Delete\" >";
                echo "<input type=\"button\" name=\"btn_import_basket\" id=\"btn_import_basket\" value=\"Import Basket\" \>";
            }
            echo "<input type=\"button\" name=\"btn_close\" id=\"btn_close\" class=\"btn_close\" value=\"Close\" \>";
        echo "</div>";
        
  
    
echo "</form>";

echo "<div class=\"clear\" ></div>";

echo "</div>"; //con_single_form

//push con_single_form bottom clear of all other divs
echo "<div class=\"clear\" ></div>";

echo "</div>"; //page_container


//common dialogs
require_once("$root/includes/standard_dialogs.inc.php");
require_once("$root/includes/script_libraries.inc.php"); //include all script libraries
?>

<div style="display:none;" id="refresh">
    <p>Looks like you reached this page by clicking 'Back in your browser.</p>
    <p>Click refresh or return to 'Wine' to start again</p>
</div>

<div id='main_menu' class="pop_up" style="width:200px; display:none; position:fixed; z-index:30;">
    <div class="ui-menu-item-first" >New Acquisition<img style="float:right; margin-top:2px;" src="/images/add_black_128.png" height="21px" /></div>
    <div>Delete Acquisition<img style="float:right; margin-top:2px;" src="/images/minus_black_256.png" height="21px" /></div>
    <div>New Wine<img style="float:right; margin-top:2px;" src="/images/add_black_128.png" height="21px" /></div>
    <div>Wines<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>Reporting<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div>Reference Data<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    <div class="ui-menu-item-last">Settings<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
</div>

</body>

<script type="text/javascript">
$(document).ready(function(){
 
    var this_page = "/acquire/acquisition.php";

    //page control object
    var obj_page = new page_control({
        save_function: function(){
            return save_page();  
        },
        save_session: function(data){
            return save_acquire_to_session();
        },
        page_url: this_page //set page url

    });

    //dynamically add tabindex
    $(":input:not(:hidden)").each(function (i) { $(this).attr('tabindex', i + 1); });
 
    //initialise basket
    initialise_basket();
   
    //load vintages from array
    load_vintages_html('recalc');
    
    //set_buttons
    set_buttons();
    
    //show ajax activity
    $('#process_indicator')
    .hide()  // hide it initially
    .ajaxStart(function() {
        $(this).show();
    })
    .ajaxStop(function() {
        $(this).hide();
    });
    
  
    function load_vintages_html(){
       //load vintage html from remote script
       console.log('load_vintages_html');
       $('#acquistion_content').load('/acquire/rpc_acquisition_html.php', function(){
           //once html is loaded - call recalc
           recalc_all();
       });
    }


    function recalc_all(){
        //recalc all rows
        var num_row = $('#table_acquisition tr').length - 1;
        if($('#table_acquisition tr').length > 1){
            //iterate through rows of table
            $('#table_acquisition tbody > tr').each(function(){
                var $row = $(this);
                var id = $row.attr('id');
                recalc(id);
            });
        }
    }


    function recalc(row_id, changed){

        if (row_id>=0){  
            var $row = $('#table_acquisition').find('#'+row_id);
            var qty = parseInt($row.find("#qty_"+row_id).val());
            var full_price = parseFloat($row.find('#full_price_'+row_id).val());
            var discount_percent = parseFloat($row.find('#discount_percent_'+row_id).val());
            var discount = full_price*(discount_percent/100);
            if(changed === 'discount'){
                var discount = parseFloat($row.find('#discount_'+row_id).val());
                var discount_percent = (discount/full_price)*100;
            }
            var price_paid = full_price-discount;
            var total_price_paid = price_paid*qty;

            //update cells
            $row.find('#qty_'+row_id).val((qty).toFixed(0));
            $row.find('#full_price_'+row_id).val((full_price).toFixed(2));
            $row.find('#discount_percent_'+row_id).val((discount_percent).toFixed(1));
            $row.find('#discount_'+row_id).val((discount).toFixed(2));
            $row.find('#price_paid_'+row_id).val((price_paid).toFixed(2));
            if(isNaN(total_price_paid)){ total_price_paid=0; }
            $row.find('#total_price_paid_'+row_id).val((total_price_paid).toFixed(2));
        }

        //calculate total row
        var sum;
        sum = 0;
        $('.qty').each(function(index){
            var num = parseInt($(this).val());
            if(!isNaN(num)){
               sum = sum + num;
            }

        });
        
        $('#qty_total').val(sum);

        var sum;
        sum = 0;
        $('.total_price_paid').each(function(index){
            var num = parseFloat($(this).val());
            if(!isNaN(num)){
               sum = sum + num;
            }

        });
        $('#total_price_paid_total').val((sum).toFixed(2));
    }


    function add_remove_vintage_acquistion(key, action){
        //add or remove vintage from acquisition
        console.log('add_remove_vintage_acquisition');
        var acquire_type_id = $('#acquire_type_id').val();
        console.log("add_remove_vintage acquire_type_id:"+acquire_type_id+" key:"+key+" action: "+action);
        
        $.post("/acquire/rpc_acquire_db.php", {
            key: key,
            acquire_type_id: acquire_type_id,
            rpc_action: action
        }, function(data){
            
            if(data.success){
                if(data.records>=0){
                    //records were added or removed
                    obj_page.set_is_dirty(true);
                    console.log('records changed = '+data.records);
                    console.log(data.msg);
                    //re-load vintages html
                    load_vintages_html();
                } else {
                    console.log('no records modified');
                    console.log(data.error);
                }
            } else {
                console.log("error post failed: "+data.error);
            }
            
        }, "json");
    }
    
    
    function add_basket(){
        //add basket contents to acquisition
        $.post("/acquire/rpc_acquire_db.php", {
            rpc_action: 'add_basket'
        }, function(data){
            if(data.success){
                if(data.records > 0){
                    //records were added or removed
                    obj_page.set_is_dirty(true);
                    console.log('# of records changed = '+data.records);
                    console.log(data.msg);
                    load_vintages_html(); //re-load vintages html
                } else if(data.records == 0) { //success but nothing to add
                    msg = "Basket is empty";
                    console.log(msg);
                    //display message
                    $(".con_button_bar").notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "warning",
                        arrowShow: false
                        }
                    );
                }
                
                //import successful
                update_basket_count(0); //jquery.basket.js
                
            } else {
                var msg = "Import Basket failed with error: "+data.error;
                console.log(msg);
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "error",
                    arrowShow: false
                    }
                );
            }
            
        }, "json");
    }


    function delete_acquisition(){
        //delete acquisition from db
        var acquire_id = $('#acquire_id').val();
        console.log('function remove_acquisition acquire_id='+acquire_id);
        $.post("/acquire/rpc_acquire_db.php", {
            acquire_id: acquire_id,
            action: 'delete_acquire_from_db'
        }, function(data){
            if(data.success){
                console.log('remove_acquisition successful msg: '+data.msg);
                obj_page.set_is_dirty(false);
                obj_page.close_page(); //close page and return to dst
            }else{
                var msg = 'There was a problem removing this acquisition. error: '+data.error;
                console.log(msg);
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "warning",
                    arrowShow: false
                    }
                );
            }
        }, "json");
    }
    
    
    function delete_acquisition_dialog(){
        //show delete dialog - prompt user for response
        
        //determine screen size
        var windowWidth = $(window).width();
        if(windowWidth > 500){
            dialogWidth = 470;
            positionMy = "left bottom";
            positionAt = "right top";
            positionOf = '#btn_delete';
        } else {
            dialogWidth = windowWidth;
            positionMy = "right top+20px";
            positionAt = "right bottom";
            positionOf = "#top_nav";
        }   
        
        $("#dialog_confirm_delete_text").text("Are you sure you want to Delete this Acquisition?");
        
        $("#dialog-delete").dialog({
            modal: true,
            width: dialogWidth,
            buttons: {
                Delete: function() {
                    $(this).dialog('close');
                    console.log('user confirmed to delete');
                    delete_acquisition();
                },
                Cancel: function() {
                    $(this).dialog('close');
                    console.log('user declined to delete acquistion');
                }
            },
            dialogClass: "clean-dialog",
            position: { my: positionMy, at: positionAt, of: positionOf }
        });
    
    }
    
    
    function delete_vintage_dialog(vintage_id, object){
        //show delete vintage dialog - prompt user
        console.log('delete_vintage_dialog vintage_id: '+vintage_id);
        
        //determine screen size
        var windowWidth = $(window).width();
        if(windowWidth > 500){
            dialogWidth = 470;
            positionMy = "right bottom";
            positionAt = "left middle";
            positionOf = object;
        } else {
            dialogWidth = windowWidth;
            positionMy = "right top";
            positionAt = "right bottom";
            positionOf = "#top_nav";
        }   
        
        $("#dialog_confirm_delete_text").text("Remove this Vintage?");
        target_id = $(object).attr('id');
        
        $("#dialog-delete").dialog({
            modal: true,
            width: dialogWidth,
            buttons: {
                Remove: function() {
                    $(this).dialog('close');
                    console.log('user confirmed to delete');
                    add_remove_vintage_acquistion(vintage_id,'remove_vintage');
                },
                Cancel: function() {
                    $(this).dialog('close');
                    console.log('user declined to delete acquistion');
                }
            },
            dialogClass: "clean-dialog",
            position: { my: positionMy, at: positionAt, of: positionOf}
        });
        
        
    }
    

    
    function save_acquire_to_session(commit_db, callback){
        //save acquire vintage records to session or db if commit_db is true
        console.log('function: save_acquire_to_session');
        
        var def = jQuery.Deferred();
        
        //disable save button
        $('#btn_save').attr('disabled','disabled');
        
        //get acquire data from form
        var acquire_id = $('#acquire_id').val();
        var acquire_date = $('#acquire_date').val();
        var merchant_id = $('#merchant_id').val();
        var acquire_type_id = $('#acquire_type_id').val();
        var acquire_notes = $('#acquire_notes').val();

        var var_acquire = new Array();
        var_acquire = [ acquire_id,
                        acquire_date,
                        merchant_id,
                        acquire_type_id,
                        acquire_notes];
        
        //get vintage data from table
        num_row = $('#table_acquisition tr').length - 1;
        
        if($('#table_acquisition tr').length > 1){
            //nothing to save unless rows > 1 as 1 row will be totals row

            i = 0;
            var var_vintages = new Array();

            //iterate through rows of table
            $('#table_acquisition tbody > tr').each(function(){
                var $row = $(this);
                var id = $row.attr('id');
                if(id==='total_row'){
                    //do not include total row
                } else {
                    //add row to vintages array
                    var vintage_has_acquire_id = id;
                    var vintage_id = $row.find('#vintage_id_'+id).val();
                    var vintage_label = $row.find('#vintage_label_'+id).text();
                    var qty = $row.find('#qty_'+id).val();
                    var full_price = $row.find('#full_price_'+id).val();
                    var discount_percent = $row.find('#discount_percent_'+id).val();
                    var discount = $row.find('#discount_'+id).val();
                    var price_paid = $row.find('#price_paid_'+id).val();
                    var total_price_paid = $row.find('#total_price_paid_'+id).val();
                    var db_action = $row.find('#db_action_'+id).val();
                   
                    var_vintage = [ vintage_has_acquire_id,
                                    vintage_id,
                                    vintage_label,
                                    qty,
                                    full_price,
                                    discount_percent,
                                    discount,
                                    price_paid,
                                    total_price_paid,
                                    db_action
                                    ];

                    var_vintages[i] = var_vintage;
                    i=i+1;
                }
                
            });
            
        } else {
            //no vintage records to save, commit acquire
            var_vintages = 'none';
        }

        //save to db?
        var action;
        
        if(commit_db){
            action = 'save_to_db';
        } else {
            action = 'save_to_session';
        }
        
        console.log("save action: "+action);
        console.log(var_vintages);
        
        //submit arrays to server 
        $.post("/acquire/rpc_acquire_db.php", {
            rpc_action: action,
            var_vintages: var_vintages,
            var_acquire: var_acquire
        }, function(data){
            if(data.success){
               //array saved successfully
               obj_page.set_is_dirty(false);
               console.log('save acquire to session successful');
               console.log('msg: '+data.msg);

               if(action==='save_to_db'){
                    //update form acquire_id
                    if(data.acquire_id){
                        $('#acquire_id').val(data.acquire_id);
                    }
                    
                    obj_page.set_is_dirty(false); //reset is_dirty
                    load_vintages_html();//reload html
                    $(".con_button_bar").notify("Save Successful",{
                        position: "top left",
                        style: "msg",
                        className: "success",
                        arrowShow: false
                        }
                    );
            
                    //update buttons
                    set_buttons();
                    
                    def.resolve(true); //return succesful prommise
                    
               }
               
               if(typeof callback === "function"){ 
                    callback();
               }
            
            }else{
                var msg = 'Save Acquision failed with error:' + data.error;
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "error",
                    arrowShow: false
                    }
                );
                def.reject(msg);
                alert(msg);
            }
            
            $('#btn_save').removeAttr('disabled'); //re-enable save button
            
        }, "json");
        
        return def.promise();
        
    }
    
    
    function open_vintage(id){
        //open vintage
        
        obj_page.leave_page({
            dst_url:        "/vintage/vintage.php",
            rtn_url:        this_page,
            page_action:    'leave',
            dst_type:       "vintage",
            dst_action:     "open",
            object_id:      id            
        });
        
    }
    
    
    function add_merchant_db(callback){
        console.log('add_merchant_to_db');
        
        var merchant = $('#new_merchant').val();
        console.log('merchant name: '+merchant);
        
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
        
        $.post("/admin/rpc_ref_data.php",{
            action: 'save_merchant_db',
            merchant: merchant
            }, function(data) {
                if(data.success){
                    console.log('rpc add_merchant_to_db SUCCESS');
                    
                    $('#merchant_id').append($('<option>', { //append option to select
                        value: data.merchant_id,
                        text: merchant
                    }));
                    
                    $('#merchant_id').val(data.merchant_id); //set select option
                    $("#dialog-form-add-merchant").dialog("close"); //close dialog
                    
                    var msg = "Add Merchant Successful";
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
                }
        }, "json");
    }
    
    
    function add_merchant(){
        console.log('add merchant');
        $( "#dialog-form-add-merchant" ).dialog( "open" );
        $('#new_merchant').focus();
    }
    
    
    $( "#dialog-form-add-merchant" ).dialog({ //requires jquery-ui plug-in
        autoOpen: false,
        height: 205,
        width: 360,
        modal: true,
        buttons: {
                OK: function() {
                    add_merchant_db();
                },
                Cancel: function() {
                    $(this).dialog("close");
                }
        },
        dialogClass: "clean-dialog",
        position: { my: "left center", at: "right top", of: '#btn_add_merchant' },
        close: function() {
               $('#new_merchant').val("");
        }
    });
    
    
    
    function set_buttons(){
        /*  Enable or disable buttons based on status
         *      New - Saved - Modified
         */
        
        if($('#acquire_id').val()){ //acquire_id so existing acquisition
            //don't show delete button on small screens
            var windowWidth = $(window).width();
            if(windowWidth > 500){
                $('#btn_delete').removeAttr('disabled').show();
            }
        }else{
            $('#btn_delete').attr('disabled', true).hide();
        }
       
    }
    

    
    $("#acquistion_content").on('focus', "#table_acquisition tr td", function(e){    
        //highlight table cell when they have focus
        
        //Check if the target has readonly class
        if($(e.target).hasClass("readonly")){
            e.stopPropagation();
            return false;
        }
        
        console.log('table cell clicked = '+$(this).attr('id'));   
        console.log(e);  
    
        //clear all highlighted cells and set for this cell only  
        $("#table_acquisition tr td").removeClass('highlight_input');
        $(this).removeClass('highlight_input').addClass('highlight_input');
        
        //e.select();
        //select text on ios
        var myInput = document.getElementById($(this).attr('id'));
        myInput.setSelectionRange(0, 9999); //for ios
   
    });
    

    
    $("#acquistion_content").on('focus', "#table_acquisition tr input", function(e){    
        //highlight input when it has focus
        console.log('input focus = '+ $(this).attr('id'));
        
        $(this).select();
        
        //select text on ios
        var myInput = document.getElementById($(this).attr('id'));
        myInput.setSelectionRange(0, 9999); //for ios
        
    });
    
    
    $("#acquistion_content").on('keyup', "#table_acquisition tr input", function(e){    
        //highlight input when tabbing into box
        var key = e.which;
        console.log('keyup key = '+ key);
        if(key === 9){ //tab detected - select contents of input
            $(this).select();    
            //select text on ios
            var myInput = document.getElementById($(this).attr('id'));
            myInput.setSelectionRange(0, 9999); //for ios
        }

        
    });
    
    
    $(document).on('mouseup','.table_input',function(e){    
        //prevents safari cancelling the selection of text on mouseup
        e.preventDefault();
        
    });
    

    $(document).on('click','.vintage_click',function(){
        //open vintage
        var row_id = $(this).parent().parent().attr('id');
        var $row = $('#table_acquisition').find('#'+row_id);
        var vintage_id = $row.find('#vintage_id_'+row_id).val();
        console.log('vintage clicked='+vintage_id);
        
        open_vintage(vintage_id);

    });
    

    $(document).on('change','.qty',function(){
        var row_id = $(this).parent().parent().attr('id');
        var cell_value = $(this).val();
        if(isNaN(cell_value) || cell_value==false){
            $(this).val(1);
        }
        recalc(row_id,'qty');
    });


    $(document).on('change','.full_price',function(){
        var row_id = $(this).parent().parent().attr('id');
        var cell_value = $(this).val();
        if(isNaN(cell_value) || cell_value==false){
            $(this).val(0);
        }
        recalc(row_id,'full_price');
    });


    $(document).on('change','.discount_percent',function(){
        var row_id = $(this).parent().parent().attr('id');
        var cell_value = $(this).val();
        if(isNaN(cell_value) || cell_value==false){
            console.log(".discount_percent set to zero");
            $(this).val(0);
        }
        console.log(".discount_percent value = "+cell_value);
        recalc(row_id,'discount_percent');
    });


    $(document).on('change','.discount',function(){
        var row_id = $(this).parent().parent().attr('id');
        var cell_value = $(this).val();
        if(isNaN(cell_value) || cell_value==false){
            $(this).val(0);
        }
        recalc(row_id,'discount');
    });



    $(document).on('click','.btn_remove_row',function(){
        //remove vintage dialog
        var vintage_id = $(this).parent().parent().attr('id');
        delete_vintage_dialog(vintage_id,this);
    });
    
    
    function import_basket(){
        save_acquire_to_session(false, function(){
            //pass as callback
            add_basket(); //call add_basket action from rpc_acquire_db.php
        });
    }


    $(document).on('click','#btn_import_basket',function(){
        //save session then import basket
        console.log('btn_import_basket');
        import_basket();
    });

    
    
    $(document).on('click','#btn_close',function(){
        obj_page.close_page();
    });
    
        
    $(document).on('click','#btn_add_merchant',function(){
        add_merchant();
    });
    
    
    $(document).on('change','#acquire_type_id',function(){
        //set discounts and merchant based on acquire type
        console.log("type selected="+$(this).val());
        if($(this).val()>1){
            //set discount percent to 100%
            $('.discount_percent').each(function(index){
                $(this).val(100);
            });
            
            //recalc each row
            $('.row').each(function(index){
                recalc($(this).attr('id'));
            });
            //is_dirty(true);
            if($(this).val()===2){
                //set merchant to gift
                $("#merchant_id").val('5');
            }
        }
    });


    $(document).on('click','#btn_save',function(){
        ///save button - checks validation before calling
        //database save functions
        console.log('btn_save...');
        save_page(); 
        
    });
    
    $(document).on('click','#btn_delete',function(){
        ///delete acquisition button event
        delete_acquisition_dialog();
    });


    $("#acquire_notes").change(function(){
       obj_page.set_is_dirty(true);
    });
    
    
    function save_page(callback){
        //save page
        
        var def = new jQuery.Deferred();
        var validator = $("#form").validate();
        
        validator.resetForm(); //clear errors before validating
        if($("#form").valid()){
            console.log('validated OK');
            //save_acquisition
            save_acquire_to_session(true, callback).then(function(status){ //commit to DB is true
                //success
                console.log('save_acquire_to_session(true) completed status: '+status);
                def.resolve(true);
            }, function(status){
                msg = 'Save page failed with error: '+status;
                console.log(msg);
                def.reject('save-acquire_to_session promise failed status:'+status);
                $(".con_button_bar").notify(msg,{
                    position: "top left",
                    style: "msg",
                    className: "error",
                    arrowShow: false
                    }
                );
                
            }); 
        }else{
            def.reject('validation of page failed');
        }
        
        return def.promise();
    }
    


    //date picker
    $( "#acquire_date" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd-M-yy"
    });



//***form validation***

    $("#form").validate({
//        debug: false,
        rules:{
            acquire_date:{
                required: true
            },
            acquire_type_id:{
                required: true,
                min: 1
            },
            merchant_id:{
                required: true,
                min: 1
            }
        },
        messages:{
            acquire_date:{
                required: "Date is required"
            },
            acquire_type_id:{
                required: "Type is required",
                min: "Type is required"
            },
            merchant_id:{
                required: "Merchant is required",
                min: "Merchant is required"
            }
        },
//        errorLabelContainer: $('#error_labels'),
//        wrapper: "li",
//        focusCleanup: true,
        errorPlacement: function(error, element){}, 
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
    
    
    jQuery.validator.addMethod(
        "qty_rule",function(value, element) {
        return this.optional(element) || value >= 1;
        }, "Qty must be 1 or more"
    );
    
    
    jQuery.validator.addClassRules("qty",{
        required: true,
        qty_rule: true
    });
    
    jQuery.validator.addMethod(
        "number",function(value, element) {
            return this.optional(element) || value >= 0;
        }, "Number cannot be less than zero"
    );
    
    jQuery.validator.addClassRules("full_price",{ 
        number: true,
    });
    
    jQuery.validator.addMethod(
        "percent",function(value, element) {
            return this.optional(element) || (value >= 0 && value <= 100);
        }, "Discount percentage must be between 0 and 100"
    );
    
    jQuery.validator.addClassRules("discount_percent", {
        percent: true
    });


//***end form validation***


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
                case 'Delete Acquisition':
                    delete_acquisition_dialog();
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
                case 'Reporting':
                    open_reporting();
                    break;
                case 'Settings':
                    open_settings();
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


    function open_reporting(){
        obj_page.leave_page({
            dst_url: "/reporting/reporting_index.php",
            rtn_url: this_page,
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
    
    function open_settings(){
        obj_page.leave_page({
            dst_url: "/user/settings.php",
            rtn_url: this_page,
            dst_action: 'open',
            page_action: 'leave'
        });
    }



});

</script>
<script type="text/javascript" src="/libraries/mainMenu.js"></script>
</html>