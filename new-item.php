<?php
require_once './includes/pc_general.php';
require_once './includes/lib.php';
require_once './includes/auth.php';
require_once './includes/database.php';
$db = Database::getDB();
$itemsByCat=$db->getAllItemsCatSort();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<title>New Item</title>
	<link rel="stylesheet" type="text/css" href="default.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<?php pc_navBar();	?>
	<div class="preview">
		<h2>
			Add an item to the shopping list
		</h2>
	</div>
	<div class="preview">
		<form action="./shopping-list.php" method="post">
			<p>Category:
			<?php
				if($authStatus){
					echo '<input id="catSelect" name="category" list="catOpts">';
					echo '<datalist id="catOpts">';
					foreach($itemsByCat as $cat=>$items){echo '<option value="'.ucfirst(strtolower($cat)).'"></option>';}
					echo '</datalist>';
				} else {
					echo '<select id="catSelect" name="category">';
					foreach($itemsByCat as $cat=>$items){echo "<option value=\"".ucfirst(strtolower($cat))."\">".ucfirst(strtolower($cat))."</option>";}
					echo '</select>';
				}
				
			?>
			
			</p>
			<p>Item:
			<input id="categoryList" list="replaceCat" name="itemInput">
				<?php
				foreach($itemsByCat as $cat=>$items){
					echo "<datalist id=\"".ucfirst(strtolower($cat))."\">";
					foreach($items as $item){
						echo "<option value=\"".ucfirst(strtolower(htmlspecialchars($item)))."\">";
					}
					echo "</datalist>";
				}
				?>
			</p>
			<input type="submit" value="Add it!">
		</form>
	</div>
	<?php
	$button='<input type="submit" name="logout" value="Log Out">';
	if(!$authStatus){
		$button='<button type="button" id="login" value="1">Admin Log In</button>';
	}
	echo '<div class="preview"><form action="./new-item.php" method="post">';
	echo $button;
	echo '</form></div>';
	?>
	<script type="text/javascript" src="./scripts/newItem.js"></script>
</body>
</html>