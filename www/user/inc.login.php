<?php
/* 
 * login functions
 * 
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");
?>



<div id="login_container">
    <div id="login_panel">
        <div id="login_panel_content" >
            
            
        </div>
        <div class="clear" ></div>
    </div>

    <div class="login_button rounded_small_bottom" id="btn_hide_login">
        <?php
            if(is_authed() ){
                echo"<span>Logout</span>";
            } else {
                echo"<span>Login</span>";
            }
        ?>
    </div>
</div>
