<?php

class tools_cache {

    static public $instances = array();
    static public $cache = array();
    static protected $namespace = '';

    static function getInstance($server='default') {
        
        if (!isset(self::$instances[$server])) {
            self::$instances[$server] = new tools_cache();
            
            if (!isset(self::$cache[$server]))
                self::initCache($server);
        }

        return self::$instances[$server];
    }

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    static function initCache($server='default') {
        try {
            $config = Zend_Registry::get('cnf');

            if (!$config->use_memcached)
                throw new Exception('Memcached disabled in config');
            
            if (!@class_exists('Memcached'))
                throw new Exception('Memcached not installed');
            
            self::$cache[$server] = new Memcached;

            if (!@self::$cache[$server])
                throw new Exception('Memcached not installed');

            $port = (isset($config->memcached_port->$server)) ? $config->memcached_port->$server : 11211;
            
            if (!is_object(self::$cache[$server]))
                throw new Exception('its not object is '.self::$cache[$server]);
            
            if (!isset($config->memcached_server) || !isset($config->memcached_server->$server))
                throw new Exception('no server '.$server.' config in $config');
            
            if (!self::$cache[$server]->addServer($config->memcached_server->$server, $port))
                throw new Exception('cannot connect to memecache');
        } catch (Exception $e) {
            //throw $e;
            self::$cache[$server] = false;
            file_put_contents('cache_err', $e->getMessage());
            if (Zend_Registry::get('cnf')->debug->on) {
                //echo "error init cache ".$e->getMessage();
            }
        }
    }

    static function get($param, $server='default') {

        self::getInstance($server);
        
        if (self::$cache[$server] !== false) {
        
            if (self::$namespace) {
                $c_val = self::$cache[$server]->get('namespace_iter_'.self::$namespace);
            }else $c_val = '';
        
            return self::$cache[$server]->get($param.$c_val);
        } else
            return '!!nocache!!';
    }

    static function save($param, $value, $expire = 3600, $server='default') {

        self::getInstance($server);
        
        if (self::$cache[$server]!== false) {
        
            if (self::$namespace) {
                $c_val = self::$cache[$server]->get('namespace_iter_'.self::$namespace);
            }else $c_val = '';

        
            if (!self::$cache[$server]->set($param.$c_val, $value, $expire))
                return false;
        }

        return false;
    }

    static function delete($param, $server='default') {

        self::getInstance($server);

        if (self::$cache[$server] !== false) {
            
            if (self::$namespace) {
                $c_val = self::$cache[$server]->get('namespace_iter_'.self::$namespace);
            }else $c_val = '';
            
            return self::$cache[$server]->delete($param.$c_val);
        }

        return false;
    }

    static function flush($server='default') {

        self::getInstance($server);

        if (self::$cache[$server] !== false) {
            return self::$cache[$server]->flush();
        }

        return true;
    }
    
    static function flushNamespace($name, $expire=3600, $server='default') {
        
        self::getInstance($server);
        
        if (self::$cache[$server] !== false) {
            $c_val = self::$cache[$server]->get('namespace_iter_'.self::$namespace);
            $c_val++;

            self::$cache[$server]->set('namespace_iter_'.self::$namespace, $c_val, $expire);
        }else return false;
    }

    static function namespaceSet($name, $expire=3600, $server='default') {
        self::getInstance($server);
        
        if (self::$cache[$server]!== false) {
            self::$namespace = $name;

            if (!self::$cache[$server]->get('namespace_iter_'.self::$namespace))
                self::$cache[$server]->set('namespace_iter_'.self::$namespace, 1, $expire);
        }else return false;
        
    }

}
