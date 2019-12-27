<?php
require_once './includes/lib.php';
require_once './includes/auth.php';
require_once './includes/database.php';

if(!$authStatus||!is_post_request()){
    die;
}
if(!isset($_POST['VOTE'])){
    echo "No items selected. Please go back and try again.";
    die;
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
$db=Database::getDB();
$data=$db->getPrintInfo($_POST['VOTE']);
foreach($data as $row){
    if($last!=$row['CATEGORY']){
        echo "<h2>".ucfirst(strtolower($row['CATEGORY']))."</h2>";
        $last=$row['CATEGORY'];
    }
    echo "<li>".ucfirst(strtolower($row['NAME']))."</li>";
    echo "\n";
}
?>
</body>
</html>