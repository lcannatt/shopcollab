<?php
require_once 'config.php';
require_once 'database.php';

# POST/GET IDENTIFIERS

function is_post_request(){
	return $_SERVER['REQUEST_METHOD']=='POST';
}
function is_get_request(){
	return $_SERVER['REQUEST_METHOD']=='GET';
}

# USER INPUT PROCESSING

function process_vote_changes(){
	$db=Database::getDB();
	$dbVals=$db->getUserVotes($_SERVER['REMOTE_ADDR']);
	# user set is array of voted item IDs
	$postedVals=[];
	if(isset($_POST['VOTE'])){
		$postedVals=$_POST['VOTE'];
	}
	$toDelete=array_diff($dbVals,$postedVals);
	#Only delete by inference if this was a sync call, not an add.
	if (!(isset($_POST['itemInput']) || isset($_POST['add'])) && $toDelete){
		$db->delVotes($toDelete,$_SERVER['REMOTE_ADDR']);
	}
	$db->putInsertVotes(array_diff($postedVals,$dbVals),$_SERVER['REMOTE_ADDR']);
}
