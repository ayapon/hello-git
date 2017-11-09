<!DOCTYPE html>
<html>
<head>
<title>Announcer - Styles preview</title>
<link href="../public/announcer-styles.css" rel="stylesheet" type="text/css">
<style type="text/css">
body{
	width: 75%;
	margin: 0 auto;
	font: 13px Arial, Helvetica, sans-serif;
}
body div{
	opacity: 1;
	-webkit-transition: opacity 1s;
	-moz-transition: opacity 1s;
}
body:hover div{
	opacity: 0.2;
	-webkit-transition: opacity 1s;
	-moz-transition: opacity 1s;
}
.wrap:hover{
	-webkit-transition: opacity 0.5s;
	-moz-transition: opacity 0.5s;
	opacity: 1;
}
.wrap:hover div{
	-webkit-transition: opacity 0.5s;
	-moz-transition: opacity 0.5s;
	opacity: 1;
}
</style>
</head>
<body>

<h1>Announcer - Styles preview</h1>
<p>Colors are randomly added. Refresh page to apply new colors</p>

<?php

$bgc = array(
	'#1abc9c', '#2ecc71', '#3498db', '#9b59b6', '#34495e', '#f1c40f', '#e67e22', '#e74c3c', '#d35400',
	'#FF0000', '#E87E04', '#1BA39C', '#2C3E50', '#3A539B', '#34495E', '#7019C1', '#2DC119', '#C11919', 
	'#2D19C1', '#08C7D8', '#D008D8', '#B7D808'
);

for($i = 1; $i<=11; $i++){
	echo '
	
	<div class="wrap">
	<h3>Style ' . $i . '</h3>
	<div id="announcer-box" class="announcer announcer-style' . $i . '" style="border-color:#000000; background-color:' . $bgc[array_rand($bgc)] . '; color: #fff">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sagittis mauris eu orci egestas et mollis metus ullamcorper.</div>
	</div>
	
	';
	
}

?>

<br>
<p align="center"><a href="http://www.aakashweb.com/" target="_blank" rel="nofollow">Aakash Web</a> | <a href="http://www.aakashweb.com/wordpress-plugins/announcer/" target="_blank" rel="nofollow">Announcer plugin</a></p>
<br>

</body>
</html>