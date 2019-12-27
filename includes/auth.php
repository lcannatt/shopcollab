<?php
require_once 'database.php';
$authStatus=false;

//Authentication Logic
$db = Database::getDB();
if(session_status() !== PHP_SESSION_ACTIVE) session_start();
$authStatus=$db->authenticateDevice(session_id());

if(!$authStatus){
    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['password'])){
        #To set this password, follow instructions in config.php
        if(password_verify($_POST['password'],PASS_HASH)){
            $authStatus=$db->putAddDevice(session_id());
        }
    }
}else if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['logout'])){
    $authStatus=$db->delLogoutDevice(session_id());
}
