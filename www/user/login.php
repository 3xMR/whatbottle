<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");



//validate login or logout early to allow for correct presentation in header and submit cookies before html
if ($_POST['btn_submit']=='Login'){

    $result= user_login($_POST['username'], $_POST['password'], $_POST['remember']);

    if ($result){
        //registration successful
        $blnLoginSuccess = true;
    } else {
        $error_login = "<p class=\"error\">Login details are incorrect</p>";
    }
}

//process logout request
if($_REQUEST['logout']=='yes'){
    //logout current authed user
    user_logout();
    //redirect
    $redirect = "\"/index.php\"";
    echo "<script> window.location=".$redirect."</script>";

}


echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";

?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/styles/reset.css" rel="stylesheet" type="text/css"/>
<link href="/styles/whatbottle.css" rel="stylesheet" type="text/css">

<title>Login</title>

<script type="text/javascript" src="/libraries/jquery-1.4.2.js"></script>
<script type="text/javascript" src="/libraries/jquery.validate.js"></script>
<script type="text/javascript" src="/libraries/jquery.autocomplete.js"></script>
<script type="text/javascript">


$(document).ready(function(){

    //Configure Validation Rules

    $("#frm_login").validate({
        rules: {
            username: { required: true,
                        email:true},
            password: {required: true}
        },
        messages: {

        }
    });

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

    //set initial focus
    $("#username").focus();
    
});




function msg(msg){
    alert(msg);
}


function logout(){
    console.log("hello logout");


}

</script>

</head>
<body>
    
<?php

echo "<div class=\"page_container\">";
    
require_once("$root/includes/nav/topheader.php");




//validate and post registration request
if ($_POST['btn_submit']=='Register'){
    //form successfully submitted - register user with Post variables
    
    if (!empty($_POST['username']) &&
        !empty($_POST['password']) &&
        !empty($_POST['firstname']) &&
        !empty($_POST['lastname'])){
        //submit registration

        $result = user_register($_POST['username'], $_POST['password'], $_POST['firstname'], $_POST['lastname']);
        
    } else {
        echo "<p class=\"error\">One or more fields were missing, unable to complete registration</p>";
    }
    
    
    if ($result>0){
        //registration successful
        echo "<h2>Welcome to whatbottle, registration was successful</h2>";

        //end page here
        echo "</body>";
        echo "</html>";
        exit;
    }
}


?>

<div class="login">
   <h1 style="margin-top:0px;">login</h1>
   <?php if(!empty($error_login)){
        echo $error_login;
    }
    ?>
   <form action="/user/login.php" method="post" id="frm_login" name="frm_login" autocomplete="off">

        <h3>email*:</h3>
        <input type="text" size="28" value="" name="username" id="username" />

        <h3>Password*:</h3>
        <input type="password" size="28" name="password" id="password" />

        <br/>
       
        <input type="checkbox" name="remember" id="remember" />
        <h4 class="inline">remember me</h4>
        <br/>
        <input type="submit" name="btn_submit" value="Login" />
        <?php
            if(is_authed()){
               echo "&nbsp;<h4 class=\"inline\"><a href=\"/user/login.php?logout=yes\" >logout</a></h4>";
            }
        ?>
    </form>
</div>

<div class="login" style="border-left-style: solid; border-left-width: 1px; border-left-color: #999999;">
    <h1 style="margin-top:0px;">register</h1>
    <?php
        if(!empty($error_register)){
            echo $error_register;
        }
        if ($blnLoginSuccess){
            //Redirect to home page
            $redirect = "\"/index.php\"";
            echo "<script> window.location=".$redirect."</script>";
        }
    ?>
    <form action="/user/login.php" method="post" id="frm_register" name="frm_register" autocomplete="off">

        <h3>email*:</h3>
        <input type="text" size="28" value="" name="username" id="username" />

        <h3>Password*:</h3>
        <input type="text" size="28" name="password" id="password" />

        <h3>Confirm Password*:</h3>
        <input type="text" size="28" name="password_confirm" id="password_confirm" />

        <h3>First Name*:</h3>
        <input size="28" name="firstname" id="firstname" />

        <h3>Last Name*:</h3>
        <input size="28" name="lastname" id="lastname" />

        <br/>
        <input type="submit" name="btn_submit" value="Register" />

    </form>

</div>

</div>

</body>
</html>