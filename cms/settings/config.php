<?php
//
// Конфигурационный массив с настройками
//
$config = array();

$site_config = ((isset($root)) ? $root : '')."site/settings/config.php";

if (file_exists($site_config))
    include($site_config);

include($config['path']['root'].'site/settings/db.php');

// Кэширование java script
$config['cache_js'] = false;
$config['cache_css'] = false;

//
// URLs
//
$config['url']['site'] = $config['url']['base'].'site/';
$config['url']['cms'] = $config['url']['base'].'cms/';

// Каталог с публичным доступом
$config['url']['public'] = $config['url']['base'].'public/';

$config['url']['cms_public'] = $config['url']['cms'].'public/';
//
// Пути
//
$config['path']['cms'] = $config['path']['root'].'cms/';


$config['path']['public'] = $config['path']['root'].'public/';

// Библиотеки

$config['path']['libs'] = $config['path']['root'].'library/';
$config['path']['vendor'] = $config['path']['root'].'library/vendor/';
// Системная
$config['path']['system'] = $config['path']['cms'].'system/';
// Настройки
$config['path']['settings'] = $config['path']['cms'].'settings/';

// Папка сайта
$config['path']['site'] = $config['path']['root'].'site/';
// Системные файлы сайта
$config['path']['site_system'] = $config['path']['site'].'system/';
// Модели сайта
$config['path']['site_models'] = $config['path']['site'].'models/';


//
// Настройки
//

// Доп. параметры
$config['common'] = array (
	// Кодировка
	'charset' => 'utf-8',
 	// Суффикс (расширение) файлов шаблонов
	'template_suffix' => 'tpl',
);

$config['debug']['message'] = 'Во время выполнения операции произошла ошибка. Уведомление об этом отправлено администратору и вскоре ошибка будет устранена. Попробуйте повторить попытку позже.';

$root_plugins_config = glob($config['path']['root']."plugins/*/settings/config.php");
$site_plugins_config = glob($config['path']['site']."plugins/*/settings/config.php");

if ($root_plugins_config)
	foreach ($root_plugins_config as $config_file) include($config_file);

if ($site_plugins_config)
	foreach ($site_plugins_config as $config_file) include($config_file);

////////////// include /site/config.local.php
$site_config_local = ((isset($root)) ? $root : '')."site/settings/config.local.php";

if (file_exists($site_config_local))
	include($site_config_local);

////////////// init plugins ///////////////////
include($config['path']['root'].'cms/system/pluginsInit.php');


$config["path"]["views"] = $config['path']['site']."skins/{$config["skin"]}/views/";
$config["path"]["skin"] = $config['path']['site']."skins/{$config["skin"]}/";
$config["url"]["skin"] = $config['url']['site']."skins/{$config["skin"]}/";
