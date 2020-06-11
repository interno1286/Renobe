<?php

class tools_skin {
    
    static function hasContent() {
        
        $config = Zend_Registry::get('cnf');
        
        $index = file_get_contents($config->path->root.'site/skins/'.$config->skin.'/views/index.tpl');
        
        return (mb_strpos($index,'site::getContent'));
        
    }
    
}
