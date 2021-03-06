/*  Common library to control basket and login javascript elements and function
 *  Version 2.0 Sept 2015
 *      Modified to support new flat ui design and new login and basket dialogs
 */

//*** basket functions ***

//initialise basket when script is loaded
initialise_basket();

function initialise_basket(){
   console.log('initialise basket');
   load_basket_html();
   $('#basket_panel').hide();
   
   console.log('initialise login');
   load_login_html();
   $('#login_panel').hide();
   
}

function load_basket_html(){
    //load basket html from remote script
    console.log('load basket_panel_content');
    $('#basket_panel_content').load('/acquire/rpc_basket_html.php',function(){
        //update basket count notification after html loaded
        var basket_count = $("#basket_count").val();
        update_basket_count(basket_count);
    });
    //$('.basket_panel_content').load('/acquire/rpc_basket_html.php');
    
}


function add_vintage_basket(vintage_id){
    //add vintage to basket

    console.log('add_vintage_basket = vintage_id: '+vintage_id);
    
    $.post("/vintage/rpc_basket.php", {
            vintage_id: vintage_id,
            action: 'add'
        }, function(data){
            
            if(data.success){
               console.log('add_vintage_basket successful. server msg: '+data.msg);
               console.log('basket count: '+data.basket_count);
               var basket_count = data.basket_count;
               update_basket_count(basket_count);
            } else {
                console.log('add_vintage_basket failed. server error: '+data.error);
            }

            load_basket_html(); //re-load html
        
    }, "json");
}


function update_basket_count(count){
    //update basket count
    if(count > 0){
        $('#noti_bubble_count').text(count);
        $('#noti_bubble').show();
    }else{
        $('#noti_bubble_count').text('0');
        $('#noti_bubble').hide();
    }
}


function remove_vintage_basket(var_vintage){
    //add or remove vintage from basket
    
    console.log(var_vintage);
    
    $.post("/vintage/rpc_basket.php", {
            vintage_array: var_vintage,
            action: 'remove'
        }, function(data){
            if(data.success){
                    console.log('remove_vintage_basket successful. server msg: ' + data.msg);
                    load_basket_html();
                    var basket_count = data.basket_count;
                    update_basket_count(basket_count);
            }else{
                console.log('remove_vintage_basket failed. server msg: ' + data.error);
            }
    }, "json");
    
}


$(document).on('click','.btn_basket',function(){
    load_basket_html();
    show_dialog_basket(); //function in jquery.basket.js
});


//check or uncheck all vintages
$(document).on('change','#basket_check_all',function(){
   
    if(this.checked){
        $(".basket_vintage_check").prop('checked', true); 
    } else {
       $(".basket_vintage_check").prop('checked', false);  
    }
    
});

//remove checked vintages
$(document).on('click','.btn_basket_vintage_remove',function(){
    //create array of checked vintages
    
    var array = [];
    console.log('remove vintages...');
    $(".basket_vintage_check:checked").each(function(){
        var id_string = $(this).attr('id');
        var id = id_string.replace('basket_check=','');
        array.push(id);
    });
    
    var json_array = JSON.stringify(array);
    console.log(json_array);
    remove_vintage_basket(array); //pass array to server for removal
    
    
});


//hide basket panel
$(document).on('click','#btn_hide_basket', function(){
    load_basket_html();
    $('#basket_panel').slideToggle("medium");
});


//add to basket
$(document).on('click', '.btn_add_to_basket',function(e){
    var id = $(this).data('vintage_id');
    console.log('add to basket - vintage = '+id);
    add_vintage_basket(id);
    e.stopPropagation();
});



function show_dialog_basket(){
    //show basket dialog - dependent on jquery ui for dialog
    //requires CSS in whatbottle.com
    
    //determine screen size
    var windowWidth = $(window).width();
    console.log('login window width = '+windowWidth);
    if(windowWidth > 500){
        dialogWidth = 400;
        positionMy = "right top";
        positionAt = "left bottom";
        positionOf = "#btn_basket_top_nav";
    } else {
        dialogWidth = windowWidth;
        positionMy = "right top+15px";
        positionAt = "right bottom";
        positionOf = "#top_nav";
    }   

    $("#dialog-basket").dialog({
        modal: true,
        title: "Basket",
        width: dialogWidth,
        minHeight: 200,
        position: { 
            my: positionMy,
            at: positionAt, 
            of: positionOf 
        },
        buttons: {
            Close: {
                click: function() {
                    console.log('close dialog');
                    $(this).dialog('close');
                },
                class: 'ok-button',
                text: 'Close'
            }
        },
        dialogClass: "basket-dialog"
    });
}




//*************login form************************


//login form
function initialise_login(){
   console.log('initialise login');
   load_login_html();
   $('#login_form_panel').hide();
}


function load_login_html(callback){
    //load login html from remote script
    console.log('load login');
    $('#login_panel_content').load('/user/rpc_login_html.php', function(){
        if(typeof callback == 'function' ){
            callback();
        }
    });
}

//toggle login panel
$(document).on('click','#btn_hide_login', function(){
    
    $('#login_panel').slideToggle("medium");
    if($('#login_panel').css('display')=='block'){
        //panel is being opened - load html and set focus
        load_login_html(function(){
            set_focus();
        });
    }  

});


function set_focus(){
    console.log('set focus');
    $("#username").focus();
}

//toggle login panel
$(document).on('click', '#btn_login',function(){
    console.log('btn_login');
    login();
});


$(document).on('click','.btn_login',function(){
    //determine whether to open settings or show login box
    var authed = $('#btn_login_top_nav').attr('authed_data');
    console.log('authed_data = '+authed);
    if(authed){
        //open settings
        //window.location = "/user/settings.php";
        show_dialog_login(); //function in jquery.basket.js
    }else{
        //show logon dialog
        show_dialog_login(); //function in jquery.basket.js
    }
});


$(document).on('click','.btn_logout',function(){
    console.log('click logout');
    logout(); //function in jquery.basket.js
});



function login(){
    //login user
    
    console.log('function_login - jquery.basket.js');
    var def = $.Deferred();

    var username = $("#login_username").val().toLowerCase().trim();
    var password = $("#login_password").val().trim();
    var remember = $("#remember").is(":checked");
    console.log("username="+username+" password="+password+"remember="+remember);

    def.done(function(response){
        //check response after submisson to server and animations completed
        if(response.success){
            location.reload(); //reload page
        }  
    });

    $.post("/user/rpc_user.php", {
        action: 'login',
        username: username,
        password: password,
        remember: remember
    }, function(data){
        if(data.success){
            console.log('post login successful');
            $("#login_msg").hide('medium').delay(200)
                .queue(function(next){
                    $("#login_msg")
                    .text("Success!")
                    .css('background-color','#57B447')
                    .show('medium', function(){
                        //resolve def at end of animation
                        var response = {
                            success: true
                        };
                        def.resolve(response);
                    });
                    next();
                });


        } else {
            console.log('login failed error='+data.error);
            console.log('failed username='+data.username);
            $("#login_msg").hide('fast')
                .text("Incorrect username or password")
                .css('background-color','red')
                .show('fast',function(){
                    var response = {
                        success: false
                    };
                    def.resolve(response);
                });
        }

    }, "json"); 

    return def.promise();

}


$("#dialog-login").keydown(function (event) {
    //make default enter key behaviour - login button
    if (event.keyCode == 13) {
        $(this).parent()
               .find("button:eq(1)").trigger("click");
        return false;
    }
});


function logout(){
    //allow user to logout
    
    $.post("/user/rpc_user.php", {
        action: 'logout'
    }, function(data){
        if(data.success){
           console.log('logout successful');
           //$("#dialog-login").dialog("close"); //close dialog
           location.reload(); //reload page
           
        } else {
            console.log('logout failed error='+data.error);
            $("#login_msg").hide('fast')
                .text("Logout failed")
                .css('background-color','red')
                .show('fast',function(){
                    var response = {
                        success: false
                    };
                    //def.resolve(response);
                });
        }
    }, "json"); 
    

}


function show_dialog_login(){
    //show login dialog - dependent on jquery ui for dialog
    //requires CSS in whatbottle.com
    
    //determine screen size
    var windowWidth = $(window).width();
    console.log('login window width = '+windowWidth);
    if(windowWidth > 500){
        dialogWidth = 400;
        positionMy = "right top";
        positionAt = "left bottom";
        positionOf = "#btn_login_top_nav"
    } else {
        dialogWidth = windowWidth;
        positionMy = "right top+10px";
        positionAt = "right bottom";
        positionOf = "#top_nav";
    }   
    

    $("#dialog-login").dialog({
        modal: true,
        title: "Login",
        width: dialogWidth,
        minHeight: 200,
        close: reset_login_dialog,
        position: { 
            my: positionMy,
            at: positionAt, 
            of: positionOf 
        },
        buttons: {
            Login: {
                click: function() {
                    //if( login() ){
                    //    console.log('dialog - login successful');
                    //    //$("#login_msg").hide().show().text('Login successful').css("background-color", "#57B447");
                    //}else{
                    //    console.log('dialog - login failed');
                    //    //$("#login_validation_msg").hide().show().text('Login failed').css("background-color", "red");
                    
                    //}
                    login();
                },
                class: 'login-button',
                text: 'Login'
            },
            Close: function() {
                $(this).dialog('close');
            }
        },
        dialogClass: "login-dialog"
    });
}
            
            
function reset_login_dialog(){
    //reset login dialog - clear all fields
    $("#username").val("");
    $("#password").val("");
    $("#login_msg").hide();
}


//**** Main Menu ****


//main menu pop-up
var main_menu = $(".main_menu").menu({
   items: "> :not(.ui-menu-header)",
   select: function( event, ui ) {
        menu_select({ //pass selcted object to menu function
            selected_item: ui.item[0].textContent,
            menu_id: $(this).attr('id')
        });
    }
}).hide();


$(document).on('click','.btn_main_menu',function(e) {
    // Make use of the general purpose show and position operations
    // open and place the menu where we want.
    console.log('btn_main_menu clicked');

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


