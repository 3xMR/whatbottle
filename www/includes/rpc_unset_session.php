<?php
/* 
 * RPC to unset provided session
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");

if($_POST['session']>""){
    $session = $_POST['session'];
    unset($_SESSION[$session]);  
} else {
    echo 'rpc_unset_session.php - No session name provided';
}

?>
