<?php
foreach($_POST as $key => $value){
	Echo "<p>$key ->";
	print_r($value);
	Echo "\n</p>";
}
foreach($_COOKIE as $cookie => $val){
	echo "<p>$cookie $val</p";
}