<?php
/* 
 * user registration and logon functions
 */
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/classes/class.db.php");
require_once("$root/classes/class.user.php");


// Salt Generator
function generate_salt ()
{
     // Declare $salt
     $salt = '';

     // And create it with random chars
     for ($i = 0; $i < 3; $i++)
     {
          $salt .= chr(rand(35, 126));
     }

     return $salt;
}


//token generator
function generate_token($salt)
{
     // Declare $salt
     $token= '';

     // And create it with random chars
     for ($i = 0; $i < 10; $i++)
     {
          $token = chr(rand(35, 126));
     }

	 $encrypted_token = md5(md5($token).$salt);

	 return $encrypted_token;
}

function user_exists($username)
{
    // Check if username already exists
    $query = "Select user_id
    FROM tblUser
    WHERE username = '$username'";
    mysql_query ($query) or die ('Error checking user');

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows==1){
        return $row['user_id'];
    }else{
        return False;
    }
}

function user_register($username, $password, $firstname, $lastname)
{
     // Get a salt using our function
     $salt = generate_salt();

     // Now encrypt the password using that salt
     $encrypted = md5(md5($password).$salt);

     // And lastly, store the information in the database
     $query = "INSERT INTO tblUser (username, password, salt, firstname, lastname, created) values ('$username', '$encrypted', '$salt', '$firstname', '$lastname', NOW())";
     mysql_query ($query) or die ('Could not create user.');
     $user_id = mysql_insert_id();

     return $user_id;
     //logWrite("New user registration: $user_id",1);
}


function user_login($username, $password, $remember=false){
     //log user in

    $query = "select salt from tblUser where username='$username' limit 1";
    $result = mysql_query($query);
    $user = mysql_fetch_array($result);

    // Using the salt, encrypt the given password to see if it
    // matches the one in the database
    $encrypted_pass = md5(md5($password).$user['salt']);

    // Try and get the user using the username & encrypted pass
    $query = "select user_id, username, firstname, lastname from tblUser where username='$username' and password='$encrypted_pass'";
    $result = mysql_query($query);
    $user = mysql_fetch_array($result);
    $numrows = mysql_num_rows($result);
    $userid = ($user['user_id']);

    // Now encrypt the data to be stored in the session
    $encrypted_id = md5($user['user_id']);
    $encrypted_name = md5($user['username']);

    // Store the data in the session
    $_SESSION['user_id'] = $userid;
    $_SESSION['username'] = $username;
    $_SESSION['encrypted_id'] = $encrypted_id;
    $_SESSION['encrypted_name'] = $encrypted_name;
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'];
    //$_SESSION['encrypted_pass'] = $encrypted_pass;

    if($remember){
        // If remember me check box is used then store logon credentials in cookies to allow seamless logon
        setcookie("cookie_username", $_SESSION['username'], time()+60*60*24*100, "/");
        setcookie("cookie_encrypted_username", $_SESSION['encrypted_name'], time()+60*60*24*100, "/");
        setcookie("cookie_user_id", $_SESSION['user_id'], time()+60*60*24*100, "/");
    }

    if ($numrows == 1)
    {
        return true;
    }
    else
    {
        return false;
    }

}


function user_logout(){
    // Log the user out, end the session and unset all variables
    
    if( is_authed() )
    {
        
        //reset session array
        $_SESSION = array();
        
        // Note: This will destroy the session, and not just the session data!
        if(ini_get("session.use_cookies"))
        {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        //Remove login credentials fromm cookies
        //cookies cannot be set if any output to screen occurs before the action
        setcookie("cookie_username", "", time()-60*60*24*100, "/");
        setcookie("cookie_encrypted_username", "", time()-60*60*24*100, "/");
        setcookie("cookie_user_id", "", time()-60*60*24*100, "/");
        
    }
    
    return true;

}

function is_authed()
{
    //If login has been remembered and cookies set, retrieve values
    
    //echo "<br/>is_authed";

    if (isset($_COOKIE['cookie_username']) && isset($_COOKIE['cookie_encrypted_username']))
	 {
            $_SESSION['username'] = $_COOKIE['cookie_username'];
            $_SESSION['encrypted_name'] = $_COOKIE['cookie_encrypted_username'];
            $_SESSION['user_id'] = $_COOKIE['cookie_user_id'];
            
            //get first and last names from db
            $user_id = $_SESSION['user_id'];
            if($user_id){
                $user_obj = new user();
                $result = $user_obj -> get("user_id=$user_id");
                $_SESSION['firstname'] = $result[0]['firstname'];
                $_SESSION['lastname'] = $result[0]['lastname'];
            }
	 }
  
    // Check if the encrypted username is the same as the unencrypted one, if it is, it hasn't been changed
    if (isset($_SESSION['username']) && (md5($_SESSION['username']) == $_SESSION['encrypted_name'])){
        return true;
    } else {
        return false;
    }

}

function reset_password($user_id, $password)
{
	 // Get the salt from the database using the username

    $query = "select salt, username from tblUser where user_id='$user_id' limit 1";
    $result = mysql_query($query);
    $user = mysql_fetch_array($result);

    $encrypted_pass = md5(md5($password).$user['salt']);

    // And lastly, store the information in the database
    $query = "UPDATE tblUser SET password='$encrypted_pass' WHERE user_id='$user_id'";
    mysql_query ($query) or die ('Could not create user.');

	//login user
	$username = $user['username'];
	$result = user_login($username, $password);

}
?>