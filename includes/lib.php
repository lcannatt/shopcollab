<?php
require_once 'config.php';

# POST/GET IDENTIFIERS

function is_post_request(){
	return $_SERVER['REQUEST_METHOD']=='POST';
}
function is_get_request(){
	return $_SERVER['REQUEST_METHOD']=='GET';
}

# DB OPERATIONS

function open_db(){
	$connection=mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
	return $connection;
}
function close_db($connection){
	mysqli_close($connection);
}

# DATA RETRIEVAL

function get_user_votes($db,$tableName){
	#Returns mysqli stmt object containing all the items the current user has voted for
	#Helper function : Output in form ID, SESSID, item name
	$user_query = $db->prepare("SELECT item_master.ITEM_ID,SESSID,NAME FROM $tableName LEFT JOIN item_master ON $tableName.ITEM_ID = item_master.ITEM_ID WHERE SESSID=?");
	$user_query->bind_param('s',$_SERVER['REMOTE_ADDR']);
	$user_query->execute();
	$user_query->store_result();
	return $user_query;
}
function get_table_totals($db,$tableName){
	#Returns mysqli stmt object containing all items on shopping list with associated metadata
	#Helper function : Output is Name,count,earliest Vote date, largest age of vote, id, and category
	$vote_stmt=$db->prepare("SELECT item_master.NAME,Count(VOTE_DATE),MIN(VOTE_DATE),MAX(DATEDIFF(NOW(),VOTE_DATE)),$tableName.ITEM_ID,CATEGORY FROM $tableName LEFT JOIN item_master ON $tableName.ITEM_ID = item_master.ITEM_ID GROUP BY item_master.NAME;");
	$vote_stmt->execute();
	$vote_stmt->store_result();
	return $vote_stmt;
}
function get_vote_info(){
	#wrapper function for vote box retrieval and formatting
	#returns formatted shoppinglist array for rendering (see format_votes)
	global $db;
	$tableName ='votes_active';
	$vote_set = get_table_totals($db,$tableName);
	$user_set = get_user_votes($db,$tableName);
	$voteList = format_votes($vote_set,$user_set);
	return $voteList;
}
function get_previously_requested_items(){
	#retrieves contents of item_master
	global $db;
	$itemsByCat=[];
	$catQuery = "SELECT CATEGORY,NAME FROM item_master ORDER BY CATEGORY";
	$catSet = mysqli_query($db,$catQuery);
	while($row=mysqli_fetch_row($catSet)){
		$itemsByCat[$row[0]][]=$row[1];
	}
	mysqli_free_result($catSet);
	return $itemsByCat;
}
function preview_shopping_list(){
	# returns top $itemCount priority items from overall shopping list.
	# formats output array for priority boxes [item=>priority]
	global $db;
	$filePath="./scripts/Top6FrontPage.sql";
	$f=fopen($filePath,"r");
	$sqlCommand=fread($f,filesize($filePath));
	$listSet=mysqli_query($db,$sqlCommand);
	$final=[];
	while($row=mysqli_fetch_row($listSet)){
		$voteCount=$row[1];
		$prio=0;
		if($voteCount>=3){
			$prio=1;
		}
		if($voteCount>=6){
			$prio=2;
		}
		$final[ucfirst(strtolower($row[0]))]=$prio;
	}
	return $final;
}
function get_shopping_list_empty(){
	global $db;
	$query="SELECT count(*) FROM votes_active;";
	$sqlResult=mysqli_query($db,$query);
	$count=mysqli_fetch_row($sqlResult)[0];
	return $count==0;
}


function get_name($db,$id){
	# Helper function
	# retrieve name from ID
	$set=mysqli_query($db,"SELECT NAME FROM item_master WHERE ITEM_ID=$id;");
	$name=mysqli_fetch_row($set)[0];
	mysqli_free_result($set);
	return $name;
}

function get_item_master_list(){
	#returns array of existing item ID,Name pairs.
	global $db;
	$query="SELECT ITEM_ID,NAME FROM item_master;";
	$sqlOut=mysqli_query($db,$query);
	$list=[];
	while($row=mysqli_fetch_row($sqlOut)){
		$list[]=$row;
	}
	mysqli_free_result($sqlOut);
	return $list;
}

# SQL DATA CHANGING

function set_delete_votes($voteList){
	global $db;
	foreach($voteList as $item){
		$deleteQuery=$db->prepare("DELETE FROM votes_active WHERE (ITEM_ID=? AND SESSID=?);");
		$deleteQuery->bind_param('is',$item,$_SERVER['REMOTE_ADDR']);
		$deleteQuery->execute();
	}
}
function set_insert_votes($voteList){
	global $db;
	foreach($voteList as $item){
		$insertQuery=$db->prepare("INSERT INTO votes_active (ITEM_ID,SESSID,VOTE_DATE) VALUES (?,?,NOW());");
		$insertQuery->bind_param('is',$item,$_SERVER['REMOTE_ADDR']);
		$insertQuery->execute();
	}
}

function set_insert_item($db,$itemName,$category){
	#add new item to the masterlist
	$insertQuery=$db->prepare("INSERT INTO item_master (NAME,CATEGORY) VALUES (?,?);");
	$insertQuery->bind_param("ss",$itemName,$category);
	$insertQuery->execute();
	return ($insertQuery)?true:false;
	
}

function set_delete_item($itemID){
	#remove an item and all votes past and present for it from the db.
	global $db;
	if(is_null($itemID)||!preg_match('/^\d+$/',$itemID)){
		return false;
	}
	$query="DELETE FROM votes_active WHERE (ITEM_ID =$itemID);";
	$out=mysqli_query($db,$query);

	$query="DELETE FROM votes_inactive WHERE (ITEM_ID =$itemID);";
	$out=mysqli_query($db,$query);

	$query="DELETE FROM item_master WHERE (ITEM_ID =$itemID);";
	$out=mysqli_query($db,$query);

	return true;
}


function set_go_shopping(){
	# move active votes to inactive votes table
	global $db;
	$filePath="./scripts/WentShopping.sql";
	$f=fopen($filePath,"r");
	$commands=fread($f,filesize($filePath));
	$commands = explode(";", $commands);
	foreach($commands as $command){
		if(trim($command)){
		$listSet=mysqli_query($db,$command);
		}
		
	}
}
function set_undo_shopping(){
	# returns most recent shopping list to the active votes table
	global $db;
	$filePath="./scripts/UndoShopping.sql";
	$f=fopen($filePath,"r");
	$commands=fread($f,filesize($filePath));
	$commands = explode(";", $commands);
	foreach($commands as $command){
		if(trim($command)){
		$listSet=mysqli_query($db,$command);
		}
	}
}

# USER INPUT PROCESSING

function process_vote_changes(){
	global $db;
	$sqlUserVotes=get_user_votes($db,'votes_active');
	# user set Output in form ID, SESSID, item name
	$postedVals=[];
	$dbVals=[];
	if(isset($_POST['VOTE'])){
		$postedVals=$_POST['VOTE'];
	}
	$sqlUserVotes->bind_result($id,$sessid,$name);
	while($sqlUserVotes->fetch()){
		$dbVals[]=$id;
	}
	#Only delete by inference if this was a sync call, not an add.
	if (!(isset($_POST['itemInput'])||isset($_POST['add'])))set_delete_votes(array_diff($dbVals,$postedVals));
	set_insert_votes(array_diff($postedVals,$dbVals));
}
#handles user facing item addition
function new_item(){
	require_once './includes/auth.php';
	global $db;
	//say no to XSS and also
	if(!preg_match('/[a-z0-9.]/', $_POST['itemInput'])||!preg_match('/[a-z0-9.]/', $_POST['category'])){
		return false;
	}
	$itemName=strtoupper($_POST['itemInput']);
	$itemName=trim($itemName);
	if ($itemName==''){
		return false;}
	$category=strtoupper($_POST['category']);
	$check=$db->prepare("SELECT ITEM_ID FROM item_master WHERE NAME=?;");
	$check->bind_param('s',$itemName);
	$check->execute();
	$check->store_result();
	#If we got results, just set a vote for it and the vote processor will handle it
	if($check->num_rows>0){
		$check->bind_result($id);
		$check->fetch();
		$_POST["VOTE"][]=$id;
		return true;
	}#otherwise, first create an entry for it in the item table, then let the vote processor vote for it.
	else{
		#for now, restrict all creation of new categories.
		#will eventually allow this for logged in admins
		if(!$authStatus){
			$catQuery=$db->prepare("SELECT COUNT(*) FROM item_master WHERE CATEGORY=?");
			$catQuery->bind_param("s",$category);
			$catQuery->execute();
			$catQuery=$catQuery->get_result();
			if($catQuery->fetch_row()[0]<1){
				return false;
			}
		}
		if(set_insert_item($db,$itemName,$category)){
			$newIdQuery=$db->query("SELECT MAX(ITEM_ID) FROM item_master");
			$id=$newIdQuery->fetch_row()[0];
			$_POST["VOTE"][]=$id;
			return true;
		}else{
			return false;
		}
		#insert the new item
		
	}
}

# DATA STRUCTURE TRANSFORM HELPER

function format_votes(&$vote_set,&$user_set){

	# Formats output vote to format $voteCount|$prio(is this old)|$item|$date|$voted|$itemID
	$temp_user_set=[];
	$result=[];
	if (!is_null($user_set)){
		$user_set->bind_result($id,$sessid,$name);
		while($user_set->fetch()){
		$temp_user_set[$id]=$sessid;
		}
	}
	$vote_set->bind_result($item,$voteCount,$date,$datediff,$itemID,$category);
	while($vote_set->fetch()){
		$prio=0;
		$voted=0;
		#Calculate priority
		if($datediff>DATE_CUTOFF){
			$prio+=1;
		}
		if(isset($temp_user_set[$itemID])){
			$voted=1;
		}
		$pieces="$voteCount|$prio|$item|$date|$voted|$itemID";
		#Sort formatted string into categories
		$result[$category][]=$pieces;
	}
	# order by vote count
	foreach($result as $cat => $infos){
		sort($infos);
		$result[$cat]=array_reverse($infos);
	}

	return $result;
}
$db = open_db();