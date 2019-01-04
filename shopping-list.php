<?php
require_once './includes/boxGen.php';
require_once './includes/lib.php';



// Open DB connection
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
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<title>Shopping List</title>
	<link rel="stylesheet" type="text/css" href="default.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="./scripts/masonry.js"></script>
</head>
<body onresize="evalCols()" onload="initCols()">
	<?php require './includes/nav.php';	?>
	<?php
	if(isset($success)&&!$success){
		echo "<div class=\"error\"><p>Bad input. No Soup for you.</p></div>";
	}
	?>
	<div class="preview">
		<h2>
			Shopping List
		</h2>
	</div>
	<div class="preview">

		<form action="./shopping-list.php" method="post">
			<!-- <span class="buttonLink">Hide My Votes</span> -->
		<div class="floater">
			<div class="columns">
				<?php 
				if(count($votes)==0){
					echo "<p>I have run out of things to buy.</p>";
				}else{
					foreach($votes as $cat => $shoppingList){
						voteBoxes($shoppingList,ucfirst(strtolower($cat)));
					}
				}
				?>
			</div>
		</div>
			<a class="buttonLink" href="./new-item.php">Add Item</a>
			<input class= "buttonLink" type="submit" value="Submit"> 
		</form>
	</div>
	<script type="text/javascript" src="./scripts/voteHandler.js"></script>
</body>
</html>