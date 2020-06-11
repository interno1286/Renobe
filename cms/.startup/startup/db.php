<?php
$config['usedb']['on'] = false;

$config['db'] = array (
	// Адаптер
	'adapter'   => 'PDO_PGSQL',
	// Параметры соединения
	'params'    => array(
		// Хост
		'host'          => 'host_val',
		// Логин
		'username'      => 'username_val',
		// Пасс
		'password'      => 'password_val',
		// Имя БД
		'dbname'        => 'dbname_val',
		// Доп конфигурация драйверов БД
		'driver_options'=> array(PDO::ATTR_EMULATE_PREPARES => true),
		//
		'profiler'      => false
	)
);

