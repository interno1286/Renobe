<?php

$start_time = microtime(true);

mb_internal_encoding("UTF-8");
setlocale(LC_TIME, 'ru_RU');

require 'cms/system/functions.php';
require 'cms/settings/config.php';
require 'cms/system/site.php';

if ($config['debug']['on']) {
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}else  {
	ini_set('display_errors',0);
	error_reporting(0);
}

if ($config['debug']['on']) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}else {
    
    if (file_exists('site/.svn/wc.db')) {
        $ts = filemtime('site/.svn/wc.db');
    }else {
        $ts = time() - 600;
    };
    header("Expires: ".gmdate("D, d M Y H:i:s",time()+60*60*24)." GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s",$ts) . " GMT");
}

site::run();

site::checkpointsReport();