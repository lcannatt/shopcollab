<?php
//create standard boxes from an indexed array
function genBoxes(&$boxes){
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
function prioBoxes(&$boxes){
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

//Create prioritized boxes using pipe delimited metadata
//Array is ordered and follows this pattern: "{Vote Count}|{Priority//DEPRECATED}|{Item Name}|{Request Date}|{Voted on prior}|{item id}"]
//Priority assignment is handled with js since it can change based on user input.
function voteBoxes(&$boxes,$title=Null){
	echo "<div class=\"listContainer\">";
	if(is_null($title)){
		$title="Category";
	}
	echo "<h4>$title</h4>";
	foreach($boxes as $parse){
		$meta = preg_split('/\|/', $parse);
		$item = ucfirst(strtolower(htmlspecialchars($meta[2])));
		$id=$meta[5];
		switch($meta[4]){
			case 0:
				$voted="";
				$checked="";
				break;
			case 1:
				$voted=" selected";
				$checked=" checked";
				break;
		}
		// echo "<label for=\"$item\">";
		echo "<input type=\"checkbox\" name =\"VOTE[]\" value=\"$id\" id=\"$id\"$checked >";
			echo "<label for=\"$id\" class=\"voteBox$voted\" title=\"Requested {$meta[3]}\">";
			echo "<span class=\"leftSpan\">$item</span>"; //Name of Item
			echo "<span class=\"rightSpan\">{$meta[0]}</span>"; //Vote Count of Item
		echo "</label>";
	}
	echo "</div>";
}

//Create prioritized boxes using pipe delimited metadata
//Array is ordered and follows this pattern: "{Vote Count}|{Priority|{Item Name}|{Request Date}|{Voted on prior}|{item id}"]
function printBoxes(&$boxes,$title=Null){
	echo "<div class=\"listContainer\">";
	if(is_null($title)){
		$title="Category";
	}
	echo "<h4>$title</h4>";
	foreach($boxes as $parse){
		$meta = preg_split('/\|/', $parse);
		$item = ucfirst(strtolower(htmlspecialchars($meta[2])));
		$id=$meta[5];
		// echo "<label for=\"$item\">";
		echo "<input type=\"checkbox\" name =\"VOTE[]\" value=\"$id\" id=\"$id\">";
			echo "<label for=\"$id\" class=\"printBox\">";
			echo "<span class=\"leftSpan\">$item</span>"; //Name of Item
			echo "<span class=\"rightSpan\">{$meta[0]}</span>"; //Vote Count of Item
		echo "</label>";
	}
	echo "</div>";
}
