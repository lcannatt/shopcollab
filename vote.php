<?php // Open DB connection
require_once './includes/lib.php';

$db = open_db();

if(is_post_request()){

	//check if we've arrived thanks to an add item request
	if(isset($_POST['itemInput'])&&isset($_POST['category'])){
		$success=new_item();

	}
	//Process any votes before taking updates;
	process_vote_changes();

}

// Query DB for existing shopping list
$votes=get_vote_info();
close_db($db);
