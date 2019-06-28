<!DOCTYPE html>
<html lang='en'>
<head>
	<title>Admin</title>
	<link rel="stylesheet" type="text/css" href="default.css">
	<link rel="stylesheet" type="text/css" href="admin.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="./scripts/masonry.js"></script>
</head>
<body onresize="evalCols()" onload="initCols()">
<?php 
require './includes/nav.php';	
require_once './includes/lib.php';
require_once './includes/auth.php';
require_once './includes/boxGen.php';
if(!$authStatus){
	echo '<div class="preview">
		<h2>
			Please Log In
		</h2>
		<form action="./admin.php" method="post">
			<input type="password" name="password">
			<button type="submit" value="submit">Authenticate</button>
		</form>
		<br>
	</div></body></html>';
	die;
}
if(is_post_request()){
	//only do one please
	if(isset($_POST['shopped'])){
		set_go_shopping();
	}
	else if(isset($_POST['undo'])){
		set_undo_shopping();
	}
	else if(isset($_POST['delete'])){
		foreach($_POST['todelete'] as $itemID){
			set_delete_item($itemID);
		}
	}
	else if(isset($_POST['VOTE'])){
		process_vote_changes();
	}
}

$items=get_item_master_list();

if(!get_shopping_list_empty()){
	$disableUndo=" disabled";
	$disableShop="";
}else{
	$disableUndo='';
	$disableShop=" disabled";
}
?>

	<div class="preview">
		<h2>
			Super Secret Admin Panel
		</h2>
	</div>
	<div class="preview">
		<h3>Shopping Cart Admin</h3>
		<p>
		<form action="./admin.php" method="post">
			<button type="submit" name="shopped" value="shopped"<?=$disableShop?>>The Shopping is Done</button>
			<button type="submit" name="undo" value="undo"<?=$disableUndo?>>Undo The Shopping</button>
			<div class="floater">
				<div class="columns">
					<div class="listContainer">
						<h4>Item Master List Maintenance</h4>
						<select name="todelete[]" multiple="multiple" style="height:120pt;width:15em;">
							<?php
							foreach($items as $item){
								echo "<option value=\"$item[0]\">".htmlspecialchars($item[1])."</option>";
							}
							?>
						</select>
						<br/>
						<button type="submit" name="delete" value="delete">Delete Selected Items</button>
					</div>
					<div class="listContainer">
						<h4>Add Old Items to Shopping List</h4>
						<select name="VOTE[]" multiple="multiple" style="height:120pt;width:15em;">
							<?php
							foreach($items as $item){
								echo "<option value=\"$item[0]\">".htmlspecialchars($item[1])."</option>";
							}
							?>
						</select>
						<br/>
						<button type="submit" name="add" value="add">Add to List</button>
					</div>
				</div>
			</div>
		</form>
		</p>
	</div>
	<div class="preview">
		<h3>
			Print Selection
		</h3>
		<form id="voteList" action="./print.php" method="post">
			<!-- <span class="buttonLink">Hide My Votes</span> -->
		<div class="floater">
			<div class="columns">
				<?php 
				$votes=get_vote_info();
				if(count($votes)==0){
					echo "<p>The shopping list is empty</p>";
				}else{
					foreach($votes as $cat => $shoppingList){
						printBoxes($shoppingList,ucfirst(strtolower($cat)));
					}
				}
				?>
			</div>
		</div>
		<br>
			<input type="submit" value="Generate List of Selected"> 
		</form>
	</div>
	<div class="preview">
			<form action="/admin.php" method="post">
				<p><button type="submit" name="logout" value="logout">Log Out</button></p>
			</form>
		</div>
</body>
<script>
	document.querySelectorAll('input[type="checkbox"]').forEach(function f(e){e.checked=true;});
</script>
</html>