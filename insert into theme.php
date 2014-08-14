<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Code Snippets to Insert into Theme File</title>
</head>
<body>


<?php

$walker = new CustomMenuWalker();
 
$args = array(
    'menu' => 'WakerMenu',
    'walker' => $walker,
);
wp_nav_menu($args);
?>


</body>
</html>
