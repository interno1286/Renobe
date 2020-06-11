<?php

class admin_TranslatorsController extends adminController {
    
    function indexAction() {
        
    }
    
    function toggleAction() {
        $this->ajax();
        
        $m = ormModel::getInstance('public','translators');
        
        $s = $m->get('enabled','id='.(int)$this->params['id']);
        
        $new_state = ($s) ? 'false' : 'true';
        
        $m->updateItem([
            'enabled'   => new Zend_Db_Expr($new_state)
        ],'id='.(int)$this->params['id']);
        
        $this->jsonAnswer([
            'error' => $m->last_error,
            'state' => ($new_state==='true')
        ]);
    }
    
    function resetAction() {
        $this->ajax();
        $m = ormModel::getInstance('public','translators');
        
        $fields = [
            'day'   => 'day_used',
            'month'   => 'month_used'
        ];
        
        $m->updateItem([
            $fields[$this->params['period']] => 0
        ],'id='.(int)$this->params['id']);
        
        $this->jsonAnswer([
            'error' => $m->last_error
        ]);
        
    }
    
    function setlimitAction() {
        $this->ajax();
        
        if (isset($this->params['day'])) {

            $m = ormModel::getInstance('public','translators');

            $m->updateItem([
                'day_limit' => $this->params['day'],
                'month_limit' => $this->params['month']
            ],'id='.$this->params['id']);

            $this->jsonAnswer([
                'error' => $m->last_error
            ]);
        }else $this->renderTplToContent('edit_limit.tpl');
    }
}
