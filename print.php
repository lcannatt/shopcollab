<?php
require_once './includes/lib.php';
require_once './includes/auth.php';

if(!$authStatus||!is_post_request()){
    die;
}
if(!isset($_POST['VOTE'])){
    echo "No items selected. Please go back and try again.";
    die;
}

function get_item_data($id){
    global $db;
    $query= $db->prepare("SELECT NAME, CATEGORY FROM item_master WHERE ITEM_ID=?");
    $query->bind_param("i",$id);
    $query->execute();
    $query->store_result();
    $query->bind_result($name,$cat);
    $query->fetch();
    $row=Array($name,$cat);
    return $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Print</title>
    <style>
        li{
            margin-left:2em;
        }
        body{
            margin:1em;
        }
    </style>
</head>
<body>
<?php
$last='';
foreach($_POST['VOTE'] as $id){
    $data=get_item_data($id);
    if($data[1]!=$last){
        echo "<h2>".ucfirst(strtolower($data[1]))."</h2>";
        $last=$data[1];
    }
    echo "<li>".ucfirst(strtolower($data[0]))."</li>";
}
?>
</body>
</html>