<?php

class tools_errors {
    
    static function lastErrorsAlert(&$content='') {
        $config = Zend_Registry::get('cnf');
        
        $errors = glob($config->path->root."temp/error/*.html");


        if ($errors) {
            @list($before,$after) = explode("</body>",$content);
            
            Zend_Registry::get('view')->assign('last_errors',$errors);
            
            $content = $before."\n\n".Zend_Registry::get('view')->render($config->path->root.'cms/views/last_errors.tpl')."</body>$after";
        }
    }
    
}