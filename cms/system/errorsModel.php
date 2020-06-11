<?php

class errorsModel extends ormModel {
    
    public $table = 'log';
    public $schema = 'errors';
    
    function __construct($id = false) {
        
        $config = Zend_Registry::get('cnf');
        
        if ($config->db->adapter=='PDO_MYSQL')
            $this->table = 'errors_log';
        
        parent::__construct($id);
        
        if ($config->db->adapter=='PDO_MYSQL')
            $this->pq('query', 'set names utf8');
    }
    
    function install() {
        
        $config = Zend_Registry::get('cnf');
        
        if ($config->db->adapter=='PDO_MYSQL') {
            
            $create = "
                    CREATE TABLE errors_log (
                        `id` INT NOT NULL AUTO_INCREMENT,
                        `code` INT NOT NULL,
                        `message` TEXT NOT NULL,
                        `trace` TEXT NOT NULL,
                        `request` TEXT NOT NULL,
                        `vars` TEXT NOT NULL,
                        `time` TIME NOT NULL,
                        `date` DATE NOT NULL,
                        `count` INT(11) NOT NULL DEFAULT '1',
                        `parsed` ENUM('true','false') NOT NULL DEFAULT 'false',
                        `timestamp` DATETIME NOT NULL,
                        `message_hash` VARCHAR(32),
                        PRIMARY KEY (`id`)
                );
            ";
            
            $this->pq('query',$create);
            
        }else if ($config->db->adapter=='PDO_PGSQL') {
            $create = "CREATE SCHEMA errors";
            try {
                $this->db->query($create);
            }catch (Exception $e) {
                $error = $e->getMessage();
            };
            
            $create = <<<SQL
CREATE TABLE errors.log (
    id SERIAL,
    code INTEGER,
    message TEXT,
    trace TEXT,
    request TEXT,
    vars TEXT,
    "time" TIME WITHOUT TIME ZONE,
    date DATE,
    count INTEGER DEFAULT 0 NOT NULL,
    parsed VARCHAR(6) DEFAULT 'false' NOT NULL,
    "timestamp" TIMESTAMP(0) WITHOUT TIME ZONE,
    message_hash VARCHAR(32),
    CONSTRAINT log_pkey PRIMARY KEY(id)
)                    
SQL;
            
            try {
                $this->db->query($create);
            }catch (Exception $e) {
                $error = $e->getMessage();
            }

        }
        
        
    }
    
    
}
