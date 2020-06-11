<?php

// Корневой путь к сайту
if (!isset($root) && isset($_SERVER['DOCUMENT_ROOT']))
    $root = $_SERVER['DOCUMENT_ROOT'].'/';

// режим отладки
$config['debug']['on'] = true;
// Профилирование запросов
$config['debug']['profiling'] = false;

// скин по умолчанию
$config['skin'] = 'main';
// плагин по умолчанию
$config['default_plugin'] = 'default';
// email менеджера (письма о заказах, регистрациях и пр.)
$config['manager_email'] = 'welcode1@yandex.ru';
// email разработчика (отладочная информация, сообщения об ошибках)
$config['debug']['email'] = 'welcode1@yandex.ru';

// логин пароль админа, просетейшая авторизация черезе /auth
$config['admin_login'] = 'admin';
$config['admin_password'] = 'D@2abD7Rwkmk';

// корневой путь сайта
$config['path']['root'] = $root;

// Корневой URL сайта
$config['url']['base'] = '/';
$config['smtp_ip'] = 'localhost';

$config['jquery'] = true;
$config['bootstrap'] = false;
$config['bootstrap3'] = false;
$config['bootstrap4'] = true;

$config['jqueryUI'] = true;
$config['mainJS'] = true;

$config['refresh_css'] = false;
$config['refresh_script'] = false;

// автоматически подключать JQUERY BOOTSTRAP и системные скрипты
$config['autoIncludeHead'] = false;

// кэширование
$config['use_memcached'] = true;
$config['memcached_server']['default'] = '127.0.0.1';
$config['memcached_port']['default'] = '11211';


$config['news']['dir'] = $config['path']['root'].'public/news/';

$config['news']['big']['width'] = 800;
$config['news']['big']['height'] = 800;

$config['news']['medium']['width'] = 600;
$config['news']['medium']['height'] = 600;

$config['news']['small']['width'] = 200;
$config['news']['small']['height'] = 200;

$config['news']['micro']['width'] = 80;
$config['news']['micro']['height'] = 80;





////////////////// GLOBAL LOG FUNCTION /////////////

$clogcounter = 0;

if (!function_exists('clog')) {
    function clog($message) {
        global $clogcounter, $root;
        $l = $root.'site/system/sync_log.txt';


        $line = ["\n".strftime('%d/%m %H:%M:%S').": ".$message];

        if ($clogcounter%10==0) {
            $mu = ceil(memory_get_usage()/1024/1024);
            $mh = ceil(memory_get_peak_usage()/1024/1024);

            $line[]="\nMemory USE/HIGH ".$mu.'/'.$mh." Mb";
        }

        $clogcounter++;

        if ($clogcounter>10000)
            $clogcounter=0;

        $log = (file_exists($l)) ? file($l) : [];

        $log = array_merge($log, $line);

        if (sizeof($log)>40) {
            $out = [];

            for ($x=(sizeof($log)-40);$x<sizeof($log);$x++)
                $out[]=$log[$x];
        }else $out = $log;

        file_put_contents($l,implode("",$out));
    }
}

//////////////////////////