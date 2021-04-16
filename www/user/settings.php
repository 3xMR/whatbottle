<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php"); //include initialise script
require_once("$root/classes/class.db.php"); //include standard db class
require_once("$root/classes/User.php"); //include standard db class
?>

<html>
    
    <head>
        <?php
        require_once("$root/includes/standard_html_head.inc.php"); //include standard html headers for responsive web page
        require_once("$root/includes/css.inc.php");//include style sheets 
        ?>
        <title>Settings</title>
    </head>

<body>

<div class="page_container" >
        <?php require_once("$root/includes/nav/topheader.php"); ?>
        <div class="con_single_form">
            
            <?php
            $user = new UserObj();
            if($user ->isAuthed()){
                $array_name = $user->getUserName();
                $firstName = $array_name['firstName'];
                $lastName = $array_name['lastName'];
            }
            
            //Title bar
            echo "<div class=\"con_title_bar\" >";
                //echo "<div class=\"con_title_container\" style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px;\" >";
                echo "<div class=\"con_title_container\" >";
                    //title bar image
                    echo "<div class=\"title_image\"  >"; 
                        echo "<img src=\"/images/settings_flat_grey_128.png\" >"; 
                    echo "</div>";
                    //title bar text
                    echo "<div class=\"title_heading\" >";
                        echo "<h1>Settings & Account Details</h1>";
                        echo "<h3 style=\"color:darkgray;\" >$firstName $lastName</h3>";
                    echo "</div>";
                    //process indicator
                    echo "<div class=\"process_indicator\" >";
                        echo "<img id=\"process_indicator\" src=\"/images/ajax_loader.gif\" height=\"24px\" width=\"24px\" />";
                    echo "</div>";
                    echo "<div class=\"clear\"></div>";
                echo "</div>";
            echo "</div>"; //con_title_bar
        
            //Page contetents go here
            ?>
            <div style="float:left; margin-top:15px; width:100%;">
                <div id="tabs">
                    <ul>
                      <li><a href="#tabs-1">Account</a></li>
                      <li><a href="#tabs-2">Password</a></li>
                      <li><a href="#tabs-3">Settings</a></li>
                    </ul>
                    <div id="tabs-1">
                        <div>
                            <?php echo "<h3 style=\"color:darkgray;\" >$firstName $lastName</h3>"; ?>
                            <div class="input-button" >
                                    <?php
                                    if(is_authed()){ //buttons that require login within here
                                        echo "<input type=\"button\" id=\"btn_logout\" class=\"btn_logout\" value=\"Logout\" />";
                                    }
                                    ?>
                            </div>
                            
                            <div class="input-main-label" style="margin-top:10px;">
                                <p>First Name</p>
                            </div>

                            <div class="input-main" >
                                <input type="text" value="" name="firstname" id="firstname" autocomplete="off" >
                            </div>
                            
                            <div class="input-main-label" >
                                <p>Last Name</p>
                            </div>
                            
                            <div class="input-main" >
                                <input type="text" value="" name="lastname" id="lastname" autocomplete="off" >
                            </div>
                            
                        </div>
                        
                        <div class="clear"></div>
                    </div>
                    <div id="tabs-2">
                        <div style="float:left; clear:both; margin-top:15px;">
                            <form action="\user\template.php" method="post" id="frm_change_password" name="frm_change_password" autocomplete="off">

                                <div class="hidden">
                                    <input type="hidden" value="<?php echo $_SESSION['var_wine_temp']['is_dirty'];?>" name="is_dirty" id="is_dirty"><br/>
                                    <input type="hidden" value="<?php echo $_SESSION['var_wine_temp']['status'];?>" name="status" id="status"><br/>
                                </div>    

                                <div class="input-main-label" >
                                    <p>Current Password</p>
                                </div>

                                <div class="input-main" >
                                    <input type="password" value="" name="password" id="password" autocomplete="off" >
                                </div>

                                <div class="input-main-label" >
                                    <p>New Password</p>
                                </div>

                                <div class="input-main" >
                                    <input type="password" value="" name="passwordNew" id="passwordNew" autocomplete="off" >
                                </div>

                                <div class="input-main-label" >
                                    <p>Confirm New Password</p>
                                </div>

                                <div class="input-main" >
                                    <input type="password" value="" name="passwordNewConfirm" id="passwordNewConfirm" autocomplete="off" >
                                </div>
                                
                                <div class="input-button" >
                                    <?php
                                    if(is_authed()){ //buttons that require login within here
                                        echo "<input type=\"button\" id=\"btn_change_password\" value=\"Save\" />";
                                    }
                                    ?>
                                </div>

                            </form>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div id="tabs-3">
                       
                        <div class="clear"></div>
                    </div>
          
                </div> <!--tabs-->
            </div>
            

            
            <?php
            //button Bar
            echo "<div class=\"con_button_bar\" >";
                if(is_authed()){ //buttons that require login within here
                    echo "<input type=\"button\" id=\"btn_save\" value=\"Save\" class=\"btn_save\" />";
                }  
                echo "<input type=\"button\" id=\"btn_close\" value=\"Close\" class=\"btn_close\" />";
            echo "</div>";

            ?>

            <div class="clear"></div>
        
        </div> <!--con_single_form-->
        

</div> <!--page_container-->

     <!--main menu html-->   
    <div id='main_menu' class="pop_up main_menu" style="width:200px; display:none; position:fixed; z-index:30;">
        <div class="ui-menu-item-first" >New Wine<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
        <div>New Acquisition<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
        <div>Wines<img style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
        <div class="ui-menu-item-last">Reference Data<img  style="float:right; margin-top:2px;" src="/images/arrow_next_black.svg" height="21px" /></div>
    </div>

     
<?php
require_once("$root/includes/standard_dialogs.inc.php"); //include standard dialogs
require_once("$root/includes/script_libraries.inc.php"); //include all script libraries
?>

<script type="text/javascript">
    
    //get page url
    var href = document.location.href;
       
    //create page control object and pass save function
    var obj_page = new page_control({
        save_function: function(){
            return save_page(true);
        },
        page_url: href //set page url
        
    });
    
    //initialise tabs
    $( "#tabs" ).tabs(); //create tabs
    
    
    function open_wines(){
        //options for main wines page control
        obj_page.leave_page({
            dst_url: "/index.php",
            dst_action: 'open',
            page_action: 'leave'
        });
    }
   
   
    function add_wine(){
         //options for add_wine page control
        obj_page.leave_page({
            dst_url:        "/wine/wine.php",
            rtn_url:        href,
            page_action:    'leave',
            dst_type:       "wine",
            object_id:      null,
            dst_action:     "add"
        });
    };
    
    
    function add_acquisition(){
        //options for add acquisition page control
        obj_page.leave_page({
            dst_url:        "/acquire/acquisition.php",
            rtn_url:        href,
            page_action:    'leave',
            dst_type:       "acquisition",
            object_id:      0,
            dst_action:     "add"
        });

    };
    
    
    function open_reference_data(){
        //options for open reference data page control
        obj_page.leave_page({
            dst_url: "/admin/index_admin.php",
            rtn_url: href,
            dst_action: 'open',
            page_action: 'leave'
        });
    }


    function menu_select(selected_object){
         //handle main menu selection
         //main_menu click event is handled in basket/common.js

         var selected_item = selected_object['selected_item'];
         var menu_id = selected_object['menu_id'];
         var origin_id = selected_object['origin_id'];

         console.log('Menu_Select Function. Item: '+selected_item + ' Menu: '+menu_id + ' origin_id: '+origin_id);

         switch(menu_id){
             case 'main_menu':
                 switch(selected_item){
                     case 'New Wine':
                         //add_wine();
                         console.log('selected add_wine function');
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
     

    

$(document).ready(function(){


    
    
    $('#process_indicator') //show process indicator during activity
        .hide()  // hide it initially
        .ajaxStart(function() {
            $(this).show();
        })
        .ajaxStop(function() {
            $(this).hide();
    });
    
    
    function save_page(save_db){
        //function to save page
        //save_db (bool) - true will save to session and db, false to session only

        console.log('save_page function called save_db parameter: '+save_db);
        //var def = $.Deferred();
        //validate page before save 
        //validate_page().done(function(response){
            //console.log('Page validation successful. response = '+response);
            //save page to db here
         
            //def.resolve(true);
           
            //def.reject(false);
       
        //}).fail(function(response){
        //    console.log('Page validation failed. response = '+response);
        //});
            
        //return def.promise();
        
    }; //save_page
    
    
    function logout_user(){
        //logout current authenticated user
        
        $.post("/user/rpc_login.php", {
            action: 'rpc_logout'
        }, function(data){
            if(data.success){
                //logout successful
                alert('logout successful');
            } else {
                console.log('error loging out='+data.error);
            }

        }, "json");
    }
    
    
    function change_password(){
        //change password
        
        console.log('function: change_password');
           
        var dataPromise = serverChangePasswordRequest(); //call main function
        
        dataPromise.done(function(data){
            console.log('serverChangePasswordRequest promise done');
            
            $("#password").val('');
            $("#passwordNew").val('');
            $("#passwordNewConfirm").val('');
            
            window.onbeforeunload = null;
            
            $(".con_button_bar").notify('Password changed successfully',{
              position: "top left",
              style: "msg",
              className: "success",
              arrowShow: false
              }
            );
            setTimeout(function(){location.reload();},1000);
        });
        
        dataPromise.fail(function(data){
            console.log('serverChangePasswordRequest promise rejected');
        });
        
    }
    
    
    function serverChangePasswordRequest(){
        //serverside request to change password
        
        var deferred =$.Deferred();
        
        var password = $("#password").val();
        var passwordNew = $("#passwordNew").val();
        var passwordNewConfirm = $("#passwordNewConfirm").val();
        
        $.post("/user/rpc_user.php", {
            action: 'changePassword',
            password: password,
            passwordNew: passwordNew,
            passwordNewConfirm: passwordNewConfirm
        }, function(data){
            if(data.success){
                console.log('password change successful');
                deferred.resolve();
            } else {
                console.log('password change failed with error='+data.error);
                //display success message
                $(".con_button_bar").notify(data.error,{
                    position: "top left",
                    style: "msg",
                    className: "error",
                    arrowShow: false
                    }
                );
                deferred.reject(data.error);
            }
        }, "json");
        
        return deferred.promise();
        
    }
    
    
    function reloadPage(){
        //reload page
        
        var deferred =$.Deferred();
        
        
        return deferred.promise();
        
    }
    
    
    $(document).on('click',".btn_save",function(){
        //button class to save page
        console.log('btn_save...');
        save_page(true); //call save_page function
    });


    $(document).on('click','.btn_close',function(){
        //button class to close page
        obj_page.close_page();
    });
    
    
    $(document).on('click','.btn_logout',function(){
        //button class to logout
        logout_user();
    });
    
    
    $(document).on('click','#btn_change_password',function(){
        change_password();
    });
       
 
});


</script>
</body>
</html>