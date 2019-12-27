<?php
$siteroot='';

function pc_navBar(){
	global $siteroot;
	echo '<div class="TOC">
	<h1>Utilities</h1>
	<div class="NavBar">
	<ul>
		<li><a href="'.$siteroot.'/index.php">Home</a></li><!--
		--><li><a href="'.$siteroot.'/shopping-list.php">Shopping List</a></li><!--
		--><li><a href="'.$siteroot.'/admin.php">Admin Tools</a></li>
	</ul>
	</div>
</div>';
}

//create standard boxes from an indexed array
function genBoxes($boxes){
	echo "<div class=\"container\">";
			foreach ($boxes as $name){
				echo "<div class=\"itemBox\">";
				// echo "<div style=\"clear: both; overflow: hidden;\" class=\"leftSpan\"><img src=\"http://www.strikingsupport.com/wp-content/uploads/2013/12/santashatleft.png\" width=\"128\" height=\"128\" alt=\"\" class=\"xmashatleft\"></div>";
				echo "<span class=\"nameSpan\">".ucfirst(strtolower(htmlspecialchars($name)));
				echo "</span></div>";
			}
			echo "</div>";
}

//create prioritized boxes from a key value array [Item=>Priority]
function prioBoxes($boxes){
	echo "<div class=\"container\">";
	foreach ($boxes as $item => $votes){
		$class="";
		if($votes > HIGH_VOTE_CUTOFF){
			$class=" highPrio";
		}
		else if($votes > MID_VOTE_CUTOFF){
			$class=" midPrio";
		}
		echo "<div class=\"itemBox$class\"><span class=\"nameSpan\">".ucfirst(strtolower(htmlspecialchars($item)))."</span></div>";
	}
	echo "</div>";
}

//Create prioritized boxes using category separated array of item datas
//Array is ordered and follows this pattern: [{CATEGORY},{VOTES},{NAME},{VOTE_DATE},{VOTED},{ITEM_ID}]...
//Priority assignment is handled with js since it can change based on user input.
function voteBoxes($boxes,$title=null){
	echo "<div class=\"listContainer\">";
	if(is_null($title)){
		$title="Category";
	}
	echo "<h4>$title</h4>";
	foreach($boxes as $box){
		$itemName = ucfirst(strtolower(htmlspecialchars($box['NAME'])));
		$voted="";
		$checked="";
		if($box['VOTED']){
			$voted=" selected";
			$checked=" checked";
		}
		// echo "<label for=\"$item\">";
		echo "<input type=\"checkbox\" name =\"VOTE[]\" value=\"{$box['ITEM_ID']}\" id=\"{$box['ITEM_ID']}\"$checked >";
			echo "<label for=\"{$box['ITEM_ID']}\" class=\"voteBox $voted\" title=\"Requested {$box['VOTE_DATE']}\">";
			echo "<span class=\"leftSpan\">$itemName</span>"; //Name of Item
			echo "<span class=\"rightSpan\">{$box['VOTES']}</span>"; //Vote Count of Item
		echo "</label>";
	}
	echo "</div>";
}

//Create prioritized boxes using pipe delimited metadata
//Array is ordered and follows this pattern: "{Vote Count}|{Priority|{Item Name}|{Request Date}|{Voted on prior}|{item id}"]
function printBoxes($boxes,$title=null){
	echo "<div class=\"listContainer\">";
	if(is_null($title)){
		$title="Category";
	}
	echo "<h4>$title</h4>";
	foreach($boxes as $box){
		$itemName = ucfirst(strtolower(htmlspecialchars($box['NAME'])));
		// echo "<label for=\"$item\">";
		echo "<input type=\"checkbox\" name =\"VOTE[]\" value=\"{$box['ITEM_ID']}\" id=\"{$box['ITEM_ID']}\">";
			echo "<label for=\"{$box['ITEM_ID']}\" class=\"printBox\">";
			echo "<span class=\"leftSpan\">$itemName</span>"; //Name of Item
			echo "<span class=\"rightSpan\">{$box['VOTES']}</span>"; //Vote Count of Item
		echo "</label>";
	}
	echo "</div>";
}
