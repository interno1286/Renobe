<?php

class admin_UsersController extends adminController {
    
    function blockAction() {
        
        $this->ajax();
        
        $m = new userModel();
        $m->updateItem([
            'blocked'   => new Zend_Db_Expr('true')
        ], 'id='.$this->params['id']);
        
        $this->jsonAnswer([
            'error'  => ''
        ]);
    }
    
    function editAction() {
        $this->ajax();
        $this->renderTplToContent('user_edit.tpl');
    }
    
    function savedataAction() {
        $this->ajax();
        
        
        $m = new userModel();
        $p = &$this->params;
        
        if ($p['password'])
            $p['db']['password'] = sha1($p['password']);
        
        $m->updateItem($p['db'], 'id='.$p['id']);
        
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);
    }
    
}
