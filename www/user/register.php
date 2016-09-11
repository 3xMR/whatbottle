<?php
/* 
 * new user registration page
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

echo "<!DOCTYPE html>";
echo "<html>";
    echo "<head>";
    
    require_once("$root/includes/standard_html_head.inc.php");
    echo "<title>What Bottle?</title>";
    require_once("$root/includes/css.inc.php");//include style sheets 
    require_once("$root/includes/script_libraries.inc.php");//include all script libraries
    
?>
<script type="text/javascript">


$(document).ready(function(){

    //Configure Validation Rules

    $("#frm_register").validate({
        rules: {
            username: { required: true,
                        email:true},
            password: {required: true},
            password_confirm: { required: true,
                                equalTo: "#password"},
            firstname: {required: true},
            lastname: {required: true}
        },
        messages: {
            
        }
    });

});




function msg(msg){
    alert(msg);
}


function register_user(){

}

</script>



</head>

<body>

<?php

echo "<div class=\"page_container\">";

    require_once("$root/includes/nav/topheader.php"); //header
    
    echo "<div class=\"con_single_form rounded\" >";
        
        //Title bar
        echo "<div class=\"con_title_bar\" >";
            //wine name
            echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px;\" >";
                echo "<div style=\"float:left; width:58px;\" >";
                    echo "<img src=\"/images/\" height=\"48px\" width=\"48px\" >";
                echo "</div>";
                echo "<div style=\"width:auto; float:left; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >Register</h1>";
                echo "</div>";
                echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left; height:50px;\"  >";
                    //echo "<img id=\"process_indicator\" src=\"/images/ajax-loader.gif\" height=\"24px\" width=\"24px\" />";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
            echo "</div>";
        echo "</div>"; //con_title_bar
        
    if ($_POST['btn_submit']=='Register'){
        //form successfully submitted - register user with Post variables

        $result = user_register($_POST['username'], $_POST['password'], $_POST['firstname'], $_POST['lastname']);

        if ($result>0){
            //registration successful
            echo "<h2>Sorry no registrations at this time</h2>";

            //end page here
            echo "</body>";
            echo "</html>";
            exit;
        }
    }


    echo "<form action=\"/user/register.php\" method=\"post\" id=\"frm_register\" name=\"frm_register\" autocomplete=\"off\">";
        

        echo "<div class=\"vertical-centre input-main-label float-left clear-left bottom-spacer\" >";
            echo "<p>Email</p>";
        echo "</div>";
        echo "<div class=\"input-main vertical-centre float-left clear-left bottom-spacer\" >";
            echo "<input type=\"text\" value=\"\" name=\"username\" id=\"username\"/>";
        echo "</div>";
        
        
        echo "<div class=\"vertical-centre input-main-label float-left clear-left bottom-spacer\" >";
            echo "<p>Password</p>";
        echo "</div>";
        echo "<div class=\"input-main vertical-centre float-left clear-left bottom-spacer\" >";
            echo "<input type=\"text\" value=\"\" name=\"password\" id=\"password\" />";
        echo "</div>";
        
        echo "<div class=\"vertical-centre input-main-label float-left clear-left bottom-spacer\" >";
            echo "<p>Confirm Password</p>";
        echo "</div>";
        echo "<div class=\"input-main vertical-centre float-left clear-left bottom-spacer\" >";
            echo "<input type=\"text\" value=\"\" name=\"password_confirm\" id=\"password_confirm\" />";
        echo "</div>";
        
        echo "<div class=\"vertical-centre input-main-label float-left clear-left bottom-spacer\" >";
            echo "<p>First Name</p>";
        echo "</div>";
        echo "<div class=\"input-main vertical-centre float-left clear-left bottom-spacer\" >";
            echo "<input type=\"text\" value=\"\" name=\"firstname\" id=\"firstname\" />";
        echo "</div>";
        
        echo "<div class=\"vertical-centre input-main-label float-left clear-left bottom-spacer\" >";
            echo "<p>Last Name</p>";
        echo "</div>";
        echo "<div class=\"input-main vertical-centre float-left clear-left bottom-spacer\" >";
            echo "<input type=\"text\" value=\"\" name=\"lastname\" id=\"lastname\" />";
        echo "</div>";
        
       
   
        //Button Bar
        echo "<div class=\"con_button_bar\" >";
            if( is_authed() ){
                echo "<input type=\"submit\" id=\"btn_submit\" value=\"Register\" />";

            }  
        echo "</div>";

      
    echo "</form>";
  
    echo "</div>"; //con_single_form
    
echo "</div>"; //page_container
?>
    

</body>
</html>