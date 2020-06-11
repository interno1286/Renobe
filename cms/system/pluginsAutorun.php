<?php

/////////////////////// Автозапуск /////////////////////////////////////////

$main_startup_files = glob($config['path']['root']."plugins/*/system/startup.php");
$site_startup_files = glob($config['path']['site']."plugins/*/system/startup.php");

$startup_files = array_merge(
		($main_startup_files) ? $main_startup_files : array(),
		($site_startup_files ) ? $site_startup_files  : array()
);

if ($startup_files)
	foreach ($startup_files as $sf)
		include($sf);

///////////////////////////////////////////////////////////////////////////
