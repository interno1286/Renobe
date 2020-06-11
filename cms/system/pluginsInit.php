<?php

$controllers = glob($config['path']['root'] . "plugins/*/controllers", GLOB_ONLYDIR);
$models = glob($config['path']['root'] . "plugins/*/models", GLOB_ONLYDIR);
$system = glob($config['path']['root'] . "plugins/*/system", GLOB_ONLYDIR);

$controllers_site = glob($config['path']['site'] . "plugins/*/controllers", GLOB_ONLYDIR);
$models_site = glob($config['path']['site'] . "plugins/*/models", GLOB_ONLYDIR);
$system_site = glob($config['path']['site'] . "plugins/*/system", GLOB_ONLYDIR);


$plugin_dirs = array_merge(
        ($controllers) ? $controllers : array(), ($models) ? $models : array(), ($system) ? $system : array(), ($controllers_site) ? $controllers_site : array(), ($models_site) ? $models_site : array(), ($system_site) ? $system_site : array()
);

if (!$plugin_dirs)
    $plugin_dirs = array();

/////////////////////// Подключаем конфиги /////////////////////////////////
$main_config_files = glob($config['path']['root'] . "plugins/*/settings/config.php");
$site_config_files = glob($config['path']['site'] . "plugins/*/settings/config.php");

if ($main_config_files)
    foreach ($main_config_files as $cf)
        include($cf);

if ($site_config_files)
    foreach ($site_config_files as $cf)
        include($cf);

////////////////////////////////////////////////////////////////////////////
/////////////////////// Подключаем функции /////////////////////////////////
$main_func_files = glob($config['path']['root'] . "plugins/*/system/functions.php");
$site_func_files = glob($config['path']['site'] . "plugins/*/system/functions.php");

$func_files = array_merge(
        ($main_func_files) ? $main_func_files : array(), ($site_func_files ) ? $site_func_files : array()
);

if ($func_files)
    foreach ($func_files as $ff)
        include($ff);

////////////////////////////////////////////////////////////////////////////
////////////////////////// AUTOSTART AT PLUGINS ////////////////////////////
$main_startup_files = glob($config['path']['root'] . "plugins/*/settings/startup.php");
$site_startup_files = glob($config['path']['site'] . "plugins/*/settings/startup.php");

$startup_files = array_merge(
        ($main_startup_files) ? $main_startup_files : array(), ($site_startup_files ) ? $site_startup_files : array()
);

if ($startup_files)
    foreach ($startup_files as $sf)
        include($sf);

////////////////////////////////////////////////////////////////////////////
?>
