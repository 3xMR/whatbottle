<?php
/*
 *  CSS test page
 */
    //standard php include files
    $root = $_SERVER['DOCUMENT_ROOT'];
    require_once("$root/includes/init.inc.php");
    require_once("$root/functions/function.php");
    require_once("$root/classes/class.db.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-capable" content="yes" >        
        <meta name="viewport" content="width=850">
        <title>Whatbottle - CSS Test</title>

        <?php
        //require_once("$root/includes/standard_html_head.inc.php"); //standard html header
        require_once("$root/includes/css.inc.php");//include style sheets
        ?>
        <link rel="stylesheet" href="/css/fontawesome-stars.css">
        <style>
            


            
        </style>
    </head>

    
        
        <div id="dialog" class="hidden" title="Add title with jquery">
            <div style="float:left; width:20px;">
                <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;" id="dialog-icon"></span>
            </div>
            <div style="float:left; width:240px; padding-left:10px;">
                <p id="dialog-text">These items will be permanently deleted and cannot be recovered. Are you sure?</p>
            </div>
        </div>
        
        
        
        
          
        
        
        <?php
        
        //standard logins
        require_once("$root/includes/standard_dialogs.inc.php");
        
        echo "<div class=\"page_container\">";
            //page header navigation
            require_once("$root/includes/nav/topheader.php");

            //main form
            echo "<div class=\"con_single_form rounded\" >";

                echo "<div class=\"con_title_bar\" >";//title bar

                    echo "<table>";//main heading & ajax process indicator
                        echo "<tr style=\"vertical-align:middle;\" >";
                            echo "<td><h1>Wine Details</h1></td>";
                            //echo "<td  style=\"padding-left:5px;\">
                                    //<img id=\"process_indicator\" src=\"/images/ajax-loader.gif\" height=\"18px\"/>
                                //</td>";
                        echo "</tr>";
                    echo "</table>";

                echo "</div>"; //con_title_bar

                //Column 1 of 2
                echo "<div class=\"con_column_2_1\" >";
                
                    echo "<div class=\"input-main-label\"  >";
                        echo "<p>Label</p>";
                    echo "</div>";
                    echo "<div class=\"input-main\" >";
                        echo "<input type=\"text\" value=\"\" placeholder=\"standard input\"  />";
                    echo "</div>";
                    
                    echo "<div class=\"input-main-label\" >";
                        echo "<p>Input with Centred Button</p>";
                    echo "</div>";  
                    echo "<div class=\"vertical-centre input-main\" >";
                        echo "<input type=\"text\" name=\"second-input\" value=\"\" placeholder=\"use button to right to login\" id=\"second-input\" />";
                        echo "<img class=\"click btn_login\" src=\"/images/user.png\" />";
                    echo "</div>";
                  
                    
                    echo "<div class=\"vertical-centre input-main-label\" >";
                        echo "<p>Label with Edit Button:</p>";
                        echo "<img class=\"click control\" id=\"btn_edit\" src=\"/images/edit.png\" />";
                    echo "</div>";
                    echo "<div class=\"vertical-centre input-main\" >";
                        echo "<input type=\"text\" name=\"second-input\" value=\"\" placeholder=\"second input - class: input_text\" id=\"second-input\" />";
                        echo "<img class=\"click control\" id=\"btn_add\" src=\"/images/add.png\" />";
                    echo "</div>";
                    
                    echo "<div class=\"vertical-centre input-main-label\"  >";
                        echo "<p>Select:</p>";
                    echo "</div>";
                    echo "<div class=\"vertical-centre input-main\">";
                        echo "<select name=\"standard-select\" id=\"standard-select\" placeholder=\"Select an option...\">";
                            //echo "<optgroup class=\"standard-option\" >";
                                echo "<option value=\"0\">Aston Martin";
                                echo "<option value=\"1\">Porsche";
                                echo "<option value=\"2\">Ferarri";
                                echo "<option value=\"3\">Mercedes";
                                echo "<option value=\"4\">Noble";
                            //echo "</optgroup>";
                        echo "</select>";
                        //echo "<img class=\"click\"  style=\"margin-left: -30px;\" src=\"/images/down_flat_darkgrey_24.png\" width=\"12px\" height=\"16px\" >";
                    echo "</div>";
                    
                    echo "<div class=\"vertical-centre input-main-label\"  >";
                        echo "<p>Number:</p>";
                    echo "</div>";
                    echo "<div class=\"input-main\">";
                        echo "<input type=\"number\" min=\"0\" max=\"100\" name=\"number-input\" class=\"input_number\"  id=\"number-input\" />";
                    echo "</div>";
                    
                    echo "<div class=\"vertical-centre input-main-inline\"   >";
                        echo "<p>Number:</p>";
                        echo "<input type=\"number\" min=\"0\" max=\"100\" name=\"number-input\" class=\"input_number\" id=\"number-input\" />";
                    echo "</div>";
                    
                    echo "<div class=\"clear\"></div>";
                    
                    echo "<div class=\"vertical-centre input-main-inline\" >";
                        echo "<p>Text:</p>";
                        echo "<input type=\"text\" name=\"third-input\" value=\"\" placeholder=\"third input - class: input-main-inline\" id=\"third-input\" />";
                    echo "</div>";
                    
                    
                    
                    echo "<div class=\"vertical-centre input-main-inline\" >";
                        echo "<p>Text:</p>";
                        echo "<input type=\"text\" name=\"third-input\" value=\"\" placeholder=\"third input - class: input-main-inline\" id=\"third-input\" />";
                    echo "</div>";
                    
                    
                    
                    echo "<div class=\"vertical-centre input-main-label\"  >";
                        echo "<p>Image Placeholder:</p>";
                        echo "<img class=\"click\" id=\"btn_edit_image\" src=\"/images/burger.png\" />";
                    echo "</div>";
                    
                    echo "<div class=\"image-placeholder\" >";
                        echo "<p style=\"text-align:center; vertial-align:middle; line-height:225px; color:gray;\" >Click to Add Image</p>";
                    echo "</div>";
                    
                    
                echo "</div>";
                
                

                //Column 2 of 2
                echo "<div class=\"con_column_2_2\" >";
                
                    echo "<div id=\"con_listBox_producer\" style=\"float:left;\" ></div>"; //ui listBox
                    echo "<div class=\"clear\" ></div>";
                    
                    echo "<div class=\"vertical-centre input-main-label\" >";
                        echo "<h2>Input with Image:</h2>";
                    echo "</div>";
                    echo "<div class=\"vertical-centre input-main\" style=\"height:30px; \" >";
                        echo "<input name=\"third-input\" class=\"input_text\" value=\"\" placeholder=\"image should be centred\" id=\"third-input\" />";
                        echo "<img class=\"click\" id=\"btn_add_1\" src=\"/images/add.png\" />";
                        echo "<div style=\"float:left; height:24px; width:24px; background-color:green;\"></div>";
                    echo "</div>";

                    echo "<div class=\"vertical-centre input-main-label\" >";
                        echo "<h2>Search:</h2>";
                    echo "</div>";
                    echo "<div class=\"search_box vertical-centre\" style=\"height:28px; width:350px; \" >";
                        echo "<input style=\"float:left; margin:0 0 0 5px; border:none; background-color: \" name=\"third-input\" class=\"input_text\" value=\"\" placeholder=\"search wines...\" id=\"search-input\" />";
                        echo "<img class=\"click\" style=\"float:right; margin-right:5px;\" id=\"btn_add\" src=\"/images/delete.png\" height=\"21px\" />";
                    echo "</div>";
                    
                    echo "<div style=\"margin-top:15px;\" \>";
                        echo "<h1>Heading 1 Text</h1>";
                        echo "<h2>Heading 2 Text</h2>";
                        echo "<h3>Heading 3 Text</h3>";
                        echo "<h4>Heading 4 Text</h4>";
                        echo "<p>This is a sentence of paragraph text</p>";
                    echo "</div>";
                    
                    echo "<div id=\"con_quality_rating_box\" style=\"margin-top:15px; width:300px; height:150px; background-color:lightblue;\" \>";
                        echo "<div style=\" \" >";
                        ?>
                            <select id="example">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        <?php
                        echo "</div>";
                    echo "</div>";
                  
                    
                echo "</div>"; //column_2_2

                echo "<div id=\"error_labels\" class=\"clear\" style=\"padding:10px; display:none;\" >";
                    //empty - placeholder for validation error messages
                echo "</div>";

                //Button Bar
                echo "<div class=\"con_button_bar\" >";
                    echo "<input type=\"button\" name=\"btn_save\"  id=\"btn_save\" value=\"Save\" class=\"submit form_input\" />";
                    echo "<input type=\"button\" name=\"btn_close\" id=\"btn_close\" value=\"Close\" class=\"btn_close form_input\" />";
                    echo "<input type=\"button\" name=\"btn_edit\" id=\"btn_edit\" value=\"Edit\" />";
                    echo "<input type=\"button\" name=\"btn_delete\" id=\"btn_delete\" value=\"Delete\" />";
                echo "</div>";

                //clear page_container
                echo "<div class=\"clear\"></div>";

            echo "</div>"; //con_single_form


    echo "</div>"; //page_container
    ?>
        
     <ul id="test-menu">
        <li class="ui-menu-item-first">Open</li>
        <li>Delete</li>
        <li>Copy</li>
        <li class="ui-menu-item-last">Edit</li>
      </ul>
        
    <ul id="main_menu">
        <li class="ui-menu-item-first ui-menu-header">Choose an action</li>
        <li>Wines</li>
        <li>Acquisitions</li>
        <li>Reference</li>
        <li class="ui-menu-item-last">Settings</li>
    </ul> 
        
    <div id='menu'>
        <div class="ui-menu-item-first ui-menu-header">Choose an action</div>
        <div>Edit<img class="click" style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
        <div>Add<img class="click" style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
        <div class="ui-menu-item-last"><img class="click" style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" />Delete</div>
    </div>
        
        
    <?php
    require_once("$root/includes/script_libraries.inc.php");//include all script libraries
    ?>
    <script src="/libraries/jquery.barrating.js"></script>
    <script type="text/javascript">
        
        $(document).ready(function(){
            
            //ratings
            
        
      
          
              $('#example').barrating({
                theme: 'fontawesome-stars'
              });
           
            
            
            
            //customisable dialog example
            function show_dialog(){

                $("#dialog").dialog({
                    modal: true,
                    title: "A new title...",
                    minWidth: 400,
                    minHeight: 200,
                    position: { 
                        my: "centre", 
                        at: "centre", 
                        of: ".con_single_form" 
                    },
                    buttons: {
                        OK: {
                            click: function() {
                                $(this).dialog('close');
                            },
                            class: 'test',
                            text: 'OK'
                        },
                        CANCEL: function() {
                            $(this).dialog('close');
                            
                        }
                    },
                    dialogClass: "dialog-style"
                });
            }
            

            $("#search-input").change(function(){
                console.log("change event triggered");
            });
            
            $(".search_container_local :input").keyup(function(){
                console.log("key up event triggered");
            });
            
            $("#search-input").click(function(){
                console.log("this is the first click event trigger");
            });
            
            $("#search-input").click(function(){
                //search container change event
                //if($(this).val()==""){
                    console.log("input was clicked");
                //}
            });
            
            $("#btn_save").click(function(){
                //set dialog details
                $("#dialog-text").text("This is new text set dynamically by jquery.");
                //ui-icon-alert, ui-icon-info, ui-icon-notice, ui-icon-help
                $("#dialog-icon").removeClass().addClass("ui-icon ui-icon-info");
                show_dialog();
            });
            
            
            $(".control").click(function(){
                //set dialog details
                $("#dialog-text").text("The edit button was clicked.");
                //ui-icon-alert, ui-icon-info, ui-icon-notice, ui-icon-help
                $("#dialog-icon").removeClass().addClass("ui-icon ui-icon-notice");
                show_dialog();
            });
            
            $("#second").click(function(){
                //set dialog details
                $("#dialog-text").text("The edit button was clicked.");
                //ui-icon-alert, ui-icon-info, ui-icon-notice, ui-icon-help
                $("#dialog-icon").removeClass().addClass("ui-icon ui-icon-notice");
                show_dialog();
            });
            
            
            //listBox ui example    
            $("#con_listBox_producer").listBox({ //setup Producer listBox
                title: "Producer",
                width: 225,
                height: 300,
                showBorder: true,
                listContent: '/admin/rpc_listBox_producer_html.php',
                showFilter: true,
                clickAdd: function(event, data){

                },
                clickRemove: function(event, data){
   
                },
                clickEdit: function(event, data){

                }
            });
            
            //main menu example
            var main_menu = $("#main_menu").menu({
               items: "> :not(.ui-menu-header)",
               select: function( event, ui ) {
                    menu_select({ //pass selcted object to menu function
                        selected_item: ui.item[0].textContent,
                        menu_id: $(this).attr('id')
                    });
                }
            }).hide();
                
 
            $('#btn_main_menu, #btn_add_1').click(function() {
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

                return false;
            });
            
            
            //pop-up menu example
            var edit_menu = $("#menu").menu({
                items: "> :not(.ui-menu-header)",
                select: function( event, ui ) {
                    console.log('menu item selected ');
                    console.log(event);
                    console.log(ui);
                    
                    menu_select({ //pass selcted object to menu function
                        selected_item: ui.item[0].textContent,
                        menu_id: $(this).attr('id')
                    });
                    
                }
          
            }).hide();
            
            
            $('#btn_edit_image').click(function() {
                // Make use of the general purpose show and position operations
                // open and place the menu where we want.
                
                //***set menu name here***
                var this_menu = edit_menu;
                
                //close all other menus before opening this one
                var this_menu_id = this_menu.attr('id');
                $(".ui-menu:not(#"+this_menu_id+")" ).hide();
                
                //show menu
                this_menu.show().position({
                      my: "left top",
                      at: "left bottom",
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
            
            function menu_select(selected_object){
                var selected_item = selected_object['selected_item'];
                var menu_id = selected_object['menu_id'];
                
                console.log('Menu_Select Function. Item: '+selected_item + ' Menu: '+menu_id);
                
            }
            

     
            

  

        });
        
        
  </script>      

    
</html>


