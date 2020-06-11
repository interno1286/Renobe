<?php

require_once 'Zend/View/Interface.php';
require_once 'Smarty/Smarty.class.php';

class SmartyView implements Zend_View_Interface {

    /**
     * Smarty
     * @var Smarty
     */
    protected $_smarty;

    /**
     *
     * @param string $tmplPath
     * @param array $extraParams
     * @return void
     */
    public function __construct($tmplPath = null, $extraParams = array()) {

        $this->_smarty = new Smarty;

        if (null !== $tmplPath) {
            $this->setScriptPath($tmplPath);
        }

        foreach ($extraParams as $key => $value) {
            $this->_smarty->$key = $value;
        }

        $this->initPlugins();
    }

    function initPlugins() {
        $config = Zend_Registry::get('cnf');
        $dirs = glob($config->path->root . 'plugins/*/smartyPlugins', GLOB_ONLYDIR);

        $site_plugins_dirs = glob($config->path->site . 'plugins/*/smartyPlugins', GLOB_ONLYDIR);

        $dirs = array_merge(
                ($dirs) ? $dirs : array(), ($site_plugins_dirs) ? $site_plugins_dirs : array()
        );

        $dirs[] = $config->path->root . 'cms/smartyPlugins';
        $dirs[] = $config->path->libs . 'Smarty/plugins';
        $this->_smarty->plugins_dir = $dirs;
    }

    /**
     *
     * @return Smarty
     */
    public function getEngine() {
        return $this->_smarty;
    }

    /**
     * @param string $path
     * @return void
     */
    public function setScriptPath($template_dir, $compile_dir = '') {

        if ($compile_dir === '') {
            $compile_dir = $template_dir . 'template_c';
        }

        $compile_dir = 'template_c';

        if (is_readable($template_dir)) {
            $this->_smarty->template_dir = $template_dir;
            if (is_readable($compile_dir)) {
                $this->_smarty->compile_dir = $compile_dir;
                return;
            }
        }

        throw new Exception('Invalid path provided');
    }

    /**
     * Для совместимости с Zend_Interface start
     */
    public function addScriptPath($path) {
        
    }

    public function setBasePath($path, $classPrefix = 'Zend_View') {
        
    }

    public function addBasePath($path, $classPrefix = 'Zend_View') {
        
    }

    /**
     * Для совместимости с Zend_Interface finish
     */
    public function getScriptPaths() {
        return $this->_smarty->template_dir;
    }

    /**
     * Установка переменных шаблона смарти
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val) {
        $this->_smarty->assign($key, $val);
        //echo $key;
    }

    /**
     * Для совместимости
     * @param type $key
     * @return type
     */
    public function get_template_vars($key) {
        return $this->_smarty->getTemplateVars($key);
    }

    /**
     * Получить переменную шаблона смарти
     *
     * @param string $key The variable name.
     * @return mixed The variable value.
     */
    public function __get($key) {
        return $this->_smarty->getTemplateVars($key);
    }

    public function __isset($key) {
        return (null !== $this->_smarty->getTemplateVars($key));
    }

    public function __unset($key) {
        $this->_smarty->clear_assign($key);
    }

    public function assign($spec, $value = null) {
        //echo $spec . '=' .  $value;
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }

        $this->_smarty->assign($spec, $value);
    }

    public function clearVars() {
        $this->_smarty->clear_all_assign();
    }

    /**
     * @param string $name Имя файла  шаблона
     * @return string отрендереный шаблон
     */
    public function render($name) {

        $config = Zend_Registry::get('cnf');

        $content = $this->_smarty->fetch($name);

        if ($name == 'index.tpl' && !site::isAjax() && !site::isPost() && site::$ajaxView == false) {
            site::includePostfixData($config, $content);
        }
        //die (site::$autoHeadScripts);
        if (site::$autoHeadScripts)
            site::includeHeadElements($this->_smarty, $content);

        return site::addDebugTplMarker($config, $name, $content);
    }

    public function addStyle($style, $priority = 'low') {

        $present = (array) $this->__get("styles");

        if (is_array($style))
            foreach ($style as $value)
                $new[$value] = $value;
        else
            $new = array($style => $style);

        if (isset($new) && is_array($new)) {

            if ($priority == 'high')
                $new = array_merge($present, $new);
            else
                $new = array_merge($new, $present);

            $this->__set("styles", $new);
        }
    }

    public function addScript($script, $priority = 'high') {

        $present = (array) $this->__get("scripts");

        if (is_array($script)) {
            foreach ($script as $value)
                $new[$value] = $value;    //путь до скрипта в качестве ключа использован для унификации массива %)
        } else
            $new = array($script => $script);

        if (isset($new) && is_array($new)) {

            if ($priority == 'high')
                $new = array_merge($present, $new);
            else
                $new = array_merge($new, $present);

            $this->__set("scripts", $new);
        }
    }

    public function delScript($script) {

        $present = (array) $this->__get("scripts");

        $new = array();

        foreach ($present as $s) {
            if (strpos($s, $script) === false)
                $new[$s] = $s;
        }

        $this->__set("scripts", $new);
    }

    public function delStyle($style) {

        $present = (array) $this->__get("styles");

        $new = array();

        foreach ($present as $s) {
            if (strpos($s, $style) === false)
                $new[$s] = $s;
        }

        $this->__set("styles", $new);
    }

    public function addMetaKeywords($words) {
        $present = $this->__get("meta_keywords");
        $this->__set("meta_keywords", ($present . ($present) ? ',' : '') . $words);
    }

    public function setMetaDescription($desc) {
        $this->__set("meta_description", $desc);
    }

    public function addTitle($title) {
        $present = $this->__get("title");
        $this->__set("title", $present . ' :: ' . $title);
    }

    public function setTitle($title) {
        $this->__set("title", $title);
    }

    public function addHeadElement($element) {
        $f = explode_filename($element);
        switch ($f["ext"]) {
            case "css":
                $this->addStyle($element);
                break;

            case "js":
                $this->addScript($element);
                break;
        }
    }

    public function display($template) {
        site::setCheckPoint('Smarty Display Begin');
        $this->_smarty->display($template);
        site::setCheckPoint('Smarty Display End');
    }

    public function register_object($var, $obj) {
        $this->_smarty->register_object($var, $obj);
    }

    public function assign_by_ref($var, $obj) {
        $this->_smarty->assign_by_ref($var, $obj);
    }

    function _fetch($template) {
        return $this->_smarty->fetch($template);
    }

    function __call($method, $params) {
        $this->_smarty->$method($params);
    }

}
