<?php
$authStatus=0;
update_device_table();
if(!sess_authenticated()){
    if(is_post_request() && isset($_POST['password'])){
        #To set this password, follow instructions in config.php
        if(password_verify($_POST['password'],PASS_HASH)){
            authenticate_sess();
            $authStatus=1;
        }
    }
}else{
    if(isset($_POST['logout'])){
        end_sess();
    }else{
        reauth_sess();
        $authStatus=1;
    }

}


function sess_authenticated(){
    global $db;
    $authTest=$db->prepare("SELECT * FROM devices WHERE SESS_ID=?;");
    $authTest->bind_param('s',$_SERVER['REMOTE_ADDR']);
    $authTest->execute();
    $authTest->store_result();
    return $authTest->num_rows > 0;
}

function authenticate_sess(){
    global $db;
    $authQ=$db->prepare("INSERT INTO devices VALUES (?,now());");
    $authQ->bind_param("s",$_SERVER['REMOTE_ADDR']);
    $authQ->execute();
}

function reauth_sess(){
    global $db;
    $reauthQ=$db->prepare("UPDATE devices SET LAST_ACTIVE=now() WHERE SESS_ID=?");
    $reauthQ->bind_param("s",$_SERVER['REMOTE_ADDR']);
    $reauthQ->execute();
}

function update_device_table(){
    global $db;
    $update="DELETE FROM devices WHERE TIMESTAMPDIFF(MINUTE,LAST_ACTIVE,now())>1440;";
    mysqli_query($db,$update);
}

function end_sess(){
    global $db;
    $end=$db->prepare("DELETE FROM devices WHERE SESS_ID=?");
    $end->bind_param("s",$_SERVER['REMOTE_ADDR']);
    $end->execute();
}