<?php

class DbModel {

    protected $db;
    protected $id = false;
    protected $config;
    static protected $transactionStarted = false;
    protected $user_data;
    protected $items_per_page = 10;
    protected $page_expr;
    protected $current_page = 1;
    protected $plugin;
    protected $cache_time = 3600;
    protected $cache_on = false;
    public $last_error = '';
    public $last_id = '';


    /**
     * @desc Контрольная таблица, для проверки наличия необходимой структуры таблиц в БД
     * @desc В случае отсутствия таблицы запускаеся $this->install();
     */
    public $controll_table=null; ///// это старообрядское название, сейчас название $table
    public $table=null;          ///// Основная таблица модели указывается для проверки и автоматическорй установки в случае отсутствия
    public $schema='public';     ///// Рабочая схема модели - указывается для проверки и автоматическорй установки в случае отсутствия

    function __construct($id = false) {
        $this->setId($id);
        $this->config = Zend_Registry::get('cnf');

        try {
            $this->user_data = Zend_Registry::get('user_data');
        } catch (Exception $e) {
            $this->user_data = array();
        }

        $this->initDB();
        $this->initModel();
        $this->checkInstalled();
    }

    function checkInstalled() {
        if (!$this->db) return false;
        
        $table = (isset($this->table)) ? $this->table : $this->controll_table;
        if ($table && $this->config->debug->on) {
            if (!$this->checkTableExists($table))
                $this->install();
        }
    }

    /**
     * @desc проверяет есть ли таблица и всё что угодно ещё
     * @param string $table 
     */
    function checkTableExists($table) {
        if ($this->config->db->adapter == 'PDO_PGSQL') {
            $sql = "
                SELECT EXISTS(
                    SELECT 1 
                    FROM   pg_catalog.pg_class c
                    JOIN   pg_catalog.pg_namespace n ON n.oid = c.relnamespace
                    WHERE  n.nspname = '".substr($this->getScheme(),0,strlen($this->getScheme())-1)."'
                    AND    c.relname = '$table'
                    AND    c.relkind = 'r'
            )";

            return (bool) $this->s_fetchOne($sql);
        }else if ($this->config->db->adapter == 'PDO_MYSQL') {
            
            $this->pq('query', 'use information_schema');
            
            $sql = "
                select
                    ENGINE
                from
                    TABLES
                where
                    TABLE_SCHEMA='{$this->config->db->params->dbname}'
                and
                    TABLE_NAME='{$this->table}'
            ";   
                    
            $res = $this->s_fetchOne($sql);
            
            $this->pq('query', 'use '.$this->config->db->params->dbname);
            
            return (bool) $res;
        }

        
    }

    function getScheme() {
        
        if ($this->config->db->adapter == 'PDO_PGSQL') {
            $schema = ($this->schema) ? $this->schema : 'public';
            $schema = $schema.'.';
        }else 
            $schema = '';
        
        return $schema;
    }

    /**
     * @desc запуск инсталляционных скриптов
     */
    function install() {

        try {
            $current_plugin = site::getCurrentModule();
            $parent_plugin = site::getParentPlugin();

            if (is_array($parent_plugin))
                $parent_plugin = $parent_plugin[0];
        } catch (Exception $e) {
            $current_plugin = $this->plugin;
        }

        if ($current_plugin) {
            $sql_file1 = $this->config->path->root . "plugins/$current_plugin/contrib/script.sql";
            $sql_file2 = $this->config->path->root . "plugins/$parent_plugin/contrib/script.sql";


            $sql_file = (file_exists($sql_file1)) ? $sql_file1 : $sql_file2;

            if (file_exists($sql_file)) {

                $expressions = explode(';', file_get_contents($sql_file));

                foreach ($expressions as $e)
                    $this->pq('query', $e);
            }
        }
    }

    function initModel() {
        
    }

    function initDB() {

        try {
            $db = Zend_Registry::get('db');
        } catch (Exception $e) {
            
            if ($this->config->db->params->host=='host_val' || ($this->config->usedb->on===false)) {
                $db = false;
            }else {
                $db = Zend_Db::factory($this->config->db);
                Zend_Db_Table_Abstract::setDefaultAdapter($db);
            }
            
            Zend_Registry::set('db',$db);
        }

        $this->db = $db;
    }
    
    
    function reInitDB() {
        $db = Zend_Db::factory($this->config->db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set('db',$db);
        $this->db = $db;
    }
    
    function hasConnect() {
        try {
            if (!$this->db) return false;
            
            $result = ($this->db->getConnection());
            $result = ($result instanceof PDO);
        }catch (Exception $e) {
            $result = false;
        }
        
        return $result;
    }
    

    public function beginTransaction() {
        if (!self::$transactionStarted) {
            $this->db->beginTransaction();
        }
        self::$transactionStarted = true;
    }

    public function rollbackTransaction() {
        if (self::$transactionStarted) {
            $this->db->rollBack();
            self::$transactionStarted = false;
        }
    }

    public function commitTransaction() {
        if (self::$transactionStarted) {
            $this->db->commit();
            self::$transactionStarted = false;
        }
    }

    public function setId($id = false) {
        $this->id = ($id !== false) ? intval($id) : false;
    }
    
    
    
    function profileStart($query) {
        if (isset($this->config->debug->profiling) && $this->config->debug->profiling) {
            $hash = md5($query);
            $this->profile[$hash]['start'] = microtime(true);
            $this->profile[$hash]['sql'] = $query;
            
        }
    }
    
    function profileEnd($query) {
        if (isset($this->config->debug->profiling) && $this->config->debug->profiling) {
            $hash = md5($query);
            $this->profile[$hash]['end'] = microtime(true);
            
            $total = (float)$this->profile[$hash]['end'] - (float)$this->profile[$hash]['start'];
            
            $text = "
                SQL:
                {$this->profile[$hash]['sql']}
                    
                ==============================
                Total execution time: {$total}ms
            ";
            
            file_put_contents("{$this->config->path->root}temp/{$total}_{$hash}.txt", $text);
        }
        
    }
    
    
    function profileFail($query) {
        if (isset($this->config->debug->profiling) && $this->config->debug->profiling) {
            $hash = md5($query);
            $this->profile[$hash] = null;
        }        
    }
    
    
    /**
     * @param $type: string (UPDATE/INSERT/DELETE/QUERY) Тип запроса
     * @param $table: string имя таблицы
     * @param $data: array массив с данными для запроса (UPDATE/INSERT) либо условие в случае DELETE
     * @param $expression=false: Условие для запросов (UPDATE/DELETE)
     * @param $error="": здесь будет текст ошибки
     * @param $id=0: При type=insert тут будет ID вставленной записи корректно вернёт только в том случае если поле ID у таблицы зовётся именно "id"
     * @param $sequence_name: 
     * @desc Функция выполнения запросов INSERT / UPDATE / DELETE с последующей обработкой ошибок
     * @return Пустую строку при успешном выполнении/либо строку с ошибкой (при включённом дебаге
     * @return либо человекопонятное сообщение об ошибке при выключенном дебаге
     */
    public function pq($type, $table, $data = false, $expression = false, &$error = "", &$id = 'undefined', $sequence_name = null) {
        $error = "";
        
        if (!$this->hasConnect()) {
            $this->last_error = 'no connection';
            return false;
        }
        
        if (!isset($sequence_name) && isset($this->sequence_name))
            $sequence_name = $this->sequence_name;
        
        try {
            $this->profileStart($type.' '.$table.' '.print_r($data,1).' where '.$expression);
            
            switch ($type) {
                case "insert":
                    $this->db->insert($table, $data);
                    //if ($id !== 'undefined') {
                        try {

                            if ($sequence_name && $this->config->db->adapter == 'PDO_PGSQL') {
                                $id = $this->db->lastSequenceId($sequence_name);
                            } else {

                                if ($this->config->db->adapter == 'PDO_PGSQL')
                                    $id = $this->db->lastInsertId($table . '_id');
                                else
                                    $id = $this->db->lastInsertId();
                            }
                        } catch (Exception $e) {
                            $id = false;
                        };
                        $this->last_id = $id;
                    //}
                    break;
                case "update":
                    $this->db->update($table, $data, $expression);
                    break;
                case "delete":
                    $this->db->delete($table, $data);
                    break;

                case "query":
                    $this->db->query($table);
                    break;
                default:
                    throw new Exception("Неверный тип запроса!");
                    break;
            }
            
            
            $this->profileEnd($type.' '.$table.' '.print_r($data,1).' where '.$expression);
            return true;
        } catch (Exception $e) {
            $this->profileFail($type.' '.$table.' '.print_r($data,1).' where '.$expression);
            $found = 0;
            
            //$error = ($this->config->debug->on) ? $e->getMessage() : $this->config->debug->message;
            $error = $e->getMessage();
            $this->last_error = $error;
            
            if ($found>2)
                errorReport($e, get_defined_vars());
            
            return false;
        }
    }

    /**
     * @desc SAFE FETCH ALL
     * @param $query string Запрос
     * @param &$error string Текст ошибки
     */
    public function s_fetchAll($query, &$error = "") {
        try {
            if (!$this->db) return [];
            
            $this->profileStart($query);
            if ($this->cache_on) {

                $hash = md5($query);

                $cached_value = tools_cache::get('sql_' . $hash);

                if ($cached_value !== '!!nocache!!' && $cached_value) {
                    $data = $cached_value;
                } else {
                    $data = $this->db->fetchAll($query);
                    tools_cache::save('sql_' . $hash, $data, $this->cache_time);
                }
            } else
                $data = $this->db->fetchAll($query);
            
            $this->profileEnd($query);
            return $data;
        } catch (Exception $e) {
            $this->profileFail($query);
            $error = ($this->config->debug->on) ? $e->getMessage() : $this->config->debug->message;
            $this->last_error = $error;
            errorReport($e, get_defined_vars());
            return array();
        }
    }

    /**
     * @desc SAFE FETCH Row
     * @param $query string Запрос
     * @param &$error string Текст ошибки
     */
    public function s_fetchRow($query, &$error = "") {
        try {
            if (!$this->db) return [];
            
            $this->profileStart($query);
            if ($this->cache_on) {

                $hash = md5($query);

                $cached_value = tools_cache::get('sql_' . $hash);

                if ($cached_value !== '!!nocache!!' && $cached_value) {
                    $data = $cached_value;
                } else {
                    $data = $this->db->fetchRow($query);
                    tools_cache::save('sql_' . $hash, $data, $this->cache_time);
                }
            } else
                $data = $this->db->fetchRow($query);
            
            $this->profileEnd($query);
            return $data;
        } catch (Exception $e) {
            $this->profileFail($query);
            $error = ($this->config->debug->on) ? $e->getMessage() : $this->config->debug->message;
            $this->last_error = $error;
            errorReport($e, get_defined_vars());
            return array();
        }
    }

    /**
     * @desc SAFE FETCH One
     * @param $query string Запрос
     * @param &$error string Текст ошибки
     */
    public function s_fetchOne($query, &$error = "") {
        try {
            if (!$this->db) return '';
            
            $this->profileStart($query);
            
            if ($this->cache_on) {

                $hash = md5($query);

                $cached_value = tools_cache::get('sql_' . $hash);

                if ($cached_value !== '!!nocache!!' && $cached_value) {
                    $data = $cached_value;
                } else {
                    $data = $this->db->fetchOne($query);
                    tools_cache::save('sql_' . $hash, $data, $this->cache_time);
                }
            } else
                $data = $this->db->fetchOne($query);
            
            $this->profileEnd($query);
            return $data;
        } catch (Exception $e) {
            $error = ($this->config->debug->on) ? $e->getMessage() : $this->config->debug->message;
            $this->last_error = $error;
            errorReport($e, get_defined_vars());
            $this->profileFail($query);
            return '';
        }
    }

    /**
     * @desc SAFE FETCH Col
     * @param $query string Запрос
     * @param &$error string Текст ошибки
     */
    public function s_fetchCol($query, &$error = "") {
        try {
            if (!$this->db) return [];
            
            if ($this->cache_on) {

                $hash = md5($query);

                $cached_value = tools_cache::get('sql_' . $hash);

                if ($cached_value !== '!!nocache!!' && $cached_value) {
                    $data = $cached_value;
                } else {
                    $data = $this->db->fetchCol($query);
                    tools_cache::save('sql_' . $hash, $data, $this->cache_time);
                }
            } else
                $data = $this->db->fetchCol($query);

            return $data;
        } catch (Exception $e) {
            $error = ($this->config->debug->on) ? $e->getMessage() : $this->config->debug->message;
            $this->last_error = $error;
            errorReport($e, get_defined_vars());
            return array();
        }
    }

    /**
     * @param string: Строка для очистки
     * @desc Функция очистки строки для инсерта
     *
     */
    protected function filterString($str) {
        return pg_escape_string(htmlentities($str, ENT_QUOTES, "UTF-8"));
    }

    /**
     * @desc Функция парсинга integer перемененых
     * Сокращение от Null If Zero
     */
    public function niz($val) {
        $val = intval($val);
        return ($val > 0) ? $val : new Zend_Db_Expr('null');
    }

    public function isnull($param) {
        return ((!is_null($param)) ? $param : new Zend_Db_Expr('NULL'));
    }

    function setItemsPerPage($num) {
        $this->items_per_page = intval($num);
    }

    function getTotalPagesCount($items_count = 0) {
        return ceil($items_count / $this->items_per_page);
    }

    function setCurrentPage($page = 1) {

        $page = intval($page);

        if ($page < 1)
            $page = 1;

        $this->current_page = $page;
    }

    function getCurrentPage() {
        return $this->current_page;
    }

    function getPageExpr() {
        return ($this->items_per_page && $this->current_page) ? "limit {$this->items_per_page} offset " . ($this->current_page * $this->items_per_page - $this->items_per_page) : '';
    }

    function enableCache($cache_time = 3600) {
        $this->cache_on = true;
        $this->cache_time = $cache_time;
    }

    function disableCache() {
        $this->cache_on = false;
        $this->cache_time = 0;
    }

    function setLastError($result, $error) {
        $this->user_data->result = $result;
        $this->user_data->last_error = $this->last_error = $error;
    }
    
    function disconnect() {
        if ($this->db) {
            $this->db->closeConnection();
            $this->db = false;
        }
    }

}
