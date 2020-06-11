<?php

/**
 * @desc Основной контроллер, отец всех контроллеров
 */
class CmsBaseController extends Zend_Controller_Action {

    public $view;
    public $controller;
    public $action;
    public $module;
    protected $model;
    public $params;
    public $user_data;
    public $edit_allowed = false;
    public $plugin_root_path;
    public $browser;
    public $browser_version;

    function init() {
        global $config;
        site::setCheckPoint('CmsBase Init');

        $this->view = Zend_Registry::get('view');
        $this->config = Zend_Registry::get('cnf');
        $this->view->conf = $config;

        // Получаем наименования модуля, контроллера и экшна
        $this->module = $this->getRequest()->getModuleName();
        $this->controller = $this->getRequest()->getControllerName();

        if (!$this->controller)
            $this->controller = 'index';

        $this->action = $this->getRequest()->getActionName();
        
        if (!$this->action)
            $this->action = 'index';

        $this->params = $this->_getAllParams();
        $this->user_data = Zend_Registry::get('user_data');

        $this->user_data->path = array();

        $this->initPluginRootPath();  ////// определение текущего пути плагина и выставление соответствующих переменных
        
        site::setCheckPoint('Pre Init Site');
        
        $this->initCurrentSite(); //////  Инициализация сайта
        site::setCheckPoint('Init site finish');
        
        $this->initViewVars();   ///////  передача служебных переменных в среду Smarty

        $this->adminInit();        //////// инициализация админских полномочий

        $this->initModel();         /////// инициализация модели

        $this->view->assign('db', $this->model); //// assign модели 

        $this->connectFrameWorks();  ///// На основе конфига подключает или нет jquery, bootstrap итд

        site::setCheckPoint('Init Controller');
        $this->initController();   ////// инициализация текущего контроллера
        site::setCheckPoint('Init Controller finish');
    }

    function initController() {
        
    }

    function getFCGIGetParams() {
        //if (php_sapi_name()=='cgi-fcgi') {
            $url = $_SERVER['REQUEST_URI'];
            
            $rs = strpos($url, '?');
            
            if ($rs!==false)  {
                $pu = substr($url, $rs+1);
                parse_str($pu, $output);
                if (sizeof($output)) {
                    foreach ($output as $k=>$v) {
                        $this->params[$k] = $v;
                    }
                    
                    $this->view->params = $this->params;
                    
                }
            }
        //}
    }
    
    /**
     * @desc возвращает true в случае если запрос является AJAX запросом
     * @return type
     */
    function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * @desc Возвращает true в случае если запрос ыл сделан через POST
     * @return type
     */
    function isPost() {
        return $this->getRequest()->isPost();
    }

    /**
     * @desc выполняется перед выполнением экшна
     */
    function preDispatch() {
        if (!isset($this->redirCount)) $this->redirCount=0;
        
        $this->redirCount++;
        
        if ($this->redirCount>10) throw new Exception('To many redirect from predispatch');
        
        if ($this->action != 'accessdenied' && $this->action != 'jsonerror') {
            $this->checkRights();
        }

        site::setCheckPoint('Pre Dispatch');
    }

    function checkRights() {
        return true;
    }

    function accessdeniedAction() {
        $this->user_data->last_url = $_SERVER['REQUEST_URI'];
        $this->_redirect('/auth?r='.$_SERVER['REQUEST_URI']);
    }

    /**
     * @desc устанавливает скин для отображения
     * @param type $skin
     */
    function setSkin($skin = "default") {

        ////// Сначала подчищаем стили и скрипты добавленные в предыдущий скин

        $this->delSkinScripts();
        $this->delSkinStyles();

        //////
        if (!is_dir($this->config->path->site . 'skins/' . $skin)) {
            $this->view->error = 'не найден скин ' . $skin . ' автоматически установлен скин default';
            $skin = 'default';
        }
        $this->user_data->template = $skin;

        ////// Переписываем переменные в конфиге

        $config = $this->config->toArray();

        $config['skin'] = $skin;
        $config["path"]["views"] = $config['path']['site'] . "skins/{$config["skin"]}/views/";
        $config["path"]["skin"] = $config['path']['site'] . "skins/{$config["skin"]}/";
        $config["url"]["skin"] = $config['url']['site'] . "skins/{$config["skin"]}/";

        $config = new Zend_Config($config, true);
        Zend_Registry::set('cnf', $config);
        $this->config = $config;
        $this->view->config = $config;
        $this->view->setScriptPath($config->path->views);

        ////// Подключаем скрипты и стили

        $this->linkSkinScripts();
        $this->linkSkinStyles();
    }

    /**
     * @desc Функция инициализации модели
     */
    function initModel() {
        
    }

    /**
     * @desc выполняется для дополнительных админских действий
     */
    protected function adminInit() {
        $this->edit_allowed = false;

        if ($this->user_data->role == 'admin') {
            $this->view->addScript($this->config->url->base . 'cms/public/js/ajaxDialog.js', 'low');
            $this->edit_allowed = true;
        };

        $this->view->edit_allowed = $this->edit_allowed;
    }

    /**
     * @desc передаёт в смарти основные переменные
     * @global type $root
     */
    protected function initViewVars() {
        global $root;
        $this->view->controller = $this->controller;
        $this->view->action = $this->action;
        $this->view->module = $this->plugin = $this->module;
        $this->view->plugin = $this->plugin;
        $this->view->params = $this->params;
        $this->view->config = $this->config;

        if ($this->plugin != 'skineditor') {
            $this->user_data->current_controller = $this->controller;
            $this->user_data->current_action = $this->action;
            $this->user_data->current_plugin = $this->plugin;
        }

        $this->view->user_data = $this->user_data;
        $this->view->root = $root;

        $this->LinkHeadElements();
    }

    protected function connectFrameWorks() {

        if ($this->config->bootstrap4)
            site::initBootStrap4($this->view);
        
        if ($this->config->bootstrap3)
            $this->initBootStrap3();

        if ($this->config->bootstrap)
            $this->initBootStrap();

        if ($this->config->jqueryUI)
            $this->initJqueryUI();

        if ($this->config->jquery)
            $this->initJquery();

        if ($this->config->mainJS)
            $this->initMainJs();
    }

    function initPluginRootPath() {
        $this->plugin_root_path = site::getPluginRootPath();

        $this->view->plugin_root_path = $this->plugin_root_path;

        $this->plugin_root_url = str_replace($this->config->path->root, $this->config->url->base, $this->plugin_root_path);
        $this->view->plugin_root_url = $this->plugin_root_url;
    }

    protected function LinkHeadElements() {
        $this->linkPluginsScripts();
        $this->linkPluginsStyles();
        $this->linkSkinStyles();
        $this->linkSkinScripts();
    }

    function linkSkinScripts() {

        if ($this->config->skin) {
            $files = glob($this->config->path->skin . "public/js/controller/{$this->module}/{$this->controller}/{$this->action}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->addScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');

            $files = glob($this->config->path->skin . "public/js/controller/{$this->module}/{$this->controller}/*.js");
            
            if ($files)
                foreach ($files as $f)
                    $this->view->addScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');

            $files = glob($this->config->path->skin . "public/js/controller/{$this->module}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->addScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');



            $files = glob($this->config->path->skin . "public/js/{$this->module}/{$this->controller}/{$this->action}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->addScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');

            $files = glob($this->config->path->skin . "public/js/{$this->module}/{$this->controller}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->addScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');

            $files = glob($this->config->path->skin . "public/js/{$this->module}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->addScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');
        }
    }

    function linkSkinStyles() {

        if ($this->config->skin) {

            $files = glob($this->config->path->skin . "public/css/{$this->module}/{$this->controller}/{$this->action}/*.css");
            if ($files)
                foreach ($files as $f)
                    $this->view->addStyle(str_replace($this->config->path->root, $this->config->url->base, $f));

            $files = glob($this->config->path->skin . "public/css/{$this->module}/{$this->controller}/*.css");
            if ($files)
                foreach ($files as $f)
                    $this->view->addStyle(str_replace($this->config->path->root, $this->config->url->base, $f));
        }
    }

    function delSkinScripts() {

        if ($this->config->skin) {
            $files = glob($this->config->path->skin . "public/js/controller/{$this->module}/{$this->controller}/{$this->action}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->delScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');

            $files = glob($this->config->path->skin . "public/js/controller/{$this->module}/{$this->controller}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->delScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');

            $files = glob($this->config->path->skin . "public/js/controller/{$this->module}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->delScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');



            $files = glob($this->config->path->skin . "public/js/{$this->module}/{$this->controller}/{$this->action}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->delScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');

            $files = glob($this->config->path->skin . "public/js/{$this->module}/{$this->controller}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->delScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');

            $files = glob($this->config->path->skin . "public/js/{$this->module}/*.js");
            if ($files)
                foreach ($files as $f)
                    $this->view->delScript(str_replace($this->config->path->site, $this->config->url->site, $f), 'low');
        }
    }

    function delSkinStyles() {

        if ($this->config->skin) {

            $files = glob($this->config->path->skin . "public/css/{$this->module}/{$this->controller}/{$this->action}/*.css");
            if ($files)
                foreach ($files as $f)
                    $this->view->delStyle(str_replace($this->config->path->root, $this->config->url->base, $f));

            $files = glob($this->config->path->skin . "public/css/{$this->module}/{$this->controller}/*.css");
            if ($files)
                foreach ($files as $f)
                    $this->view->delStyle(str_replace($this->config->path->root, $this->config->url->base, $f));
        }
    }

    function linkPluginsScripts() {

        $files = glob($this->plugin_root_path . "public/js/controller/{$this->controller}/{$this->action}/*.js");
        if ($files)
            foreach ($files as $f)
                $this->view->addScript(str_replace($this->config->path->root, $this->config->url->base, $f), 'high');

        $files = glob($this->plugin_root_path . "public/js/controller/{$this->controller}/*.js");
        if ($files)
            foreach ($files as $f)
                $this->view->addScript(str_replace($this->config->path->root, $this->config->url->base, $f), 'high');
    }

    function linkPluginsStyles() {
        $files = glob($this->config->path->root . "plugins/{$this->module}/public/css/controller/{$this->controller}/{$this->action}/*.css");
        if ($files)
            foreach ($files as $f)
                $this->view->addStyle(str_replace($this->config->path->root, $this->config->url->base, $f));

        $files = glob($this->config->path->root . "plugins/{$this->module}/public/css/controller/{$this->controller}/*.css");
        if ($files)
            foreach ($files as $f)
                $this->view->addStyle(str_replace($this->config->path->root, $this->config->url->base, $f));

        $files = glob($this->config->path->site . "plugins/{$this->module}/public/css/controller/{$this->controller}/*.css");
        if ($files)
            foreach ($files as $f)
                $this->view->addStyle(str_replace($this->config->path->site, $this->config->url->site, $f));

        $files = glob($this->config->path->site . "plugins/{$this->module}/public/css/controller/{$this->controller}/{$this->action}/*.css");
        if ($files)
            foreach ($files as $f)
                $this->view->addStyle(str_replace($this->config->path->site, $this->config->url->site, $f));
    }

    public function postDispatch() {
        
        site::setCheckPoint('Post Dispatch');
        
        if ($this->config->debug->on) {
            global $start_time;
            $finish_time = microtime(true);
            $total = $finish_time - $start_time;
            $this->view->execution_time = 'Время выполнения ' . round($total, 4) . 'c.';
        } else
            $this->view->execution_time = '';

    }

    public function doRedirect($url) {
        $this->_redirect($url);
    }

    public function getAjaxView($folder = 'ajax') {
        if (is_dir($this->config->path->site . 'skins/ajax'))
            $this->view->setScriptPath($this->config->path->site . 'skins/ajax', $this->config->path->site . 'skins/ajax/template_c');
        else
            $this->view->setScriptPath($this->config->path->views . $folder, $this->config->path->views . $folder . '/template_c');

        site::$ajaxView = true;
    }
    
    function ajax() {
        $this->getAjaxView('ajax');
    }
    
    public function useAjaxView($folder = 'ajax') {
        $this->getAjaxView($folder);
    }

    public function initMainJs() {
        $this->view->addScript($this->config->url->base . 'cms/public/js/main.js', 'high');
        $this->view->addStyle($this->config->url->base . 'cms/public/css/main.css');
    }

    public function initBootStrap() {
        $this->view->addScript($this->config->url->base . 'cms/public/bootstrap/js/bootstrap.min.js', 'low');
        $this->view->addScript($this->config->url->base . 'cms/public/js/bsDialog.js', 'low');
        $this->view->addScript($this->config->url->base . 'cms/public/bootstrap/js/bootstrap-datepicker.js', 'high');

        $this->view->addStyle($this->config->url->base . 'cms/public/bootstrap/css/bootstrap.min.css', 'low');
        $this->view->addStyle($this->config->url->base . 'cms/public/bootstrap/css/datepicker.css', 'high');
    }

    public function initBootStrap3() {
        site::initBootStrap3($this->view);
    }

    public function initJquery() {
        site::initJquery($this->view);
    }

    public function initJqueryUI($theme = "smoothness") {
        site::initJqueryUI($this->view, $theme);
    }

    public function initCurrentSite() {
        
    }

    /**
     * @desc Логер, записывает любое событие в текстовый лог файл
     * @param type $message
     */
    function logger($message) {
        if ($this->config->debug->on) {
            $f = fopen($this->config->path->root . "log.txt", "a+");

            fwrite($f, date("r: ") . $message . "\n");

            fclose($f);
        }
    }

    function renderTplToContent($tpl) {
        $this->view->content = site::renderTpl($tpl);
    }

    /**
     * @desc Функция, рендерит тпл, автоматически выбирая откуда её брать
     * @param type $tpl
     * @return type
     */
    function renderTpl($tpl) {
        return site::renderTpl($tpl);
    }

    /**
     * @desc Функция для редактирования любого типового объекта,
     * @desc Вызывает стандартную процедуру редактирования
     * @desc объекта через диалоговое окно
     * @desc при использовании этой схемы редактирования
     * @desc обязательно добавлять редактируемый объект(типа /module/controller/seedit<object_name>) в контроль прав. !<object_name> первая буква большая!
     */
    function seeditobjectAction() {
        $this->getAjaxView();

        $object = preg_replace('#([^a-z0-9-_])#', '', $this->params['object']);

        $action = 'seedit' . $object;  // stedit - Standart Edit

        if (class_exists('acl')) {
            $acl = acl::getInstance();
            $code = "/{$this->module}/{$this->controller}/$action";

            $read_access = $acl->canAccess($code);
            $edit_access = $acl->canEdit($code);
        } else {
            $read_access = $edit_access = $this->edit_allowed;
        }


        try {

            $error = '';

            if (!$read_access)
                throw new Exception("В доступе к $code отказано!");

            if ($this->getRequest()->isPost()) {

                if (!$edit_access)
                    throw new Exception("В доступе к $code отказано!");

                $action = 'seedit' . ucfirst($object);  // seedit - Standart Edit

                if (method_exists($this, $action))
                    $this->$action($this->params);

                if (method_exists($this->model, $action))
                    $this->model->$action($this->params);

                $action = 'sefinishedit' . ucfirst($object);  // seedit - Standart Edit

                if (method_exists($this, $action))
                    $this->$action();
            }else {
                $method = 'segetdata' . ucfirst($object);

                if (method_exists($this->model, $method))
                    $this->view->data = $this->model->$method($this->params);

                if (method_exists($this, $method))
                    $this->$method($this->params);

                $this->renderTplToContent($this->plugin_root_path . 'views/seedit/' . $object . '.tpl');
                return true;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $this->view->content = ($this->getRequest()->isPost() && ($this->isAjaxRequest() || strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== false)) ? Zend_Json::encode(array('error' => $error, 'finish' => true)) : $error;
    }

    function detectBrowser() {
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $version = 1;
        switch (true) {
            case (strpos($ua, 'MSIE') !== false):
                $browser = 'IE';
                break;

            case (strpos($ua, 'Firefox') !== false):
                $browser = 'Firefox';
                break;

            case (strpos($ua, 'Opera') !== false):
                $browser = 'Opera';
                break;

            case (strpos($ua, 'Chrome') !== false):
                $browser = 'Chrome';
                break;
            case (strpos($ua, 'Safari') !== false):
                $browser = 'Safari';
                break;

            default:
                $browser = $ua;
                break;
        }

        $this->browser = $browser;
        $this->browser_version = $version;

        $this->view->browser = $this->browser;
        $this->view->browser_version = $this->browser_version;
    }

    /**
     * @desc отключает шаблонизатор и включает обычный вывод
     */
    public function disableLayout() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function authAction() {
        $i = $this->config->path->root.'init';
        
        if (!tools_skin::hasContent())
            $this->setSkin('simple');
        
        if (file_exists($i) && file_get_contents($i)==='1') {
            
            $this->user_data->role = 'admin';
            
            $this->user_data->user_type = 'admin';
            
            $this->user_data->logged = true;
            
            if (isset($this->params['r']))
                $this->view->redirect = $this->params['r'];
            
            $this->renderTplToContent($this->config->path->root . 'cms/views/simpleAuth.tpl');
            
            unlink($i);
            
        }else $this->simpleAuth();
        
    }

    /**
     * @desc Функция для простейшей авторизации, может быть вызвана в произвольном экшне любого контроллера, что превратит его в экшн авторизации
     * @param type $tpl - опционально, TPL с формой авторизации
    /**/
    public function simpleAuth($tpl = false) {
        
        if (!$tpl && file_exists($this->config->path->skin.'views/simpleAuth.tpl'))
                $tpl = $this->config->path->skin.'views/simpleAuth.tpl';
        
        $tpl = ($tpl) ? $tpl : $this->config->path->root . 'cms/views/simpleAuth.tpl';

        if (isset($this->params['r']))
            $this->view->redirect = $this->params['r'];

        if ($this->getRequest()->isPost()) {

            if (($this->params['user'] == $this->config->admin_login && $this->params['password'] == $this->config->admin_password) || $this->user_data->logged) {
                $this->user_data->role = 'admin';
                $this->user_data->user_type = 'admin';
                $this->user_data->logged = true;
            } else
                $this->view->error = "Неверно введены логин или пароль!";
        }
        if (!tools_skin::hasContent())
            $this->setSkin('simple');
        
        $this->renderTplToContent($tpl);

    }

    /**
     * @desc проверяет наличие прав админа и редиректит в эксес денайд в случае неудачи
     */
    function needAdminRights() {
        $admin_role = ($this->config->admin_role) ? $this->config->admin_role : 'admin';

        if ($this->user_data->role != $admin_role) {
            //throw new Exception('Access Denied');
            if (!in_array($this->action, ['accessdenied','auth']))
                $this->_forward('accessdenied');
        }
    }

    function jsonerrorAction() {
        $this->useAjaxView();
        $this->view->content = Zend_Json::encode(array('error' => 'authorization failed'));
    }

    function grantAccess() {
        $this->view->access_denied = $this->access_denied = false;
        $this->view->edit_allowed = $this->edit_allowed = false;
    }

    function goBack() {
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }

    function goMain() {
        $this->_redirect('/');
    }

    function setJsonAnswer() {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
    }
    
    function jsonAnswer($array) {
        $this->setJsonAnswer();
        
        $this->view->content = Zend_Json::encode($array);
    }

    function showError($err) {
        $this->user_data->result = false;
        $this->user_data->last_error = $err;
    }

    function installAction() {
        $this->needAdminRights();

        $plugin_name = preg_replace("#([^a-z0-9-])#ui", '', $this->params['name']);

        if (!$plugin_name)
            throw new Exception('invalid plugin name');


        mkdir($this->config->path->root . "plugins/$plugin_name");

        tools_zip::unzip($this->config->path->root . "temp/{$plugin_name}.zip", $this->config->path->root . "plugins/$plugin_name/");

        unlink($this->config->path->root . "temp/{$plugin_name}.zip");

        $this->_redirect("/{$plugin_name}");
    }

    function manageAction() {
        $this->needAdminRights();
        $this->useAjaxView();
        $this->renderTplToContent('manage.tpl');
    }
}
