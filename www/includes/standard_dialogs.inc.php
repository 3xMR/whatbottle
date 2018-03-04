<!- Standard dialog boxes for use across multiple pages 
    - main_menu
    - unsaved_changes
    - generic_error
    - leave_page
    - dialog-login
    - dialog-basket
    - dialog-delete
->


<div id="unsaved_changes" class="hidden" title="Unsaved Changes">
    
    <div style="float:left; display:inline-block; margin-top:5px; margin-bottom:10px;">
        <img src="/images/warning_flat_orange_32.png" width="32px" height="32px">
    </div>
    <div style="float:left; margin-left:15px; margin-bottom:10px; margin-top:5px; line-height:150%;">
        <h2>You have unsaved changes!</h2>
    </div>
    <div style="clear:both; float:left;">
        <p style="color:grey;"><b>Save</b> to save changes and leave page<br/>  
        <b>Leave</b> to leave page and lose changes<br/>
        <b>Cancel</b> to return to page<br/></p>
    </div>
    
</div>


<div id="generic_error" class="hidden" title="Error">
    <p>
            <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
            Sorry, an error has occurred?</p>
    
    <div id="error_dialog_text">

    </div>
   

</div>


<div id="leave_page" class="hidden" title="Unsaved Changes">
    
    <div style="float:left; display:inline-block; margin-top:5px; margin-bottom:10px;">
        <img src="/images/warning_flat_orange_32.png" width="32px" height="32px">
    </div>
    <div style="float:left; margin-left:15px; margin-bottom:10px; width:320px; line-height:150%;">
        <h2>You have unsaved changes!</h2>
    </div>
    <div style="clear:both; float:left;">
        <p style="color:grey;"> 
            <b>Continue</b> and lose changes<br/>
            <b>Save</b> to keep changes<br/>
        </p>
    </div>
    
</div>



<div id="dialog-login" class="hidden dialog-login" title="Login">

    <div style="margin-top:px; margin-left:px; margin-right:px;">
        <h2>Login</h2>
        <div style="margin-top:25px;">
            <!--<h4 style="margin-left:0px;"> Username</h4>-->
            <div class="input-main" style="margin-top:10px;"  >
                <input name="username" type="email" class="input_text _ignore_dirty"  style="width:100%;" value="" placeholder="email" id="username" autocapitalize="off">
            </div>
        </div>
        <div style="margin-top:25px; margin-bottom:10px;"> 
            <!--<h4 style="margin-left:5px;">Password</h4>-->
            <div class="input-main" style="margin-top:10px;">
                <input type="password" name="password" autocapitalize="off" class="input_text _ignore_dirty"  style="width:100%;" value="" placeholder="password" id="password" >
            </div>
        </div>

        <div style="margin-top:10px; margin-bottom:10px;">
            <div style="float:left; margin-top:0px; width:100%; height:25px; background-color:white; ">
                <div style="float:left; line-height:20px;  background-color:white;">
                    <input style="color:gray;" type="checkbox" name="remember_me"  value="remember_me" id="remember_me" >
                    <label style="color:gray; " for="remember_me">Stay logged in</label>

                </div>
                <div style="float:right; line-height:20px; background-color:white;">
                    <label class="click btn_logout" style="color:gray;">Logout</label>
                </div>
            </div>
        </div>

        <div id="con_login_msg" style="margin-top:10px; margin-bottom:5px; clear:both;" >
            <h3  id="login_msg" style="display:none; background-color:#57B447; color:white; width:100%; padding:10px 5px 10px 10px; box-sizing:border-box; vertical-align: middle;" >Validation message</h3>
        </div>
    </div>
</div>


<div id="dialog-basket" class="hidden" title="Basket">

    <div style="margin-top:15px; margin-left:10px; margin-right:10px;">
        <h1>Basket</h1>
        <div id="basket_panel_content" style="margin-top:25px;">
            <!--Basket Contents from xxx-->
        </div>

    </div>

</div>


<div id="dialog-delete" class="hidden" title="Confirm Delete">

    <div style="float:left; width:50px; display:inline-block; margin-top:5px; margin-bottom:10px;">
        <img src="/images/warning_flat_orange_32.png" width="32px" height="32px">
    </div>
    
    <div style="overflow:hidden; width:auto; margin-left:15px; margin-bottom:10px; margin-top:5px; line-height:150%;">
        <h2 id="dialog_confirm_delete_text">dialog text here id=dialog_text</h2>
    </div>

    <div style="float:left; clear:both;">
        <p style="color:grey;">You will not be able to undo this change.</p>
    </div>
    
</div>