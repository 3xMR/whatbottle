<?php
/*Manage Pictures
 * 
 * Vintage Image: $_SESSION['var_vintage_temp']['image1'] = vintageid_frontlabel.jpg
 * Edit Vintage Image: $_SESSION['var_vintage_temp']['image1_tmp']
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
require_once("$root/includes/css.inc.php"); //include style sheets
echo "<link type=\"text\/css\" href=\"/css/jquery.jcrop.css\" rel=\"stylesheet\" />";
echo "<title>Image Manager - What Bottle?</title>";//page title
require_once("$root/includes/script_libraries.inc.php"); //include all script libraries

?>

</head>
<body>
    
<?php

require_once("$root/includes/standard_dialogs.inc.php");

//variables
$wine_id = $_SESSION['var_vintage_temp']['wine_id'];
$vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];
$wine = $_SESSION['var_vintage_temp']['wine'];
$producer = $_SESSION['var_vintage_temp']['producer'];
$year = $_SESSION['var_vintage_temp']['year'];
$search_term = urlencode("$producer $wine $year");
$path = rtrim($root, '/\\');

//page container
echo "<div class=\"page_container\" >";

    //header
    require_once("$root/includes/nav/topheader.php");

    echo "<div class=\"con_single_form rounded\" >";
    
        echo "<div class=\"con_title_bar\" >";
            //wine name
            echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px;\" >";
                echo "<div style=\"float:left; width:58px;\" >";
                    echo "<img src=\"/images/vintage_flat_grey_64.png\" height=\"48px\" width=\"48px\" >";
                echo "</div>";
                echo "<div style=\"width:auto; float:left; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >".$_SESSION['var_vintage_temp']['year']." ".$_SESSION['var_vintage_temp']['wine'].", ".$_SESSION['var_vintage_temp']['producer']."</h1>";
                    echo "<h3 style=\"color:darkgrey;\">".$_SESSION['var_vintage_temp']['country'].", ".$_SESSION['var_vintage_temp']['region'];
                    if($_SESSION['var_vintage_temp']['subregion']){
                        echo ", ".$_SESSION['var_vintage_temp']['subregion']."</h3>";
                    }else{
                        echo "</h3>";
                    }
                echo "</div>";
                echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left; height:50px;\"  >";
                    echo "<img id=\"process_indicator\" src=\"/images/ajax_loader.gif\" height=\"24px\" width=\"24px\" />";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
            echo "</div>";
        echo "</div>"; //con_title_bar

        
        echo "<div  id=\"con_edit\" >";
            echo "<div id=\"con_image_outer_frame\" >";
                echo "<div id=\"con_image_inner_frame\" >";
                    echo "<img id=\"edit_image\" src=\"\" style=\"margin-left:auto; margin-right:auto;\"  >"; //filled by jS
                echo "</div>";
            echo "</div>";
        echo "</div>";
        
        
        echo "<div id=\"con_same_wine_images\" >"; //display labels for other vintages of the same wine
  
            $obj_vintage = new vintage();
            $columns = "vintage_id,year, image1";
            $where = " wine_id = $wine_id AND vintage_id <> $vintage_id ";
            $rst_vintages = $obj_vintage->get($where, $columns);
            
            echo "<div id=\"con_other_vintages\" >";
                echo "<div id=\"con_other_vintages_title\" >";
                    echo "<p>Other Vintage Labels</p>";
                echo "</div>";
                echo "<div style=\"float:left; width:900px;\">";
                    if($rst_vintages){
                        foreach($rst_vintages as $vintage_image){
                            $image_name = $vintage_image['image1'];
                            $image_path = "/images/labels/$image_name";
                            $vintage_id = $vintage_image['vintage_id'];

                            //set size of image to fit in placeholder
                            list($source_width, $source_height, $type, $attr) = getimagesize("$path/$image_path");
                            $target_width = 125;
                            $target_height = 188;
                            $height_ratio = $source_height/$target_height;
                            $width_ratio = $source_width/$target_width;
                            $target_ratio = ($height_ratio > $width_ratio) ? $height_ratio : $width_ratio;
                            $set_height = $source_height/$target_ratio;
                            $set_width = $source_width/$target_ratio;

                            echo "<div id=\"con_wine_vintages\" style=\"width:131px; height:215px; border:solid 1px gray; float:left; margin-left:5px; margin-right:5px;\" >";
                                echo "<div style=\"width:100%; height:15px; text-align:center; color:white; padding:3px 0px 3px 0px; background-color:darkgray; border-bottom:solid 1px gray;\" >";
                                    echo "<p>".$vintage_image['year']."</p>";
                                echo "</div>";
                                echo "<div class=\"other_vintage_image\" id=\"$image_name\" style=\"width:125px; height:188px; padding:3px;\" >";
                                    echo "<img src=\"$image_path\" width=\"$set_width\" height=\"$set_height\" style=\" display:block; margin-left:auto; margin-right:auto;\" >";
                                echo "</div>";
                            echo "</div>";

                        }
                    }
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"con_sub_button_bar\" style=\"width:auto; margin-top:5px;\" >";
                echo "<input type=\"button\" name=\"btn_search\" id=\"btn_search\" value=\"Google Search\" />";
            echo "</div>";
            
        echo "</div>";

        


        
        
        echo "<div class=\"con_sub_button_bar\" style=\"clear:both; width:auto;\" id=\"con_edit_controls\" >";
            echo "<hr/>";
                echo "<input type=\"button\" name=\"btn_delete\" id=\"btn_delete\" value=\"Delete Image\" />";
                echo "<input type=\"button\" name=\"btn_rotate_left\" id=\"btn_rotate_left\" value=\"Rotate Left\" />";
                echo "<input type=\"button\" name=\"btn_rotate_right\" id=\"btn_rotate_right\" value=\"Rotate Right\" />";
                echo "<input type=\"button\" name=\"btn_crop\" id=\"btn_crop\" value=\"Crop\" />";
        echo "</div>";
        
        echo "<div style=\"clear:both; margin-top:10px; width:auto;\" id=\"file-uploader\" >";
            echo "<noscript>";
                echo "<p>Please enable JavaScript to use file uploader.</p>";
            echo "</noscript>";
        echo "</div>";
        

    
        //debug panel
        echo "<div class=\"clear\" id=\"debug_panel\">";
                //only show when trying to debug

                echo "<div style=\"float:left; width:300px;\" id=\"con_saved\" >";
                    echo "<h2>Temp Image:</h2>";
                    echo "<img id=\"temp_image\" src=\"\" width=\"200\" >";
                echo "</div>";

                echo "<div style=\"float:left; width:300px;\" id=\"con_saved\" >";
                    echo "<h2>Saved Image:</h2>";
                    echo "<img id=\"saved_image\" src=\"\" width=\"200\" >";
                echo "</div>";

                echo "<div class=\"clear\"></div>";

                echo "wine_id: <input type=\"\" name=\"wine_id\" value=\"".$_SESSION['var_vintage_temp']['wine_id']."\" /><br/>";
                echo "vintage_id: <input type=\"\" name=\"vintage_id\" value=\"".$_SESSION['var_vintage_temp']['vintage_id']."\" /><br/>";
                echo "search term: <input type=\"\" id=\"search_term\" name=\"search_term\" value=\"$search_term\" /><br/>";
                echo "<br/>";

                echo "edit_name: <input type=\"\" id=\"edit_name\" name=\"file_name\" size=\"100\" /><br/>";
                echo "edit_status: <input type=\"\" id=\"edit_status\" /><br/>";

                //echo "edit_prev_name: <input type=\"\" id=\"file_prev_name\" size=\"100\"/><br/>";
                //echo "edit_prev_status: <input type=\"\" id=\"file_prev_status\" /><br/>";

                echo "temp_name: <input type=\"\" id=\"temp_name\" size=\"100\" /><br/>";
                echo "temp_status: <input type=\"\" id=\"temp_status\" /><br/>";

                echo "saved_name: <input type=\"\" id=\"saved_name\" size=\"100\" /><br/>";
                echo "saved_status: <input type=\"\" id=\"saved_status\" /><br/>";

                echo "src_x: <input type=\"text\" size=\"4\" id=\"x\" name=\"x\" />";
                echo "src_y: <input type=\"text\" size=\"4\" id=\"y\" name=\"y\" />";
                echo "<input type=\"text\" size=\"4\" id=\"x2\" name=\"x2\" />";
                echo "<input type=\"text\" size=\"4\" id=\"y2\" name=\"y2\" />";
                echo "targ_w: <input type=\"text\" size=\"4\" id=\"w\" name=\"w\" />";
                echo "targ_h: <input type=\"text\" size=\"4\" id=\"h\" name=\"h\" />";

        echo "</div>";

        //form buttons
        echo "<div class=\"con_button_bar\" >";
            echo "<input type=\"button\" id=\"btn_save\" name=\"btn_save\" value=\"Save\" />";
            echo "<input type=\"button\" id=\"btn_close\" class=\"btn_close\" name=\"btn_close\" value=\"Close\" />";         
        echo "</div>"; //con_form_buttons

        echo "<div class=\"clear\"></div>";

    echo "</div>"; //single_form_container

echo "</div>"; //page_container

?>


</body>

<script type="text/javascript" src="/libraries/jquery.jcrop.min.js"></script>
<script type="text/javascript">
//Note: php.ini memory_limit must be greater than default 64MB to handle normal photo size image
$(document).ready(function(){
    
    //TODO: Ajax spinners - change color to gray or black , currently green and there shouldn't be two
    //TODO: Show spinner when uploading and processing images as it is slow across internet
    //TODO: When uploading image it shows full size before being resized
    //FIX: Rotate image not working on .com
    //FIX: Image still showing when deleted on .com

    //globals
    var src_images = "/images/labels/";
    var src_uploads =  "/images/labels/uploads/";
    var jcrop_api;
    var var_image_edit;


    //page control object
    var obj_page = new page_control({
        save_function: function(){
            save_page();  
        },
        page_url: "/vintage/select_image.php", //set page url
        pop_up: true //pop-up page - don't set as return url
        
    });
    
    //show ajax activity
    $('#process_indicator')
    .hide()  // hide it initially
    .ajaxStart(function() {
        $(this).show();
    })
    .ajaxStop(function() {
        $(this).hide();
    });
    
       
    //get_images from session
    get_image();


    //hide image tag until src is provided
    $('#img_tag').hide();
    $('#saved_img_tag').hide();
    $('#debug_panel').hide(); //hide of show debug panel


    //cropbox fill input fields
    function showCoords(c){
        console.log('showCoords:');
        console.log(c);
        jQuery('#x').val(c.x);
        jQuery('#y').val(c.y);
        jQuery('#x2').val(c.x2);
        jQuery('#y2').val(c.y2);
        jQuery('#w').val(c.w);
        jQuery('#h').val(c.h);
        //run crop
        crop_image();
    };


    function delete_image(callback){
        //delete edit image
          
        var vintage_id = $('#vintage_id').val();
        var edit_name = $('#edit_name').val();
        //var edit_status = $('#edit_status').val();
          
        $.post("/vintage/rpc_vintage.php", {
            action: 'delete_image_edit',
            vintage_id: vintage_id,
            edit_name: edit_name,
            edit_status: 'deleted'
            },
            function(data){
                if(data.success){
                    console.log('delete_image successful msg: '+data.msg);
                    //update fields
                    $('#edit_name').val("");
                    $('#edit_status').val("");
                    obj_page.set_is_dirty(true); //set is_dirty
                    get_image(); //reload images
                    
                    if(typeof callback === 'function'){
                       callback();
                    }
                    
                }else{
                    var msg = 'Delete Image failed with error: '+data.error;
                    console.log(msg);
                }
                
                

        }, "json");

    }


        //_____Events & Actions_____

    $("#btn_close").click(function(){
        //close form - clean up images using before_close function
        console.log('btn_close...cancelled');
        //obj_page.set_is_dirty(false); //set is_dirty false to prevent warnings
        
        //run clean-up before closing page
        $.when(cleanup()).done(function(data){  
            obj_page.close_page(); //close page
        });

    });
    

    $("#btn_save").click(function(){
        //save image then close page
        console.log('btn_save');
        $.when(save_page()).done(function(data){
            obj_page.close_page(); //close page
        });
    });
    
    
    $("#btn_delete").click(function(){
        //delete edit image
        console.log('btn_delete...');
        //delete edit image
        delete_image();
        
    });
    
        
    $("#btn_rotate_left").click(function(){
        //rotate image 90 deg left
        console.log('btn_rotate_left...');
        
        rotate_image(90);
        
    });
    
    
    $("#btn_rotate_right").click(function(){
        //rotate image 90 deg left
        console.log('btn_rotate_left...');
        
        rotate_image(270);
        
    });
    
    
    
    $("#btn_crop").click(function(){
        //rotate image 90 deg left
        console.log('btn_crop...');
        
        if(jcrop_api){
            //crop is on
            console.log('crop is on - switch it off');
            //remove highlight
            $('#btn_crop').removeClass('highlight');
            
            jcrop_api.destroy();
            $('#edit_image').css('visibility', 'visible');
            jcrop_api = null;
            
        } else {
            //crop is off
            console.log('crop is off - switch it on');
            //highlight button
            $('#btn_crop').addClass('highlight');
            
            $('#edit_image').Jcrop({
                    onSelect: showCoords
                }, function(){
                    jcrop_api = this;
            });  
        }
        
    });
    
    
    $(".other_vintage_image").click(function(){
       console.log('vintage_id clicked = ' + $(this).attr('id'));
       copy_image_to_uploads($(this).attr('id')); //copy image and make it the new edit image
       
    });
    
    

    $("#btn_search").click(function(){

       google_pre = "https://www.google.com/search?q=";
       search_term = $("#search_term").val();
       google_post = "&source=lnms&tbm=isch";
       href = google_pre + search_term + google_post;
       console.log(href);
       window.open(href, '_blank');
       
    });
    

    
    function copy_image_to_uploads(image_name){
        /*  copy selected existing image in /images/labels 
         *  to /images/labels/uploads
         */ 
        
        if(!image_name){
            console.log('image_copy_to_upload - image name parameter empty');
            return false;
        }
        
        $.post("/vintage/rpc_vintage.php", {
            action: 'image_copy_to_upload',
            image_name: image_name
            },
            function(data){
                if(data.success){
                    console.log('image_copy_to_upload successful msg:'+data.msg);
                    obj_page.set_is_dirty(true);
                    get_image(); //reload image
                }else{
                    var msg = 'image_copy_to_upload failed with error: '+data.error;
                    console.log(msg);
                }

        }, "json");
        
        
    }
   
   
    function cleanup(callback){
        //clean-up temp edit image on close
        console.log('cleanup function');
        
        var def = $.Deferred(); //create deferred
        var edit_name = $('#edit_name').val();
        
        if(edit_name){ //delete temp edit file
            
            console.log('delete_image_edit');
            
            $.post("/vintage/rpc_vintage.php", {
                action: 'delete_image_edit'
                },
                function(data){
                    if(data.success){
                        console.log('fnc: cleanup - delete_image_edit OK msg='+data.msg);
                        def.resolve(true); //resolve promise
                    
                        if(typeof callback === 'function'){
                           console.log('fnc: clean-up - callback');
                           callback();
                        }
                        
                    }else{
                        var msg = 'Cleanup function failed with error: '+data.error;
                        console.log(msg);
                        def.resolve(false); //resolve promise
                        $(".con_button_bar").notify(msg,{
                            position: "top left",
                            style: "msg",
                            className: "error",
                            arrowShow: false
                            }
                        );
                    }

            }, "json");
            
        }else{
            var msg = "no edit_name provided to cleanup edit_name: "+edit_name;
            console.log(msg);
            def.resolve(msg);
        }
        
        return def.promise(); //return promise

    }
    
    
    function rotate_image(degrees){
        //rotate image
        $('#process_indicator').show();
        console.log('fnc: rotate_image by degrees: '+degrees);
        var edit_name = $('#edit_name').val();

        if(edit_name){

            $.post("/vintage/rpc_vintage.php", {
                action: 'rotate_image',
                degrees: degrees
                },
                function(data){
                    $('#process_indicator').hide();
                    if(data.success){
                        console.log('rotate_image OK. msg:'+data.msg);
                        console.log('new image name = '+data.edit_name);
                        obj_page.set_is_dirty(true);//set is_dirty
                        get_image();//reload images
                    }else{
                        console.log('rotate_image failed with error: '+data.error);
                    }

            }, "json");
            
        }else{
            console.log('no edit image to rotate');
        }
    }
    
    
    function crop_image(){
        //crop image
        
        var x = $('#x').val();
        var y = $('#y').val();
        var x2 = $('#x2').val();
        var y2 = $('#y2').val();
        var w = $('#w').val();
        var h = $('#h').val();
      
        console.log('crop_image var_image_edit:');
        console.log(var_image_edit);
        
        //destroy crop
        jcrop_api.destroy();
        $('#edit_image').css('visibility', 'visible');
        $('#btn_crop').removeClass('highlight');
        
        $.post("/vintage/rpc_vintage.php", {
            action: 'crop_image',
            x: x,
            y: y,
            //x2: x2,
           // y2: y2,
            w: w,
            h: h,
            image_width: var_image_edit.width_display
            },
            function(data){
                if(data.success){
                    console.log('crop_image OK. msg:'+data.msg);
                    obj_page.set_is_dirty(true); //set is_dirty
                    console.log(data.data);
                    //obj_page.set_ignore_dirty(true); //prevent is_dirty warnings temporarily while page reloads
                    //location.reload();//reload page to rezize image
                    get_image();
                }else{
                    console.log('crop_image failed with error: '+data.error);
                }

        }, "json");
        
        
    }
    

    function add_src(filename, status){
        //add src to filename based on status
        
        if(filename && status){
            if(status==='new'){
                return src_uploads+filename;
            } else {
                return src_images+filename;
            }
        } else {
            return false;
        }
        
    }
    
    
    function set_image_size(element_id, image_id, image_data){
        //get size of containing div and set image to fit within
        console.log('set_image_size element_id = '+element_id);
        var div = $('#' + element_id);
        width_div = div.width();
        height_div = div.height();
        console.log('div Width = ' + width_div + ' div Height = '+height_div);
        
        var image = $('#' + image_id);
        width_image = image_data.orig_w;
        height_image = image_data.orig_h;
        console.log('Image Width = ' + width_image + ' Image Height = ' + height_image);
        
        width_ratio = width_image/width_div;
        height_ratio = height_image/height_div;
        target_ratio = height_ratio > width_ratio ? height_ratio : width_ratio;
        console.log('target_ratio = '+target_ratio);
        
        width_set = width_image/target_ratio;
        height_set = height_image/target_ratio;
        $('#' + image_id).height(height_set).width(width_set);
        console.log('Width Set = ' + width_set + ' Height Set = ' + height_set);
        
        //set global
        var_image_edit = {
            width_actual: image.width(),
            height_actual: image.height(),
            width_display: width_set,
            height_display: height_set
        };
        
        
    }
    
    
    //get images
    function get_image(callback){
        //retrieve image details from session
        console.log('get_image from session');
        
        $.post("/vintage/rpc_vintage.php", {
            action: 'get_image' },
            function(data){
                if(data.success){
                    console.log('get_image from session successful');
                    console.log(data);
                    
                    if(data.edit_name){
                        $('#edit_name').val(data.edit_name);
                        $('#edit_status').val(data.edit_status);
        
                        if(data.edit_status == 'deleted'){
                            $('#edit_image').attr("src", src).hide(); //update image src and hide
                            //$('#con_edit').hide();
                            $('#con_edit_controls').hide();
                            return true;
                        }else{
                            var src = add_src(data.edit_name, data.edit_status);
                            $('#edit_image').attr("src", src).show(); //update image src and show
                            $('#con_edit').show();
                            $('#con_edit_controls').show();
                        }
                        
                        console.log('edit_name: '+data.edit_name+' edit_status: '+data.edit_status+' src: '+src);
                        console.log('get_image data:');
                        console.log(data.data);
                        
                        set_image_size('con_image_inner_frame','edit_image',data.data); //resize image to fit within div
                        
                        $('#edit_image').load(function(){
                            console.log('image loaded...');
                        });

                    }else{
                        //hide image
                        console.log('no edit_image returned - hide edit_image');
                        //$('#con_edit').hide();
                        $('#con_edit_controls').hide();
                    }
                    
                    if(data.saved_name){
                        $('#saved_name').val(data.saved_name);
                        $('#saved_status').val(data.saved_status);
                        var src = add_src(data.saved_name, data.saved_status);
                        $('#saved_image').attr("src", src).show();
                    }
                    
                    if(data.temp_name){
                        $('#temp_name').val(data.temp_name);
                        $('#temp_status').val(data.temp_status);
                        var src = add_src(data.temp_name,data.temp_status);
                        $('#temp_image').attr("src", src).show();
                    }
                    
                    //callback
                    if(typeof callback === 'function'){
                        callback();
                    }

                }else{
                    console.log('get_image failed with error: '+data.error);

                }
                
                console.log('get_image msg: '+data.msg);

        }, "json");
        
    }
    
    
    function put_image_session(){
        //put image details to session
        var def = $.Deferred();

        var edit_name = $('#edit_name').val();
        var edit_status = $('#edit_status').val();
        console.log('put_image_session edit_name: '+edit_name+' edit_status: '+edit_status);
        
        //push details to session   
        $.post("/vintage/rpc_vintage.php", {
            action: 'put_image_session',
            edit_name: edit_name,
            edit_status: edit_status
            },
            function(data){
                if(data.success){
                    var msg = 'put_image_session successful msg: '+data.msg;
                    console.log(msg);
                    get_image(); //reload image
                    def.resolve(data); //resolve promise
                }else{
                    msg = 'put_image_session failed with error: '+data.error;
                    console.log(msg);
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
    
 
    function save_page(callback){
        //update vintage session with image details on close of page
        var def = $.Deferred();

        var edit_name = $('#edit_name').val();
        var edit_status = $('#edit_status').val();
        console.log('save_page edit_name: '+edit_name+' edit_status: '+edit_status);
        
        //push details to session   
        $.post("/vintage/rpc_vintage.php", {
            action: 'save_image_to_vintage'
            },
            function(data){
                if(data.success){
                    var msg = "Save Image successful";
                    console.log('save_image_to_vintage successful msg:'+data.msg);
                    obj_page.set_is_dirty(false); 

                    $(".con_button_bar").notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "success",
                        arrowShow: false
                        }
                    );

                    //get_image(); //reload image
                    if(typeof callback === 'function'){
                        callback();
                    }
                    
                    def.resolve(data); //resolve promise

                }else{
                    var msg = 'Save Image failed with error: '+data.error;
                    console.log(msg);
                    def.reject(msg);
                    $(".con_button_bar").notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "error",
                        arrowShow: false
                        }
                    );
                }

        }, "json");
            
        return def.promise();
        
    }



    var uploader = new qq.FileUploader({
        // pass the dom node (ex. $(selector)[0] for jQuery users)
        element: document.getElementById('file-uploader'),
        // path to server-side upload script
        action: '/images/labels/rpc_image_uploader.php',
        //debug
        debug: true,
        //onsubmit
        onSubmit: function(id, fileName){
            //check filename for valid file types
            console.log('uploader id: '+id+' fileName: '+fileName);
            var ext = fileName.split('.').pop().toLowerCase();
            if($.inArray(ext, ['gif','png','jpg','jpeg','bmp']) == -1) {
                alert('Not a valid image file!');
                return false;
            }
        },
        //response
        onComplete: function(id, fileName, responseJSON){
            
            var file_name = responseJSON.file_name;
            console.log('uploader completed file_name: '+responseJSON.file_name);
            
            $('#edit_image').height('').width(''); //remove dims so image is uploaded native size and can be resized by get_image()
            
            //update previous file settings
            $('#file_prev_name').val($('#edit_name').val());
            $('#file_prev_status').val($('#edit_status').val());
            
            //update edit name field
            $('#edit_name').val(file_name);
            //set status
            $('#edit_status').val('new');
            
            //set is dirty
            obj_page.set_is_dirty(true);

             //show edit image
            var src = "/images/labels/uploads/"+file_name;
            $('#edit_image').attr("src", src).show();
            $('#con_edit').show();
        
            //push new details to session
            put_image_session();     

        }
        
    });




});

</script>
</html>