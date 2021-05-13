<?php
//start php session
if(!isset($_SESSION)){
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

//legacy mysql db connection
$root = $_SERVER['DOCUMENT_ROOT'];
$new_root = rtrim($root, '/\\');
$real_root = rtrim($root, '/\\');

//handle different environments
if($root=="/Volumes/sites/whatbottle/03 whatbottle.local/www/" ||
        $root=="/Volumes/sites/whatbottle/whatbottle.local/www/" ||
        $root=="/Users/magnus/Documents/Sites/whatbottle.local/www/" ||
        $root=="/Volumes/SanDisk/01 whatbottle.dev/www/"){
    //use local sql database
    //$link = mysql_connect("localhost",  "root", "root") or die(mysql_error());
    //mysql_set_charset('utf8',$link);
    //mysql_select_db("whatbottle") or die(mysql_error());
    $environment = 'dev';
    
} else if($root=="/Volumes/sites/whatbottle/01 whatbottle.test/www/" || 
        $root=="/Users/magnus/Documents/Sites/whatbottle.test/www/" ){
    //development environment
     //use local dev sql database
    //$link = mysql_connect("localhost",  "root", "root") or die(mysql_error());
    //mysql_set_charset('utf8',$link);
    //mysql_select_db("whatbottle_dev") or die(mysql_error());
    $environment = "dev";
} else {
    //public details are held in config file in config folder
    //include_once('/home2/magnus/config/whatbottleConfig.php');
    //$link = mysql_connect($DB_HOST, $DB_USER, $DB_PASS) or die(mysql_error());
    //mysql_set_charset($DB_CHAR,$link);
    //mysql_select_db($DB_NAME) or die(mysql_error());
    $environment = 'live';
}


// Seed the random number generator
srand();

//Globals
$label_path = "/images/labels/";
$label_upload_path ="/images/labels/uploads/";
$log_path = "/logs/";


//set timezone
date_default_timezone_set('Europe/London');
?>