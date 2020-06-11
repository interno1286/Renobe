<?php

$r = "../../../../../..";
include($r.'/site/settings/config.php');

$conf['resize_upload']['max_width'] = 1024;
$conf['resize_upload']['max_height'] = 768;


$local_config = $config['path']['root'].'site/settings/cyberim_config.php';

if (file_exists($local_config))
	include($local_config);

if (!is_dir($conf['filesystem.files_path']))
	mkdir($conf['filesystem.files_path'],0777,true);

include($config['path']['root'].'cms/system/tools/photo.php');
?>