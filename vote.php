<?php // Open DB connection
require_once './includes/lib.php';



if(is_post_request()){

	//check if we've arrived thanks to an add item request
	if(isset($_POST['itemInput'])&&isset($_POST['category'])){
		$success=new_item();

	}
	//Process any votes before taking updates;
	process_vote_changes();

}

function new_item(){
	require_once './includes/auth.php';
	$db=Database::getDB();
	//say no to XSS and also
	if(!preg_match('/[A-Z a-z0-9.]/', $_POST['itemInput'])||!preg_match('/[A-Z a-z0-9.]/', $_POST['category'])){
		echo 'data not valid';
		return false;
	}
	$itemName=strtoupper(trim($_POST['itemInput']));
	if ($itemName==''){
		return false;}
	$category=strtoupper($_POST['category']);
	$id=$db->getItemId($itemName);
	#If we got results, just set a vote for it and the vote processor will handle it
	if($id){
		$_POST["VOTE"][]=$id;
		return true;
	}#otherwise, first create an entry for it in the item table, then let the vote processor vote for it.
	else{
		#If user is not an admin, ensure the category already exists, boot if not
		if(!$authStatus && !$db->getCatExists($category)){
			echo 'not auth to create category';
			return false;
		}
		# having passed input validation, insert the item, boot if failure.
		$itemId=$db->putItemDefinition($itemName,$category);
		if($itemId){
			$_POST["VOTE"][]=$itemId;
			return true;
		}else{
			return false;
		}		
	}
}