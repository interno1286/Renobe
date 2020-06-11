<?php

if (!is_dir($conf['filesystem.files_path']))
	mkdir($conf['filesystem.files_path'],0777,true);


$conf['resize_upload']['max_width'] = 1024;
$conf['resize_upload']['max_height'] = 768;


$local_config = $_SERVER['DOCUMENT_ROOT'].'/site/settings/cyberim_config.php';

if (file_exists($local_config))
	include($local_config);


include($_SERVER['DOCUMENT_ROOT'].'/cms/system/functions.php');
?>