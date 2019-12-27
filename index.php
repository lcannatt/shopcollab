<?php

require_once './includes/lib.php';
require_once './includes/pc_general.php';
require_once './includes/database.php';

?>
<!DOCTYPE html>
<html lang='en'>
	<head>
		<title>Utilities</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="default.css">
	</head>
	<body>
		<?php pc_navBar();?>
		<div class="preview">
			<h2>News Update</h2>
			<div style="margin:0em 2em;">
				<p><b>Some subtitle type stuff goes here</b></p>
				<div style="margin:0em 1em;">
				<p>This would be an adequate place for elaboration</p>
				</div>
			</div>
		</div>
		<div class="preview">
			<h2>Shopping List</h2>
			<p>Top 6 Most Requested Items</p>
			<?php
			$db=Database::getDB();
			$shoppingList=$db->getShoppingPreview();
			if(count($shoppingList)==0){echo "<p>Whoops! Nothing to see here.. Maybe the shopping got done.</p>";}
			else{prioBoxes($shoppingList);}
			?>
			<p><a href="shopping-list.php">Link to shopping list</a></p>
		</div>
	</body>
</html>