<?php

/**
 * Description of meta
 *
 * @author v0yager
 */
class meta {
    
    static function get($field) {
        $url = $_SERVER['REQUEST_URI'];
        $mdurl = md5($url);
        
        $from_cache = tools_cache::get('meta_'.$mdurl);
        
        if ($from_cache && $from_cache!=='!!nocache!!') return $from_cache[$field];
        
        $m = ormModel::getInstance('metaModel');
        
        $data = $m->getRow("url='$mdurl'");
        
        if ($data) tools_cache::save('meta_'.md5($url), $data);
        
        return $data[$field];
    }
    
}
