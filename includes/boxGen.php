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
	foreach ($boxes as $item => $prio){
		switch($prio){
			case 0:$class="";
				break;
			case 1:$class=" midPrio";
				break;
			case 2:$class=" highPrio";
				break;
			default:$class="";
		}
		echo "<div class=\"itemBox$class\"><span class=\"nameSpan\">".ucfirst(strtolower(htmlspecialchars($item)))."</span></div>";
	}
	echo "</div>";
}

//Create prioritized boxes using pipe delimited metadata
//Array is ordered and follows this pattern: "{Priority}|{Vote Count}|{Item Name}|{Request Date}|{Voted on prior}|{item id}"]
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
		switch($meta[1]){
			case 0:$class="";
				break;
			case 1:$class=" midPrio";
				break;
			case 2:$class=" highPrio";
				break;
			default:$class="";
		}
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
			echo "<label for=\"$id\" class=\"voteBox$voted\" title=\"Requested {$meta[3]}\" prio=\"{$meta[1]}\">";
			echo "<span class=\"leftSpan\">$item</span>"; //Name of Item
			echo "<span class=\"rightSpan\">{$meta[0]}</span>"; //Vote Count of Item
		echo "</label>";
	}
	echo "</div>";
}
