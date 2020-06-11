<?php

class admin_ParserController extends adminController {
    
    function back() {
        //sleep(1);
        
        $this->_redirect($_SERVER['HTTP_REFERER']);
        
    }
    
    function onAction() {
        $this->ajax();
        file_put_contents('parser_on','1');
        $this->back();
    }
    
    function offAction() {
        $this->ajax();
        unlink('parser_on');
        $this->back();
    }
    
    
}
