<?php

/// балансировщик и маршрутизатор переводчиков
class translaTor {
    
    public $srcLng = "";
    static $instance = false;
    
    function __construct($srcLng="") {
        $this->srcLng = $srcLng;
    }
    
    function translate($text, $novella_id=false) {
        
        $m = ormModel::getInstance('public','translators');
        
        $t = $m->getAll('enabled=true and month_limit>month_used and day_limit>day_used and class is not null');
        
        $count = sizeof($t);
        
        //if (!$count) throw new Exception("No translators available");
        if (!$count) return '';
        
        $translator = $t[rand(0, $count-1)];
        
        $class = new $translator['class']($this->srcLng);
        
        $transLen = mb_strlen($text);
        
        $data = $class->translate($text, $novella_id);
        
        $m->updateItem([
            'day_used'  => new Zend_Db_Expr('day_used+'.$transLen),
            'month_used'  => new Zend_Db_Expr('month_used+'.$transLen)
        ],'id='.$translator['id']);
        
        return $data;
    }
    

    static function getInstance($srcLng="") {
        
        if (self::$instance) {
            self::$instance->srcLng = $srcLng;
            return self::$instance;
        }else self::$instance = new translaTor($srcLng);
        
        return self::$instance;
    }
        
}
