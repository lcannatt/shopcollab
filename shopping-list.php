<?php
require_once './includes/boxGen.php';
require_once './includes/lib.php';
require_once './vote.php';


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
	<?php require './includes/nav.php';	
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

		<form id="voteList" action="./shopping-list.php" method="post">
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
			<input class= "buttonLink" type="submit" value="Submit+Update"> 
		</form>
	</div>
	<div class="preview">
		<h4>
			How This Works:
		</h4>
		<div class="justified">
		<p>The list above is how we decide what gets bought for the office.</p>
		<p>If you see something that you want already on the list, vote for it by clicking on it.</p>
		<p>If you want something and it isn't on the list, click <b>Add Item</b>, and add it to the appropriate category.</p>
		<br>
		<p>You get a maximum of one vote per item. Items with more votes are more likely to be bought.</p>
		</div>
	</div>
	<script type="text/javascript" src="./scripts/voteHandler.js"></script>
</body>
</html>