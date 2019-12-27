<?php
$siteroot='';
function pc_navBar(){
	echo '<div class="TOC">
	<h1>Utilities</h1>
	<div class="NavBar">
	<ul>
		<li><a href="<?=$siteroot?>/index.php">Home</a></li><!--
		--><li><a href="<?=$siteroot?>/shopping-list.php">Shopping List</a></li><!--
		--><li><a href="<?=$siteroot?>/admin.php">Admin Tools</a></li>
	</ul>
	</div>
</div>';
}

