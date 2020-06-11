<?php

if (!isset($_GET['f'])) die();

$f = preg_replace('#([^a-z0-9_а-я])#ui','',$_GET['f']);

if ($f)
	unlink($f.'.html');

header("Location: /temp/error/");

?>
