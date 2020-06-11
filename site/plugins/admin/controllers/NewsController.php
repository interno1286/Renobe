<?php

class admin_NewsController extends adminController {
    
    function indexAction() {
        
    }
    
    function editAction() {
        $this->ajax();
        
        $this->renderTplToContent('news_edit.tpl');
    }
    
    
    function saveAction() {
        $this->ajax();
        
        $m = new newsModel();
        $err = '';
        
        
        $m->savenews($this->params, $err);
        
        $this->jsonAnswer([
            'error' => $err
        ]);
    }
    
    function delAction() {
        $this->ajax();
        
        $m = new newsModel();
        
        $m->delEvent($this->params['id']);
        
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);        
    }
    
}
