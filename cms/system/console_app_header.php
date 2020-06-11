<?php

/**
 * @DESC Хэдер файл для консольного приложения.
 * @DESC Содержит весь необходимый код для запуска консольного приложения. После чего можно использоватль все плюшки зенда, модели, контроллеры, конфиг
 */
error_reporting(E_ERROR);
ini_set('display_errors',1);
include($root.'cms/system/functions.php');          /// Подключаем функции
include($root.'cms/settings/config.php');              /// Глобальный Конфиг CMS
include($root.'site/settings/db.php');                    //// Конфигурация БД
include($root.'site/settings/config.php');		     //// Конфигурация текущего сайта

if (file_exists($root.'site/settings/config.local.php'))
    include($root.'site/settings/config.local.php');		     //// Конфигурация текущего сайта

$paths = implode(PATH_SEPARATOR,
								array_merge(
									array(
										$config['path']['libs'],
										$config['path']['system'],
										$config['path']['settings'],
										$config['path']['site_system'],
										$config['path']['site_models']
									),
									(($plugin_dirs) ? $plugin_dirs : array())
								)
);
set_include_path($paths);

function __autoload($className) {
	$className = str_replace("_","/",$className);
	include_once("{$className}.php");
}
spl_autoload_register('__autoload');                   //// Регистрируем автолоадеры
spl_autoload_register('myAL');		             //// Регистрируем автолоадеры

$config = new Zend_Config($config,true);
Zend_Registry::set('cnf',$config);


if (!function_exists('out')) {
	/**
	 * @desc функция для очистки вывода от лишних переводов строки и пробелов.
	 * @desc Если скрипт выполнеяется в кроне и выводит любую информацию, то она отправляется на e-mail
	 * @desc PHP всегда выводит переводы строки, которые присутствуют в исходнике
	 * @param type $text
	 * @return type
	 */
	function out($text) {
		if (preg_replace('#([\s\t\n\r])#Usi','',$text)!='')
			return $text;
	}
}

