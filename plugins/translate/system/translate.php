<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Евгений
 * Date: 24.04.14
 * Time: 19:05
 * To change this template use File | Settings | File Templates.
 */

class translate {

    static private $language = 'ru';
    
    static private $local_cache = array();  //// ускоряет работу при отсутствии мемкеша

    static private $lang_assoc = array(
        'ru' => array('ru','be','uk','ky','ab','mo','et','lv'),
        'en' => 'en'
    );
    
    static private $default = 'ru';
    
    static function setLanguage($lng='ru') {
        self::$language = $lng;
    }

    static function item($name) {
        
        if (isset(self::$local_cache[self::$language][$name])) {
            return self::$local_cache[self::$language][$name];
        }
        
        $cached_value = tools_cache::get('translate_'.self::$language.$name);
        
        if (!$cached_value || $cached_value=='!!nocache!!') {
        
            $model = new translateModel();

            $row = $model->getItem($name,self::$language);
            
            $value = (@$row['txt']) ? $row['txt'] : $name;
            
            tools_cache::save('translate_'.self::$language.$name, $value, 86400);
            self::$local_cache[self::$language][$name] = $value;
            
        }else $value = $cached_value;

        return $value;
        
    }

    static function getLanguage() {
        return self::$language;
    }
    
    
    static function getLanguageFromHeaders() {
        
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            //errorReport('Нет заголовка с языком! за то есть вот такие '.print_r($_SERVER,1));
            return 'ru';
        }
        
        if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                
                $language = array_combine($list[1], $list[2]);
                
                foreach ($language as $n => $v)
                    $language[$n] = $v ? $v : 1;
                
                arsort($language, SORT_NUMERIC);
            }
        }else $language = array();
        
        
        $languages=array();
        
        foreach (self::$lang_assoc as $lang => $alias) {
            if (is_array($alias)) {
                foreach ($alias as $alias_lang) {
                    $languages[strtolower($alias_lang)] = strtolower($lang);
                }
            }else $languages[strtolower($alias)]=strtolower($lang);
        }

        foreach ($language as $l => $v) {
            $s = strtok($l, '-'); // убираем то что идет после тире в языках вида "en-us, ru-ru"
            if (isset($languages[$s]))
                return $languages[$s];
        }
        return self::$default;
    }
}