<?php

class Kernel {

    protected static $config;

    /**
     * Запуск приложения
     */
    public static function run($config) {

        try {
            site::init();

            // проверяем есть ли доп. библиотеки Smarty ZendFramework и пр.
            self::checkLibraryPresent();

            // Включаем autoload
            Zend_Loader_Autoloader::getInstance()
                    ->setFallbackAutoloader(true)
                    ->registerNamespace('ZendExt_')
                    ->pushAutoloader('smartyAL')
                    ->pushAutoloader('MyAL');

            tools_profiler::checkpoint('Kernel run begin');

            // Инициализация конфига
            self::$config = $cnf = new Zend_Config($config, true);

            site::setCheckPoint('Config init');

            // Конфиг в реестр
            Zend_Registry::set('cnf', $cnf);

            // Подключение к БД
            self::setDbAdapter();

            site::setCheckPoint('Set DB Adapter');

            $views = (is_dir($cnf->path->views)) ? $cnf->path->views : $cnf->path->site . 'skins/default/views/';

            site::setCheckPoint('check view dir exists');

            Zend_Layout::startMvc(array(
                'layoutPath' => $views,
                'layout' => 'index',
            ));

            $layout = Zend_Layout::getMvcInstance();

            site::setCheckPoint('Init layout');
            //
            // Подключение вывода
            //
            $view = new SmartyView();
            $view->setScriptPath($views);

            site::setCheckPoint('smarty init');

            // Заносим в реестр
            Zend_Registry::set('view', $view);

            // Устанавливаем расширения для темплейтов
            $layout->setViewSuffix($cnf->common->template_suffix);

            // Установка вывода
            $layout->setView($view);

            site::setCheckPoint('Layout manipulation');

            // Создание фронт контроллера
            $front = Zend_Controller_Front::getInstance();


            site::setCheckPoint('Init front controller');

            // Создание роутера
            $router = new Zend_Controller_Router_Rewrite();

            site::setCheckPoint('Init router');

            // Установка параметров фронт контроллера
            $front->setBaseUrl($cnf->url->base)
                  ->throwexceptions(true)
                  ->setRouter($router);

            site::setCheckPoint('Front controller config');

            self::linkRoutes();

            site::setCheckPoint('Link plugin routes');

            // Отключаем стандартный вывод (вывод идет через смарти)
            $front->setParam('noViewRenderer', true);

            //ini_set('session.cookie_path', $cnf->url->base);

            // Данные о залогинином пользователе в сессию
            $user_data = new Zend_Session_Namespace('user_data');
            Zend_Registry::set('user_data', $user_data);

            // Автозапуск для плагинах
            require $cnf->path->root . 'cms/system/pluginsAutorun.php';

            site::setCheckPoint('Plugins autorun');

            $controller_dirs = array();
            
            $site_plugins = glob( $cnf->path->site . "plugins/*/controllers", GLOB_ONLYDIR );
           
            if (!$site_plugins)
                $site_plugins = array();

            $base_plugins = glob( $cnf->path->root . "plugins/*/controllers", GLOB_ONLYDIR );
            
            if (!$base_plugins)
                $base_plugins = array();

            $plugins = array_merge($base_plugins, $site_plugins);

            foreach ($plugins as $plugin) {
                $folders = explode("/", $plugin);
                $controller_dirs[$folders[sizeof($folders) - 2]] = $plugin . '/';
            }

            $front->setDefaultModule($cnf->default_plugin);

            site::setCheckPoint('Kernel Run finish');

            Zend_Controller_Front::run($controller_dirs);
        } catch (Exception $e) {
            require_once $config['path']['system'].'CMSError.php';
            CMSError::catchException($e);
        }
    }

    static function linkRoutes() {
        
        if (!file_exists("cache/routes.ser")) {
            
            $main = [];
            // Подключение путей CMS для контроллеров
                    
            if (file_exists(self::$config->path->settings . 'routes.php')) {
                $main[] = self::$config->path->settings . 'routes.php';
            }

            site::setCheckPoint('Link main routes');
            
            // Подключение путей Сайта для контроллеров
            if (file_exists(self::$config->path->site . 'system/routes.php')) {
                $main[] = self::$config->path->site . 'system/routes.php';
            }

            site::setCheckPoint('Link site routes');

            $root_plugin_routes = glob(self::$config->path->root . 'plugins/*/system/routes.php');
            $site_plugin_routes = glob(self::$config->path->site . 'plugins/*/system/routes.php');

            $plugin_routes = array_merge(
                    $main,
                    (($root_plugin_routes) ? $root_plugin_routes : array()), (($site_plugin_routes) ? $site_plugin_routes : array())
                    
            );

            if (!is_dir('cache')) mkdir('cache',0777,true);
            
            file_put_contents('cache/routes.ser', serialize($plugin_routes));
            
        }else $plugin_routes = unserialize(file_get_contents('cache/routes.ser'));
        
        
        site::setCheckPoint('Search for plugin routes');


        if ($plugin_routes) {
            foreach ($plugin_routes as $r) {
                require($r);
            }
        }
        
        site::setCheckPoint('Link plugin routes');
    }

    /**
     * @desc Устанавливаем адаптер БД
     */
    public static function setDbAdapter() {

        $cnf = Zend_Registry::get('cnf');

        if ((isset($cnf->usedb->on) && $cnf->usedb->on) || (!isset($cnf->usedb->on))) {
            $db = Zend_Db::factory($cnf->db);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);
        }
    }

    /**
     * @desc Получение родительского плагина, от которого наследован текущий
     * @return array
     */
    public static function getParentPlugin() {
        return site::getParentPlugin();
    }

    /**
     * @desc Функция получает шаблон для текущей страницы
     * @return string
     */
    public static function getContent() {
        return site::getContent();   /////// функция здесь оставлена для совместимости
    }

    /**
     * @desc Проверяет установлена ли библиотека расширения Zend
     * @global type $root
     * @throws Exception
     */
    static function checkLibraryPresent() {
        global $root, $config;
        if (!file_exists('library/Zend/Loader/Autoloader.php'))
            throw new Exception("Необходимо установить дополнительные библиотеки в /library", 666);
        require_once 'Zend/Loader/Autoloader.php';
    }

    /**
     * @desc Активириует перенос основных переменных конфига в среду JavaScript
     */
    static function enabledJsVarTransport() {
        site::enabledJsVarTransport();
    }

}
