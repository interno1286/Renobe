<?php

// Корневой путь к сайту
if (!isset($root) && isset($_SERVER['DOCUMENT_ROOT']))
    $root = $_SERVER['DOCUMENT_ROOT'].'/';

// режим отладки
$config['debug']['on'] = true;
// Профилирование запросов
$config['debug']['profiling'] = false;

// скин по умолчанию
$config['skin'] = 'default';
// плагин по умолчанию
$config['default_plugin'] = 'default';
// email менеджера (письма о заказах, регистрациях и пр.)
$config['manager_email'] = 'chenzya@gmail.com';
// email разработчика (отладочная информация, сообщения об ошибках)
$config['debug']['email'] = 'chenzya@gmail.com';

// логин пароль админа, просетейшая авторизация черезе /auth
$config['admin_login'] = 'admin';
$config['admin_password'] = '123';

// корневой путь сайта
$config['path']['root'] = $root;

// Корневой URL сайта
$config['url']['base'] = '/';
$config['smtp_ip'] = 'localhost';

$config['jquery'] = true;
$config['bootstrap'] = false;
$config['bootstrap3'] = true;
$config['jqueryUI'] = true;
$config['mainJS'] = true;

$config['refresh_css'] = false;
$config['refresh_script'] = false;

// автоматически подключать JQUERY BOOTSTRAP и системные скрипты
$config['autoIncludeHead'] = true;

// кэширование
$config['use_memcached'] = false;
$config['memcached_server']['default'] = '127.0.0.1';
$config['memcached_port']['default'] = '11211';

