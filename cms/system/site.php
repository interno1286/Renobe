<?php

/**
 * @desc Класс для выполнения околоядерных действий
 */
class site {

    private static $jsTransport = false; /////// Признак транспортировки переменных PHP в среду JavaScript
    private static $markTpl = true; /////// Признак маркировки пути шаблона перед выдачей
    private static $contentPostfixHook = array();  //////// Массив содержащий в себе функции, содержимое которых будет вставляться после контента
    private static $main_menu = array(); ////// Основное меню сайта
    private static $mainMenuDisabled = false; ///// Отключить главное меню 
    public static $ajaxView = false;  ////// Признак выдачи контента через AJAX
    public static $autoHeadScripts = true; ///// Автоматическая вставка скриптов / стилей в тег HEAD
    public static $customizeMode = false; ////Признак режима редактирования
    public static $usedTpls = array(); //// �?спользуемые при ототбражении TPL 
    
    
    public static $liveTrace = false; //// если true то будет писаться лог запуска run.log в корне
    
    static function init() {
        self::$usedTpls = array();
    }
    
    static function run() {
        
        try {

            global $config, $plugin_dirs;

            header('X-Powered-By: GLENN CMS');

            $paths = implode(PATH_SEPARATOR,
                array_merge(
                        array(
                                $config['path']['libs'],
                                $config['path']['vendor'],
                                $config['path']['system'],
                                $config['path']['settings'],
                                $config['path']['site_system'],
                                $config['path']['site_models']
                        ),
                        $plugin_dirs
                )
            ).PATH_SEPARATOR.get_include_path();

            set_include_path($paths);

            require $config['path']['system'].'Kernel.php';
            
            if (isset($config['autoIncludeHead']) && $config['autoIncludeHead']===false)
                self::$autoHeadScripts = false;

            Kernel::run($config);
            
        } catch (Exception $e) {
            CMSError::catchException($e);
        }

    }
    
    static function customizeModeOn() {
        self::$customizeMode = true;
    }
    
    static function customizeModeOff() {
        self::$customizeMode = false;
    }
    
    
    
    /**
     * @desc Функция получает контент для текущей страницы
     * @return string
     */
    static function getContent() {
        
        if (self::$customizeMode)
            return self::customizeContent();
        
        $view = Zend_Registry::get('view');
        $config = Zend_Registry::get('cnf');
        
        $c=$view->content;
        
        if (!$view->content) {
            $front = Zend_Controller_Front::getInstance();

            $plugin_dir = $front->getModuleDirectory();

            $controller = $front->getRequest()->getControllerName();
            $plugin = $front->getRequest()->getModuleName();
            $action = $front->getRequest()->getActionName();

            list($parent_plugin, $parent_controller) = self::getParentPlugin();

            $parent_class_template = $config->path->root . 'plugins/' . strtolower($parent_plugin) . '/views/content/' . str_replace('controller', '', strtolower($parent_controller)) . '/' . $action . '.tpl';

            $skin_path = $config->path->skin;

            $tpl = '';

            $search_paths = array(
                $skin_path . 'views/content/' . $plugin . '/' . $controller . '/' . $action . '.tpl',
                $plugin_dir . '/views/content/' . $controller . '/' . $action . '.tpl',
                $parent_class_template
            );

            /// Выбираем шаблон из найденных в соответствии с приоритетом
            /// Папка скина, папка views текущего плагина, папка views родительского плагина
            /// Первый найденный - используем
            switch (true) {
                case (file_exists($skin_path . 'views/content/' . $plugin . '/' . $controller . '/' . $action . '.tpl')):
                    $tpl = $skin_path . 'views/content/' . $plugin . '/' . $controller . '/' . $action . '.tpl';
                    break;

                case (file_exists($plugin_dir . '/views/content/' . $controller . '/' . $action . '.tpl')):
                    $tpl = $plugin_dir . '/views/content/' . $controller . '/' . $action . '.tpl';
                    break;

                case (file_exists($parent_class_template)):
                    $tpl = $parent_class_template;
                    break;

                case (isset($view->content)):
                    $tpl = $config->path->skin . 'views/ajax/index.tpl';
                    break;
            }

            if ($tpl != '') {
                $content = $view->render($tpl);
                if (self::$jsTransport) {
                    $content.=$view->render($config->path->cms . 'views/js_var_transport.tpl');
                }

                if ($config->debug->on) {
                    site::rememberTpl($tpl);
                    site::rememberContentTpl($tpl);

                    $content = "
                        <div id='glenn_content_div'>$content</div>
                        <input type='hidden' id='content_tpl' value='$tpl' />
                    ";
                }

                return self::addDebugTplMarker($config, $tpl, $content);
            }
            
        }else {
            return $view->content;
        }
        
        return "
			<div class='alert alert-error'>
			Не найден шаблон!<br /><br />

			Искал в:<br />" . implode("<br />", $search_paths) . "
			</div>
		";
    }

    /***
     * @desc отдаёт содержимое в реиме отладки
     */
    static function customizeContent() {
        return "Область содержимого сайта";
    }
    
    
    /**
     * @desc Активириует перенос основных переменных конфига в среду JavaScript
     */
    static function enabledJsVarTransport() {
        self::$jsTransport = true;
    }

    /**
     * @desc Получение родительского плагина, от которого наследован текущий
     * @return array
     */
    static function getParentPlugin() {

        $front = Zend_Controller_Front::getInstance();

        $request = $front->getRequest();

        if (!$request)
            return false;

        $controller = $request->getControllerName();
        $plugin = $request->getModuleName();

        $c1_name = ucfirst($controller) . 'Controller';
        $c2_name = ucfirst($plugin) . '_' . ucfirst($controller) . 'Controller';

        if (class_exists($c1_name))
            $current_controller_class_name = $c1_name;
        else if (class_exists($c2_name))
            $current_controller_class_name = $c2_name;

        $parent_class = get_parent_class($current_controller_class_name);
        $elements = explode("_", $parent_class);

        $parent_plugin = strtolower((isset($elements[0])) ? $elements[0] : '');
        $parent_controller = strtolower((isset($elements[1])) ? $elements[1] : '');

        return array(
            'parent_plugin' => $parent_plugin,
            0 => $parent_plugin,
            'parent_controller' => $parent_controller,
            1 => $parent_controller
        );
    }

    /**
     * @desc Функция, рендерит тпл, автоматически выбирая откуда её брать
     * @param type $tpl
     * @return type
     */
    static function renderTpl($tpl, $default=false) {
        $config = Zend_Registry::get('cnf');

        if ($tpl{0} != '/' && $tpl{1} != ':') {

            $parent = self::getParentPlugin();

            $plugin_root_path = self::getPluginRootPath();

            $module = self::getCurrentModule();

            $search_paths = array(
                $plugin_root_path . 'views/' . $tpl,
                $config->path->root . 'plugins/' . $parent['parent_plugin'] . '/views/' . $tpl,
                $config->path->skin . 'views/' . $module . '/' . $tpl,
                $config->path->skin . 'views/' . $tpl,
                $config->path->root . 'cms/views/' . $tpl
            );
            
            $f = false;
            
            foreach ($search_paths as $p) {
                if (file_exists($p)) {
                    $f = true;        $tpl = $p;
                    break;
                }
            }
            
            if (!$f && $default) { 
                $f   = true;
                $tpl = $default;
            }
            
            if (!$f) return "
                    <div class='alert alert-error'>
                    Не могу найти TPL $tpl
                    <br /><br />
                    �?скал в:<br />
                    " . implode("<br />", $search_paths) . "
                    </div>
                ";
        }
        
        return self::addDebugTplMarker($config, $tpl, Zend_Registry::get('view')->render($tpl));
    }

    static function getPluginRootPath() {
        $front = Zend_Controller_Front::getInstance();
        return $front->getModuleDirectory() . '/';
    }

    static function getCurrentModule() {

        $request = Zend_Controller_Front::getInstance()->getRequest();

        return ($request) ? $request->getModuleName() : false;
    }
    
    static function getCurrentPlugin() {
        return self::getCurrentModule();
    }

    /**
     * @desc в режиме дебага маркируется блок и указывается откуда берётся tpl
     * @param type $config
     * @param type $filename
     * @param type $content
     * @return type
     */
    static function addDebugTplMarker($config = null, $filename, $content) {

        $config = ($config === null) ? Zend_Registry::get('cnf') : $config;

        if ($config->debug->on) {
            site::rememberTpl($filename);
        
            if (self::$markTpl && !self::$ajaxView) { ///// если включён дебаг
                if (@json_decode($content) != false || str_replace(array(' ', "\n", "\r", "\t"), '', $content) == '[]')
                    return $content; //// �? наш контент это не JSON даннные

                if (strpos($content, "debug $filename") === false) {   ////// защита от повторной маркировки
                    $content = "\n<!-- ////////////// debug $filename /////////////// -->\n$content\n<!-- ///// END OF $filename //// -->\n";
                }
            }
        }

        return $content;
    }
    
    static function rememberTpl($tpl) {
        
        if (site::getCurrentModule()=='skineditor')
            return false;
        
        if (!dirname($tpl))
            $tpl = Zend_Registry::get('cnf')->path->skin.'views/'.$tpl;
        
        self::$usedTpls[site::getCurrentModule()][$tpl] = $tpl;
        
        Zend_Registry::get('user_data')->usedTpls = self::$usedTpls;
        
    }
    
    static function rememberContentTpl($tpl) {
        if (site::getCurrentModule()=='skineditor')
            return false;
        
        Zend_Registry::get('user_data')->content_tpl = $tpl;
    }

    /**
     * @desc Функция добавляет к контенту различную информацию добавленную туда через функцию site::addDataToContent()
     * @param type $content
     */
    static function includePostfixData($config = null, &$content) {
        $config = ($config === null) ? Zend_Registry::get('cnf') : $config;

        if ($config->debug->on && self::$markTpl && !self::$ajaxView) { ///// если включён дебаг
            $content.="<!-- //////////////// POSTFIX DATA ////////////////// !-->";
        }

        foreach (self::$contentPostfixHook as $func) {
            list($object, $method) = explode('::', $func);

            if (!isset($object) || !isset($method))
                throw new Exception("Не корректный формат добавленной функции $func через site::addDataToContent");

            $method = str_replace(array('(', ')'), '', $method);

            if (!method_exists($object, $method))
                throw new Exception("Не найден метод $func");

            $result = $object::$method($content);
        }

        if ($config->debug->on) { ///// если включён дебаг
            //self::includeMainMenu($content);
            tools_errors::lastErrorsAlert($content);

            if (self::$markTpl && !self::$ajaxView)
                $content.="<!-- //////////////// END OF POSTFIX DATA ////////////////// !-->";
        }
    }

    static function disableTplMarker() {
        self::$markTpl = false;
    }

    static function linkCKEditor() {
        $view = Zend_Registry::get('view');
        $config = Zend_Registry::get('cnf');

        $view->addScript($config->url->base . 'cms/public/js/ckeditor/ckeditor.js');
    }

    static function getSkins() {
        $config = Zend_Registry::get('cnf');
        $skins = glob($config->path->root . 'site/skins/*', GLOB_ONLYDIR);

        foreach ($skins as &$skin) {
            $skin = str_replace($config->path->root . 'site/skins/', '', $skin);
        }

        return $skins;
    }

    /**
     * @desc Функция добавляет к контенту различную информацию
     * @param type $data
     */
    static function addDataToContent($func_name) {
        self::$contentPostfixHook[] = $func_name;
    }

    static function setCheckPoint($name = "") {
        tools_profiler::checkpoint($name);
    }

    static function checkpointsReport() {
        tools_profiler::report();
    }

    ////////////////////////////MAIN MENU /////////////////////////////
    
    static function disableMainMenu() {
        self::$mainMenuDisabled = true;
    }
    

    static function addMainMenuElement($name, $onclick) {
        self::$main_menu[] = array(
            'name' => $name,
            'onclick' => $onclick
        );
    }

    static function includeMainMenu(&$content) {
        global $config;
        
        if (self::$mainMenuDisabled)
            return false;
        
        @list($before, $after) = explode("</body>", $content);

        Zend_Registry::get('view')->assign('main_menu_elements', self::$main_menu);

        $content = $before . "\n\n" . Zend_Registry::get('view')->render($config['path']['root'] . 'cms/views/main_menu.tpl') . "</body>$after";
    }

    static function initBootStrap4(&$view) {
        self::deInitBootStrap2($view);
        self::deInitBootStrap3($view);
        
        global $config;
        $view->addScript($config['url']['base'] . 'cms/public/bootstrap4/js/bootstrap.min.js', 'low');
        $view->addScript($config['url']['base'] . 'cms/public/js/bsDialog.js', 'low');
        $view->addScript($config['url']['base'] . 'cms/public/bootstrap/js/bootstrap-datepicker.js', 'high');
        $view->addStyle($config['url']['base'] . 'cms/public/bootstrap4/css/bootstrap.min.css', 'low');
    }
    
    
    
    static function initBootStrap3(&$view) {
        global $config;
        
        self::deInitBootStrap2($view);
        
        $view->addScript($config['url']['base'] . 'cms/public/bootstrap3/js/bootstrap.min.js', 'low');
        $view->addScript($config['url']['base'] . 'cms/public/js/bsDialog.js', 'low');
        $view->addScript($config['url']['base'] . 'cms/public/bootstrap/js/bootstrap-datepicker.js', 'high');
        $view->addStyle($config['url']['base'] . 'cms/public/bootstrap3/css/bootstrap.min.css', 'low');
        $view->addStyle($config['url']['base'] . 'cms/public/bootstrap/css/datepicker.css', 'high');
    }
    
    
    static function deInitBootStrap3(&$view) {
        global $config;
        $view->delScript($config['url']['base'] . 'cms/public/bootstrap3/js/bootstrap.min.js', 'low');
        $view->delScript($config['url']['base'] . 'cms/public/js/bsDialog.js', 'low');
        $view->delScript($config['url']['base'] . 'cms/public/bootstrap/js/bootstrap-datepicker.js', 'high');
        $view->delStyle($config['url']['base'] . 'cms/public/bootstrap3/css/bootstrap.min.css', 'low');
        $view->delStyle($config['url']['base'] . 'cms/public/bootstrap/css/datepicker.css', 'high');
    }

    static function deInitBootStrap2(&$view) {
        global $config;
        
        $view->delScript($config['url']['base'] . 'cms/public/bootstrap/js/bootstrap.min.js', 'low');
        $view->delScript($config['url']['base'] . 'cms/public/js/bsDialog.js', 'low');
        $view->delScript($config['url']['base'] . 'cms/public/bootstrap/js/bootstrap-datepicker.js', 'high');
        $view->delStyle($config['url']['base'] . 'cms/public/bootstrap/css/bootstrap.min.css', 'low');
        $view->delStyle($config['url']['base'] . 'cms/public/bootstrap/css/datepicker.css', 'high');
        
    }

    
    
    static function initJquery(&$view) {
        global $config;
        /*
        $view->addScript(array(
            $config['url']['base'] . 'cms/public/js/jquery/jquery-1.10.2.min.js',
            $config['url']['base'] . 'cms/public/js/ajaxDialog.js',
            $config['url']['base'] . 'cms/public/js/jquery/jquery.cookie.js',
            $config['url']['base'] . 'cms/public/js/jquery/jquery.scrollTo.min.js',
            $config['url']['base'] . 'cms/public/js/jquery/jquery.transit.min.js',
        ), 'low');
         * 
         */
        
        $view->addScript(array(
            $config['url']['base'] . 'cms/public/js/jquery3/jquery-3.4.1.min.js',
            $config['url']['base'] . 'cms/public/js/ajaxDialog.js',
            $config['url']['base'] . 'cms/public/js/jquery/jquery.cookie.js',
            $config['url']['base'] . 'cms/public/js/jquery/jquery.scrollTo.min.js',
            $config['url']['base'] . 'cms/public/js/jquery/jquery.transit.min.js',
        ), 'low');
        
    }

    static function initJqueryUI(&$view, $theme = "smoothness") {
        global $config;
        $view->addScript(array(
            //$this->config->url->base.'cms/public/js/jquery/jquery-ui-1.10.0.custom.min.js',
            $config['url']['base'] . 'cms/public/js/jquery/jquery-ui.min.js',
            $config['url']['base'] . 'cms/public/js/jquery/jquery.ui.datepicker-ru.js',
            $config['url']['base'] . 'cms/public/js/jquery/ckeditor_in_jq_dialog_patch.js'), 'low');

        $view->addStyle($config['url']['base'] . "cms/public/css/jquery/$theme/jquery-ui.custom.min.css", 'high');
    }

    ////////////////////////////////////////////////////////////////////




    static function isAjax() {
        return (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
                ||
                site::$ajaxView
        );
    }

    static function isPost() {
        return ($_SERVER['REQUEST_METHOD'] == 'POST');
    }
    
    static function includeHeadElements(&$smarty, &$content) {
        $config = Zend_Registry::get('cnf');
        
        //die($config->path->root.'cms/views/head_block.tpl');
        
        $block = $smarty->fetch($config->path->root.'cms/views/head_block.tpl')."<!--headincluded-->";
        
        if ((strpos($content,'<head>')!==false) && strpos($content, '<!--headincluded-->')===false) {
            
            $pos = mb_strpos($content, '<head>');
            
            $before = mb_substr($content, 0, $pos+6);
            $after = mb_substr($content, $pos+6);
            
            //list($before,$after) = explode("<head>",$content);
            
            $content = $before.$block.$after;
        }
        
    }
    
    
    static function json_response($response) {
        Zend_Registry::get('view')->content = Zend_Json::encode($response);
    }
}
