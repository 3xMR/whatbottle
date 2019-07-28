<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
//require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";

?>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<link href="/css/reset.css" rel="stylesheet" type="text/css"/>
<link type="text/css" href="/css/whatbottle_rd.css" rel="stylesheet" />

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

    $username = '';
    $password = '';
    $remember = '';
    $blnLoginSuccess = false;


?>


<div style="margin-top:50px;" class="login">
   <h1 style="margin-top:0px;">login</h1>
   

   <?php
        //validate login or logout early to allow for correct presentation in header and submit cookies before html
    
   if(!empty($_POST['btn_submit'])){
    //if ($_POST['btn_submit']=='Login'){
       

       
        if(empty($_POST['username'])){
            echo "Missing parameter 'username'<br/>";
        }else{
            $username = $_POST['username'];
        }
        
        if(empty($_POST['password'])){
            echo "Missing parameter 'password'</br>";
        }else{
            $password = $_POST['password'];
        }
        
        if(!empty($_POST['remember'])){
            $remember = $_POST['remember'];
        }

         $result= user_login($_POST['username'], $_POST['password'], $remember);

         if ($result){
             //registration successful
             $blnLoginSuccess = true;
         } else {
             $blnLoginSuccess = false;
             $error_login = "<p class=\"error\">Login details are incorrect</p>";
         }
     }

     //process logout request
     if(!empty($_REQUEST['logout'])){
        if($_REQUEST['logout']=='yes'){
            //logout current authed user
            user_logout();
            //redirect
            $redirect = "\"/index.php\"";
            echo "<script> window.location=".$redirect."</script>";

        }
     }
   
    if(!empty($error_login)){
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
        <input type="button" name="forgottenPassword" id="forgottenPassword" value="test email">
        <?php
            if(is_authed()){
               echo "&nbsp;<h4 class=\"inline\"><a href=\"/user/login.php?logout=yes\" >logout</a></h4>";
            }
        ?>
    </form>
</div>

    
<div class="login" style="margin-top:25px;">
    <h1 style="margin-top:0px;">Register</h1>
    <?php
        require_once("$root/classes/User.php");
        
        if (isset($_POST['btn_checkName'])){
            //check if username is taken
            echo "check if username exists...</br>";
            
            $username = $_POST['username'];
            if(!$username){
                echo "Missing parameter 'username'";
            }else{
                checkUserExists($username);
            } 
            
        }
        
        
        function checkUserExists($username){
             $user = new UserObj();
             $userExists = $user->userExists($username);
             if($userExists === true){
                 echo "User exists</br>";
                 return true;
             }else if($userExists === false){
                 echo "User does not exist</br>";
                 return false;
             }else{
                 echo $userExists; //display error message
                 return $userExists; //return error message
             }
         }
         
         
        
        
        //validate and post registration request
        if (isset($_POST['btn_submit'])){
            //form successfully submitted - register user with Post variables
            $result = 0;
            
            if (!empty($_POST['username']) &&
                !empty($_POST['password']) &&
                !empty($_POST['firstname']) &&
                !empty($_POST['lastname'])){
                //submit registration
                
                //confirm passwords match
                
                
                if(checkUserExists($username)===false){ //check user doesnt already exist
                    //register user
                    $user = new UserObj();
                    $result = $user -> userRegister($_POST['username'], $_POST['password'], $_POST['firstname'], $_POST['lastname']);
                    if($result > 0){
                        echo "Welcome to whatbottle your user id is $result";
                    }else{
                        echo "There was an error: $result";
                    }
                }
                
                //$result = user_register($_POST['username'], $_POST['password'], $_POST['firstname'], $_POST['lastname']);

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
    
        if(!empty($error_register)){
            echo $error_register;
        }
        
        //if ($blnLoginSuccess==true){
        //    //Redirect to home page
        //    $redirect = "\"/index.php\"";
        //    echo "<script> window.location=".$redirect."</script>";
        //}
    ?>
    
    
    <form action="/user/login.php" method="post" id="frm_register" name="frm_register" autocomplete="off">

        <h3>email*:</h3>
        <input type="email" autocomplete="off" size="28" value="" name="username" id="username" />

        <h3>Password*:</h3>
        <input type="password" size="28" name="password" id="password" />

        <h3>Confirm Password*:</h3>
        <input type="password" size="28" name="password_confirm" id="password_confirm" />

        <h3>First Name*:</h3>
        <input size="28" name="firstname" id="firstname" />

        <h3>Last Name*:</h3>
        <input size="28" name="lastname" id="lastname" />

        <br/>
        <input type="submit" name="btn_submit" value="Register" />
        <input type="submit" name="btn_checkName" value="Check Name" />

    </form>

</div>

</div>

</body>
</html>