<?php

class CMSError {

    protected static $views_folder = null;
    public static $tpl = "";

    public static function catchException(Exception $exception) {
        //header("HTTP/1.0 404 Not Found");
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

        global $config;

        if ($config['debug']['on'])
            error_reporting(E_ALL);

        // Получение текста ошибки
        $message = $exception->getMessage();
        // Получение трейса ошибки как строки
        $trace = $exception->getTraceAsString();

        if (file_exists($config['path']['system'] . 'SmartyView.php') && file_exists($config['path']['libs'] . 'Zend/View/Interface.php')) {
            include_once($config['path']['system'] . 'SmartyView.php');
            $smarty = new SmartyView();

            self::$tpl = (file_exists($config['path']['views'] . 'error.tpl')) ? $config['path']['views'] . 'error.tpl' : $config['path']['root'] . 'cms/views/error.tpl';
            
            self::$views_folder = (!is_dir($config['path']['views']) || !file_exists($config['path']['views'] . 'error.tpl')) ? $config['path']['site'] . 'skins/default/views/' : $config['path']['views'];

            $smarty->setScriptPath(self::$views_folder);

            site::initBootStrap3($smarty);
            site::initJqueryUI($smarty);
            site::initJquery($smarty);
        } else
            $smarty = new fakeSmarty();

        if ($exception->getCode()!==666) {  /// 666 is a code for not found library 
            $smarty->assign('config', new Zend_Config($config, true));
            $smarty->assign('conf', $config);
        }
        $smarty->assign('_SERVER',$_SERVER);

        $not_found_error = false;

        try {
            if (file_exists($config['path']['libs'] . 'Zend/Registry.php')) {
                $user_data = Zend_Registry::get('user_data');
                $smarty->assign('user_data', $user_data);
                Zend_Registry::set('view', $smarty);
            }
        } catch (Exception $e) {
            
        };


        if (strpos(strtolower($message), "invalid controller") !== false or strpos(strtolower($message), "does not exist") !== false) {
            
            global $config;
            
            if (
                    isset($config['imported_from']) 
                    && $config['imported_from'] 
                    && $config['debug']['on']
                ) {
                
                $valid_for_download_ext = array(
                    'css',
                    'js',
                    'gif',
                    'png',
                    'jpg',
                    'ico',
                    'mp4',
                    'webm',
                    'woff'
                );
                
                $ext = strtolower(pathinfo(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),PATHINFO_EXTENSION));

                if (in_array($ext, $valid_for_download_ext)) {
                
                    $url = $config['imported_from'];
                    $need = $_SERVER['REQUEST_URI'];

                    $headers = get_headers($url.$need);

                    if (strpos($headers[0],'200 OK')!==false) {
                        $data = file_get_contents($url.$need);
                        $folder = dirname($need);

                        if (substr($folder,0,1)=='/') $folder = $config['path']['root'].substr($folder,1);

                        mkdir($folder,0777,true);
                        
                        $save_to = parse_url(urldecode($need), PHP_URL_PATH);
                        
                        if (substr($save_to,0,1)=='/') 
                                $save_to = $config['path']['root'].substr($save_to,1);

                        file_put_contents($save_to, $data);

                        header("Location: ".$_SERVER['REQUEST_URI']);
                        return true;
                    }
                }
            }
            
            
            header("HTTP/1.0 404 Not Found");
            $not_found_error = true;
            $smarty->assign("error_text", "Запрашиваемой страницы не существует");
            $text = "
			Страница, которую Вы ищете(" . $_SERVER['REQUEST_URI'] . "), не найдена. Возможно, она была удалена, изменился её адрес, либо страница временно недоступна.
			<br><br>
			Пожалуйста, попробуйте следующее:
			<ul>
			    <li>Убедитесь, что адрес, набранный в адресной строке Вашего браузера, не содержит ошибок.</li>
			    <li>Если Вы попали на эту страницу по ссылке, сообщите администратору системы о некорректной ссылке.</li>
			    <li>Нажмите кнопку «Назад» чтобы попробовать перейти по другой ссылке.</li>
			</ul>
			";
            $smarty->assign("error_status", "404");
            $smarty->assign("not_found", "1");
            
            ///search for non used html in current skin
            
            $files = glob($config['path']['skin'].'/views/*.html.tpl');
            
            foreach ($files as &$f)
                $f = str_replace ($config['path']['skin'].'/views/', '', $f);
            
            $smarty->assign('newFiles', $files);
            
        } else {
            $smarty->assign("not_found", "0");
            $smarty->assign("error_text", "Извините, произошла непредвиденная ошибка");
            $smarty->assign("error_status", "500");
            $text = "
			Приносим свои извинения, но по каким-то причинам произошла непредвиденная ошибка,
			уведомление об этом отправлено администратору. Попробуйте повторить попытку позднее.
			";
        }
        // Если включен режим отладки отображаем сообщение о ошибке на экран
        if ($config['debug']['on']) {

            $text = "<textarea style='width:98%;height:400px;color:black;font-size: 13px;' wrap='off'>$message\n\n$trace</textarea>";

            if ($not_found_error)
                site::includePostfixData(null, $text);
            
        }else {        // Иначе выводим сообщение об ошибке
            if ($not_found_error) {

                $root = $_SERVER["DOCUMENT_ROOT"];

                if (!is_dir("$root/temp"))
                    mkdir("$root/temp", 0777, true);

                $f = fopen("$root/temp/404.txt", "a");

                if ($f) {
                    $ref = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

                    $f_text = $_SERVER['SERVER_NAME'] . "|" . $_SERVER['REQUEST_URI'] . "|" . $ref . "|" . date("h:i:s") . "\n";
                    fwrite($f, $f_text);
                    fclose($f);
                }
            } else if ($exception->getMessage() != 'Доступ закрыт!')
                tools_debug::errorReport($exception);
        }

        $req_file_ext = strtolower(pathinfo(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), PATHINFO_EXTENSION));

        if ($not_found_error && in_array($req_file_ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp')) && file_exists($config['path']['root'] . 'public/images/not_found_image.jpg')) {
            header('Location: ' . '/public/images/not_found_image.jpg');
        } else {
            $smarty->assign("content", $text);
            //$smarty->display(self::$views_folder . "error.tpl");
            $smarty->display(self::$tpl);
            //$smarty->display($config['path']['views']."index.tpl");
        }
    }

}

class fakeSmarty {

    function __call($name, $arguments) {
        
    }

    function assign($var, $val) {
        $this->$var = $val;
    }

    function display() {
        echo $this->content;
    }

}
