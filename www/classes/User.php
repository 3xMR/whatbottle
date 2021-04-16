<?php
/* 
 * Class User to manage all user functions
 * 
 * Public functions
 * 
 */



require_once("$root/classes/MyPDO.php"); //include PDO db class

class UserObj{
    
    public $lastErrorMessage = ''; //used to store last errorMessage for external recall
    protected $db; //db connection object
    private $userID, $userName, $firstName, $lastName, $salt, $password;
            
    
    function __construct() {
        //constructor function
        
        $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
    }
   
    
    protected function generateSalt() {
        //Salt Generator
        
        $salt = '';

        //create it with random chars
        for ($i = 0; $i < 3; $i++)
        {
             $salt .= chr(rand(35, 126));
        }

        return $salt;
    }
    
    
    protected function getSalt($username){
        //return salt for username
        
        if(empty($username) && empty($userID)){
            $this->lastErrorMessage = "No username or userID provided";
            return false;
        }
        
        //get salt from db
        $stmt = $this->db->prepare('SELECT * FROM tblUser WHERE username = ?');
        $stmt->execute([$username]);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($rst){
            return $rst['salt'];
        }else{
            $this->lastErrorMessage = "No record returned for user";
            return false;
        }
        
    }
    

    protected function generateToken($salt){
        //generate random token
  
         $token = '';
         
         if(!$salt){
             $this->lastErrorMessage = "Missing parameter 'salt' in call to generateToken function";
             return false;
         }

         // And create it with random chars
         for ($i = 0; $i < 10; $i++)
         {
              $token = chr(rand(35, 126));
         }

        return md5(md5($token).$salt);

    }
    
    
    protected function authenticateUser($username,$password){
        //authenticate user based on username and password
        $this->lastErrorMessage = "";
        
        if($this->userExists($username)==false){
            //error message is provided by userExists function
            return false;
        }
        
        if(empty($password)){
            $this->lastErrorMessage = "Password not provided";
            return false;
        }
        
        
        //get record for username
        $stmt = $this->db->prepare('SELECT * FROM tblUser WHERE username=?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$user){
            $this->lastErrorMessage = "No record returned for user";
            return false;
        }
        
        $salt = $user['salt'];
        $encrypted = md5(md5($password).$salt);
        if($encrypted == $user['password']){
            //authentication successful
            return true;
        }else{
            $this->lastErrorMessage = "password incorrect";
            return false;
        }
        
    }
    
    
    protected function getUser($userID){
        //retrieve user record from db using userID
        
        if(empty($userID)){
            $this->lastErrorMessage = "Missing UserID parameter";
            return false;
        }
        
        $stmt = $this->db->prepare('SELECT * FROM tblUser WHERE user_id = ?');
        $stmt->execute([$userID]);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(empty($rst)){
            $this->lastErrorMessage = "No record returned for UserID";
            return false;
        }
        
        $this->userID = $rst['user_id'];
        $this->userName = $rst['username'];
        $this->firstName = $rst['firstname'];
        $this->lastName = $rst['lastname'];
        $this->password = $rst['password'];
        $this->salt = $rst['salt'];
        
        return true;
        
    }
    
    
    public function userExists($username){
        // Check if username exists in db
        
        if(!$username){
            $this->lastErrorMessage = "username parameter missing";
            return false;
        }
        
        
        //query db
        $stmt = $this->db->prepare('SELECT * FROM tblUser WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if($user){ return true; }
        
        return false;

    }
    
    
    function userRegister($username, $password, $firstname, $lastname){
     // register a new user
     // returns: true | error message description
     
        if(empty($username)){
            return "Missing parameter 'username' in class:User function:userRegister";
        }
        
        if(empty($password)){
            return "Missing parameter 'password' in class:User function:userRegister";
        }
        
        if(empty($firstname)){
            return "Missing parameter 'firstname' in class:User function:userRegister";
        }
        
        if(empty($lastname)){
            return "Missing parameter 'lastname' in class:User function:userRegister";
        }
        
        //check username does not already exist
        if($this->userExists($username)===true){
            return "username: $username is already in use";
        }
        
        //confirm passwords match
        
        
        //confirm valid email format
        
        
        //encrypt password
        $salt = $this->generateSalt();
        $encrypted = md5(md5($password).$salt);

        //add to db
        ///$query = "INSERT INTO tblUser (username, password, salt, firstname, lastname, created) values ('$username', '$encrypted', '$salt', '$firstname', '$lastname', NOW())";
        $stmt = $this->db->prepare('INSERT INTO tblUser (username, password, firstname, lastname, created) values (:username, :password, :firstname, :lastname, NOW())');
        $stmt->execute(['username' => $username, 'password' => $encrypted, 'firstname' => $firstname, 'lastname' => $lastname]);
        $lastId = $this->db->lastInsertId();
        
        return $lastId;
      
    }
    
    
    public function isAuthed(){
        //if login has been remembered and cookies set, retrieve values
    
         //get details from cookies
        if (isset($_COOKIE['cookie_username']) && isset($_COOKIE['cookie_encrypted_username'])){
            $_SESSION['username'] = $_COOKIE['cookie_username'];
            $_SESSION['encrypted_name'] = $_COOKIE['cookie_encrypted_username'];
            $_SESSION['user_id'] = $_COOKIE['cookie_user_id'];
        }

        // Check if the encrypted username is the same as the unencrypted one, if it is, it hasn't been changed
        if (isset($_SESSION['username']) && (md5($_SESSION['username']) == $_SESSION['encrypted_name'])){
            
            $userID = $_SESSION['user_id']; //get user_id from session
            
            //TODO if userID is not set in session then retrieve from db using username
            
            if(!$userID){
                $this->lastErrorMessage = "User ID missing";
                return false;
            }
            
            if($this->getUser($userID)==false){ //get user record into class object based on userID
                //$this->lastErrorMessage = "Failed to find User ID";
                return false;
            }

            $_SESSION['firstname'] = $this->firstName;
            $_SESSION['lastname'] = $this->lastName;
            
            return $userID; //authenticated
        } else {
            
            return false; //not authenticated
        }

    }
    
    
    public function getUserName(){
        //returns array with first name, last name and combined display name
        
        if($this->isAuthed()){
            $names = array(); //create empty array
            $names['firstName'] = $this->firstName;
            $names['lastName'] = $this->lastName;
            return $names;
        }else{
            //no authenticated user results to return
            return false;
        }
 
    }
    
    
    public function changePasswordAuthed($password, $passwordNew, $passwordNewConfirm){
        //changes password for authenticated user
        
        $this->lastErrorMessage = "";
        
        
        if($this->isAuthed()==false){
            //user not authenticated
            //$this->lastErrorMessage = "User not authenticated cannot change password";
            return false;
        }
        
        $userID = $this->userID; //userID set by isAuthed()
        
        if(empty($userID)){
            $this->lastErrorMessage = "User ID missing";
            return false;
        }
        
        if(empty($password)){
            $this->lastErrorMessage = "Password missing";
            return false;
        }
        
        if(empty($passwordNew)){
            $this->lastErrorMessage = "New Password missing";
            return false;
        }
        
                
        if(empty($passwordNewConfirm)){
            $this->lastErrorMessage = "Confirm New Password missing";
            return false;
        }
        
        if($passwordNew !== $passwordNewConfirm){
            $this->lastErrorMessage = "New password does not match confirmation";
            return false;
        }

        //query db to find user and get salt
        //$stmt = $this->db->prepare('SELECT * FROM tblUser WHERE user_id = ?');
        //$stmt->execute([$userID]);
       // $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($this->getUser($userID)==false){ 
            //$this->lastErrorMessage = "No record returned for user";
            return false;
        }
        
        $salt = $this->salt;
        $encrypted = md5(md5($passwordNew).$salt); //encrypt new password
        $username = $this->userName;
        
        //check user authentication
        $authenticated = $this->authenticateUser($username, $password); //using existing credentials
        if($authenticated == false){
            $this->lastErrorMessage = "There was a problem authenticating this username and password";
            return false;
        }

        
        //update record with new password
        $stmt = $this->db->prepare('UPDATE tblUser SET password=:password, modified=Now() WHERE user_id=:userID');
        $stmt->execute(['password' => $encrypted, 'userID' => $userID]);
        if($stmt->errorCode() == 0) {
            $this->userLogout();
            return true;
        } else {
            $this->lastErrorMessage = $stmt->errorInfo();
            return false;
        }
        
    }
    
    
    
    
    
    function userLogin($username, $password, $remember=false){
         //log user in
        $this->lastErrorMessage = "";

        if(empty($username)){
            $this->lastErrorMessage = "Username not provided";
            return false;
        }
        
        if(empty($password)){
            $this->lastErrorMessage = "Password not provided (userLogin $password)";
            return false;
        }
        
        //get salt to encrypt password
        $salt = $this->getSalt($username);
        
        if($salt == false){
            $this->lastErrorMessage = "failed to get salt for user";
            return false;
        }
        
        // Using the salt, encrypt the given password to see if it
        // matches the one in the database
        $encryptedPassword = md5(md5($password).$salt);
        $stmt = $this->db->prepare('SELECT * FROM tblUser WHERE username=:username AND password=:password');
        $stmt->execute(['password' => $encryptedPassword, 'username' => $username]);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(empty($rst)){
            //no records returned
            $this->lastErrorMessage = "username or password is incorrect ";
            return false;
        }
   
        if(count($rst)>9){ //should return 9 fields in array
            $this->lastErrorMessage = "more than one record found cannot continue";
            return false;
        }

        // Now encrypt the data to be stored in the session
        $encrypted_id = md5($rst['user_id']);
        $encrypted_name = md5($rst['username']);

        // Store the data in the session
        $_SESSION['user_id'] = $rst['user_id'];
        $_SESSION['username'] = $username;
        $_SESSION['encrypted_id'] = $encrypted_id;
        $_SESSION['encrypted_name'] = $encrypted_name;
        $_SESSION['firstname'] = $rst['firstname'];
        $_SESSION['lastname'] = $rst['lastname'];


        if($remember){
            // If remember me check box is used then store logon credentials in cookies to allow seamless logon
            setcookie("cookie_username", $_SESSION['username'], time()+60*60*24*100, "/");
            setcookie("cookie_encrypted_username", $_SESSION['encrypted_name'], time()+60*60*24*100, "/");
            setcookie("cookie_user_id", $_SESSION['user_id'], time()+60*60*24*100, "/");
        }

        return true;

    }
    
    
    public function userLogout(){
        // Log the user out, end the session and unset all variables
        
        if(!$this->isAuthed()){
            //user not authenticated - nothing to do
            //return true;
        }
        
        //reset session array
        $_SESSION = array();

        // Note: This will destroy the session, and not just the session data!
        if(ini_get("session.use_cookies")){
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
        
        return true;

    }

       

   
    
    
    
} //User class ends








function userRegister($username, $password, $firstname, $lastname)
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


function userLogin($username, $password, $remember=false){
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


function userLogout(){
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

function isAuthed()
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


function resetPassword($user_id, $password)
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