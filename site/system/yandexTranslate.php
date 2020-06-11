<?php

/**
 * Description of yandexTranslate
 *
 * @author glenn.ru
 */
class yandexTranslate extends provider {
    
    static $instance = false;
    public $lang = "ru";
    
    static function getInstance($lng="ru") {
        
        if (self::$instance) {
            self::$instance->key = settings::getVal('yandex_tr_api');
            self::$instance->lang = $lng;
            return self::$instance;
        }else self::$instance = new yandexTranslate($lng);
        
        return self::$instance;
    }
    
    
    public $key = 'trnsl.1.1.20191009T211431Z.c15326c8bb7aa4aa.44e35e28e52acb1a852f0f6002a765e075bfb8a6';
    
    function __construct($lng="ru") {
        $this->lang = $lng;
        $this->key = settings::getVal('yandex_tr_api');
    }
    
    function translate($text='') {
        
        if (!$text) return '';
        
        $data = $this->getPage("https://translate.yandex.net/api/v1.5/tr.json/translate?key={$this->key}&text=".urlencode($text).'&lang='.$this->lang);
        
        $enc = json_decode($data);
        if (!$enc || $enc->code!==200 || !isset($enc->text[0])) throw new Exception('Translate error '.$data, 666);
        
        return $enc->text[0];
    }
    
}
