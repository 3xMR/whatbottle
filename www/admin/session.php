<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/includes/init.inc.php');
require_once(__ROOT__.'/functions/function.php');
require_once(__ROOT__.'/includes/script_libraries.inc.php'); //include all script libraries
?>

<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/css/whatbottle_rd.css" rel="stylesheet" type="text/css">
<title>Session Manager</title>

<script type="text/javascript" src="/libraries/jquery.page_control_1.4.js"></script>
<script type="text/javascript">

$(document).ready(function(){

    function unset_session(session_name){
        //unset session
        console.log('function: unset_session');
        console.log('session name = '+session_name);
        $.post("/vintage/rpc_vintage.php", {
            action: 'unset_session',
            session_name: session_name
        }, function(data){
            if(data.success){
                console.log('unset_session SUCCESS');
                location.reload();
            }else{
                console.log('unset_session FAILED: '+data.error);
            }
        }, "json");
    }

    $('.button').click(function(){
        var session_name;
        session_name = $(this).attr('id');
        console.log('session_name='+session_name);
        unset_session(session_name);
    })
    
});

</script>
</head>
<body>

<div class="page_container">

<?php
    
    echo "<h1>Manage Session:</h1>";
    echo "<hr/>";

    $var_sessions = array(  "var_basket",
                            "wine_form",
                            "var_wine_temp",
                            "vintage_form",
                            "var_vintage_temp",
                            "var_acquire",
                            "var_acquire_vintages",
                            "var_awards_temp",
                            "test",
                            "var_note",
                            "var_pages",
                            "var_page_flow",
                            "var_wine_search_criteria"
                         );
    
    sort($var_sessions);
    
    foreach ($var_sessions as $key => $val) {
        echo "<h2>$val:</h2>";
        print_r($_SESSION[$val]);
        echo "<br/><input type=\"button\" class=\"button\" id=\"$val\" value=\"unset\" />";
        echo "<hr/>";
    }
    
    echo "<h2>var_vintage_temp:var_grapes:</h2>";
        print_r($_SESSION['var_vintage_temp']['var_grapes']);
    echo "<hr/>";

    echo "<h2>var_vintage_temp:var_awards:</h2>";
        print_r($_SESSION['var_vintage_temp']['var_awards']);
    echo "<hr/>";
    
    echo "<h2>var_vintage_temp:var_images:</h2>";
        print_r($_SESSION['var_vintage_temp']['var_images']);
    echo "<hr/>";

    echo "<h2>ALL:</h2>";
    print_r($_SESSION);
    echo "<hr/>";
    
    echo "<h2>Cookies:</h2>";
    echo $_COOKIE['cookie_username'];
    echo "<hr/>";


?>

</div>
</body>